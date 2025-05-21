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
    const expressCheckoutData = JSON.parse(sessionStorage.getItem(klarnapayment.expressCheckoutData));

    if (!expressCheckoutData) {
        return;
    }

    console.info('Express checkout data prefill started');

    prefillCustomerForm(expressCheckoutData);
    prefillAddressForm(expressCheckoutData);

    console.info('Express checkout data prefill in payment form finished');
});

function prefillCustomerForm(expressCheckoutData)
{
    new Promise((resolve) => {
        const intervalId = setInterval(() => {
            const $customerFormInputsExist = ($('input[name="email"], input[name="email_confirmation"], input[name="supercheckout_email"]').length > 0 && $('input[name="firstname"], input[name="customer_firstname"], input[name="shipping_address[firstname]"]').length > 0);
            if ($customerFormInputsExist) {
                clearInterval(intervalId);
                resolve();
            }
        }, 10);
    }).then(() => {
        $(document).find('input[name="firstname"] , input[name="customer_firstname"], input[name="shipping_address[firstname]"]').val(expressCheckoutData.collected_shipping_address.given_name);
        $(document).find('input[name="lastname"], input[name="customer_lastname"], input[name="shipping_address[lastname]"]').val(expressCheckoutData.collected_shipping_address.family_name);
        $(document).find('input[name="email"], input[name="email_confirmation"], input[name="supercheckout_email"]').val(expressCheckoutData.collected_shipping_address.email);

        console.info('Express checkout data prefill in customer form finished');
    })
}

async function prefillAddressForm(expressCheckoutData) {
    await new Promise((resolve) => {
        const intervalId = setInterval(() => {
            const $addressFormInputsExist = ($('input[name="address1"], input[name="delivery_address1"], input[name="shipping_address[address1]"]').length > 0 && $('input[name="postcode"], input[name="delivery_postcode"], input[name="shipping_address[postcode]"]').length > 0);

            if ($addressFormInputsExist) {
                clearInterval(intervalId);
                resolve();
            }
        }, 10);
    });

    $(document).find('input[name="firstname"], input[name="delivery_firstname"], input[name="shipping_address[firstname]"]').val(expressCheckoutData.collected_shipping_address.given_name);
    $(document).find('input[name="lastname"], input[name="delivery_lastname"], input[name="shipping_address[lastname]"]').val(expressCheckoutData.collected_shipping_address.family_name);
    $(document).find('input[name="address1"], input[name="delivery_address1"], input[name="shipping_address[address1]"]').val(expressCheckoutData.collected_shipping_address.street_address);
    $(document).find('input[name="address2"], input[name="delivery_address2"], input[name="shipping_address[address2]"]').val(expressCheckoutData.collected_shipping_address.street_address2);
    $(document).find('input[name="postcode"], input[name="delivery_postcode"], input[name="shipping_address[postcode]"]').val(expressCheckoutData.collected_shipping_address.postal_code);
    $(document).find('input[name="city"], input[name="delivery_city"], input[name="shipping_address[city]"]').val(expressCheckoutData.collected_shipping_address.city);
    $(document).find('input[name="phone"], input[name="delivery_phone_mobile"], input[name="shipping_address[phone_mobile]"]').val(expressCheckoutData.collected_shipping_address.phone);

    try {
        const { countryId, regionId } = await getCountryData(
            expressCheckoutData.collected_shipping_address.country,
            expressCheckoutData.collected_shipping_address.region
        );

        $(document).find('select[name="id_country"], select[name="delivery_id_country"], select[name="shipping_address[id_country]"]').val(countryId).change();

        prestashop.on('updatedAddressForm', function (event) {
            $(document).find('select[name="id_state"], select[name="delivery_id_state"], select[name="shipping_address[id_state]"]').val(regionId).change();
        });
    } catch (error) {
        console.error('Failed to retrieve country data. Error message: ', error.message);
    }

    console.info('Express checkout data prefill in address form finished');
}

async function getCountryData(countryIsoCode, stateIsoCode) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                country_iso_code: countryIsoCode,
                state_iso_code: stateIsoCode,
                token: klarnapayment.staticToken,
                action: 'getAddressData',
            },
            success: function (response) {
                const jsonResponse = JSON.parse(response);

                if (!('data' in jsonResponse) || !('countryId' in jsonResponse.data) || !('stateId' in jsonResponse.data)) {
                    reject(new Error(`Missing required data properties. Response: ${response}`));
                }

                resolve({countryId: jsonResponse.data.countryId, regionId: jsonResponse.data.stateId});
            },
            error: function (xhr) {
                const result = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                const errorMessage = result.errors || 'Unknown error occurred';

                reject(new Error(`Failed to retrieve address data: ${errorMessage}`));
            }
        });
    });
}
