<?php
/**
 * JosraWhatsapp - Boton flotante de WhatsApp para PrestaShop
 * Compatible: PS 1.6, 1.7, 8 y 9
 * Autor: josraso
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class JosraWhatsapp extends Module
{
    /** Evita pintar el boton dos veces si el tema llama a varios hooks de pie */
    protected static $printed = false;

    /** Hooks que el modulo necesita tener registrados */
    protected $expectedHooks = array(
        'displayHeader',
        'displayFooterBefore',
        'displayFooter',
    );

    protected $days = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo');

    public function __construct()
    {
        $this->name = 'josrawhatsapp';
        $this->tab = 'front_office_features';
        $this->version = '1.3.1';
        $this->author = 'josraso';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('JosraWhatsapp - Boton flotante WhatsApp');
        $this->description = $this->l('Boton flotante de WhatsApp con programacion semanal de dos turnos.');
    }

    /* ==================================================================
     * Instalacion
     * ================================================================== */

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        foreach ($this->expectedHooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return $this->setDefaults();
    }

    public function uninstall()
    {
        foreach (array_keys($this->getDefaults()) as $key) {
            Configuration::deleteByName($key);
        }
        Configuration::deleteByName('JOSRA_WAP_SCHEDULE');

        return parent::uninstall();
    }

    /** Unica lista de ajustes: la usan install, uninstall, el reset y la lectura */
    protected function getDefaults()
    {
        return array(
            'JOSRA_WAP_ENABLED' => '1',
            'JOSRA_WAP_PHONE' => '',
            'JOSRA_WAP_ABOVE_TEXT' => '',
            'JOSRA_WAP_SEND_TEXT' => '',
            'JOSRA_WAP_SEND_URL' => '0',
            'JOSRA_WAP_ICON' => '1',
            'JOSRA_WAP_ICON_SIZE' => '75',
            'JOSRA_WAP_ICON_POS' => '1',
            'JOSRA_WAP_ICON_COLOR' => '0',
            'JOSRA_WAP_MOBILE_SCALE' => '70',
            'JOSRA_WAP_ANIMATE' => '0',
            'JOSRA_WAP_SCHEDULE_ENABLED' => '0',
            'JOSRA_WAP_OFFLINE_MODE' => 'hide',
            'JOSRA_WAP_OFFLINE_MSG' => '',
            'JOSRA_WAP_OFFLINE_URL' => '',
        );
    }

    /** Que ajustes guarda cada pestana, para que guardar una no pise las otras */
    protected function getTabFields($tab)
    {
        $map = array(
            'general' => array(
                'JOSRA_WAP_ENABLED',
                'JOSRA_WAP_PHONE',
                'JOSRA_WAP_ABOVE_TEXT',
                'JOSRA_WAP_SEND_TEXT',
                'JOSRA_WAP_SEND_URL',
            ),
            'apariencia' => array(
                'JOSRA_WAP_ICON',
                'JOSRA_WAP_ICON_COLOR',
                'JOSRA_WAP_ICON_SIZE',
                'JOSRA_WAP_MOBILE_SCALE',
                'JOSRA_WAP_ICON_POS',
                'JOSRA_WAP_ANIMATE',
            ),
            'programacion' => array(
                'JOSRA_WAP_SCHEDULE_ENABLED',
                'JOSRA_WAP_OFFLINE_MODE',
                'JOSRA_WAP_OFFLINE_MSG',
                'JOSRA_WAP_OFFLINE_URL',
            ),
        );

        return isset($map[$tab]) ? $map[$tab] : array();
    }

    /** Horario por defecto: L-V de 09:00 a 14:00, fin de semana cerrado */
    protected function getDefaultSchedule()
    {
        $schedule = array();
        $laborables = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');

        foreach ($this->days as $day) {
            $schedule[$day] = array(
                'enabled' => in_array($day, $laborables) ? 1 : 0,
                't1_from' => '09:00',
                't1_to' => '14:00',
                't2_enabled' => 0,
                't2_from' => '16:00',
                't2_to' => '20:00',
            );
        }

        return $schedule;
    }

    protected function setDefaults()
    {
        foreach ($this->getDefaults() as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        Configuration::updateValue('JOSRA_WAP_SCHEDULE', json_encode($this->getDefaultSchedule()));

        return true;
    }

    /**
     * Autorreparacion: al subir el modulo por FTP sin reinstalar, los hooks
     * nuevos no quedan registrados y los ajustes nuevos no existen.
     */
    protected function repair()
    {
        foreach ($this->expectedHooks as $hook) {
            if (!$this->isRegisteredInHook($hook)) {
                $this->registerHook($hook);
            }
        }

        // Antes que los valores por defecto: hasta la 1.3.0 el boton se mostraba
        // fuera de horario solo si habia mensaje o enlace. Al estrenar la opcion
        // se respeta lo que la tienda estuviera haciendo hasta ahora.
        if (Configuration::get('JOSRA_WAP_OFFLINE_MODE') === false) {
            $seMostraba = trim(Configuration::get('JOSRA_WAP_OFFLINE_MSG')) !== ''
                || trim(Configuration::get('JOSRA_WAP_OFFLINE_URL')) !== '';
            Configuration::updateValue('JOSRA_WAP_OFFLINE_MODE', $seMostraba ? 'show' : 'hide');
        }

        foreach ($this->getDefaults() as $key => $value) {
            if (Configuration::get($key) === false) {
                Configuration::updateValue($key, $value);
            }
        }

        if (Configuration::get('JOSRA_WAP_SCHEDULE') === false) {
            Configuration::updateValue('JOSRA_WAP_SCHEDULE', json_encode($this->getDefaultSchedule()));
        }
    }

    /* ==================================================================
     * Lectura de ajustes
     * ================================================================== */

    public function getSettings()
    {
        $settings = array();

        foreach ($this->getDefaults() as $key => $default) {
            $value = Configuration::get($key);
            $settings[$key] = ($value === false) ? $default : $value;
        }

        return $settings;
    }

    public function getSchedule()
    {
        $saved = json_decode(Configuration::get('JOSRA_WAP_SCHEDULE'), true);
        if (!is_array($saved)) {
            $saved = array();
        }

        $schedule = $this->getDefaultSchedule();
        foreach ($this->days as $day) {
            if (isset($saved[$day]) && is_array($saved[$day])) {
                $schedule[$day] = array_merge($schedule[$day], $saved[$day]);
            }
        }

        return $schedule;
    }

    /* ==================================================================
     * Configuracion (Modulos > Configurar)
     * ================================================================== */

    public function getContent()
    {
        $this->repair();

        $output = '';
        $activeTab = 'general';

        if (Tools::isSubmit('submitJosraWhatsappDefaults')) {
            $this->setDefaults();
            $output .= $this->displayConfirmation($this->l('Configuracion restablecida a los valores por defecto.'));
        } elseif (Tools::isSubmit('submitJosraGeneral')) {
            $activeTab = 'general';
            $output .= $this->processSave('general');
        } elseif (Tools::isSubmit('submitJosraApariencia')) {
            $activeTab = 'apariencia';
            $output .= $this->processSave('apariencia');
        } elseif (Tools::isSubmit('submitJosraProgramacion')) {
            $activeTab = 'programacion';
            $output .= $this->processSave('programacion');
        }

        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');

        return $output . $this->renderTabs($activeTab);
    }

    /** Guarda una pestana y devuelve el aviso de exito o los errores */
    protected function processSave($tab)
    {
        $errors = $this->validateTab($tab);

        if (count($errors)) {
            $out = '';
            foreach ($errors as $error) {
                $out .= $this->displayError($error);
            }

            return $out;
        }

        $this->saveTab($tab);

        return $this->displayConfirmation($this->l('Cambios guardados.'));
    }

    protected function validateTab($tab)
    {
        $errors = array();

        if ($tab === 'general') {
            $phone = trim(Tools::getValue('JOSRA_WAP_PHONE'));

            if ($phone === '') {
                $errors[] = $this->l('No se ha guardado: el numero de telefono esta vacio y sin el el boton no puede abrir WhatsApp.');
            } elseif (!preg_match('/^\+?[0-9]{6,20}$/', $phone)) {
                $errors[] = $this->l('No se ha guardado: el telefono solo puede llevar numeros (y un + inicial), con el prefijo del pais y sin espacios. Ejemplo: 34612345678.');
            }
        }

        if ($tab === 'apariencia') {
            $size = (int) Tools::getValue('JOSRA_WAP_ICON_SIZE');
            $scale = (int) Tools::getValue('JOSRA_WAP_MOBILE_SCALE');
            $color = (int) Tools::getValue('JOSRA_WAP_ICON_COLOR');

            if ($size < 30 || $size > 200) {
                $errors[] = $this->l('No se ha guardado: el tamano del icono debe estar entre 30 y 200 px.');
            }
            if ($scale < 30 || $scale > 100) {
                $errors[] = $this->l('No se ha guardado: el tamano en movil debe estar entre 30 y 100 % del tamano normal.');
            }
            if ($color < 0 || $color > 360) {
                $errors[] = $this->l('No se ha guardado: el giro de color debe estar entre 0 y 360 grados.');
            }
        }

        if ($tab === 'programacion') {
            $offlineUrl = trim(Tools::getValue('JOSRA_WAP_OFFLINE_URL'));

            if ($offlineUrl !== '' && !$this->isValidOfflineUrl($offlineUrl)) {
                $errors[] = $this->l('No se ha guardado: el enlace fuera de horario debe empezar por / o por http:// o https://.');
            }

            $errors = array_merge($errors, $this->validateSchedulePost());
        }

        return $errors;
    }

    protected function saveTab($tab)
    {
        foreach ($this->getTabFields($tab) as $key) {
            $value = Tools::getValue($key);

            if ($key === 'JOSRA_WAP_OFFLINE_MODE') {
                Configuration::updateValue($key, $value === 'show' ? 'show' : 'hide');
            } elseif (in_array($key, array('JOSRA_WAP_PHONE', 'JOSRA_WAP_ABOVE_TEXT', 'JOSRA_WAP_SEND_TEXT', 'JOSRA_WAP_OFFLINE_MSG', 'JOSRA_WAP_OFFLINE_URL'))) {
                Configuration::updateValue($key, trim($value));
            } else {
                Configuration::updateValue($key, (int) $value);
            }
        }

        if ($tab !== 'programacion') {
            return;
        }

        $schedule = array();
        foreach ($this->days as $day) {
            $schedule[$day] = array(
                'enabled' => (int) Tools::getValue('josra_day_enabled_' . $day),
                't1_from' => $this->sanitizeTime(Tools::getValue('josra_t1_from_' . $day), '09:00'),
                't1_to' => $this->sanitizeTime(Tools::getValue('josra_t1_to_' . $day), '14:00'),
                't2_enabled' => (int) Tools::getValue('josra_t2_enabled_' . $day),
                't2_from' => $this->sanitizeTime(Tools::getValue('josra_t2_from_' . $day), '16:00'),
                't2_to' => $this->sanitizeTime(Tools::getValue('josra_t2_to_' . $day), '20:00'),
            );
        }
        Configuration::updateValue('JOSRA_WAP_SCHEDULE', json_encode($schedule));
    }

    /** El cierre debe ser posterior a la apertura, y el turno 2 no puede pisar al 1 */
    protected function validateSchedulePost()
    {
        $errors = array();
        $labels = $this->getDayLabels();

        foreach ($this->days as $day) {
            if (!(int) Tools::getValue('josra_day_enabled_' . $day)) {
                continue;
            }

            $t1From = $this->sanitizeTime(Tools::getValue('josra_t1_from_' . $day), '09:00');
            $t1To = $this->sanitizeTime(Tools::getValue('josra_t1_to_' . $day), '14:00');

            if ($t1To <= $t1From) {
                $errors[] = sprintf(
                    $this->l('No se ha guardado: en %s el primer turno cierra a la misma hora o antes de abrir.'),
                    $labels[$day]
                );
            }

            if ((int) Tools::getValue('josra_t2_enabled_' . $day)) {
                $t2From = $this->sanitizeTime(Tools::getValue('josra_t2_from_' . $day), '16:00');
                $t2To = $this->sanitizeTime(Tools::getValue('josra_t2_to_' . $day), '20:00');

                if ($t2To <= $t2From) {
                    $errors[] = sprintf(
                        $this->l('No se ha guardado: en %s el segundo turno cierra a la misma hora o antes de abrir.'),
                        $labels[$day]
                    );
                }
                if ($t2From < $t1To) {
                    $errors[] = sprintf(
                        $this->l('No se ha guardado: en %s el segundo turno empieza antes de que acabe el primero.'),
                        $labels[$day]
                    );
                }
            }
        }

        return $errors;
    }

    protected function sanitizeTime($value, $fallback)
    {
        $value = trim($value);
        if (preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            return $value;
        }
        if (preg_match('/^([0-9]):[0-5][0-9]$/', $value)) {
            return '0' . $value;
        }

        return $fallback;
    }

    protected function isValidOfflineUrl($url)
    {
        if (Tools::substr($url, 0, 1) === '/') {
            return true;
        }

        return (bool) preg_match('#^https?://#i', $url);
    }

    protected function getDayLabels()
    {
        return array(
            'lunes' => $this->l('Lunes'),
            'martes' => $this->l('Martes'),
            'miercoles' => $this->l('Miercoles'),
            'jueves' => $this->l('Jueves'),
            'viernes' => $this->l('Viernes'),
            'sabado' => $this->l('Sabado'),
            'domingo' => $this->l('Domingo'),
        );
    }

    /**
     * URL completa del back-office.
     * En PS 8/9 la configuracion se abre por ruta Symfony, asi que una direccion
     * relativa acabaria en .../configure/index.php y daria 404.
     */
    protected function getBackOfficeUrl()
    {
        $base = Tools::getShopDomainSsl(true) . __PS_BASE_URI__;

        $adminDir = '';
        if (defined('_PS_ADMIN_DIR_')) {
            $adminDir = trim(str_replace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_), '/\\');
        }

        return $base . ($adminDir !== '' ? $adminDir . '/' : '')
            . 'index.php?controller=AdminModules&configure=' . $this->name
            . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
    }

    /* ------------------------------------------------------------------
     * Pestanas
     * ------------------------------------------------------------------ */

    protected function renderTabs($activeTab)
    {
        $settings = $this->getSettings();
        $schedule = $this->getSchedule();

        $tabs = array(
            array(
                'id' => 'general',
                'title' => $this->l('General'),
                'icon' => 'icon-cogs',
                'content' => $this->renderTabForm('general', $this->getFormGeneral(), $settings),
            ),
            array(
                'id' => 'apariencia',
                'title' => $this->l('Apariencia'),
                'icon' => 'icon-picture',
                'content' => $this->renderTabForm('apariencia', $this->getFormAppearance(), $settings),
            ),
            array(
                'id' => 'programacion',
                'title' => $this->l('Programacion'),
                'icon' => 'icon-time',
                'content' => $this->renderTabForm(
                    'programacion',
                    $this->getFormSchedule($schedule),
                    $this->getScheduleValues($settings, $schedule)
                ),
            ),
        );

        $this->context->smarty->assign(array(
            'josra_tabs' => $tabs,
            'josra_active_tab' => $activeTab,
            'josra_is_open_now' => $this->isOnSchedule($schedule),
            'josra_schedule_on' => (int) $settings['JOSRA_WAP_SCHEDULE_ENABLED'],
            'josra_now' => $this->getShopNow()->format('d/m/Y H:i'),
        ));

        return $this->fetchAdmin('configure.tpl');
    }

    protected function getScheduleValues($settings, $schedule)
    {
        $values = $settings;

        foreach ($this->days as $day) {
            $values['josra_day_enabled_' . $day] = (int) $schedule[$day]['enabled'];
            $values['josra_t1_from_' . $day] = $schedule[$day]['t1_from'];
            $values['josra_t1_to_' . $day] = $schedule[$day]['t1_to'];
            $values['josra_t2_enabled_' . $day] = (int) $schedule[$day]['t2_enabled'];
            $values['josra_t2_from_' . $day] = $schedule[$day]['t2_from'];
            $values['josra_t2_to_' . $day] = $schedule[$day]['t2_to'];
        }

        return $values;
    }

    /** Un HelperForm por pestana: cada una guarda solo sus propios campos */
    protected function renderTabForm($tab, $fieldsForm, $values)
    {
        $actions = array(
            'general' => 'submitJosraGeneral',
            'apariencia' => 'submitJosraApariencia',
            'programacion' => 'submitJosraProgramacion',
        );
        $submitAction = $actions[$tab];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->table = $this->name . '_' . $tab;
        $helper->submit_action = $submitAction;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->getBackOfficeUrl();
        $helper->default_form_language = (int) $this->context->language->id;
        $helper->allow_employee_form_lang = (int) $this->context->language->id;
        $helper->show_toolbar = false;
        $helper->fields_value = $values;

        // fields_form es protected en HelperForm (de 1.6 a 9):
        // se pasa como argumento a generateForm(), nunca por propiedad.
        return $helper->generateForm(array($fieldsForm));
    }

    protected function getSwitchValues()
    {
        return array(
            array('id' => 'on', 'value' => 1, 'label' => $this->l('Si')),
            array('id' => 'off', 'value' => 0, 'label' => $this->l('No')),
        );
    }

    protected function getFormGeneral()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Ajustes generales'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mostrar el boton'),
                        'name' => 'JOSRA_WAP_ENABLED',
                        'is_bool' => true,
                        'desc' => $this->l('Desactivalo para quitar el boton de la tienda sin desinstalar el modulo.'),
                        'values' => $this->getSwitchValues(),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Numero de telefono'),
                        'name' => 'JOSRA_WAP_PHONE',
                        'required' => true,
                        'placeholder' => '34612345678',
                        'class' => 'fixed-width-xl',
                        'desc' => $this->l('Con el prefijo del pais y sin espacios ni guiones. Ejemplo: 34612345678.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Texto sobre el icono'),
                        'name' => 'JOSRA_WAP_ABOVE_TEXT',
                        'placeholder' => $this->l('Hablamos?'),
                        'desc' => $this->l('Se muestra encima del icono. Dejalo vacio para no mostrar nada.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Mensaje predefinido'),
                        'name' => 'JOSRA_WAP_SEND_TEXT',
                        'placeholder' => $this->l('Hola, queria informacion sobre...'),
                        'desc' => $this->l('Texto que el cliente vera ya escrito al abrir WhatsApp.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Anadir la URL de la pagina'),
                        'name' => 'JOSRA_WAP_SEND_URL',
                        'is_bool' => true,
                        'desc' => $this->l('Anade al mensaje la direccion desde la que escribe el cliente, para saber que producto esta mirando.'),
                        'values' => $this->getSwitchValues(),
                    ),
                ),
                'submit' => array('title' => $this->l('Guardar')),
                'buttons' => array(
                    array(
                        'type' => 'submit',
                        'name' => 'submitJosraWhatsappDefaults',
                        'title' => $this->l('Restaurar valores por defecto'),
                        'icon' => 'icon-refresh',
                        'js' => 'return confirm(\''
                            . addslashes($this->l('Se perderan todos los ajustes actuales, incluido el horario. Continuar?'))
                            . '\');',
                    ),
                ),
            ),
        );
    }

    protected function getFormAppearance()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Apariencia del boton'),
                    'icon' => 'icon-picture',
                ),
                'input' => array(
                    array(
                        'type' => 'html',
                        'label' => $this->l('Vista previa'),
                        'name' => 'josra_preview',
                        'html_content' => $this->renderPreview(),
                    ),
                    array(
                        'type' => 'html',
                        'label' => $this->l('Icono'),
                        'name' => 'josra_icon_picker',
                        'html_content' => $this->renderIconPicker(),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Giro de color'),
                        'name' => 'JOSRA_WAP_ICON_COLOR',
                        'class' => 'fixed-width-sm josra-live',
                        'suffix' => $this->l('grados'),
                        'desc' => $this->l('0 verde original, 90 azul, 180 rosa, 220 rojo, 270 naranja.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tamano del icono'),
                        'name' => 'JOSRA_WAP_ICON_SIZE',
                        'class' => 'fixed-width-sm josra-live',
                        'suffix' => 'px',
                        'desc' => $this->l('Entre 30 y 200 px. Por defecto 75.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tamano en movil'),
                        'name' => 'JOSRA_WAP_MOBILE_SCALE',
                        'class' => 'fixed-width-sm josra-live',
                        'suffix' => '%',
                        'desc' => $this->l('Porcentaje del tamano normal en pantallas de menos de 768 px. Entre 30 y 100.'),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Posicion'),
                        'name' => 'JOSRA_WAP_ICON_POS',
                        'class' => 't',
                        'values' => array(
                            array('id' => 'pos_right', 'value' => 1, 'label' => $this->l('Abajo a la derecha')),
                            array('id' => 'pos_left', 'value' => 2, 'label' => $this->l('Abajo a la izquierda')),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Animar el icono'),
                        'name' => 'JOSRA_WAP_ANIMATE',
                        'is_bool' => true,
                        'desc' => $this->l('Late despacio para llamar la atencion. Se desactiva solo fuera de horario.'),
                        'values' => $this->getSwitchValues(),
                    ),
                ),
                'submit' => array('title' => $this->l('Guardar')),
            ),
        );
    }

    protected function getFormSchedule($schedule)
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Programacion semanal'),
                    'icon' => 'icon-time',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Usar horario'),
                        'name' => 'JOSRA_WAP_SCHEDULE_ENABLED',
                        'is_bool' => true,
                        'desc' => $this->l('Con el horario activo, el boton solo esta operativo en los dias y horas marcados abajo.'),
                        'values' => $this->getSwitchValues(),
                    ),
                    array(
                        'type' => 'html',
                        'label' => $this->l('Dias y horas'),
                        'name' => 'josra_schedule_grid',
                        'html_content' => $this->renderScheduleGrid($schedule),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Fuera de horario'),
                        'name' => 'JOSRA_WAP_OFFLINE_MODE',
                        'class' => 't josra-offline-mode',
                        'desc' => $this->l('Que hace el boton cuando la tienda esta cerrada.'),
                        'values' => array(
                            array(
                                'id' => 'offline_hide',
                                'value' => 'hide',
                                'label' => $this->l('Ocultarlo del todo'),
                            ),
                            array(
                                'id' => 'offline_show',
                                'value' => 'show',
                                'label' => $this->l('Mostrarlo en rojo (no abre WhatsApp)'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Mensaje fuera de horario'),
                        'name' => 'JOSRA_WAP_OFFLINE_MSG',
                        'placeholder' => $this->l('Ahora no estamos, dejanos tu mensaje'),
                        'form_group_class' => 'josra-offline-extra',
                        'desc' => $this->l('Se muestra junto al icono mientras la tienda esta cerrada. Dejalo vacio para mostrar solo el icono rojo.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Enlace fuera de horario'),
                        'name' => 'JOSRA_WAP_OFFLINE_URL',
                        'placeholder' => '/contactenos',
                        'form_group_class' => 'josra-offline-extra',
                        'desc' => $this->l('A donde lleva el boton mientras la tienda esta cerrada, por ejemplo la pagina de contacto. Dejalo vacio para que no se pueda pulsar.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Guardar')),
            ),
        );
    }

    protected function renderPreview()
    {
        $settings = $this->getSettings();

        $this->context->smarty->assign(array(
            'josra_module_dir' => $this->_path,
            'josra_prev_icon' => (int) $settings['JOSRA_WAP_ICON'],
            'josra_prev_size' => (int) $settings['JOSRA_WAP_ICON_SIZE'],
            'josra_prev_color' => (int) $settings['JOSRA_WAP_ICON_COLOR'],
            'josra_prev_scale' => (int) $settings['JOSRA_WAP_MOBILE_SCALE'],
            'josra_prev_size_mobile' => (int) round(
                $settings['JOSRA_WAP_ICON_SIZE'] * $settings['JOSRA_WAP_MOBILE_SCALE'] / 100
            ),
            'josra_prev_text' => $settings['JOSRA_WAP_ABOVE_TEXT'],
        ));

        return $this->fetchAdmin('preview.tpl');
    }

    protected function renderIconPicker()
    {
        $settings = $this->getSettings();

        $this->context->smarty->assign(array(
            'josra_module_dir' => $this->_path,
            'josra_icons' => array(1, 2, 3, 4),
            'josra_icon' => (int) $settings['JOSRA_WAP_ICON'],
            'josra_icon_color' => (int) $settings['JOSRA_WAP_ICON_COLOR'],
        ));

        return $this->fetchAdmin('icon-picker.tpl');
    }

    protected function renderScheduleGrid($schedule)
    {
        $this->context->smarty->assign(array(
            'josra_days' => $this->days,
            'josra_day_labels' => $this->getDayLabels(),
            'josra_schedule' => $schedule,
        ));

        return $this->fetchAdmin('schedule.tpl');
    }

    /** display() y setTemplate() varian entre versiones: ruta absoluta y a correr */
    protected function fetchAdmin($template)
    {
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/' . $template
        );
    }

    /* ==================================================================
     * Front office
     * ================================================================== */

    public function hookDisplayHeader($params)
    {
        $settings = $this->getSettings();

        if (!$settings['JOSRA_WAP_ENABLED'] || $settings['JOSRA_WAP_PHONE'] === '') {
            return '';
        }

        $this->context->controller->addCSS($this->_path . 'views/css/josrawhatsapp.css');
        $this->context->controller->addJS($this->_path . 'views/js/josrawhatsapp.js');

        return '';
    }

    /** PS 1.7, 8 y 9 */
    public function hookDisplayFooterBefore($params)
    {
        return $this->renderButton();
    }

    /** PS 1.6, que no tiene displayFooterBefore */
    public function hookDisplayFooter($params)
    {
        return $this->renderButton();
    }

    protected function renderButton()
    {
        if (self::$printed) {
            return '';
        }

        $settings = $this->getSettings();

        if (!$settings['JOSRA_WAP_ENABLED'] || $settings['JOSRA_WAP_PHONE'] === '') {
            return '';
        }

        $phone = preg_replace('/[^0-9]/', '', $settings['JOSRA_WAP_PHONE']);
        if ($phone === '') {
            return '';
        }

        $text = '';
        if ($settings['JOSRA_WAP_SEND_TEXT'] !== '') {
            $text = $settings['JOSRA_WAP_SEND_TEXT'];
        }
        if ($settings['JOSRA_WAP_SEND_URL'] == 1) {
            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            $text .= ($text !== '' ? ' ' : '') . Tools::getShopDomainSsl(true) . $uri;
        }

        $query = 'phone=' . $phone . ($text !== '' ? '&text=' . rawurlencode($text) : '');

        $isOnline = true;
        $offlineMsg = '';
        $offlineUrl = '';

        if ($settings['JOSRA_WAP_SCHEDULE_ENABLED'] == 1) {
            $isOnline = $this->isOnSchedule($this->getSchedule());

            if (!$isOnline) {
                // Fuera de horario: o se oculta, o se muestra en rojo
                if ($settings['JOSRA_WAP_OFFLINE_MODE'] !== 'show') {
                    return '';
                }

                $offlineMsg = $settings['JOSRA_WAP_OFFLINE_MSG'];
                $offlineUrl = $settings['JOSRA_WAP_OFFLINE_URL'];

                if ($offlineUrl !== '' && Tools::substr($offlineUrl, 0, 1) === '/') {
                    $offlineUrl = Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . ltrim($offlineUrl, '/');
                }
            }
        }

        self::$printed = true;

        $this->context->smarty->assign(array(
            'josra_module_dir' => $this->_path,
            'josra_settings' => $settings,
            'josra_url' => 'https://api.whatsapp.com/send?' . $query,
            'josra_mobile_url' => 'whatsapp://send?' . $query,
            'josra_is_online' => $isOnline,
            'josra_offline_msg' => $offlineMsg,
            'josra_offline_url' => $offlineUrl,
        ));

        return $this->display(__FILE__, 'views/templates/front/button.tpl');
    }

    /* ==================================================================
     * Horario
     * ================================================================== */

    /** Hora actual segun la zona horaria configurada en la tienda */
    public function getShopNow()
    {
        $tz = Configuration::get('PS_TIMEZONE');
        if (!$tz) {
            $tz = 'Europe/Madrid';
        }

        try {
            $now = new DateTime('now', new DateTimeZone($tz));
        } catch (Exception $e) {
            $now = new DateTime('now');
        }

        return $now;
    }

    public function isOnSchedule($schedule)
    {
        $dayMap = array(
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
        );

        $now = $this->getShopNow();
        $dayKey = $dayMap[(int) $now->format('w')];

        if (!isset($schedule[$dayKey]) || !$schedule[$dayKey]['enabled']) {
            return false;
        }

        $day = $schedule[$dayKey];
        $current = $now->format('H:i');

        if ($current >= $day['t1_from'] && $current <= $day['t1_to']) {
            return true;
        }

        if (!empty($day['t2_enabled'])
            && $current >= $day['t2_from']
            && $current <= $day['t2_to']
        ) {
            return true;
        }

        return false;
    }
}
