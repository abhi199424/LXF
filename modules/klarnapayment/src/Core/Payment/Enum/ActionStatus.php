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

namespace KlarnaPayment\Module\Core\Payment\Enum;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ActionStatus
{
    public const ERROR = 'error';
    public const CANCEL = 'cancel';
    public const SUCCESS = 'success';
    public const UPDATE = 'update';
}
