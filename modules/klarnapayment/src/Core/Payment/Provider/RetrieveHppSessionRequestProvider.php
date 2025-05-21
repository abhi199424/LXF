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

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Api\Requests\RetrieveHppSessionRequest;
use KlarnaPayment\Module\Core\Payment\DTO\RetrieveHppSessionData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrieveHppSessionRequestProvider
{
    public function get(RetrieveHppSessionData $data): RetrieveHppSessionRequest
    {
        $request = new RetrieveHppSessionRequest();

        $request->setSessionId($data->getSessionId());

        return $request;
    }
}
