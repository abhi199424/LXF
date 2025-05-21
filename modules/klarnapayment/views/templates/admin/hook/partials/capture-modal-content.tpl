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
{assign var='show_checkboxes' value=true}

<form id="klarnapayment-admin-form" method="post"
      action="{$klarnaPaymentOrder.action|escape:'htmlall':'UTF-8'}">
    <div class="modal fade modal-wrapper" id="capture-modal-{$klarnaPaymentOrder.id|escape:'htmlall':'UTF-8'}" data-context="capture">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-content-wrapper">
                <input type="hidden" id="remaining-amount" value="{$klarnaPaymentOrder.remainingCaptureAmount|escape:'htmlall':'UTF-8'}">

                <div class="products-wrapper-container table-wrapper {if $klarnaPaymentOrder.isZeroComputingPrecision}d-none{/if}">
                    <div>
                        {if $klarnaPaymentOrder.isZeroComputingPrecision}
                            <div class="alert alert-warning" role="alert">
                                {l s='Due to zero decimal in shop configurations capture by products is unavailable.' mod='klarnapayment'}
                            </div>
                        {/if}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title">
                            {l s='You can capture up to' mod='klarnapayment'} {$klarnaPaymentOrder.remainingCaptureAmountFormatted|escape:'htmlall':'UTF-8'}
                        </h5>
                    </div>

                    {assign var='order_lines' value=$klarnaPaymentOrder.productsToCapture}
                    {include './products-list.tpl'}

                    <div class="row">
                        <div class="col-3 btn-display">
                            <div class="action">
                                <a class="action-toggle-button">{l s='Capture amount' mod='klarnapayment'}</a>
                            </div>
                        </div>
                        <div class="col-9">
                            <div class="action action-btn">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='klarnapayment'}</button>
                                {if !$klarnaPaymentOrder.isZeroComputingPrecision}
                                    <button type="submit" class="btn btn-primary submit-order-lines js-klarnapayment-capture-order" name="capture-order-lines" disabled>{l s='Capture' mod='klarnapayment'}</button>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="amount-wrapper-container table-wrapper {if !$klarnaPaymentOrder.isZeroComputingPrecision}d-none{/if}">
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title">
                            {l s='You can capture up to' mod='klarnapayment'} {$klarnaPaymentOrder.remainingCaptureAmountFormatted|escape:'htmlall':'UTF-8'}
                        </h5>
                    </div>

                    <div class="modal-body modal-body-wrapper">
                        <div class="form-group">
                            <label>{l s='Amount to capture' mod='klarnapayment'}</label>
                            <input type="number"
                                   class="form-control col-6 amount-input"
                                   name="capture_amount"
                                   pattern="\d+(\.\d(1,2))?"
                                   step="0.01"
                                   min="0.01"
                                   max="{$klarnaPaymentOrder.remainingCaptureAmount|escape:'htmlall':'UTF-8'}"
                                   placeholder="{$klarnaPaymentOrder.remainingCaptureAmountFormatted|escape:'htmlall':'UTF-8'}"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-3 btn-display">
                            <div class="action">
                                <a class="action-toggle-button">{l s='Capture order lines' mod='klarnapayment'}</a>
                            </div>
                        </div>
                        <div class="col-9">
                            <div class="action action-btn">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='klarnapayment'}</button>
                                <button type="submit" class="btn btn-primary submit-order-amount js-klarnapayment-capture-order" name="capture-order-amount" disabled>{l s='Capture' mod='klarnapayment'}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
