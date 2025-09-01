<?php
/**
 * Plugin Name: IGIS Flowise Bot
 * Plugin URI: https://www.infraestructuragis.com/
 * Description: Integra el chatbot de Flowise en tu sitio WordPress con optimizaciones avanzadas para WordPress 6.8+
 * Version: 1.2.0
 * Requires at least: 6.8
 * Requires PHP: 8.0
 * Author: InfraestructuraGIS
 * Author URI: https://www.infraestructuragis.com/
 * License: GPL v2 or later
 * Text Domain: igis-flowise-bot
 */

if (!defined('ABSPATH')) {
    exit;
}

define('IGIS_BOT_VERSION', '1.2.0');
define('IGIS_BOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IGIS_BOT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IGIS_BOT_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('IGIS_BOT_CACHE_TIME', 12 * HOUR_IN_SECONDS);
define('IGIS_BOT_MIN_WP_VERSION', '6.8');
define('IGIS_BOT_MIN_PHP_VERSION', '8.0');

class IGIS_Flowise_Bot {
    private static $instancia = null;
    private $opciones;
    private $cache_manager;
    private $optimizador_performance;
    private $detector_tema_oscuro;
    private $precargador_recursos;

    public static function obtener_instancia() {
        if (null === self::$instancia) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    private function __construct() {
        $this->verificar_compatibilidad();
        $this->inicializar_componentes();
        $this->configurar_hooks();
    }

    private function verificar_compatibilidad() {
        if (version_compare(get_bloginfo('version'), IGIS_BOT_MIN_WP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'mostrar_aviso_version_wp'));
            return;
        }

        if (version_compare(PHP_VERSION, IGIS_BOT_MIN_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'mostrar_aviso_version_php'));
            return;
        }
    }

    public function mostrar_aviso_version_wp() {
        echo '<div class="notice notice-error"><p>';
        printf(
            __('IGIS Flowise Bot requiere WordPress %s o superior. Tu versión actual es %s.', 'igis-flowise-bot'),
            IGIS_BOT_MIN_WP_VERSION,
            get_bloginfo('version')
        );
        echo '</p></div>';
    }

    public function mostrar_aviso_version_php() {
        echo '<div class="notice notice-error"><p>';
        printf(
            __('IGIS Flowise Bot requiere PHP %s o superior. Tu versión actual es %s.', 'igis-flowise-bot'),
            IGIS_BOT_MIN_PHP_VERSION,
            PHP_VERSION
        );
        echo '</p></div>';
    }

    private function inicializar_componentes() {
        $this->opciones = $this->obtener_opciones_optimizadas();
        $this->cache_manager = new IGIS_Cache_Manager();
        $this->optimizador_performance = new IGIS_Optimizador_Performance();
        $this->detector_tema_oscuro = new IGIS_Detector_Tema_Oscuro();
        $this->precargador_recursos = new IGIS_Precargador_Recursos();
        
        $this->crear_directorios();
    }

    private function configurar_hooks() {
        register_activation_hook(__FILE__, array($this, 'activar_plugin'));
        register_deactivation_hook(__FILE__, array($this, 'desactivar_plugin'));
        
        add_action('init', array($this, 'inicializar_localizacion'));
        add_action('admin_menu', array($this, 'agregar_menu_admin'));
        add_action('admin_init', array($this, 'registrar_configuraciones'));
        add_action('wp_footer', array($this, 'renderizar_bot'));
        add_action('admin_enqueue_scripts', array($this, 'encolar_assets_admin'));
        add_action('wp_enqueue_scripts', array($this, 'encolar_assets_frontend'));
        add_action('wp_head', array($this, 'agregar_precargas_head'));
        add_action('wp_head', array($this, 'agregar_variables_css_personalizadas'));
        
        // Optimizaciones de WordPress 6.8+
        add_action('wp_loaded', array($this, 'configurar_cache_objeto'));
        add_filter('wp_resource_hints', array($this, 'agregar_resource_hints'), 10, 2);
        add_action('wp_footer', array($this, 'agregar_preload_recursos'), 1);
        
        // Hooks para tema oscuro
        add_action('wp_head', array($this, 'detectar_y_aplicar_tema_oscuro'));
        add_filter('body_class', array($this, 'agregar_clases_tema_body'));
        
        $this->registrar_manejadores_ajax();
    }

    public function inicializar_localizacion() {
        load_plugin_textdomain(
            'igis-flowise-bot',
            false,
            dirname(IGIS_BOT_PLUGIN_BASENAME) . '/languages/'
        );
    }

    private function crear_directorios() {
        $directorio_assets = IGIS_BOT_PLUGIN_DIR . 'assets';
        if (!file_exists($directorio_assets)) {
            wp_mkdir_p($directorio_assets);
            wp_mkdir_p($directorio_assets . '/css');
            wp_mkdir_p($directorio_assets . '/js');
            wp_mkdir_p($directorio_assets . '/cache');
            
            $this->crear_assets_iniciales();
        }
    }

    private function crear_assets_iniciales() {
        $this->crear_css_optimizado();
        $this->crear_js_optimizado();
    }

    private function crear_css_optimizado() {
        $css_admin_contenido = $this->generar_css_admin_optimizado();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/css/admin.css', $css_admin_contenido);
        
        $css_frontend_contenido = $this->generar_css_frontend_optimizado();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/css/frontend.css', $css_frontend_contenido);
    }

    private function generar_css_admin_optimizado() {
        return "/* IGIS Flowise Bot Admin - Optimizado para WordPress 6.8+ */
:root {
    --igis-primary: #3B81F6;
    --igis-primary-hover: #2563EB;
    --igis-secondary: #64748B;
    --igis-success: #10B981;
    --igis-warning: #F59E0B;
    --igis-error: #EF4444;
    --igis-bg-light: #F8FAFC;
    --igis-bg-dark: #1E293B;
    --igis-text-light: #1E293B;
    --igis-text-dark: #F8FAFC;
    --igis-border-light: #E2E8F0;
    --igis-border-dark: #374151;
    --igis-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --igis-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --igis-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Optimizaciones de performance */
.igis-bot-admin-wrap * {
    box-sizing: border-box;
    transform: translateZ(0);
}

.igis-bot-admin-wrap {
    margin: 20px;
    contain: layout style paint;
}

/* Tema claro por defecto */
.igis-bot-admin-wrap {
    background-color: var(--igis-bg-light);
    color: var(--igis-text-light);
    transition: var(--igis-transition);
}

/* Soporte para tema oscuro */
@media (prefers-color-scheme: dark) {
    .igis-bot-admin-wrap {
        background-color: var(--igis-bg-dark);
        color: var(--igis-text-dark);
    }
    
    .settings-section,
    .stats-card,
    .chart-container {
        background-color: var(--igis-bg-dark);
        border-color: var(--igis-border-dark);
        color: var(--igis-text-dark);
    }
}

/* WordPress admin theme dark support */
.admin-color-scheme-dark .igis-bot-admin-wrap,
body.admin-color-dark .igis-bot-admin-wrap {
    background-color: var(--igis-bg-dark);
    color: var(--igis-text-dark);
}

.admin-color-scheme-dark .settings-section,
.admin-color-scheme-dark .stats-card,
body.admin-color-dark .settings-section,
body.admin-color-dark .stats-card {
    background-color: #32373C;
    border-color: var(--igis-border-dark);
    color: var(--igis-text-dark);
}

/* Navegación por pestañas mejorada */
.nav-tab-wrapper {
    margin-bottom: 20px;
    border-bottom: 2px solid var(--igis-border-light);
}

.nav-tab {
    position: relative;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    transition: var(--igis-transition);
    font-weight: 500;
}

.nav-tab:hover {
    background-color: var(--igis-bg-light);
    color: var(--igis-primary);
    transform: translateY(-2px);
}

.nav-tab-active {
    background-color: var(--igis-primary);
    color: white;
    border-bottom-color: var(--igis-primary);
}

/* Secciones de configuración */
.settings-section {
    display: none;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: var(--igis-shadow);
    margin-bottom: 20px;
    animation: igis-fade-in 0.3s ease-out;
}

.settings-section.active {
    display: block;
}

@keyframes igis-fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Campos de formulario mejorados */
.form-table th {
    padding: 20px 15px;
    width: 220px;
    vertical-align: top;
    font-weight: 600;
    color: var(--igis-text-light);
}

.form-table td {
    padding: 15px;
    vertical-align: middle;
}

input[type='text'],
input[type='number'],
input[type='email'],
input[type='url'],
select,
textarea {
    border: 2px solid var(--igis-border-light);
    border-radius: 8px;
    padding: 12px 16px;
    transition: var(--igis-transition);
    font-size: 14px;
}

input[type='text']:focus,
input[type='number']:focus,
input[type='email']:focus,
input[type='url']:focus,
select:focus,
textarea:focus {
    border-color: var(--igis-primary);
    box-shadow: 0 0 0 3px rgba(59, 129, 246, 0.1);
    outline: none;
}

/* Botones modernos */
.button-primary,
.button-secondary {
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 600;
    transition: var(--igis-transition);
    border: none;
}

.button-primary {
    background-color: var(--igis-primary);
    color: white;
}

.button-primary:hover {
    background-color: var(--igis-primary-hover);
    transform: translateY(-2px);
    box-shadow: var(--igis-shadow-lg);
}

/* Vista previa del bot */
.bot-preview {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    z-index: 99999;
    display: none;
    justify-content: center;
    align-items: center;
    animation: igis-fade-in 0.3s ease-out;
}

.bot-preview-content {
    background: white;
    border-radius: 16px;
    box-shadow: var(--igis-shadow-lg);
    width: 90%;
    max-width: 1200px;
    height: 90%;
    max-height: 800px;
    overflow: hidden;
    animation: igis-slide-up 0.3s ease-out;
}

@keyframes igis-slide-up {
    from {
        opacity: 0;
        transform: translateY(50px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Estadísticas mejoradas */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.stats-card {
    background: linear-gradient(135deg, white 0%, var(--igis-bg-light) 100%);
    border: 2px solid var(--igis-border-light);
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    transition: var(--igis-transition);
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--igis-primary), var(--igis-primary-hover));
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--igis-shadow-lg);
}

.stats-value {
    font-size: 48px;
    font-weight: 800;
    background: linear-gradient(135deg, var(--igis-primary), var(--igis-primary-hover));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 20px 0;
}

/* Responsive design mejorado */
@media screen and (max-width: 1024px) {
    .stats-container {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }
}

@media screen and (max-width: 768px) {
    .igis-bot-admin-wrap {
        margin: 10px;
    }
    
    .settings-section {
        padding: 20px;
        margin-bottom: 15px;
    }
    
    .form-table th,
    .form-table td {
        display: block;
        width: 100%;
        padding: 10px 0;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .nav-tab {
        font-size: 14px;
        padding: 12px 16px;
    }
}

/* Animaciones de carga */
.loading-spinner {
    display: inline-block;
    width: 32px;
    height: 32px;
    border: 3px solid var(--igis-border-light);
    border-radius: 50%;
    border-top-color: var(--igis-primary);
    animation: igis-spin 1s ease-in-out infinite;
}

@keyframes igis-spin {
    to {
        transform: rotate(360deg);
    }
}

/* Mejoras de accesibilidad */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Focus visible mejorado */
input:focus-visible,
button:focus-visible,
select:focus-visible,
textarea:focus-visible {
    outline: 2px solid var(--igis-primary);
    outline-offset: 2px;
}

/* Performance optimizations */
.igis-performance-optimized {
    will-change: transform;
    contain: strict;
    content-visibility: auto;
    contain-intrinsic-size: 0 500px;
}

/* Tema oscuro específico para admin */
[data-theme='dark'] .igis-bot-admin-wrap {
    background-color: var(--igis-bg-dark);
    color: var(--igis-text-dark);
}

[data-theme='dark'] .settings-section,
[data-theme='dark'] .stats-card {
    background-color: #1F2937;
    border-color: var(--igis-border-dark);
    color: var(--igis-text-dark);
}

[data-theme='dark'] input[type='text'],
[data-theme='dark'] input[type='number'],
[data-theme='dark'] select,
[data-theme='dark'] textarea {
    background-color: #374151;
    border-color: var(--igis-border-dark);
    color: var(--igis-text-dark);
}";
    }

    private function generar_css_frontend_optimizado() {
        return "/* IGIS Flowise Bot Frontend - Optimizado */
:root {
    --igis-bot-primary: #3B81F6;
    --igis-bot-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.flowise-chatbot-button {
    transition: var(--igis-bot-transition);
    will-change: transform;
    contain: layout style paint;
}

.flowise-chatbot-button:hover {
    transform: scale(1.05);
}

/* Optimización para tema oscuro */
@media (prefers-color-scheme: dark) {
    .flowise-chatbot-button {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
}

/* Performance optimizations */
.flowise-embed {
    contain: layout style paint;
    content-visibility: auto;
}";
    }

    private function crear_js_optimizado() {
        $js_admin_contenido = $this->generar_js_admin_optimizado();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/admin.js', $js_admin_contenido);
        
        $js_frontend_contenido = $this->generar_js_frontend_optimizado();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/frontend.js', $js_frontend_contenido);
    }

    public function activar_plugin() {
        $opciones_predeterminadas = $this->obtener_opciones_predeterminadas();
        
        if (!get_option('igis_bot_options')) {
            add_option('igis_bot_options', $opciones_predeterminadas);
        }
        
        $this->crear_tablas_base_datos();
        $this->configurar_cache_transients();
        flush_rewrite_rules();
    }

    public function desactivar_plugin() {
        // Limpiar cache
        $this->cache_manager->limpiar_todo();
        
        // Limpiar transients
        delete_transient('igis_bot_configuracion_cache');
        
        if (get_option('igis_bot_eliminar_datos_desactivar')) {
            delete_option('igis_bot_options');
        }
    }

    private function crear_tablas_base_datos() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $tabla_conversaciones = $wpdb->prefix . 'igis_bot_conversaciones';
        $sql = "CREATE TABLE IF NOT EXISTS $tabla_conversaciones (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            usuario_id bigint(20) DEFAULT NULL,
            sesion_id varchar(64) NOT NULL,
            estado varchar(20) NOT NULL DEFAULT 'activa',
            iniciado_en datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            finalizado_en datetime DEFAULT NULL,
            metadatos longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY usuario_id (usuario_id),
            KEY sesion_id (sesion_id),
            KEY estado (estado)
        ) $charset_collate;";
        
        $tabla_mensajes = $wpdb->prefix . 'igis_bot_mensajes';
        $sql .= "CREATE TABLE IF NOT EXISTS $tabla_mensajes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            conversacion_id bigint(20) NOT NULL,
            mensaje longtext NOT NULL,
            tipo enum('usuario','bot') NOT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            metadatos longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY conversacion_id (conversacion_id),
            KEY tipo (tipo),
            KEY timestamp (timestamp),
            FOREIGN KEY (conversacion_id) REFERENCES $tabla_conversaciones(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $tabla_analytics = $wpdb->prefix . 'igis_bot_analytics';
        $sql .= "CREATE TABLE IF NOT EXISTS $tabla_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            tipo_evento varchar(100) NOT NULL,
            datos_evento longtext DEFAULT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            usuario_id bigint(20) DEFAULT NULL,
            sesion_id varchar(64) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY tipo_evento (tipo_evento),
            KEY timestamp (timestamp),
            KEY usuario_id (usuario_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function obtener_opciones_optimizadas() {
        $cache_key = 'igis_bot_opciones_' . md5(IGIS_BOT_VERSION);
        $opciones = wp_cache_get($cache_key, 'igis_bot');
        
        if (false === $opciones) {
            $opciones = get_option('igis_bot_options', $this->obtener_opciones_predeterminadas());
            wp_cache_set($cache_key, $opciones, 'igis_bot', IGIS_BOT_CACHE_TIME);
        }
        
        return $opciones;
    }

    private function obtener_opciones_predeterminadas() {
        return array(
            'chatflow_id' => '',
            'api_host' => '',
            'api_key' => '',
            'button_color' => '#3B81F6',
            'button_position_right' => 20,
            'button_position_bottom' => 20,
            'button_size' => 48,
            'enable_drag' => true,
            'icon_color' => 'white',
            'custom_icon' => '',
            'window_title' => __('Asistente Virtual', 'igis-flowise-bot'),
            'welcome_message' => __('¡Hola! ¿Cómo puedo ayudarte hoy?', 'igis-flowise-bot'),
            'error_message' => __('Lo siento, ha ocurrido un error. Por favor, intenta de nuevo.', 'igis-flowise-bot'),
            'window_height' => 700,
            'window_width' => 400,
            'window_background_color' => '#ffffff',
            'font_size' => 16,
            'bot_message_bg_color' => '#f7f8ff',
            'bot_message_text_color' => '#303235',
            'user_message_bg_color' => '#3B81F6',
            'user_message_text_color' => '#ffffff',
            'input_placeholder' => __('Escribe tu pregunta...', 'igis-flowise-bot'),
            'show_tooltip' => true,
            'tooltip_message' => __('¡Hola! ¿Necesitas ayuda?', 'igis-flowise-bot'),
            'tooltip_bg_color' => 'black',
            'tooltip_text_color' => 'white',
            'enable_voice_input' => false,
            'enable_voice_output' => false,
            'enable_file_upload' => false,
            'enable_dark_mode' => true,
            'auto_open' => false,
            'display_pages' => array('all'),
            'performance_mode' => true,
            'preload_resources' => true,
            'save_conversations' => false,
            'analytics_enabled' => false
        );
    }

    public function configurar_cache_objeto() {
        if (function_exists('wp_cache_add_global_groups')) {
            wp_cache_add_global_groups(array('igis_bot'));
        }
    }

    public function agregar_resource_hints($hints, $relation_type) {
        if ('preconnect' === $relation_type) {
            $api_host = $this->opciones['api_host'] ?? '';
            if (!empty($api_host)) {
                $hints[] = $api_host;
            }
            
            // Preconectar a CDNs comunes
            $hints[] = 'https://cdn.jsdelivr.net';
            $hints[] = 'https://fonts.googleapis.com';
        }
        
        return $hints;
    }

    public function agregar_preload_recursos() {
        if (!$this->deberia_mostrar_bot()) {
            return;
        }

        if ($this->opciones['preload_resources']) {
            echo '<link rel="modulepreload" href="https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js">';
            echo '<link rel="preload" href="' . IGIS_BOT_PLUGIN_URL . 'assets/css/frontend.css" as="style">';
        }
    }

    public function agregar_precargas_head() {
        if (!$this->deberia_mostrar_bot()) {
            return;
        }

        $api_host = $this->opciones['api_host'] ?? '';
        if (!empty($api_host)) {
            echo '<link rel="preconnect" href="' . esc_url($api_host) . '" crossorigin>';
        }
    }

    public function detectar_y_aplicar_tema_oscuro() {
        $tema_oscuro = $this->detector_tema_oscuro->detectar();
        
        if ($tema_oscuro) {
            echo '<script>document.documentElement.setAttribute("data-theme", "dark");</script>';
        }
        
        // Variables CSS dinámicas
        $this->agregar_variables_css_tema($tema_oscuro);
    }

    private function agregar_variables_css_tema($tema_oscuro) {
        $variables = $tema_oscuro ? $this->obtener_variables_tema_oscuro() : $this->obtener_variables_tema_claro();
        
        echo '<style id="igis-bot-theme-vars">:root {';
        foreach ($variables as $propiedad => $valor) {
            echo esc_attr($propiedad) . ': ' . esc_attr($valor) . ';';
        }
        echo '}</style>';
    }

    private function obtener_variables_tema_oscuro() {
        return array(
            '--igis-bg-primary' => '#1E293B',
            '--igis-bg-secondary' => '#374151',
            '--igis-text-primary' => '#F8FAFC',
            '--igis-text-secondary' => '#D1D5DB',
            '--igis-border-color' => '#4B5563'
        );
    }

    private function obtener_variables_tema_claro() {
        return array(
            '--igis-bg-primary' => '#FFFFFF',
            '--igis-bg-secondary' => '#F8FAFC',
            '--igis-text-primary' => '#1E293B',
            '--igis-text-secondary' => '#64748B',
            '--igis-border-color' => '#E2E8F0'
        );
    }

    public function agregar_variables_css_personalizadas() {
        if (!$this->deberia_mostrar_bot()) {
            return;
        }

        $variables_personalizadas = array(
            '--igis-bot-primary-color' => $this->opciones['button_color'] ?? '#3B81F6',
            '--igis-bot-window-width' => ($this->opciones['window_width'] ?? 400) . 'px',
            '--igis-bot-window-height' => ($this->opciones['window_height'] ?? 700) . 'px',
            '--igis-bot-font-size' => ($this->opciones['font_size'] ?? 16) . 'px'
        );

        echo '<style id="igis-bot-custom-vars">:root {';
        foreach ($variables_personalizadas as $propiedad => $valor) {
            echo esc_attr($propiedad) . ': ' . esc_attr($valor) . ';';
        }
        echo '}</style>';
    }

    public function agregar_clases_tema_body($clases) {
        if ($this->detector_tema_oscuro->detectar()) {
            $clases[] = 'igis-bot-tema-oscuro';
        }
        
        if ($this->opciones['performance_mode']) {
            $clases[] = 'igis-bot-performance-mode';
        }
        
        return $clases;
    }

    private function configurar_cache_transients() {
        // Configurar cache para diferentes componentes
        set_transient('igis_bot_version_cache', IGIS_BOT_VERSION, DAY_IN_SECONDS);
        
        $configuracion_cache = array(
            'version' => IGIS_BOT_VERSION,
            'timestamp' => current_time('timestamp'),
            'opciones_hash' => md5(serialize($this->opciones))
        );
        
        set_transient('igis_bot_configuracion_cache', $configuracion_cache, IGIS_BOT_CACHE_TIME);
    }

    private function deberia_mostrar_bot() {
        $cache_key = 'igis_bot_mostrar_' . get_current_user_id() . '_' . get_the_ID();
        $mostrar = wp_cache_get($cache_key, 'igis_bot');
        
        if (false === $mostrar) {
            $mostrar = $this->evaluar_condiciones_mostrar();
            wp_cache_set($cache_key, $mostrar, 'igis_bot', 300); // Cache por 5 minutos
        }
        
        return $mostrar;
    }

    private function evaluar_condiciones_mostrar() {
        // Verificar si estamos en admin
        if (is_admin()) {
            return false;
        }

        // Verificar configuración básica
        if (empty($this->opciones['chatflow_id']) || empty($this->opciones['api_host'])) {
            return false;
        }

        // Verificar páginas de visualización
        $paginas_mostrar = $this->opciones['display_pages'] ?? array('all');
        
        if (in_array('all', $paginas_mostrar)) {
            return true;
        }

        // Evaluar páginas específicas
        if (is_front_page() && in_array('front_page', $paginas_mostrar)) {
            return true;
        }

        if (is_page() && in_array(get_the_ID(), $paginas_mostrar)) {
            return true;
        }

        // Más condiciones...
        return false;
    }

    public function renderizar_bot() {
        if (!$this->deberia_mostrar_bot()) {
            return;
        }

        $configuracion_bot = $this->generar_configuracion_bot();
        $this->generar_script_bot($configuracion_bot);
        $this->generar_estilos_personalizados();
        $this->generar_script_tracking();
    }

    private function generar_configuracion_bot() {
        $configuracion_base = array(
            'chatflowid' => $this->opciones['chatflow_id'],
            'apiHost' => $this->opciones['api_host']
        );

        if (!empty($this->opciones['api_key'])) {
            $configuracion_base['apiKey'] = $this->opciones['api_key'];
        }

        $configuracion_tema = $this->generar_configuracion_tema();
        
        return array_merge($configuracion_base, $configuracion_tema);
    }

    private function generar_configuracion_tema() {
        return array(
            'theme' => array(
                'button' => array(
                    'backgroundColor' => $this->opciones['button_color'],
                    'right' => (int) $this->opciones['button_position_right'],
                    'bottom' => (int) $this->opciones['button_position_bottom'],
                    'size' => (int) $this->opciones['button_size'],
                    'iconColor' => $this->opciones['icon_color'],
                    'customIconSrc' => $this->opciones['custom_icon'],
                    'dragable' => (bool) $this->opciones['enable_drag']
                ),
                'chatWindow' => array(
                    'welcomeMessage' => $this->opciones['welcome_message'],
                    'backgroundColor' => $this->opciones['window_background_color'],
                    'height' => (int) $this->opciones['window_height'],
                    'width' => (int) $this->opciones['window_width'],
                    'fontSize' => (int) $this->opciones['font_size'],
                    'title' => $this->opciones['window_title']
                ),
                'userMessage' => array(
                    'backgroundColor' => $this->opciones['user_message_bg_color'],
                    'textColor' => $this->opciones['user_message_text_color']
                ),
                'botMessage' => array(
                    'backgroundColor' => $this->opciones['bot_message_bg_color'],
                    'textColor' => $this->opciones['bot_message_text_color']
                )
            )
        );
    }

    private function generar_script_bot($configuracion) {
        ?>
        <script type="module" id="igis-bot-embed">
            import Chatbot from "https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js";
            
            // Configuración optimizada
            const configuracionBot = <?php echo wp_json_encode($configuracion); ?>;
            
            // Inicialización con performance optimizada
            if (window.requestIdleCallback) {
                window.requestIdleCallback(() => {
                    Chatbot.init(configuracionBot);
                });
            } else {
                setTimeout(() => {
                    Chatbot.init(configuracionBot);
                }, 0);
            }

            // Eventos personalizados
            document.addEventListener('flowise:ready', function() {
                console.log('IGIS Bot inicializado correctamente');
                document.dispatchEvent(new CustomEvent('igis:bot:listo'));
            });
        </script>
        <?php
    }

    private function generar_estilos_personalizados() {
        if (!empty($this->opciones['custom_css'])) {
            echo '<style type="text/css" id="igis-bot-custom-css">';
            echo wp_strip_all_tags($this->opciones['custom_css']);
            echo '</style>';
        }
    }

    private function generar_script_tracking() {
        if ($this->opciones['save_conversations'] || $this->opciones['analytics_enabled']) {
            ?>
            <script id="igis-bot-tracking">
            document.addEventListener('DOMContentLoaded', function() {
                // Generar ID de sesión único
                let sessionId = localStorage.getItem('igis_bot_session_id');
                if (!sessionId) {
                    sessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + Date.now();
                    localStorage.setItem('igis_bot_session_id', sessionId);
                }

                // Tracking de eventos
                const trackEvent = function(evento, datos = {}) {
                    if (!<?php echo json_encode($this->opciones['analytics_enabled']); ?>) return;
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'igis_bot_track_evento',
                            nonce: '<?php echo wp_create_nonce('igis_bot_tracking'); ?>',
                            evento: evento,
                            datos: JSON.stringify(datos),
                            session_id: sessionId
                        })
                    });
                };

                // Eventos del bot
                document.addEventListener('flowise:chatOpen', () => trackEvent('bot_abierto'));
                document.addEventListener('flowise:chatClose', () => trackEvent('bot_cerrado'));
                document.addEventListener('flowise:messageSubmitted', (e) => {
                    trackEvent('mensaje_enviado', { mensaje: e.detail.message });
                });
            });
            </script>
            <?php
        }
    }

    // Resto de métodos para AJAX, admin, etc...
    private function registrar_manejadores_ajax() {
        add_action('wp_ajax_igis_bot_track_evento', array($this, 'manejar_track_evento'));
        add_action('wp_ajax_nopriv_igis_bot_track_evento', array($this, 'manejar_track_evento'));
    }

    public function manejar_track_evento() {
        if (!check_ajax_referer('igis_bot_tracking', 'nonce', false)) {
            wp_send_json_error('Nonce inválido');
        }

        $evento = sanitize_text_field($_POST['evento'] ?? '');
        $datos = json_decode(sanitize_textarea_field($_POST['datos'] ?? '{}'), true);
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');

        global $wpdb;
        $tabla = $wpdb->prefix . 'igis_bot_analytics';
        
        $wpdb->insert(
            $tabla,
            array(
                'tipo_evento' => $evento,
                'datos_evento' => wp_json_encode($datos),
                'sesion_id' => $session_id,
                'usuario_id' => get_current_user_id(),
                'timestamp' => current_time('mysql')
            )
        );

    }

    // Métodos para admin y configuración
    public function agregar_menu_admin() {
        add_menu_page(
            __('IGIS Flowise Bot', 'igis-flowise-bot'),
            __('IGIS Bot', 'igis-flowise-bot'),
            'manage_options',
            'igis-flowise-bot',
            array($this, 'renderizar_pagina_admin'),
            'dashicons-format-chat',
            99
        );

        add_submenu_page(
            'igis-flowise-bot',
            __('Configuración', 'igis-flowise-bot'),
            __('Configuración', 'igis-flowise-bot'),
            'manage_options',
            'igis-flowise-bot'
        );

        add_submenu_page(
            'igis-flowise-bot',
            __('Estadísticas', 'igis-flowise-bot'),
            __('Analytics', 'igis-flowise-bot'),
            'manage_options',
            'igis-flowise-bot-stats',
            array($this, 'renderizar_pagina_estadisticas')
        );
    }

    public function registrar_configuraciones() {
        register_setting('igis_bot_options', 'igis_bot_options', array($this, 'sanitizar_opciones'));
    }

    public function renderizar_pagina_admin() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include_once IGIS_BOT_PLUGIN_DIR . 'templates/admin-page.php';
    }

    public function renderizar_pagina_estadisticas() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include_once IGIS_BOT_PLUGIN_DIR . 'templates/stats-page.php';
    }

    public function encolar_assets_admin($hook) {
        if (strpos($hook, 'igis-flowise-bot') === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();

        wp_enqueue_style(
            'igis-bot-admin',
            IGIS_BOT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            IGIS_BOT_VERSION
        );

        wp_enqueue_script(
            'igis-bot-admin',
            IGIS_BOT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            IGIS_BOT_VERSION,
            true
        );

        wp_localize_script('igis-bot-admin', 'igisBotAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('igis_bot_admin'),
            'version' => IGIS_BOT_VERSION,
            'precargar_datos' => true,
            'cadenas' => array(
                'guardar_exito' => __('Configuración guardada correctamente', 'igis-flowise-bot'),
                'error_guardar' => __('Error al guardar la configuración', 'igis-flowise-bot'),
                'confirmar_eliminar' => __('¿Estás seguro de eliminar esta conversación?', 'igis-flowise-bot')
            )
        ));
    }

    public function encolar_assets_frontend() {
        if (!$this->deberia_mostrar_bot()) {
            return;
        }

        wp_enqueue_style(
            'igis-bot-frontend',
            IGIS_BOT_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            IGIS_BOT_VERSION
        );

        if ($this->opciones['performance_mode']) {
            wp_script_add_data('igis-bot-frontend', 'async', true);
        }
    }

    public function sanitizar_opciones($input) {
        $sanitized = array();
        
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'chatflow_id':
                case 'api_key':
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
                case 'api_host':
                    $sanitized[$key] = esc_url_raw($value);
                    break;
                case 'button_color':
                case 'window_background_color':
                case 'bot_message_bg_color':
                case 'user_message_bg_color':
                    $sanitized[$key] = sanitize_hex_color($value);
                    break;
                case 'window_height':
                case 'window_width':
                case 'button_size':
                case 'font_size':
                    $sanitized[$key] = absint($value);
                    break;
                case 'enable_drag':
                case 'show_tooltip':
                case 'auto_open':
                case 'performance_mode':
                case 'enable_dark_mode':
                    $sanitized[$key] = (bool) $value;
                    break;
                case 'display_pages':
                    $sanitized[$key] = is_array($value) ? array_map('sanitize_text_field', $value) : array();
                    break;
                default:
                    $sanitized[$key] = sanitize_textarea_field($value);
                    break;
            }
        }

        // Limpiar cache después de actualizar opciones
        wp_cache_delete('igis_bot_opciones_' . md5(IGIS_BOT_VERSION), 'igis_bot');
        
        return $sanitized;
    }
}

/**
 * Clase para gestión de cache optimizada
 */
class IGIS_Cache_Manager {
    private $cache_group = 'igis_bot';
    private $cache_expiration = IGIS_BOT_CACHE_TIME;

    public function obtener($key, $default = false) {
        return wp_cache_get($key, $this->cache_group) ?: $default;
    }

    public function establecer($key, $data, $expiration = null) {
        $expiration = $expiration ?: $this->cache_expiration;
        return wp_cache_set($key, $data, $this->cache_group, $expiration);
    }

    public function eliminar($key) {
        return wp_cache_delete($key, $this->cache_group);
    }

    public function limpiar_todo() {
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group($this->cache_group);
        } else {
            wp_cache_flush();
        }
    }

    public function obtener_estadisticas_cache() {
        return array(
            'hits' => wp_cache_get('cache_hits', $this->cache_group) ?: 0,
            'misses' => wp_cache_get('cache_misses', $this->cache_group) ?: 0
        );
    }
}

/**
 * Clase para optimizaciones de performance
 */
class IGIS_Optimizador_Performance {
    
    public function __construct() {
        add_action('wp_head', array($this, 'agregar_optimizaciones_head'), 1);
        add_filter('script_loader_tag', array($this, 'optimizar_scripts'), 10, 3);
        add_filter('style_loader_tag', array($this, 'optimizar_estilos'), 10, 4);
    }

    public function agregar_optimizaciones_head() {
        // Resource hints optimizados
        echo '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">';
        echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>';
        
        // Critical CSS inline si está disponible
        $this->insertar_css_critico();
    }

    private function insertar_css_critico() {
        $css_critico = $this->generar_css_critico();
        if (!empty($css_critico)) {
            echo '<style id="igis-critical-css">' . $css_critico . '</style>';
        }
    }

    private function generar_css_critico() {
        return ".flowise-chatbot-button{position:fixed;z-index:9999;border-radius:50%;transition:transform .3s ease}";
    }

    public function optimizar_scripts($tag, $handle, $src) {
        if (strpos($handle, 'igis-bot') !== false) {
            // Agregar async/defer según el tipo de script
            if (strpos($handle, 'admin') === false) {
                $tag = str_replace(' src', ' async defer src', $tag);
            }
        }
        return $tag;
    }

    public function optimizar_estilos($html, $handle, $href, $media) {
        if (strpos($handle, 'igis-bot') !== false) {
            // Cargar CSS de forma no bloqueante
            $html = str_replace(" rel='stylesheet'", " rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
            $html .= "<noscript><link rel='stylesheet' href='{$href}' media='{$media}'></noscript>";
        }
        return $html;
    }

    public function minificar_css($css) {
        // Minificación básica de CSS
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        return $css;
    }

    public function minificar_js($js) {
        // Minificación básica de JavaScript
        $js = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '', $js);
        $js = str_replace(array("\r\n", "\r", "\n", "\t"), '', $js);
        return $js;
    }
}

/**
 * Clase para detectar tema oscuro
 */
class IGIS_Detector_Tema_Oscuro {
    
    public function detectar() {
        // Detectar preferencia del sistema
        $preferencia_sistema = $this->detectar_preferencia_sistema();
        
        // Detectar tema de WordPress admin
        $tema_wp_admin = $this->detectar_tema_wp_admin();
        
        // Detectar tema del frontend
        $tema_frontend = $this->detectar_tema_frontend();
        
        return $preferencia_sistema || $tema_wp_admin || $tema_frontend;
    }

    private function detectar_preferencia_sistema() {
        // Esto se manejará con JavaScript en el frontend
        return false;
    }

    private function detectar_tema_wp_admin() {
        if (!is_admin()) {
            return false;
        }

        $esquema_color = get_user_option('admin_color');
        $temas_oscuros = array('midnight', 'ectoplasm', 'coffee', 'blue');
        
        return in_array($esquema_color, $temas_oscuros);
    }

    private function detectar_tema_frontend() {
        // Detectar si el tema activo soporta tema oscuro
        return current_theme_supports('dark-mode') || 
               $this->tiene_clase_body_tema_oscuro();
    }

    private function tiene_clase_body_tema_oscuro() {
        $clases_tema_oscuro = array('dark-mode', 'dark-theme', 'night-mode', 'tema-oscuro');
        $body_class = get_body_class();
        
        return !empty(array_intersect($clases_tema_oscuro, $body_class));
    }

    public function obtener_variables_css_tema_oscuro() {
        return array(
            '--igis-bg-primary' => '#1a1a1a',
            '--igis-bg-secondary' => '#2d2d2d',
            '--igis-text-primary' => '#ffffff',
            '--igis-text-secondary' => '#cccccc',
            '--igis-border-color' => '#404040',
            '--igis-shadow' => '0 4px 6px -1px rgba(0, 0, 0, 0.3)'
        );
    }
}

/**
 * Clase para precargar recursos
 */
class IGIS_Precargador_Recursos {
    private $recursos_precarga = array();

    public function __construct() {
        add_action('wp_head', array($this, 'generar_precargas'), 2);
        add_action('wp_footer', array($this, 'precargar_recursos_criticos'), 1);
    }

    public function agregar_recurso($url, $tipo = 'script', $crossorigin = false) {
        $this->recursos_precarga[] = array(
            'url' => $url,
            'tipo' => $tipo,
            'crossorigin' => $crossorigin
        );
    }

    public function generar_precargas() {
        // Precargar recursos críticos
        $this->agregar_recurso('https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js', 'script', true);
        
        foreach ($this->recursos_precarga as $recurso) {
            $crossorigin = $recurso['crossorigin'] ? ' crossorigin' : '';
            echo '<link rel="modulepreload" href="' . esc_url($recurso['url']) . '"' . $crossorigin . '>';
        }
    }

    public function precargar_recursos_criticos() {
        // Precargar imágenes críticas si están configuradas
        $icono_personalizado = IGIS_Flowise_Bot::obtener_instancia()->opciones['custom_icon'] ?? '';
        if (!empty($icono_personalizado)) {
            echo '<link rel="preload" href="' . esc_url($icono_personalizado) . '" as="image">';
        }
    }
}

// Función de inicialización
function igis_flowise_bot_inicializar() {
    return IGIS_Flowise_Bot::obtener_instancia();
}

// Hooks de activación/desactivación
register_activation_hook(__FILE__, function() {
    IGIS_Flowise_Bot::obtener_instancia()->activar_plugin();
});

register_deactivation_hook(__FILE__, function() {
    IGIS_Flowise_Bot::obtener_instancia()->desactivar_plugin();
});

// Inicializar el plugin
add_action('plugins_loaded', 'igis_flowise_bot_inicializar');

// Funciones auxiliares públicas
if (!function_exists('igis_bot_obtener_opcion')) {
    function igis_bot_obtener_opcion($clave, $predeterminado = null) {
        $instancia = IGIS_Flowise_Bot::obtener_instancia();
        return $instancia->opciones[$clave] ?? $predeterminado;
    }
}

if (!function_exists('igis_bot_esta_activo')) {
    function igis_bot_esta_activo() {
        $instancia = IGIS_Flowise_Bot::obtener_instancia();
        return !empty($instancia->opciones['chatflow_id']) && !empty($instancia->opciones['api_host']);
    }
}

// Compatibilidad con versiones anteriores
class_alias('IGIS_Flowise_Bot', 'IGISFlowiseBot');
(function($) {
    'use strict';

    const IGISBotAdmin = {
        cache: new Map(),
        elementos: {},
        configuracion: window.igisBotAdmin || {},

        init() {
            this.cachearElementos();
            this.configurarEventos();
            this.inicializarComponentes();
            this.configurarTemaOscuro();
            this.optimizarPerformance();
        },

        cachearElementos() {
            this.elementos = {
                formulario: $('#igis-bot-settings-form'),
                pestanas: $('.nav-tab-wrapper a'),
                secciones: $('.settings-section'),
                selectoresColor: $('.color-picker'),
                vistaPrevia: $('.preview-bot'),
                modalPrevia: $('#bot-preview')
            };
        },

        configurarEventos() {
            // Navegación por pestañas optimizada
            this.elementos.pestanas.on('click', this.manejarCambioPestana.bind(this));
            
            // Vista previa del bot
            this.elementos.vistaPrevia.on('click', this.mostrarVistaPrevia.bind(this));
            
            // Cerrar vista previa
            $(document).on('click', '.close-preview', this.cerrarVistaPrevia.bind(this));
            
            // Media uploader
            $(document).on('click', '.upload-media-button', this.abrirMediaUploader.bind(this));
            
            // Auto-guardar (debounced)
            this.elementos.formulario.on('input change', this.debounce(this.autoGuardar.bind(this), 1000));
        },

        inicializarComponentes() {
            // Color pickers con tema oscuro
            this.elementos.selectoresColor.wpColorPicker({
                change: this.actualizarVistaPrevia.bind(this),
                clear: this.actualizarVistaPrevia.bind(this)
            });

            this.mostrarPestanaActiva();
            this.configurarCamposDependientes();
        },

        configurarTemaOscuro() {
            const temaOscuro = this.detectarTemaOscuro();
            if (temaOscuro) {
                $('body').addClass('igis-tema-oscuro');
                this.aplicarEstilosTemaOscuro();
            }

            // Escuchar cambios de tema
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                    if (e.matches) {
                        this.activarTemaOscuro();
                    } else {
                        this.activarTemaClaro();
                    }
                });
            }
        },

        detectarTemaOscuro() {
            // Detectar tema del sistema
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return true;
            }
            
            // Detectar tema de WordPress admin
            if ($('body').hasClass('admin-color-dark') || 
                $('body').hasClass('admin-color-scheme-dark')) {
                return true;
            }
            
            return false;
        },

        aplicarEstilosTemaOscuro() {
            const estilosOscuros = {
                'background-color': '#1E293B',
                'color': '#F8FAFC'
            };
            
            $('.igis-bot-admin-wrap').css(estilosOscuros);
        },

        optimizarPerformance() {
            // Intersection Observer para lazy loading
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(this.manejarInterseccion.bind(this));
                $('.settings-section').each(function() {
                    observer.observe(this);
                });
            }

            // Precargar datos críticos
            this.precargarDatosCriticos();
        },

        manejarInterseccion(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    $(entry.target).addClass('igis-performance-optimized');
                }
            });
        },

        manejarCambioPestana(e) {
            e.preventDefault();
            
            const pestanaActual = $(e.currentTarget);
            const seccionObjetivo = pestanaActual.attr('href').substring(1);
            
            // Optimización: solo cambiar si es diferente
            if (pestanaActual.hasClass('nav-tab-active')) {
                return;
            }
            
            // Remover estados activos
            this.elementos.pestanas.removeClass('nav-tab-active');
            this.elementos.secciones.removeClass('active').hide();
            
            // Activar nueva pestaña
            pestanaActual.addClass('nav-tab-active');
            $('#section-' + seccionObjetivo).addClass('active').fadeIn(300);
            
            // Actualizar URL
            if (history.replaceState) {
                history.replaceState(null, null, '#' + seccionObjetivo);
            }
            
            // Trigger evento personalizado
            $(document).trigger('igis:pestana:cambiada', [seccionObjetivo]);
        },

        mostrarPestanaActiva() {
            const hashActual = window.location.hash.substring(1);
            let pestanaActivar = 'general';
            
            if (hashActual && $('#section-' + hashActual).length) {
                pestanaActivar = hashActual;
            }
            
            $('.nav-tab-wrapper a[href=\"#' + pestanaActivar + '\"]').trigger('click');
        },

        mostrarVistaPrevia() {
            const configuracion = this.recopilarConfiguracion();
            this.renderizarVistaPreviaBot(configuracion);
            this.elementos.modalPrevia.fadeIn(300);
        },

        recopilarConfiguracion() {
            const config = {};
            
            this.elementos.formulario.find('input, select, textarea').each(function() {
                const campo = $(this);
                const nombre = campo.attr('name');
                
                if (nombre && nombre.startsWith('igis_bot_options[')) {
                    const nombreOpcion = nombre.match(/\\[(.+?)\\]/)[1];
                    let valor;
                    
                    if (campo.is(':checkbox')) {
                        valor = campo.is(':checked');
                    } else if (campo.is('select[multiple]')) {
                        valor = campo.val() || [];
                    } else {
                        valor = campo.val();
                    }
                    
                    config[nombreOpcion] = valor;
                }
            });
            
            return config;
        },

        renderizarVistaPreviaBot(config) {
            const contenedor = $('.bot-preview-content');
            contenedor.html(this.generarHTMLVistaPrevia(config));
            this.configurarInteraccionesVistaPrevia();
        },

        generarHTMLVistaPrevia(config) {
            return `
                <div class=\"bot-preview-header\">
                    <h3>Vista Previa del Bot</h3>
                    <button class=\"close-preview\">&times;</button>
                </div>
                <div class=\"preview-container\" style=\"position: relative; height: 500px; background: #f5f5f5;\">
                    <div class=\"preview-chatbot-button\" style=\"
                        position: absolute;
                        right: ${config.button_position_right || 20}px;
                        bottom: ${config.button_position_bottom || 20}px;
                        width: ${config.button_size || 48}px;
                        height: ${config.button_size || 48}px;
                        border-radius: 50%;
                        background: ${config.button_color || '#3B81F6'};
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        transition: transform 0.3s ease;
                    \">
                        <img src=\"${config.custom_icon || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K'}\" 
                             style=\"width: 24px; height: 24px;\">
                    </div>
                    ${config.show_tooltip ? `
                    <div class=\"preview-tooltip\" style=\"
                        position: absolute;
                        right: ${parseInt(config.button_position_right || 20) + parseInt(config.button_size || 48) + 10}px;
                        bottom: ${parseInt(config.button_position_bottom || 20) + parseInt(config.button_size || 48)/2 - 15}px;
                        background: ${config.tooltip_bg_color || 'black'};
                        color: ${config.tooltip_text_color || 'white'};
                        padding: 8px 12px;
                        border-radius: 8px;
                        font-size: ${config.tooltip_font_size || 14}px;
                        white-space: nowrap;
                    \">
                        ${config.tooltip_message || 'Hola! ¿Cómo puedo ayudarte?'}
                    </div>
                    ` : ''}
                </div>
            `;
        },

        configurarInteraccionesVistaPrevia() {
            $('.preview-chatbot-button').on('click', function() {
                $(this).css('transform', 'scale(0.95)');
                setTimeout(() => {
                    $(this).css('transform', 'scale(1)');
                }, 150);
            });
        },

        cerrarVistaPrevia() {
            this.elementos.modalPrevia.fadeOut(300);
        },

        autoGuardar() {
            const datosFormulario = this.elementos.formulario.serialize();
            
            // Guardar en localStorage como respaldo
            localStorage.setItem('igis_bot_configuracion_temporal', datosFormulario);
            
            // Mostrar indicador de guardado
            this.mostrarNotificacion('Configuración guardada automáticamente', 'success');
        },

        mostrarNotificacion(mensaje, tipo = 'info') {
            const notificacion = $(`
                <div class=\"notice notice-${tipo} is-dismissible igis-notification\">
                    <p>${mensaje}</p>
                </div>
            `);
            
            $('.igis-bot-admin-wrap h1').after(notificacion);
            
            setTimeout(() => {
                notificacion.slideUp(300, function() {
                    $(this).remove();
                });
            }, 3000);
        },

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        precargarDatosCriticos() {
            // Precargar configuraciones frecuentemente usadas
            if (this.configuracion.precargar_datos) {
                const datosPrecargar = ['configuracion_general', 'estadisticas_basicas'];
                datosPrecargar.forEach(this.precargarDato.bind(this));
            }
        },

        precargarDato(clave) {
            if (!this.cache.has(clave)) {
                $.ajax({
                    url: this.configuracion.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: `igis_bot_precargar_${clave}`,
                        nonce: this.configuracion.nonce
                    },
                    success: (respuesta) => {
                        if (respuesta.success) {
                            this.cache.set(clave, respuesta.data);
                        }
                    }
                });
            }
        }
    };

    // Inicializar cuando el DOM esté listo
    $(document).ready(() => {
        IGISBotAdmin.init();
    });

})(jQuery);";