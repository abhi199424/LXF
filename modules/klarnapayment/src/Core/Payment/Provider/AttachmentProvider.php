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

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Api\Enum\ContentType;
use KlarnaPayment\Module\Api\Models\Attachment;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\HookInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AttachmentProvider
{
    private $customerAccountInfoProvider;
    private $hook;
    private $configuration;

    public function __construct(
        CustomerAccountInfoProvider $customerAccountInfoProvider,
        Configuration $configuration,
        HookInterface $hook
    ) {
        $this->customerAccountInfoProvider = $customerAccountInfoProvider;
        $this->configuration = $configuration;
        $this->hook = $hook;
    }

    public function get(int $customerId): ?Attachment
    {
        $apiAttachment = new Attachment();

        $initialBody = [
            'customer_account_info' => [
                $this->customerAccountInfoProvider->get($customerId),
            ],
        ];

        $hookResults = $this->hook->exec('klarnapaymentAdditionalEmd') ?? [];
        $additionalBody = [];

        foreach ($hookResults as $hookResult) {
            $additionalBody = array_merge_recursive($additionalBody, $hookResult);
        }

        $result = array_merge($initialBody, $additionalBody);

        if (!$result) {
            return null;
        }

        $apiAttachment->setBody(json_encode($result));
        $apiAttachment->setContentType(ContentType::CONTENT_TYPE);

        return $apiAttachment;
    }
}
