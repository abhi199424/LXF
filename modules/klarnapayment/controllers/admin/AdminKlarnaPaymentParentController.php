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

use KlarnaPayment\Module\Infrastructure\Controller\AbstractAdminController as ModuleAdminController;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminKlarnaPaymentParentController extends ModuleAdminController
{
    public function postProcess()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKlarnaPaymentSettings'));
    }
}
