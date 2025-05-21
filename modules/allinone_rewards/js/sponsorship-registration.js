/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

jQuery(function($) {
	if (typeof ps_version != 'undefined' && ps_version > '1.7')
		initSponsorshipJS();
});


function initSponsorshipJS() {
	$('form').delegate('input[name=sponsorship]', 'focus', function(e){
		$('#sponsorship_result').remove();
		if (ps_version >= '1.7') {
			$(this).parents('div.form-group').removeClass('has-error');
		} else if (ps_version >= '1.6') {
			$(this).parents('p').removeClass('form-error');
			$(this).parents('p').removeClass('form-ok');
		} else {
			$(this).removeClass('sponsorship_nok');
		}
	});

	$('form').delegate('#customer-form button[type=submit], #submitAccount', 'click', function(e){
		$('input[name=sponsorship]').trigger('blur');
	});

	$('form').delegate('input[name=sponsorship]', 'blur', function(e){
		$('#sponsorship_result').remove();
		if (jQuery.trim($(this).val()) != '') {
			$.ajax({
				type	: "POST",
				cache	: false,
				async   : false,
				url		: url_allinone_sponsorship,
				dataType: 'json',
				data 	: "popup=1&checksponsor=1&sponsorship="+$(this).val()+"&customer_email="+$(this).parents('form').find('input[name=email]').val(),
				success: function(obj)	{
					if (obj && obj.result == 1) {
						if (ps_version < '1.7')
							$('input[name=sponsorship]').after('&nbsp;<img id="sponsorship_result" src="'+img_path+'img/valid.png" align="absmiddle" class="icon" />');
						else
							$('input[name=sponsorship]').parents('p').addClass('form-ok');
						$('#customer-form button[type=submit], #submitAccount').removeClass('disabled').prop('disabled', false);
					} else {
						if (ps_version >= '1.7') {
							$('input[name=sponsorship]').parents('div.form-group').addClass('has-error');
							$('input[name=sponsorship]').after('<div id="sponsorship_result" class="help-block"><ul><li>'+error_sponsor+'</li></ul></div>');
						} else if (ps_version >= '1.6') {
							$('input[name=sponsorship]').parents('p').removeClass('form-ok');
							$('input[name=sponsorship]').parents('p').addClass('form-error');
						} else {
							$('input[name=sponsorship]').addClass('sponsorship_nok');
							$('input[name=sponsorship]').after('&nbsp;<img id="sponsorship_result" src="'+img_path+'img/invalid.png" title="'+error_sponsor+'" align="absmiddle" class="icon" />');
						}
						$('#customer-form button[type=submit], #submitAccount').addClass('disabled').prop('disabled', true);
						e.preventDefault();
						e.stopPropagation();
					}
				}
			});
		} else
			$('#customer-form button[type=submit], #submitAccount').removeClass('disabled').prop('disabled', false);
	});
}