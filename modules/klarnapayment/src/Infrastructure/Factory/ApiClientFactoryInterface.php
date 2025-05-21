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

namespace KlarnaPayment\Module\Infrastructure\Factory;

use KlarnaPayment\Module\Api\ApiClient;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface ApiClientFactoryInterface
{
    /**
     * @throws \KlarnaPayment\Module\Api\Exception\CouldNotConfigureApiClient
     */
    public function create(array $customOptions = []): ApiClient;
}
