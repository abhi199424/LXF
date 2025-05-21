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

namespace KlarnaPayment\Module\Api\Http;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HttpMethod
{
    const GET = 'Get';
    const POST = 'Post';
    const PUT = 'Put';
    const PATCH = 'Patch';
    const DELETE = 'Delete';
    const HEAD = 'Head';
}
