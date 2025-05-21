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

class GiftWrappingOrderLineProvider
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

    public function get(array $giftWrapping): OrderLine
    {
        $giftWrappingCostTaxExcl = (float) $giftWrapping['price'];
        $giftWrappingCostTaxIncl = (float) $giftWrapping['price_wt'];

        $taxRate = 0;

        // Prevent division by zero
        if ($giftWrappingCostTaxExcl > 0) {
            $taxRate = $this->moneyCalculator->calculateTaxRate($giftWrappingCostTaxIncl, $giftWrappingCostTaxExcl);
        }

        if ($taxRate) {
            $giftWrappingTotalTaxIncl = NumberUtility::round($giftWrappingCostTaxIncl, $this->context->getComputingPrecision());
            $giftWrappingTotalTaxExcl = NumberUtility::round($giftWrappingCostTaxExcl, $this->context->getComputingPrecision());

            $totalTaxAmount = NumberUtility::minus($giftWrappingTotalTaxIncl, $giftWrappingTotalTaxExcl, $this->context->getComputingPrecision());
        } else {
            $totalTaxAmount = 0;
        }

        $totalAmount = NumberUtility::round($giftWrappingCostTaxIncl, $this->context->getComputingPrecision());
        $unitPrice = NumberUtility::round($giftWrappingCostTaxIncl, $this->context->getComputingPrecision());

        $orderLine = new OrderLine();

        $orderLine->setType('physical');
        $orderLine->setMerchantData('GIFT_WRAPPING');
        $orderLine->setReference('gift_wrapping_fee');
        $orderLine->setName('Gift Wrapping');
        $orderLine->setQuantity(1);
        $orderLine->setUnitPrice($this->moneyCalculator->calculateIntoInteger($unitPrice));
        $orderLine->setTotalAmount($this->moneyCalculator->calculateIntoInteger($totalAmount));
        $orderLine->setTotalTaxAmount($this->moneyCalculator->calculateIntoInteger($totalTaxAmount));
        $orderLine->setTaxRate($this->moneyCalculator->calculateIntoInteger($taxRate));

        return $orderLine;
    }
}
