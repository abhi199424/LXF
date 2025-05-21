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

use KlarnaPayment\Module\Api\Requests\RetrievePaymentSessionRequest;
use KlarnaPayment\Module\Core\Payment\DTO\RetrievePaymentSessionData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrievePaymentSessionRequestProvider
{
    public function get(RetrievePaymentSessionData $data): RetrievePaymentSessionRequest
    {
        $request = new RetrievePaymentSessionRequest();

        $request->setSessionId($data->getSessionId());

        return $request;
    }
}
