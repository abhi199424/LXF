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

use KlarnaPayment\Module\Api\Models\HppSessionMerchantUrl;
use KlarnaPayment\Module\Api\Models\HppSessionOptions;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/hpp-merchant/#operation/createHppSession
 */
class CreateHppSessionRequest implements \JsonSerializable, RequestInterface
{
    /**
     * @var HppSessionMerchantUrl
     */
    private $merchantUrls;

    /**
     * @var HppSessionOptions
     */
    private $options;

    /**
     * @var string
     */
    private $paymentSessionUrl;

    /**
     * @var string|null
     */
    private $profileId;

    /**
     * @return HppSessionMerchantUrl
     */
    public function getMerchantUrls(): HppSessionMerchantUrl
    {
        return $this->merchantUrls;
    }

    public function getOptions(): HppSessionOptions
    {
        return $this->options;
    }

    public function getPaymentSessionUrl(): string
    {
        return $this->paymentSessionUrl;
    }

    public function getProfileId(): ?string
    {
        return $this->profileId;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\HppSessionMerchantUrl $merchantUrls
     *
     * @maps merchant_urls
     */
    public function setMerchantUrls(HppSessionMerchantUrl $merchantUrls): void
    {
        $this->merchantUrls = $merchantUrls;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\HppSessionOptions $options
     *
     * @maps options
     */
    public function setOptions(HppSessionOptions $options): void
    {
        $this->options = $options;
    }

    /**
     * @maps payment_session_url
     */
    public function setPaymentSessionUrl(string $paymentSessionUrl): void
    {
        $this->paymentSessionUrl = $paymentSessionUrl;
    }

    /**
     * @maps profile_id
     */
    public function setProfileId(?string $profileId): void
    {
        $this->profileId = $profileId;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['merchant_urls'] = $this->getMerchantUrls();
        $json['options'] = $this->getOptions();
        $json['payment_session_url'] = $this->getPaymentSessionUrl();
        $json['profile_id'] = $this->getProfileId();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
