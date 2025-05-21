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
if (!defined('_PS_VERSION_')) {
    exit;
}

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\Currency\LocaleProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

class KlarnaPaymentOsmInfoPageModuleFrontController extends AbstractFrontController
{
    const FILE_NAME = 'osmInfoPage';

    public function initContent()
    {
        parent::initContent();

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var Tools $tools */
        $tools = $this->module->getService(Tools::class);

        $osmEnabled = $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE);
        $infoPageEnabled = $configuration->getByEnvironmentAsBoolean(Config::ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG);

        if (!$osmEnabled || !$infoPageEnabled) {
            $logger->debug(sprintf('%s - Users can\'t access OSM info page because OSM feature / infopage is disabled', self::FILE_NAME));

            $tools->redirect($this->context->link->getPageLink('index'));
        }

        /** @var LocaleProvider $localeProvider */
        $localeProvider = $this->module->getService(LocaleProvider::class);

        $this->context->smarty->assign([
            'klarnapayment' => [
                'osm' => [
                    'locale' => $localeProvider->getLocale(),
                ],
            ],
        ]);

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $this->setTemplate('module:klarnapayment/views/templates/front/osm/infopage.tpl');
    }
}
