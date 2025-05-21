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
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdditionalHookOrderLineProvider
{
    /** @var MoneyCalculator */
    private $moneyCalculator;

    public function __construct(MoneyCalculator $moneyCalculator)
    {
        $this->moneyCalculator = $moneyCalculator;
    }

    public function get(array $hookOrderLineData): OrderLine
    {
        $orderLine = new OrderLine();

        $orderLine->setType($hookOrderLineData['type'] ?? 'surcharge');
        $orderLine->setName($hookOrderLineData['name']);
        $orderLine->setUnitPrice($this->moneyCalculator->calculateIntoInteger($hookOrderLineData['unit_price']));
        $orderLine->setTotalAmount($this->moneyCalculator->calculateIntoInteger($hookOrderLineData['total_amount']));
        $orderLine->setQuantity($hookOrderLineData['quantity']);

        return $orderLine;
    }
}
