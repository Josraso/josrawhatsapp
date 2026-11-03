{*
 * JosraWhatsapp - Pantalla de configuracion
 * Pestanas con las clases nativas del back-office (nav-tabs / tab-pane).
 * Cada pestana es un HelperForm con su propio boton Guardar.
 *}
<div class="josra-config">

    <div class="josra-status panel">
        <div class="josra-status-inner">
            <span class="josra-status-title">
                <i class="icon-comments"></i> {l s='Estado del boton' mod='josrawhatsapp'}
            </span>
            {if $josra_schedule_on}
                {if $josra_is_open_now}
                    <span class="badge josra-badge-open">{l s='Abierto ahora' mod='josrawhatsapp'}</span>
                {else}
                    <span class="badge josra-badge-closed">{l s='Cerrado ahora' mod='josrawhatsapp'}</span>
                {/if}
            {else}
                <span class="badge josra-badge-always">{l s='Siempre visible' mod='josrawhatsapp'}</span>
            {/if}
            <span class="josra-status-clock">
                {l s='Hora de la tienda' mod='josrawhatsapp'}: <strong>{$josra_now|escape:'html':'UTF-8'}</strong>
            </span>
        </div>
    </div>

    <ul class="nav nav-tabs josra-nav" id="josra-nav">
        {foreach from=$josra_tabs item=t}
            <li class="nav-item{if $t.id == $josra_active_tab} active{/if}">
                <a class="nav-link{if $t.id == $josra_active_tab} active{/if}"
                   href="#josra-pane-{$t.id|escape:'html':'UTF-8'}"
                   data-josra-tab="{$t.id|escape:'html':'UTF-8'}">
                    <i class="{$t.icon|escape:'html':'UTF-8'}"></i> {$t.title|escape:'html':'UTF-8'}
                </a>
            </li>
        {/foreach}
    </ul>

    <div class="tab-content josra-panes">
        {foreach from=$josra_tabs item=t}
            <div class="tab-pane josra-pane{if $t.id == $josra_active_tab} active in{/if}"
                 id="josra-pane-{$t.id|escape:'html':'UTF-8'}">
                {$t.content nofilter}
            </div>
        {/foreach}
    </div>

</div>
