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

namespace KlarnaPayment\Module\Presentation\Presenter\Verification;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Shared\Repository\CartRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class IsCartAmountValidForKlarnaPayments
{
    /** @var MoneyCalculator */
    private $moneyCalculator;

    /** @var Configuration */
    private $configuration;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var Tools */
    private $tools;

    /** @var Context */
    private $context;

    public function __construct(
        MoneyCalculator $moneyCalculator,
        Configuration $configuration,
        CartRepositoryInterface $cartRepository,
        Tools $tools,
        Context $context
    ) {
        $this->moneyCalculator = $moneyCalculator;
        $this->configuration = $configuration;
        $this->cartRepository = $cartRepository;
        $this->tools = $tools;
        $this->context = $context;
    }

    public function verify(int $cartId, array $product = []): bool
    {
        $orderMin = $this->configuration->getByEnvironmentAsInteger(Config::KLARNA_PAYMENT_ORDER_MIN);
        $orderMax = $this->configuration->getByEnvironmentAsInteger(Config::KLARNA_PAYMENT_ORDER_MAX);

        $tools = $this->tools;

        if ($orderMax === 0) {
            return true;
        }

        /** @var \Cart $cart */
        $cart = $this->cartRepository->findOneBy(['id_cart' => $cartId]);

        $total = 0;

        if ($cart) {
            $total += $this->moneyCalculator->calculateIntoInteger($cart->getOrderTotal());
        }

        if (!empty($product)) {
            $productPrice = $product['price_without_reduction'];
            $fullPrice = $productPrice - ($product['reduction'] ?? 0);
            $roundedPrice = $tools->ps_round($fullPrice, $this->context->getComputingPrecision());
            $productTotalAmount = NumberUtility::multiplyBy(
                $this->moneyCalculator->calculateIntoInteger($roundedPrice),
                $product['quantity_wanted']
            );

            $total += (int) $productTotalAmount;
        }

        return $total <= $orderMax && $total >= $orderMin;
    }
}
