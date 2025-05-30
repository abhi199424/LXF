{*
 * This file allows you to customize your search page.
 * You can safely remove it if you want it to appear exactly like all other product listing pages
 *}
{extends file='catalog/listing/product-list.tpl'}

{block name="error_content"}
  <h4 id="product-search-no-matches">{l s='No matches were found for your search' d='Shop.Theme.Catalog'}</h4>
  <p>{l s='Please try other keywords to describe what you are looking for.' d='Shop.Theme.Catalog'}</p>
{/block}

{block name='product_list'}
  <div class="sec_heading title">
      <h1 class="h1">Search result for "{$search_string}"</h1>
  </div>
  {include file='catalog/_partials/products.tpl' listing=$listing productClass="col-xs-12 col-sm-6 col-xl-3"}
{/block}
