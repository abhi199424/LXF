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

use KlarnaPayment\Module\Core\Account\Api\Repository\AccountApiRepository;
use KlarnaPayment\Module\Core\Account\Api\Repository\AccountApiRepositoryInterface;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepository;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepositoryInterface;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepository;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\Api\Repository\HppSessionApiRepository;
use KlarnaPayment\Module\Core\Payment\Api\Repository\HppSessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\Api\Repository\SessionApiRepository;
use KlarnaPayment\Module\Core\Payment\Api\Repository\SessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\AddressRepository;
use KlarnaPayment\Module\Core\Shared\Repository\AddressRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CarrierRepository;
use KlarnaPayment\Module\Core\Shared\Repository\CarrierRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CartRepository;
use KlarnaPayment\Module\Core\Shared\Repository\CartRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepository;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CurrencyRepository;
use KlarnaPayment\Module\Core\Shared\Repository\CurrencyRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CustomerRepository;
use KlarnaPayment\Module\Core\Shared\Repository\CustomerRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\GenderRepository;
use KlarnaPayment\Module\Core\Shared\Repository\GenderRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\HookRepository;
use KlarnaPayment\Module\Core\Shared\Repository\HookRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\ImageRepository;
use KlarnaPayment\Module\Core\Shared\Repository\ImageRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaExpressCheckoutRepository;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaExpressCheckoutRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCartRepository;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCartRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCustomerRepository;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCustomerRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\LanguageRepository;
use KlarnaPayment\Module\Core\Shared\Repository\LanguageRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\OrderCarrierRepository;
use KlarnaPayment\Module\Core\Shared\Repository\OrderCarrierRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\OrderRepository;
use KlarnaPayment\Module\Core\Shared\Repository\OrderRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\OrderStateRepository;
use KlarnaPayment\Module\Core\Shared\Repository\OrderStateRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\ProductRepository;
use KlarnaPayment\Module\Core\Shared\Repository\ProductRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\StateRepository;
use KlarnaPayment\Module\Core\Shared\Repository\StateRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\TabRepository;
use KlarnaPayment\Module\Core\Shared\Repository\TabRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Logger\Repository\KlarnaPaymentLogRepository;
use KlarnaPayment\Module\Infrastructure\Logger\Repository\KlarnaPaymentLogRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Logger\Repository\PrestashopLoggerRepository;
use KlarnaPayment\Module\Infrastructure\Logger\Repository\PrestashopLoggerRepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RepositoryServiceProvider extends AbstractServiceProvider
{
    public $bindings = [
        CurrencyRepositoryInterface::class => CurrencyRepository::class,
        PrestashopLoggerRepositoryInterface::class => PrestashopLoggerRepository::class,
        KlarnaPaymentLogRepositoryInterface::class => KlarnaPaymentLogRepository::class,
        TabRepositoryInterface::class => TabRepository::class,
        LanguageRepositoryInterface::class => LanguageRepository::class,
        KlarnaPaymentOrderRepositoryInterface::class => KlarnaPaymentOrderRepository::class,
        SessionApiRepositoryInterface::class => SessionApiRepository::class,
        HppSessionApiRepositoryInterface::class => HppSessionApiRepository::class,
        OrderApiRepositoryInterface::class => OrderApiRepository::class,
        AccountApiRepositoryInterface::class => AccountApiRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        CustomerRepositoryInterface::class => CustomerRepository::class,
        KlarnaPaymentCartRepositoryInterface::class => KlarnaPaymentCartRepository::class,
        AddressRepositoryInterface::class => AddressRepository::class,
        CountryRepositoryInterface::class => CountryRepository::class,
        GenderRepositoryInterface::class => GenderRepository::class,
        StateRepositoryInterface::class => StateRepository::class,
        OrderStateRepositoryInterface::class => OrderStateRepository::class,
        CartRepositoryInterface::class => CartRepository::class,
        ImageRepositoryInterface::class => ImageRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        OrderCarrierRepositoryInterface::class => OrderCarrierRepository::class,
        CarrierRepositoryInterface::class => CarrierRepository::class,
        KlarnaExpressCheckoutRepositoryInterface::class => KlarnaExpressCheckoutRepository::class,
        HookRepositoryInterface::class => HookRepository::class,
        KlarnaPaymentCustomerRepositoryInterface::class => KlarnaPaymentCustomerRepository::class,
    ];

    public function register(): void
    {
    }
}
