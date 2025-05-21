/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

jQuery(function($){
	if (window.prestashop != undefined) {
		// presta 1.7
		prestashop.on('updatedCart', function (event) {
			$.ajax({
				type	: 'POST',
				cache	: false,
				url		: url_allinone_loyalty,
				dataType: 'html',
				data 	: 'reload_cart=1',
				success : function(data) {
					if (data == '')
						$('#reward_loyalty').hide().html('');
					else
						$('#reward_loyalty').replaceWith(data).show();
				}
			});
		});
	}
});