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

namespace KlarnaPayment\Module\Infrastructure\Verification;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Shared\Repository\HookAliasRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\HookRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AreRequiredHooksAttached
{
    private $globalShopContext;
    private $module;
    private $hookRepository;
    private $logger;
    private $hookAliasRepository;

    public function __construct(
        ModuleFactory $moduleFactory,
        GlobalShopContextInterface $globalShopContext,
        HookRepositoryInterface $hookRepository,
        LoggerInterface $logger,
        HookAliasRepositoryInterface $hookAliasRepository
    ) {
        $this->module = $moduleFactory->getModule();
        $this->globalShopContext = $globalShopContext;
        $this->hookRepository = $hookRepository;
        $this->logger = $logger;
        $this->hookAliasRepository = $hookAliasRepository;
    }

    public function verify(): bool
    {
        $attachedHooks = $this->hookRepository->getAttached(
            (int) $this->module->id,
            (int) $this->globalShopContext->getShopId()
        );

        $hookNames = array_map(function ($item) {
            return strtolower($item['name']);
        }, $attachedHooks);

        foreach (Config::HOOK_LIST as $hookName) {
            $hookName = strtolower($hookName);
            if (!in_array($hookName, $hookNames) && !$this->hookAliasRepository->hookAliasExist($hookName)) {
                $this->logger->alert($hookName . 'hook is not a registered or is not valid',
                    [
                        'failed_hook' => $hookName,
                        'attacked_hooks' => $hookNames,
                        'HOOK_LIST' => Config::HOOK_LIST,
                    ]
                );

                return false;
            }
        }

        return true;
    }
}
