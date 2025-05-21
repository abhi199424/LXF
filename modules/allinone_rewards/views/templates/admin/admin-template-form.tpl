{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div style="padding-bottom: 10px">
	<form class="rewards_template" action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
	<input type="hidden" name="plugin" value="{$object->name|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="rewards_template_action">
	<input type="hidden" name="rewards_template_name">
	<fieldset>
		<legend>{l s='Choose a template to modify' mod='allinone_rewards'}</legend>
		{l s='By default, all customers are using the same settings. If you need different profiles with different settings, you can create templates and then link the customers you want to those templates. Default settings will be overriden automatically. Be carefull, when you create a new template the default values are displayed but nothing is saved in database at this moment. So please save all the forms at least once if you want to register you own settings, else default settings will continue to be used for the unsaved forms.' mod='allinone_rewards'}<br><br>
		<div style="float: left">
			{l s='You\'re currently working on' mod='allinone_rewards'}
			<select class="rewards_template" name="rewards_{$object->name|escape:'htmlall':'UTF-8'}_template_id">
				<option value="0">{$title|escape:'htmlall':'UTF-8'} : {l s='default template' mod='allinone_rewards'}</option>
{foreach from=$templates item=template}
				<option {if $object->id_template==$template['id_template']}selected{/if} value="{$template['id_template']|intval}">{$template['name']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
			</select>
{if $object->id_template != 0}
			<img src="../img/admin/edit.gif" width="16" height="16" alt="{l s='Rename' mod='allinone_rewards'}" title="{l s='Rename' mod='allinone_rewards'}" onClick="promptTemplate($(this), 'rename', '{l s='Name of the template ?' mod='allinone_rewards' js=1}', '', '{l s='Rename the template' mod='allinone_rewards' js=1}')">
			<img src="../img/admin/delete.gif" width="16" height="16" alt="{l s='Delete' mod='allinone_rewards'}" title="{l s='Delete' mod='allinone_rewards'}" onClick="deleteTemplate($(this), '{l s='Do you really want to delete this template and links with its customers ?' mod='allinone_rewards' js=1}', '{l s='Delete the template' mod='allinone_rewards' js=1}')">
			<img src="../img/admin/duplicate.png" width="16" height="16" alt="{l s='Duplicate' mod='allinone_rewards'}" title="{l s='Duplicate' mod='allinone_rewards'}" onClick="promptTemplate($(this), 'duplicate', '{l s='Name of the new template ?' mod='allinone_rewards' js=1}', '', '{l s='Duplicate the template' mod='allinone_rewards' js=1}')">
{/if}
{if $object->name==$plugin && $object->id_template}
			<img id="view_template_customers" width="16" height="16" src="{$module_template_dir|escape:'html':'UTF-8'}img/employee.gif" title="{l s='List of customers using that template' mod='allinone_rewards'}">
{/if}
		</div>
		<input style="float: right" type="button" class="button" value="{l s='Or create a new template' mod='allinone_rewards'}" onClick="promptTemplate($(this), 'create', '{l s='Name of the new template ?' mod='allinone_rewards' js=1}', '', '{l s='Create a new template' mod='allinone_rewards' js=1}')">
{if $object->name==$plugin && $object->id_template}
		<fieldset id="rewards_template_customers" class="clear" style="margin-top: 45px;">
			<legend>{l s='List of customers using that template' mod='allinone_rewards'}</legend>
			<div class="clear">
				<script>
					var text1 = "{l s='Do you really want to update the groups list ?' mod='allinone_rewards' js=1}";
					var text2 = "{l s='List of customers groups' mod='allinone_rewards' js=1}";
				</script>
				<b>{l s='All customers whose default group is checked in that list will automatically use that template' mod='allinone_rewards'}</b> ({l s='checking a group already used in another template will remove it from the other template' mod='allinone_rewards'})<br>
				<select name="add_groups[]" multiple="multiple" class="multiselect">
	{foreach from=$groups item=group}
		{if !in_array($group['id_group'], $groups_off)}

					<option {if is_array($add_groups) && in_array($group['id_group'], $add_groups)}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
		{/if}
	{/foreach}
				</select>
				<input type="button" class="button" value="{l s='Save' mod='allinone_rewards'}" onClick="addTemplateGroups(text1, text2)">
				<br><br><br>
				<b>{l s='List of additional customers using that template' mod='allinone_rewards'}</b>
				<table class="tablesorter tablesorter-ice">
					<thead>
						<th class="id">{l s='ID' mod='allinone_rewards'}</th>
						<th>{l s='Firstname' mod='allinone_rewards'}</th>
						<th>{l s='Lastname' mod='allinone_rewards'}</th>
						<th>{l s='Email' mod='allinone_rewards'}</th>
						<th class="action filter-false sorter-false">&nbsp;</th>
					</thead>
					<tbody>
	{if is_array($customers)}
		{foreach from=$customers item=customer}
						<tr id="{$customer['id_customer']|intval}">
							<td class="id">{$customer['id_customer']|intval}</td>
							<td>{$customer['firstname']|escape:'htmlall':'UTF-8'}</td>
							<td>{$customer['lastname']|escape:'htmlall':'UTF-8'}</td>
							<td>{$customer['email']|escape:'htmlall':'UTF-8'}</td>
							<td><img src="../img/admin/delete.gif" class="delete"></td>
						</tr>
		{/foreach}
	{/if}

					</tbody>
				</table>
				<div class="pager">
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/first.png" class="first"/>
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/prev.png" class="prev"/>
			    	<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/next.png" class="next"/>
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/last.png" class="last"/>
			    	<select class="pagesize">
			      		<option value="10">10</option>
			      		<option value="20">20</option>
			      		<option value="50">50</option>
			      		<option value="100">100</option>
			      		<option value="500">500</option>
			    	</select>
				</div>
				<div class="clear" style="padding-top: 20px">
					<b>{l s='Add new customers to this template' mod='allinone_rewards'}</b><br/>
			 		<label>{l s='Search for a customer' mod='allinone_rewards'}</label><input type="text" size="30" id="new_customer" value="" /> {l s='Search will be applied on id_customer, firstname, lastname, email' mod='allinone_rewards'}<br/>
					<label>{l s='Add from a group' mod='allinone_rewards'}</label>
					<script>
						var text3 = "{l s='Do you really want to add customers from that group ?' mod='allinone_rewards' js=1}";
						var text4 = "{l s='Add from a group' mod='allinone_rewards' js=1}";
					</script>
					<select class="add_from_group" name="add_from_group" onChange="addTemplateCustomersFromGroup(text3, text4)"/>
						<option value="0">{l s='Choose a group' mod='allinone_rewards'}</option>
	{foreach from=$groups item=group}
		{if !in_array($group['id_group'], $groups_off)}
						<option value="{$group['id_group']|intval}">{$group['name']|escape:'htmlall':'UTF-8'}</option>
		{/if}
	{/foreach}
					</select> {l s='All customers from that group already linked to another template will be moved to that one' mod='allinone_rewards'}
				</div>
				<script>
					var idText="{l s='ID' mod='allinone_rewards' js=1}";
					var firstnameText="{l s='Firstname' mod='allinone_rewards' js=1}";
					var lastnameText="{l s='Lastname' mod='allinone_rewards' js=1}";
					var emailText="{l s='Email' mod='allinone_rewards' js=1}";
					var footer_pager = "{l s='{startRow} to {endRow} of {totalRows} rows' mod='allinone_rewards' js=1}";
					initTemplate({if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}true{else}false{/if});
				</script>
			</div>
{/if}
		</fieldset>
	</fieldset>
	</form>
</div>