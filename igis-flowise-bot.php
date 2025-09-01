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
        $css = <<<EOD
/* IGIS Flowise Bot Admin - Optimizado para WordPress 6.8+ */
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

.igis-bot-admin-wrap * {
    box-sizing: border-box;
    transform: translateZ(0);
}

.igis-bot-admin-wrap {
    margin: 20px;
    contain: layout style paint;
    background-color: var(--igis-bg-light);
    color: var(--igis-text-light);
    transition: var(--igis-transition);
}

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

.admin-color-scheme-dark .igis-bot-admin-wrap,
body.admin-color-dark .igis-bot-admin-wrap {
    background-color: var(--igis-bg-dark);
    color: var(--igis-text-dark);
}

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
    overflow: auto;
}

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

.igis-performance-optimized {
    will-change: transform;
    contain: strict;
    content-visibility: auto;
    contain-intrinsic-size: 0 500px;
}

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
}
EOD;
        return $css;
    }

    private function generar_css_frontend_optimizado() {
        $css = <<<EOD
/* IGIS Flowise Bot Frontend - Optimizado */
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

@media (prefers-color-scheme: dark) {
    .flowise-chatbot-button {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
}

.flowise-embed {
    contain: layout style paint;
    content-visibility: auto;
}
EOD;
        return $css;
    }

    private function crear_js_optimizado() {
        $js_admin_contenido = $this->generar_js_admin_optimizado();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/admin.js', $js_admin_contenido);
        
        $js_frontend_contenido = $this->generar_js_frontend_optimizado();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/frontend.js', $js_frontend_contenido);
    }

    private function generar_js_admin_optimizado() {
        $js = <<<'EOD'
// IGIS Flowise Bot Admin - Optimizado para WordPress 6.8+
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
            this.elementos.pestanas.on('click', this.manejarCambioPestana.bind(this));
            this.elementos.vistaPrevia.on('click', this.mostrarVistaPrevia.bind(this));
            $(document).on('click', '.close-preview', this.cerrarVistaPrevia.bind(this));
            $(document).on('click', '.upload-media-button', this.abrirMediaUploader.bind(this));
        },

        inicializarComponentes() {
            this.elementos.selectoresColor.wpColorPicker();
            this.mostrarPestanaActiva();
        },

        configurarTemaOscuro() {
            const temaOscuro = this.detectarTemaOscuro();
            if (temaOscuro) {
                $('body').addClass('igis-tema-oscuro');
            }
        },

        detectarTemaOscuro() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return true;
            }
            return $('body').hasClass('admin-color-dark');
        },

        optimizarPerformance() {
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            $(entry.target).addClass('igis-performance-optimized');
                        }
                    });
                });
                $('.settings-section').each(function() {
                    observer.observe(this);
                });
            }
        },

        manejarCambioPestana(e) {
            e.preventDefault();
            const pestana = $(e.currentTarget);
            const seccion = pestana.attr('href').substring(1);
            
            if (pestana.hasClass('nav-tab-active')) return;
            
            this.elementos.pestanas.removeClass('nav-tab-active');
            this.elementos.secciones.removeClass('active').hide();
            
            pestana.addClass('nav-tab-active');
            $('#section-' + seccion).addClass('active').fadeIn(300);
        },

        mostrarPestanaActiva() {
            const hash = window.location.hash.substring(1) || 'general';
            $('.nav-tab-wrapper a[href="#' + hash + '"]').trigger('click');
        },

        mostrarVistaPrevia() {
            this.elementos.modalPrevia.fadeIn(300);
        },

        cerrarVistaPrevia() {
            this.elementos.modalPrevia.fadeOut(300);
        },

        abrirMediaUploader(e) {
            e.preventDefault();
            const boton = $(e.currentTarget);
            const campo = boton.siblings('input');
            
            const mediaUploader = wp.media({
                title: 'Seleccionar Archivo',
                button: { text: 'Usar este archivo' },
                multiple: false
            });

            mediaUploader.on('select', function() {
                const adjunto = mediaUploader.state().get('selection').first().toJSON();
                campo.val(adjunto.url);
            });

            mediaUploader.open();
        }
    };

    $(document).ready(() => {
        IGISBotAdmin.init();
    });

})(jQuery);
EOD;
        return $js;
    }

    private function generar_js_frontend_optimizado() {
        $js = <<<'EOD'
// IGIS Flowise Bot Frontend - Optimizado para WordPress 6.8+
(function() {
    'use strict';

    const CONFIG = {
        version: '1.2.0',
        cache: {
            sessionKey: 'igis_bot_session_' + Date.now(),
            configKey: 'igis_bot_config_cache'
        }
    };

    class IGISBotManager {
        constructor() {
            this.sessionId = this.obtenerOCrearSessionId();
            this.conversacionId = null;
            this.cache = new Map();
            
            this.init();
        }

        init() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.inicializar());
            } else {
                this.inicializar();
            }
        }

        inicializar() {
            this.configurarEventosBot();
            this.configurarTema();
        }

        obtenerOCrearSessionId() {
            let sessionId = localStorage.getItem('igis_bot_session_id');
            if (!sessionId) {
                sessionId = 'sess_' + this.generarId() + '_' + Date.now();
                localStorage.setItem('igis_bot_session_id', sessionId);
            }
            return sessionId;
        }

        generarId() {
            return Math.random().toString(36).substring(2, 15) + 
                   Math.random().toString(36).substring(2, 15);
        }

        configurarEventosBot() {
            document.addEventListener('flowise:ready', () => {
                console.log('IGIS Bot v' + CONFIG.version + ' listo');
            });
        }

        configurarTema() {
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                this.aplicarTema(mediaQuery.matches ? 'dark' : 'light');
                mediaQuery.addEventListener('change', (e) => {
                    this.aplicarTema(e.matches ? 'dark' : 'light');
                });
            }
        }

        aplicarTema(tema) {
            document.documentElement.setAttribute('data-igis-theme', tema);
        }
    }

    const botManager = new IGISBotManager();

    window.IGISBot = {
        version: CONFIG.version,
        manager: botManager
    };

})();
EOD;
        return $js;
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
        if (isset($this->cache_manager)) {
            $this->cache_manager->limpiar_todo();
        }
        
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
            $hints[] = 'https://cdn.jsdelivr.net';
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
        if (isset($this->detector_tema_oscuro)) {
            $tema_oscuro = $this->detector_tema_oscuro->detectar();
            
            if ($tema_oscuro) {
                echo '<script>document.documentElement.setAttribute("data-theme", "dark");</script>';
            }
        }
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
        if (isset($this->detector_tema_oscuro) && $this->detector_tema_oscuro->detectar()) {
            $clases[] = 'igis-bot-tema-oscuro';
        }
        
        if ($this->opciones['performance_mode']) {
            $clases[] = 'igis-bot-performance-mode';
        }
        
        return $clases;
    }

    private function configurar_cache_transients() {
        set_transient('igis_bot_version_cache', IGIS_BOT_VERSION, DAY_IN_SECONDS);
        
        $configuracion_cache = array(
            'version' => IGIS_BOT_VERSION,
            'timestamp' => current_time('timestamp'),
            'opciones_hash' => md5(serialize($this->opciones))
        );
        
        set_transient('igis_bot_configuracion_cache', $configuracion_cache, IGIS_BOT_CACHE_TIME);
    }

    private function deberia_mostrar_bot() {
        if (is_admin()) {
            return false;
        }

        if (empty($this->opciones['chatflow_id']) || empty($this->opciones['api_host'])) {
            return false;
        }

        $paginas_mostrar = $this->opciones['display_pages'] ?? array('all');
        
        if (in_array('all', $paginas_mostrar)) {
            return true;
        }

        if (is_front_page() && in_array('front_page', $paginas_mostrar)) {
            return true;
        }

        if (is_page() && in_array(get_the_ID(), $paginas_mostrar)) {
            return true;
        }

        return false;
    }

    public function renderizar_bot() {
        if (!$this->deberia_mostrar_bot()) {
            return;
        }

        $configuracion_bot = $this->generar_configuracion_bot();
        $this->generar_script_bot($configuracion_bot);
    }

    private function generar_configuracion_bot() {
        $configuracion_base = array(
            'chatflowid' => $this->opciones['chatflow_id'],
            'apiHost' => $this->opciones['api_host']
        );

        if (!empty($this->opciones['api_key'])) {
            $configuracion_base['apiKey'] = $this->opciones['api_key'];
        }

        $configuracion_tema = array(
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
        
        return array_merge($configuracion_base, $configuracion_tema);
    }

    private function generar_script_bot($configuracion) {
        ?>
        <script type="module" id="igis-bot-embed">
            import Chatbot from "https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js";
            
            const configuracionBot = <?php echo wp_json_encode($configuracion); ?>;
            
            if (window.requestIdleCallback) {
                window.requestIdleCallback(() => {
                    Chatbot.init(configuracionBot);
                });
            } else {
                setTimeout(() => {
                    Chatbot.init(configuracionBot);
                }, 0);
            }
        </script>
        <?php
    }

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
    }

    public function registrar_configuraciones() {
        register_setting('igis_bot_options', 'igis_bot_options', array($this, 'sanitizar_opciones'));
    }

    public function renderizar_pagina_admin() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        echo '<div class="wrap igis-bot-admin-wrap">';
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
        echo '<p>Plugin IGIS Flowise Bot configurado correctamente.</p>';
        echo '</div>';
    }

    public function encolar_assets_admin($hook) {
        if (strpos($hook, 'igis-flowise-bot') === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

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
                default:
                    $sanitized[$key] = sanitize_textarea_field($value);
                    break;
            }
        }

        wp_cache_delete('igis_bot_opciones_' . md5(IGIS_BOT_VERSION), 'igis_bot');
        
        return $sanitized;
    }

    private function registrar_manejadores_ajax() {
        // Los manejadores AJAX se registrarán aquí si es necesario
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
}

/**
 * Clase para optimizaciones de performance
 */
class IGIS_Optimizador_Performance {
    
    public function __construct() {
        add_action('wp_head', array($this, 'agregar_optimizaciones_head'), 1);
    }

    public function agregar_optimizaciones_head() {
        echo '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">';
        echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">';
    }
}

/**
 * Clase para detectar tema oscuro
 */
class IGIS_Detector_Tema_Oscuro {
    
    public function detectar() {
        if (is_admin()) {
            $esquema_color = get_user_option('admin_color');
            $temas_oscuros = array('midnight', 'ectoplasm', 'coffee', 'blue');
            return in_array($esquema_color, $temas_oscuros);
        }
        return false;
    }
}

/**
 * Clase para precargar recursos
 */
class IGIS_Precargador_Recursos {
    public function __construct() {
        // Configuración de precarga
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
