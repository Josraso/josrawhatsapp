<?php
/**
 * JosraWhatsapp - Botón flotante de WhatsApp para PrestaShop
 * Compatible: PS 1.7, PS 8, PS 9
 * Autor: josraso
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class JosraWhatsapp extends Module
{
    private $days = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];

    public function __construct()
    {
        $this->name             = 'josrawhatsapp';
        $this->tab              = 'front_office_features';
        $this->version          = '1.1.0';
        $this->author           = 'josraso';
        $this->need_instance    = 0;
        $this->bootstrap        = true;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('JosraWhatsapp - Botón flotante WhatsApp');
        $this->description = $this->l('Botón flotante de WhatsApp con horarios y programación semanal');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayFooterBefore')
            && $this->setDefaults();
    }

    public function uninstall()
    {
        $keys = [
            'JOSRA_WAP_PHONE','JOSRA_WAP_ABOVE_TEXT','JOSRA_WAP_SEND_TEXT',
            'JOSRA_WAP_SEND_URL','JOSRA_WAP_ICON','JOSRA_WAP_ICON_SIZE',
            'JOSRA_WAP_ICON_POS','JOSRA_WAP_ICON_COLOR','JOSRA_WAP_ANIMATE',
            'JOSRA_WAP_ENABLED','JOSRA_WAP_SCHEDULE_ENABLED',
            'JOSRA_WAP_OFFLINE_MSG','JOSRA_WAP_OFFLINE_URL','JOSRA_WAP_SCHEDULE',
        ];
        foreach ($keys as $k) {
            Configuration::deleteByName($k);
        }
        return parent::uninstall();
    }

    private function setDefaults()
    {
        Configuration::updateValue('JOSRA_WAP_PHONE', '');
        Configuration::updateValue('JOSRA_WAP_ABOVE_TEXT', '');
        Configuration::updateValue('JOSRA_WAP_SEND_TEXT', '');
        Configuration::updateValue('JOSRA_WAP_SEND_URL', '0');
        Configuration::updateValue('JOSRA_WAP_ICON', '1');
        Configuration::updateValue('JOSRA_WAP_ICON_SIZE', '75');
        Configuration::updateValue('JOSRA_WAP_ICON_POS', '1');
        Configuration::updateValue('JOSRA_WAP_ICON_COLOR', '0');
        Configuration::updateValue('JOSRA_WAP_ANIMATE', '0');
        Configuration::updateValue('JOSRA_WAP_ENABLED', '1');
        Configuration::updateValue('JOSRA_WAP_SCHEDULE_ENABLED', '0');
        Configuration::updateValue('JOSRA_WAP_OFFLINE_MSG', '');
        Configuration::updateValue('JOSRA_WAP_OFFLINE_URL', '');

        // Horario por defecto: L-V 09:00-14:00 y 16:00-20:00, S-D cerrado
        $defaultSchedule = [];
        foreach ($this->days as $d) {
            $defaultSchedule[$d] = [
                'enabled' => in_array($d, ['lunes','martes','miercoles','jueves','viernes']) ? 1 : 0,
                't1_from' => '09:00', 't1_to' => '14:00',
                't2_from' => '16:00', 't2_to' => '20:00',
                't2_enabled' => 0,
            ];
        }
        Configuration::updateValue('JOSRA_WAP_SCHEDULE', json_encode($defaultSchedule));

        return true;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitJosraWhatsappDefaults')) {
            $this->setDefaults();
            $output .= $this->displayConfirmation($this->l('Configuración restablecida a valores por defecto.'));
        }

        if (Tools::isSubmit('submitJosraWhatsapp')) {
            $phone = trim(Tools::getValue('josra_phone'));
            $size  = (int) Tools::getValue('josra_icon_size');

            if (empty($phone)) {
                $output .= $this->displayError($this->l('El número de teléfono es obligatorio.'));
            } elseif ($size < 30 || $size > 200) {
                $output .= $this->displayError($this->l('El tamaño del icono debe estar entre 30 y 200 px.'));
            } else {
                Configuration::updateValue('JOSRA_WAP_PHONE',      $phone);
                Configuration::updateValue('JOSRA_WAP_ABOVE_TEXT', trim(Tools::getValue('josra_above_text')));
                Configuration::updateValue('JOSRA_WAP_SEND_TEXT',  trim(Tools::getValue('josra_send_text')));
                Configuration::updateValue('JOSRA_WAP_SEND_URL',   (int) Tools::getValue('josra_send_url'));
                Configuration::updateValue('JOSRA_WAP_ICON',       (int) Tools::getValue('josra_icon'));
                Configuration::updateValue('JOSRA_WAP_ICON_SIZE',  $size);
                Configuration::updateValue('JOSRA_WAP_ICON_POS',   (int) Tools::getValue('josra_icon_pos'));
                Configuration::updateValue('JOSRA_WAP_ICON_COLOR', (int) Tools::getValue('josra_icon_color'));
                Configuration::updateValue('JOSRA_WAP_ANIMATE',    (int) Tools::getValue('josra_animate'));
                Configuration::updateValue('JOSRA_WAP_ENABLED',    (int) Tools::getValue('josra_enabled'));
                Configuration::updateValue('JOSRA_WAP_SCHEDULE_ENABLED', (int) Tools::getValue('josra_schedule_enabled'));
                Configuration::updateValue('JOSRA_WAP_OFFLINE_MSG', trim(Tools::getValue('josra_offline_msg')));
                Configuration::updateValue('JOSRA_WAP_OFFLINE_URL', trim(Tools::getValue('josra_offline_url')));

                // Guardar horarios
                $schedule = [];
                foreach ($this->days as $d) {
                    $schedule[$d] = [
                        'enabled'    => (int) Tools::getValue('josra_day_enabled_' . $d),
                        't1_from'    => $this->sanitizeTime(Tools::getValue('josra_t1_from_' . $d)),
                        't1_to'      => $this->sanitizeTime(Tools::getValue('josra_t1_to_' . $d)),
                        't2_enabled' => (int) Tools::getValue('josra_t2_enabled_' . $d),
                        't2_from'    => $this->sanitizeTime(Tools::getValue('josra_t2_from_' . $d)),
                        't2_to'      => $this->sanitizeTime(Tools::getValue('josra_t2_to_' . $d)),
                    ];
                }
                Configuration::updateValue('JOSRA_WAP_SCHEDULE', json_encode($schedule));

                $output .= $this->displayConfirmation($this->l('Configuración guardada correctamente.'));
            }
        }

        $this->context->smarty->assign([
            'josra_module_dir' => $this->_path,
            'josra_fields'     => $this->getFields(),
            'josra_days'       => $this->days,
            'josra_day_labels' => [
                'lunes'=>'Lunes','martes'=>'Martes','miercoles'=>'Miércoles',
                'jueves'=>'Jueves','viernes'=>'Viernes','sabado'=>'Sábado','domingo'=>'Domingo'
            ],
        ]);

        return $output . $this->display(__FILE__, 'views/templates/admin/config.tpl');
    }

    private function sanitizeTime($val)
    {
        $val = trim($val);
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $val)) {
            return $val;
        }
        return '00:00';
    }

    public function getFields()
    {
        $schedule = json_decode(Configuration::get('JOSRA_WAP_SCHEDULE'), true);
        if (!is_array($schedule)) {
            $schedule = [];
        }
        return [
            'phone'             => Configuration::get('JOSRA_WAP_PHONE'),
            'above_text'        => Configuration::get('JOSRA_WAP_ABOVE_TEXT'),
            'send_text'         => Configuration::get('JOSRA_WAP_SEND_TEXT'),
            'send_url'          => Configuration::get('JOSRA_WAP_SEND_URL'),
            'icon'              => Configuration::get('JOSRA_WAP_ICON'),
            'icon_size'         => Configuration::get('JOSRA_WAP_ICON_SIZE'),
            'icon_pos'          => Configuration::get('JOSRA_WAP_ICON_POS'),
            'icon_color'        => Configuration::get('JOSRA_WAP_ICON_COLOR'),
            'animate'           => Configuration::get('JOSRA_WAP_ANIMATE'),
            'enabled'           => Configuration::get('JOSRA_WAP_ENABLED'),
            'schedule_enabled'  => Configuration::get('JOSRA_WAP_SCHEDULE_ENABLED'),
            'offline_msg'       => Configuration::get('JOSRA_WAP_OFFLINE_MSG'),
            'offline_url'       => Configuration::get('JOSRA_WAP_OFFLINE_URL'),
            'schedule'          => $schedule,
        ];
    }

    public function hookDisplayHeader($params)
    {
        if (!Configuration::get('JOSRA_WAP_ENABLED') || !Configuration::get('JOSRA_WAP_PHONE')) {
            return '';
        }
        $this->context->controller->addCSS($this->_path . 'views/css/josrawhatsapp.css');
        $this->context->controller->addJS($this->_path . 'views/js/josrawhatsapp.js');
        return '';
    }

    public function hookDisplayFooterBefore($params)
    {
        if (!Configuration::get('JOSRA_WAP_ENABLED') || !Configuration::get('JOSRA_WAP_PHONE')) {
            return '';
        }

        $fields = $this->getFields();
        $phone  = preg_replace('/[^0-9+]/', '', $fields['phone']);

        $sendText = '';
        if (!empty($fields['send_text'])) {
            $sendText = urlencode($fields['send_text']);
        }
        if ($fields['send_url'] == '1') {
            $currentUrl = Tools::getHttpHost(true) . $_SERVER['REQUEST_URI'];
            $sendText  .= ($sendText ? '%20' : '') . urlencode($currentUrl);
        }

        $whatsappUrl = 'https://api.whatsapp.com/send?phone=' . $phone . ($sendText ? '&text=' . $sendText : '');
        $mobileUrl   = 'whatsapp://send?phone=' . $phone . ($sendText ? '&text=' . $sendText : '');

        // Comprobar si estamos en horario
        $isOnline    = true;
        $offlineMsg  = '';
        $offlineUrl  = '';

        if ($fields['schedule_enabled'] == '1' && !empty($fields['schedule'])) {
            $isOnline   = $this->isOnSchedule($fields['schedule']);
            $offlineMsg = $fields['offline_msg'];
            $offlineUrl = $fields['offline_url'];

            // Si fuera de horario y no hay mensaje ni enlace offline, no mostrar botón
            if (!$isOnline && empty($offlineMsg) && empty($offlineUrl)) {
                return '';
            }
        }

        $this->context->smarty->assign([
            'josra_wap_url'        => $whatsappUrl,
            'josra_wap_mobile_url' => $mobileUrl,
            'josra_wap_fields'     => $fields,
            'josra_module_dir'     => $this->_path,
            'josra_is_online'      => $isOnline,
            'josra_offline_msg'    => $offlineMsg,
            'josra_offline_url'    => $offlineUrl,
        ]);

        return $this->display(__FILE__, 'views/templates/front/button.tpl');
    }

    private function isOnSchedule($schedule)
    {
        $dayMap = [0=>'domingo',1=>'lunes',2=>'martes',3=>'miercoles',4=>'jueves',5=>'viernes',6=>'sabado'];
        $now    = new DateTime('now');
        $dow    = (int) $now->format('w');
        $dayKey = $dayMap[$dow];

        if (!isset($schedule[$dayKey]) || !$schedule[$dayKey]['enabled']) {
            return false;
        }

        $day     = $schedule[$dayKey];
        $current = $now->format('H:i');

        // Turno 1
        if ($current >= $day['t1_from'] && $current <= $day['t1_to']) {
            return true;
        }
        // Turno 2
        if (!empty($day['t2_enabled']) && $day['t2_enabled'] == 1) {
            if ($current >= $day['t2_from'] && $current <= $day['t2_to']) {
                return true;
            }
        }

        return false;
    }
}
