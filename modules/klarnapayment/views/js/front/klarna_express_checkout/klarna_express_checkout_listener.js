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
        'updatedProduct',
        function () {
            // Check if klarnaAsyncCallback is defined before calling it
            if (window.klarnaAsyncCallback) {
                localStorage.setItem('kecLoaded', 'false');
                window.klarnaAsyncCallback();
            } else {
                console.error('klarnaAsyncCallback is not defined.');
            }
        }
    );

    prestashop.on(
        'updatedCart',
        function () {
            // Check if klarnaAsyncCallback is defined before calling it
            if (window.klarnaAsyncCallback) {
                localStorage.setItem('kecLoaded', 'false');
                window.klarnaAsyncCallback();
            } else {
                console.error('klarnaAsyncCallback is not defined.');
            }
        }
    );
});
