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

namespace KlarnaPayment\Module\Presentation\Presenter;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\Currency\LocaleProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Utility\ArrayUtility;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OnsiteMessagingPresenter
{
    /** @var \KlarnaPayment */
    private $module;
    /** @var float */
    private $purchaseAmount;
    /** @var LocaleProvider */
    private $localeProvider;
    /** @var Configuration */
    private $configuration;
    /** @var Context */
    private $context;
    /** @var MoneyCalculator */
    private $moneyCalculator;

    public function __construct(
        ModuleFactory $moduleFactory,
        LocaleProvider $localeProvider,
        Configuration $configuration,
        Context $context,
        MoneyCalculator $moneyCalculator
    ) {
        $this->module = $moduleFactory->getModule();
        $this->localeProvider = $localeProvider;
        $this->configuration = $configuration;
        $this->context = $context;
        $this->moneyCalculator = $moneyCalculator;
    }

    public function setPurchaseAmount(float $purchaseAmount): void
    {
        $this->purchaseAmount = $purchaseAmount;
    }

    public function present(array $themeConfigurationKey, array $dataKeyConfigurationKey): string
    {
        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE)) {
            return '';
        }

        $theme = (string) $this->configuration->getByEnvironment($themeConfigurationKey);
        $dataKey = (string) $this->configuration->getByEnvironment($dataKeyConfigurationKey);

        if (empty($theme) || empty($dataKey)) {
            return '';
        }

        if (!$this->validatePurchaseAmount($this->purchaseAmount, $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_ORDER_MIN), $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_ORDER_MAX))) {
            return '';
        }

        $previousKlarnaPaymentSmartyVars = $this->context->getSmarty()->getTemplateVars('klarnapayment') ?? [];

        $this->context->getSmarty()->assign([
            'klarnapayment' => ArrayUtility::recursiveArrayMerge($previousKlarnaPaymentSmartyVars, [
                'onsite_messaging' => [
                    'purchase_amount' => !empty($this->purchaseAmount)
                        ? NumberUtility::multiplyBy(
                            NumberUtility::round($this->purchaseAmount, $this->context->getComputingPrecision()),
                            100
                        )
                        : 0,
                    'theme' => $theme,
                    'key' => $dataKey,
                    'locale' => $this->localeProvider->getLocale(),
                ],
            ]),
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/front/hook/onsite_messaging.tpl');
    }

    private function validatePurchaseAmount(?float $purchaseAmount = null, ?int $orderMin = null, ?int $orderMax = null): bool
    {
        if ($purchaseAmount === null || $orderMax === 0 || $orderMax === null) {
            return true;
        }

        $purchaseAmount = $this->moneyCalculator->calculateIntoInteger($purchaseAmount);

        if (
            ($orderMin !== null && $purchaseAmount < $orderMin)
            || ($purchaseAmount > $orderMax)
        ) {
            return false;
        }

        return true;
    }
}
