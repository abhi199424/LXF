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

use KlarnaPayment\Module\Api\Requests\UpdatePaymentSessionRequest;
use KlarnaPayment\Module\Core\Payment\DTO\UpdatePaymentSessionData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdatePaymentSessionRequestProvider
{
    private $paymentSessionProvider;

    public function __construct(
        PaymentSessionProvider $paymentSessionProvider
    ) {
        $this->paymentSessionProvider = $paymentSessionProvider;
    }

    public function get(UpdatePaymentSessionData $data): UpdatePaymentSessionRequest
    {
        $request = new UpdatePaymentSessionRequest();

        $request->setSessionId($data->getSessionId());
        $request->setSession($this->paymentSessionProvider->get($data->getCart(), $data->isAddCustomerDetails()));

        return $request;
    }
}
