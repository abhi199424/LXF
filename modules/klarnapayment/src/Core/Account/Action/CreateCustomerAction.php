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

namespace KlarnaPayment\Module\Core\Account\Action;

use KlarnaPayment\Module\Core\Account\DTO\CreateCustomerData;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateCustomerAction
{
    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function run(CreateCustomerData $createCustomerData): \Customer
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        $customer = new \Customer();

        $customer->email = $createCustomerData->getEmail();
        $customer->firstname = $createCustomerData->getFirstName();
        $customer->lastname = $createCustomerData->getLastName();
        $customer->birthday = $createCustomerData->getBirthday();
        $customer->is_guest = false;
        $customer->id_default_group = (int) \Configuration::get('PS_GUEST_GROUP');

        if (class_exists('PrestaShop\PrestaShop\Core\Crypto\Hashing')) {
            $crypto = new \PrestaShop\PrestaShop\Core\Crypto\Hashing();

            $customer->passwd = $crypto->hash(
                time() . _COOKIE_KEY_,
                _COOKIE_KEY_
            );
        } else {
            $customer->passwd = md5(time() . _COOKIE_KEY_);
        }

        $customer->save();

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $customer;
    }
}
