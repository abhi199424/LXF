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
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TotalAmountPrecisionAdjustmentOrderLineProvider
{
    private const TRANSLATION_ID = 'TotalAmountPrecisionAdjustmentOrderLineProvider';

    /**
     * @var \KlarnaPayment|null
     */
    private $module;

    public function __construct(ModuleFactory $moduleFactory)
    {
        $this->module = $moduleFactory->getModule();
    }

    /**
     * @param int $totalAmount
     * @param array<OrderLine> $orderLines
     *
     * @return OrderLine|null
     */
    public function get(int $totalAmount, array $orderLines): ?OrderLine
    {
        $orderLinesTotalAmount = 0;

        foreach ($orderLines as $orderLine) {
            $orderLinesTotalAmount += $orderLine->getTotalAmount();
        }

        if ($orderLinesTotalAmount === $totalAmount) {
            return null;
        }

        $additionalOrderLine = new OrderLine();

        $additionalOrderLine->setMerchantData(Config::DECIMAL_PRECISION_ADJUSTMENT);
        $additionalOrderLine->setName($this->module->l('Decimal precision adjustment', self::TRANSLATION_ID));
        $additionalOrderLine->setQuantity(1);

        $additionalOrderLine->setTotalAmount($totalAmount - $orderLinesTotalAmount);
        $additionalOrderLine->setUnitPrice($totalAmount - $orderLinesTotalAmount);

        return $additionalOrderLine;
    }
}
