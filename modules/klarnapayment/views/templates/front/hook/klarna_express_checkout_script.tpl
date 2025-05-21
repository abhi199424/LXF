{**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 *}
<script src="{$klarnapayment.klarna_express_checkout.callback_script_path|escape:"htmlall":"UTF-8"}"></script>

<script
        async
        src='{$klarnapayment.klarna_express_checkout.sdk_url|escape:"htmlall":"UTF-8"}'
        data-id='{$klarnapayment.klarna_express_checkout.mid|escape:"htmlall":"UTF-8"}'
        data-environment='{$klarnapayment.environment|escape:"htmlall":"UTF-8"}'
></script>
