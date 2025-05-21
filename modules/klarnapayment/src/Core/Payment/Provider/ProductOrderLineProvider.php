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

use KlarnaPayment\Module\Api\Models\OrderLine;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductOrderLineProvider
{
    /** @var MoneyCalculator */
    private $moneyCalculator;
    /** @var Context */
    private $context;
    /** @var GlobalShopContextInterface */
    private $globalShopContext;
    /** @var Configuration */
    private $configuration;

    public function __construct(
        MoneyCalculator $moneyCalculator,
        Context $context,
        GlobalShopContextInterface $globalShopContext,
        Configuration $configuration
    ) {
        $this->moneyCalculator = $moneyCalculator;
        $this->context = $context;
        $this->globalShopContext = $globalShopContext;
        $this->configuration = $configuration;
    }

    public function get(array $product): OrderLine
    {
        $productCostTaxIncl = (float) $product['price_wt'];
        $productCostTaxExcl = (float) $product['price'];

        $productCostWithoutReduction = (float) $product['price_without_reduction'];

        $taxRate = 0;

        $computingPrecision = $this->context->getComputingPrecision() ?: Config::DEFAULT_COMPUTING_PRECISION;

        // Prevent division by zero
        if ($productCostTaxExcl > 0) {
            $taxRate = $this->moneyCalculator->calculateTaxRate($productCostTaxIncl, $productCostTaxExcl);
        }

        if ($taxRate) {
            $totalTaxAmountTaxIncl = $this->calculateProductLineAmount($productCostTaxIncl, (int) $product['cart_quantity'], $computingPrecision);
            $totalTaxAmountTaxExcl = $this->calculateProductLineAmount($productCostTaxExcl, (int) $product['cart_quantity'], $computingPrecision);

            $totalTaxAmount = NumberUtility::minus($totalTaxAmountTaxIncl, $totalTaxAmountTaxExcl, $computingPrecision);
        } else {
            $totalTaxAmount = 0;
        }

        $truncateProductCostTaxIncl = floor($productCostTaxIncl * 100) / 100;

        $unitDiscountAmount = NumberUtility::minus($productCostWithoutReduction, $truncateProductCostTaxIncl, $computingPrecision);
        $totalDiscountAmount = $this->calculateProductLineAmount($unitDiscountAmount, (int) $product['cart_quantity'], $computingPrecision);

        $totalAmount = $this->calculateProductLineAmount($productCostTaxIncl, (int) $product['cart_quantity'], $computingPrecision);
        $unitPrice = NumberUtility::round($productCostWithoutReduction, $computingPrecision);

        $orderLine = new OrderLine();

        $orderLine->setType((int) $product['is_virtual'] ? 'digital' : 'physical');
        $orderLine->setMerchantData(sprintf('PRODUCT_%s_%s', $product['id_product'], $product['id_product_attribute']));
        $orderLine->setReference((string) $product['reference'] ?: null);
        $orderLine->setName((string) $product['name']);
        $orderLine->setQuantity((int) $product['cart_quantity']);
        $orderLine->setQuantityUnit('pcs');
        $orderLine->setUnitPrice($this->moneyCalculator->calculateIntoInteger($unitPrice));
        $orderLine->setTotalAmount($this->moneyCalculator->calculateIntoInteger($totalAmount));
        $orderLine->setTotalTaxAmount($this->moneyCalculator->calculateIntoInteger($totalTaxAmount));
        $orderLine->setTaxRate($this->moneyCalculator->calculateIntoInteger($taxRate));
        $orderLine->setTotalDiscountAmount($this->moneyCalculator->calculateIntoInteger($totalDiscountAmount));
        $orderLine->setProductUrl(\Context::getContext()->link->getProductLink($product['id_product']));
        $orderLine->setImageUrl(\Context::getContext()->link->getImageLink($product['link_rewrite'], $product['id_image']));

        return $orderLine;
    }

    /**
     * Product line amount = product unit amount * quantity.
     * Respects PS rounding type settings.
     */
    private function calculateProductLineAmount(float $productUnitAmount, int $quantity, int $computingPrecision): float
    {
        $roundType = $this->configuration->getAsInteger('PS_ROUND_TYPE', $this->globalShopContext->getShopId());

        switch ($roundType) {
            default:
            case \Order::ROUND_ITEM:
                $roundedUnitAmount = NumberUtility::round($productUnitAmount, $computingPrecision);

                return (float) NumberUtility::multiplyBy($roundedUnitAmount, $quantity, $computingPrecision);
            case \Order::ROUND_LINE:
            case \Order::ROUND_TOTAL:
                return (float) NumberUtility::multiplyBy($productUnitAmount, $quantity, NumberUtility::FLOAT_PRECISION);
        }
    }
}
