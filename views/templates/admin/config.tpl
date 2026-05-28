{* JosraWhatsapp - Panel de configuración con pestañas *}

<style>
.josra-tabs { display:flex; gap:4px; margin-bottom:0; border-bottom:2px solid #25d366; }
.josra-tab  { padding:10px 20px; cursor:pointer; background:#f5f5f5; border:1px solid #ddd;
              border-bottom:none; border-radius:4px 4px 0 0; font-weight:600; color:#555; }
.josra-tab.active { background:#25d366; color:#fff; border-color:#25d366; }
.josra-tab-content { display:none; }
.josra-tab-content.active { display:block; }

/* Selector de días */
.josra-day-grid { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.josra-day-block { border:1px solid #ddd; border-radius:6px; padding:12px; min-width:130px; }
.josra-day-toggle { display:block; text-align:center; font-weight:700; font-size:14px;
                    padding:8px; border-radius:4px; cursor:pointer; margin-bottom:10px;
                    transition:background 0.2s; }
.josra-day-toggle.on  { background:#25d366; color:#fff; }
.josra-day-toggle.off { background:#e74c3c; color:#fff; }
.josra-time-row { display:flex; align-items:center; gap:4px; margin-bottom:6px; font-size:12px; }
.josra-time-row input[type=time] { width:90px; padding:3px 5px; border:1px solid #ccc; border-radius:3px; }
.josra-turno2-toggle { font-size:11px; color:#888; cursor:pointer; text-decoration:underline;
                       display:block; margin-top:4px; }
.josra-turno2-block { margin-top:6px; padding-top:6px; border-top:1px dashed #ddd; }
</style>

<form id="josra_wap_form" class="defaultForm form-horizontal" method="post"
      action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}">

    <input type="hidden" name="submitJosraWhatsapp" value="1">

    <div class="josra-tabs">
        <div class="josra-tab active" onclick="josraTab('general',this)">⚙️ General</div>
        <div class="josra-tab" onclick="josraTab('apariencia',this)">🎨 Apariencia</div>
        <div class="josra-tab" onclick="josraTab('programacion',this)">🕐 Programación</div>
    </div>

    {* ===== PESTAÑA GENERAL ===== *}
    <div id="josra-tab-general" class="josra-tab-content active">
        <div class="panel" style="border-top:none; border-radius:0 4px 4px 4px;">

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Activar módulo' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="josra_enabled" id="josra_enabled_on" value="1"
                               {if $josra_fields.enabled == '1'}checked{/if}>
                        <label for="josra_enabled_on">{l s='Sí' mod='josrawhatsapp'}</label>
                        <input type="radio" name="josra_enabled" id="josra_enabled_off" value="0"
                               {if $josra_fields.enabled != '1'}checked{/if}>
                        <label for="josra_enabled_off">{l s='No' mod='josrawhatsapp'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Número de teléfono' mod='josrawhatsapp'} <span class="required">*</span></label>
                <div class="col-lg-9">
                    <input type="text" name="josra_phone" class="fixed-width-xxl"
                           value="{$josra_fields.phone|escape:'html':'UTF-8'}"
                           placeholder="34612345678" />
                    <p class="help-block">{l s='Con prefijo de país, sin espacios. Ej: 34612345678' mod='josrawhatsapp'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Texto sobre el icono' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <input type="text" name="josra_above_text" class="fixed-width-xxl"
                           value="{$josra_fields.above_text|escape:'html':'UTF-8'}"
                           placeholder="¿Hablamos?" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Mensaje predefinido' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <input type="text" name="josra_send_text" class="fixed-width-xxl"
                           value="{$josra_fields.send_text|escape:'html':'UTF-8'}"
                           placeholder="Hola, me gustaría información sobre..." />
                    <p class="help-block">{l s='Texto que se abre al pulsar el botón.' mod='josrawhatsapp'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Incluir URL de la página' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="josra_send_url" id="josra_send_url_on" value="1"
                               {if $josra_fields.send_url == '1'}checked{/if}>
                        <label for="josra_send_url_on">{l s='Sí' mod='josrawhatsapp'}</label>
                        <input type="radio" name="josra_send_url" id="josra_send_url_off" value="0"
                               {if $josra_fields.send_url != '1'}checked{/if}>
                        <label for="josra_send_url_off">{l s='No' mod='josrawhatsapp'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" name="submitJosraWhatsapp" value="1" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Guardar' mod='josrawhatsapp'}
                </button>
                <button type="submit" name="submitJosraWhatsappDefaults" value="1" class="btn btn-default pull-right">
                    <i class="process-icon-refresh"></i> {l s='Restaurar defaults' mod='josrawhatsapp'}
                </button>
            </div>
        </div>
    </div>

    {* ===== PESTAÑA APARIENCIA ===== *}
    <div id="josra-tab-apariencia" class="josra-tab-content">
        <div class="panel" style="border-top:none; border-radius:0 4px 4px 4px;">

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Icono' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    {for $i=1 to 4}
                    <label style="cursor:pointer; margin-right:15px;">
                        <input type="radio" name="josra_icon" value="{$i}"
                               {if $josra_fields.icon == $i}checked{/if}>
                        <img src="{$josra_module_dir}views/img/icon{$i}.png" width="55"
                             style="vertical-align:middle; margin-left:4px;
                             filter:hue-rotate({$josra_fields.icon_color|intval}deg);" />
                    </label>
                    {/for}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Color (hue-rotate)' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <input type="number" name="josra_icon_color" class="fixed-width-sm"
                           min="0" max="360" value="{$josra_fields.icon_color|intval}" />
                    <p class="help-block">{l s='0=verde, 90=azul, 180=rosa, 220=rojo, 270=naranja' mod='josrawhatsapp'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Tamaño (px)' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <input type="number" name="josra_icon_size" class="fixed-width-sm"
                           min="30" max="200" value="{$josra_fields.icon_size|intval}" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Posición' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <label style="margin-right:20px; cursor:pointer;">
                        <input type="radio" name="josra_icon_pos" value="1"
                               {if $josra_fields.icon_pos == '1'}checked{/if}>
                        {l s='Derecha - abajo' mod='josrawhatsapp'}
                    </label>
                    <label style="cursor:pointer;">
                        <input type="radio" name="josra_icon_pos" value="2"
                               {if $josra_fields.icon_pos == '2'}checked{/if}>
                        {l s='Izquierda - abajo' mod='josrawhatsapp'}
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Animar icono' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="josra_animate" id="josra_animate_on" value="1"
                               {if $josra_fields.animate == '1'}checked{/if}>
                        <label for="josra_animate_on">{l s='Sí' mod='josrawhatsapp'}</label>
                        <input type="radio" name="josra_animate" id="josra_animate_off" value="0"
                               {if $josra_fields.animate != '1'}checked{/if}>
                        <label for="josra_animate_off">{l s='No' mod='josrawhatsapp'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                    <p class="help-block">{l s='Animación de pulso para llamar la atención.' mod='josrawhatsapp'}</p>
                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" name="submitJosraWhatsapp" value="1" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Guardar' mod='josrawhatsapp'}
                </button>
            </div>
        </div>
    </div>

    {* ===== PESTAÑA PROGRAMACIÓN ===== *}
    <div id="josra-tab-programacion" class="josra-tab-content">
        <div class="panel" style="border-top:none; border-radius:0 4px 4px 4px;">

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Activar programación' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="josra_schedule_enabled" id="josra_sche_on" value="1"
                               {if $josra_fields.schedule_enabled == '1'}checked{/if}>
                        <label for="josra_sche_on">{l s='Sí' mod='josrawhatsapp'}</label>
                        <input type="radio" name="josra_schedule_enabled" id="josra_sche_off" value="0"
                               {if $josra_fields.schedule_enabled != '1'}checked{/if}>
                        <label for="josra_sche_off">{l s='No' mod='josrawhatsapp'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                    <p class="help-block">{l s='Si está activo, el botón solo se muestra en el horario definido.' mod='josrawhatsapp'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Programación semanal' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <div class="josra-day-grid">
                        {foreach $josra_days as $d}
                            {assign var='ds' value=$josra_fields.schedule[$d]|default:[]}
                            {assign var='den' value=$ds.enabled|default:0}
                            {assign var='t2en' value=$ds.t2_enabled|default:0}
                        <div class="josra-day-block">
                            <input type="hidden" name="josra_day_enabled_{$d}" id="josra_day_enabled_{$d}" value="{$den|intval}">
                            <span class="josra-day-toggle {if $den}on{else}off{/if}"
                                  onclick="josraToggleDay('{$d}',this)">
                                {$josra_day_labels[$d]}
                            </span>

                            <div class="josra-time-row">
                                <span title="Apertura">🌅</span>
                                <input type="time" name="josra_t1_from_{$d}"
                                       value="{$ds.t1_from|default:'09:00'|escape:'html':'UTF-8'}" />
                            </div>
                            <div class="josra-time-row">
                                <span title="Cierre">🌙</span>
                                <input type="time" name="josra_t1_to_{$d}"
                                       value="{$ds.t1_to|default:'14:00'|escape:'html':'UTF-8'}" />
                            </div>

                            <span class="josra-turno2-toggle" onclick="josraToggleTurno2('{$d}',this)">
                                {if $t2en}➖ Quitar turno 2{else}➕ Añadir turno 2{/if}
                            </span>

                            <div id="josra_turno2_{$d}" class="josra-turno2-block"
                                 style="display:{if $t2en}block{else}none{/if}">
                                <input type="hidden" name="josra_t2_enabled_{$d}" id="josra_t2_enabled_{$d}" value="{$t2en|intval}">
                                <div class="josra-time-row">
                                    <span>🌅</span>
                                    <input type="time" name="josra_t2_from_{$d}"
                                           value="{$ds.t2_from|default:'16:00'|escape:'html':'UTF-8'}" />
                                </div>
                                <div class="josra-time-row">
                                    <span>🌙</span>
                                    <input type="time" name="josra_t2_to_{$d}"
                                           value="{$ds.t2_to|default:'20:00'|escape:'html':'UTF-8'}" />
                                </div>
                            </div>
                        </div>
                        {/foreach}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Mensaje offline' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <input type="text" name="josra_offline_msg" class="fixed-width-xxl"
                           value="{$josra_fields.offline_msg|escape:'html':'UTF-8'}"
                           placeholder="Estoy descansando, déjame tu mensaje..." />
                    <p class="help-block">{l s='Texto que aparece fuera de horario. Vacío = ocultar botón.' mod='josrawhatsapp'}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Enlace offline' mod='josrawhatsapp'}</label>
                <div class="col-lg-9">
                    <input type="text" name="josra_offline_url" class="fixed-width-xxl"
                           value="{$josra_fields.offline_url|escape:'html':'UTF-8'}"
                           placeholder="/contactenos" />
                    <p class="help-block">{l s='URL a la que lleva el botón fuera de horario. Vacío = no enlazar.' mod='josrawhatsapp'}</p>
                </div>
            </div>

            <div class="panel-footer">
                <button type="submit" name="submitJosraWhatsapp" value="1" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Guardar' mod='josrawhatsapp'}
                </button>
            </div>
        </div>
    </div>

</form>

<script>
function josraTab(name, el) {
    document.querySelectorAll('.josra-tab-content').forEach(function(t){ t.classList.remove('active'); });
    document.querySelectorAll('.josra-tab').forEach(function(t){ t.classList.remove('active'); });
    document.getElementById('josra-tab-' + name).classList.add('active');
    el.classList.add('active');
}

function josraToggleDay(day, el) {
    var inp = document.getElementById('josra_day_enabled_' + day);
    var isOn = inp.value == '1';
    inp.value = isOn ? '0' : '1';
    el.classList.toggle('on', !isOn);
    el.classList.toggle('off', isOn);
}

function josraToggleTurno2(day, el) {
    var block = document.getElementById('josra_turno2_' + day);
    var inp   = document.getElementById('josra_t2_enabled_' + day);
    var isVisible = block.style.display !== 'none';
    block.style.display = isVisible ? 'none' : 'block';
    inp.value = isVisible ? '0' : '1';
    el.textContent = isVisible ? '➕ Añadir turno 2' : '➖ Quitar turno 2';
}
</script>
