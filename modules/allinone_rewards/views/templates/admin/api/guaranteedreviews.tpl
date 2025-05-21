{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div class="clear not_templated indent optional review_api_optional_GuaranteedReviewsAPI">
	<label>{l s='API key' mod='allinone_rewards'}</label>
	<div class="margin-form">
		<input type="text" size="100" name="review_gr_key" value="{$review_gr_key|escape:'htmlall':'UTF-8'}" />
		<div style="padding-top: 8px">
			{if $lang_iso=='fr'}
			{assign var="sdga_url" value="https://www.societe-des-avis-garantis.fr/"}
			{assign var="sdga_url_settings" value="configuration/prestashop/?swcfpc=1"}
			{else if $lang_iso=='es'}
			{assign var="sdga_url" value="https://www.sociedad-de-opiniones-contrastadas.es/"}
			{assign var="sdga_url_settings" value="configuracion/certificado-de-confianza/?swcfpc=1"}
			{else if $lang_iso=='it'}
			{assign var="sdga_url" value="https://www.societa-recensioni-garantite.it/"}
			{assign var="sdga_url_settings" value="conﬁgurazione/certificato-di-fiducia/?swcfpc=1"}
			{else if $lang_iso=='de'}
			{assign var="sdga_url" value="https://www.g-g-b.de/"}
			{assign var="sdga_url_settings" value="einstellungen/zertifikat-des-vertrauens/?swcfpc=1"}
			{else if $lang_iso=='nl'}
			{assign var="sdga_url" value="https://www.g-b-n.nl/"}
			{assign var="sdga_url_settings" value="configuratie/vertrouwenscertificaat/?swcfpc=1"}
			{else}
			{assign var="sdga_url" value="https://www.guaranteed-reviews.com/"}
			{assign var="sdga_url_settings" value="settings/trust-attestation/?swcfpc=1"}
			{/if}
			<a href="{$sdga_url|escape:'htmlall':'UTF-8'}{$sdga_url_settings|escape:'htmlall':'UTF-8'}&ref=17198" target="_blank">{l s='You can find it in Configuration > Integration > Module/CCI page in your Guaranteed Reviews Company account' mod='allinone_rewards'}</a>
		</div>
		<div class="reward_alert_message">
			<b>{l s='Not using Guaranteed Reviews Company yet?' mod='allinone_rewards'}</b><br>
			{l s='Send a mail to contact@societe-des-avis-garantis.fr and provide them with my PRESTAPLUGINS code to benefit from the following advantages:' mod='allinone_rewards'}<br><br>
			<ul>
				<li>
					<b>{l s='Don\t have a review platform yet?' mod='allinone_rewards'}</b><br>
					{l s='A 30% discount will be applied to your first year of subscription.' mod='allinone_rewards'}<br><br>
				</li>
				<li>
					<b>{l s='You already have a review platform:' mod='allinone_rewards'}</b><br>
					{l s='Switch to Guaranteed Reviews Company and enjoy a free subscription until the end of your current platform\'s subscription (or a minimum of 1 month free).' mod='allinone_rewards'}<br>
					<u>{l s='Good to know:' mod='allinone_rewards'}</u> {l s='Guaranteed Reviews Company allows you to retrieve your existing reviews from many platforms and offers free installation.' mod='allinone_rewards'}<br><br>
				</li>
			</ul>
			<b>{l s='Website URL:' mod='allinone_rewards'}</b> <a href="{$sdga_url|escape:'htmlall':'UTF-8'}?ref=17198" target="_blank">{$sdga_url|escape:'htmlall':'UTF-8'}</a>
		</div>
	</div>
</div>
<script>
	jQuery(function($){
		var text = $('.review_api_optional_GuaranteedReviewsAPI .reward_alert_message').html();
		text = text.replace(/contact@societe-des-avis-garantis\.fr/g, '<a href="mailto:contact@societe-des-avis-garantis.fr" target="_blank">contact@societe-des-avis-garantis.fr</a>');
		text = text.replace(/PRESTAPLUGINS/g, '<span style="color: red; font-weight: bold">PRESTAPLUGINS</span>');
		$('.review_api_optional_GuaranteedReviewsAPI .reward_alert_message').html(text);
	});
</script>