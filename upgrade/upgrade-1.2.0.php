<?php
/**
 * JosraWhatsapp 1.1.0 -> 1.2.0
 * - Registra el hook displayFooter, necesario en PrestaShop 1.6.
 * - Crea el ajuste nuevo del tamano en movil.
 * - Completa el horario guardado con las claves que falten.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($module)
{
    if (!$module->isRegisteredInHook('displayFooter')) {
        $module->registerHook('displayFooter');
    }
    if (!$module->isRegisteredInHook('displayFooterBefore')) {
        $module->registerHook('displayFooterBefore');
    }
    if (!$module->isRegisteredInHook('displayHeader')) {
        $module->registerHook('displayHeader');
    }

    if (Configuration::get('JOSRA_WAP_MOBILE_SCALE') === false) {
        Configuration::updateValue('JOSRA_WAP_MOBILE_SCALE', '70');
    }

    $schedule = json_decode(Configuration::get('JOSRA_WAP_SCHEDULE'), true);
    if (is_array($schedule)) {
        $days = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo');
        $defaults = array(
            'enabled' => 0,
            't1_from' => '09:00',
            't1_to' => '14:00',
            't2_enabled' => 0,
            't2_from' => '16:00',
            't2_to' => '20:00',
        );

        foreach ($days as $day) {
            if (!isset($schedule[$day]) || !is_array($schedule[$day])) {
                $schedule[$day] = $defaults;
            } else {
                $schedule[$day] = array_merge($defaults, $schedule[$day]);
            }
        }

        Configuration::updateValue('JOSRA_WAP_SCHEDULE', json_encode($schedule));
    }

    return true;
}
