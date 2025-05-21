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

namespace KlarnaPayment\Module\Core\Payment\Api\Repository;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Requests\CreatePaymentSessionRequest;
use KlarnaPayment\Module\Api\Requests\RetrievePaymentSessionRequest;
use KlarnaPayment\Module\Api\Requests\UpdatePaymentSessionRequest;
use KlarnaPayment\Module\Api\Responses\CreatePaymentSessionResponse;
use KlarnaPayment\Module\Api\Responses\RetrievePaymentSessionResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface SessionApiRepositoryInterface
{
    /**
     * @throws ApiException
     */
    public function createPaymentSession(CreatePaymentSessionRequest $request): ?CreatePaymentSessionResponse;

    /**
     * @throws ApiException
     */
    public function updatePaymentSession(UpdatePaymentSessionRequest $request): void;

    /**
     * @throws ApiException
     */
    public function retrievePaymentSession(RetrievePaymentSessionRequest $request): RetrievePaymentSessionResponse;

    public function setApiClient(ApiClient $apiClient): void;
}
