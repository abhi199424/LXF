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

namespace KlarnaPayment\Module\Core\Tools\Action;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ValidateIsAssetsRequired
{
    private $validateIsOpcModuleController;

    public function __construct(ValidateIsOpcModuleController $validateIsOpcModuleController)
    {
        $this->validateIsOpcModuleController = $validateIsOpcModuleController;
    }

    /**
     * It returns true if it's an OPC controller or an OrderController with products in the cart. Otherwise, it returns false.
     */
    public function run(\FrontController $controller): bool
    {
        $isOrderController = $controller instanceof \OrderControllerCore
            || $controller instanceof \ModuleFrontController && isset($controller->php_self) && $controller->php_self === 'order';

        if (!$this->validateIsOpcModuleController->run($controller)) {
            return $isOrderController && !empty(\Context::getContext()->cart->getProducts());
        }

        return true;
    }
}
