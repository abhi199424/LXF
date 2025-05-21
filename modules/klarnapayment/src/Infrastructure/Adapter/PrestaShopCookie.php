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

namespace KlarnaPayment\Module\Infrastructure\Adapter;

use Context as PrestashopContext;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaShopCookie
{
    public function set($id, $value): void
    {
        PrestashopContext::getContext()->cookie->{$id} = $value;
    }

    /**
     * @param string $id
     *
     * @return false|string
     */
    public function get($id)
    {
        return PrestashopContext::getContext()->cookie->{$id};
    }

    public function clear($id): void
    {
        if (isset(PrestashopContext::getContext()->cookie->{$id})) {
            unset(PrestashopContext::getContext()->cookie->{$id});
        }
    }
}
