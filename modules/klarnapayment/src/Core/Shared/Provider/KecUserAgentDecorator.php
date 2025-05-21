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

namespace KlarnaPayment\Module\Core\Shared\Provider;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KecUserAgentDecorator implements UserAgentProviderInterface
{
    private $userAgentProvider;

    public function __construct(UserAgentProviderInterface $userAgentProvider)
    {
        $this->userAgentProvider = $userAgentProvider;
    }

    public function get(): string
    {
        return $this->userAgentProvider->get() . '-KEC';
    }
}
