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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/hpp-merchant/#operation/createHppSession
 */
class CreateHppSessionResponse implements \JsonSerializable, ResponseInterface
{
    /** @var ?string */
    private $distributionUrl;
    /** @var ?string */
    private $manualIdentificationCheckUrl;
    /** @var ?string */
    private $qrCodeUrl;
    /** @var ?string */
    private $redirectUrl;
    /** @var string */
    private $sessionId;
    /** @var ?string */
    private $sessionUrl;

    public function getDistributionUrl(): string
    {
        return $this->distributionUrl;
    }

    public function getManualIdentificationCheckUrl(): string
    {
        return $this->manualIdentificationCheckUrl;
    }

    public function getQrCodeUrl(): string
    {
        return $this->qrCodeUrl;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getSessionUrl(): string
    {
        return $this->sessionUrl;
    }

    /**
     * @maps distribution_url
     */
    public function setDistributionUrl(string $distributionUrl): void
    {
        $this->distributionUrl = $distributionUrl;
    }

    /**
     * @maps manual_identification_check_url
     */
    public function setManualIdentificationCheckUrl(string $manualIdentificationCheckUrl): void
    {
        $this->manualIdentificationCheckUrl = $manualIdentificationCheckUrl;
    }

    /**
     * @maps qr_code_url
     */
    public function setQrCodeUrl(string $qrCodeUrl): void
    {
        $this->qrCodeUrl = $qrCodeUrl;
    }

    /**
     * @maps redirect_url
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @maps session_id
     */
    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @maps session_url
     */
    public function setSessionUrl(string $sessionUrl): void
    {
        $this->sessionUrl = $sessionUrl;
    }

    public function jsonSerialize(): array
    {
        $json = [
            'distribution_url' => $this->getDistributionUrl(),
            'manual_identification_check_url' => $this->getManualIdentificationCheckUrl(),
            'qr_code_url' => $this->getQrCodeUrl(),
            'redirect_url' => $this->getRedirectUrl(),
            'session_id' => $this->getSessionId(),
            'session_url' => $this->getSessionUrl(),
        ];

        // Filter out null values
        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
