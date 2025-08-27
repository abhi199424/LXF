<div class="panel">
    <div class="panel-heading">
        {l s='Module Manager' mod='spfilterproducts'}
        <span class="badge">{$modules|count}</span>
        <span class="panel-heading-action">
            <a id="desc-module-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&addModule=1">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add New Module" data-html="true" data-placement="top">
                    <i class="process-icon-new"></i>
                </span>
            </a>
            <a class="list-toolbar-btn" href="javascript:location.reload();">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Refresh list'}" data-html="true" data-placement="top">
                    <i class="process-icon-refresh"></i>
                </span>
            </a>
        </span>
    </div>
    <style>
        @media (max-width: 992px) {
            .table-responsive-row td:nth-of-type(1):before {
                content: "ID";
            }
            .table-responsive-row td:nth-of-type(2):before {
                content: "Position";
            }
            .table-responsive-row td:nth-of-type(3):before {
                content: "Title";
            }
            .table-responsive-row td:nth-of-type(4):before {
                content: "Hook Into";
            }
            .table-responsive-row td:nth-of-type(5):before {
                content: "Status";
            }
        }
    </style>

    <div class="table-responsive-row clearfix">
        <table id="table-spfilterproducts" class="table spfilterproducts" >
            <thead>
                <tr class="nodrag nodrop">
                    <th class="">
                        <span class="title_box">{l s='ID' mod='spfilterproducts'}</span>
                    </th>

                    <th class="">
                        <span class="title_box">{l s='Title' mod='spfilterproducts'}</span>
                    </th>
                    <th class="">
                        <span class="title_box">{l s='Hook Into' mod='spfilterproducts'}</span>
                    </th>
                    <th class="">
                        <span class="title_box ">{l s='Position' mod='spfilterproducts'}</span>
                    </th>
                    <th class="">
                        <span class="title_box">{l s='Status' mod='spfilterproducts'}</span>
                    </th>
                    <th class="text-right">
                        <span class="title_box">{l s='Action' mod='spfilterproducts'}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                {if !count($modules)}
                <tr>
                    <td class="list-empty" colspan="6">
                        <div class="list-empty-msg">
                            <i class="icon-warning-sign list-empty-icon"></i>
                            {l s='No records found' mod='spfilterproducts'}
                        </div>
                    </td>
                </tr>
                {else}
                {foreach $modules AS $index => $tr}
                    <tr id="item_{$tr.id_spfilterproducts}" class="{if $tr@iteration is odd by 1}odd{/if}">
						<td class="pointer" onclick="document.location ='{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&editModule&id_spfilterproducts={$tr.id_spfilterproducts}'">{$tr.id_spfilterproducts}</td>
						<td class="pointer" onclick="document.location ='{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&editModule&id_spfilterproducts={$tr.id_spfilterproducts}'">{$tr.title_module}
						{if $tr.is_shared}
							<span class="label label-success" >
								{l s='Shared Module' mod='spfilterproducts'}
							</span>
						{/if}
						</td>
						<td  class="pointer" onclick="document.location ='{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&editModule&id_spfilterproducts={$tr.id_spfilterproducts}'">{$tr.hook_name}</td>
						<td class="pointer dragHandle"><div class="dragGroup"><div class="positions">{$tr.position}</div></div></td>
						<td class="pointer" onclick="document.location ='{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&editModule&id_spfilterproducts={$tr.id_spfilterproducts}'">
							<a class="list-action-enable action-enabled" href="{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&statusModule&id_spfilterproducts={$tr.id_spfilterproducts}" title="{l s='Status' mod='spfilterproducts'}">
								{if $tr.active}
									<i class="material-icons action-enabled ">check</i>
								{else}
									<i class="material-icons action-disabled">clear</i>
								{/if}
								
							</a>
						</td>
						<td class="text-right">
						   <div class="btn-group-action">
							  <div class="btn-group pull-right">
								 <a href="{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&editModule&id_spfilterproducts={$tr.id_spfilterproducts}" title="Edit" class="edit btn btn-default">
								 <i class="icon-pencil"></i>&nbsp;{l s='Edit' mod='spfilterproducts'}
								 </a>
								 <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								 <i class="icon-caret-down"></i>&nbsp;
								 </button>
								 <ul class="dropdown-menu">
									<li>
									   <a href="{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&duplicateModule&id_spfilterproducts={$tr.id_spfilterproducts}" title="Duplicate" onclick="return confirm('{l s='Are you sure want duplicate this item?' mod='spfilterproducts'}');">
									   <i class="icon-copy"></i>&nbsp;{l s='Duplicate' mod='spfilterproducts'}
									   </a>
									</li>
									<li class="divider">
									</li>
									<li>
									   <a href="{$link->getAdminLink('AdminModules')}&configure=spfilterproducts&deleteModule&id_spfilterproducts={$tr.id_spfilterproducts}" onclick="return confirm('{l s='Are you sure?' mod='spfilterproducts'}');" title="Delete" class="delete">
									   <i class="icon-trash"></i>&nbsp;{l s='Delete' mod='spfilterproducts'}
									   </a>
									</li>
								 </ul>
							  </div>
						   </div>
						</td>
					</tr>
                {/foreach}

                {/if}
            </tbody>

        </table>
    </div>
</div>