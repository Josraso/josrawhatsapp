{*
 * JosraWhatsapp - Boton flotante
 *}
{if $josra_is_online}
    {assign var='josra_href' value=$josra_url}
    {assign var='josra_mobile_href' value=$josra_mobile_url}
    {assign var='josra_caption' value=$josra_settings.JOSRA_WAP_ABOVE_TEXT}
{else}
    {assign var='josra_href' value=$josra_offline_url}
    {assign var='josra_mobile_href' value=$josra_offline_url}
    {assign var='josra_caption' value=$josra_offline_msg}
{/if}

<div id="josra_whatsapp_button"
     class="josra-wap{if !$josra_is_online} josra-offline{/if}{if $josra_settings.JOSRA_WAP_ANIMATE == 1 && $josra_is_online} josra-animate{/if}"
     style="{if $josra_settings.JOSRA_WAP_ICON_POS == 1}right{else}left{/if}:15px;
            width:{$josra_settings.JOSRA_WAP_ICON_SIZE|intval}px;
            --josra-mobile-scale:{$josra_settings.JOSRA_WAP_MOBILE_SCALE|intval}%;">

    {if $josra_href != ''}
    <a id="josra_whatsapp_link"
       class="josra-wap-link"
       href="{$josra_href|escape:'html':'UTF-8'}"
       data-mobile-href="{$josra_mobile_href|escape:'html':'UTF-8'}"
       target="{if $josra_is_online}_blank{else}_self{/if}"
       rel="noopener noreferrer"
       title="{if $josra_caption != ''}{$josra_caption|escape:'html':'UTF-8'}{else}WhatsApp{/if}">
    {else}
    <span class="josra-wap-link">
    {/if}

        {if $josra_caption != ''}
            <span class="josra-wap-caption">{$josra_caption|escape:'html':'UTF-8'}</span>
        {/if}

        <img class="josra-wap-icon"
             src="{$josra_module_dir|escape:'html':'UTF-8'}views/img/icon{$josra_settings.JOSRA_WAP_ICON|intval}.png"
             width="{$josra_settings.JOSRA_WAP_ICON_SIZE|intval}"
             height="{$josra_settings.JOSRA_WAP_ICON_SIZE|intval}"
             style="filter:hue-rotate({$josra_settings.JOSRA_WAP_ICON_COLOR|intval}deg);"
             alt="WhatsApp" />

    {if $josra_href != ''}
    </a>
    {else}
    </span>
    {/if}
</div>
