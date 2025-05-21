/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

jQuery(function($){
	var span = $('.cart-voucher .js-error-text');
	if (span.length > 0) {
		var observer = new MutationObserver(function(mutations) {
			if (span.html().indexOf('[AIOR]') >= 0) {
				observer.disconnect();
				span.html(span.html().substr(6));
	 			span.parent().addClass('sponsorship-promo-code');
				observer.observe(span[0], config);
	 		} else
				span.parent().removeClass('sponsorship-promo-code');
		});
		// Configure and start observing the target element for changes in child nodes
	 	var config = { childList: true, subtree: false };
	 	observer.observe(span[0], config);
	}
});