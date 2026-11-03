{*
 * JosraWhatsapp - Rejilla de programacion semanal
 * Se inyecta como campo 'html' dentro del HelperForm nativo.
 *}
<div class="josra-schedule">

    <div class="josra-days">
        {foreach from=$josra_days item=day}
            {assign var='d' value=$josra_schedule[$day]}
            <div class="josra-day" data-day="{$day|escape:'html':'UTF-8'}">

                <input type="hidden"
                       name="josra_day_enabled_{$day|escape:'html':'UTF-8'}"
                       id="josra_day_enabled_{$day|escape:'html':'UTF-8'}"
                       value="{if $d.enabled}1{else}0{/if}" />

                <button type="button"
                        class="josra-day-toggle {if $d.enabled}josra-on{else}josra-off{/if}"
                        data-target="josra_day_enabled_{$day|escape:'html':'UTF-8'}"
                        data-on="{l s='Abierto' mod='josrawhatsapp'}"
                        data-off="{l s='Cerrado' mod='josrawhatsapp'}">
                    <span class="josra-day-name">{$josra_day_labels[$day]|escape:'html':'UTF-8'}</span>
                    <span class="josra-day-state">
                        {if $d.enabled}{l s='Abierto' mod='josrawhatsapp'}{else}{l s='Cerrado' mod='josrawhatsapp'}{/if}
                    </span>
                </button>

                <div class="josra-shifts">

                    <div class="josra-shift">
                        <input type="time" class="form-control input-sm"
                               name="josra_t1_from_{$day|escape:'html':'UTF-8'}"
                               value="{$d.t1_from|escape:'html':'UTF-8'}" />
                        <input type="time" class="form-control input-sm"
                               name="josra_t1_to_{$day|escape:'html':'UTF-8'}"
                               value="{$d.t1_to|escape:'html':'UTF-8'}" />
                    </div>

                    <input type="hidden"
                           name="josra_t2_enabled_{$day|escape:'html':'UTF-8'}"
                           id="josra_t2_enabled_{$day|escape:'html':'UTF-8'}"
                           value="{if $d.t2_enabled}1{else}0{/if}" />

                    <div class="josra-shift josra-shift-2"
                         id="josra_shift2_{$day|escape:'html':'UTF-8'}"
                         {if !$d.t2_enabled}style="display:none;"{/if}>
                        <input type="time" class="form-control input-sm"
                               name="josra_t2_from_{$day|escape:'html':'UTF-8'}"
                               value="{$d.t2_from|escape:'html':'UTF-8'}" />
                        <input type="time" class="form-control input-sm"
                               name="josra_t2_to_{$day|escape:'html':'UTF-8'}"
                               value="{$d.t2_to|escape:'html':'UTF-8'}" />
                    </div>

                    <button type="button" class="josra-shift2-toggle"
                            data-day="{$day|escape:'html':'UTF-8'}"
                            data-add="{l s='+ turno tarde' mod='josrawhatsapp'}"
                            data-remove="{l s='- turno tarde' mod='josrawhatsapp'}">
                        {if $d.t2_enabled}
                            {l s='- turno tarde' mod='josrawhatsapp'}
                        {else}
                            {l s='+ turno tarde' mod='josrawhatsapp'}
                        {/if}
                    </button>

                </div>
            </div>
        {/foreach}
    </div>

    <div class="josra-schedule-tools">
        <button type="button" class="btn btn-default btn-xs" id="josra_copy_monday">
            <i class="icon-copy"></i> {l s='Copiar el lunes al resto' mod='josrawhatsapp'}
        </button>
        <button type="button" class="btn btn-default btn-xs" id="josra_open_all">
            {l s='Abrir todos' mod='josrawhatsapp'}
        </button>
        <button type="button" class="btn btn-default btn-xs" id="josra_close_weekend">
            {l s='Cerrar fin de semana' mod='josrawhatsapp'}
        </button>
        <span class="help-block josra-inline-help">
            {l s='Pulsa el nombre del dia para abrirlo o cerrarlo.' mod='josrawhatsapp'}
        </span>
    </div>

</div>
