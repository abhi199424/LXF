{*
 * @package Sp One Step Checkout
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2016 YouTech Company. All Rights Reserved.
 * @author MagenTech http://www.magentech.com
 *}

<div class="panel">
	<div class="productTabs" id="spco_tabs">
		<ul class="tab nav nav-tabs">
			{foreach $tabs as $tab}
			<li class="tab-row {if isset($active_tab) && $tab.id==$active_tab}active{/if}">
				<a class="tab-page" id="cart_rule_link_informations" href="#" data-target="#fieldset_{$tab.data_tabs|escape:'htmlall':'UTF-8'}">
				<i class="{$tab.icon|escape:'htmlall':'UTF-8'}"></i>
				{$tab.title|escape:'htmlall':'UTF-8'}</a>
			</li>
			{/foreach}
		</ul>
		
	</div>
	<div class="content" id="spco_content">
	  {$content nofilter}
	</div>
</div>
