/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

jQuery(function($){
	if (typeof(url_allinone_sponsorship) != "undefined") {
		if (window.location.href.indexOf('http://')===0) {
			url_allinone_sponsorship = url_allinone_sponsorship.replace('https://','http://');
	    } else {
			url_allinone_sponsorship = url_allinone_sponsorship.replace('http://','https://');
	    }
	}

	$('#sponsorship_link a').fancybox();

	if ($('#sponsorship_popup').size() > 0)
		openPopup();

	if ($('#rewards_sponsorship').length > 0)
		initRewards();
	else if ($('#sponsorship_product').length > 0)
		initShareAndCopy();
});

function openPopup(skeepStep) {
	var scheduled = $('#sponsorship_popup').hasClass('scheduled') ? '1' : '0';
	$.ajax({
		type	: "POST",
		cache	: false,
		url		: url_allinone_sponsorship,
		dataType: "html",
		data 	: "popup=1&scheduled=" + scheduled,
		success : function(data) {
			fancybox(data);
			if (skeepStep) {
				$('#sponsorship_text').hide();
				$('#sponsorship_form').show();
			}
		}
	});
	return false;
}

function initRewards() {
	// utile pour order-confirmation et sponsorship.php
	$('#invite').click(function(){
		$('#sponsorship_text').hide();
		$('#sponsorship_form').show();
		$.fancybox.resize();
	});

	$('#noinvite').click(function(){
		$.fancybox.close();
	});

	$('a.rules, a.mail').fancybox({
		'titleShow' : false,
		onComplete   : function() {
			if ($('textarea', '#list_contacts_form').length > 0 && $('textarea', '#list_contacts_form').val().length > 0)
				$('#mail_message').html($('textarea', '#list_contacts_form').val().replace(/\n/g, "<br />"));
    	}
	});

	$('#list_contacts_form').submit(function() {
		return submitForm($(this));
	});
}

function acceptSponsorshipCGV(form) {
	if (!$('input.cgv:checked', $(form)).length) {
		alert(msg);
		return false;
	}
	return true;
}

function submitForm(form) {
	if ($('#sponsorship_popup').size() > 0) {
		if (acceptSponsorshipCGV($(form))) {
			var scheduled = $('#sponsorship_popup').hasClass('scheduled') ? '1' : '0';
			$.fancybox.showActivity();
			$.ajax({
				type	: "POST",
				cache	: false,
				url		: url_allinone_sponsorship,
				data	: $(form).serialize() + "&popup=1&scheduled=" + scheduled,
				dataType: "html",
				success : function(data) {
					fancybox(data);
				}
			});
		}
		return false;
	} else
		return acceptSponsorshipCGV($(form));
}

function fancybox(data) {
	$.fancybox(
	[
		{
			'content'			: data,
			'enableEscapeButton': false,
			'onComplete': function() {
				initRewards();
			}
		}
	],
	{
		'autoDimensions'	: true,
		'hideOnContentClick': false,
		'hideOnOverlayClick': false,
		'titleShow'			: false,
		'showNavArrows'		: false
	});
}

function checkAll() {
	if ($('#checkall').attr('checked'))
		$('#checkall').parents('table.std').find(':checkbox').attr('checked', true);
	else
		$('#checkall').parents('table.std').find(':checkbox').attr('checked', false);
}

function initShareAndCopy() {
	if (navigator.share) {
	  	// Enable the Web Share API button
	  	const shareLink = $('#link_to_share').html();
	  	$('#sponsorship_share_btn').show();
    	$(document).on("click", "#sponsorship_share_btn", function(){
      		navigator.share({
        		title: $('#sponsorship_product').length > 0 ? document.title : '',
        		url: shareLink,
      		})
     		.then(function(){
     			// if on product page, share button popup
     			if ($('#sponsorship_product').length > 0)
     				$.fancybox.close(true)
     		});
		});
	} else {
	  	$('#sponsorship_copy_btn').show();
		var clipboard = new ClipboardJS('#sponsorship_copy_btn');
		clipboard.on('success', function(e) {
			// if on product page, share button popup
			if ($('#sponsorship_product').length > 0)
				$.fancybox.close(true);
		});
		clipboard.on('error', function(e) {
			// if on product page, share button popup
			if ($('#sponsorship_product').length > 0)
				$.fancybox.close(true);
		});
	}
}