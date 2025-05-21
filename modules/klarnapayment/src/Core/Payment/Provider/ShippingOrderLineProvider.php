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
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ShippingOrderLineProvider
{
    /** @var MoneyCalculator */
    private $moneyCalculator;
    /** @var Context */
    private $context;

    public function __construct(MoneyCalculator $moneyCalculator, Context $context)
    {
        $this->moneyCalculator = $moneyCalculator;
        $this->context = $context;
    }

    public function get(array $shipping): OrderLine
    {
        $shippingCostTaxExcl = (float) $shipping['price'];
        $shippingCostTaxIncl = (float) $shipping['price_wt'];

        $taxRate = 0;

        // Prevent division by zero
        if ($shippingCostTaxExcl > 0) {
            $taxRate = $this->moneyCalculator->calculateTaxRate($shippingCostTaxIncl, $shippingCostTaxExcl);
        }

        if ($taxRate) {
            $shippingTotalTaxIncl = NumberUtility::round($shippingCostTaxIncl, $this->context->getComputingPrecision());
            $shippingTotalTaxExcl = NumberUtility::round($shippingCostTaxExcl, $this->context->getComputingPrecision());

            $totalTaxAmount = NumberUtility::minus($shippingTotalTaxIncl, $shippingTotalTaxExcl, $this->context->getComputingPrecision());
        } else {
            $totalTaxAmount = 0;
        }

        $totalAmount = NumberUtility::round($shippingCostTaxIncl, $this->context->getComputingPrecision());
        $unitPrice = NumberUtility::round($shippingCostTaxIncl, $this->context->getComputingPrecision());

        $orderLine = new OrderLine();

        $orderLine->setType('shipping_fee');
        $orderLine->setMerchantData(sprintf('SHIPPING_%s', $shipping['id_carrier']));
        $orderLine->setName('Shipping');
        $orderLine->setQuantity(1);
        $orderLine->setUnitPrice($this->moneyCalculator->calculateIntoInteger($unitPrice));
        $orderLine->setTotalAmount($this->moneyCalculator->calculateIntoInteger($totalAmount));
        $orderLine->setTotalTaxAmount($this->moneyCalculator->calculateIntoInteger($totalTaxAmount));
        $orderLine->setTaxRate($this->moneyCalculator->calculateIntoInteger($taxRate));

        return $orderLine;
    }
}
