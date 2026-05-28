{* JosraWhatsapp - Botón flotante frontend *}

{if $josra_is_online}
    {assign var='josra_href' value=$josra_wap_url}
    {assign var='josra_mobile_href' value=$josra_wap_mobile_url}
    {assign var='josra_title' value=$josra_wap_fields.above_text|default:'WhatsApp'}
{else}
    {assign var='josra_href' value=$josra_offline_url|default:'#'}
    {assign var='josra_mobile_href' value=$josra_offline_url|default:'#'}
    {assign var='josra_title' value=$josra_offline_msg|default:'Fuera de horario'}
{/if}

<div id="josra_whatsapp_button"
     style="
        text-align:center;
        z-index:99999;
        position:fixed;
        bottom:15px;
        {if $josra_wap_fields.icon_pos == '1'}right{else}left{/if}:15px;
        width:{$josra_wap_fields.icon_size|intval}px;
        {if $josra_wap_fields.animate == '1' && $josra_is_online}animation: josra_wap_pulse 1.5s infinite;{/if}
     ">
    <a id="josra_whatsapp_link"
       target="{if $josra_is_online}_blank{else}_self{/if}"
       rel="noopener noreferrer"
       href="{$josra_href|escape:'html':'UTF-8'}"
       data-mobile-href="{$josra_mobile_href|escape:'html':'UTF-8'}"
       title="{$josra_title|escape:'html':'UTF-8'}"
       style="display:block;cursor:pointer;text-decoration:none;">

        {if $josra_is_online && $josra_wap_fields.above_text}
            <span style="display:block;text-align:center;color:#000;font-size:12px;margin-bottom:4px;line-height:1.3;">
                {$josra_wap_fields.above_text|escape:'html':'UTF-8'}
            </span>
        {elseif !$josra_is_online && $josra_offline_msg}
            <span style="display:block;text-align:center;color:#555;font-size:11px;margin-bottom:4px;line-height:1.3;background:#fff;padding:4px;border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                {$josra_offline_msg|escape:'html':'UTF-8'}
            </span>
        {/if}

        <img src="{$josra_module_dir}views/img/icon{$josra_wap_fields.icon|intval}.png"
             width="{$josra_wap_fields.icon_size|intval}"
             height="{$josra_wap_fields.icon_size|intval}"
             style="filter:hue-rotate({$josra_wap_fields.icon_color|intval}deg){if !$josra_is_online} grayscale(100%) opacity(0.6){/if};display:block;"
             alt="WhatsApp" />
    </a>
</div>
