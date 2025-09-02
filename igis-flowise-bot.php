<?php
/**
 * Plugin Name: IGIS Flowise Bot
 * Plugin URI: https://www.infraestructuragis.com/
 * Description: Integra el chatbot de Flowise en tu sitio WordPress con opciones configurables avanzadas y optimizaciones para WordPress 6.8+
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
    private static $instance = null;
    private $options;
    private $cache_manager;
    private $performance_optimizer;
    private $dark_theme_detector;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->verify_compatibility();
        $this->options = get_option('igis_bot_options', array());
        $this->initialize_components();
        
        // Crear estructura de carpetas si no existe
        $this->create_directories();
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_footer', array($this, 'render_bot'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Optimizaciones WordPress 6.8+
        add_action('wp_head', array($this, 'add_performance_optimizations'), 1);
        add_action('wp_head', array($this, 'add_dark_theme_support'));
        add_filter('wp_resource_hints', array($this, 'add_resource_hints'), 10, 2);
        
        // Registrar manejadores AJAX
        $this->register_ajax_handlers();
    }

    private function verify_compatibility() {
        if (version_compare(get_bloginfo('version'), IGIS_BOT_MIN_WP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'wp_version_notice'));
            return;
        }

        if (version_compare(PHP_VERSION, IGIS_BOT_MIN_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'php_version_notice'));
            return;
        }
    }

    public function wp_version_notice() {
        echo '<div class="notice notice-error"><p>';
        printf(
            __('IGIS Flowise Bot requiere WordPress %s o superior. Tu versión actual es %s.', 'igis-flowise-bot'),
            IGIS_BOT_MIN_WP_VERSION,
            get_bloginfo('version')
        );
        echo '</p></div>';
    }

    public function php_version_notice() {
        echo '<div class="notice notice-error"><p>';
        printf(
            __('IGIS Flowise Bot requiere PHP %s o superior. Tu versión actual es %s.', 'igis-flowise-bot'),
            IGIS_BOT_MIN_PHP_VERSION,
            PHP_VERSION
        );
        echo '</p></div>';
    }

    private function initialize_components() {
        $this->cache_manager = new IGIS_Cache_Manager();
        $this->performance_optimizer = new IGIS_Performance_Optimizer();
        $this->dark_theme_detector = new IGIS_Dark_Theme_Detector();
    }
    
    private function create_directories() {
        // Crear directorio de assets si no existe
        $assets_dir = IGIS_BOT_PLUGIN_DIR . 'assets';
        if (!file_exists($assets_dir)) {
            mkdir($assets_dir, 0755, true);
            mkdir($assets_dir . '/css', 0755, true);
            mkdir($assets_dir . '/js', 0755, true);
            
            // Copiar archivos CSS y JS iniciales
            $this->create_initial_assets();
        }
    }
    
    private function create_initial_assets() {
        // Crear archivo CSS de administración con optimizaciones
        $admin_css = $this->get_optimized_admin_css();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/css/admin.css', $admin_css);
        
        // Crear archivo JS de administración optimizado
        $admin_js = $this->get_optimized_admin_js();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/admin.js', $admin_js);
        
        // Crear archivo CSS del frontend optimizado
        $frontend_css = $this->get_optimized_frontend_css();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/css/frontend.css', $frontend_css);
        
        // Crear archivo JS del frontend optimizado
        $frontend_js = $this->get_optimized_frontend_js();
        file_put_contents(IGIS_BOT_PLUGIN_DIR . 'assets/js/frontend.js', $frontend_js);
    }

    private function get_optimized_admin_css() {
        return <<<'CSS'
/**
 * Estilos optimizados para la administración del plugin IGIS Flowise Bot - WordPress 6.8+
 */

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
}

/* Optimizaciones de performance */
.igis-bot-admin-wrap * {
    box-sizing: border-box;
}

.igis-bot-admin-wrap {
    margin: 20px;
    contain: layout style;
}

.igis-bot-admin-wrap .nav-tab-wrapper {
    margin-bottom: 20px;
    border-bottom: 2px solid var(--igis-border-light);
}

/* Navegación por pestañas mejorada */
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
    background-color: var(--igis-primary) !important;
    color: white !important;
    border-bottom-color: var(--igis-primary) !important;
}

/* Secciones de configuración */
.settings-section {
    display: none;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: var(--igis-shadow);
    margin-bottom: 20px;
    animation: fadeIn 0.3s ease-out;
}

.settings-section.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Formularios y campos mejorados */
.form-table th {
    padding: 20px 15px;
    width: 220px;
    vertical-align: top;
    font-weight: 600;
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
    width: 100%;
    max-width: 400px;
}

input[type='text']:focus,
input[type='number']:focus,
select:focus,
textarea:focus {
    border-color: var(--igis-primary);
    box-shadow: 0 0 0 3px rgba(59, 129, 246, 0.1);
    outline: none;
}

/* Color picker mejorado */
.color-picker-wrapper {
    display: inline-block;
    vertical-align: middle;
}

.upload-media-button {
    margin-left: 10px !important;
    border-radius: 6px !important;
}

.media-preview {
    margin-top: 10px;
    max-width: 150px;
    max-height: 150px;
    overflow: hidden;
    border: 2px solid var(--igis-border-light);
    padding: 5px;
    border-radius: 8px;
}

.media-preview img {
    max-width: 100%;
    height: auto;
}

/* Botones modernos */
.button-primary,
.button-secondary {
    border-radius: 8px !important;
    padding: 12px 24px !important;
    font-weight: 600 !important;
    transition: var(--igis-transition) !important;
    border: none !important;
}

.button-primary {
    background-color: var(--igis-primary) !important;
    color: white !important;
}

.button-primary:hover {
    background-color: var(--igis-primary-hover) !important;
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
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
}

.bot-preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background-color: var(--igis-primary);
    color: white;
    border-radius: 16px 16px 0 0;
}

.bot-preview-header h3 {
    margin: 0;
    font-size: 18px;
}

.close-preview {
    font-size: 24px;
    cursor: pointer;
    color: white;
    background: none;
    border: none;
    padding: 0;
    margin: 0;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.close-preview:hover {
    opacity: 1;
}

.bot-preview-content {
    background: white;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    width: 90%;
    max-width: 1200px;
    height: 90%;
    max-height: 800px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* Estadísticas mejoradas */
.igis-bot-stats-wrap {
    margin: 20px;
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
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.stats-card h3 {
    margin-top: 0;
    color: var(--igis-secondary);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

/* Tema oscuro */
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

    input[type='text'],
    input[type='number'],
    select,
    textarea {
        background-color: #374151;
        border-color: var(--igis-border-dark);
        color: var(--igis-text-dark);
    }
}

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

/* Responsive */
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

    input[type='text'],
    input[type='number'],
    select,
    textarea {
        max-width: 100%;
    }
}

/* Performance optimizations */
.performance-optimized {
    will-change: transform;
    contain: layout style paint;
}

/* Animaciones adicionales */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

.slide-in {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { 
        transform: translateY(20px); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}
CSS;
    }

    private function get_optimized_admin_js() {
        return <<<'JS'
// IGIS Flowise Bot Admin - Optimizado para WordPress 6.8+
jQuery(document).ready(function($) {
    'use strict';

    // Inicializar los selectores de color
    $('.color-picker').wpColorPicker({
        change: function(event, ui) {
            updatePreview();
        }
    });

    // Performance optimizations
    const performanceObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                $(entry.target).addClass('performance-optimized');
            }
        });
    });

    $('.settings-section').each(function() {
        performanceObserver.observe(this);
    });

    // Detectar tema oscuro
    function detectDarkTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return true;
        }
        return $('body').hasClass('admin-color-dark') || $('body').hasClass('admin-color-scheme-dark');
    }

    // Aplicar tema oscuro si es necesario
    if (detectDarkTheme()) {
        $('body').addClass('igis-dark-theme');
    }

    // Manejar la navegación por pestañas
    $('.nav-tab-wrapper a').on('click', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var targetSection = $this.attr('href').substring(1);
        
        // Remover clase activa de todas las pestañas
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        
        // Agregar clase activa a la pestaña actual
        $this.addClass('nav-tab-active');
        
        // Ocultar todas las secciones
        $('.settings-section').removeClass('active').hide();
        
        // Mostrar la sección correspondiente con animación
        $('#section-' + targetSection).addClass('active').fadeIn(300);
        
        // Actualizar el hash de la URL
        if (history.replaceState) {
            history.replaceState(null, null, '#' + targetSection);
        }
    });

    // Mostrar la pestaña activa basada en el hash de la URL
    function showActiveTab() {
        var activeSection = window.location.hash.substring(1);
        
        if (activeSection && $('#section-' + activeSection).length) {
            $('.nav-tab-wrapper a[href="#' + activeSection + '"]').click();
        } else {
            // Por defecto, mostrar la primera pestaña
            $('.nav-tab-wrapper a:first').click();
        }
    }
    
    showActiveTab();

    // Media Uploader para imágenes y sonidos
    $(document).on('click', '.upload-media-button', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var targetField = button.data('target');
        var inputField = $('input[name="igis_bot_options[' + targetField + ']"]');
        
        var mediaUploader = wp.media({
            title: 'Seleccionar Archivo',
            button: {
                text: 'Usar este archivo'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            
            // Actualizar la vista previa
            var previewContainer = button.siblings('.media-preview');
            if (previewContainer.length) {
                if (attachment.type === 'image') {
                    previewContainer.html('<img src="' + attachment.url + '" alt="Preview" style="max-width:100px; max-height:100px;" />');
                } else {
                    previewContainer.html('<span class="file-preview">' + attachment.filename + '</span>');
                }
            }
        });

        mediaUploader.open();
    });

    // Vista previa del bot
    $('.preview-bot').on('click', function(e) {
        e.preventDefault();
        $('#bot-preview').fadeIn(300);
        renderBotPreview();
    });
    
    $('.close-preview, #bot-preview').on('click', function(e) {
        if (e.target === this) {
            $('#bot-preview').fadeOut(300);
        }
    });
    
    function renderBotPreview() {
        var options = collectFormOptions();
        var previewContent = $('.bot-preview-content');
        
        var previewHTML = '<div class="bot-preview-header">' +
            '<h3>Vista Previa del Bot</h3>' +
            '<button class="close-preview">&times;</button>' +
        '</div>' +
        '<div class="preview-container" style="position: relative; height: 500px; background: #f5f5f5; padding: 20px; overflow: hidden;">' +
            '<div class="preview-chatbot-button" style="' +
                'position: absolute;' +
                'right: ' + (options.button_position_right || 20) + 'px;' +
                'bottom: ' + (options.button_position_bottom || 20) + 'px;' +
                'width: ' + (options.button_size || 48) + 'px;' +
                'height: ' + (options.button_size || 48) + 'px;' +
                'border-radius: 50%;' +
                'background: ' + (options.button_color || '#3B81F6') + ';' +
                'display: flex;' +
                'align-items: center;' +
                'justify-content: center;' +
                'cursor: pointer;' +
                'box-shadow: 0 4px 12px rgba(0,0,0,0.15);' +
                'transition: transform 0.3s ease;' +
            '">' +
                '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                    '<path d="M8 12H16M8 8H16M8 16H12M6 20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
                '</svg>' +
            '</div>';
            
        if (options.show_tooltip) {
            previewHTML += '<div class="preview-tooltip" style="' +
                'position: absolute;' +
                'right: ' + (parseInt(options.button_position_right || 20) + parseInt(options.button_size || 48) + 10) + 'px;' +
                'bottom: ' + (parseInt(options.button_position_bottom || 20) + parseInt(options.button_size || 48)/2 - 15) + 'px;' +
                'background: ' + (options.tooltip_bg_color || 'black') + ';' +
                'color: ' + (options.tooltip_text_color || 'white') + ';' +
                'padding: 8px 12px;' +
                'border-radius: 8px;' +
                'font-size: ' + (options.tooltip_font_size || 14) + 'px;' +
                'white-space: nowrap;' +
                'box-shadow: 0 4px 12px rgba(0,0,0,0.15);' +
            '">' + (options.tooltip_message || 'Hola! ¿Cómo puedo ayudarte?') + '</div>';
        }
        
        previewHTML += '</div>';
        
        previewContent.html(previewHTML);
        
        // Agregar interactividad
        $('.preview-chatbot-button').on('click', function() {
            $(this).css('transform', 'scale(0.95)');
            setTimeout(function() {
                $('.preview-chatbot-button').css('transform', 'scale(1)');
            }, 150);
        });
    }
    
    function collectFormOptions() {
        var options = {};
        
        $('form#igis-bot-settings-form').find('input, select, textarea').each(function() {
            var input = $(this);
            var name = input.attr('name');
            
            if (name && name.startsWith('igis_bot_options[') && name.endsWith(']')) {
                var optionName = name.substring(16, name.length - 1);
                var value;
                
                if (input.is(':checkbox')) {
                    value = input.is(':checked');
                } else if (input.is('select[multiple]')) {
                    value = input.val() || [];
                } else {
                    value = input.val();
                }
                
                options[optionName] = value;
            }
        });
        
        return options;
    }

    function updatePreview() {
        // Actualizar vista previa cuando cambien los colores
        if ($('#bot-preview').is(':visible')) {
            renderBotPreview();
        }
    }

    // Debounced auto-save
    var autoSaveTimeout;
    $('form#igis-bot-settings-form').on('input change', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Auto-guardar configuración en localStorage
            var formData = $('form#igis-bot-settings-form').serialize();
            localStorage.setItem('igis_bot_temp_config', formData);
        }, 2000);
    });

    // Funcionalidad para estadísticas si existe la página
    if ($('.igis-bot-stats-wrap').length) {
        loadStatsData();
    }
    
    function loadStatsData() {
        if (typeof igisBotAdmin !== 'undefined' && igisBotAdmin.ajaxUrl) {
            $.ajax({
                url: igisBotAdmin.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'igis_bot_get_stats',
                    nonce: igisBotAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        renderCharts(response.data);
                    }
                },
                error: function() {
                    console.log('Error loading stats data');
                }
            });
        }
    }

    function renderCharts(data) {
        // Si Chart.js está disponible, renderizar gráficos
        if (typeof Chart !== 'undefined') {
            if ($('#conversationsChart').length && data.conversations_chart) {
                new Chart($('#conversationsChart')[0].getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.conversations_chart.map(function(item) { return item.date; }),
                        datasets: [{
                            label: 'Conversaciones',
                            data: data.conversations_chart.map(function(item) { return item.count; }),
                            backgroundColor: 'rgba(59, 129, 246, 0.2)',
                            borderColor: 'rgba(59, 129, 246, 1)',
                            borderWidth: 2,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Conversaciones por día'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    }

    // Monitoreo de cambios de tema en tiempo real
    if (window.matchMedia) {
        var darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
        darkModeQuery.addListener(function(e) {
            if (e.matches) {
                $('body').addClass('igis-dark-theme');
            } else {
                $('body').removeClass('igis-dark-theme');
            }
        });
    }
});
JS;
    }

    private function get_optimized_frontend_css() {
        return <<<'CSS'
/* IGIS Flowise Bot Frontend - Optimizado para WordPress 6.8+ */
:root {
    --igis-bot-primary: #3B81F6;
    --igis-bot-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.flowise-chatbot-button {
    transition: var(--igis-bot-transition) !important;
    will-change: transform;
    contain: layout style paint;
}

.flowise-chatbot-button:hover {
    transform: scale(1.05) !important;
}

/* Optimización para tema oscuro */
@media (prefers-color-scheme: dark) {
    .flowise-chatbot-button {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
    }
    
    .flowise-chatwindow {
        background-color: #1a1a1a !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
}

/* Performance optimizations */
.flowise-embed {
    contain: layout style paint;
    content-visibility: auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .flowise-chatwindow {
        width: 100vw !important;
        height: 100vh !important;
        max-width: none !important;
        max-height: none !important;
        border-radius: 0 !important;
    }
}
CSS;
    }

    private function get_optimized_frontend_js() {
        return <<<'JS'
jQuery(document).ready(function($) {
    'use strict';

    // Generador de IDs de sesión optimizado
    function generateSessionId() {
        return 'igis_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now();
    }
    
    // Obtener ID de sesión existente o crear uno nuevo
    var sessionId = localStorage.getItem('igis_bot_session_id');
    if (!sessionId) {
        sessionId = generateSessionId();
        localStorage.setItem('igis_bot_session_id', sessionId);
    }

    // Performance optimization: usar requestIdleCallback cuando esté disponible
    function executeWhenIdle(callback) {
        if (window.requestIdleCallback) {
            window.requestIdleCallback(callback);
        } else {
            setTimeout(callback, 1);
        }
    }

    // Configurar tema oscuro automático
    function setupDarkThemeSupport() {
        if (window.matchMedia) {
            var darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            function applyTheme(isDark) {
                document.documentElement.setAttribute('data-igis-theme', isDark ? 'dark' : 'light');
            }
            
            // Aplicar tema inicial
            applyTheme(darkModeQuery.matches);
            
            // Escuchar cambios
            darkModeQuery.addListener(function(e) {
                applyTheme(e.matches);
            });
        }
    }

    // Inicializar soporte de tema oscuro
    setupDarkThemeSupport();

    // Registrar eventos del chatbot cuando esté disponible
    executeWhenIdle(function() {
        // Registrar el inicio de la conversación cuando se abra el chatbot
        $(document).on('click', '.flowise-chatbot-button', function() {
            if (typeof igisBotFrontend !== 'undefined' && igisBotFrontend.ajaxUrl) {
                $.ajax({
                    url: igisBotFrontend.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'igis_bot_log_conversation',
                        nonce: igisBotFrontend.nonce,
                        session_id: sessionId,
                        status: 'active'
                    },
                    success: function(response) {
                        if (response.success) {
                            localStorage.setItem('igis_bot_conversation_id', response.data.conversation_id);
                        }
                    }
                });
            }
        });

        // Escuchar eventos nativos de Flowise si están disponibles
        if (typeof document.addEventListener === 'function') {
            document.addEventListener('flowise:chatOpen', function() {
                console.log('IGIS Bot: Chat opened');
                // Trigger evento personalizado
                $(document).trigger('igis_bot:opened', {sessionId: sessionId});
            });

            document.addEventListener('flowise:chatClose', function() {
                console.log('IGIS Bot: Chat closed');
                $(document).trigger('igis_bot:closed', {sessionId: sessionId});
            });

            document.addEventListener('flowise:messageSubmitted', function(e) {
                console.log('IGIS Bot: Message submitted', e.detail);
                $(document).trigger('igis_bot:message_sent', {
                    message: e.detail.message,
                    sessionId: sessionId
                });
            });

            document.addEventListener('flowise:messageReceived', function(e) {
                console.log('IGIS Bot: Message received', e.detail);
                $(document).trigger('igis_bot:message_received', {
                    message: e.detail.message,
                    sessionId: sessionId
                });
            });
        }
    });

    // API pública para desarrolladores
    window.IGISBot = {
        version: '1.2.0',
        sessionId: sessionId,
        
        // Métodos públicos
        getSessionId: function() {
            return sessionId;
        },
        
        openChat: function() {
            $('.flowise-chatbot-button').trigger('click');
        },
        
        // Event tracking personalizado
        trackEvent: function(eventName, eventData) {
            $(document).trigger('igis_bot:custom_event', {
                event: eventName,
                data: eventData,
                sessionId: sessionId
            });
        }
    };

    // Optimización de performance: lazy loading de recursos
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    // Precargar recursos cuando el chatbot esté cerca del viewport
                    var chatbotButton = entry.target;
                    if (chatbotButton.classList.contains('flowise-chatbot-button')) {
                        // Precargar recursos adicionales si es necesario
                        observer.unobserve(chatbotButton);
                    }
                }
            });
        }, {
            rootMargin: '100px'
        });

        // Observar el botón del chatbot cuando se cree
        executeWhenIdle(function() {
            var checkForButton = setInterval(function() {
                var button = document.querySelector('.flowise-chatbot-button');
                if (button) {
                    observer.observe(button);
                    clearInterval(checkForButton);
                }
            }, 100);
            
            // Limpiar el intervalo después de 10 segundos si no se encuentra el botón
            setTimeout(function() {
                clearInterval(checkForButton);
            }, 10000);
        });
    }
});
JS;
    }

    public function activate() {
        $default_options = array(
            // Configuración General
            'chatflow_id' => '',
            'api_host' => '',
            'api_key' => '',
            
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
            'auto_open' => false,
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
            'enable_send_sound' => false,
            'enable_receive_sound' => false,
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
            
            // Configuración Avanzada (Nuevas características WordPress 6.8+)
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
            'webhook_events' => array(),
            'performance_mode' => true,
            'enable_dark_theme' => true,
            'preload_resources' => true
        );
        
        if (!get_option('igis_bot_options')) {
            add_option('igis_bot_options', $default_options);
        }
        
        // Crear tablas de base de datos
        $this->create_database_tables();
        
        // Configurar cache
        $this->setup_cache();
    }
    
    private function create_database_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla de conversaciones con optimizaciones
        $table_name = $wpdb->prefix . 'igis_bot_conversations';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(64) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            started_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ended_at datetime DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY session_id (session_id),
            KEY status (status),
            KEY started_at (started_at)
        ) $charset_collate;";
        
        // Tabla de mensajes con índices optimizados
        $messages_table = $wpdb->prefix . 'igis_bot_messages';
        $sql .= "CREATE TABLE IF NOT EXISTS $messages_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            conversation_id bigint(20) NOT NULL,
            message longtext NOT NULL,
            type varchar(10) NOT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            metadata longtext DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY conversation_id (conversation_id),
            KEY type (type),
            KEY timestamp (timestamp),
            FOREIGN KEY (conversation_id) REFERENCES $table_name(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // Tabla de analytics mejorada
        $analytics_table = $wpdb->prefix . 'igis_bot_analytics';
        $sql .= "CREATE TABLE IF NOT EXISTS $analytics_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data longtext DEFAULT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            session_id varchar(64) DEFAULT NULL,
            user_id bigint(20) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY event_type (event_type),
            KEY timestamp (timestamp),
            KEY session_id (session_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function setup_cache() {
        // Configurar WordPress object cache si está disponible
        if (function_exists('wp_cache_add_global_groups')) {
            wp_cache_add_global_groups(array('igis_bot'));
        }
        
        // Configurar transients para cache de configuración
        set_transient('igis_bot_version', IGIS_BOT_VERSION, DAY_IN_SECONDS);
    }

    public function deactivate() {
        // Limpiar cache al desactivar
        if (isset($this->cache_manager)) {
            $this->cache_manager->clear_all();
        }
        
        delete_transient('igis_bot_version');
        
        if (get_option('igis_bot_delete_data')) {
            delete_option('igis_bot_options');
        }
    }

    // Optimizaciones para WordPress 6.8+
    public function add_performance_optimizations() {
        if (!$this->should_display_bot()) {
            return;
        }

        // DNS prefetch para recursos externos
        echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">';
        
        // Preconnect para API host si está configurado
        $api_host = $this->options['api_host'] ?? '';
        if (!empty($api_host)) {
            echo '<link rel="preconnect" href="' . esc_url($api_host) . '" crossorigin>';
        }
        
        // Resource hints adicionales
        if (isset($this->options['preload_resources']) && $this->options['preload_resources']) {
            echo '<link rel="modulepreload" href="https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js">';
        }
    }

    public function add_dark_theme_support() {
        if (!$this->should_display_bot()) {
            return;
        }

        // Detectar soporte de tema oscuro
        if (isset($this->options['enable_dark_theme']) && $this->options['enable_dark_theme']) {
            $dark_mode_css = "
            <style id='igis-bot-dark-theme'>
            @media (prefers-color-scheme: dark) {
                :root {
                    --igis-chatbot-bg: #1a1a1a;
                    --igis-chatbot-text: #ffffff;
                    --igis-chatbot-border: #404040;
                }
            }
            </style>
            ";
            echo $dark_mode_css;
        }

        // Variables CSS dinámicas
        $css_vars = "
        <style id='igis-bot-css-vars'>
        :root {
            --igis-bot-primary: " . ($this->options['button_color'] ?? '#3B81F6') . ";
            --igis-bot-window-width: " . ($this->options['window_width'] ?? '400') . "px;
            --igis-bot-window-height: " . ($this->options['window_height'] ?? '700') . "px;
        }
        </style>
        ";
        echo $css_vars;
    }

    public function add_resource_hints($hints, $relation_type) {
        if ('preconnect' === $relation_type && $this->should_display_bot()) {
            $api_host = $this->options['api_host'] ?? '';
            if (!empty($api_host)) {
                $hints[] = $api_host;
            }
            $hints[] = 'https://cdn.jsdelivr.net';
        }
        return $hints;
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
            'api_host' => array('text', 'API Host'),
            'api_key' => array('text', 'API Key (Opcional)')
        );

        $appearance_fields = array(
            'button_color' => array('color', 'Color del Botón'),
            'button_position_right' => array('number', 'Posición Derecha (px)'),
            'button_position_bottom' => array('number', 'Posición Inferior (px)'),
            'button_size' => array('number', 'Tamaño del Botón (px)'),
            'enable_drag' => array('checkbox', 'Permitir Arrastrar'),
            'icon_color' => array('color', 'Color del Icono'),
            'custom_icon' => array('media', 'Icono Personalizado')
        );

        $message_fields = array(
            'window_title' => array('text', 'Título de la Ventana'),
            'welcome_message' => array('textarea', 'Mensaje de Bienvenida'),
            'bot_message_bg_color' => array('color', 'Color de Fondo Bot'),
            'bot_message_text_color' => array('color', 'Color de Texto Bot'),
            'user_message_bg_color' => array('color', 'Color de Fondo Usuario'),
            'user_message_text_color' => array('color', 'Color de Texto Usuario'),
            'input_placeholder' => array('text', 'Placeholder del Input')
        );

        $display_fields = array(
            'display_pages' => array('multiselect', 'Mostrar en Páginas', $this->get_available_pages()),
            'auto_open' => array('checkbox', 'Auto Abrir'),
            'auto_open_delay' => array('number', 'Retraso de Auto Apertura (seg)'),
            'show_tooltip' => array('checkbox', 'Mostrar Tooltip'),
            'tooltip_message' => array('text', 'Mensaje del Tooltip'),
            'hide_on_mobile' => array('checkbox', 'Ocultar en Móvil')
        );

        $advanced_fields = array(
            'performance_mode' => array('checkbox', 'Modo Performance'),
            'enable_dark_theme' => array('checkbox', 'Soporte Tema Oscuro'),
            'preload_resources' => array('checkbox', 'Precargar Recursos'),
            'custom_css' => array('code', 'CSS Personalizado'),
            'custom_js' => array('code', 'JavaScript Personalizado'),
            'save_conversations' => array('checkbox', 'Guardar Conversaciones'),
            'analytics_enabled' => array('checkbox', 'Activar Analytics'),
            'debug_mode' => array('checkbox', 'Modo Debug')
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

            case 'textarea':
                echo '<textarea class="large-text" rows="3" name="igis_bot_options[' . esc_attr($field) . ']">' . esc_textarea($value) . '</textarea>';
                break;

            case 'media':
                echo '<div class="media-field">';
                echo '<input type="text" class="regular-text media-input" name="igis_bot_options[' . esc_attr($field) . ']" value="' . esc_attr($value) . '">';
                echo '<button type="button" class="button upload-media-button" data-target="' . esc_attr($field) . '">Seleccionar Archivo</button>';
                if (!empty($value)) {
                    echo '<div class="media-preview">';
                    echo '<img src="' . esc_url($value) . '" alt="Preview" style="max-width:100px; max-height:100px;">';
                    echo '</div>';
                }
                echo '</div>';
                break;

            case 'multiselect':
                if ($options) {
                    echo '<select multiple class="regular-text" name="igis_bot_options[' . esc_attr($field) . '][]" size="4">';
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
                    <div id="section-general" class="settings-section active">
                        <?php 
                        settings_fields('igis_bot_options');
                        do_settings_fields('igis-flowise-bot', 'igis_bot_general'); 
                        ?>
                    </div>

                    <div id="section-appearance" class="settings-section">
                        <?php do_settings_fields('igis-flowise-bot', 'igis_bot_appearance'); ?>
                    </div>

                    <div id="section-messages" class="settings-section">
                        <?php do_settings_fields('igis-flowise-bot', 'igis_bot_messages'); ?>
                    </div>

                    <div id="section-display" class="settings-section">
                        <?php do_settings_fields('igis-flowise-bot', 'igis_bot_display'); ?>
                    </div>

                    <div id="section-advanced" class="settings-section">
                        <?php do_settings_fields('igis-flowise-bot', 'igis_bot_advanced'); ?>
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
                <div class="bot-preview-content">
                    <!-- Vista previa se carga aquí -->
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
                    <h3><?php _e('Sesiones Activas', 'igis-flowise-bot'); ?></h3>
                    <div class="stats-value"><?php echo $this->get_active_sessions(); ?></div>
                </div>
            </div>

            <div class="stats-charts">
                <div class="chart-container">
                    <canvas id="conversationsChart"></canvas>
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
            
            <p><?php _e('Aquí se mostrarán las conversaciones guardadas cuando la función esté habilitada.', 'igis-flowise-bot'); ?></p>
        </div>
        <?php
    }

    public function register_ajax_handlers() {
        // Handlers para el frontend
        add_action('wp_ajax_igis_bot_log_conversation', array($this, 'log_conversation'));
        add_action('wp_ajax_nopriv_igis_bot_log_conversation', array($this, 'log_conversation'));
        
        // Handlers para el admin
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
        
        $wpdb->insert(
            $table,
            array(
                'user_id' => get_current_user_id(),
                'session_id' => $session_id,
                'status' => $status,
                'started_at' => current_time('mysql')
            )
        );
        
        wp_send_json_success(array('conversation_id' => $wpdb->insert_id));
    }
    
    public function get_stats() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $stats = array(
            'total_conversations' => $this->get_total_conversations(),
            'total_messages' => $this->get_total_messages(),
            'active_sessions' => $this->get_active_sessions()
        );
        
        wp_send_json_success($stats);
    }

    private function get_total_conversations() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'igis_bot_conversations';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return 0;
        }
        
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }

    private function get_total_messages() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'igis_bot_messages';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return 0;
        }
        
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }

    private function get_active_sessions() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'igis_bot_conversations';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return 0;
        }
        
        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} 
             WHERE status = 'active' 
             AND started_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        );
    }

    public function render_bot() {
        if (!$this->should_display_bot()) {
            return;
        }
        
        $options = $this->get_sanitized_options();
        
        // Verificar campos requeridos antes de renderizar
        if (empty($options['chatflow_id']) || empty($options['api_host'])) {
            if ($options['debug_mode'] && current_user_can('manage_options')) {
                echo '<!-- IGIS Bot: Configuración incompleta. Asegúrese de configurar chatflow_id y api_host -->';
            }
            return;
        }
        
        $bot_config = $this->generate_bot_config($options);
        $this->output_bot_script($bot_config);
        $this->output_custom_styles($options);
        $this->output_tracking_script($options);
    }

    private function generate_bot_config($options) {
        $config = array(
            'chatflowid' => $options['chatflow_id'],
            'apiHost' => $options['api_host']
        );

        // Agregar API key si está configurada
        if (!empty($options['api_key'])) {
            $config['apiKey'] = $options['api_key'];
        }
        
        $config['theme'] = array(
            'button' => array(
                'backgroundColor' => $options['button_color'],
                'right' => (int) $options['button_position_right'],
                'bottom' => (int) $options['button_position_bottom'],
                'size' => (int) $options['button_size'],
                'iconColor' => $options['icon_color'],
                'customIconSrc' => $options['custom_icon'],
                'dragable' => (bool) $options['enable_drag']
            ),
            'chatWindow' => array(
                'welcomeMessage' => $options['welcome_message'],
                'backgroundColor' => $options['window_background_color'],
                'height' => (int) $options['window_height'],
                'width' => (int) $options['window_width'],
                'fontSize' => (int) $options['font_size'],
                'title' => $options['window_title']
            ),
            'userMessage' => array(
                'backgroundColor' => $options['user_message_bg_color'],
                'textColor' => $options['user_message_text_color']
            ),
            'botMessage' => array(
                'backgroundColor' => $options['bot_message_bg_color'],
                'textColor' => $options['bot_message_text_color']
            ),
            'textInput' => array(
                'placeholder' => $options['input_placeholder'],
                'backgroundColor' => $options['input_bg_color'],
                'textColor' => $options['input_text_color'],
                'sendButtonColor' => $options['input_send_button_color']
            )
        );

        // Agregar tooltip si está habilitado
        if ($options['show_tooltip']) {
            $config['theme']['tooltip'] = array(
                'showTooltip' => true,
                'tooltipMessage' => $options['tooltip_message'],
                'tooltipBackgroundColor' => $options['tooltip_bg_color'],
                'tooltipTextColor' => $options['tooltip_text_color'],
                'tooltipFontSize' => (int) $options['tooltip_font_size']
            );
        }

        // Agregar auto-open si está configurado
        if ($options['auto_open']) {
            $config['theme']['button']['autoWindowOpen'] = array(
                'autoOpen' => true,
                'openDelay' => (int) $options['auto_open_delay'],
                'autoOpenOnMobile' => (bool) $options['auto_open_mobile']
            );
        }

        return $config;
    }

    private function output_bot_script($config) {
        ?>
        <script type="module" id="igis-bot-embed">
            import Chatbot from "https://cdn.jsdelivr.net/npm/flowise-embed/dist/web.js";
            
            const botConfig = <?php echo wp_json_encode($config); ?>;
            
            // Inicialización optimizada con performance
            if (<?php echo $this->options['performance_mode'] ? 'true' : 'false'; ?>) {
                if (window.requestIdleCallback) {
                    window.requestIdleCallback(() => {
                        Chatbot.init(botConfig);
                    });
                } else {
                    setTimeout(() => {
                        Chatbot.init(botConfig);
                    }, 0);
                }
            } else {
                Chatbot.init(botConfig);
            }

            // Debug mode
            <?php if ($this->options['debug_mode']): ?>
            console.log('IGIS Bot Debug Mode:', {
                version: '<?php echo IGIS_BOT_VERSION; ?>',
                config: botConfig
            });
            <?php endif; ?>
        </script>
        <?php
    }

    private function output_custom_styles($options) {
        if (!empty($options['custom_css'])) {
            echo '<style type="text/css" id="igis-bot-custom-css">';
            echo wp_strip_all_tags($options['custom_css']);
            echo '</style>';
        }
    }

    private function output_tracking_script($options) {
        if ($options['save_conversations'] || $options['analytics_enabled']) {
            ?>
            <script id="igis-bot-tracking">
            document.addEventListener('DOMContentLoaded', function() {
                // Tracking implementado en frontend.js
                console.log('IGIS Bot tracking initialized');
            });
            </script>
            <?php
        }
    }

    private function should_display_bot() {
        // Verificar si estamos en admin
        if (is_admin()) {
            return false;
        }

        $options = $this->options;
        
        // Verificar configuración básica
        if (empty($options['chatflow_id']) || empty($options['api_host'])) {
            return false;
        }

        // Verificar si estamos en modo móvil y está deshabilitado
        if (isset($options['hide_on_mobile']) && $options['hide_on_mobile'] && wp_is_mobile()) {
            return false;
        }

        // Verificar páginas de visualización
        $display_pages = isset($options['display_pages']) ? $options['display_pages'] : array('all');
        
        if (in_array('all', $display_pages)) {
            return true;
        }

        if (is_front_page() && in_array('front_page', $display_pages)) {
            return true;
        }

        if (is_page() && in_array(get_the_ID(), $display_pages)) {
            return true;
        }

        return false;
    }

    private function get_available_pages() {
        $pages = get_pages();
        $options = array(
            'all' => __('Todas las páginas', 'igis-flowise-bot'),
            'front_page' => __('Página de inicio', 'igis-flowise-bot'),
            'blog' => __('Página del blog', 'igis-flowise-bot')
        );

        foreach ($pages as $page) {
            $options[$page->ID] = $page->post_title;
        }

        return $options;
    }

    public function enqueue_admin_assets($hook) {
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

        // Chart.js para estadísticas
        if (strpos($hook, 'stats') !== false) {
            wp_enqueue_script(
                'chartjs',
                'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js',
                array(),
                '3.9.1',
                true
            );
        }

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
            'version' => IGIS_BOT_VERSION
        ));
    }

    public function enqueue_frontend_assets() {
        if (!$this->should_display_bot()) {
            return;
        }

        wp_enqueue_style(
            'igis-bot-frontend',
            IGIS_BOT_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            IGIS_BOT_VERSION
        );

        wp_enqueue_script(
            'igis-bot-frontend',
            IGIS_BOT_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            IGIS_BOT_VERSION,
            true
        );

        wp_localize_script('igis-bot-frontend', 'igisBotFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('igis_bot_frontend'),
            'version' => IGIS_BOT_VERSION
        ));
    }

    public function sanitize_options($input) {
        $sanitized = array();
        
        foreach ($input as $key => $value) {
            switch ($key) {
                // Campos de texto simples
                case 'chatflow_id':
                case 'api_key':
                case 'window_title':
                case 'welcome_message':
                case 'input_placeholder':
                case 'tooltip_message':
                    $sanitized[$key] = sanitize_text_field($value);
                    break;

                // URLs
                case 'api_host':
                case 'custom_icon':
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
                case 'auto_open_delay':
                    $sanitized[$key] = absint($value);
                    break;

                // Booleanos
                case 'enable_drag':
                case 'auto_open':
                case 'auto_open_mobile':
                case 'show_tooltip':
                case 'hide_on_mobile':
                case 'performance_mode':
                case 'enable_dark_theme':
                case 'preload_resources':
                case 'save_conversations':
                case 'analytics_enabled':
                case 'debug_mode':
                    $sanitized[$key] = (bool) $value;
                    break;

                // Arrays
                case 'display_pages':
                    $sanitized[$key] = is_array($value) ? array_map('sanitize_text_field', $value) : array();
                    break;

                // Código personalizado (con cuidado)
                case 'custom_css':
                    $sanitized[$key] = wp_strip_all_tags($value);
                    break;
                case 'custom_js':
                    // Permitir solo a administradores
                    if (current_user_can('manage_options')) {
                        $sanitized[$key] = $value;
                    } else {
                        $sanitized[$key] = '';
                    }
                    break;

                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        }

        // Limpiar cache después de guardar opciones
        if (isset($this->cache_manager)) {
            $this->cache_manager->clear_all();
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
            'api_key' => '',
            'button_color' => '#3B81F6',
            'button_position_right' => 20,
            'button_position_bottom' => 20,
            'button_size' => 48,
            'enable_drag' => true,
            'icon_color' => 'white',
            'custom_icon' => '',
            'window_title' => 'IGIS Bot',
            'welcome_message' => 'Hello! How can I help you today?',
            'window_height' => 700,
            'window_width' => 400,
            'window_background_color' => '#ffffff',
            'font_size' => 16,
            'bot_message_bg_color' => '#f7f8ff',
            'bot_message_text_color' => '#303235',
            'user_message_bg_color' => '#3B81F6',
            'user_message_text_color' => '#ffffff',
            'input_placeholder' => 'Type your question',
            'input_bg_color' => '#ffffff',
            'input_text_color' => '#303235',
            'input_send_button_color' => '#3B81F6',
            'show_tooltip' => true,
            'tooltip_message' => 'Hi There 👋!',
            'tooltip_bg_color' => 'black',
            'tooltip_text_color' => 'white',
            'tooltip_font_size' => 16,
            'auto_open' => false,
            'auto_open_delay' => 2,
            'auto_open_mobile' => false,
            'display_pages' => array('all'),
            'hide_on_mobile' => false,
            'performance_mode' => true,
            'enable_dark_theme' => true,
            'preload_resources' => true,
            'save_conversations' => false,
            'analytics_enabled' => false,
            'debug_mode' => false,
            'custom_css' => '',
            'custom_js' => ''
        );
    }
}

// Clases auxiliares optimizadas para WordPress 6.8+
class IGIS_Cache_Manager {
    private $cache_group = 'igis_bot';
    
    public function get($key, $default = false) {
        return wp_cache_get($key, $this->cache_group) ?: $default;
    }
    
    public function set($key, $data, $expiration = IGIS_BOT_CACHE_TIME) {
        return wp_cache_set($key, $data, $this->cache_group, $expiration);
    }
    
    public function delete($key) {
        return wp_cache_delete($key, $this->cache_group);
    }
    
    public function clear_all() {
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group($this->cache_group);
        }
    }
}

class IGIS_Performance_Optimizer {
    public function __construct() {
        add_action('wp_head', array($this, 'add_performance_hints'), 1);
    }
    
    public function add_performance_hints() {
        echo '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">';
        echo '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">';
    }
}

class IGIS_Dark_Theme_Detector {
    public function detect() {
        // Detectar tema WordPress admin
        if (is_admin()) {
            $color_scheme = get_user_option('admin_color');
            $dark_schemes = array('midnight', 'ectoplasm', 'coffee', 'blue');
            return in_array($color_scheme, $dark_schemes);
        }
        
        // Para frontend, se detecta con JavaScript
        return false;
    }
}

// Funciones de inicialización
function igis_flowise_bot_init() {
    return IGIS_Flowise_Bot::get_instance();
}

// Funciones auxiliares públicas
if (!function_exists('igis_bot_get_option')) {
    function igis_bot_get_option($key, $default = null) {
        $instance = IGIS_Flowise_Bot::get_instance();
        return isset($instance->options[$key]) ? $instance->options[$key] : $default;
    }
}

if (!function_exists('igis_bot_is_active')) {
    function igis_bot_is_active() {
        $chatflow_id = igis_bot_get_option('chatflow_id');
        $api_host = igis_bot_get_option('api_host');
        return !empty($chatflow_id) && !empty($api_host);
    }
}

// Inicializar el plugin
add_action('plugins_loaded', 'igis_flowise_bot_init');

// Compatibilidad con versiones anteriores
class_alias('IGIS_Flowise_Bot', 'IGISFlowiseBot');
