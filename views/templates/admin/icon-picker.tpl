{*
 * JosraWhatsapp - Selector de icono
 * Se inyecta como campo 'html' dentro del HelperForm nativo.
 *}
<div class="josra-icon-picker">
    {foreach from=$josra_icons item=n}
        <label class="josra-icon-option {if $josra_icon == $n}josra-icon-selected{/if}">
            <input type="radio" name="JOSRA_WAP_ICON" value="{$n|intval}"
                   {if $josra_icon == $n}checked="checked"{/if} />
            <img src="{$josra_module_dir|escape:'html':'UTF-8'}views/img/icon{$n|intval}.png"
                 alt="{l s='Icono' mod='josrawhatsapp'} {$n|intval}"
                 class="josra-icon-preview"
                 style="filter:hue-rotate({$josra_icon_color|intval}deg);" />
        </label>
    {/foreach}
    <p class="help-block">
        {l s='El giro de color de abajo se aplica tambien a estas miniaturas al guardar.' mod='josrawhatsapp'}
    </p>
</div>
