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

use KlarnaPayment\Module\Infrastructure\Request\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CapturePaymentData
{
    /** @var int */
    private $internalOrderId;
    /** @var string */
    private $externalOrderId;
    /** @var float */
    private $capturedAmount;

    private function __construct(int $internalOrderId, string $externalOrderId, float $capturedAmount)
    {
        $this->internalOrderId = $internalOrderId;
        $this->externalOrderId = $externalOrderId;
        $this->capturedAmount = $capturedAmount;
    }

    public function getInternalOrderId(): int
    {
        return $this->internalOrderId;
    }

    public function getExternalOrderId(): string
    {
        return $this->externalOrderId;
    }

    public function getCapturedAmount(): float
    {
        return $this->capturedAmount;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->get('internal_order_id'),
            $request->get('external_order_id'),
            $request->get('captured_amount')
        );
    }
}
