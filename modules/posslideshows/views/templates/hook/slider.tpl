{if $homeslider.slides}
<div class="explore_universe">
    <div class="container">
        <div class="sec_heading title">
            <h2>{l s='Explorez nos rayons' mod='posslideshows'}</h2>
        </div>
        <div class="row posfordesktop d-none d-lg-block">
            <div class="col-lg-12 col-12">
                <div class="owl-carousel owl-theme universe_slider">
					{foreach from=$homeslider.slides key=key item=slide}
						<div class="item">
							<a class="box" href="{$slide.url}s">
								<h6>{$slide.title}</h6>
                                <img src="{$slide.image_url}" alt="{$slide.title}">
							</a>
						</div>
					{/foreach}
                </div>
            </div>
        </div>

        <div class="row posformobile d-block d-lg-none">
          {foreach from=$homeslider.slides key=key item=slide}
            <div class="col-lg-3 col-sm-6">
              <a class="box" href="{$slide.url}s">
                <h6>{$slide.title}</h6>
                                <img src="{$slide.image_url}" alt="{$slide.title}">
              </a>
            </div>
          {/foreach}
               
        </div>


    </div>
</div>
{/if}