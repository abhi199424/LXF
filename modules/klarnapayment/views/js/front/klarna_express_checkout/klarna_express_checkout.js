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

// Support case. KEC button is displaying twice on the product page.
document.onload = function () {
    localStorage.setItem('kecLoaded', 'false');
}
window.addEventListener('unload', function() {
    localStorage.setItem('kecLoaded', 'false');
});

window.klarnaAsyncCallback = function () {
    let kecLoaded = localStorage.getItem('kecLoaded') === 'true';
    if (document.querySelector('.klarnapayment-kec-wrapper') && !document.querySelector('.klarna-payments-button') && !kecLoaded) {
        new Promise((resolve) => {
            const intervalId = setInterval(() => {
                if (document.querySelector('.klarnapayment-kec-wrapper')) {
                    clearInterval(intervalId);
                    resolve();
                }
            }, 10);
        }).then(() => {
            window.Klarna.Payments.Buttons.init({
                client_id: klarnapayment.client_identifier,
            }).load(
                {
                    container: klarnapayment.container,
                    theme: klarnapayment.theme,
                    shape: klarnapayment.shape,
                    locale: klarnapayment.locale,
                    on_click: (authorize) => {
                        onClickHandler(authorize);
                    }
                },
            )
            localStorage.setItem('kecLoaded', 'true');
        });
    }
}

async function onClickHandler(authorize) {
    var payload;

    try {
        if (klarnapayment.isProductPage) {
            await addProductToCart();
            console.info('Express checkout data process end');
        }

        getPayload().then(function (result) {
            payload = JSON.parse(result).data.payload;

            authorize(
                {
                    collect_shipping_address: true,
                    auto_finalize: false,
                },
                payload,
                (result) => {
                    onAuthorizationHandler(result);
                }
            );
        })
    } catch (error) {
        console.error('Error processing express checkout:', error.message);
    }
}

async function onAuthorizationHandler(authorizeResult) {
    if (!authorizeResult.approved) {
        return;
    }

    sessionStorage.setItem(klarnapayment.expressCheckoutData, JSON.stringify(authorizeResult))

    var collectedShippingAddress = {
        city: authorizeResult.collected_shipping_address.city,
        country: authorizeResult.collected_shipping_address.country,
        email: authorizeResult.collected_shipping_address.email,
        family_name: authorizeResult.collected_shipping_address.family_name,
        given_name: authorizeResult.collected_shipping_address.given_name,
        phone: authorizeResult.collected_shipping_address.phone,
        postal_code: authorizeResult.collected_shipping_address.postal_code,
        street_address: authorizeResult.collected_shipping_address.street_address,
        street_address2: authorizeResult.collected_shipping_address.street_address2,
        region: authorizeResult.collected_shipping_address.region
    };

    enableKecFlow(authorizeResult.client_token, collectedShippingAddress).then(function () {
        location.href = klarnapayment.checkoutUrl;
    });
}

function enableKecFlow(clientToken, shippingAddress)
{
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'enableKecFlow',
                client_token: clientToken,
                shipping_address: shippingAddress
            },
            success: function () {
                console.info('Enabled KEC flow');
                resolve();
            },
            error: function () {
                reject(new Error('Failed to enable KEC flow'));
            }
        });
    });
}

function getProductData() {
    const productDetails = $('#product-details');

    if (
        !productDetails.length ||
        !productDetails.data('product')
    ) {
        throw new Error('Failed to retrieve product details from DOM');
    }

    const productData = productDetails.data('product');

    if (
        typeof productData !== 'object' ||
        !('id_product' in productData) ||
        !('id_product_attribute' in productData) ||
        !('quantity_wanted' in productData) ||
        !('id_customization' in productData)
    ) {
        throw new Error('Failed to find required data properties in product detail dataset');
    }

    return {
        'product_id': productData.id_product,
        'product_attribute_id': productData.id_product_attribute,
        'quantity': productData.quantity_wanted,
        'customization_id': productData.id_customization
    };
}

async function addProductToCart() {
    const productData = getProductData();

    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                product_data: productData,
                token: klarnapayment.staticToken,
                action: 'addProductToCart',
            },
            success: function () {
                resolve();
            },
            error: function (xhr) {
                const result = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                const errorMessage = result.errors || 'Unknown error occurred';

                reject(new Error(`Failed to add product to cart: ${errorMessage}`));
            }
        });
    });
}

function getPayload() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.expressCheckoutUrl,
            data: {
                ajax: true,
                token: klarnapayment.staticToken,
                action: 'getPayload',
            },
            success: function (response) {
                resolve(response);
            },
            error: function () {
                reject(new Error('Failed to get session payload for KEC'));
            }

        });
    });
}