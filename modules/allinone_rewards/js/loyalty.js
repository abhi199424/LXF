/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

functions_to_load.push('loadLoyalty()');

function loadLoyalty() {
	if (typeof(url_allinone_loyalty) !== 'undefined') {
		var quantity = 1;
		if ($('#quantity_wanted').length > 0 && $('#quantity_wanted').val() > 0)
			quantity = $('#quantity_wanted').val();
		$.ajax({
			type	: 'POST',
			cache	: false,
			url		: url_allinone_loyalty,
			dataType: 'html',
			data 	: 'id_product='+$('#product_page_product_id').val()+'&id_product_attribute='+aior_id_product_attribute+'&quantity='+quantity,
			success : function(data) {
				if (data == '')
					$('#loyalty').hide().html('');
				else
					$('#loyalty').html(data).show();
			}
		});
	} else
		console.log('All-in-one Rewards : ERROR, url_allinone_loyalty is not initialized');
}