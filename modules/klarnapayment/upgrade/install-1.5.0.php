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

use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_0(KlarnaPayment $module)
{
    /* @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    try {
        $configuration = $module->getService(\KlarnaPayment\Module\Infrastructure\Adapter\Configuration::class);

        /** @var \KlarnaPayment\Module\Core\Tools\Action\ValidateOpcModuleCompatibilityAction $opcModuleValidator */
        $opcModuleValidator = new \KlarnaPayment\Module\Core\Tools\Action\ValidateOpcModuleCompatibilityAction();
        $isOpcModuleInUse = $opcModuleValidator->run();

        $configuration->set(\KlarnaPayment\Module\Core\Config\Config::KLARNA_PAYMENT_HPP_SERVICE['production'], (int) $isOpcModuleInUse);
        $configuration->set(\KlarnaPayment\Module\Core\Config\Config::KLARNA_PAYMENT_HPP_SERVICE['sandbox'], (int) $isOpcModuleInUse);
        $configuration->set(\KlarnaPayment\Module\Core\Config\Config::KLARNA_PAYMENT_SECRET_KEY, uniqid('', true));
    } catch (\Throwable $e) {
        $logger->error('Failed to upgrade to version 1.5.0', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}
