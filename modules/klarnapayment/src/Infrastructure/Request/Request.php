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

namespace KlarnaPayment\Module\Infrastructure\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Request extends \Symfony\Component\HttpFoundation\Request
{
    public function all(): array
    {
        return $this->request->all() + $this->query->all() + (json_decode(\Tools::file_get_contents('php://input'), true) ?? []) + $this->files->all();
    }

    /**
     * @return mixed|null
     */
    public function getArgument($key, $default = null, $deep = false)
    {
        $data = $this->all();

        if (parent::get($key)) {
            return parent::get($key);
        }

        return $data[$key] ?? $default;
    }
}
