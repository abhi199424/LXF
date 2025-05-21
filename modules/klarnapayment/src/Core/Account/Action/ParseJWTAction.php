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

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use KlarnaPayment\Module\Core\Account\DTO\ParseJWTActionData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ParseJWTAction
{
    /**
     * @throws \Throwable
     */
    public function run(ParseJWTActionData $data): array
    {
        $parsedKeySet = JWK::parseKeySet([
            'keys' => json_decode(json_encode($data->getJwks()), true),
        ]);

        $decodedToken = JWT::decode($data->getToken(), $parsedKeySet);

        return json_decode(json_encode($decodedToken), true);
    }
}
