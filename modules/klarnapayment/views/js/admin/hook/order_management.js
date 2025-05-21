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
$(document).ready(function () {
    $('.open-modal').on('click', function (e) {
        // NOTE: opening modal
        $('#' + $(this).data('target')).modal('show');
    });

    $('.select-all-check-box').on('click', function (e) {
        const $productWrapper = $(this).closest('.products-wrapper');

        $productWrapper.find('.individual-check').prop('checked', this.checked);
    });

    $('.individual-check').on('change', updateOrderLinesButtonState);

    $('.modal-wrapper .action-toggle-button').on('click', function (e) {
        const $modalWrapper = $(this).closest('.modal-wrapper');

        $modalWrapper.find('.products-wrapper-container').toggleClass('d-none');
        $modalWrapper.find('.amount-wrapper-container').toggleClass('d-none');
    });

    $(document).on('click', '.js-klarnapayment-capture-order', function (e) {
        if (!confirm(klarnapayment.orderManagementMessages.confirmations.captureOrder)) {
            e.preventDefault()
        }
    });

    $(document).on('click', '.js-klarnapayment-refund-order', function (e) {
        if (!confirm(klarnapayment.orderManagementMessages.confirmations.refundOrder)) {
            e.preventDefault()
        }
    });

    $(document).on('click', '.js-klarnapayment-cancel-order', function (e) {
        if (!confirm(klarnapayment.orderManagementMessages.confirmations.cancelOrder)) {
            e.preventDefault()
        }
    });

    $('.amount-input').on('keyup change', updateAmountButtonState);
})

function updateOrderLinesButtonState() {
    const $tableWrapper = $(this).closest('.table-wrapper');
    const $checkedCheckboxes = $tableWrapper.find('.individual-check:checked');
    const $modalWrapper = $(this).closest('.modal-wrapper');
    const remainingAmount = parseFloat($modalWrapper.find('#remaining-amount').attr('value'));

    let $submitOrderLinesButton = $tableWrapper.find('.submit-order-lines');
    let totalSelectedAmount = 0;

    $checkedCheckboxes.each(function () {
        totalSelectedAmount += parseFloat($(this).data('total-price'));
    });

    totalSelectedAmount = totalSelectedAmount.toFixed(2);

    if (totalSelectedAmount > 0 && totalSelectedAmount <= remainingAmount) {
        $submitOrderLinesButton.prop('disabled', false);
    } else {
        $submitOrderLinesButton.prop('disabled', true);
    }

    $submitOrderLinesButton.text(klarnapayment[$modalWrapper.data('context') + 'ButtonText'] + ' ' + formatPriceValue(totalSelectedAmount))
}

function updateAmountButtonState() {
    const $tableWrapper = $(this).closest('.table-wrapper');
    const $modalWrapper = $(this).closest('.modal-wrapper');
    const remainingAmount = parseFloat($modalWrapper.find('#remaining-amount').attr('value'));

    let amountInputValue = this.value

    let $submitOrderAmountButton = $tableWrapper.find('.submit-order-amount');

    if (amountInputValue.length > 0 && amountInputValue > 0 && amountInputValue <= remainingAmount) {
        $submitOrderAmountButton.prop('disabled', false);
    } else {
        $submitOrderAmountButton.prop('disabled', true);
    }

    $submitOrderAmountButton.text(klarnapayment[$modalWrapper.data('context') + "ButtonText"] + ' ' + formatPriceValue(amountInputValue));
}

function formatPriceValue(inputValue) {
    return new Intl.NumberFormat(
        klarnapayment.currentLocaleIsoCode,
        {style: 'currency', currency: klarnapayment.currentCurrencyIsoCode}
    ).format(inputValue)
}
