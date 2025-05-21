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
{extends file='page.tpl'}

{block name="page_content"}
    <klarna-placement
            data-key="info-page"
            data-locale="{$klarnapayment.osm.locale}"
    ></klarna-placement>
{/block}