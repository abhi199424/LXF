{*
 * @package SP Custom Html
 * @version 1.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author MagenTech http://www.magentech.com
 *}
{if isset($htmllist) && !empty($htmllist)}
    {foreach from=$htmllist item=item}
        {assign var="moduleclass_sfx" value=( isset( $item.params.moduleclass_sfx ) ) ?  $item.params.moduleclass_sfx : ''}
        {math equation='rand()' assign='rand'}
        {assign var='randid' value="now"|strtotime|cat:$rand}
        {assign var="uniqued" value="sp_customhtml_{$item.id_spcustomhtml}_{$randid}"}
        <div class="moduletable {$uniqued|escape:'html':'UTF-8'}
		{$moduleclass_sfx|escape:'html':'UTF-8'} spcustom_html">
            {if isset($item.params.display_title_module) && $item.params.display_title_module && !empty($item.title_module)}
                <h1 class="module-title h1 {if $hookName == 'displayID1Customhtml8' || $hookName == 'displayID2Customhtml'} hidden-sm-down {/if}">
                    {$item.title_module nofilter}
                </h1>
                
                {if $hookName == 'displayID1Customhtml8' || $hookName == 'displayID2Customhtml'}
                    <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_{$hookName}" data-toggle="collapse">
                    <span class="h3">{$item.title_module nofilter}</span>
                    <span class="float-xs-right">
                      <span class="navbar-toggler collapse-icons">
                        <i class="material-icons add">&#xE313;</i>
                        <i class="material-icons remove">&#xE316;</i>
                      </span>
                    </span>
                  </div>
               {/if}                
            {/if}
            {if isset($item.content) && !empty($item.content)}
            	{if $hookName == 'displayID1Customhtml8' || $hookName == 'displayID2Customhtml'}
                	<div id="footer_sub_menu_{$hookName}" class="collapse">
                		{$item.content nofilter}
                    </div>
                {else}
                	{$item.content nofilter}
                {/if}
                {*$item.content nofilter*}
            {/if}
        </div>
    {/foreach}
{/if}

