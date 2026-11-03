{*
 * JosraWhatsapp - Vista previa del boton
 * Se actualiza en vivo al tocar tamano, color o icono.
 *}
<div class="josra-preview" id="josra_preview">
    <div class="josra-preview-stage">
        <span class="josra-preview-label">{l s='Escritorio' mod='josrawhatsapp'}</span>
        <div class="josra-preview-box">
            {if $josra_prev_text != ''}
                <span class="josra-preview-caption" id="josra_preview_caption">{$josra_prev_text|escape:'html':'UTF-8'}</span>
            {/if}
            <img id="josra_preview_img"
                 src="{$josra_module_dir|escape:'html':'UTF-8'}views/img/icon{$josra_prev_icon|intval}.png"
                 width="{$josra_prev_size|intval}"
                 style="filter:hue-rotate({$josra_prev_color|intval}deg);"
                 alt="" />
        </div>
        <span class="josra-preview-size" id="josra_preview_size">{$josra_prev_size|intval} px</span>
    </div>

    <div class="josra-preview-stage">
        <span class="josra-preview-label">{l s='Movil' mod='josrawhatsapp'}</span>
        <div class="josra-preview-box">
            <img id="josra_preview_img_mobile"
                 src="{$josra_module_dir|escape:'html':'UTF-8'}views/img/icon{$josra_prev_icon|intval}.png"
                 width="{$josra_prev_size_mobile|intval}"
                 style="filter:hue-rotate({$josra_prev_color|intval}deg);"
                 alt="" />
        </div>
        <span class="josra-preview-size" id="josra_preview_size_mobile"></span>
    </div>
</div>
