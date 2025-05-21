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

namespace KlarnaPayment\Module\Api\Enum;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Intent
{
    public const BUY = 'buy';
    public const TOKENIZE = 'tokenize';
    public const BUY_AND_TOKENIZE = 'buy_and_tokenize';
}
