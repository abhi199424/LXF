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
{foreach $klarnaPaymentOrder.captures as $key => $capture}
    <div class="pl-0 table-wrapper captures-list-wrapper {if $isLegacyHook}col-md-4 left-column{/if}">
        <div class="panel card">
            <div class="panel-heading card-header">
                <div class="d-flex justify-content-between">
                    <span>{l s='Capture #' mod='klarnapayment'}{$key|escape:'htmlall':'UTF-8' + 1}</span>
                </div>
            </div>

            <div class="container py-3">
                {if !empty($capture.order_lines)}
                    {assign var='order_lines' value=$capture.order_lines}

                    {include './partials/products-list.tpl'}
                {else}
                    {assign var='captured_amount' value=$capture.captured_amount}

                    {include './partials/amount-list.tpl'}
                {/if}

                {if $klarnaPaymentOrder.canRefund}
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-primary open-modal" data-target="refund-modal-{$capture.capture_id|escape:'htmlall':'UTF-8'}">
                            {l s='Refund' mod='klarnapayment'}
                        </a>
                    </div>
                {/if}
            </div>
        </div>
    </div>

    {include './partials/refund-modal-content.tpl'}
{/foreach}
