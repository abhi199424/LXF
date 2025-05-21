<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Api\Enum\AcquiringChannel;
use KlarnaPayment\Module\Api\Enum\Intent;
use KlarnaPayment\Module\Api\Models\MerchantUrl;
use KlarnaPayment\Module\Api\Models\Session;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Order\Action\CalculateTotalTaxAmountBasedOnPrecisionAction;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\HookInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Product;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use KlarnaPayment\Module\Infrastructure\Utility\SecurityTokenUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentSessionProvider
{
    private const USA_ISO_CODE = 'US';

    private $productOrderLineProvider;
    private $giftWrappingOrderLineProvider;
    private $shippingOrderLineProvider;
    private $cartRuleOrderLineProvider;
    private $addressProvider;
    private $moneyCalculator;
    private $salesTaxOrderLineProvider;
    private $customerProvider;
    private $attachmentProvider;
    private $additionalHookOrderLineProvider;
    private $hook;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var Configuration
     */
    private $configuration;
    private $countryRepository;
    private $totalAmountPrecisionAdjustmentOrderLineProvider;
    private $calculateTotalTaxAmountBasedOnPrecisionAction;
    private $product;

    public function __construct(
        ProductOrderLineProvider $productOrderLineProvider,
        GiftWrappingOrderLineProvider $giftWrappingOrderLineProvider,
        ShippingOrderLineProvider $shippingOrderLineProvider,
        CartRuleOrderLineProvider $cartRuleOrderLineProvider,
        SalesTaxOrderLineProvider $salesTaxOrderLineProvider,
        AddressProvider $addressProvider,
        MoneyCalculator $moneyCalculator,
        CustomerProvider $customerProvider,
        AttachmentProvider $attachmentProvider,
        Context $context,
        Configuration $configuration,
        CountryRepositoryInterface $countryRepository,
        AdditionalHookOrderLineProvider $additionalHookOrderLineProvider,
        HookInterface $hook,
        TotalAmountPrecisionAdjustmentOrderLineProvider $totalAmountPrecisionAdjustmentOrderLineProvider,
        CalculateTotalTaxAmountBasedOnPrecisionAction $calculateTotalTaxAmountBasedOnPrecisionAction,
        Product $product
    ) {
        $this->productOrderLineProvider = $productOrderLineProvider;
        $this->giftWrappingOrderLineProvider = $giftWrappingOrderLineProvider;
        $this->shippingOrderLineProvider = $shippingOrderLineProvider;
        $this->cartRuleOrderLineProvider = $cartRuleOrderLineProvider;
        $this->addressProvider = $addressProvider;
        $this->moneyCalculator = $moneyCalculator;
        $this->salesTaxOrderLineProvider = $salesTaxOrderLineProvider;
        $this->customerProvider = $customerProvider;
        $this->attachmentProvider = $attachmentProvider;
        $this->context = $context;
        $this->configuration = $configuration;
        $this->countryRepository = $countryRepository;
        $this->additionalHookOrderLineProvider = $additionalHookOrderLineProvider;
        $this->hook = $hook;
        $this->totalAmountPrecisionAdjustmentOrderLineProvider = $totalAmountPrecisionAdjustmentOrderLineProvider;
        $this->calculateTotalTaxAmountBasedOnPrecisionAction = $calculateTotalTaxAmountBasedOnPrecisionAction;
        $this->product = $product;
    }

    public function get(\Cart $cart, bool $addCustomerDetails): Session
    {
        $session = new Session();

        $address = new \Address($cart->id_address_invoice);
        $country = new \Country($address->id_country);
        $currency = new \Currency($cart->id_currency);
        $language = new \Language($cart->id_lang);

        // Setting default country if it is not presented this is needed for OPC.
        if (!\Validate::isLoadedObject($country)) {
            $country = $this->countryRepository->findOneBy([
                'iso_code' => $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_DEFAULT_LOCALE),
            ], (int) $cart->id_lang);
        }

        $orderLines = [];

        $withOrderLineTaxes = $country->iso_code !== self::USA_ISO_CODE;

        $specificPriceOutput = null;

        // PRODUCT
        foreach ($cart->getProducts(true) as $product) {
            $priceWithoutReduction = $this->product->getPriceStatic(
                $product['id_product'],
                $withOrderLineTaxes,
                $product['id_product_attribute'],
                $this->context->getComputingPrecision(),
                null,
                false,
                false,
                $product['cart_quantity'],
                false,
                $cart->id_customer,
                $cart->id,
                $cart->id_address_delivery,
                $specificPriceOutput,
                true,
                true,
                null,
                true,
                $product['id_customization'] ?? null
            );

            $orderLines[] = $this->productOrderLineProvider->get(array_merge($product, [
                'price' => $product['price'],
                'price_wt' => $product[$withOrderLineTaxes ? 'price_wt' : 'price'],
                'price_without_reduction' => $priceWithoutReduction,
            ]));
        }

        // GIFT
        if ($cart->gift) {
            $orderLines[] = $this->giftWrappingOrderLineProvider->get([
                'price' => $cart->getOrderTotal(false, \Cart::ONLY_WRAPPING),
                'price_wt' => $cart->getOrderTotal($withOrderLineTaxes, \Cart::ONLY_WRAPPING),
            ]);
        }

        // SHIPPING
        if ($cart->getTotalShippingCost() > 0) {
            $orderLines[] = $this->shippingOrderLineProvider->get([
                'id_carrier' => $cart->id_carrier,
                'price' => $cart->getOrderTotal(false, \Cart::ONLY_SHIPPING),
                'price_wt' => $cart->getOrderTotal($withOrderLineTaxes, \Cart::ONLY_SHIPPING),
            ]);
        }

        // DISCOUNTS
        foreach ($cart->getCartRules(\CartRule::FILTER_ACTION_ALL, false) as $cartRuleDetails) {
            if (empty((float) $cartRuleDetails['value_real'])) {
                continue;
            }

            $priceTaxIncl = $cartRuleDetails[$withOrderLineTaxes ? 'value_real' : 'value_tax_exc'];
            $priceTaxExcl = $cartRuleDetails['value_tax_exc'];

            // NOTE: workaround for incorrectly given information from PS regarding value of calculation. It returns based on computation value (2) instead of 6 decimals causing errors in calculations.

            if (isset($cartRuleDetails['reduction_percent']) && (float) $cartRuleDetails['reduction_percent']) {
                // NOTE: it seems like it's correctly calculated for everything except whole cart.

                if (isset($cartRuleDetails['reduction_product']) && (int) $cartRuleDetails['reduction_product'] === 0) {
                    $totalProductsTaxIncl = 0.00;
                    $totalProductsTaxExcl = 0.00;

                    foreach ($cart->getProducts() as $product) {
                        if ((float) $product['reduction'] && (int) $cartRuleDetails['reduction_exclude_special']) {
                            continue;
                        }

                        $totalProductsTaxIncl += $product['total_wt'];
                        $totalProductsTaxExcl += $product['total'];
                    }

                    $priceTaxIncl = NumberUtility::multiplyByFloat(
                        (float) $totalProductsTaxIncl,
                        (float) NumberUtility::divideBy((float) $cartRuleDetails['reduction_percent'], 100, NumberUtility::FLOAT_PRECISION),
                        NumberUtility::FLOAT_PRECISION
                    );

                    $priceTaxExcl = NumberUtility::multiplyByFloat(
                        (float) $totalProductsTaxExcl,
                        (float) NumberUtility::divideBy((float) $cartRuleDetails['reduction_percent'], 100, NumberUtility::FLOAT_PRECISION),
                        NumberUtility::FLOAT_PRECISION
                    );
                }
            }

            // NOTE: re-adding shipping price to the discount because of the calculations above that only calculate amount with reduction
            if (((float) $cartRuleDetails['reduction_percent'] !== 0.0) && $cartRuleDetails['free_shipping']) {
                if ($cart->getTotalShippingCost() > 0) {
                    $priceTaxExcl = NumberUtility::add($priceTaxExcl, $cart->getOrderTotal(false, \Cart::ONLY_SHIPPING), NumberUtility::FLOAT_PRECISION);
                    $priceTaxIncl = NumberUtility::add($priceTaxIncl, $cart->getOrderTotal($withOrderLineTaxes, \Cart::ONLY_SHIPPING), NumberUtility::FLOAT_PRECISION);
                }
            }

            $orderLines[] = $this->cartRuleOrderLineProvider->get([
                'id_cart_rule' => $cartRuleDetails['id_cart_rule'],
                'price' => $cartRuleDetails['value_tax_exc'],
                'price_wt' => $cartRuleDetails['value_real'],
                'name' => $cartRuleDetails['name'],
            ]);
        }

        /* @see https://docs.klarna.com/klarna-payments/in-depth-knowledge/tax-handling/#tax-handling-best-practices-transmitting-tax-in-the-us */
        if (!$withOrderLineTaxes) {
            $orderLines[] = $this->salesTaxOrderLineProvider->get([
                'price' => $cart->getOrderTotal(false),
                'price_wt' => $cart->getOrderTotal(true),
            ]);
        }

        // ADDITIONAL HOOK DATA
        $additionalHookData = $this->hook->exec('klarnapaymentAdditionalOrderLineData') ?? [];

        if (!empty($additionalHookData)) {
            foreach ($additionalHookData as $hookData) {
                $orderLines[] = $this->additionalHookOrderLineProvider->get($hookData);
            }
        }

        // CUSTOMER
        if ($addCustomerDetails && $cart->id_customer) {
            if ($cart->id_address_invoice) {
                $session->setBillingAddress($this->addressProvider->get([
                    'id_customer' => $cart->id_customer,
                    'id_address' => $cart->id_address_invoice,
                ]));
            }

            if ($cart->id_address_delivery) {
                $session->setShippingAddress($this->addressProvider->get([
                    'id_customer' => $cart->id_customer,
                    'id_address' => $cart->id_address_delivery,
                ]));
            }

            $session->setCustomer($this->customerProvider->get((int) $cart->id_customer));
        }

        $session->setOrderAmount($this->moneyCalculator->calculateIntoInteger($cart->getOrderTotal()));

        if ($this->context->getComputingPrecision() === 0) {
            $orderLines[] = $this->totalAmountPrecisionAdjustmentOrderLineProvider->get((int) $session->getOrderAmount(), $orderLines) ?? array_pop($orderLines);
        }

        $session->setOrderTaxAmount($this->calculateTotalTaxAmountBasedOnPrecisionAction->run($orderLines, $cart));
        $session->setOrderLines($orderLines);
        $session->setPurchaseCountry($country->iso_code ?? $this->context->getCountryIso());
        $session->setPurchaseCurrency($currency->iso_code);
        $session->setLocale($language->locale);

        if ($language->iso_code === 'AU') {
            $session->setLocale('en-AU');
        }

        if ($cart->id_customer) {
            $session->setAttachment($this->attachmentProvider->get($cart->id_customer));
        }

        $session->setMerchantUrls(new MerchantUrl());
        $session->getMerchantUrls()->setAuthorization($this->context->getModuleLink(
            'klarnapayment',
            'authorization',
            [
                'cart_id' => $cart->id,
                'security_token' => SecurityTokenUtility::generateTokenFromCart($cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
            ]
        ));

        $session->setIntent(Intent::BUY);
        $session->setAcquiringChannel(AcquiringChannel::ECOMMERCE);

        $this->hook->exec('klarnaModifySessionRequest', ['sessionRequest' => $session]);

        return $session;
    }
}
