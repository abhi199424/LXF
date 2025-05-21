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

namespace KlarnaPayment\Module\Infrastructure\Container\Providers;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Payment\Api\Repository\FeatureAvailabilityApiRepository;
use KlarnaPayment\Module\Core\Payment\Api\Repository\FeatureAvailabilityApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\Provider\CountryProvider;
use KlarnaPayment\Module\Core\Payment\Provider\CountryProviderInterface;
use KlarnaPayment\Module\Core\Payment\Provider\CurrencyProvider;
use KlarnaPayment\Module\Core\Payment\Provider\CurrencyProviderInterface;
use KlarnaPayment\Module\Core\Shared\Provider\UserAgentProvider;
use KlarnaPayment\Module\Core\Shared\Provider\UserAgentProviderInterface;
use KlarnaPayment\Module\Core\Shared\Repository\HookAliasRepository;
use KlarnaPayment\Module\Core\Shared\Repository\HookAliasRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Hook;
use KlarnaPayment\Module\Infrastructure\Adapter\HookInterface;
use KlarnaPayment\Module\Infrastructure\Cache\CacheInterface;
use KlarnaPayment\Module\Infrastructure\Cache\FilesystemCache;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContext;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\EntityManagerInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\ObjectModelEntityManager;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactory;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactoryInterface;
use KlarnaPayment\Module\Infrastructure\Logger\Formatter\LogFormatter;
use KlarnaPayment\Module\Infrastructure\Logger\Formatter\LogFormatterInterface;
use KlarnaPayment\Module\Infrastructure\Logger\Logger;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Handler\CookieNotificationHandler;
use KlarnaPayment\Module\Infrastructure\Notification\Handler\NotificationHandlerInterface;
use KlarnaPayment\Module\Infrastructure\Translator\AdminLegacyModuleTabTranslator;
use KlarnaPayment\Module\Infrastructure\Translator\AdminLegacyTranslator;
use KlarnaPayment\Module\Infrastructure\Translator\AdminModuleTabTranslatorInterface;
use KlarnaPayment\Module\Infrastructure\Translator\AdminTranslatorInterface;
use KlarnaPayment\Module\Infrastructure\Translator\ExceptionLegacyTranslator;
use KlarnaPayment\Module\Infrastructure\Translator\ExceptionTranslatorInterface;
use PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts;
use PrestaShop\PsAccountsInstaller\Installer\Installer as PsAccountsInstaller;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Load base services here which are usually required
 */
final class BaseServiceProvider extends AbstractServiceProvider
{
    public $bindings = [
        GlobalShopContextInterface::class => GlobalShopContext::class,
        EntityManagerInterface::class => ObjectModelEntityManager::class,
        LogFormatterInterface::class => LogFormatter::class,
        LoggerInterface::class => Logger::class,
        AdminModuleTabTranslatorInterface::class => AdminLegacyModuleTabTranslator::class,
        NotificationHandlerInterface::class => CookieNotificationHandler::class,
        ExceptionTranslatorInterface::class => ExceptionLegacyTranslator::class,
        AdminTranslatorInterface::class => AdminLegacyTranslator::class,
        CacheInterface::class => FilesystemCache::class,
        UserAgentProviderInterface::class => UserAgentProvider::class,
        HookAliasRepositoryInterface::class => HookAliasRepository::class,
        CurrencyProviderInterface::class => CurrencyProvider::class,
        CountryProviderInterface::class => CountryProvider::class,
        FeatureAvailabilityApiRepositoryInterface::class => FeatureAvailabilityApiRepository::class,
    ];

    public function register(): void
    {
        $this->container->add(ApiClientFactoryInterface::class, function () {
            return $this->container->get(ApiClientFactory::class);
        }, true);

        $this->container->add(HookInterface::class, function () {
            return $this->container->get(Hook::class);
        }, true);

        $this->container->add(PsAccountsInstaller::class, PsAccountsInstaller::class)
            ->addArgument(Config::PS_ACCOUNT_VERSION);

        $this->container->add(PsAccounts::class, PsAccounts::class)
            ->addArgument(PsAccountsInstaller::class);
    }
}
