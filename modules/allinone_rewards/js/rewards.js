/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

var aior_loading = false;

jQuery(function($){
	$('input[name=value-to-transform]').keydown(function(){
		$(this).val(jQuery.trim($(this).val().replace(/,/g, '\.')));
		$(this).val(jQuery.trim($(this).val().replace(/[^0-9\.]/g, '')));
	});

	$('input[name=value-to-transform]').keyup(function(){
		$(this).val(jQuery.trim($(this).val().replace(/,/g, '\.')));
		$(this).val(jQuery.trim($(this).val().replace(/[^0-9\.]/g, '')));
	});

	$('#transform_button').click(function(event){
		event.preventDefault();
		if (!aior_loading && ($('input[name=value-to-transform]').length==0 || jQuery.trim($('input[name=value-to-transform]').val())!='')) {
			aior_loading = true;
			fancyConfirm(function(ret) {
	    		if (ret === true)
					$('#transform_form').submit();
				else
					aior_loading = false;
			});
		}
	});

	$('#payment_button').click(function(event){
		$('#payment_form').toggle();
		event.preventDefault();
	});

	$('input[name=rewards_reminder]').click(function(){
		$('#rewards_options').submit();
	});
});

function fancyConfirm(callback) {
    var ret;
    var message;
    if (!!$.prototype.fancybox) {
		message = 	'<div class="aior_fancyconfirm">' +
	    				'<div class="aior_fancyconfirm_message">'+aior_transform_confirm_message+'</div>' +
	    				'<div style="text-right" class="aior_fancyconfirm_button">' +
	    					'<button id="fancyconfirm_cancel" class="btn btn-default">'+aior_transform_confirm_message2+'</button>' +
	    					'<button id="fancyConfirm_ok" class="btn btn-primary" style="margin-left: 4px;">'+aior_transform_confirm_message3+'</button>' +
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
		ret = confirm(aior_transform_confirm_message);
		callback.call(this, ret);
	}
}

function fancyClose() {
    $.fancybox.close(true);
}