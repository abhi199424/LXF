/**
 * @copyright Studio Kiwik 2014
 * @see http://licences.studio-kiwik.fr/infinitescroll
 */

$(function(){

	var ajax_search = true;
	$(".search_query").change(function(){
		if($(this).val().length > 4){
			ajax_search = false;
		} else {
			ajax_search = true;
		}
	});

	//We remove all these classes because it's buggy when pages aren't exactly the modulo of items per line
	function cleanAllFirstLastClasses(elt){
		//@fix 0.7.3 this breaks design when height aren't the same
		//elt.removeClass('first-in-line last-line first-item-of-tablet-line first-item-of-mobile-line last-mobile-line last-item-of-tablet-line last-in-line last-item-of-mobile-line');
		//@fix 0.7.3 so we only remove the "margin/padding" for last line
		elt.removeClass('last-line last-mobile-line');
	}
	
	function appendProductsAndHideLoader(productsToAdd){

		if(productsToAdd.length==0){
			displayReachedBottomLabel();
			return;
		}
		$('.infinitescroll-loader').fadeOut(500, function(){
			$(this).remove();

			productsToAdd.hide();
			cleanAllFirstLastClasses(productsToAdd);

			//We append the result to the existing ones
			var last_item_selector = kiwik.infinitescroll.LIST_SELECTOR.split(',').map(function(a){return a+ ' ' + kiwik.infinitescroll.ITEM_SELECTOR + ':last';}).join(', ');
			$(last_item_selector).after( productsToAdd );
			
			//Handling list and grid view
			if(typeof bindGrid === 'function')
				bindGrid();
			productsToAdd.fadeIn();

			//load js script for callback
			kiwik.infinitescroll.callbackAfterInfiniteScroll();

			refreshOffset();
			isLoading = false;
			lastSentAjax =null;
		});	//end fadeOut callback		
	}

	function displayReachedBottomLabel(){
		var callback = function() {
			if (kiwik.infinitescroll.HIDE_BUTTON != 1 && $('.infinitescroll-bottom-message').length==0)
			{
				$(kiwik.infinitescroll.LIST_SELECTOR).append('<p class="infinitescroll-bottom-message" style=" clear:both; color:'+kiwik.infinitescroll.POLICE_BUTTON+'; background-color:'+kiwik.infinitescroll.BACKGROUND_BUTTON+'; border: 2px solid '+kiwik.infinitescroll.BORDER_BUTTON+' ">'+kiwik.infinitescroll.LABEL_BOTTOM+'. <a href="#" onclick="$(\'html,body\').animate({scrollTop:0}, 300);return false;"  style="color:'+kiwik.infinitescroll.POLICE_BUTTON+';">'+kiwik.infinitescroll.LABEL_TOTOP+' <i class="icon-level-up"></i></a></p>');
			}
		}
		if($('.infinitescroll-loader').length) {
			$('.infinitescroll-loader').fadeOut(function(){
				$(this).remove();
				callback();
			});
		} else {
			callback();
		}
	}

	function displayLoadMoreLabel(){
		var items_selector = kiwik.infinitescroll.LIST_SELECTOR.split(',').map(function(a){return a+ ' ' + kiwik.infinitescroll.ITEM_SELECTOR;}).join(', ');
		var nbProductDisplayed = $(items_selector).length;
		var products_per_page = kiwik.infinitescroll.NB_PRODUCT_PER_PAGE;
		kiwik.infinitescroll.NB_PRODUCT_PER_PAGE
		if(nbProductDisplayed % products_per_page != 0) {
			displayReachedBottomLabel();
			return;
		}
		if($('.infinitescroll-load-more').length == 0){
			$('.infinitescroll-loader').hide();
			$(kiwik.infinitescroll.LIST_SELECTOR).append(
				'<p class="infinitescroll-bottom-message infinitescroll-load-more" style="clear:both; color:'+kiwik.infinitescroll.POLICE_BUTTON+'; background-color:'+kiwik.infinitescroll.BACKGROUND_BUTTON+'; border: 2px solid '+kiwik.infinitescroll.BORDER_BUTTON+' ">'+
				'<a href="#" onclick="$(\'.infinitescroll-load-more\').remove();kiwik.infinitescroll.acceptedToLoadMoreProducts++;$(window).trigger(\'scroll.infinitescroll\');return false;" style="color:'+kiwik.infinitescroll.POLICE_BUTTON+';">'+kiwik.infinitescroll.LABEL_LOADMORE+' <i class="icon-level-down"></i></a></p>'
			);
		}
	}
	
	function displayErrorLabel(){
		$('.infinitescroll-loader').fadeOut(function(){
			$(this).remove();
			$(kiwik.infinitescroll.LIST_SELECTOR).append('<p class="alert alert-warning" style="clear:both">'+kiwik.infinitescroll.LABEL_TOTOP+'.</p>');
		});
	}

	var offset = {top:0, left:0};
	function refreshOffset(){
		var last_item_selector = kiwik.infinitescroll.LIST_SELECTOR.split(',').map(function(a){return a+ ' ' + kiwik.infinitescroll.ITEM_SELECTOR + ':last';}).join(', ');
		offset = $(last_item_selector).offset();
		if(offset==null)
			offset = {top:0, left:0};
	}

	var nextPage = kiwik.infinitescroll.CURRENT_PAGE + 1;
	var isLoading = false;
	var lastSentAjax = null;
	refreshOffset();
	var loader = $('<div class="infinitescroll-loader" style="clear:both;width:100%;margin:20px;text-align:center;"><img src="'+kiwik.infinitescroll.LOADER_IMAGE+'"/></div>');

	//Why in js ? Because google needs to find the other pages !
	$(kiwik.infinitescroll.PAGINATION_SELECTOR).hide();
	var items_selector = kiwik.infinitescroll.LIST_SELECTOR.split(',').map(function(a){return a+ ' ' + kiwik.infinitescroll.ITEM_SELECTOR;}).join(', ');
	cleanAllFirstLastClasses($(items_selector));
	
	var prefetch = (function(){
		
		var UNKNOWN = 0;
		var LOADING = 1;
		var LOADED  = 2;
		
		var _DATA  = {}
		var _STATE = {}
		var _AJAX = {}

		var _hash = function (url, params){
			return url + $.param(params ? params : {});
		}
		
		var _state = function (hash) {
			 if (typeof _STATE[hash] === 'undefined') {
				 return UNKNOWN;
			 } else {
				 return _STATE[hash];
			 } // if
		}
		
		var _launch = function(url, params) {
			var hash = _hash(url, params);
			params['prefetch'] = 1;
			if (_state(hash) ===  UNKNOWN) {
				_STATE[hash] = LOADING;
				_AJAX[hash] = $.get(url, params, function(data){
					_STATE[hash] = LOADED;
					_DATA[hash] = data;
				})
			}
			return hash;
		}
		
		function _promise(hash) {
			 if (typeof _AJAX[hash] === 'undefined') {
				 return null;
			 } else {
				 return _AJAX[hash];
			 }
		}
		
		function _get(hash) {
			 if (typeof _DATA[hash] === 'undefined') {
				 return null;
			 } else {
				 return _DATA[hash];
			 }
		}
		
		return {
				hash : _hash,
				get : _get,
				launch : _launch,
				state : _state,
				promise : _promise,
				LOADED : LOADED,
				LOADING : LOADING,
				UNKNOWN : UNKNOWN
				}
		
	})();

	/**
	*	Scroll Handling
	*/
	$(window).bind('scroll.infinitescroll', function(){

		var update = function(data){

				if(data.substr(data.length-3) === 'nok'){
					displayErrorLabel();
					return;
				}
				if(data.substr(data.length-3) === 'end'){
					displayReachedBottomLabel();
					return;
				}

			//Hiding the loader
			var productsToAdd = $(data).find(kiwik.infinitescroll.ITEM_SELECTOR);

			appendProductsAndHideLoader(productsToAdd);
		};
		
		var prefetch_next = function(params, data){
			var hash = prefetch.hash(kiwik.infinitescroll.AJAX_LINK, params);
			if ((typeof data == 'undefined') || (data && !(data.substr(data.length-3) === 'end' || data.substr(data.length-3)==='nok'))) {
				 var params_prefetch = params;
				 params_prefetch.p = params_prefetch.p + 1;
				 prefetch.launch(kiwik.infinitescroll.AJAX_LINK, params_prefetch);
			 }
		}
		
		var update_prefetch = 	function(hash){
			 var data = prefetch.get(hash);
			 update(data);
			 prefetch_next(params, data);
		}
		
		refreshOffset();
		if( offset.top-$(window).height() <= $(window).scrollTop() && !isLoading && ajax_search){

 			if(kiwik.infinitescroll.STOP_BOTTOM){
 				if(kiwik.infinitescroll.STOP_BOTTOM_PAGE + kiwik.infinitescroll.acceptedToLoadMoreProducts * kiwik.infinitescroll.STOP_BOTTOM_FREQ < nextPage){
 					displayLoadMoreLabel();
 					return;
 				}
 			}

 			isLoading = true;
			$(kiwik.infinitescroll.LIST_SELECTOR).append(loader);
			$('.infinitescroll-loader').slideDown().show();
			
			var params = {"p":nextPage++,"current_id":kiwik.infinitescroll.CURRENT_ID, "scroll_type":kiwik.infinitescroll.SCROLL_TYPE,'orderby':kiwik.infinitescroll.SCROLL_ORDERBY, 'orderway':kiwik.infinitescroll.SCROLL_ORDERWAY}
			var hash = prefetch.hash(kiwik.infinitescroll.AJAX_LINK,params);
			
			if (prefetch.state(hash) != prefetch.UNKNOWN) {
				prefetch.promise(hash).done(function(){update_prefetch(hash)});
			} else {
				lastSentAjax = $.get(kiwik.infinitescroll.AJAX_LINK, params, update);//end ajax load
				prefetch_next(params);
			}

		}//end if should load

	});//end onscroll

	//Support for blocklayered
	if(kiwik.infinitescroll.IS_BLOCKLAYERED_INSTALLED){
		
		if (typeof $.scrollTo === "function"){
			var oldScrollTo = $.scrollTo;
			$.scrollTo = function(){
				return false;
			}
		}

		$(document).ajaxComplete(function(e, jqXHR, ajaxOptions){
			//If the request is one of the blocklayered, then we need to reset the infinite scroll
			if(ajaxOptions.url.search('/modules/blocklayered/blocklayered-ajax.php') != -1 
				&& ajaxOptions.url.search('&infinitescroll=1') == -1){

				$(kiwik.infinitescroll.PAGINATION_SELECTOR).hide();

				var blocklayeredCalledUrl = ajaxOptions.url;
				var successFunction = ajaxOptions.success;

				//Need to check if current page is not 1 ! (page-2 or p=2 depends if f5 or not)
				var reg=new RegExp("^", "g");
				var pageRegexp = /[&?/]{1}(p=|page-)([0-9]+)/g;
				match = pageRegexp.exec(blocklayeredCalledUrl);
				if(match!==null){
					nextPage = parseInt(match[2],10) + 1;
					blocklayeredCalledUrl = blocklayeredCalledUrl.replace(pageRegexp, '');
					if(blocklayeredCalledUrl.search('\\?') === -1)
						blocklayeredCalledUrl += '?';
				}
				else{
					nextPage = 2;
				}

				isLoading=false;
				if(lastSentAjax !== null)
					lastSentAjax.abort();
				lastSentAjax=null;
				refreshOffset();

				//we change the scroll handling in that case
				$(window).unbind('scroll.infinitescroll').bind('scroll.infinitescroll', function(){
					refreshOffset();
					
					if( offset.top-$(window).height() <= $(window).scrollTop() && !isLoading && ajax_search){
			 			
						if(kiwik.infinitescroll.STOP_BOTTOM){
			 				if(kiwik.infinitescroll.STOP_BOTTOM_PAGE + kiwik.infinitescroll.acceptedToLoadMoreProducts * kiwik.infinitescroll.STOP_BOTTOM_FREQ < nextPage){
			 					displayLoadMoreLabel();
			 					return;
			 				}
			 			}

			 			isLoading = true;
						$(kiwik.infinitescroll.LIST_SELECTOR).append(loader);
						$('.infinitescroll-loader').slideDown().show();
			 
						lastSentAjax = $.get(blocklayeredCalledUrl+'&p='+(nextPage++)+'&infinitescroll=1',function(data){	
							data = JSON.parse(data);

			 				if($(data.productList).find(kiwik.infinitescroll.ITEM_SELECTOR).length === 0){
			 					displayReachedBottomLabel();
			 					return;
			 				}

							//Hiding the loader
							$('.infinitescroll-loader').fadeOut(500, function(){
								$(this).remove();
								

								var heightBefore = $(kiwik.infinitescroll.CENTRAL_SELECTOR).height();
								var styleBefore = $(kiwik.infinitescroll.CENTRAL_SELECTOR).attr('style');
								if (typeof styleBefore === 'undefined'){
									styleBefore = "";
								}
								$(kiwik.infinitescroll.CENTRAL_SELECTOR).attr('style',styleBefore+';min-height:'+heightBefore+'px');
								//var productsToAdd = productListResult.find(kiwik.infinitescroll.ITEM_SELECTOR);
								//We add existing products on the front
								var items_selector = kiwik.infinitescroll.LIST_SELECTOR.split(',').map(function(a){return a+ ' ' + kiwik.infinitescroll.ITEM_SELECTOR;}).join(', ');
								var productsBefore = $(items_selector);
								
								successFunction(data);

								cleanAllFirstLastClasses($(items_selector));

								refreshOffset();
								isLoading = false;

								//we prepend the old products and hide the current result to fade it in later
								$(kiwik.infinitescroll.LIST_SELECTOR + ' ' + kiwik.infinitescroll.ITEM_SELECTOR).hide();
								$(kiwik.infinitescroll.LIST_SELECTOR).prepend(productsBefore);
								$(kiwik.infinitescroll.PAGINATION_SELECTOR).hide();
								
								setTimeout(function(){
									$(items_selector).fadeIn()
									//load js script for callback
									kiwik.infinitescroll.callbackAfterInfiniteScroll();
								},500);

								
							});	//end fadeOut callback		

						});//end ajax load

					}//end if should load

				});//end onscroll
			}
		});//end ajaxComplete
	}//end if blocklayered is loaded

	//Support for pmsas4
	if(kiwik.infinitescroll.IS_PMAS4_INSTALLED){

		var ajaxUrlPMAS4 = null;

		var functionToBindForAs4 = function(){
			refreshOffset();

			//update the url since we could be currently fading
			ajaxUrlPMAS4 = document.location.href;
			if(ajaxUrlPMAS4.indexOf('#')===-1)
				return;
			
			if( offset.top-$(window).height() <= $(window).scrollTop() && !isLoading && ajax_search){

				if(kiwik.infinitescroll.STOP_BOTTOM){
	 				if(kiwik.infinitescroll.STOP_BOTTOM_PAGE + kiwik.infinitescroll.acceptedToLoadMoreProducts * kiwik.infinitescroll.STOP_BOTTOM_FREQ < nextPage){
	 					displayLoadMoreLabel();
	 					return;
	 				}
	 			}

				//On transforme l'url comme on veut
				var realAjaxUrl = ASSearchUrl +'?'+ decodeAsParams(ajaxUrlPMAS4.substr(ajaxUrlPMAS4.indexOf('#')+1));
				realAjaxUrl += '&only_products=1&ajaxMode=1';
				//gestion de la page
				realAjaxUrl.replace(/&p=[\d]*/gi, '&p='+nextPage);
				if(realAjaxUrl.search('&p='+nextPage)==-1){
					realAjaxUrl+='&p='+nextPage;
				}
				// ajout des paramètres de filtres sur pm_ad4
				var productFilterListSource='';
				var productFilterListData='';
				if(typeof ASParams != 'undefined'){
					for(var key in ASParams) {
						productFilterListSource = ASParams[key]['as4_productFilterListSource'];
						productFilterListData = ASParams[key]['as4_productFilterListData'];
					}
				}
				
				realAjaxUrl += '&productFilterListSource='+productFilterListSource;
				realAjaxUrl += '&productFilterListData='+productFilterListData;
				realAjaxUrl = encodeURI(realAjaxUrl);
				nextPage++;
				isLoading = true;
				$(kiwik.infinitescroll.LIST_SELECTOR).append(loader);
				$('.infinitescroll-loader').slideDown().show();
	 
				lastSentAjax = $.get(realAjaxUrl, function(data){
					var products = null;
					if(data !== null)
						products = $(data.html_products);//@fix 0.7.6 no need for that, done lower .find(kiwik.infinitescroll.LIST_SELECTOR);


					if(data === null || $(products).find(kiwik.infinitescroll.ITEM_SELECTOR).length === 0){
	 					displayReachedBottomLabel();
	 					return;
	 				}

	 				//Hiding the loader
	 				var productsToAdd = $(products).find(kiwik.infinitescroll.ITEM_SELECTOR);

	 				appendProductsAndHideLoader(productsToAdd);

				}, 'json');
			}
		}//end functionToBindForAs4

		if(typeof setResultsContents === "function"){
			kiwik.infinitescroll.setResultsContents = setResultsContents;
			setResultsContents = function(id_search, htmlResults, context){
				kiwik.infinitescroll.setResultsContents(id_search, htmlResults, context);
				//On met à jour la page et nos infos pour le chargement ajax
				$(kiwik.infinitescroll.PAGINATION_SELECTOR).hide();
				nextPage=2;
				ajaxUrlPMAS4 = document.location.href;
				//reset current ajax if needed
				isLoading=false;
				if(lastSentAjax !== null)
					lastSentAjax.abort();
				lastSentAjax=null;
				refreshOffset();
				$(window).unbind('scroll.infinitescroll').bind('scroll.infinitescroll', functionToBindForAs4);
			}
			//because as4 uses animation and updates stuff only when animations are finisehd... we never know when
			setInterval(function(){$(kiwik.infinitescroll.PAGINATION_SELECTOR).hide();},200);
		} else {
			//Si on a AS4 mais pas "setResultsContents" c'est surement la nouvelle version
			//@FIXME
			//@TODO ajouter la compatibilité avec la nouvelle version de AS4
			$(document).ajaxComplete(function(e, jqXHR, ajaxOptions){
				//si on tape sur advancedsearch4, on désactive tout infinitescroll
				if(ajaxOptions.url.search('advancedsearch4') != -1 
					|| ajaxOptions.url.search('as4c') != -1){
					$(window).unbind('scroll.infinitescroll');
					$(kiwik.infinitescroll.PAGINATION_SELECTOR).show();
					$('.infinitescroll-bottom-message').hide();
					console.warn('Infinitescroll switched off due to newer version of PM_AS4')
				}
			});
		}
		//Si on arrive sur une page déjà chargée avec pm_as4 dans l'url	
		if(document.location.href.search('as4_base_selection:')!=-1){
			ajaxUrlPMAS4 = document.location.href;
			//on regarde si on a déjà une page dans l'url
			if(ajaxUrlPMAS4.search('&p:')!=-1){
				var matches = /&p:([\d]*)/.exec(ajaxUrlPMAS4);
				if(matches!=null){
					nextPage = parseInt(matches[1])+1;
				}
			}
			$(kiwik.infinitescroll.PAGINATION_SELECTOR).hide();

			$(window).unbind('scroll.infinitescroll').bind('scroll.infinitescroll', functionToBindForAs4);
		}
	}//end if pm_as4 is loaded

});/*end ready*/
