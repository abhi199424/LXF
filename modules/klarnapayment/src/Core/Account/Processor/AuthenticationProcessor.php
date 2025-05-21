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

namespace KlarnaPayment\Module\Core\Account\Processor;

use KlarnaPayment\Module\Core\Account\Action\ParseJWTAction;
use KlarnaPayment\Module\Core\Account\Action\RetrieveJWKSAction;
use KlarnaPayment\Module\Core\Account\DTO\AuthenticationProcessorData;
use KlarnaPayment\Module\Core\Account\DTO\ParseJWTActionData;
use KlarnaPayment\Module\Core\Account\Handler\AuthenticationHandler;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AuthenticationProcessor
{
    /** @var RetrieveJWKSAction */
    private $retrieveJWKSAction;
    /** @var ParseJWTAction */
    private $parseJWTAction;
    /** @var AuthenticationHandler */
    private $authenticationHandler;

    public function __construct(
        RetrieveJWKSAction $retrieveJWKSAction,
        ParseJWTAction $parseJWTAction,
        AuthenticationHandler $authenticationHandler
    ) {
        $this->retrieveJWKSAction = $retrieveJWKSAction;
        $this->parseJWTAction = $parseJWTAction;

        $this->authenticationHandler = $authenticationHandler;
    }

    /**
     * @param AuthenticationProcessorData $data
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function run(AuthenticationProcessorData $data): void
    {
        $retrieveJWKSResponse = $this->retrieveJWKSAction->run();

        $decodedData = $this->parseJWTAction->run(ParseJWTActionData::create(
            $data->getTokenId(),
            $retrieveJWKSResponse->getKeys()
        ));

        $this->authenticationHandler->authenticate(
            $data->getTokenId(),
            $data->getRefreshToken(),
            $decodedData
        );
    }
}
