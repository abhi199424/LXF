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

namespace KlarnaPayment\Module\Core\Payment\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrieveHppSessionData
{
    /** @var string */
    private $sessionId;

    private function __construct(
        $sessionId
    ) {
        $this->sessionId = $sessionId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public static function create(
        string $sessionId
    ): self {
        return new self(
            $sessionId
        );
    }
}
