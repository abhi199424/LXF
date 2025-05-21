/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

$(document).ready(function () {
    handleFormSubmit();
    new Promise((resolve) => {
        const intervalId = setInterval(() => {
            const radioButtons = $('[data-module-name*="klarnapayment"]');

            if (radioButtons.length > 0) {
                clearInterval(intervalId);
                resolve();
            }
        }, 10);
    }).then(() => {
        initPaymentOptions();

        $('[data-module-name*="klarnapayment"]').each(function (input) {

            if ($("[name=klarnapaymentIsKec]").val()) {
                $('.ps-shown-by-js[data-module-name="klarnapayment"]').first().prop('checked', true).trigger('change');
            }
        });
    });

    function loadKlarnaWidget(container, paymentMethodCategory) {
        handleSubmitButtonStatus('#' + container);
        var clientToken = $(document).find("[name=klarnapaymentClientToken]").val();

        $.getScript(klarnapayment.cdn_url, function () {
            Klarna.Payments.init({
                client_token: clientToken
            });

            Klarna.Payments.load({
                container: '#' + container,
                payment_method_category: paymentMethodCategory
            }, function (res) {
                removeLoadingScreen();
                toggleDisableSubmitButton(false);

                if (res.error) {
                    console.error(res.error);
                }
            });
        });
    }

    if (!initPaymentOptions()) {
        // Module "thecheckout" compatibility
        prestashop.on(
            'thecheckout_updatePaymentBlock',
            function () {
                initPaymentOptions();
            }
        );
    }

    // Module "onepagecheckoutps" compatibility
    $(document).on('opc-load-review:completed', function () {
        initPaymentOptions();
    });

    $(document).ajaxSend(function (event, xhr, settings) {
        if (settings.data === undefined || typeof settings !== 'object') {
            return;
        }

        // Module "supercheckout" compatibility
        try {
            if (
                settings.data?.indexOf("loadPaymentAdditionalInfo") !== -1
                && settings.data?.indexOf("loadPaymentAdditionalInfo") !== undefined
            ) {
                initPaymentOptions();
            }

            // Module "thecheckout" compatibility
            if (
                settings.data?.indexOf("selectPaymentOption") !== -1
                && settings.data?.indexOf("selectPaymentOption") !== undefined
            ) {
                initPaymentOptions();
            }
        }
        catch (error) {
            // Left empty intentionally
        }
    });

    function initPaymentOptions() {
        var options = $('.klarnapayment-option');

        if (options.length < 1) {
            console.log('No payment options found.');

            return false;
        }

        options.each(function (index, option) {
            loadKlarnaWidget(option.id, $(option).data('payment_method_category'));
        });
    }

    function processPayment() {
        const isKec = $("[name=klarnapaymentIsKec]").val();

        toggleDisableSubmitButton(true);
        hideErrorMessages();

        const $id = $('[data-module-name*="klarnapayment"]:checked').attr('id');
        const $paymentMethodCategory = $('#' + $id + '-additional-information')?.find('.klarnapayment-option')?.attr('data-payment_method_category');

        $('<div class="klarnapayment-loading"></div>').appendTo('body');

        //NOTE: if payment method does not exist which might be case on OPC we set it to default flow
        finalizePayment($paymentMethodCategory ? $paymentMethodCategory : 'pay_now', isKec).then((result) => {
            $('.klarnapayment-loading').remove();

            if (!result) {
                toggleDisableSubmitButton(false);
                authorizeFailedCallback();

                return;
            }

            const url = new URL(klarnapayment.payment_url);
            url.searchParams.append('authorization_token', result);

            window.location.href = url.href;
        });
    }

    function finalizePayment(paymentMethodCategory, isKec) {
        // NOTE: Klarna express checkout payment processing
        if (isKec) {
            return finalize(getOrderDetails());
        }

        // NOTE: Klarna standard flow payment processing
        return authorize(paymentMethodCategory, getOrderDetails());
    }

    function authorize(payment_method_category, data) {
        return new Promise(function (resolve, reject) {
            console.info('Calling Klarna authorize.');

            Klarna.Payments.authorize({
                    payment_method_category: payment_method_category
                },
                data,
                function (res) {
                    if (!res.show_form && !res.approved) {
                        resolve();
                    }

                    if (res.show_form && !res.approved) {
                        resolve();
                    }

                    if (res.show_form && res.approved) {
                        resolve(res.authorization_token);
                    }
                }
            );
        });
    }

    function finalize(payload) {
        return new Promise(function (resolve, reject) {
            console.info('Calling Klarna Finalize');

            Klarna.Payments.finalize({}, payload, function (res) {
                if (!res.show_form && !res.approved) {
                    resolve();
                }

                if (res.show_form && !res.approved) {
                    resolve();
                }

                if (res.show_form && res.approved) {
                    resolve(res.authorization_token);
                }
            });
        });
    }

    function getOrderDetails() {
        var result;

        $.ajax({
            type: 'GET',
            url: klarnapayment.get_order_details_url,
            async: false,
            success: function (response) {
                result = JSON.parse(response);
            },
            error: function (error) {
                showError(error);

                return;
            }
        });

        return result.data.order_details;
    }

    function showError(error) {
        $(".klarnapayment-error").find('p').text(error).show();
        $(".klarnapayment-error").show();
    }

    function hideErrorMessages() {
        $(".klarnapayment-generic-error").hide();
        $(".klarnapayment-error").hide();
    }

    function toggleDisableSubmitButton(disable) {
        var submitButton = $('#payment-confirmation button');

        if (submitButton.length) {
            submitButton.attr('disabled', disable);

            if (disable) {
                submitButton.addClass('klarnapayment-disabled');
            } else {
                submitButton.removeClass('klarnapayment-disabled');
            }
        }
    }

    function handleSubmitButtonStatus(klarnaPaymentMethodIdentifier) {
        var paymentOptions = $('.payment-options .payment-option input');

        if (paymentOptions.length < 1) {
            return;
        }

        const firstPaymentOption = paymentOptions.first();

        if (firstPaymentOption.data('module-name') === 'klarnapayment' && firstPaymentOption.is(':checked')) {
            toggleDisableSubmitButton(!($(klarnaPaymentMethodIdentifier).children().length > 0));
        } else {
            toggleDisableSubmitButton(false);
        }

        paymentOptions.on('change', function () {
            if ($(this).data('module-name') === 'klarnapayment' && $(this).is(':checked')) {
                toggleDisableSubmitButton(!($(klarnaPaymentMethodIdentifier).children().length > 0));
            } else {
                toggleDisableSubmitButton(false);
            }
        });
    }

    function removeLoadingScreen() {
        $('.klarnapayment-loading-background').remove();
        $('.klarnapayment-coc-text-block').show();
    }

    function handleFormSubmit() {
        var formSubmitProcessing = false;
        var buttonClickProcessing = false;

        $('body').on('submit', '[id^=pay-with-][id$=-form] form', function (e) {
            if (!isKlarnaPaymentOptionSelected()) {
                return;
            }

            if (buttonClickProcessing) {
                e.preventDefault();
                return;
            }
            formSubmitProcessing = true;

            handlePaymentFormSubmit(e)

        });

        const checkoutSelector = prestashop?.selectors?.checkout?.confirmationSelector;
        const buttonWrapperSelector = checkoutSelector || '#payment-confirmation button, .js-payment-confirmation';

        $('body').on('click', buttonWrapperSelector + ' button', function (e) {
            if (!isKlarnaPaymentOptionSelected()) {
                return;
            }

            if (formSubmitProcessing) {
                return;
            }

            buttonClickProcessing = true;
            handlePaymentFormSubmit(e)
        })
    }

    function isKlarnaPaymentOptionSelected() {
        return $('[data-module-name*="klarnapayment"]:checked').length > 0;
    }

    function handlePaymentFormSubmit(e) {
        if (!isKlarnaPaymentOptionSelected()) {
            return;
        }

        const isKec = $("[name=klarnapaymentIsKec]").val();

        // NOTE: HPP payment do not require any front end logic. We just let to handle flow as normal payment
        if (klarnapayment.is_hpp_enabled && !isKec) {
            if ($('input[name="payment-option"]:checked').length) {
                var option = $('input[name="payment-option"]:checked').attr('id');
                window.location.href = $(`#pay-with-${option}-form form`)[0].action;

                return;
            } else {
                // "supercheckout" module compatibility
                var option = $('input[data-module-name="klarnapayment"]:checked').attr('id').replace('payment-option-', '');
                window.location.href = $(`#payment-option-${option}-additional-information form`)[0].action;

                return;
            }
        }

        e.preventDefault();
        e.stopImmediatePropagation();
        processPayment();
    }

    function authorizeFailedCallback() {
        // supercheckout compatibility
        if (typeof hide_progress == 'function') {
            hide_progress();
        }
    }
});
