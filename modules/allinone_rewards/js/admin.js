/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

var initPage = false;

jQuery(function($){
	// hide success or error message on each submit
	$('body').on('submit', '.tabs form', function(event){
		$('.module_confirmation, .module_error').hide();
	});

	// non-template form submit
	$('body').on('submit', '.tabs form:not(form.rewards_template)', function(event){
		template = $(this).parents('.tabcontent').find('select.rewards_template');
		if (template.val() != 0)
			$(this).append('<input type="hidden" name="'+template.attr('name')+'" value="'+template.val()+'">');
	});

	// templates change
	$('body').on('change', 'select.rewards_template', function(){
		if ($(this).val() == 0) {
			$(this).parents('form').find('input.optional').hide();
		}
		reloadTemplate($(this));
	});

	// change on reward virtual value
	$('body').on('blur', 'input[name^="rewards_virtual_value"]', function(){
		$('.notvirtual').trigger('blur');
	});

	// click on an input with options
	$('body').on('click', 'input.with_options', function(){
		$('.optional[class*="'+$(this).attr('name')+'"]').hide();
		$('.optional[class*="'+$(this).attr('name')+'_optional_'+$(this).val()+'"]').show();
		$('.optional[class*="'+$(this).attr('name')+'_optional_'+$(this).val()+'"] input[type=radio]:checked').trigger('click');
	});

	$('body').on('change', 'select.with_options', function(){
		console.log($(this).val());
		$('.optional[class*="'+$(this).attr('name')+'"]').hide();
		$('.optional[class*="'+$(this).attr('name')+'_optional_'+$(this).val()+'"]').show();
		$('.optional[class*="'+$(this).attr('name')+'_optional_'+$(this).val()+'"] input[type=radio]:checked').trigger('click');
	});

	// reward transformation option disable gift option
	$('body').on('click', 'input[name="rewards_voucher"]', function(){
		if ($(this).val() == 1) {
			$('#rewards_gift_off').prop('checked', true);
			$('.rewards_gift_optional_1').hide();
		}
	});

	// reward gift product option disable transformation option
	$('body').on('click', 'input[name="rewards_gift"]', function(){
		if ($(this).val() == 1) {
			$('#rewards_voucher_off').prop('checked', true);
			$('.rewards_voucher_optional_1').hide();
		}
	});

	// Reward for registration
	$('body').on('click', '#add_rule', function(){
		addSponsorshipRegistrationRule();
	});

	// MLM option
	$('body').on('click', '#add_level', function(){
		addSponsorshipLevel();
	});

	// PS 1.6
	$('.tabs.general').on('tabsbeforeactivate', function(event, ui){
		if ($(ui.newPanel).parent().hasClass('general')) {
			if (initPage)
				return;
			// hide old message
			$('.module_confirmation, .module_error').hide();
			// remove the non-ajax panel
			$('.tabcontent').remove();
			// empty the current panel to remove the category tree
			if ($(ui.oldPanel).attr('id') != 'tabs-news')
				$(ui.oldPanel).hide().html('');
		}
	});

	// PS 1.5
	// when changing tab, remove all categories tree from others tabs
	$('.tabs.general').on('tabsselect', function(event, ui){
		// if it's a 1st level tab
		if ($(ui.panel).parent().hasClass('general')) {
			// remove style from current non ajax tab when clicking on another one
			$(ui.tab).parents('ul').find('li').each(function(){
				if ($(ui.tab).parent().attr('id') != $(this).attr('id'))
					$(this).removeClass('ui-tabs-selected').removeClass('ui-state-active');
			});
			// hide old message
			$('.module_confirmation, .module_error').hide();
			// remove the non-ajax panel
			$('.tabcontent').remove();
			// empty the current panel to remove the category tree
			$('.tabs.general > .ui-tabs-panel:visible:not(#tabs-news)').addClass('ui-tabs-hide').html('');
		}
	});

	$('.tabs.general').on('tabsbeforeload', function(event, ui){
		if (initPage)
			ui.jqXHR.abort();
	});

	$('.tabs.general').on('tabsload', function(event, ui){
		initForm();
	});

	initForm(true);
});

function initForm(firstInit){
	$('input.with_options:checked').trigger('click');
	$('select.with_options').trigger('change');
	$('.notvirtual').trigger('blur');
	$('input[name^="discount_type_gc"]:checked').trigger('click');

	$('select.rewards_template').each(function(i){
		if ($(this).val() != 0) {
			$(this).parents('.tabcontent').find('.not_templated').hide();
		}
	});

	tinySetup({
		editor_selector :"autoload_rte"
	});
	displayFlags(languages, id_language, false);

	$(".multiselect").multiselect({
		height: "auto",
		checkAllText: checkAllText,
		uncheckAllText: uncheckAllText,
		selectedText: selectedText,
		noneSelectedText: noneSelectedText
	});

	$('.tabs').tabs();

	if (firstInit) {
		// s'il y a un sous onglet, on le sélectionne
		if (current_subtab != '')
			version == '1.5' ? $('.tabs').tabs('select', current_subtab) : $("li a[href='#"+current_subtab+"']").trigger('click');

		// sur presta 1.6 il faut sélectionner l'onglet actif (ne fonctionne pas juste avec les CSS modifiés comme en 1.5)
		// et l'empêcher de se charger en ajax pour ne pas perdre le contenu de la variable POST.
		// sinon l'onglet News n'est plus accessible car il est considéré comme l'onglet actif
		// Pour presta 1.5 on change les styles et ça suffit à activer l'onglet
		if (current_tab != '') {
			if (version == '1.6') {
				initPage = true;
				$('#a-'+current_tab).trigger('click');
				initPage = false;
			} else  {
				$('#tabs-news').addClass('ui-tabs-hide');
				$('#li-news').removeClass('ui-tabs-selected').removeClass('ui-state-active');
				$('#li-' + current_tab).addClass('ui-tabs-selected').addClass('ui-state-active');
			}
		}
	}
	$('.tabs').show();
}

function addSponsorshipRegistrationRule() {
	var rules = $('table.reward_for_registration tbody tr');
	var nb = rules.size();
	var newRule = $(rules[nb-1]).clone(true);
	newRule.find('span.numrule').html(nb + 1);
	newRule.find('input').val('');
	$(rules[nb-1]).after(newRule);
	return false;
}

function delSponsorshipRegistrationRule(obj) {
	var nb = $('table.reward_for_registration tbody tr').size();
	if (nb > 1) {
		$(obj).parents('table.reward_for_registration tbody tr').remove();
		var cpt = 1;
		// on réaffecte des ID séquentiels aux levels
		$('table.reward_for_registration tbody tr').each(function(i){
			$(this).find('span.numrule').html(cpt);
			cpt++;
		});
	}
	return false;
}

function addSponsorshipLevel() {
	var levels = $('div.level_information');
	var nb = levels.size();
	var newLevel = $(levels[nb-1]).clone(true);
	newLevel.find('span.numlevel').html(nb + 1);
	var reg=new RegExp('\\\['+(nb-1)+'\\\]"', "g");
	newLevel.html(newLevel.html().replace(reg,'['+nb+']"'));
	reg=new RegExp('\\\['+(nb-1)+'\\\]_', "g");
	newLevel.html(newLevel.html().replace(reg,'['+nb+']_'));
	$(levels[nb-1]).after(newLevel);
	$('#unlimited_level').html(nb + 1);
	// hack pour cocher le type sur la nouvelle ligne à l'identique, sinon sur FF ca bug
	var selectedValue = $(levels[nb-1]).find('input[name^="reward_type_s"]:checked').val();
	newLevel.find('input[name^="reward_type_s"][value="'+selectedValue+'"]').trigger('click');
	return false;
}

function delSponsorshipLevel(obj) {
	var nb = $('div.level_information').size();
	if (nb > 1) {
		$(obj).parents('div.level_information').remove();
		var cpt = 1;
		// on réaffecte des ID séquentiels aux levels
		$("div.level_information").each(function(i){
			$(this).find('span.numlevel').html(cpt);
			cpt++;
		});
		$('#unlimited_level').html(nb - 1);
	}
	return false;
}

function showDetails(id_sponsor, url) {
	$('.statistics .details').remove();
	$.ajax({
		type	: "POST",
		cache	: false,
		url		: url + '&stats=1&id_sponsor=' + id_sponsor,
		dataType: "html",
		success : function(data) {
			$('#line_' + id_sponsor).after(data);
		}
	});
}

function convertCurrencyValue(obj, fromField, rate) {
	$fromField = $('input[name^='+fromField+'].currency_default');
	if ($fromField.size() > 1) {
		$fromField = $(obj).parents('.level_information').find('input[name^='+fromField+'].currency_default');
	}
	value = $fromField.val();
	fieldTo = $(obj).parent().find('input');
	fieldTo.val((value * rate).toFixed(4));
	fieldTo.trigger('blur');
	return false;
}

function showVirtualValue(obj, id_currency, suffix) {
	var currency_virtual_value = virtual_value[id_currency];
	if ($('input[name="rewards_virtual_value['+id_currency+']"]').length > 0)
		currency_virtual_value = $('input[name="rewards_virtual_value['+id_currency+']"]').val();

	if ($(obj).hasClass('product_per_product')) {
		var inputField = $(obj).parent().find('input').val();
		var selectField = $(obj).parent().find('select').val();
		if (selectField == 1 && !isNaN(inputField))
			$(obj).parent().find('.virtualvalue').html('('+(inputField * currency_virtual_value).toFixed(2)+' '+virtual_name+')').show();
		else
			$(obj).parent().find('.virtualvalue').html('').hide();
	} else if (!isNaN($(obj).val()))
		$(obj).parent().find('.virtualvalue').html('('+($(obj).val() * currency_virtual_value).toFixed(2)+' '+virtual_name+')');
}


function reloadTemplate(obj) {
	obj.parents('form').find('input[name=rewards_template_action]').val('');
	obj.parents('form').submit();
}

function promptTemplate(obj, action, label, value, title) {
	jPrompt(label, value, title, function(r) {
	    if (r) {
	    	obj.parents('form').find('input[name=rewards_template_action]').val(action);
	    	obj.parents('form').find('input[name=rewards_template_name]').val(r);
	    	obj.parents('form').submit();
	    }
	});
}

function deleteTemplate(obj, label, title) {
	jConfirm(label, title, function(r) {
	    if (r) {
	    	obj.parents('form').find('input[name=rewards_template_action]').val('delete');
	    	obj.parents('form').submit();
	    }
	});
}

function initTemplate(version1_6) {
	$(function() {
		initTableSorterTemplate();
		initAutocomplete(version1_6);
	});
}

function addTemplateGroups(label, title) {
	jConfirm(label, title, function(r) {
	    if (r) {
			$('#rewards_template_customers').parents('form').find('input[name=rewards_template_action]').val('add_groups');
			$.ajax({
				type	: 'POST',
				async	: false,
				cache	: false,
				url		: $('#rewards_template_customers').parents('form').attr('action'),
				dataType: 'json',
				data 	: $('#rewards_template_customers').parents('form').serialize()+'&ajax=1'
			});
		}
	});
}

function addTemplateCustomer(customer) {
	$('#new_customer').parents('form').find('input[name=rewards_template_action]').val('add_customer');
	$.ajax({
		type	: 'POST',
		async	: false,
		cache	: false,
		url		: $('#new_customer').parents('form').attr('action'),
		dataType: 'json',
		data 	: $('#new_customer').parents('form').serialize()+'&ajax=1&id_customer='+customer.id_customer,
		success : function(data) {
			var row = '<tr id="'+customer.id_customer+'"><td class="id">'+customer.id_customer+'</td><td>'+customer.firstname+'</td><td>'+customer.lastname+'</td><td>'+customer.email+'</td><td><img src="../img/admin/delete.gif" class="delete"></td></tr>';
			$row = $(row);
			$('.tablesorter').find('tbody').append($row)
			$('.tablesorter').trigger('addRows', [$row]);
		}
	});
}

function addTemplateCustomersFromGroup(label, title) {
	if ($('.add_from_group').val() != 0) {
		jConfirm(label, title, function(r) {
		    if (r) {
				$('.add_from_group').parents('form').find('input[name=rewards_template_action]').val('add_customers_from_group');
				$.ajax({
					type	: 'POST',
					async	: false,
					cache	: false,
					url		: $('.add_from_group').parents('form').attr('action'),
					dataType: 'json',
					data 	: $('.add_from_group').parents('form').serialize()+'&ajax=1',
					success : function(data) {
						var rows = new Array();
						for(i=0; i < data.length; i++) {
							$row = $('<tr id="'+data[i].id_customer+'"><td class="id">'+data[i].id_customer+'</td><td>'+data[i].firstname+'</td><td>'+data[i].lastname+'</td><td>'+data[i].email+'</td><td><img src="../img/admin/delete.gif" class="delete"></td></tr>');
							$('.tablesorter').find('tbody').append($row);
							$('.tablesorter').trigger('addRows', [$row]);
						}

					}
				});
			}
		});
	}
}

function delTemplateCustomer(obj) {
	$('#new_customer').parents('form').find('input[name=rewards_template_action]').val('delete_customer');
	$.ajax({
		type	: 'POST',
		async	: false,
		cache	: false,
		url		: $('#new_customer').parents('form').attr('action'),
		dataType: 'json',
		data 	: $('#new_customer').parents('form').serialize()+'&ajax=1&id_customer='+$(obj).closest('tr').attr('id'),
		success : function(data) {
			$(obj).closest('tr').remove();
			$('.tablesorter').trigger('update');
		}
	});
  	return false;
}

function deleteAllModuleData(obj, label, title) {
	jConfirm(label, title, function(r) {
	    if (r) {
	    	var action = obj.parents('form').attr('action')+'&submitDeleteData';
	    	obj.parents('form').attr('action', action);
	    	obj.parents('form').submit();
	    }
	});
}

/* tablesorter */
function initTableSorter() {
	$('.tablesorter').each(function() {
		$(this).tablesorter({
			theme: 'ice',
			widthFixed: true,
			sortList: [[0,0]],
			widgets: ['filter'],
		}).tablesorterPager({
			container: $(this).next('.pager'),
			output: footer_pager,
			page: 0,
			size: 10,
			removeRows: false,
			savePages: false
		});
	});
}

/* tablesorter */
function initTableSorterTemplate() {
	initTableSorter();

	$('.tablesorter').delegate('img.delete', 'click', function(){
    	return delTemplateCustomer($(this));
    });

    $('body').delegate('#view_template_customers', 'click', function(){
    	$('#rewards_template_customers').toggle();
    });
}

function initAutocomplete(version1_6) {
	if (version1_6) {
		$('#new_customer').autocomplete(
			$('#new_customer').parents('form').attr('action'),
			{
				cacheLength: 0,
				minChars: 2,
				width: 570,
				selectFirst: false,
				scroll: true,
				scrollHeight: 160,
				dataType: 'json',
				formatItem: function(item, i, max, value, term) {
					if ($('.autocomplete_header').length == 0)
						$('.ac_results').prepend('<div class="autocomplete_header"><span class="autocomplete_id_customer">'+idText+'</span><span class="autocomplete_firstname">'+firstnameText+'</span><span class="autocomplete_lastname">'+lastnameText+'</span><span class="autocomplete_email">'+emailText+'</span></div>');
					return '<a><span class="autocomplete_id_customer">'+item.id_customer+'</span><span class="autocomplete_firstname">'+item.firstname+'</span><span class="autocomplete_lastname">'+item.lastname+'</span><span class="autocomplete_email">'+item.email+'</span></a>';
				},
				parse: function(data) {
					var mytab = new Array();
					for (var i = 0; i < data.length; i++)
						mytab[mytab.length] = { data: data[i], value: data[i].id_customer };
					return mytab;
				},
				extraParams: {
					ajax: 1,
					rewards_template_action: 'list_customer',
					id_template: $('#new_customer').parents('form').find('select').val(),
					plugin: $('#new_customer').parents('form').find('input[name=plugin]').val()
				}
			}
		)
		.result(function(event, data, formatted) {
			addTemplateCustomer(data);
		});
	} else {
		$('#new_customer').autocomplete({
			source: function(request, response) {
					$.getJSON($('#new_customer').parents('form').attr('action')+'&ajax=1&rewards_template_action=list_customer&id_template='+$('#new_customer').parents('form').find('select').val()+'&plugin='+$('#new_customer').parents('form').find('input[name=plugin]').val(), request, function(data, status, xhr) {
					response($.map(data, function(item) {
						return {
							label: '<span class="autocomplete_id_customer">'+item.id_customer+'</span><span class="autocomplete_firstname">'+item.firstname+'</span><span class="autocomplete_lastname">'+item.lastname+'</span><span class="autocomplete_email">'+item.email+'</span>',
							value: '',
							obj: item
						}
					}));
				});
			},
			minLength: 2,
			html: true,
			select: function(event, ui) {
				addTemplateCustomer(ui.item.obj);
			}
		});

		/*
		* jQuery UI Autocomplete HTML Extension
		*
		* Copyright 2010, Scott González (http://scottgonzalez.com)
		* Dual licensed under the MIT or GPL Version 2 licenses.
		*
		* http://github.com/scottgonzalez/jquery-ui-extensions
		*/
		var proto = $.ui.autocomplete.prototype,
		initSource = proto._initSource;

		function filter( array, term ) {
	    	var matcher = new RegExp( $.ui.autocomplete.escapeRegex(term), "i" );
	      	return $.grep( array, function(value) {
	        	return matcher.test( $( "<div>" ).html( value.label || value.value || value ).text() );
	        });
		}

		$.extend( proto, {
	    	_initSource: function() {
				if ( this.options.html && $.isArray(this.options.source) ) {
					this.source = function( request, response ) {
						response( filter( this.options.source, request.term ) );
					};
				} else {
					initSource.call( this );
				}
			},
	        _renderItem: function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( $( "<a></a>" )[ this.options.html ? "html" : "text" ]( item.label ) )
					.appendTo( ul );
			},
			_renderMenu: function( ul, items ) {
	            var self = this;
	            ul.prepend('<div class="autocomplete_header"><span class="autocomplete_id_customer">'+idText+'</span><span class="autocomplete_firstname">'+firstnameText+'</span><span class="autocomplete_lastname">'+lastnameText+'</span><span class="autocomplete_email">'+emailText+'</span></div>');
	            $.each( items, function( index, item ) {
	                self._renderItem( ul, item );
	            });
	        }
		});
	}
}