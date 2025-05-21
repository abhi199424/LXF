/**
 * @copyright Studio Kiwik 2017
 * @see http://licences.studio-kiwik.fr/infinitescroll
 */

if (typeof kiwik === 'undefined')
	kiwik = {};
if (typeof kiwik.infinitescroll === 'undefined')
	kiwik.infinitescroll = {};

function initializeInfiniteScrollPlugin() {

	//avoid when it's loaded and executed in ajax in 1.5 while already in the page
	if (kiwik.infinitescroll.is_initialised)
		return;

	kiwik.infinitescroll.is_running = true;
	kiwik.infinitescroll.waiting_for_next_page = false;
	kiwik.infinitescroll.waiting_for_previous_page = false;
	kiwik.infinitescroll.current_page_bottom = 1;
	kiwik.infinitescroll.current_page_top = 1;
	kiwik.infinitescroll.page_cache = {};
	kiwik.infinitescroll.override_page_to_call = false;
	kiwik.infinitescroll.override_friendly_url = false;
	//sauvegarder les pages déjà chargées pour les recharger ?

	//UTILITIES
	kiwik.infinitescroll.log = function(msg, options) {
		if (typeof options === 'undefined') {
			options = {};
		}

		if (kiwik.infinitescroll.SANDBOX_MODE) {
			if (options.error)
				console.error(msg);
			else if(options.warn)
				console.warn(msg);
			else
				console.log(msg);
		}
	}
	kiwik.infinitescroll.hidePagination = function() {
		//Why in js ? Because google needs to find the other pages !
		$(kiwik.infinitescroll.PAGINATION_SELECTOR).hide();
	}

	kiwik.infinitescroll.showLoader = function(options) {
		var putBefore = typeof options === 'object' && typeof options['before'] !== 'undefined' ? options['before'] : false;

		//kiwik.infinitescroll.log('kiwik.infinitescroll.showLoader()');
		if ($('.infinitescroll-loader').length == 0) {

			var loader = $('<div class="infinitescroll-loader" style="clear:both;width:100%;margin:20px;text-align:center;display:none;"><img src="'+kiwik.infinitescroll.LOADER_IMAGE+'"/></div>');
			if (!putBefore) {
				$(kiwik.infinitescroll.LIST_SELECTOR).after(loader);
			} else {
				$(kiwik.infinitescroll.LIST_SELECTOR).before(loader);
			}
		}
		$('.infinitescroll-loader').stop(true, true).fadeIn();
	}

	kiwik.infinitescroll.hideLoader = function(options) {
		//kiwik.infinitescroll.log('kiwik.infinitescroll.hideLoader()');
		$('.infinitescroll-loader').remove();/*.stop(true, true).fadeOut(function(){
			$(this).remove();
		});*/
	}

	kiwik.infinitescroll.displayReachedBottomLabel = function(options) {
		kiwik.infinitescroll.log('kiwik.infinitescroll.displayReachedBottomLabel()');
		if (kiwik.infinitescroll.HIDE_BUTTON != 1 && $('.infinitescroll-reached-bottom-message').length==0)
		{
			var products_selector = kiwik.infinitescroll.getProductsSelector();
			$(products_selector).last().after(
				'<div class="infinitescroll-bottom-message infinitescroll-reached-bottom-message" style=" clear:both; color:'+
				kiwik.infinitescroll.POLICE_BUTTON+'; background-color:'+kiwik.infinitescroll.BACKGROUND_BUTTON+
				'; border: 2px solid '+kiwik.infinitescroll.BORDER_BUTTON+' ">'+
				kiwik.infinitescroll.LABEL_BOTTOM+'. <a href="#" onclick="$(\'html,body\').animate({scrollTop:0}, 300);return false;"  style="color:'+kiwik.infinitescroll.POLICE_BUTTON+';">'+
				kiwik.infinitescroll.LABEL_TOTOP+' <i class="icon-level-up"></i></a></div>');
		}
	}
	kiwik.infinitescroll.hideReachedBottomLabel = function(options) {
		$('.infinitescroll-reached-bottom-message').stop(true, true).fadeOut(function(){
			$(this).remove();
		});
	}

	kiwik.infinitescroll.processEnd = function(options) {
		kiwik.infinitescroll.log('kiwik.infinitescroll.processEnd()');
		kiwik.infinitescroll.hideLoader();
		kiwik.infinitescroll.displayReachedBottomLabel();
		//kiwik.infinitescroll.is_running = false;
	}
	kiwik.infinitescroll.processReset = function(options) {
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;;
		kiwik.infinitescroll.log('kiwik.infinitescroll.processReset() page='+page);

		kiwik.infinitescroll.current_page_bottom = page;
		kiwik.infinitescroll.current_page_top = page;

		for(var page in kiwik.infinitescroll.page_cache) {
			if (!kiwik.infinitescroll.page_cache[page].loaded) {
				kiwik.infinitescroll.page_cache[page].ajax.abort();
			}
		}
		kiwik.infinitescroll.page_cache = {};
		kiwik.infinitescroll.waiting_for_next_page = false;
		kiwik.infinitescroll.waiting_for_previous_page = false;
		kiwik.infinitescroll.acceptedToLoadMoreProductsToBottom = 0;
		kiwik.infinitescroll.hideReachedBottomLabel();
		kiwik.infinitescroll.hidePagination();
		kiwik.infinitescroll.hideLoadMoreLabelToBottom();
		kiwik.infinitescroll.hideLoadMoreLabelToTop();
		//prefetch page 1 courante
		kiwik.infinitescroll.getProductsPage({'page': 1});
		kiwik.infinitescroll.alterProductsPageDatas($(kiwik.infinitescroll.getProductsSelector()), 1);
	}


	kiwik.infinitescroll.addProductsToPage = function(options) {
		var $products = typeof options === 'object' && typeof options['$products'] !== 'undefined' ? options['$products'] : $();
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

		kiwik.infinitescroll.log('kiwik.infinitescroll.addProductsToPage()');

		kiwik.antiScroll.disableScroll();
		$(window).unbind('scroll.infinitescroll');
		kiwik.infinitescroll.hideLoader();

		$products = kiwik.infinitescroll.callbackProcessProducts($products);
		//altération des classes/data des produits pour avoir les infos sur la page liée au produit
		kiwik.infinitescroll.alterProductsPageDatas($products, page);

		$products.hide();
		var products_selector = kiwik.infinitescroll.getProductsSelector();
		if (page <= kiwik.infinitescroll.current_page_top) {
			var $firstProduct = $(products_selector).first();
			$firstProduct.before($products);
			var tmp_scrolltop = $(window).scrollTop();
			var height_before = $(kiwik.infinitescroll.LIST_SELECTOR).height();

			$products.css('opacity', 0.3);
			$products.show();

			if ($firstProduct.length == 0) {
				kiwik.infinitescroll.log('kiwik.infinitescroll.addProductsToPage() : could not find "$firstProduct', {warn:1});
				return;
			}

			setTimeout(function(){
				//var target_top = tmp_scrolltop + $(kiwik.infinitescroll.LIST_SELECTOR).height() - height_before;
				var target_top = $firstProduct.offset().top;
				//@kiwik MB 29082018 fix when products are too high and we wanna scroll below 0
				target_top = Math.max(0, target_top - $firstProduct.height() / 2);
				$('body, html').animate({scrollTop: target_top}, 1000, function(){
					$products.animate({'opacity': 1}, 250);
					//$(window).scrollTop(target_top);
					kiwik.infinitescroll.log('kiwik.infinitescroll.addProductsToPage() : Forcing scroll to '+target_top);
					kiwik.infinitescroll.waiting_for_previous_page = false;

					kiwik.infinitescroll.callbackAfterAjaxDisplayed();
					kiwik.antiScroll.enableScroll();
					$(window).bind('scroll.infinitescroll', kiwik.infinitescroll.handleScroll);
				});
			}, 100);
			
		} else {
			var $lastProduct = $(products_selector).last();
			$lastProduct.after($products);

			var total_done = 0;
			$products.fadeIn({
				'duration': 200,
				'complete': function() {
					total_done++;
					if ($products.length == 0 || total_done == $products.length) {
						//quand on va vers le bas, pas besoin de mettre à jour la hauteur du scroll
						kiwik.infinitescroll.waiting_for_next_page = false;
						kiwik.infinitescroll.callbackAfterAjaxDisplayed();
						kiwik.antiScroll.enableScroll();
						$(window).bind('scroll.infinitescroll', kiwik.infinitescroll.handleScroll);
					}
				}
			});
		}

		//Handling list and grid view
		if(typeof bindGrid === 'function')
			bindGrid();


		//kiwik.antiScroll.enableScroll();
	}

	kiwik.infinitescroll.getProductsSelector = function() {
		 return kiwik.infinitescroll.LIST_SELECTOR.split(',').map(function(a){return a+ ' ' + kiwik.infinitescroll.ITEM_SELECTOR;}).join(', ');
	}
	kiwik.infinitescroll.getOffset = function(options){
		var topOffset = typeof options === 'object' && typeof options['top'] !== 'undefined' ? options['top'] : false;

		var products_selector = kiwik.infinitescroll.getProductsSelector();
		if (!topOffset)
			var offset = $(products_selector).last().offset();
		else
			var offset = $(products_selector).first().offset();

		if(offset==null)
			offset = {top:0, left:0};
		return offset;
	}


	kiwik.infinitescroll.displayLoadMoreLabelToTop = function(options) {
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

		kiwik.infinitescroll.log('kiwik.infinitescroll.displayLoadMoreLabelToTop()');

		if (page < 1) {
			return;
		}

		var $button = $('<div>');
		$button.addClass('infinitescroll-bottom-message').addClass('infinitescroll-load-more-top');
		$button.attr('style', 'clear:both; color:'+kiwik.infinitescroll.POLICE_BUTTON+'; background-color:'+kiwik.infinitescroll.BACKGROUND_BUTTON+'; border: 2px solid '+kiwik.infinitescroll.BORDER_BUTTON);

		var $a = $('<a>');
		$a.attr('href', '#');
		$a.attr('style', 'color:'+kiwik.infinitescroll.POLICE_BUTTON);
		$a.html(kiwik.infinitescroll.LABEL_LOADMORE+' <i class="icon-level-up"></i>');
		$button.append($a);

		$(kiwik.infinitescroll.LIST_SELECTOR).before($button);

		$button.find('a').click(function(){
			$(this).parent().remove();
			kiwik.infinitescroll.showLoader({'before':true});

			var $products = kiwik.infinitescroll.getProductsPage({'page': page, 'callback':kiwik.infinitescroll.displayPage});
			//si c'est préchargé on le fait now, sinon on attend hein
			if ($products !== false) {
				kiwik.infinitescroll.displayPage({'page':page, '$products': $products, 'before':true});	
			}
			return false;
		});
	}

	kiwik.infinitescroll.displayLoadMoreLabelToBottom = function(options) {
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

		kiwik.infinitescroll.log('kiwik.infinitescroll.displayLoadMoreLabelToBottom()');

		if (kiwik.infinitescroll.page_cache[page] != undefined && kiwik.infinitescroll.page_cache[page].loaded) {
			var $products = kiwik.infinitescroll.page_cache[page].products
			var page_one_is_loaded_and_identical = kiwik.infinitescroll.isEqualToPageOne({'page':page}) && kiwik.infinitescroll.page_cache[1].loaded == true;
			var page_is_empty = $products.length == 0;
			if (page_one_is_loaded_and_identical || page_is_empty) {
				kiwik.infinitescroll.hideLoadMoreLabelToBottom();
				kiwik.infinitescroll.processEnd();
				return;
			}
		}

		if($('.infinitescroll-load-more-bottom').length == 0){
			$('.infinitescroll-loader').hide();
			$(kiwik.infinitescroll.LIST_SELECTOR).after(
				'<div class="infinitescroll-bottom-message infinitescroll-load-more-bottom" style="clear:both; color:'+kiwik.infinitescroll.POLICE_BUTTON+'; background-color:'+kiwik.infinitescroll.BACKGROUND_BUTTON+'; border: 2px solid '+kiwik.infinitescroll.BORDER_BUTTON+' ">'+
				'<a href="#" onclick="$(\'.infinitescroll-load-more-bottom\').remove();kiwik.infinitescroll.acceptedToLoadMoreProductsToBottom++;$(window).trigger(\'scroll.infinitescroll\');return false;" style="color:'+kiwik.infinitescroll.POLICE_BUTTON+';">'+kiwik.infinitescroll.LABEL_LOADMORE+' <i class="icon-level-down"></i></a></div>'
			);
		}	
	}
	kiwik.infinitescroll.hideLoadMoreLabelToBottom = function(options) {
		kiwik.infinitescroll.log('kiwik.infinitescroll.hideLoadMoreLabelToBottom()');	
		$('.infinitescroll-load-more-bottom').remove();
	}
	kiwik.infinitescroll.hideLoadMoreLabelToTop = function(options) {
		kiwik.infinitescroll.log('kiwik.infinitescroll.hideLoadMoreLabelToTop()');	
		$('.infinitescroll-load-more-top').remove();
	}

	kiwik.infinitescroll.getParamsFromUrl = function(url) {
		var hash;
	    var json_result = {};
	    if (url.indexOf('?') !== -1) {
		    var hashes = url.substr(url.indexOf('?') + 1).split('&');
		    for (var i = 0; i < hashes.length; i++) {
		        hash = hashes[i].split('=');
		        if (hash.length == 2) {
		        	json_result[hash[0]] = decodeURIComponent(hash[1]);
		        }
		    }
		}
	    return json_result;
	}
	//BRAINS
	kiwik.infinitescroll.getUrlToFetch = function(params) {
		var base = document.location.href;
		if (kiwik.infinitescroll.override_page_to_call)
			base = kiwik.infinitescroll.override_page_to_call;

		if (params !== undefined) {
			var full_params = kiwik.infinitescroll.getParamsFromUrl(base);
			if (base.indexOf('?') !== -1)
				base = base.substr(0, base.indexOf('?'));
			for(var name in params) {
				full_params[name] = params[name];
			}
			params = '?' + (Object.keys(full_params).length  > 0 ? decodeURIComponent($.param(full_params)):'');
		} else {
			params = '';
		}

		return base + params;
	}

	kiwik.infinitescroll.updateUrl = function(page) {
		if (page == 0 || isNaN(page)) {
			return;
		}
		
		if (page != kiwik.infinitescroll.CURRENT_PAGE) {
			window.history.pushState(page, false, kiwik.infinitescroll.getFriendlyUrl({'page': page}));
			if (typeof ga !== "undefined") {
				var friendly_url = kiwik.infinitescroll.getFriendlyUrl({'page': page});
				friendly_url = friendly_url.replace(kiwik.infinitescroll.SHOP_BASE_URI, '/');
				ga('set', 'page', friendly_url);
				ga('send', 'pageview');
			}
		}
		kiwik.infinitescroll.CURRENT_PAGE = page;
	}

	kiwik.infinitescroll.displayPage = function(options) {
		var $products = typeof options === 'object' && typeof options['$products'] !== 'undefined' ? options['$products'] : $();
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
		kiwik.infinitescroll.log('kiwik.infinitescroll.displayPage() : page='+page);

		//verif de si ils sont pas déjà affichés, si oui on est au bout ?
		var page_one_is_loaded_and_identical = kiwik.infinitescroll.isEqualToPageOne({'page':page}) && kiwik.infinitescroll.page_cache[1].loaded == true;
		var page_is_empty = $products.length == 0;
		if ((page_one_is_loaded_and_identical || page_is_empty) && page > kiwik.infinitescroll.current_page_bottom) {
			kiwik.infinitescroll.processEnd();
			return;
		}

		if (kiwik.infinitescroll.EXTRA_DEBUG) {
			$products.prepend('<div class="col-xs-12"><h1>PAGE '+page+'</h1></div>');
		}

		kiwik.infinitescroll.addProductsToPage({'$products': $products, 'page': page});

		kiwik.infinitescroll.current_page_bottom = Math.max(kiwik.infinitescroll.current_page_bottom, page);
		kiwik.infinitescroll.current_page_top = Math.min(kiwik.infinitescroll.current_page_top, page);

		kiwik.infinitescroll.updateUrl(page);

		//on balance un petit prefetch cadeau à cet endroit
		kiwik.infinitescroll.prefetchMultipleProductPages({'page': page, 'number': 3});
	}

	kiwik.infinitescroll.getFriendlyUrl = function(options) {
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
		
		if (kiwik.infinitescroll.override_friendly_url) {
			return typeof kiwik.infinitescroll.addPageToFriendlyUrl === "function" ? kiwik.infinitescroll.addPageToFriendlyUrl(kiwik.infinitescroll.override_friendly_url, page) : '';
		}

		params = {};
		params[kiwik.infinitescroll.DEFAULT_PAGE_PARAMETER] = page;
		var result = kiwik.infinitescroll.getUrlToFetch(params);
		result = result.replace(/([?&]+kiwik-ajax[=]?[\d]*)/gi, '');
		//suppression de p=1 si y'a que ça
		if (page == 1) {
			result = result.replace(/&?p=[\d]*/,'').replace(/\?$/, '');
		}

		return result;
	}

	kiwik.infinitescroll.isEqualToPageOne = function(options) {
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;

		//petite exception pour autoriser la vraie page 1 :)
		if (page == 1)
			return false;

		var page_one_products = kiwik.infinitescroll.page_cache[1].products;
		var page_to_check_products = kiwik.infinitescroll.page_cache[page].products;
		if(page_one_products == null || page_to_check_products == null ||page_one_products.length == 0 || page_to_check_products == 0) {
			return true;
		}
		
		if (kiwik.infinitescroll.page_cache[1].products_html_raw == kiwik.infinitescroll.page_cache[page].products_html_raw) {
			return true;
		}

		//sinon on regarde si les liens dedans sont les mêmes ou pas
		var page_one_link = '';
		page_one_products.find('a').each(function(){page_one_link += $(this).attr('href');});
		var page_to_check_links = '';
		page_to_check_products.find('a').each(function(){page_to_check_links += $(this).attr('href');});

		return page_one_link == page_to_check_links;
	}

	kiwik.infinitescroll.getProductsFromAjaxResult = function(result) {
		//suppression des balises script sinon elles sont chargées...
		result = result.replace(/<script(.*?)>(.*?)<\/script>/gi, '');

		var $response = $('<html />').html(result);
		var $products = $response.find(kiwik.infinitescroll.getProductsSelector());
		return $products;
	}

	kiwik.infinitescroll.prefetchMultipleProductPages = function(options) {
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
		var number = typeof options === 'object' && typeof options['number'] !== 'undefined' ? options['number'] : 1;

		if(number > 0) {
			for (var i = 0; i < number; i++) {
				//on bloque le prefetch à 3 au max
				if (kiwik.infinitescroll.page_cache[page +i] == undefined 
					&& page + i <= kiwik.infinitescroll.current_page_bottom + 3
					&& page + i >= kiwik.infinitescroll.current_page_top - 1) {
						kiwik.infinitescroll.getProductsPage({'page': page + i});
				}
			}
		} else if (number < 0) {
			for (var i = 0; i > number; i--) {
				if (page + i > 0) {
					kiwik.infinitescroll.getProductsPage({'page': page + i});
				}
			}
		}
	}

	kiwik.infinitescroll.getProductsPage = function(options) {
		var page = typeof options === 'object' && typeof options['page'] !== 'undefined' ? options['page'] : 1;
		var callback = typeof options === 'object' && typeof options['callback'] !== 'undefined' ? options['callback'] : null;

		if (kiwik.infinitescroll.page_cache[page] !== undefined && kiwik.infinitescroll.page_cache[page].loaded === true) {
			return kiwik.infinitescroll.page_cache[page].products;
		}

		if (kiwik.infinitescroll.page_cache[page] !== undefined && kiwik.infinitescroll.page_cache[page].loaded === false) {
			//si on demande un callback on ecrase le précedent
			if (typeof callback === 'function') {
				kiwik.infinitescroll.log('kiwik.infinitescroll.getProductsPage() : Adding a callback to page ='+page);
				kiwik.infinitescroll.page_cache[page].callback = callback;
			}
			return false;
		}

		if(page != 0) {
			kiwik.infinitescroll.log(
				'kiwik.infinitescroll.getProductsPage() : page='+page+
				(typeof callback == "function" ? ' with a callback : ' + callback.toString().substr(0, callback.toString().indexOf('(')) : ' without callback')
			);
		}

		var params = {'content_only':1, 'infinitescroll':1};
		params[kiwik.infinitescroll.DEFAULT_PAGE_PARAMETER] = page;

		var customxhr = new XMLHttpRequest();
		var calledURL = kiwik.infinitescroll.getUrlToFetch(params);
		var ajax_id = $.ajax({
			url: calledURL,
			type: 'GET',
			cache: true,
			xhr: function() {
				return customxhr;
			},
			success: function(result, status, jqXHR) {
				if (status == 'abort') {
					return;
				}
				//@kiwik 26092019 tentative fix redirection advanced search pages SEO qui redirige quand on demande une page trop grande
				if (customxhr && typeof customxhr.responseURL != 'undefined') {
					//@kiwik 27092019 tentative fix si on demande la page 0 qui redirige sur la page 1, on coupe pas tout
					if (decodeURI(customxhr.responseURL) != calledURL && customxhr.responseURL != calledURL && page > 1) {
						kiwik.infinitescroll.log('kiwik.infinitescroll.getProductsPage() : got a redirection from '+calledURL+' to '+customxhr.responseURL);
						result = '';//on met ça pour que la page se mette bien en "chargée" mais sans produits, pour qu'il s'arrête, sinon ça attend à l'infini
						//on coupe que si on est en bas ?
						if (page == kiwik.infinitescroll.current_page_bottom+1) {
							kiwik.infinitescroll.waiting_for_next_page = true;
							kiwik.infinitescroll.processEnd();
						}
					}
				}

				kiwik.infinitescroll.log('kiwik.infinitescroll.getProductsPage() : ajax result for page '+page);	
				$products = kiwik.infinitescroll.getProductsFromAjaxResult(result);
				//kiwik.infinitescroll.log($products);
				kiwik.infinitescroll.page_cache[page].products = $products;
				kiwik.infinitescroll.page_cache[page].products_html_raw = $products.html();
				kiwik.infinitescroll.page_cache[page].loaded = true;

				if (typeof kiwik.infinitescroll.page_cache[page].callback === 'function')
					kiwik.infinitescroll.page_cache[page].callback({'$products':$products, 'page':page});
				//à ce stade on peut lancer un prefetch sur les next si y'a pas sur plusieurs pages ?
				kiwik.infinitescroll.prefetchMultipleProductPages({'page': page, 'number': 3});
			},
			error: function(result, status, jqXHR) {
				if (status == 'abort') {
					return;
				}
				if (status == 'error') {
					//pour bloquer le chargement par le bas si ça a buggé
					if (page == kiwik.infinitescroll.current_page_bottom+1) {
						kiwik.infinitescroll.waiting_for_next_page = true;
						kiwik.infinitescroll.processEnd();
					}
					return;
				}
			}
		});

		kiwik.infinitescroll.page_cache[page] = {
			'loaded' : false,
			'products' : null,
			'products_html_raw' : '',
			'ajax' : ajax_id,
			'callback' : callback
		};
		
		return false;
	}

	//altération des classes/data des produits pour avoir les infos sur la page liée au produit
	kiwik.infinitescroll.alterProductsPageDatas = function($products, page) {
		kiwik.infinitescroll.log('kiwik.infinitescroll.alterProductsPageDatas() for page='+page);
		if (isNaN(page) || page == 0) {
			return;
		}
		$products.attr('data-page', page);
		$products.addClass('page-'+page);

		$('body').off('mouseenter touchstart', kiwik.infinitescroll.getProductsSelector())
			.on('mouseenter touchstart', kiwik.infinitescroll.getProductsSelector(), function(){
				var page = parseInt($(this).attr('data-page'));
				kiwik.infinitescroll.updateUrl(page);
				//on stocke ça dans le local storage
				kiwik.infinitescroll.saveProductToBeVisited($(this).find('a').first().attr('href'), page, document.location.href);
		});
	}

	//on stocke ça dans le local storage
	kiwik.infinitescroll.saveProductToBeVisited = function(product_link, page, current_url) {
		try {
			localStorage.setItem('is_product_link', product_link);
			localStorage.setItem('is_page', page);
			localStorage.setItem('is_current_url', current_url);
		} catch (e) {
			kiwik.infinitescroll.log('kiwik.infinitescroll.saveProductToBeVisited() : localStorage Failed', {warn: true});
		}
	}

	kiwik.infinitescroll.loadProductVisitedInfos = function() {
		var product_link = null;
		var page = null;
		var current_url = null;
		try {
			product_link = localStorage.getItem('is_product_link');
			page = localStorage.getItem('is_page');
			current_url = localStorage.getItem('is_current_url');
		} catch (e) {
			kiwik.infinitescroll.log('kiwik.infinitescroll.saveProductToBeVisited() : loadProductVisitedInfos Failed', {warn: true});
		}

		var result = {
			product_link: product_link,
			page: page,
			current_url: current_url
		};

		kiwik.infinitescroll.log('kiwik.infinitescroll.loadProductVisitedInfos() : ', result);
		kiwik.infinitescroll.saveProductToBeVisited('', 0, document.location.href);
		return result;
	}


	kiwik.infinitescroll.cache_scrolltop = 0;
	kiwik.infinitescroll.updateCacheScrolltop = function(force_value) {
		kiwik.infinitescroll.cache_scrolltop = force_value !== undefined ? force_value : $(window).scrollTop();
	}
	kiwik.infinitescroll.handleScroll = function() {

		var delta = kiwik.infinitescroll.cache_scrolltop - $(window).scrollTop();
		kiwik.infinitescroll.updateCacheScrolltop();

		if (!kiwik.infinitescroll.is_running)
			return;

		//si on a "bloqué" le scroll, alors on ne lance pas de recherche ni rien
		if (kiwik.antiScroll.active) {
			return;
		}

		kiwik.infinitescroll.hidePagination();

		//tant que la page 1 n'est pas chargée on ne fait rien ? vu que c'est notre condition d'arrêt
		if ((kiwik.infinitescroll.page_cache[1] === undefined || kiwik.infinitescroll.page_cache[1].loaded == false)) {
			return false;
		} 	

		var offsetBottom = kiwik.infinitescroll.getOffset();
		var offsetTop = kiwik.infinitescroll.getOffset({'top':true});
		//si on va vers le bas
		if (delta <= 0 && offsetBottom.top-$(window).height() <= $(window).scrollTop() && !kiwik.infinitescroll.waiting_for_next_page) {
			if(kiwik.infinitescroll.STOP_BOTTOM){
				if(kiwik.infinitescroll.STOP_BOTTOM_PAGE + kiwik.infinitescroll.acceptedToLoadMoreProductsToBottom * kiwik.infinitescroll.STOP_BOTTOM_FREQ < kiwik.infinitescroll.current_page_bottom + 1){
					kiwik.infinitescroll.displayLoadMoreLabelToBottom({'page':kiwik.infinitescroll.current_page_bottom + 1});
					return;
				}
			}

			kiwik.infinitescroll.waiting_for_next_page = true;
			kiwik.infinitescroll.showLoader();

			var $products = kiwik.infinitescroll.getProductsPage({'page': kiwik.infinitescroll.current_page_bottom+1, 'callback':kiwik.infinitescroll.displayPage});
			//si c'est préchargé on le fait now, sinon on attend hein
			if ($products !== false)
				kiwik.infinitescroll.displayPage({'page': kiwik.infinitescroll.current_page_bottom+1, '$products': $products, 'before':false});			
		} 
		//si on va vers le haut
		else if (delta > 0 && $(window).scrollTop() < offsetTop.top && !kiwik.infinitescroll.waiting_for_previous_page && kiwik.infinitescroll.current_page_top > 1) {
			kiwik.infinitescroll.waiting_for_previous_page = true;
			
			kiwik.infinitescroll.displayLoadMoreLabelToTop({'page': kiwik.infinitescroll.current_page_top-1});		
		}
		//sinon on preload vers le bas
		else {
			kiwik.infinitescroll.getProductsPage({'page': kiwik.infinitescroll.current_page_bottom+1});
			kiwik.infinitescroll.getProductsPage({'page': kiwik.infinitescroll.current_page_top-1});
			//si on est sur du scroll classique, on vérifie si on doit update l'url selon le nombre de produits visibles par page
			var scrollTop = $(window).scrollTop();
			var windowHeight = $(window).height();
			var products_per_page = {};
			$(kiwik.infinitescroll.getProductsSelector()).each(function(){
				var product_top =$(this).offset().top;
				var product_bottom =  product_top + $(this).height();

				if ( (product_bottom > scrollTop && product_bottom < scrollTop + windowHeight)
					|| (product_top > scrollTop && product_top < scrollTop + windowHeight)) {
					var page = parseInt($(this).attr('data-page'));
					if (typeof products_per_page[page] === "undefined")
						products_per_page[page] = 0;
					products_per_page[page]++;
				}
			});
			var most_products_per_page = 0;
			var best_page = null;
			for(var page in products_per_page) {
				var nb = products_per_page[page];
				if (nb >= most_products_per_page) {
					most_products_per_page = nb;
					best_page = page;
				}
			}
			if (best_page) {
				//si on va vers le haut, et que la page avec le plus de produit est "plus basse" que l'url
				if (delta > 0 && best_page < kiwik.infinitescroll.CURRENT_PAGE)
					kiwik.infinitescroll.updateUrl(best_page);
				else if (delta < 0 && best_page > kiwik.infinitescroll.CURRENT_PAGE)
					kiwik.infinitescroll.updateUrl(best_page);
			}
		}
	}

	//EVENTS
	$(function() {

		if (kiwik.infinitescroll.INSTANT_SEARCH_LOADED) {
			$(".search_query").change(function(){
				if($(this).val().length > 4){
					kiwik.infinitescroll.processReset();
					kiwik.infinitescroll.is_running = false;
				} else {
					kiwik.infinitescroll.is_running = true;
				}
			});
		}


		kiwik.infinitescroll.hidePagination();
		//on force de la mettre en haut, pour éviter quand on revient en arrière de trigger des scroll par erreur
		//$(window).scrollTop(0);
		kiwik.infinitescroll.updateCacheScrolltop();

		//on met charge la prépage 1 pour les verif du type "check que les produits sont pas la page 1"
		kiwik.infinitescroll.current_page_top = kiwik.infinitescroll.current_page_bottom = kiwik.infinitescroll.CURRENT_PAGE;
		//@kiwik MB fix 28112018 quand on arrive page 7, il faut que notre validation de "charger la suite" soit cohérente
        kiwik.infinitescroll.acceptedToLoadMoreProductsToBottom = Math.ceil( (kiwik.infinitescroll.CURRENT_PAGE - kiwik.infinitescroll.STOP_BOTTOM_PAGE) / kiwik.infinitescroll.STOP_BOTTOM_FREQ);
        
		kiwik.infinitescroll.getProductsPage({'page': 1});
		if (kiwik.infinitescroll.CURRENT_PAGE > 1) {
			kiwik.infinitescroll.getProductsPage({'page': kiwik.infinitescroll.CURRENT_PAGE});

			//on prefetch celle d'avant direct
			kiwik.infinitescroll.waiting_for_previous_page = true;
			kiwik.infinitescroll.displayLoadMoreLabelToTop({'page': kiwik.infinitescroll.current_page_top-1});
			//de plus on bloque le scroll et on le remet bien au début de la page en cours
			$(window).load(function(){
				console.log('kiwik.infinitescroll.initializeInfiniteScrollPlugin : Window is loaded. Now starting the forced scroll to first products');

				var $firstProduct = $(kiwik.infinitescroll.getProductsSelector()).first();
				//si on a un produit qu'on a visité qui est présent sur la page, alors on va vers lui, et pas vers "le premier"
				var product_visited = kiwik.infinitescroll.loadProductVisitedInfos();
				if (product_visited.page == kiwik.infinitescroll.CURRENT_PAGE && document.location.href == product_visited.current_url) {
					$(kiwik.infinitescroll.getProductsSelector()).each(function(){	
						if ($(this).find('a').first().attr('href') == product_visited.product_link) {
							$firstProduct = $(this);
						}
					});
				}

				if ($firstProduct.length == 0) {
					kiwik.infinitescroll.log('kiwik.infinitescroll.addProductsToPage() : could not find "$firstProduct', {warn:1});
					return;
				}

				kiwik.antiScroll.disableScroll();
				var interval_force_scroll = setInterval(function(){
					//var target_top = tmp_scrolltop + $(kiwik.infinitescroll.LIST_SELECTOR).height() - height_before;
					var target_top = $firstProduct.offset().top;
					target_top -= $firstProduct.height() / 2;
					//@kiwik MB 26092018 fix when products are too high and we wanna scroll below 0
					target_top = Math.round(Math.max(0, target_top - $firstProduct.height() / 2));
					$('body, html').stop(true).animate({scrollTop: target_top}, 500, function(){
						//@kiwik MB 26092018 add error tolerance so that floating pixels will work
						var error_margin = 5;
						if (Math.round($(window).scrollTop()) - error_margin <= target_top && Math.round($(window).scrollTop()) + error_margin >= target_top) {
							kiwik.infinitescroll.log('kiwik.infinitescroll ON READY : reached destination');
							clearInterval(interval_force_scroll);
							kiwik.antiScroll.enableScroll();
							return;
						}
						kiwik.infinitescroll.log('kiwik.infinitescroll ON READY : Forcing scroll to first products');
						//$(window).scrollTop(target_top);
					});
				}, 600);
				//altération des classes/data des produits pour avoir les infos sur la page liée au produit
				kiwik.infinitescroll.alterProductsPageDatas($(kiwik.infinitescroll.getProductsSelector()), kiwik.infinitescroll.CURRENT_PAGE);
			});
		} else {
			//altération des classes/data des produits pour avoir les infos sur la page liée au produit
			kiwik.infinitescroll.alterProductsPageDatas($(kiwik.infinitescroll.getProductsSelector()), kiwik.infinitescroll.CURRENT_PAGE);
		}
		
		$(window).bind('scroll.infinitescroll', kiwik.infinitescroll.handleScroll);
		//quand on arrive sur la page on check bien qu'on est pas déjà en bas
		kiwik.infinitescroll.handleScroll();


		//SUPPORT POUR LA NOUVELLE VERSION DE AS4
		$(document).on('as4-After-Set-Results-Contents', function(id_search, context) {
			kiwik.infinitescroll.processReset();
		});
		$(document).on('as4-Search-Reset', function(id_search) {
			kiwik.infinitescroll.processReset();
		});

		//SUPPORT POUR BLOCKLAYERED
		if(kiwik.infinitescroll.IS_BLOCKLAYERED_INSTALLED){

			$(document).ajaxComplete(function(e, jqXHR, ajaxOptions){
				//If the request is one of the blocklayered, then we need to reset the infinite scroll
				if(ajaxOptions.url.search('/modules/blocklayered') != -1 
					&& ajaxOptions.url.search('&infinitescroll=1') == -1){
					kiwik.infinitescroll.log('AJAX call on blocklayered');
					//la page peut être différente de 1 si on fait F5 par exemple
					var matches = /\/page\-([\d]*)/.exec(ajaxOptions.url)
					var page_called = 1;
					if (matches && matches.length) {
						page_called = parseInt(matches[1]);
						if (isNaN(page_called)) {
							page_called = 1;
						}
					}

					//on désactive le scroll si on vient de fetch la page != 1
					if (page_called != 1) {
						//désactivation du scrollto que fait le module depuis peu seulement après une recherche, pour eviter que quand on charge la page 2 il nous fasse scroller...
						if (typeof $.scrollTo === "function"){
							var oldScrollTo = $.scrollTo;
							$.scrollTo = function(){
								return false;
							}
						}
					}


					kiwik.infinitescroll.override_page_to_call = ajaxOptions.url.replace(/\/page\-[\d*]/, '');
					kiwik.infinitescroll.processReset({'page':page_called});

					//override de la fonction de traitement du résultat (vu que là on a du json)
					kiwik.infinitescroll.getProductsFromAjaxResult = function(result) {
						data = JSON.parse(result);
						var base_url = document.location.href;
						//suppression des trucs après le #
						base_url = base_url.substr(0, base_url.indexOf('#'))
						//suppression du p=XXX
						base_url = base_url.replace(/\?p=[\d]*/, '?');
						base_url = base_url.replace(/\&p=[\d]*/, '');
						//on enlève aussi page-XX dans le friendly
						data.current_friendly_url = utf8_decode(data.current_friendly_url.replace(/\/page\-[\d*]/, ''));
						//ici ffriendly url c'est juste après le "#" (inclu)
						kiwik.infinitescroll.override_friendly_url = base_url + data.current_friendly_url;
						kiwik.infinitescroll.log('Overriding override_friendly_url with ' + kiwik.infinitescroll.override_friendly_url);
						var $response = $('<html />').html(utf8_decode(data.productList));
						var $products = $response.find(kiwik.infinitescroll.getProductsSelector());
						return $products;
					};				
					kiwik.infinitescroll.addPageToFriendlyUrl = function(url, page) {
						return url + '/page-'+page;
					};
					//je suis désolé... mais blocklayered est mal fait donc bon
					setInterval(function(){lockLocationChecking = true}, 10);
				}

				//gestion du blocklayered sur la 1.7
				//désactivation et passage par l'event "updateProductList"
				/*if (ajaxOptions.url.search('from-xhr') != -1 ) {
					kiwik.infinitescroll.log('AJAX call on facetedsearch');
					kiwik.infinitescroll.override_page_to_call = decodeURIComponent(ajaxOptions.url.replace('from-xhr', 'kiwik-ajax'));
					kiwik.infinitescroll.processReset();
				}*/
			});//end ajaxComplete

			prestashop.on('updatedProductList', function(data) {
				kiwik.infinitescroll.processReset();
			});

			if (typeof prestashop !== "undefined") {
				//on regarde si on est bien sur un résultat de recherche à facette
				prestashop.on('updateProductList', function(data) {
					if (typeof data === 'object') {
						if (typeof data.current_url !== "undefined" && typeof data.rendered_facets !== "undefined" && typeof data.rendered_products !== "undefined") {
							kiwik.infinitescroll.log('AJAX call on facetedsearch');
							var ajax_url = data.current_url;
							ajax_url += (ajax_url.indexOf('?') == -1 ? '?' : '&') + 'kiwik-ajax';
							//@kiwik MB 20/06/2019 s'il y a un "%26" dans l'url, c'est à dire un "&" encodé,
							// il faut qu'on fasse attention, donc on le remplace par un truc temporaire pour pas qu'il soit transformé en "&" (et donc interprété...)
							var kiwik_et = '----kiwik-et----';
							kiwik.infinitescroll.override_page_to_call = decodeURIComponent(
								ajax_url.replace('%26', kiwik_et)
							).replace(kiwik_et, encodeURIComponent('%26'));//obligé d'ajouter un encode sur le %26 pour le transformer en %2526 car c'est décodé plus loin dans l'appel
								//ajaxOptions.url.replace('from-xhr', 'kiwik-ajax'));
							kiwik.infinitescroll.processReset();
						}
					}
				});
			}

		}//end if blocklayered is loaded
	});
	//end document ready

	//plugin anti scroll
	// left: 37, up: 38, right: 39, down: 40,
	// spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
	kiwik.antiScroll = {};
	kiwik.antiScroll.keys = {37: 1, 38: 1, 39: 1, 40: 1};
	kiwik.antiScroll.active = false;

	kiwik.antiScroll.preventDefault = function(e) {
		e = e || window.event;
		if (e.cancelable) { 
			if (e.preventDefault)
				e.preventDefault();
				e.returnValue = false;  
		}	
	}

	kiwik.antiScroll.preventDefaultForScrollKeys = function(e) {
	    if (kiwik.antiScroll.keys[e.keyCode]) {
	        kiwik.antiScroll.preventDefault(e);
	        return false;
	    }
	}

	kiwik.antiScroll.disableScroll = function() {
		kiwik.antiScroll.active = true;
		kiwik.infinitescroll.log('kiwik.antiScroll.disableScroll', {'warn':true});
	  if (window.addEventListener) // older FF
	      window.addEventListener('DOMMouseScroll', kiwik.antiScroll.preventDefault, false);
	  window.onwheel = kiwik.antiScroll.preventDefault; // modern standard
	  window.onmousewheel = document.onmousewheel = kiwik.antiScroll.preventDefault; // older browsers, IE
	  window.ontouchmove  = kiwik.antiScroll.preventDefault; // mobile
	  document.onkeydown  = kiwik.antiScroll.preventDefaultForScrollKeys;
	}

	kiwik.antiScroll.enableScroll = function() {
		kiwik.antiScroll.active = false;
		kiwik.infinitescroll.log('kiwik.antiScroll.enableScroll', {'warn':true});
	    if (window.removeEventListener)
	        window.removeEventListener('DOMMouseScroll', kiwik.antiScroll.preventDefault, false);
	    window.onmousewheel = document.onmousewheel = null; 
	    window.onwheel = null; 
	    window.ontouchmove = null;  
	    document.onkeydown = null;  
	}

	kiwik.infinitescroll.is_initialised = true;
}

$(function(){
	initializeInfiniteScrollPlugin();
});