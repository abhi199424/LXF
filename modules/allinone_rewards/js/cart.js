/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

jQuery(function($){
	prestashop.on('updatedCart', function (event) {
		$('.remove-from-cart[data-id-product='+aior_id_default_gift_product+']').hide();
		$('.remove-from-cart[data-id-product='+aior_id_default_gift_product+']').parents('.product-line-grid').find('.qty div').hide();
	});
	$('.remove-from-cart[data-id-product='+aior_id_default_gift_product+']').hide();
	$('.remove-from-cart[data-id-product='+aior_id_default_gift_product+']').parents('.product-line-grid').find('.qty div').hide();
});