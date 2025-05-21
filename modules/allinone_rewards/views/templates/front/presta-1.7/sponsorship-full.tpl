{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{extends file='customer/page.tpl'}

{block name='page_title'}
  	{l s='Sponsorship program' mod='allinone_rewards'}
{/block}

{block name='page_content'}
{if version_compare($smarty.const._PS_VERSION_,'1.7.1.0','<')}
	{include file='file:modules/allinone_rewards/views/templates/front/presta-1.7/sponsorship.tpl'}
{else}
	{include file='module:allinone_rewards/views/templates/front/presta-1.7/sponsorship.tpl'}
{/if}
{/block}