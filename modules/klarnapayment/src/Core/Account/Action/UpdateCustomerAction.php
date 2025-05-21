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

use KlarnaPayment\Module\Core\Account\DTO\UpdateCustomerData;
use KlarnaPayment\Module\Core\Account\Exception\CouldNotUpdateCustomer;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateCustomerAction
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
    public function run(UpdateCustomerData $data): \Customer
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        $customer = new \Customer($data->getId());

        if (!\Validate::isLoadedObject($customer)) {
            throw CouldNotUpdateCustomer::customerNotFound($data->getId());
        }

        $customer->email = $data->getEmail() ?: $customer->email;
        $customer->firstname = $data->getFirstName() ?: $customer->firstname;
        $customer->lastname = $data->getLastName() ?: $customer->lastname;
        $customer->birthday = $data->getBirthday() ?: $customer->birthday;

        $customer->save();

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $customer;
    }
}
