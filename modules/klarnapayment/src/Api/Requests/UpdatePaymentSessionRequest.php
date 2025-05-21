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

use KlarnaPayment\Module\Api\Models\Session;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/payments/#operation/updateCreditSession
 */
class UpdatePaymentSessionRequest implements \JsonSerializable, RequestInterface
{
    /** @var ?string */
    private $sessionId;

    /** @var ?Session */
    private $session;

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Session|null $session
     *
     * @maps session
     */
    public function setSession(?Session $session)
    {
        $this->session = $session;
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
        $json['session'] = $this->getSession();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
