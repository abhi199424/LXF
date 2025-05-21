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

namespace KlarnaPayment\Module\Core\Order\Action;

use KlarnaPayment\Module\Api\Models\OrderLine;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CalculateTotalTaxAmountBasedOnPrecisionAction
{
    private $computingPrecision;
    private $moneyCalculator;

    public function __construct(Context $context, MoneyCalculator $moneyCalculator)
    {
        $this->computingPrecision = $context->getComputingPrecision();
        $this->moneyCalculator = $moneyCalculator;
    }

    /**
     * @param array<OrderLine> $orderLines
     *
     * @return int
     */
    public function run(array $orderLines, \Cart $cart): int
    {
        $totalTaxAmount = 0;

        if ((int) $this->computingPrecision === 0) {
            foreach ($orderLines as $orderLine) {
                $totalTaxAmount += $orderLine->getTotalTaxAmount();
            }

            return $totalTaxAmount;
        }

        return $this->moneyCalculator->calculateIntoInteger(
            NumberUtility::minus(
                $cart->getOrderTotal(),
                $cart->getOrderTotal(false),
                $this->computingPrecision
            )
        );
    }
}
