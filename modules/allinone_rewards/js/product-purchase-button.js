/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

var aior_loading = false;
if (typeof functions_to_load != 'undefined')
	functions_to_load.push('loadProductButton()');

jQuery(function($){
	// Pour les listes de produits
	if ($('#aior_add_to_cart_available_real').length > 0) {
		$(document).off('click', '.aior_add_to_cart').on('click', '.aior_add_to_cart', function(e) {
			e.preventDefault();
			var idProduct =  parseInt($(this).data('id-product'));
			var idProductAttribute =  parseInt($(this).data('id-product-attribute'));
			var price = $(this).data('aior-product-price-display');
			aior_add_to_cart(idProduct, idProductAttribute, price, false);
		});
	}

	// Pour la fiche produit
	$(document).off('click', '#aior_add_to_cart').on('click', '#product #aior_add_to_cart', function(e) {
		e.preventDefault();
		aior_add_to_cart($('#product_page_product_id').val(), aior_id_product_attribute, $('#aior_add_to_cart_price').html(), true);
	});
});

function aior_add_to_cart(idProduct, idProductAttribute, rewards, addedFromProductPage) {
	if (!aior_loading) {
		aior_loading = true;

		fancyConfirm(rewards, addedFromProductPage, function(ret) {
	    	if (ret === true) {
				$.ajax({
					type	: 'POST',
					cache	: false,
					url		: aior_product_purchase_url,
					dataType: 'json',
					data 	: 'ajax=true&action=purchase&id_product='+idProduct+'&id_product_attribute='+idProductAttribute,
					success: function(jsonData,textStatus,jqXHR) {
						if (addedFromProductPage)
							loadProductButton();
						else {
							$('#aior_add_to_cart_available_display').html(jsonData.aior_total_available_display);
							$('#aior_add_to_cart_available_real').html(jsonData.aior_total_available_real);
							$('.aior_add_to_cart').each(function() {
								if ($(this).data('aior-product-price-real') > jsonData.aior_total_available_real)
									$(this).css('display', 'none');
							});
						}

						if (!jsonData.has_error) {
							if (window.ajaxCart != undefined) {
								//$('#cart_block_list dl.products, .cart_block_list dl.products').remove();
								ajaxCart.refresh();
							}

							// presta 1.7
							if (window.prestashop != undefined) {
								// presta 1.7.8
								resp = prestashop;

								prestashop.emit('updateCart', {
									reason: {
						              	idProduct: idProduct,
						              	idProductAttribute: idProductAttribute,
						              	linkAction: 'add-to-cart',
						              	cart: resp.cart
						            },
						            resp,
		      					});
							} else
								$("#header #cart_block, .shopping_cart .cart_block").stop(true, true).slideDown(450);

							fancyAlert('<div class="aior_fancyalert">'+aior_success_message+'<br><br>'+aior_success_message2+' '+jsonData.aior_total_available_display+'.<br><br><button onClick="fancyClose();" class="btn btn-default">'+aior_success_message3+'</button><a href="'+aior_cart_url+'" class="btn btn-primary" style="margin-top: 10px; display: block;">'+aior_success_message4+'</a></div>', aior_success_message+'<br><br>'+aior_success_message2+' '+jsonData.aior_total_available_display+'.');
						} else
							fancyAlert(jsonData.error_msg);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						var error = "Impossible to add the product to the cart.<br>textStatus: '" + textStatus + "'<br>errorThrown: '" + errorThrown + "'<br>responseText:<br>" + XMLHttpRequest.responseText;
						fancyAlert(error);
					}
				});
			}
		});
		aior_loading = false;
	}
}

function fancyConfirm(rewards, addedFromProductPage, callback) {
    var ret;
    var message;
    if (!!$.prototype.fancybox) {
		message = 	'<div class="aior_fancyconfirm">' +
	    				'<div class="aior_fancyconfirm_title">'+aior_purchase_confirm_message0+'</div>' +
	    				'<div class="aior_fancyconfirm_message">'+
							'<br>'+aior_purchase_confirm_message1+' '+$('#aior_add_to_cart_available_display').html()+'.<br><br>'+
							rewards+' '+aior_purchase_confirm_message2+
							(addedFromProductPage ? '<br>'+aior_purchase_confirm_message3+' '+$('#aior_add_to_cart_available_after').html()+'.' : '')+'<br><br>'+
							aior_purchase_confirm_message4+
	    				'</div>' +
	    				'<div style="text-right" class="aior_fancyconfirm_button">' +
	    					'<button id="fancyconfirm_cancel" class="btn btn-default">'+aior_purchase_confirm_message5+'</button>' +
	    					'<button id="fancyConfirm_ok" class="btn btn-primary" style="margin-left: 4px;">'+aior_purchase_confirm_message6+'</button>' +
	    				'</div>' +
	    			'</div>';

	    $.fancybox(
		    [
		    	{
			        modal : true,
			        content : message,
			        afterShow : function() {
			            $("#fancyconfirm_cancel").click(function() {
			                ret = false;
			                fancyClose();

			            });
			            $("#fancyConfirm_ok").click(function() {
			                ret = true;
			                fancyClose();
			            });
			        },
			        afterClose : function() {
			            callback.call(this, ret);
			        },
			        // for prestashop < 1.5.5.0
			        onComplete : function() {
			            $("#fancyconfirm_cancel").click(function() {
			                ret = false;
			                fancyClose();

			            });
			            $("#fancyConfirm_ok").click(function() {
			                ret = true;
			                fancyClose();
			                callback.call(this, ret);
			            });
			        }
			    }
		    ]
	    );
	} else {
		message = aior_purchase_confirm_message0+'\n\n'+aior_purchase_confirm_message1+' '+$('#aior_add_to_cart_available_display').html()+'.\n\n'+rewards+' '+aior_purchase_confirm_message2+(addedFromProductPage ? '\n'+aior_purchase_confirm_message3+' '+$('#aior_add_to_cart_available_after').html()+'.' : '')+'\n\n'+aior_purchase_confirm_message4;
		ret = confirm(message.replace(/&nbsp;/g, ' '));
		callback.call(this, ret);
	}
}

function fancyClose() {
    $.fancybox.close(true);
}

function loadProductButton() {
	// if combination doesnt exist in back-end or quantity==0 and order out of stock is not allowed
	// or if product without combination and quantity==0 and order out of stock is not allowed

	// presta 1.7
	if (window.prestashop != undefined) {
		var hide = aior_combination_minimal_quantity > 1 || (!aior_allow_oosp && aior_combination_quantity==0);
	} else {
		var hide = (typeof combinations != 'undefined' && combinations.length > 0 && (aior_id_product_attribute==0 || aior_combination_minimal_quantity > 1 || (!aior_allow_oosp && aior_combination_quantity==0))) || ((typeof combinations == 'undefined' || combinations.length == 0) && $('#add_to_cart:visible').length == 0 && !aior_allow_oosp);
	}

	if (hide)
		$('#aior_product_button').hide();
	else {
		$.ajax({
			type	: 'POST',
			cache	: false,
			url		: aior_product_purchase_url,
			dataType: 'json',
			data 	: 'ajax=true&id_product='+$('#product_page_product_id').val()+'&id_product_attribute='+aior_id_product_attribute,
			success: function(jsonData,textStatus,jqXHR) {
				if (!jsonData.has_error) {
					$('#aior_add_to_cart_price').html(jsonData.aior_product_price_display);
					$('#aior_add_to_cart_available_display').html(jsonData.aior_total_available_display);
					$('#aior_add_to_cart_available_real').html(jsonData.aior_total_available_real);
					$('#aior_add_to_cart_available_after').html(jsonData.aior_total_available_after);
					$('#aior_product_button').show();
				} else
					$('#aior_product_button').hide();
				if (!jsonData.aior_show_buy_button) {
					$('#loyalty').addClass('aior_unvisible');
					// presta 1.5 and 1.6
					$('#quantity_wanted_p').addClass('aior_unvisible');
					$('#add_to_cart').addClass('aior_unvisible');
					// presta 1.7
					$('.product-add-to-cart').addClass('aior_unvisible');
				} else {
					$('#loyalty').removeClass('aior_unvisible');
					// presta 1.5 and 1.6
					$('#quantity_wanted_p').removeClass('aior_unvisible');
					$('#add_to_cart').removeClass('aior_unvisible');
					// presta 1.7
					$('.product-add-to-cart').removeClass('aior_unvisible');
				}
			}
		});
	}
}

function fancyAlert(msg, msg1=false) {
	if (!!$.prototype.fancybox) {
		$.fancybox(
			[
				{
					content : '<p class="fancybox-error">' + msg + '</p>'
				}
			],
		);
	} else {
		if (!msg1)
			alert(msg.replace(/&nbsp;/g, ' ').replace(/<br>/g, '\n'));
		else
			alert(msg1.replace(/&nbsp;/g, ' ').replace(/<br>/g, '\n'));
	}
}