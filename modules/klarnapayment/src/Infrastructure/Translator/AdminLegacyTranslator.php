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

namespace KlarnaPayment\Module\Infrastructure\Translator;

use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminLegacyTranslator implements AdminTranslatorInterface
{
    const FILE_NAME = 'AdminTranslator';

    /** @var \KlarnaPayment|false|\Module|null */
    private $module;

    public function __construct(ModuleFactory $moduleFactory)
    {
        $this->module = $moduleFactory->getModule();
    }

    public function translate(string $key): string
    {
        return isset($this->getTranslations()[$key]) ? $this->getTranslations()[$key] : $key;
    }

    private function getTranslations(): array
    {
        return [];
    }
}
