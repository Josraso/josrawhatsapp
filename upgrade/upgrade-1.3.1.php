<?php
/**
 * JosraWhatsapp -> 1.3.1
 * Estrena la opcion "fuera de horario": ocultar el boton o mostrarlo en rojo.
 * Se respeta lo que la tienda hacia hasta ahora: antes se mostraba solo si
 * habia mensaje o enlace fuera de horario.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_1($module)
{
    if (Configuration::get('JOSRA_WAP_OFFLINE_MODE') === false) {
        $seMostraba = trim(Configuration::get('JOSRA_WAP_OFFLINE_MSG')) !== ''
            || trim(Configuration::get('JOSRA_WAP_OFFLINE_URL')) !== '';
        Configuration::updateValue('JOSRA_WAP_OFFLINE_MODE', $seMostraba ? 'show' : 'hide');
    }

    return true;
}
