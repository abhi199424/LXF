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
<style>
	tr.inactive td {
		text-decoration: line-through;
	}
	tr.inactive td.nostrike {
		text-decoration: none;
	}
</style>
<div class="col-lg-12" id="admincustomer_sponsorship">
{if version_compare($smarty.const._PS_VERSION_,'1.7.6','<')}
	<div class="panel">
		<div class="panel-heading">{l s='Sponsorship program' mod='allinone_rewards'}</div>
		{if $msg}{$msg nofilter}{/if}
{else}
	<div class="card">
		<h3 class="card-header">{l s='Sponsorship program' mod='allinone_rewards'}</h3>
		{if $msg}{$msg nofilter}{/if}
{/if}
		<div class="{if version_compare($smarty.const._PS_VERSION_,'1.7.6','>=')}card-body{/if}">
			<form id='sponsor' method='post'>
				<input type="hidden" name="action" />
				<input type="hidden" id="id_sponsorship_to_update" name="id_sponsorship_to_update" />
				<span>{l s='Sponsorship code :' mod='allinone_rewards'} {$sponsorship_code|escape:'htmlall':'UTF-8'}</span>
				<span style="padding-left: 40px">{l s='You can customize this code' mod='allinone_rewards'} <input type="text" class="form-control" name="sponsorship_custom_code" value="{$sponsorship_custom_code|escape:'htmlall':'UTF-8'}" size="20" maxlength="20" style="width: auto; display: inline-block"/></span>
				<button class="btn btn-primary" type="submit" name="submitSponsorCustomCode" id="submitSponsorCustomCode">{l s='Save settings' mod='allinone_rewards'}</button>
				&nbsp;{l s='(length between 5 and 20 characters, and only digits or letters)' mod='allinone_rewards'}
				<br/><span>{l s='Sponsorship link :' mod='allinone_rewards'} {$sponsorship_link|escape:'htmlall':'UTF-8'}</span><br/><br/>

				{l s='Template used for "Sponsorship program"' mod='allinone_rewards'}&nbsp;
				<select class="change_template form-control" name="sponsorship_template" style="display: inline; width: auto;">
					<option value='0'>{l s='Default template' mod='allinone_rewards'}</option>
					{foreach from=$sponsorship_templates item=template}
						<option {if $template['id_template']==$sponsorship_template_id}selected{/if} value='{$template['id_template']|intval}'>{$template['name']|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select><br/><br/>
		{if $sponsor}
				{l s='Sponsor' mod='allinone_rewards'} <a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $sponsor->id, 'viewcustomer' => 1])|escape:'html':'UTF-8'}">{$sponsor->firstname|escape:'htmlall':'UTF-8'} {$sponsor->lastname|escape:'htmlall':'UTF-8'}</a><br/>
				{l s='Choose a new sponsor' mod='allinone_rewards'}&nbsp;
		{else}
				{l s='Choose a sponsor' mod='allinone_rewards'}&nbsp;
		{/if}
				<input type="hidden" size="30" name="new_sponsor" id="new_sponsor"/>
				<input type="text" class="form-control" size="30" id="search_sponsor" style="display: inline; width: 150px;"/>
				&nbsp;&nbsp;&nbsp;&nbsp;{l s='Generate the welcome voucher ?' mod='allinone_rewards'}&nbsp;<input type="checkbox" value="1" name="generate_voucher" style="display: inline; width: auto;">&nbsp;
				<select name="generate_currency" class="form-control" style="display: inline !important; width: auto;">
				{foreach from=$currencies item=currency}
					<option {if $default_currency==$currency['id_currency']}selected{/if} value="{$currency['id_currency']|intval}">{$currency['name']|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
				</select>
				&nbsp;<button class="btn btn-primary" name="submitSponsor" id="submitSponsor" type="submit">{l s='Save settings' mod='allinone_rewards'}</button>
				<br/>{l s='(search will be applied on id_customer, firstname, lastname, email)' mod='allinone_rewards'}
				<br/><br/>
		{if $friends|@count}
				<table cellspacing='0' cellpadding='0' class='table'>
					<thead>
						<tr style="background-color: #EEEEEE">
							<th class="borderright text-center" colspan='3'>{l s='Total rewards' mod='allinone_rewards'}</th>
							<th colspan='5' class="text-center">{l s='Sponsored friends (Level 1)' mod='allinone_rewards'}</th>
						</tr>
						<tr style="background-color: #EEEEEE">
							<th class="text-center">{l s='Rewards for orders' mod='allinone_rewards'}</th>
							<th class="text-center">{l s='Rewards for registrations' mod='allinone_rewards'}</th>
							<th class="borderright text-center">{l s='Indirect rewards' mod='allinone_rewards'}</th>
							<th class="text-center">{l s='Pending' mod='allinone_rewards'}</th>
							<th class="text-center">{l s='Registered' mod='allinone_rewards'}</th>
							<th class="text-center">{l s='With orders' mod='allinone_rewards'}</th>
							<th class="text-center">{l s='Orders' mod='allinone_rewards'}</th>
							<th class="text-center">{l s='Total' mod='allinone_rewards'}</th>
						</tr>
					</thead>
					<tr>
						<td align='center'>{displayPrice price=$stats['direct_rewards_orders']}</td>
						<td align='center'>{displayPrice price=$stats['direct_rewards_registrations']}</td>
						<td class="borderright" align='center'>{displayPrice price=$stats['indirect_rewards']}</td>
						<td align='center'>{$stats['nb_pending']|intval}</td>
						<td align='center'>{$stats['nb_registered']|intval}</td>
						<td align='center'>{$stats['nb_buyers']|intval}</td>
						<td align='center'>{$stats['nb_orders']|intval}</td>
						<td align='center'>{displayPrice price=$stats['total_orders']}</td>
					</tr>
				</table>
				<div class='clear' style="margin-top: 20px">&nbsp;</div>
				<table cellspacing='0' cellpadding='0' class='tablesorter tablesorter-ice' id='sponsorships_list'>
					<thead>
						<tr style="background-color: #EEEEEE">
							<th class="filter-select">{l s='Levels' mod='allinone_rewards'}</th>
							<th class="filter-select">{l s='Channels' mod='allinone_rewards'}</th>
							<th>{l s='Name of the friends' mod='allinone_rewards'}</th>
							<th class="filter-select">{l s='Number of orders' mod='allinone_rewards'}</th>
							<th>{l s='Total orders' mod='allinone_rewards'}</th>
							<th>{l s='Total rewards' mod='allinone_rewards'}</th>
							<th>{l s='End date' mod='allinone_rewards'}</th>
							<th class='filter-false sorter-false'>{l s='Action' mod='allinone_rewards'}</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$friends item=sponsored}
						{if $sponsored['level_sponsorship'] > 1}
							{assign var="channel" value=""}
						{else}
							{assign var="channel" value="{l s='Email invitation' mod='allinone_rewards'}"}
							{if ($sponsored['channel']==2)}
								{assign var="channel" value="{l s='Sponsorship link' mod='allinone_rewards'}"}
							{else if ($sponsored['channel']==3)}
								{assign var="channel" value="{l s='Facebook' mod='allinone_rewards'}"}
							{else if ($sponsored['channel']==4)}
								{assign var="channel" value="{l s='Twitter' mod='allinone_rewards'}"}
							{else if ($sponsored['channel']==5)}
								{assign var="channel" value="{l s='Google +1' mod='allinone_rewards'}"}
							{/if}
						{/if}
						<tr {if !$sponsored['active']}class="inactive"{/if}>
							<td align='center'>{$sponsored['level_sponsorship']|intval}</td>
							<td>{$channel|escape:'htmlall':'UTF-8'}</td>
							<td><a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $sponsored['id_sponsored'], 'viewcustomer' => 1])|escape:'html':'UTF-8'}">{$sponsored['lastname']|escape:'htmlall':'UTF-8'} {$sponsored['firstname']|escape:'htmlall':'UTF-8'}</a></td>
							<td align='center'>{$sponsored['nb_orders']|intval}</td>
							<td align='right'>{displayPrice price=$sponsored['total_orders']}</td>
							<td align='right'>{displayPrice price=$sponsored['total_rewards']}</td>
							<td class="nostrike text-center">
							{if $sponsored['level_sponsorship']==1}
							<div class="input-group">
								<input type="text" {if $sponsored['deleted']}disabled{/if} name="date_end_{$sponsored['id_sponsorship']|intval}" class="form-control datetimepicker" value="{if $sponsored['date_end']!='0000-00-00 00:00:00'}{$sponsored['date_end']|escape:'htmlall':'UTF-8'}{/if}">
								<span class="input-group-addon"><i class="material-icons">date_range</i></span>
							</div>
							{/if}
							</td>
							<td class="nostrike text-center">{if !$sponsored['deleted'] && $sponsored['level_sponsorship']==1}<button class="btn btn-primary" name="submitSponsorshipEndDate" type="submit" onClick="$('#id_sponsorship_to_update').val({$sponsored['id_sponsorship']|intval})">{l s='Save settings' mod='allinone_rewards'}</button>{/if}</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
				<div class="pager">
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/first.png" class="first"/>
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/prev.png" class="prev"/>
			    	<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/next.png" class="next"/>
			    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/last.png" class="last"/>
			    	<select class="pagesize form-control" style="width: auto; display: inline">
			      		<option value='10'>10</option>
			      		<option value='20'>20</option>
			      		<option value='50'>50</option>
			      		<option value='100'>100</option>
			      		<option value='500'>500</option>
			    	</select>
				</div>
		{else}
				{l s='This customer has not sponsored any friends yet.' mod='allinone_rewards'}
		{/if}
			</form>
		</div>
	</div>
</div>
<script>
{if version_compare($smarty.const._PS_VERSION_,'1.7.6','<')}
	$('.datetimepicker').datetimepicker({
		prevText: '',
		nextText: '',
		dateFormat: 'yy-mm-dd',
		// Define a custom regional settings in order to use PrestaShop translation tools
		currentText: '{l s='Now' mod='allinone_rewards'}',
		closeText: '{l s='Done' mod='allinone_rewards'}',
		ampm: false,
		amNames: ['AM', 'A'],
		pmNames: ['PM', 'P'],
		timeFormat: 'hh:mm:ss tt',
		timeSuffix: '',
		timeOnlyTitle: "{l s='Choose Time' mod='allinone_rewards'}",
		timeText: '{l s='Time' mod='allinone_rewards'}',
		hourText: '{l s='Hour' mod='allinone_rewards'}',
		minuteText: '{l s='Minute' mod='allinone_rewards'}',
		secondText: '{l s='Second' mod='allinone_rewards'}',
		showSecond: true
	});
{else}
	$('.datetimepicker').datetimepicker({
      	locale: iso_user,
		format: 'YYYY-MM-DD HH:mm:ss',
		sideBySide: true,
		icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-arrow-up',
        	down: 'fa fa-arrow-down'
        },
        tooltips: {
		    selectMonth: '',
		    prevMonth: '',
		    nextMonth: '',
		    selectYear: '',
		    prevYear: '',
		    nextYear: '',
		    incrementHour: '',
		    decrementHour:'',
		    incrementMinute: '',
		    decrementMinute:'',
		    incrementSecond: '',
		    decrementSecond:'',
		    pickHour: '{l s='Hour' mod='allinone_rewards'}',
		    pickMinute: '{l s='Minute' mod='allinone_rewards'}',
		    pickSecond: '{l s='Second' mod='allinone_rewards'}',
		}
    });
{/if}

var idText="{l s='ID' mod='allinone_rewards'}";
var firstnameText="{l s='Firstname' mod='allinone_rewards'}";
var lastnameText="{l s='Lastname' mod='allinone_rewards'}";
var emailText="{l s='Email' mod='allinone_rewards'}";
var sponsor_url = "{$sponsor_url|escape:'javascript':'UTF-8'}";
initAutocomplete(true);
</script>
<!-- END : MODULE allinone_rewards -->