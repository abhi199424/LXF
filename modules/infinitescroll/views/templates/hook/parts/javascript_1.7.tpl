/*

<!--{*
 * Infinite scroll premium
 *
 * @author    Studio Kiwik
 * @copyright Studio Kiwik 2014-2017
 * @license   http://licences.studio-kiwik.fr/infinitescroll
 *}-->

*/

kiwik.infinitescroll.callbackAfterAjaxDisplayed = function() {
	kiwik.infinitescroll.log('kiwik.infinitescroll.callbackAfterAjaxDisplayed()');

	$(document).trigger('is-callbackAfterAjaxDisplayed');

	{$js_script_after nofilter}
}

function is_process_callback($products) {
	kiwik.infinitescroll.log('kiwik.infinitescroll.callbackProcessProducts()');
	//can use "$products" :)
	{$js_script_process nofilter}

	return $products;
}

kiwik.infinitescroll.callbackProcessProducts = is_process_callback;
