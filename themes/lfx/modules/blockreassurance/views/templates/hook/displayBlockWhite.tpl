<div class="facilities_sec">
    <div class="container">
        <div class="row">
            {foreach from=$blocks item=$block key=$key name=blocks}
                {if $block['icon'] != 'undefined'}
                    <div class="f_box">
                        <div class="icon">
                        {if $block['custom_icon']}
                            <img src="{$block['custom_icon']}">
                        {elseif $block['icon']}
                            <img src="{$block['icon']}">
                        {/if}
                        </div>
                        <div class="txt">{$block['title']}</div>
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
</div>