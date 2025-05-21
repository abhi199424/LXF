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

namespace KlarnaPayment\Module\Core\Merchant\Action;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SiwkScopeBuilderAction
{
    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function run(array $requiredScope, array $optionalScope): string
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        $scopes = array_merge([Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_MANDATORY_SCOPE], $requiredScope, $optionalScope);
        $scopes = implode(' ', $scopes);

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $scopes;
    }
}
