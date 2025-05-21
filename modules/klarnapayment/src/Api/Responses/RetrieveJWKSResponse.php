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

namespace KlarnaPayment\Module\Api\Responses;

use KlarnaPayment\Module\Api\Models\JWK;

if (!defined('_PS_VERSION_')) {
    exit;
}

/** @see https://docs.klarna.com/sign-in-with-klarna/integrate-sign-in-with-klarna/sign-in-with-klarna-tokens/decoding-id-token/ */
class RetrieveJWKSResponse implements \JsonSerializable, ResponseInterface
{
    /** @var JWK[] */
    private $keys;

    /**
     * @param \KlarnaPayment\Module\Api\Models\JWK[] $keys
     *
     * @maps keys
     */
    public function setKeys(array $keys): void
    {
        $this->keys = $keys;
    }

    public function getKeys(): array
    {
        return $this->keys;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['keys'] = $this->getKeys();

        return array_filter($json, static function ($val) {
            return !empty($val);
        });
    }
}
