<!--{*
 * Infinite scroll premium
 *
 * @author    Studio Kiwik
 * @copyright Studio Kiwik 2014-2017
 * @license   http://licences.studio-kiwik.fr/infinitescroll
 *}-->

<script type="text/javascript">

	if (typeof kiwik === "undefined"){
		kiwik = {};
	}	

	if(typeof kiwik.infinitescroll === "undefined"){
		kiwik.infinitescroll = {};
	}

    kiwik.infinitescroll.CENTRAL_SELECTOR = "{$infinitescroll_central_selector|replace:'"':"'" nofilter}";
    kiwik.infinitescroll.LIST_SELECTOR = "{$infinitescroll_selector|replace:'"':"'" nofilter}";
	kiwik.infinitescroll.DEFAULT_PAGE_PARAMETER = "{$infinitescroll_default_page_parameter|escape:'quotes':'UTF-8'}";
	kiwik.infinitescroll.HIDE_BUTTON = {$infinitescroll_button|intval};
	kiwik.infinitescroll.BORDER_BUTTON = "{$infinitescroll_border|escape:'htmlall':'UTF-8'}";
	kiwik.infinitescroll.BACKGROUND_BUTTON = "{$infinitescroll_background|escape:'htmlall':'UTF-8'}";
	kiwik.infinitescroll.POLICE_BUTTON = "{$infinitescroll_police|escape:'htmlall':'UTF-8'}";
    kiwik.infinitescroll.ITEM_SELECTOR = "{$infinitescroll_item_selector|replace:'"':"'" nofilter}";
    kiwik.infinitescroll.PAGINATION_SELECTOR = "{$infinitescroll_pagination_selector|replace:'"':"'" nofilter}";
	kiwik.infinitescroll.LOADER_IMAGE = "{$infinitescroll_image|escape:'quotes':'UTF-8'}";
	kiwik.infinitescroll.LABEL_BOTTOM = "{$infinitescroll_text_label_bottom_page|escape:'html':'UTF-8'}";
	kiwik.infinitescroll.LABEL_TOTOP = "{$infinitescroll_text_label_totop|escape:'html':'UTF-8'}";
	kiwik.infinitescroll.LABEL_ERROR = "{$infinitescroll_text_error|escape:'html':'UTF-8'}";
	kiwik.infinitescroll.LABEL_LOADMORE = "{$infinitescroll_text_loadmore|escape:'html':'UTF-8'}";
	kiwik.infinitescroll.VERSION = "{$infinitescroll_version|escape:'htmlall':'UTF-8'}";
	kiwik.infinitescroll.IS_BLOCKLAYERED_INSTALLED = {$is_blocklayered_loaded|intval};
	kiwik.infinitescroll.STOP_BOTTOM = {$infinitescroll_stop_bottom|intval};
	kiwik.infinitescroll.STOP_BOTTOM_PAGE = {$infinitescroll_stop_bottom_page|intval};
	kiwik.infinitescroll.STOP_BOTTOM_FREQ = {$infinitescroll_stop_bottom_freq|intval};
	kiwik.infinitescroll.SANDBOX_MODE = {$debug_mode|intval};
	kiwik.infinitescroll.EXTRA_DEBUG = false;//petite option pour afficher le numéro de la page en H1 au dessus du produit quand on l'affiche
	kiwik.infinitescroll.CURRENT_PAGE = {$current_page|intval};
	kiwik.infinitescroll.INSTANT_SEARCH_LOADED = {$instant_search|intval};
	kiwik.infinitescroll.acceptedToLoadMoreProductsToBottom = 0; //default value, used in case you want the "stop bottom" feature
	kiwik.infinitescroll.SHOP_BASE_URI = "{$shop_base_uri|escape:'htmlall':'UTF-8'}";

	//quick tip to avoid multiple test in the javascript
	if (kiwik.infinitescroll.STOP_BOTTOM_FREQ === 0) {
		kiwik.infinitescroll.STOP_BOTTOM_FREQ = 999999;
	}

	{if Tools::version_compare($ps_version, '1.7', '<')}
		{include file='./parts/javascript_1.6.tpl'}
	{else}
		{include file='./parts/javascript_1.7.tpl'}
	{/if}

</script>