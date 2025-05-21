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

namespace KlarnaPayment\Module\Api\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Attachment implements \JsonSerializable
{
    /** @var string */
    private $body;

    /** @var string */
    private $contentType;

    public function getBody(): string
    {
        return $this->body;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @maps body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @maps content_type
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['body'] = $this->getBody();
        $json['content_type'] = $this->getContentType();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
