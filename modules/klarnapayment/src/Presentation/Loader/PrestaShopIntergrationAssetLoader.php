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

namespace KlarnaPayment\Module\Presentation\Loader;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use PrestaShop\Module\PsEventbus\Service\PresenterService;
use PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaShopIntegrationAssetLoader
{
    /**
     * @var \KlarnaPayment|null
     */
    private $module;

    /**
     * @var Context
     */
    private $context;

    public function __construct(ModuleFactory $module, Context $context)
    {
        $this->module = $module->getModule();
        $this->context = $context;
    }

    public function load(): void
    {
        $this->loadPsAccounts();
        $this->loadCloudSync();
    }

    private function loadPsAccounts(): void
    {
        /** @var PsAccounts $accountsFacade */
        $accountsFacade = $this->module->getService(PsAccounts::class);
        $psAccountsPresenter = $accountsFacade->getPsAccountsPresenter();
        $psAccountsService = $accountsFacade->getPsAccountsService();

        $this->context->getSmarty()->assign('klarnapayment', [
            'url' => [
                'psAccountsCdnUrl' => $psAccountsService->getAccountsCdn(),
            ],
        ], true);

        $previousJsDef = isset(\Media::getJsDef()['klarnapayment']) ? \Media::getJsDef()['klarnapayment'] : [];

        \Media::addJsDef([
            'contextPsAccounts' => $psAccountsPresenter->present(),
            'klarnapayment' => array_merge($previousJsDef, [
                'isPsAccountsLinked' => $psAccountsService->isAccountLinked(),
            ]),
        ]);
    }

    private function loadCloudSync(): void
    {
        if (!class_exists('Ps_eventbus') || !class_exists('PrestaShop\Module\PsEventbus\Service\PresenterService')) {
            return;
        }

        /** @phpstan-ignore-next-line PHPStan does not recognize the event bus module, so it doesn't know it has getService function */
        $eventbusModule = \Module::getInstanceByName('ps_eventbus');

        if (!$eventbusModule) {
            return;
        }

        /** @phpstan-ignore-next-line PHPStan does not recognize the event bus module, so it doesn't know it has getService function */
        $eventbusPresenterService = $eventbusModule->getService(PresenterService::class);

        $previousJsDef = isset(\Media::getJsDef()['klarnapayment']) ? \Media::getJsDef()['klarnapayment'] : [];

        \Media::addJsDef([
            'contextPsEventbus' => $eventbusPresenterService->expose($this->module, [
                'info',
                'carts',
                'currencies',
                'orders',
                'products',
                'images',
                'stocks',
                'stores',
                'modules',
                'themes',
            ]),
            'klarnapayment' => array_merge($previousJsDef, [
                'url' => [
                    'cloudSyncPathCDC' => Config::PRESTASHOP_CLOUDSYNC_CDC,
                ],
            ]),
        ]);
    }
}
