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
 * @see https://docs.klarna.com/api/payments/#operation/createCreditSession
 */
class CreatePaymentSessionRequest implements \JsonSerializable, RequestInterface
{
    /** @var ?Session */
    private $session;

    public function getSession(): ?Session
    {
        return $this->session;
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

    public function jsonSerialize(): array
    {
        $json = [];
        $json['session'] = $this->getSession();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
