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

class PaymentOptionType
{
    public const INVOICE = 'INVOICE';
    public const FIXED_AMOUNT = 'FIXED_AMOUNT';
    public const FIXED_AMOUNT_BY_CARD = 'FIXED_AMOUNT_BY_CARD';
    public const PAY_LATER_IN_PARTS = 'PAY_LATER_IN_PARTS';
    public const ACCOUNT = 'ACCOUNT';
    public const DIRECT_DEBIT = 'DIRECT_DEBIT';
    public const CARD = 'CARD';
    public const BANK_TRANSFER = 'BANK_TRANSFER';
    public const PAY_IN_X = 'PAY_IN_X';
    public const INVOICE_BUSINESS = 'INVOICE_BUSINESS';
    public const DEFERRED_INTEREST = 'DEFERRED_INTEREST';
    public const FIXED_SUM_CREDIT = 'FIXED_SUM_CREDIT';
    public const PAY_BY_CARD = 'PAY_BY_CARD';
    public const PAY_LATER_BY_CARD = 'PAY_LATER_BY_CARD';
    public const MOBILEPAY = 'MOBILEPAY';
    public const SWISH = 'SWISH';
}
