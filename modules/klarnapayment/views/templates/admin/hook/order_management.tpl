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
{if !empty($klarnaPaymentOrder.orderReference)}
    {assign var='show_checkboxes' value=false}

    <div class="pl-0 {if $isLegacyHook}col-md-4 left-column{/if}">
        <div class="panel card">
            <div class="panel-heading card-header">
                <div class="d-flex justify-content-between">
                    <span>{l s='Manage Klarna Order' mod='klarnapayment'}</span>
                    <a href="{$klarnaPaymentOrder.orderUrl|escape:'htmlall':'UTF-8'}"
                       target="_blank">({$klarnaPaymentOrder.orderReference|escape:'htmlall':'UTF-8'})</a>
                </div>
            </div>
            {if $warnings}
                {include file='module:klarnapayment/views/templates/admin/settings/warning-box.tpl' warningBox=$warnings customStyles=$customStyles}
            {/if}

            <div class="card-body order-management-content">
                <p>{l s='Payment method' mod='klarnapayment'}:
                    &nbsp;{$klarnaPaymentOrder.paymentMethod|escape:'htmlall':'UTF-8'}</p>
                <p>{l s='Captured' mod='klarnapayment'}:
                    &nbsp;{$klarnaPaymentOrder.capturedAmountFormatted|escape:'htmlall':'UTF-8'}</p>
                <p>{l s='Refunded' mod='klarnapayment'}:
                    &nbsp;{$klarnaPaymentOrder.refundedAmountFormatted|escape:'htmlall':'UTF-8'}</p>
                <p>{l s='Left to capture' mod='klarnapayment'}:
                    &nbsp;{$klarnaPaymentOrder.remainingCaptureAmountFormatted|escape:'htmlall':'UTF-8'}</p>

                {if $klarnaPaymentOrder.canInteract}
                    <input type="hidden" name="orderId" value="{$klarnaPaymentOrder.id|escape:'htmlall':'UTF-8'}">
                    <div class="row">
                        {if $klarnaPaymentOrder.canCapture}
                            <div class="col-lg-12 button-container">
                                <div
                                        class="btn btn-primary w-100 text-uppercase outline-black open-modal"
                                        data-toggle="modal"
                                        data-target="capture-modal-{$klarnaPaymentOrder.id|escape:'htmlall':'UTF-8'}"
                                >
                                    {l s='Capture order' mod='klarnapayment'}
                                </div>
                            </div>

                            {include file='./partials/capture-modal-content.tpl'}
                        {/if}
                    </div>

                    <div class="row">
                        {if $klarnaPaymentOrder.canCancel}
                            <div class="col-lg-12">
                                <form id="klarnapayment-admin-form" method="post"
                                      action="{$klarnaPaymentOrder.action|escape:'htmlall':'UTF-8'}">
                                    <div class="button-container">
                                        <button class="btn btn-danger w-100 text-uppercase outline-black js-klarnapayment-cancel-order"
                                                type="submit"
                                                name="cancel-order">{l s='Cancel order' mod='klarnapayment'}</button>
                                    </div>
                                </form>
                            </div>
                        {/if}
                    </div>
                {else}
                    <div class="row">
                        <div class="col-md-12 action-container">
                            <div class="d-inline-flex justify-content-center align-items-center h-100 w-100">
                                <span class="font-weight-bold">{l s='No actions available' mod='klarnapayment'}</span>
                            </div>
                        </div>
                    </div>
                {/if}

                {if !empty($klarnaPaymentOrder.captures)}
                    {include file='./captures.tpl'}
                {/if}

                {if !empty($klarnaPaymentOrder.refunds)}
                    {include file='./refunds.tpl'}
                {/if}
            </div>
        </div>
    </div>
{/if}


