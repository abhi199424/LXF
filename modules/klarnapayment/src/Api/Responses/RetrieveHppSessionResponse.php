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

use KlarnaPayment\Module\Api\Models\HppSession;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/hpp-merchant/#operation/getSessionById
 */
class RetrieveHppSessionResponse implements \JsonSerializable, ResponseInterface
{
    /** @var HppSession */
    private $session;

    public function getSession(): HppSession
    {
        return $this->session;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\HppSession $session
     *
     * @maps session
     */
    public function setSession(HppSession $session): void
    {
        $this->session = $session;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['session'] = $this->getSession();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
