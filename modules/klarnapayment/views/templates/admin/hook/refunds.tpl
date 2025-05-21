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
{foreach $klarnaPaymentOrder.refunds as $key => $refund}
    <div class="pl-0 table-wrapper refunds-list-wrapper {if $isLegacyHook}col-md-4 left-column{/if}">
        <div class="panel card">
            <div class="panel-heading card-header">
                <div class="d-flex justify-content-between">
                    <span>{l s='Refund #' mod='klarnapayment'}{$key|escape:'htmlall':'UTF-8' + 1}</span>
                </div>
            </div>

            <div class="container py-3">
                {if !empty($refund.order_lines)}
                    {assign var='order_lines' value=$refund.order_lines}

                    {include './partials/products-list.tpl'}
                {else}
                    {assign var='captured_amount' value=$refund.refunded_amount}

                    {include './partials/amount-list.tpl'}
                {/if}

                {l s='Refunded total:' mod='klarnapayment'} {$refund.refunded_amount|escape:'htmlall':'UTF-8'}
            </div>
        </div>
    </div>
{/foreach}
