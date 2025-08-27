$(document).ready(function(){
	var $gird_items = $("#table-spfilterproducts tbody");
	$gird_items.sortable({
		opacity: 0.8,
		cursor: "move",
		handle: ".dragGroup",
		update: function() {
			var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
			$.ajax({
				type: "POST",
				dataType: "json",
				data:order,
				url:baseDir +'modules/spfilterproducts/updateposition.php?secure_key='+token,
				success: function (msg){
					if (msg.error)
					{
						showErrorMessage(msg.error);
						return;
					}
					$(".positions", $gird_items).each(function(i){
						$(this).text(i+1);
					});
					showSuccessMessage(msg.success);
				}
			});
		}
	});

	var spco_tabs = $('#spco_tabs');
	var spco_content = $('#spco_content');
	var url_ajax = spco_content.attr('data-url');
	$('div[id^="fieldset_"]', spco_content).addClass('spco-panel');
	$('div[id^="fieldset_"]:first', spco_content).addClass('spco-open');
	$('.tab-page', spco_tabs).on('click', function (e){
		e.preventDefault();
		var elem = $(this);
		var target = $(elem.data('target'));
		elem.parent().addClass('active').siblings().removeClass('active');
		$('.spco-panel').removeClass('spco-open');
		target.addClass('spco-open');
	});

	$(document).on('change', 'select[name=SPFP_TYPE_SHOW]', function() {
		if ($(this).val() == 2) {
			$('.spfp-slide').removeClass('hide').show('slow');
		} else {
			$('.spfp-slide').addClass('hide').hide('slow');
		}
	});

	$(document).on('change', 'select[name=SPFP_SELECT_SOURCE]', function() {
		if ($(this).val() == 'other_products') {
			$('.spfp-in-category').removeClass('hide').show('slow');
		} else {
			$('.spfp-in-category').addClass('hide').hide('slow');
		}
	});

	$(document).on('change', 'select[name=SPFP_SELECT_SOURCE]', function() {
		if ($(this).val() == 'countdown_products') {
			$('.spfp-countdown').removeClass('hide').show('slow');
		} else {
			$('.spfp-countdown').addClass('hide').hide('slow');
		}
	});
	
	$(document).on('change', 'select[name=SPFP_SELECT_SOURCE]', function() {
		if ($(this).val() == 'featured_products') {
			$('.spfp-random').removeClass('hide').show('slow');
		} else {
			$('.spfp-random').addClass('hide').hide('slow');
		}
	});
});