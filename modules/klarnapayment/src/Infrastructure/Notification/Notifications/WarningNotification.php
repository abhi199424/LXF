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

namespace KlarnaPayment\Module\Infrastructure\Notification\Notifications;

use KlarnaPayment\Module\Infrastructure\Notification\Enum\NotificationType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WarningNotification
{
    public static function create(
        $message
    ): Notification {
        return Notification::create(
            NotificationType::WARNING,
            $message
        );
    }
}
