{* 
* @Module Name: Leo Bootstrap Menu
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  Leotheme
*}

{if $successful == 1}
	<div class="bootstrap">
		<div class="alert alert-success megamenu-alert">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Successfully' mod='leobootstrapmenu'}
		</div>
	</div>
{/if}
<form id="leoMenuItem" class="defaultForm form-horizontal" action="{$addnew}" method="post" enctype="multipart/form-data" novalidate="">
	<input type="hidden" name="quicksaveleobootstrapmenu" value="1">
	<input type="hidden" name="quicksavemenu" value="">
	<input type="hidden" name="id_group" value="{$id_group}">
	<input type="hidden" name="id_parent" value="0">
	<input type="hidden" name="show_title" value="1">
	<input type="hidden" name="sub_with" value="none">
	<input type="hidden" name="target" value="_self">
	<input type="hidden" name="is_group" value="0">
	<input type="hidden" name="colums" value="2">
	<input type="hidden" name="active" value="1">
	<select multiple="multiple" id="availableItems" style="width: 350px; height: 200px;margin: 0 auto;">
		<optgroup label="{l s='CMS' mod='leobootstrapmenu'}">
			{foreach from=$cmss item=cms}
				<option value="cms_{$cms['id_cms']}">{$cms['meta_title']}</option>
			{/foreach}
		</optgroup>
		<optgroup label="{l s='CMS Category' mod='leobootstrapmenu'}">
			{foreach from=$cmsCategory item=cms}
				<option value="cms-category_{$cms['id_cms_category']}">{$cms['meta_title']}</option>
			{/foreach}
		</optgroup>
		<optgroup label="{l s='Category' mod='leobootstrapmenu'}">
			{foreach from=$categories item=cat}
				<option value="category_{$cat['id_category']}">{$cat['name']}</option>
			{/foreach}
		</optgroup>
		<optgroup label="{l s='Suppliers' mod='leobootstrapmenu'}">
			{foreach from=$suppliers item=sup}
				<option value="supplier_{$sup['id_supplier']}">{$sup['name']}</option>
			{/foreach}
		</optgroup>
		<optgroup label="{l s='Brands' mod='leobootstrapmenu'}">
			{foreach from=$manufacturers item=man}
				<option value="manufacture_{$man['id_manufacturer']}">{$man['name']}</option>
			{/foreach}
		</optgroup>
		<optgroup label="{l s='Page Controllers' mod='leobootstrapmenu'}">
			{foreach from=$page_controller item=page}
				<option value="controller_{$page['link']}">{$page['name']}</option>
			{/foreach}
		</optgroup>
	</select>
	<button type="submit" value="1" id="leoAddMenuItem" name="quicksaveleobootstrapmenu" class="button btn btn-default" style="margin: 10px auto;display: block;width: 150px;">
		<i class="icon-arrow-down"></i> {l s='Add to menu' mod='leobootstrapmenu'}
	</button>
</form>
<div class="col-lg-12"> 
	<div class="" style="float: right">
		<div class="pull-right">
			<a href="{$live_editor_url}" class="btn btn-danger">{l s='Live Edit Tools' mod='leobootstrapmenu'}</a>
               {l s='To Make Rich Content For Megamenu' mod='leobootstrapmenu'}
		</div>
	</div>
</div>
 
<div class="tab-content row">
	<div class="tab-pane active" id="megamenu">
	
		<div class="col-md-4">
			<div class="panel panel-default">
				<h3 class="panel-title">{l s='Tree Megamenu Management - Group: ' mod='leobootstrapmenu'}{$current_group_title}{l s=' - Type: ' mod='leobootstrapmenu'}{$current_group_type}</h3>
				<div class="panel-content">{l s='To sort orders or update parent-child, you drap and drop expected menu.' mod='leobootstrapmenu'}
					<hr>
					<p>
						<input type="button" value="{l s='New Menu Item' mod='leobootstrapmenu'}" id="addcategory" data-loading-text="{l s='Processing ...' mod='leobootstrapmenu'}" class="btn btn-danger" name="addcategory">
						<a href="{$admin_widget_link}" class="leo-modal-action btn btn-modeal btn-success btn-info">{l s='List Widget' mod='leobootstrapmenu'}</a>
					</p>
					<p>
						<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=leobootstrapmenu&addmenuproductlayout=1&id_group={$id_group}" class="btn btn-modeal btn-success">{l s='Add Menu Item For Product Multi Layout (Only For Developers)' mod='leobootstrapmenu'}</a>
					</p>
					<hr>										
					{$tree}
                                        <a href="javascript:void(0);" class="btn btn-danger delete_many_menus">
                                            <i class="icon-trash"></i>&nbsp;Delete selected
                                        </a>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			{$helper_form}
		</div>
		<script type="text/javascript">
			var addnew ="{$addnew}"; 
			var action="{$action}";
			$("#content").PavMegaMenuList({
				action:action,
				addnew:addnew
			});
		</script>
	</div>
</div>
<script>
	$('#myTab a[href="#profile"]').tab('show');
</script>