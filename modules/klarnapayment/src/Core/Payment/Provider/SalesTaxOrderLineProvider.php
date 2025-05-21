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

class SalesTaxOrderLineProvider
{
    private $moneyCalculator;
    /** @var Context */
    private $context;

    public function __construct(MoneyCalculator $moneyCalculator, Context $context)
    {
        $this->moneyCalculator = $moneyCalculator;
        $this->context = $context;
    }

    public function get(array $salesTax): OrderLine
    {
        $cartTotalTaxExl = (float) $salesTax['price'];
        $cartTotalTaxIncl = (float) $salesTax['price_wt'];

        $totalTaxAmount = NumberUtility::minus($cartTotalTaxIncl, $cartTotalTaxExl, $this->context->getComputingPrecision());

        $totalAmount = NumberUtility::round($totalTaxAmount, $this->context->getComputingPrecision());
        $unitPrice = NumberUtility::round($totalTaxAmount, $this->context->getComputingPrecision());

        $orderLine = new OrderLine();

        $orderLine->setType('sales_tax');
        $orderLine->setName('Tax');
        $orderLine->setQuantity(1);
        $orderLine->setUnitPrice($this->moneyCalculator->calculateIntoInteger($unitPrice));
        $orderLine->setTotalAmount($this->moneyCalculator->calculateIntoInteger($totalAmount));

        return $orderLine;
    }
}
