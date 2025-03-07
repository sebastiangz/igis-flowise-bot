<?php
/**
 * Plugin Name: IGIS Flowise Bot
 * Plugin URI: https://www.infraestructuragis.com/
 * Description: Integra el chatbot de Flowise en tu sitio WordPress con opciones configurables avanzadas
 * Version: 1.1.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: InfraestructuraGIS
 * Author URI: https://www.infraestructuragis.com/
 * License: GPL v2 or later
 * Text Domain: igis-flowise-bot
 */

if (!defined('ABSPATH')) {
    exit;
}

define('IGIS_BOT_VERSION', '1.1.1');
define('IGIS_BOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IGIS_BOT_PLUGIN_URL', plugin_dir_url(__FILE__));

class IGIS_Flowise_Bot {
    private static $instance = null;
    private $options;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->options = get_option('igis_bot_options', array());
        
        // Crear estructura de carpetas si no existe
        $this->create_directories();
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_footer', array($this, 'render_bot'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Registrar manejadores AJAX
        $this->register_ajax_handlers();
    }
    
    private function create_directories() {
        // Crear directorio de assets si no existe
        $assets_dir = IGIS_BOT_PLUGIN_DIR . 'assets';
        if (!file_exists($assets_dir)) {
            mkdir($assets_dir, 0755);
            mkdir($assets_dir . '/css', 0755);
            mkdir($assets_dir . '/js', 0755);
            
            // Copiar archivos CSS y JS iniciales
            $this->create_initial_assets();
        }
    }
    
    private function create_initial_assets() {
        // Crear archivo CSS de administración
        $admin_css = file_get_contents(IGIS_BOT_PLUGIN_DIR . 'admin-css.css');
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/css/admin.css', $admin_css);
        
        // Crear archivo JS de administración
        $admin_js = file_get_contents(IGIS_BOT_PLUGIN_DIR . 'admin-js.js');
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/admin.js', $admin_js);
        
        // Crear archivo CSS del frontend
        $frontend_css = "/* IGIS Flowise Bot Frontend Styles */
.flowise-chatbot-button {
    transition: all 0.3s ease;
}";
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/css/frontend.css', $frontend_css);
        
        // Crear archivo JS del frontend
        $frontend_js = "jQuery(document).ready(function($) {
    // Generador de IDs de sesión
    function generateSessionId() {
        return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    }
    
    // Obtener ID de sesión existente o crear uno nuevo
    const sessionId = localStorage.getItem('igis_bot_session_id') || generateSessionId();
    localStorage.setItem('igis_bot_session_id', sessionId);
    
    // Registrar el inicio de la conversación cuando se abra el chatbot
    $(document).on('click', '.flowise-chatbot-button', function() {
        $.ajax({
            url: igisBotFrontend.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_log_conversation',
                nonce: igisBotFrontend.nonce,
                session_id: sessionId,
                status: 'active'
            }
        });
    });
});";
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/frontend.js', $frontend_js);
    }

    public function activate() {
        $default_options = array(
            // Configuración General
            'chatflow_id' => '',
            'api_host' => '',
            
            // Configuración del Botón
            'button_color' => '#3B81F6',
            'button_position_right' => '20',
            'button_position_bottom' => '20',
            'button_size' => '48',
            'enable_drag' => true,
            'icon_color' => 'white',
            'custom_icon' => 'https://raw.githubusercontent.com/walkxcode/dashboard-icons/main/svg/google-messages.svg',
            
            // Configuración de la Ventana
            'window_title' => 'IGIS Bot',
            'welcome_message' => 'Hello! How can I help you today?',
            'error_message' => 'Lo siento, ha ocurrido un error. Por favor, intenta de nuevo.',
            'window_height' => '700',
            'window_width' => '400',
            'window_background_color' => '#ffffff',
            'window_background_image' => '',
            'font_size' => '16',
            
            // Configuración de Mensajes
            'bot_message_bg_color' => '#f7f8ff',
            'bot_message_text_color' => '#303235',
            'bot_avatar_enabled' => true,
            'bot_avatar_src' => '',
            'user_message_bg_color' => '#3B81F6',
            'user_message_text_color' => '#ffffff',
            'user_avatar_enabled' => true,
            'user_avatar_src' => '',

            // Configuración del Input
            'input_placeholder' => 'Type your question',
            'input_bg_color' => '#ffffff',
            'input_text_color' => '#303235',
            'input_send_button_color' => '#3B81F6',
            'max_chars' => '50',
            'max_chars_warning' => 'You exceeded the characters limit.',
            'auto_focus' => true,
            
            // Configuración de Visualización
            'display_pages' => array('all'),
            'auto_open' => true,
            'auto_open_delay' => '2',
            'auto_open_mobile' => false,
            'show_for_logged_in' => false,
            'show_for_roles' => array(),
            'hide_on_mobile' => false,
            
            // Configuración del Tooltip
            'show_tooltip' => true,
            'tooltip_message' => 'Hi There 👋!',
            'tooltip_bg_color' => 'black',
            'tooltip_text_color' => 'white',
            'tooltip_font_size' => '16',
            
            // Prompts de Inicio
            'starter_prompts' => "What is a bot?\nWho are you?",
            'starter_prompt_font_size' => '15',
            
            // Configuración de Sonido
            'enable_send_sound' => true,
            'enable_receive_sound' => true,
            'send_sound_url' => '',
            'receive_sound_url' => '',
            
            // Configuración del Footer
            'footer_text_color' => '#303235',
            'footer_text' => 'Powered by IGIS Bot',
            'footer_company' => 'InfraestructuraGIS',
            'footer_company_link' => 'https://www.infraestructuragis.com/',
            
            // Configuración del Disclaimer
            'show_disclaimer' => false,
            'disclaimer_title' => 'Disclaimer',
            'disclaimer_message' => '',
            'disclaimer_button_text' => 'Start Chatting',
            'disclaimer_button_color' => '#3b82f6',
            'disclaimer_text_color' => 'black',
            'disclaimer_bg_color' => 'white',
            'disclaimer_overlay_color' => 'rgba(0, 0, 0, 0.4)',
            
            // Configuración Avanzada
            'custom_css' => '',
            'custom_js' => '',
            'custom_headers' => '',
            'rate_limiting' => 60,
            'session_timeout' => 30,
            'debug_mode' => false,
            'save_conversations' => false,
            'analytics_enabled' => false,
            'analytics_tracking_id' => '',
            'webhook_url' => '',
            'webhook_events' => array()
        );
        
        if (!get_option('igis_bot_options')) {
            add_option('igis_bot_options', $default_options);
        }
        
        // Crear tablas de base de datos
        $this->create_database_tables();
    }
    
    private function create_database_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla de conversaciones
        $table_name = $wpdb->prefix . 'igis_bot_conversations';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(32) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            started_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ended_at datetime DEFAULT NULL,
            metadata text DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY session_id (session_id)
        ) $charset_collate;";
        
        // Tabla de mensajes
        $messages_table = $wpdb->prefix . 'igis_bot_messages';
        $sql .= "CREATE TABLE IF NOT EXISTS $messages_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            conversation_id bigint(20) NOT NULL,
            message text NOT NULL,
            type varchar(10) NOT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            metadata text DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY conversation_id (conversation_id),
            FOREIGN KEY (conversation_id) REFERENCES $table_name(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // Tabla de analytics
        $analytics_table = $wpdb->prefix . 'igis_bot_analytics';
        $sql .= "CREATE TABLE IF NOT EXISTS $analytics_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data text DEFAULT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY event_type (event_type)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function deactivate() {
        // Limpieza al desactivar si es necesario
        if (get_option('igis_bot_delete_data')) {
            delete_option('igis_bot_options');
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            'IGIS Flowise Bot',
            'IGIS Bot',
            'manage_options',
            'igis-flowise-bot',
            array($this, 'render_admin_page'),
            'dashicons-format-chat',
            99
        );

        // Submenús
        add_submenu_page(
            'igis-flowise-bot',
            'Configuración General',
            'Configuración',
            'manage_options',
            'igis-flowise-bot'
        );

        add_submenu_page(
            'igis-flowise-bot',
            'Estadísticas',
            'Estadísticas',
            'manage_options',
            'igis-flowise-bot-stats',
            array($this, 'render_stats_page')
        );

        add_submenu_page(
            'igis-flowise-bot',
            'Conversaciones',
            'Conversaciones',
            'manage_options',
            'igis-flowise-bot-conversations',
            array($this, 'render_conversations_page')
        );
    }

    public function register_settings() {
        register_setting('igis_bot_options', 'igis_bot_options', array($this, 'sanitize_options'));
        
        // Secciones principales para cada pestaña
        $sections = array(
            'general' => 'Configuración General',
            'appearance' => 'Apariencia',
            'messages' => 'Mensajes',
            'display' => 'Visualización',
            'advanced' => 'Configuración Avanzada'
        );
        
        // Registrar secciones para cada pestaña
        foreach ($sections as $id => $title) {
            add_settings_section(
                'igis_bot_' . $id,
                $title,
                null,
                'igis-flowise-bot'
            );
        }
        
        $this->add_settings_fields();
    }

    private function add_settings_fields() {
        // Arrays de configuración de campos
        $general_fields = array(
            'chatflow_id' => array('text', 'Chatflow ID'),
            'api_host' => array('text', 'API Host')
        );

        $appearance_fields = array(
            'button_color' => array('color', 'Color del Botón'),
            'button_position_right' => array('number', 'Posición Derecha'),
            'button_position_bottom' => array('number', 'Posición Inferior'),
            'button_size' => array('number', 'Tamaño del Botón'),
            'enable_drag' => array('checkbox', 'Permitir Arrastrar'),
            'icon_color' => array('color', 'Color del Icono'),
            'custom_icon' => array('media', 'Icono Personalizado')
        );

        $message_fields = array(
            'bot_message_bg_color' => array('color', 'Color de Fondo Bot'),
            'bot_message_text_color' => array('color', 'Color de Texto Bot'),
            'bot_avatar_enabled' => array('checkbox', 'Mostrar Avatar Bot'),
            'bot_avatar_src' => array('media', 'Avatar del Bot'),
            'user_message_bg_color' => array('color', 'Color de Fondo Usuario'),
            'user_message_text_color' => array('color', 'Color de Texto Usuario'),
            'user_avatar_enabled' => array('checkbox', 'Mostrar Avatar Usuario'),
            'user_avatar_src' => array('media', 'Avatar del Usuario')
        );

        $display_fields = array(
            'display_pages' => array('multiselect', 'Mostrar en Páginas', $this->get_available_pages()),
            'auto_open' => array('checkbox', 'Auto Abrir'),
            'auto_open_delay' => array('number', 'Retraso de Auto Apertura'),
            'auto_open_mobile' => array('checkbox', 'Auto Abrir en Móvil'),
            'show_for_logged_in' => array('checkbox', 'Solo Usuarios Registrados'),
            'show_for_roles' => array('multiselect', 'Roles de Usuario', $this->get_user_roles()),
            'hide_on_mobile' => array('checkbox', 'Ocultar en Móvil')
        );

        $advanced_fields = array(
            'custom_css' => array('code', 'CSS Personalizado'),
            'custom_js' => array('code', 'JavaScript Personalizado'),
            'rate_limiting' => array('number', 'Límite de Peticiones'),
            'session_timeout' => array('number', 'Tiempo de Sesión'),
            'debug_mode' => array('checkbox', 'Modo Debug'),
            'save_conversations' => array('checkbox', 'Guardar Conversaciones'),
            'analytics_enabled' => array('checkbox', 'Activar Analytics'),
            'analytics_tracking_id' => array('text', 'ID de Analytics'),
            'webhook_url' => array('text', 'URL del Webhook'),
            'webhook_events' => array('multiselect', 'Eventos del Webhook', array(
                'conversation_start' => 'Inicio de Conversación',
                'conversation_end' => 'Fin de Conversación',
                'message_sent' => 'Mensaje Enviado',
                'message_received' => 'Mensaje Recibido'
            ))
        );

        // Registrar todos los campos
        $all_fields = array(
            'general' => $general_fields,
            'appearance' => $appearance_fields,
            'messages' => $message_fields,
            'display' => $display_fields,
            'advanced' => $advanced_fields
        );

        foreach ($all_fields as $section => $fields) {
            foreach ($fields as $field => $config) {
                add_settings_field(
                    $field,
                    $config[1],
                    array($this, 'render_field'),
                    'igis-flowise-bot',
                    'igis_bot_' . $section,
                    array(
                        'field' => $field,
                        'type' => $config[0],
                        'options' => isset($config[2]) ? $config[2] : null
                    )
                );
            }
        }
    }

    public function render_field($args) {
        $field = $args['field'];
        $type = $args['type'];
        $options = isset($args['options']) ? $args['options'] : null;
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        
        switch ($type) {
            case 'text':
                echo '<input type="text" class="regular-text" name="igis_bot_options[' . esc_attr($field) . ']" value="' . esc_attr($value) . '">';
                break;

            case 'number':
                echo '<input type="number" class="small-text" name="igis_bot_options[' . esc_attr($field) . ']" value="' . esc_attr($value) . '">';
                break;

            case 'color':
                echo '<input type="text" class="color-picker" name="igis_bot_options[' . esc_attr($field) . ']" value="' . esc_attr($value) . '">';
                break;

            case 'checkbox':
                echo '<input type="checkbox" name="igis_bot_options[' . esc_attr($field) . ']" ' . checked($value, true, false) . ' value="1">';
                break;

            case 'media':
                echo '<div class="media-field">';
                echo '<input type="text" class="regular-text media-input" name="igis_bot_options[' . esc_attr($field) . ']" value="' . esc_attr($value) . '">';
                echo '<button class="button upload-media-button" data-target="' . esc_attr($field) . '">Seleccionar Archivo</button>';
                if (!empty($value)) {
                    echo '<div class="media-preview">';
                    if (wp_attachment_is_image($value)) {
                        echo wp_get_attachment_image($value, 'thumbnail');
                    } else {
                        echo '<img src="' . esc_url($value) . '" alt="Preview" style="max-width:100px; max-height:100px;">';
                    }
                    echo '</div>';
                }
                echo '</div>';
                break;

            case 'multiselect':
                if ($options) {
                    echo '<select multiple class="regular-text" name="igis_bot_options[' . esc_attr($field) . '][]">';
                    foreach ($options as $option_value => $option_label) {
                        $selected = is_array($value) && in_array($option_value, $value) ? 'selected' : '';
                        echo '<option value="' . esc_attr($option_value) . '" ' . $selected . '>' . esc_html($option_label) . '</option>';
                    }
                    echo '</select>';
                }
                break;

            case 'code':
                echo '<textarea class="large-text code" rows="10" name="igis_bot_options[' . esc_attr($field) . ']">' . esc_textarea($value) . '</textarea>';
                break;

            case 'textarea':
                echo '<textarea class="large-text" rows="5" name="igis_bot_options[' . esc_attr($field) . ']">' . esc_textarea($value) . '</textarea>';
                break;
        }
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap igis-bot-admin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="notice notice-info">
                <p>
                    <?php _e('Configure su bot IGIS Flowise aquí. Asegúrese de tener un ID de Chatflow válido y una URL de API configurada.', 'igis-flowise-bot'); ?>
                </p>
            </div>

            <form action="options.php" method="post" id="igis-bot-settings-form">
                <nav class="nav-tab-wrapper">
                    <a href="#general" class="nav-tab nav-tab-active"><?php _e('General', 'igis-flowise-bot'); ?></a>
                    <a href="#appearance" class="nav-tab"><?php _e('Apariencia', 'igis-flowise-bot'); ?></a>
                    <a href="#messages" class="nav-tab"><?php _e('Mensajes', 'igis-flowise-bot'); ?></a>
                    <a href="#display" class="nav-tab"><?php _e('Visualización', 'igis-flowise-bot'); ?></a>
                    <a href="#advanced" class="nav-tab"><?php _e('Avanzado', 'igis-flowise-bot'); ?></a>
                </nav>

                <div class="tab-content">
                    <!-- Secciones de configuración -->
                    <div id="section-general" class="settings-section active">
                        <?php 
                        settings_fields('igis_bot_options');
                        do_settings_sections('igis-flowise-bot'); 
                        ?>
                    </div>

                    <div class="submit-container">
                        <?php submit_button('Guardar Cambios'); ?>
                        <button type="button" class="button button-secondary preview-bot">
                            <?php _e('Vista Previa del Bot', 'igis-flowise-bot'); ?>
                        </button>
                    </div>
                </div>
            </form>

            <div id="bot-preview" class="bot-preview" style="display: none;">
                <div class="bot-preview-header">
                    <h3><?php _e('Vista Previa del Bot', 'igis-flowise-bot'); ?></h3>
                    <button class="close-preview">&times;</button>
                </div>
                <div class="bot-preview-content">
                    <!-- La vista previa del bot se cargará aquí -->
                </div>
            </div>
        </div>
        <?php
    }

    public function render_stats_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap igis-bot-stats-wrap">
            <h1><?php _e('Estadísticas del Bot', 'igis-flowise-bot'); ?></h1>
            
            <div class="stats-container">
                <div class="stats-card">
                    <h3><?php _e('Conversaciones Totales', 'igis-flowise-bot'); ?></h3>
                    <div class="stats-value"><?php echo $this->get_total_conversations(); ?></div>
                </div>
                
                <div class="stats-card">
                    <h3><?php _e('Mensajes Enviados', 'igis-flowise-bot'); ?></h3>
                    <div class="stats-value"><?php echo $this->get_total_messages(); ?></div>
                </div>
                
                <div class="stats-card">
                    <h3><?php _e('Tasa de Respuesta', 'igis-flowise-bot'); ?></h3>
                    <div class="stats-value"><?php echo $this->get_response_rate(); ?>%</div>
                </div>
            </div>

            <div class="stats-charts">
                <div class="chart-container">
                    <canvas id="conversationsChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="messagesChart"></canvas>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_conversations_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap igis-bot-conversations-wrap">
            <h1><?php _e('Historial de Conversaciones', 'igis-flowise-bot'); ?></h1>
            
            <div class="tablenav top">
                <div class="alignleft actions">
                    <select name="filter_date">
                        <option value="today"><?php _e('Hoy', 'igis-flowise-bot'); ?></option>
                        <option value="yesterday"><?php _e('Ayer', 'igis-flowise-bot'); ?></option>
                        <option value="last_week"><?php _e('Última Semana', 'igis-flowise-bot'); ?></option>
                        <option value="last_month"><?php _e('Último Mes', 'igis-flowise-bot'); ?></option>
                    </select>
                    <button class="button" id="filter-conversations">Filtrar</button>
                </div>
                <div class="tablenav-pages">
                    <!-- Paginación -->
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col"><?php _e('ID', 'igis-flowise-bot'); ?></th>
                        <th scope="col"><?php _e('Fecha', 'igis-flowise-bot'); ?></th>
                        <th scope="col"><?php _e('Usuario', 'igis-flowise-bot'); ?></th>
                        <th scope="col"><?php _e('Mensajes', 'igis-flowise-bot'); ?></th>
                        <th scope="col"><?php _e('Estado', 'igis-flowise-bot'); ?></th>
                        <th scope="col"><?php _e('Acciones', 'igis-flowise-bot'); ?></th>
                    </tr>
                </thead>
                <tbody id="conversations-list">
                    <!-- Los datos se cargarán vía AJAX -->
                </tbody>
            </table>
        </div>
        <?php
    }

    public function register_ajax_handlers() {
        // Handlers para el frontend
        add_action('wp_ajax_igis_bot_log_conversation', array($this, 'log_conversation'));
        add_action('wp_ajax_nopriv_igis_bot_log_conversation', array($this, 'log_conversation'));
        
        add_action('wp_ajax_igis_bot_log_message', array($this, 'log_message'));
        add_action('wp_ajax_nopriv_igis_bot_log_message', array($this, 'log_message'));
        
        // Handlers para el admin
        add_action('wp_ajax_igis_bot_get_conversations', array($this, 'get_conversations'));
        add_action('wp_ajax_igis_bot_get_conversation_details', array($this, 'get_conversation_details'));
        add_action('wp_ajax_igis_bot_delete_conversation', array($this, 'delete_conversation'));
        add_action('wp_ajax_igis_bot_get_stats', array($this, 'get_stats'));
    }
    
    public function log_conversation() {
        if (!check_ajax_referer('igis_bot_frontend', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }
        
        if (!isset($this->options['save_conversations']) || !$this->options['save_conversations']) {
            wp_send_json_success('Logging disabled');
            return;
        }
        
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'active';
        
        global $wpdb;
        $table = $wpdb->prefix . 'igis_bot_conversations';
        
        // Comprobar si ya existe una conversación con este session_id
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE session_id = %s",
            $session_id
        ));
        
        if ($existing) {
            // Actualizar conversación existente
            $wpdb->update(
                $table,
                array('status' => $status, 'ended_at' => current_time('mysql')),
                array('session_id' => $session_id)
            );
            $conversation_id = $existing;
        } else {
            // Crear nueva conversación
            $wpdb->insert(
                $table,
                array(
                    'user_id' => get_current_user_id(),
                    'session_id' => $session_id,
                    'status' => $status,
                    'started_at' => current_time('mysql')
                )
            );
            $conversation_id = $wpdb->insert_id;
        }
        
        // Registrar evento de analytics si está habilitado
        if (isset($this->options['analytics_enabled']) && $this->options['analytics_enabled']) {
            $this->log_analytics_event('conversation_' . ($existing ? 'continued' : 'started'), array(
                'conversation_id' => $conversation_id,
                'session_id' => $session_id
            ));
        }
        
        wp_send_json_success(array('conversation_id' => $conversation_id));
    }
    
    public function log_message() {
        if (!check_ajax_referer('igis_bot_frontend', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }
        
        if (!isset($this->options['save_conversations']) || !$this->options['save_conversations']) {
            wp_send_json_success('Logging disabled');
            return;
        }
        
        $conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'user';
        
        if (empty($conversation_id) || empty($message)) {
            wp_send_json_error('Missing required fields');
            return;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'igis_bot_messages';
        
        $wpdb->insert(
            $table,
            array(
                'conversation_id' => $conversation_id,
                'message' => $message,
                'type' => $type,
                'timestamp' => current_time('mysql')
            )
        );
        
        // Registrar evento de analytics si está habilitado
        if (isset($this->options['analytics_enabled']) && $this->options['analytics_enabled']) {
            $this->log_analytics_event('message_' . $type, array(
                'conversation_id' => $conversation_id,
                'message_length' => strlen($message)
            ));
        }
        
        wp_send_json_success(array('message_id' => $wpdb->insert_id));
    }
    
    public function get_conversations() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        if (!check_ajax_referer('igis_bot_admin', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'igis_bot_conversations';
        $messages_table = $wpdb->prefix . 'igis_bot_messages';
        
        // Filtrado por fecha
        $date_filter = isset($_POST['date_filter']) ? sanitize_text_field($_POST['date_filter']) : 'all';
        $where = '';
        
        switch ($date_filter) {
            case 'today':
                $where = "WHERE DATE(started_at) = CURDATE()";
                break;
            case 'yesterday':
                $where = "WHERE DATE(started_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'last_week':
                $where = "WHERE started_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last_month':
                $where = "WHERE started_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
        }
        
        // Paginación
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        // Obtener conversaciones
        $conversations = $wpdb->get_results(
            "SELECT c.*, 
            COUNT(m.id) as message_count,
            (SELECT user_login FROM {$wpdb->users} WHERE ID = c.user_id) as username
            FROM $table c
            LEFT JOIN $messages_table m ON c.id = m.conversation_id
            $where
            GROUP BY c.id
            ORDER BY c.started_at DESC
            LIMIT $offset, $per_page"
        );
        
        // Obtener total de conversaciones para paginación
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table $where");
        
        wp_send_json_success(array(
            'conversations' => $conversations,
            'total' => $total,
            'pages' => ceil($total / $per_page)
        ));
    }
    
    public function get_conversation_details() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        if (!check_ajax_referer('igis_bot_admin', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }
        
        $conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
        
        if (empty($conversation_id)) {
            wp_send_json_error('Invalid conversation ID');
        }
        
        global $wpdb;
        $conversations_table = $wpdb->prefix . 'igis_bot_conversations';
        $messages_table = $wpdb->prefix . 'igis_bot_messages';
        
        // Obtener detalles de la conversación
        $conversation = $wpdb->get_row($wpdb->prepare(
            "SELECT c.*, 
            (SELECT user_login FROM {$wpdb->users} WHERE ID = c.user_id) as username
            FROM $conversations_table c
            WHERE c.id = %d",
            $conversation_id
        ));
        
        if (!$conversation) {
            wp_send_json_error('Conversation not found');
        }
        
        // Obtener mensajes
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $messages_table 
            WHERE conversation_id = %d 
            ORDER BY timestamp ASC",
            $conversation_id
        ));
        
        wp_send_json_success(array(
            'conversation' => $conversation,
            'messages' => $messages
        ));
    }
    
    public function delete_conversation() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        if (!check_ajax_referer('igis_bot_admin', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }
        
        $conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
        
        if (empty($conversation_id)) {
            wp_send_json_error('Invalid conversation ID');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'igis_bot_conversations';
        
        // Eliminar la conversación (los mensajes se eliminarán en cascada)
        $deleted = $wpdb->delete($table, array('id' => $conversation_id));
        
        if ($deleted) {
            wp_send_json_success('Conversation deleted');
        } else {
            wp_send_json_error('Failed to delete conversation');
        }
    }
    
    public function get_stats() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        if (!check_ajax_referer('igis_bot_admin', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }
        
        $stats = array(
            'total_conversations' => $this->get_total_conversations(),
            'total_messages' => $this->get_total_messages(),
            'response_rate' => $this->get_response_rate(),
            'conversations_chart' => $this->get_conversations_chart_data(),
            'messages_chart' => $this->get_messages_chart_data()
        );
        
        wp_send_json_success($stats);
    }
    
    private function log_analytics_event($event_type, $data = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'igis_bot_analytics';
        
        $wpdb->insert(
            $table,
            array(
                'event_type' => $event_type,
                'event_data' => json_encode($data),
                'timestamp' => current_time('mysql')
            )
        );
        
        // Enviar a webhook si está configurado
        $this->send_webhook_event($event_type, $data);
    }
    
    private function send_webhook_event($event_type, $data) {
        if (empty($this->options['webhook_url']) || empty($this->options['webhook_events'])) {
            return false;
        }
        
        // Verificar si este tipo de evento está habilitado para webhooks
        if (!in_array($event_type, $this->options['webhook_events'])) {
            return false;
        }
        
        $payload = array(
            'event' => $event_type,
            'timestamp' => current_time('mysql'),
            'data' => $data
        );
        
        $response = wp_remote_post($this->options['webhook_url'], array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => false,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode($payload)
        ));
        
        return true;
    }
    
    private function get_conversations_chart_data() {
        global $wpdb;
        $table = $wpdb->prefix . 'igis_bot_conversations';
        
        // Datos de los últimos 30 días
        $data = array();
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE DATE(started_at) = %s",
                $date
            ));
            
            $data[] = array(
                'date' => $date,
                'count' => (int)$count
            );
        }
        
        return $data;
