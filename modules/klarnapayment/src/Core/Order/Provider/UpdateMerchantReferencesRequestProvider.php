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

namespace KlarnaPayment\Module\Core\Order\Provider;

use KlarnaPayment\Module\Api\Requests\UpdateMerchantReferencesRequest;
use KlarnaPayment\Module\Core\Order\DTO\UpdateMerchantReferencesRequestData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateMerchantReferencesRequestProvider
{
    public function get(UpdateMerchantReferencesRequestData $data): UpdateMerchantReferencesRequest
    {
        $request = new UpdateMerchantReferencesRequest();

        $request->setOrderId($data->getOrderId());
        $request->setMerchantReference1($data->getMerchantReference1());
        $request->setMerchantReference2($data->getMerchantReference2());

        return $request;
    }
}
