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

namespace KlarnaPayment\Module\Api\Requests;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/hpp-merchant/#operation/getSessionById
 */
class RetrieveHppSessionRequest implements \JsonSerializable, RequestInterface
{
    /** @var ?string */
    private $sessionId;

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * @maps session_id
     */
    public function setSessionId(?string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['session_id'] = $this->getSessionId();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
