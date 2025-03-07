<?php
/**
 * Plugin Name: IGIS Flowise Bot
 * Plugin URI: https://www.infraestructuragis.com/
 * Description: Integra el chatbot de Flowise en tu sitio WordPress con opciones configurables avanzadas
 * Version: 1.1.0
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

define('IGIS_BOT_VERSION', '1.1.0');
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
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_footer', array($this, 'render_bot'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
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
        
        // Secciones principales
        $sections = array(
            'general' => 'Configuración General',
            'appearance' => 'Apariencia',
            'messages' => 'Mensajes',
            'display' => 'Visualización',
            'advanced' => 'Configuración Avanzada'
        );
        
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
                    <?php
                    settings_fields('igis_bot_options');
                    do_settings_sections('igis-flowise-bot');
                    ?>

                    <div class="submit-container">
                        <?php submit_button('Guardar Cambios'); ?>
                        <button type="button" class="button button-secondary preview-bot">
                            <?php _e('Vista Previa del Bot', 'igis-flowise-bot'); ?>
                        </button>
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

    public function render_bot() {
        if (!$this->should_display_bot()) {
            return;
        }
        
        $options = $this->get_sanitized_options();
        ?>
        <script type="module">
            import Chatbot from "https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js"
            Chatbot.init({
                chatflowid: "<?php echo esc_js($options['chatflow_id']); ?>",
                apiHost: "<?php echo esc_js($options['api_host']); ?>",
                theme: {
                    button: {
                        backgroundColor: "<?php echo esc_js($options['button_color']); ?>",
                        right: <?php echo (int)$options['button_position_right']; ?>,
                        bottom: <?php echo (int)$options['button_position_bottom']; ?>,
                        size: <?php echo (int)$options['button_size']; ?>,
                        dragAndDrop: <?php echo $options['enable_drag'] ? 'true' : 'false'; ?>,
                        iconColor: "<?php echo esc_js($options['icon_color']); ?>",
                        customIconSrc: "<?php echo esc_js($options['custom_icon']); ?>",
                        autoWindowOpen: {
                            autoOpen: <?php echo $options['auto_open'] ? 'true' : 'false'; ?>,
                            openDelay: <?php echo (int)$options['auto_open_delay']; ?>,
                            autoOpenOnMobile: <?php echo $options['auto_open_mobile'] ? 'true' : 'false'; ?>
                        }
                    },
                    tooltip: {
                        showTooltip: <?php echo $options['show_tooltip'] ? 'true' : 'false'; ?>,
                        tooltipMessage: "<?php echo esc_js($options['tooltip_message']); ?>",
                        tooltipBackgroundColor: "<?php echo esc_js($options['tooltip_bg_color']); ?>",
                        tooltipTextColor: "<?php echo esc_js($options['tooltip_text_color']); ?>",
                        tooltipFontSize: <?php echo (int)$options['tooltip_font_size']; ?>
                    },
                    chatWindow: {
                        showTitle: true,
                        title: "<?php echo esc_js($options['window_title']); ?>",
                        welcomeMessage: "<?php echo esc_js($options['welcome_message']); ?>",
                        errorMessage: "<?php echo esc_js($options['error_message']); ?>",
                        backgroundColor: "<?php echo esc_js($options['window_background_color']); ?>",
                        height: <?php echo (int)$options['window_height']; ?>,
                        width: <?php echo (int)$options['window_width']; ?>,
                        fontSize: <?php echo (int)$options['font_size']; ?>,
                        starterPrompts: <?php echo json_encode(array_map('trim', explode("\n", $options['starter_prompts']))); ?>,
                        botMessage: {
                            backgroundColor: "<?php echo esc_js($options['bot_message_bg_color']); ?>",
                            textColor: "<?php echo esc_js($options['bot_message_text_color']); ?>",
                            showAvatar: <?php echo $options['bot_avatar_enabled'] ? 'true' : 'false'; ?>,
                            avatarSrc: "<?php echo esc_js($options['bot_avatar_src']); ?>"
                        },
                        userMessage: {
                            backgroundColor: "<?php echo esc_js($options['user_message_bg_color']); ?>",
                            textColor: "<?php echo esc_js($options['user_message_text_color']); ?>",
                            showAvatar: <?php echo $options['user_avatar_enabled'] ? 'true' : 'false'; ?>,
                            avatarSrc: "<?php echo esc_js($options['user_avatar_src']); ?>"
                        },
                        textInput: {
                            placeholder: "<?php echo esc_js($options['input_placeholder']); ?>",
                            backgroundColor: "<?php echo esc_js($options['input_bg_color']); ?>",
                            textColor: "<?php echo esc_js($options['input_text_color']); ?>",
                            sendButtonColor: "<?php echo esc_js($options['input_send_button_color']); ?>",
                            maxChars: <?php echo (int)$options['max_chars']; ?>,
                            maxCharsWarningMessage: "<?php echo esc_js($options['max_chars_warning']); ?>",
                            autoFocus: <?php echo $options['auto_focus'] ? 'true' : 'false'; ?>,
                            sendMessageSound: <?php echo $options['enable_send_sound'] ? 'true' : 'false'; ?>,
                            sendSoundLocation: "<?php echo esc_js($options['send_sound_url']); ?>",
                            receiveMessageSound: <?php echo $options['enable_receive_sound'] ? 'true' : 'false'; ?>",
                            receiveSoundLocation: "<?php echo esc_js($options['receive_sound_url']); ?>"
                        },
                        footer: {
                            textColor: "<?php echo esc_js($options['footer_text_color']); ?>",
                            text: "<?php echo esc_js($options['footer_text']); ?>",
                            company: "<?php echo esc_js($options['footer_company']); ?>",
                            companyLink: "<?php echo esc_js($options['footer_company_link']); ?>"
                        }
                    },
                    disclaimer: {
                        show: <?php echo $options['show_disclaimer'] ? 'true' : 'false'; ?>,
                        title: "<?php echo esc_js($options['disclaimer_title']); ?>",
                        message: "<?php echo esc_js($options['disclaimer_message']); ?>",
                        buttonText: "<?php echo esc_js($options['disclaimer_button_text']); ?>",
                        buttonColor: "<?php echo esc_js($options['disclaimer_button_color']); ?>",
                        textColor: "<?php echo esc_js($options['disclaimer_text_color']); ?>",
                        backgroundColor: "<?php echo esc_js($options['disclaimer_bg_color']); ?>",
                        blurredBackgroundColor: "<?php echo esc_js($options['disclaimer_overlay_color']); ?>"
                    }
                }
            });

            <?php if ($options['debug_mode']): ?>
            console.log('IGIS Bot Debug Mode:', {
                version: '<?php echo IGIS_BOT_VERSION; ?>',
                options: <?php echo json_encode($options); ?>
            });
            <?php endif; ?>
        </script>

        <?php if (!empty($options['custom_css'])): ?>
        <style type="text/css">
            <?php echo wp_strip_all_tags($options['custom_css']); ?>
        </style>
        <?php endif; ?>

        <?php if (!empty($options['custom_js'])): ?>
        <script type="text/javascript">
            <?php echo $options['custom_js']; ?>
        </script>
        <?php endif; ?>
        <?php
    }

    private function should_display_bot() {
        $options = $this->options;
        
        // Verificar si estamos en modo móvil y está deshabilitado para móviles
        if ($options['hide_on_mobile'] && wp_is_mobile()) {
            return false;
        }

        // Verificar restricciones de usuario
        if ($options['show_for_logged_in'] && !is_user_logged_in()) {
            return false;
        }

        // Verificar roles de usuario
        if (!empty($options['show_for_roles']) && is_user_logged_in()) {
            $user = wp_get_current_user();
            $intersect = array_intersect($options['show_for_roles'], $user->roles);
            if (empty($intersect)) {
                return false;
            }
        }

        // Verificar páginas de visualización
        $display_pages = isset($options['display_pages']) ? $options['display_pages'] : array('all');
        
        if (in_array('all', $display_pages)) {
            return true;
        }

        if (is_page() && in_array(get_the_ID(), $display_pages)) {
            return true;
        }

        if (is_single() && in_array('posts', $display_pages)) {
            return true;
        }

        if (is_archive() && in_array('archives', $display_pages)) {
            return true;
        }

        if (is_home() && in_array('blog', $display_pages)) {
            return true;
        }

        if (is_front_page() && in_array('front_page', $display_pages)) {
            return true;
        }

        return false;
    }

    private function get_available_pages() {
        $pages = get_pages();
        $options = array(
            'all' => __('Todas las páginas', 'igis-flowise-bot'),
            'front_page' => __('Página de inicio', 'igis-flowise-bot'),
            'blog' => __('Página del blog', 'igis-flowise-bot'),
            'posts' => __('Entradas individuales', 'igis-flowise-bot'),
            'archives' => __('Páginas de archivo', 'igis-flowise-bot')
        );

        foreach ($pages as $page) {
            $options[$page->ID] = $page->post_title;
        }

        return $options;
    }

    private function get_user_roles() {
        $roles = wp_roles()->get_names();
        return array_combine(array_keys($roles), array_values($roles));
    }

    private function get_total_conversations() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'igis_bot_conversations';
        return $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }

    private function get_total_messages() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'igis_bot_messages';
        return $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }

    private function get_response_rate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'igis_bot_conversations';
        $total = $this->get_total_conversations();
        if ($total === 0) return 0;
        
        $responded = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'completed'");
        return round(($responded / $total) * 100, 2);
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_igis-flowise-bot' !== $hook) {
            return;
        }

        // Color Picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Media Uploader
        wp_enqueue_media();
        
        // Custom Admin Styles
        wp_enqueue_style(
            'igis-bot-admin',
            IGIS_BOT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            IGIS_BOT_VERSION
        );
        
        // Chart.js para estadísticas
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js',
            array(),
            '3.7.0',
            true
        );
        
        // Custom Admin Scripts
        wp_enqueue_script(
            'igis-bot-admin',
            IGIS_BOT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker', 'chartjs'),
            IGIS_BOT_VERSION,
            true
        );

        // Localize script
        wp_localize_script('igis-bot-admin', 'igisBotAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('igis_bot_admin'),
            'strings' => array(
                'confirmDelete' => __('¿Está seguro de que desea eliminar esta conversación?', 'igis-flowise-bot'),
                'errorLoading' => __('Error al cargar los datos', 'igis-flowise-bot'),
                'saveSuccess' => __('Configuración guardada correctamente', 'igis-flowise-bot'),
                'saveError' => __('Error al guardar la configuración', 'igis-flowise-bot')
            )
        ));
    }

    public function enqueue_frontend_assets() {
        if (!$this->should_display_bot()) {
            return;
        }

        // Estilos del front-end
        wp_enqueue_style(
            'igis-bot-frontend',
            IGIS_BOT_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            IGIS_BOT_VERSION
        );

        // Scripts del front-end
        wp_enqueue_script(
            'igis-bot-frontend',
            IGIS_BOT_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            IGIS_BOT_VERSION,
            true
        );

        // Localizar script
        wp_localize_script('igis-bot-frontend', 'igisBotFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('igis_bot_frontend'),
            'strings' => array(
                'errorMessage' => $this->options['error_message']
            )
        ));
    }

    public function sanitize_options($input) {
        $sanitized = array();
        
        foreach ($input as $key => $value) {
            switch ($key) {
                // Campos de texto simples
                case 'chatflow_id':
                case 'window_title':
                case 'welcome_message':
                case 'error_message':
                case 'input_placeholder':
                case 'max_chars_warning':
                case 'footer_text':
                case 'footer_company':
                case 'disclaimer_title':
                case 'disclaimer_message':
                case 'disclaimer_button_text':
                    $sanitized[$key] = sanitize_text_field($value);
                    break;

                // URLs
                case 'api_host':
                case 'custom_icon':
                case 'bot_avatar_src':
                case 'user_avatar_src':
                case 'send_sound_url':
                case 'receive_sound_url':
                case 'footer_company_link':
                case 'webhook_url':
                    $sanitized[$key] = esc_url_raw($value);
                    break;

                // Colores
                case 'button_color':
                case 'icon_color':
                case 'window_background_color':
                case 'bot_message_bg_color':
                case 'bot_message_text_color':
                case 'user_message_bg_color':
                case 'user_message_text_color':
                case 'input_bg_color':
                case 'input_text_color':
                case 'input_send_button_color':
                case 'tooltip_bg_color':
                case 'tooltip_text_color':
                case 'footer_text_color':
                case 'disclaimer_button_color':
                case 'disclaimer_text_color':
                case 'disclaimer_bg_color':
                    $sanitized[$key] = sanitize_hex_color($value);
                    break;

                // Números
                case 'button_position_right':
                case 'button_position_bottom':
                case 'button_size':
                case 'window_height':
                case 'window_width':
                case 'font_size':
                case 'tooltip_font_size':
                case 'max_chars':
                case 'auto_open_delay':
                case 'rate_limiting':
                case 'session_timeout':
                    $sanitized[$key] = absint($value);
                    break;

                // Booleanos
                case 'enable_drag':
                case 'auto_open':
                case 'auto_open_mobile':
                case 'show_tooltip':
                case 'bot_avatar_enabled':
                case 'user_avatar_enabled':
                case 'auto_focus':
                case 'enable_send_sound':
                case 'enable_receive_sound':
                case 'show_disclaimer':
                case 'debug_mode':
                case 'save_conversations':
                case 'analytics_enabled':
                case 'show_for_logged_in':
                case 'hide_on_mobile':
                    $sanitized[$key] = (bool)$value;
                    break;

                // Arrays
                case 'display_pages':
                case 'show_for_roles':
                case 'webhook_events':
                    $sanitized[$key] = is_array($value) ? array_map('sanitize_text_field', $value) : array();
                    break;

                // Código personalizado
                case 'custom_css':
                    $sanitized[$key] = wp_strip_all_tags($value);
                    break;
                case 'custom_js':
                    $sanitized[$key] = $value; // No sanitizamos JavaScript personalizado
                    break;

                // Campos multilínea
                case 'starter_prompts':
                case 'custom_headers':
                    $sanitized[$key] = sanitize_textarea_field($value);
                    break;

                // Color con transparencia
                case 'disclaimer_overlay_color':
                    $sanitized[$key] = $value; // Permitimos rgba()
                    break;

                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        }

        return $sanitized;
    }

    private function get_sanitized_options() {
        return array_merge($this->get_default_options(), $this->options);
    }

    private function get_default_options() {
        return array(
            'chatflow_id' => '',
            'api_host' => '',
            'button_color' => '#3B81F6',
            'button_position_right' => 20,
            'button_position_bottom' => 20,
            'button_size' => 48,
            'enable_drag' => true,
            'icon_color' => 'white',
            'custom_icon' => 'https://raw.githubusercontent.com/walkxcode/dashboard-icons/main/svg/google-messages.svg',
            'window_title' => 'IGIS Bot',
            'welcome_message' => 'Hello! How can I help you today?',
            'error_message' => 'Lo siento, ha ocurrido un error. Por favor, intenta de nuevo.',
            'window_height' => 700,
            'window_width' => 400,
            'window_background_color' => '#ffffff',
            'font_size' => 16,
            'show_tooltip' => true,
            'tooltip_message' => 'Hi There 👋!',
            'tooltip_bg_color' => 'black',
            'tooltip_text_color' => 'white',
            'tooltip_font_size' => 16
        );
    }
}

// Inicializar el plugin
function igis_flowise_bot_init() {
    return IGIS_Flowise_Bot::get_instance();
}

// Función de instalación
function igis_flowise_bot_install() {
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

// Función de desinstalación
function igis_flowise_bot_uninstall() {
    // Solo si se ha habilitado la opción de eliminar datos
    if (get_option('igis_bot_delete_data')) {
        global $wpdb;
        
        // Eliminar tablas
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}igis_bot_messages");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}igis_bot_conversations");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}igis_bot_analytics");
        
        // Eliminar opciones
        delete_option('igis_bot_options');
        delete_option('igis_bot_delete_data');
    }
}

// Registrar hooks
register_activation_hook(__FILE__, 'igis_flowise_bot_install');
register_uninstall_hook(__FILE__, 'igis_flowise_bot_uninstall');

// Arrancar el plugin
igis_flowise_bot_init();
