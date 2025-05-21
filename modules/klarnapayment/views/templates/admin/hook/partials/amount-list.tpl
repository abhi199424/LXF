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
<div class="products-wrapper table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">{l s='Item' mod='klarnapayment'}</th>
            <th scope="col">{l s='Amount' mod='klarnapayment'}</th>
        </tr>
        </thead>

        <tbody>
            <tr>
                <td>{l s='Custom amount' mod='klarnapayment'}</td>
                <td>{$captured_amount|escape:'htmlall':'UTF-8'}</td>
            </tr>
        </tbody>
    </table>
</div>
