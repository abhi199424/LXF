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

namespace KlarnaPayment\Module\Core\Order\Handler\Status;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface InternalOrderStatusHandlerInterface
{
    /**
     * @param int $internalOrderId
     * @param string $orderStatus
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function handle(int $internalOrderId, string $orderStatus): void;
}
