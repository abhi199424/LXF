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

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Requests\CreateHppSessionRequest;
use KlarnaPayment\Module\Api\Requests\RetrieveHppSessionRequest;
use KlarnaPayment\Module\Api\Responses\CreateHppSessionResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveHppSessionResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface HppSessionApiRepositoryInterface
{
    /**
     * @throws ApiException
     */
    public function createHppSession(CreateHppSessionRequest $request): ?CreateHppSessionResponse;

    /**
     * @throws ApiException
     */
    public function retrieveHppSession(RetrieveHppSessionRequest $request): ?RetrieveHppSessionResponse;
}
