/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(function(){
  $(document).ajaxComplete(function() {
      setTimeout(function() {
          const $backdrops = $('.modal-backdrop.fade.in');
          if ($backdrops.length > 1) {
              $backdrops.slice(1).remove();
          }
      }, 2000);
  });
  $(document).on('click', '.blockcart.cart-preview.active', function(e) {
      e.preventDefault();
      
      $('#blockcart-modal').modal('show');
  });

  $(document).on('click', '#blockcart-modal .close', function(e) {
    $('#blockcart-modal').fadeOut(200);
    setTimeout(function() {
      $('.modal-backdrop.fade.in').remove();
    }, 800);
  });


  $(document).on('mouseenter mouseleave', '.thumbnail-wrapper', function (e) {
    if (e.type === 'mouseenter') {
      $(this).find('.thumb-info').show();
    } else {
      $(this).find('.thumb-info').hide();
    }
  });
});


$(document).ready(function () {
    function updateMiniCart($input, op) {
        let id_product, id_product_attribute, qty;
        if (op == 'delete') {
            id_product = $input.data('id-product');
            id_product_attribute = $input.data('id-product-attribute');
            qty = 0;
        } else {
            const $wrapper = $input.closest('.qty-box').find('.input-group input');
            id_product = $wrapper.data('id-product');
            id_product_attribute = $wrapper.data('id-product-attribute');
            qty = parseInt($input.val()) || 1;
        }

        const token = prestashop.static_token || $('meta[name=static-token]').attr('content');

        $('.overly-mini-cart-pop').show();
        $('.loader-mini').show();
        if(id_product) {
            $.ajax({
                url: '/modules/minicart/ajax.php',
                method: 'GET',
                data: {
                    id_product: id_product,
                    id_product_attribute: id_product_attribute,
                    qty: qty,
                    op: op,
                    token: token
                },
                success: function (response) {
                    try {
                        const res = JSON.parse(response);

                        if (res.cart) {
                            if (res.cart_count == 0) {
                                location.reload();
                            } else {
                                $('.cart_pop_container').html(res.cart);
                                $('.cart-products-count').text(res.cart_count);
                                setTimeout(function () {
                                    $('.overly-mini-cart-pop').hide();
                                    $('.loader-mini').hide();
                                }, 1000);
                            }
                        } else {
                            console.error('Cart update failed.');
                            $('.overly-mini-cart-pop').hide();
                            $('.loader-mini').hide();
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        $('.overly-mini-cart-pop').hide();
                        $('.loader-mini').hide();
                    }
                },
                error: function () {
                    console.error('AJAX error');
                    $('.overly-mini-cart-pop').hide();
                    $('.loader-mini').hide();
                }
            });
        }
        
    }
    const isMobile = /Mobi|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    if (isMobile) {
        function safeUpdateMiniCart($input, op) {
            // Slight delay to handle scroll conflicts on mobile
            setTimeout(() => {
                updateMiniCart($input, op);
            }, 80);
        }

        // Use pointerup + click for full compatibility
        const miniCartEvents = ['pointerup', 'click'];

        // Bind event handlers
        miniCartEvents.forEach(evt => {
            // Quantity up
            document.body.addEventListener(evt, function (e) {
                if (e.target.closest('.btn-touchspin.bootstrap-touchspin-up')) {
                    const $input = $(e.target).closest('.input-group').find('.js-cart-line-product-quantity-mini');
                    safeUpdateMiniCart($input, 'up');
                }
            });

            // Quantity down
            document.body.addEventListener(evt, function (e) {
                if (e.target.closest('.btn-touchspin.bootstrap-touchspin-down')) {
                    const $input = $(e.target).closest('.input-group').find('.js-cart-line-product-quantity-mini');
                    safeUpdateMiniCart($input, 'down');
                }
            });

            // Remove from cart
            document.body.addEventListener(evt, function (e) {
                if (e.target.closest('.remove-from-cart-mini')) {
                    const $target = $(e.target).closest('.remove-from-cart-mini');
                    safeUpdateMiniCart($target, 'delete');
                }
            });
        });

        // Blur event for input remains the same
        $(document).on('blur', '.js-cart-line-product-quantity-mini', function () {
            updateMiniCart($(this), 'input');
        });
    } else {
        // Quantity increase
        $(document).on('click', '.btn-touchspin.bootstrap-touchspin-up', function () {
            const $input = $(this).closest('.input-group').find('.js-cart-line-product-quantity-mini');
            updateMiniCart($input, 'up');
        });

        // Quantity decrease
        $(document).on('click', '.btn-touchspin.bootstrap-touchspin-down', function () {
            const $input = $(this).closest('.input-group').find('.js-cart-line-product-quantity-mini');
            updateMiniCart($input, 'down');
        });

        // On blur/input change
        $(document).on('blur', '.js-cart-line-product-quantity-mini', function () {
            updateMiniCart($(this), 'input');
        });

        // Remove item
        $(document).on('click', '.remove-from-cart-mini', function () {
            updateMiniCart($(this), 'delete');
        });
    }
});

