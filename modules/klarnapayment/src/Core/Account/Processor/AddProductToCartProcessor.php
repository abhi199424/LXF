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

namespace KlarnaPayment\Module\Core\Account\Processor;

use KlarnaPayment\Module\Core\Account\DTO\AddProductToCartProcessorData;
use KlarnaPayment\Module\Core\Account\Exception\CouldNotProcessProductAddToCart;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AddProductToCartProcessor
{
    /** @var Context */
    private $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @throws \Throwable
     */
    public function run(AddProductToCartProcessorData $data): void
    {
        try {
            $cart = $this->createCartInstance();
        } catch (\Throwable $exception) {
            throw CouldNotProcessProductAddToCart::failedToCreateCartInstance($exception);
        }

        if (!$cart->updateQty(
            $data->getQuantity(),
            $data->getProductId(),
            !$data->getProductAttributeId() ? null : $data->getProductAttributeId(),
            !$data->getCustomizationId() ? false : $data->getCustomizationId(),
            'up'
        )) {
            $cart->delete();

            throw CouldNotProcessProductAddToCart::failedToAddProductToCart((int) $cart->id, $data->getQuantity(), $data->getProductId(), $data->getProductAttributeId(), $data->getCustomizationId());
        }

        try {
            $this->context->setCurrentCart($cart);
        } catch (\Throwable $exception) {
            $cart->delete();

            throw CouldNotProcessProductAddToCart::failedToUpdateContext($exception);
        }
    }

    /**
     * @throws \Throwable
     */
    private function createCartInstance(): \Cart
    {
        $existingCart = $this->context->getCart();

        if ($existingCart && (int) $existingCart->id) {
            return $existingCart;
        }

        $cart = new \Cart();

        $cart->id_currency = $this->context->getCurrencyId();
        $cart->id_lang = $this->context->getLanguageId();

        $cart->add();

        return $cart;
    }
}
