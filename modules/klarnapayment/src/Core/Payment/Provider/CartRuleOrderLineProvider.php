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
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CartRuleOrderLineProvider
{
    private $moneyCalculator;
    private $context;

    public function __construct(
        MoneyCalculator $moneyCalculator,
        Context $context
    ) {
        $this->moneyCalculator = $moneyCalculator;
        $this->context = $context;
    }

    public function get(array $cartRule): OrderLine
    {
        $computingPrecision = $this->context->getComputingPrecision() ?: Config::DEFAULT_COMPUTING_PRECISION;

        $cartRuleCostTaxExcl = (float) $cartRule['price'];
        $cartRuleCostTaxIncl = (float) $cartRule['price_wt'];

        $taxRate = 0;

        // Prevent division by zero
        if ($cartRuleCostTaxExcl > 0) {
            $taxRate = $this->moneyCalculator->calculateTaxRate($cartRuleCostTaxIncl, $cartRuleCostTaxExcl);
        }

        if ($taxRate) {
            $cartRuleTotalTaxIncl = NumberUtility::round($cartRuleCostTaxIncl, $computingPrecision);
            $cartRuleTotalTaxExcl = NumberUtility::round($cartRuleCostTaxExcl, $computingPrecision);

            $totalTaxAmount = NumberUtility::minus($cartRuleTotalTaxIncl, $cartRuleTotalTaxExcl, $computingPrecision);
        } else {
            $totalTaxAmount = 0;
        }

        $totalAmount = NumberUtility::round($cartRuleCostTaxIncl, $computingPrecision);
        $unitPrice = NumberUtility::round($cartRuleCostTaxIncl, $computingPrecision);

        $orderLine = new OrderLine();

        $orderLine->setType('discount');
        $orderLine->setMerchantData(sprintf('DISCOUNT_%s', $cartRule['id_cart_rule']));
        $orderLine->setName($cartRule['name']);
        $orderLine->setQuantity(1);
        $orderLine->setUnitPrice(-$this->moneyCalculator->calculateIntoInteger($unitPrice));
        $orderLine->setTotalAmount(-$this->moneyCalculator->calculateIntoInteger($totalAmount));
        $orderLine->setTotalTaxAmount(-$this->moneyCalculator->calculateIntoInteger($totalTaxAmount));
        $orderLine->setTaxRate($this->moneyCalculator->calculateIntoInteger($taxRate));

        return $orderLine;
    }
}
