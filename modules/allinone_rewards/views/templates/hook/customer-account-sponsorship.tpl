{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<!-- MODULE allinone_rewards -->
{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
<li><a href="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" title="{l s='Sponsorship program' mod='allinone_rewards'}"><i class="icon-group"></i><span>{l s='Sponsorship program' mod='allinone_rewards'}</span></a></li>
{else}
<li><a href="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" title="{l s='Sponsorship program' mod='allinone_rewards'}"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/sponsorship.gif" alt="{l s='Sponsorship program' mod='allinone_rewards'}" class="icon" /></a> <a href="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" title="{l s='Sponsorship program' mod='allinone_rewards'}">{l s='Sponsorship program' mod='allinone_rewards'}</a></li>
{/if}
<!-- END : MODULE allinone_rewards -->