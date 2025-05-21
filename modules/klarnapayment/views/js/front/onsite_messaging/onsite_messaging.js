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
    prestashop.on(
        'updatedCart',
        function () {
            /**
             * NOTE: price comes with currency sign
             */
            const $price = $('div.cart-total .value');

            if ($price.length < 1) {
                return;
            }

            const price = cleanPriceValue($price.text());

            if (price.length < 1) {
                return;
            }

            $('klarna-placement').attr(
                'data-purchase-amount',
                formatIntegerPrice(price)
            );

            window.Klarna.OnsiteMessaging.refresh()
        }
    );

    prestashop.on(
        'updatedProduct',
        function () {
            const $price = $('.js-product-prices .current-price .current-price-value');
            const $quantity = $('.js-product-add-to-cart .product-quantity #quantity_wanted');

            if ($price.length < 1 || $quantity.length < 1) {
                return;
            }

            const price = $price.attr('content');
            const quantity = $quantity.val();

            if (price.length < 1 || quantity.length < 1) {
                return;
            }

            $('klarna-placement').attr(
                'data-purchase-amount',
                formatIntegerPrice(price * quantity)
            );

            window.Klarna.OnsiteMessaging.refresh()
        }
    );

    function cleanPriceValue(price) {
        if (klarnapayment.precision === 0) {
            return price.replace(/\D/g, '');
        }

        // Remove any spaces between digits and replace commas with periods
        const formattedPrice = price.replace(/[^0-9.,]/g, '').replace(/,/, '.');

        // Keep only the last period
        return formattedPrice.replace(/\.(?=.*\.)/g, '');
    }

    function formatIntegerPrice(price) {
        const integerPrice = parseFloat(price).toFixed(2) * 100;

        return Math.round(integerPrice);
    }
});
