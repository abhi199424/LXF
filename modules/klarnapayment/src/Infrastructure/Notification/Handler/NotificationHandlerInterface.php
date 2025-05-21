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

namespace KlarnaPayment\Module\Infrastructure\Notification\Handler;

use KlarnaPayment\Module\Infrastructure\Notification\Notifications\Notification;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface NotificationHandlerInterface
{
    /**
     * @param string $key
     * @param Notification $notification
     *
     * @return void
     */
    public function addNotification(string $key, Notification $notification);

    /**
     * @param string $key
     * @param Notification $notification
     *
     * @return void
     */
    public function removeNotification(string $key, Notification $notification);

    /**
     * @param string $key
     *
     * @return void
     */
    public function removeNotifications(string $key);

    /**
     * @param string $key
     *
     * @return Notification[]
     */
    public function getNotifications(string $key);
}
