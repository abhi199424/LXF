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
            {if $show_checkboxes}
                <th>
                    <div class="form-check">
                        <input class="form-check-input select-all-check-box" type="checkbox">
                        <label class="form-check-label"></label>
                    </div>
                </th>
            {/if}
            <th scope="col">{l s='Quantity' mod='klarnapayment'}</th>
            <th scope="col">{l s='Item' mod='klarnapayment'}</th>
            <th scope="col">{l s='Amount' mod='klarnapayment'}</th>
        </tr>
        </thead>

        <tbody>
        {foreach $order_lines as $order_line}
            <tr>
                {if $show_checkboxes}
                    <td>
                        <div class="form-check">
                            <input class="form-check-input individual-check" name="order_line_ids[]"
                                   type="checkbox"
                                   value="{$order_line.id_order_line|escape:'htmlall':'UTF-8'}"
                                   data-total-price="{$order_line.total_price|escape:'htmlall':'UTF-8'}"
                            >
                            <label class="form-check-label"></label>
                        </div>
                    </td>
                {/if}
                <td>{$order_line.product_quantity|escape:'htmlall':'UTF-8'}</td>
                <td>{$order_line.product_name|escape:'htmlall':'UTF-8'}</td>
                <td>{$order_line.total_price_formatted|escape:'htmlall':'UTF-8'}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
