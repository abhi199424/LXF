{**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 *}
{if $log_severity_level == $log_severity_level_informative}
  <span class="badge badge-pill badge-success" style="margin-bottom: 5px">{l s='Informative only' mod='klarnapayment'} ({$log_severity_level|intval})</span>
{elseif $log_severity_level == $log_severity_level_warning}
  <span class="badge badge-pill badge-warning" style="margin-bottom: 5px">{l s='Warning' mod='klarnapayment'} ({$log_severity_level|intval})</span>
{elseif $log_severity_level == $log_severity_level_error}
  <span class="badge badge-pill badge-danger" style="margin-bottom: 5px">{l s='Error' mod='klarnapayment'} ({$log_severity_level|intval})</span>
{elseif $log_severity_level == $log_severity_level_major}
  <span class="badge badge-pill badge-critical" style="margin-bottom: 5px">{l s='Major issue (crash)!' mod='klarnapayment'} ({$log_severity_level|intval})</span>
{else}
  <span class="badge badge-pill">{$log_severity_level|escape:'htmlall':'UTF-8'}</span>
{/if}
