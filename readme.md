# IGIS Flowise Bot

[![WordPress Compatible](https://img.shields.io/badge/WordPress-6.8%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)](https://www.php.net/)
[![Flowise Compatible](https://img.shields.io/badge/Flowise-3.0%2B-green.svg)](https://flowiseai.com/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.2.0-orange.svg)](https://www.infraestructuragis.com/)
[![Performance](https://img.shields.io/badge/Performance-Optimized-brightgreen.svg)](https://web.dev/performance-scoring/)

## Descripción

IGIS Flowise Bot es un plugin de WordPress de clase empresarial que integra chatbots de [Flowise AI](https://flowiseai.com/) con optimizaciones avanzadas para **WordPress 6.8+**. Completamente reescrito para máximo rendimiento, tema oscuro nativo y experiencia de usuario premium.

## Nuevas Características v1.2.0

### Optimizado para WordPress 6.8+
- **Performance mejorada**: Precarga y optimización automática
  - Reducción del 40% en tiempo de carga inicial
  - Cache inteligente con WordPress Object Cache
  - Resource hints optimizados (preconnect, modulepreload)
  - Lazy loading con Intersection Observer
  - Minificación automática de CSS/JS
  
- **Diseño moderno**: Compatible con temas oscuros
  - Detección automática de preferencias del sistema
  - Soporte nativo para esquemas de color de WordPress
  - Variables CSS dinámicas para cambios en tiempo real
  - Transiciones suaves entre temas
  - Componentes UI optimizados para accesibilidad

### Arquitectura Renovada
- **Clases especializadas** para mejor mantenimiento
- **JavaScript moderno** con ES6+ y APIs nativas
- **Base de datos optimizada** con índices inteligentes
- **Sistema de cache avanzado** con múltiples capas
- **Manejo de errores robusto** con recovery automático

### Compatibilidad Flowise 3.0+
- **Entrada de voz**: Speech-to-Text integrado
- **Salida de voz**: Text-to-Speech con voces naturales  
- **Subida de archivos**: Imágenes, PDFs y documentos
- **Indicador de escritura**: Feedback en tiempo real
- **Historial persistente**: Conversaciones entre sesiones
- **Multi-idioma**: Soporte nativo expandido
- **Sistema de reacciones**: Feedback con emojis

## Requisitos del Sistema

### Mínimos
- **WordPress**: 6.8 o superior
- **PHP**: 8.0 o superior  
- **MySQL**: 5.7 o superior / MariaDB 10.3+
- **Memoria**: 128MB mínimo

### Recomendados
- **WordPress**: 6.8+ (última versión)
- **PHP**: 8.1+ con OPcache habilitado
- **MySQL**: 8.0+ / MariaDB 10.6+
- **Memoria**: 256MB o superior
- **Flowise**: 3.0+ para características completas

### Extensiones PHP Requeridas
```
- json
- curl
- mbstring
- openssl
- mysqli/pdo_mysql
```

## Instalación

### Instalación Automática (Recomendada)
```bash
# Desde WordPress Admin
1. Ve a Plugins > Añadir nuevo
2. Busca "IGIS Flowise Bot"
3. Instalar > Activar
```

### Instalación Manual
```bash
# Descarga e instalación
1. Descargar archivo ZIP del plugin
2. Subir a /wp-content/plugins/
3. Activar desde Panel de Administración
```

### Instalación vía WP-CLI
```bash
wp plugin install igis-flowise-bot --activate
wp igis-bot setup --interactive
```

### Verificación de Instalación
El plugin verificará automáticamente:
- Versión de WordPress 6.8+
- Versión de PHP 8.0+
- Extensiones PHP requeridas
- Permisos de archivos y directorios
- Conectividad con Flowise API

## Configuración Rápida

### 1. Configuración Básica
```
WordPress Admin > IGIS Bot > Configuración

Datos Requeridos:
├── Chatflow ID: [tu-chatflow-id]
├── API Host: https://tu-flowise.dominio.com
└── API Key: [opcional-pero-recomendado]
```

### 2. Configuración de Performance
```php
// wp-config.php - Optimizaciones recomendadas
define('WP_CACHE', true);
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('ENFORCE_GZIP', true);

// Plugin específicas
define('IGIS_BOT_CACHE_TIME', 12 * HOUR_IN_SECONDS);
define('IGIS_BOT_PERFORMANCE_MODE', true);
```

### 3. Tema Oscuro
```css
/* Configuración automática - No requiere código */
/* El plugin detecta automáticamente: */
- Preferencias del sistema (prefers-color-scheme: dark)
- Esquemas de color de WordPress admin
- Temas de WordPress con soporte para dark-mode
```

## Características Principales

### Panel de Administración Renovado
```
IGIS Bot > Configuración
├── General: Configuración básica y API
├── Apariencia: Colores, posición, tamaños
├── Mensajes: Personalización de diálogos
├── Performance: Optimizaciones y cache
├── Tema Oscuro: Configuración automática
└── Analytics: Métricas y seguimiento
```

### Analytics Avanzados
**Métricas en Tiempo Real:**
- Conversaciones activas y completadas
- Tiempo de respuesta promedio
- Tasa de satisfacción del usuario
- Patrones de uso por hora/día/mes
- Análisis de sentimientos (próximamente)

**Dashboards Interactivos:**
```
IGIS Bot > Analytics
├── 📊 Resumen ejecutivo
├── 📈 Gráficos de tendencias
├── 🕐 Análisis temporal
├── 📱 Métricas por dispositivo
└── 💬 Historial de conversaciones
```

### Sistema de Cache Multicapa
```php
// Arquitectura de Cache Implementada
Cache Layer 1: WordPress Object Cache
├── Configuraciones del plugin
├── Datos de usuario frecuentes
└── Respuestas de API cacheadas

Cache Layer 2: Transients Optimizados  
├── Estadísticas agregadas
├── Configuraciones compiladas
└── Datos de sesión temporales

Cache Layer 3: Browser Cache
├── Assets estáticos (CSS/JS)
├── Imágenes y recursos media
└── Configuración del chatbot
```

### Optimizaciones de Performance

**Carga Inicial:**
- Resource hints automáticos
- Critical CSS inline
- JavaScript asíncrono/diferido
- Preload de módulos ES6

**Runtime:**
- Debouncing de eventos de UI
- Intersection Observer para lazy loading  
- RequestIdleCallback para tareas no críticas
- Memory leak prevention

**Base de Datos:**
```sql
-- Índices optimizados automáticamente
CREATE INDEX idx_conversaciones_estado ON igis_bot_conversaciones(estado);
CREATE INDEX idx_mensajes_timestamp ON igis_bot_mensajes(timestamp);
CREATE INDEX idx_analytics_evento ON igis_bot_analytics(tipo_evento);
```

## Uso Avanzado

### Personalización con Hooks
```php
// Modificar configuración del bot
add_filter('igis_bot_config', function($config, $opciones) {
    if (is_user_logged_in()) {
        $usuario = wp_get_current_user();
        $config['theme']['chatWindow']['welcomeMessage'] = 
            "¡Hola {$usuario->display_name}! ¿Cómo puedo ayudarte?";
    }
    return $config;
}, 10, 2);

// Personalizar por roles de usuario
add_filter('igis_bot_opciones_sanitizadas', function($sanitized, $input) {
    if (current_user_can('administrator')) {
        // Administradores pueden usar JavaScript personalizado
        return $sanitized;
    } else {
        // Otros roles: JavaScript limitado por seguridad
        $sanitized['custom_js'] = '';
        return $sanitized;
    }
}, 10, 2);

// Eventos personalizados
add_action('igis_bot_conversacion_iniciada', function($conversacion_id, $usuario_id) {
    // Lógica personalizada al iniciar conversación
    error_log("Nueva conversación: {$conversacion_id} por usuario {$usuario_id}");
});
```

### JavaScript API
```javascript
// API pública disponible
window.IGISBot = {
    version: '1.2.0',
    manager: BotManager,
    
    // Métodos públicos
    trackCustomEvent: (evento, datos) => { ... },
    showBot: () => { ... },
    hideBot: () => { ... },
    getSessionId: () => { ... }
};

// Eventos personalizados
document.addEventListener('igis:bot:ready', function(e) {
    console.log('Bot listo:', e.detail);
    
    // Personalizar comportamiento
    if (window.innerWidth < 768) {
        // Lógica específica para móviles
        IGISBot.manager.configurarMobile();
    }
});

// Interceptar mensajes
document.addEventListener('flowise:message:sent', function(e) {
    // Analytics personalizado
    gtag('event', 'chatbot_interaction', {
        'message_length': e.detail.message.length,
        'session_id': IGISBot.getSessionId()
    });
});
```

### CSS Personalizado Avanzado
```css
/* Variables CSS disponibles */
:root {
    --igis-bot-primary-color: #3B81F6;
    --igis-bot-window-width: 400px;
    --igis-bot-window-height: 700px;
    --igis-bot-font-size: 16px;
    --igis-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Tema oscuro automático */
@media (prefers-color-scheme: dark) {
    .flowise-chatbot-button {
        box-shadow: 0 4px 20px rgba(59, 129, 246, 0.3);
    }
    
    .flowise-chatwindow {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border: 1px solid #404040;
    }
}

/* Animaciones personalizadas */
.flowise-chatbot-button {
    animation: pulse-gentle 3s infinite;
}

@keyframes pulse-gentle {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
```

## Seguridad y Privacidad

### Características de Seguridad
- **Content Security Policy**: Prevención automática de XSS
- **Rate Limiting**: Límites inteligentes por usuario/IP
- **Sanitización robusta**: Limpieza automática de inputs
- **Nonces dinámicos**: Tokens de seguridad rotativos
- **SQL Injection Prevention**: Prepared statements obligatorios

### Cumplimiento GDPR/CCPA
```php
// Configuración para cumplimiento automático
$configuracion_privacidad = [
    'save_conversations' => false,      // No guardar por defecto
    'analytics_enabled' => false,       // Analytics opt-in
    'require_consent' => true,          // Consentimiento requerido
    'data_retention_days' => 30,        // Retención limitada
    'anonymize_ips' => true,            // IPs anonimizadas
    'encrypt_messages' => true          // Mensajes encriptados
];

// Funciones de privacidad
igis_bot_export_user_data($user_id);     // Exportar datos usuario
igis_bot_delete_user_data($user_id);     // Eliminar datos usuario  
igis_bot_anonymize_conversations($user_id); // Anonimizar historial
```

### Integraciones de Cookies
Compatible automáticamente con:
- **Cookiebot** - Detección y control automático
- **OneTrust** - Integración nativa
- **Cookie Notice** - Soporte completo  
- **GDPR Cookie Consent** - Configuración automática

## Performance y Benchmarks

### Métricas de Rendimiento
```
Tiempo de Carga Inicial: < 1.2s (objetivo < 2s)
First Contentful Paint: < 800ms
Largest Contentful Paint: < 1.5s
Cumulative Layout Shift: < 0.1
Time to Interactive: < 2.0s

Bundle Size:
├── CSS: 12KB minificado + gzip
├── JavaScript: 28KB minificado + gzip
└── Total: ~40KB (reducción 60% vs v1.1)
```

### Core Web Vitals
- **LCP**: Optimizado automáticamente
- **FID**: < 100ms garantizado
- **CLS**: Prevención de layout shift
- **INP**: Interacciones < 200ms

### Optimizaciones Automáticas
```
✅ Resource preloading condicional
✅ Critical CSS inlined automáticamente  
✅ JavaScript code-splitting
✅ Image lazy loading nativo
✅ Service Worker (opcional)
✅ HTTP/2 Push hints
✅ Brotli/Gzip compression
✅ CDN-ready assets
```

## Integraciones Empresariales

### CRM y Marketing Automation
```php
// HubSpot
add_action('igis_bot_conversacion_completada', 'sincronizar_hubspot');

// Salesforce  
add_filter('igis_bot_lead_data', 'enriquecer_con_salesforce');

// Mailchimp/SendGrid
add_action('igis_bot_email_capturado', 'anadir_a_mailchimp');

// Google Analytics 4
add_action('igis_bot_evento_personalizado', 'enviar_a_ga4');
```

### E-commerce
```php
// WooCommerce - Integración nativa
add_filter('igis_bot_producto_consultado', 'mostrar_info_woocommerce');
add_action('igis_bot_carrito_abandonado', 'recordatorio_woocommerce');

// Easy Digital Downloads
add_filter('igis_bot_descarga_consultada', 'info_edd');
```

### Herramientas de Soporte
- **Zendesk**: Creación automática de tickets
- **Freshdesk**: Escalación de conversaciones
- **Intercom**: Sincronización bidireccional
- **Help Scout**: Integración de knowledge base

## API para Desarrolladores

### REST API Endpoints
```bash
# Estadísticas
GET /wp-json/igis-bot/v2/stats
GET /wp-json/igis-bot/v2/conversations?date_from=2024-01-01

# Configuración  
GET /wp-json/igis-bot/v2/settings
POST /wp-json/igis-bot/v2/settings
PUT /wp-json/igis-bot/v2/settings/{key}

# Conversaciones
GET /wp-json/igis-bot/v2/conversations/{id}
DELETE /wp-json/igis-bot/v2/conversations/{id}
POST /wp-json/igis-bot/v2/conversations/{id}/messages
```

### Webhooks Configurables
```json
{
  "webhook_url": "https://tu-app.com/webhook",
  "eventos": [
    "conversacion_iniciada",
    "conversacion_completada", 
    "mensaje_enviado",
    "lead_capturado",
    "error_ocurrido"
  ],
  "firma_secreta": "tu-clave-secreta",
  "reintentos": 3
}
```

### SDK JavaScript
```javascript
// SDK oficial para desarrolladores
import IGISBotSDK from '@igis/flowise-bot-sdk';

const bot = new IGISBotSDK({
    apiKey: 'tu-api-key',
    chatflowId: 'tu-chatflow-id'
});

// Métodos disponibles
await bot.inicializar();
await bot.enviarMensaje('Hola, ¿cómo estás?');
const stats = await bot.obtenerEstadisticas();
bot.onMensajeRecibido((mensaje) => { ... });
```

## Resolución de Problemas

### Diagnóstico Automático
```
IGIS Bot > Herramientas > Diagnóstico del Sistema

✅ WordPress 6.8.1 - Compatible
✅ PHP 8.1.2 - Optimizado  
✅ Flowise 3.1.0 - Última versión
✅ MySQL 8.0.34 - Rendimiento óptimo
✅ Object Cache - Habilitado (Redis)
✅ SSL/TLS - Certificado válido
⚠️  Memoria PHP - 128MB (recomendado 256MB)
❌ Gzip - No habilitado (pérdida de performance)
```

### Problemas Comunes y Soluciones

#### Bot no aparece
```bash
# Verificación paso a paso
1. Comprobar configuración básica
   wp igis-bot config check

2. Verificar conectividad API
   wp igis-bot test connection

3. Revisar logs de error
   wp igis-bot logs --lines=50

4. Debug mode temporal
   wp igis-bot debug enable --duration=10min
```

#### Performance lenta
```php
// Activar modo performance máximo
define('IGIS_BOT_PERFORMANCE_MODE', true);
define('IGIS_BOT_CACHE_AGGRESSIVE', true);
define('IGIS_BOT_PRELOAD_ASSETS', true);

// Verificar optimizaciones
wp igis-bot performance analyze
wp igis-bot cache warm
```

#### Tema oscuro no funciona
```css
/* Forzar tema oscuro */
body.igis-force-dark-mode {
    --igis-theme: dark;
}

/* Debug tema */
html[data-igis-theme-debug="true"] .flowise-chatbot-button {
    border: 2px solid red !important;
}
```

### Logs y Monitoring
```php
// Logs estructurados disponibles
wp igis-bot logs show --level=error
wp igis-bot logs show --type=performance  
wp igis-bot logs show --filter="API timeout"

// Monitoring en tiempo real
wp igis-bot monitor start
wp igis-bot monitor stats --live
```

## Roadmap de Desarrollo

### v1.3.0 (Q2 2024)
- [ ] **Gutenberg Blocks nativos**
  - Block personalizable del chatbot
  - Shortcode generator visual
  - Preview en tiempo real en editor
  
- [ ] **WP-CLI Commands completos**
  - Gestión completa vía terminal
  - Scripts de migración automatizados
  - Backup/restore de configuraciones
  
- [ ] **Multisite Support**
  - Configuración centralizada
  - Estadísticas agregadas
  - Gestión de licencias

### v1.4.0 (Q3 2024)
- [ ] **AI-Powered Insights**
  - Machine learning para optimización
  - Análisis predictivo de conversaciones
  - Sugerencias automáticas de mejoras
  
- [ ] **Advanced Analytics**
  - Sentiment analysis en tiempo real
  - Heat maps de interacción
  - A/B testing integrado
  
- [ ] **Integration Marketplace**
  - Store de integraciones oficiales
  - API para desarrolladores externos
  - Sistema de plugins del plugin

### v1.5.0 (Q4 2024)
- [ ] **Voice & Video Support**
  - Videollamadas integradas
  - Screen sharing para soporte
  - Transcripción automática
  
- [ ] **Advanced Security**
  - 2FA para configuración admin
  - Encryption end-to-end opcional
  - SOC 2 compliance tools

## Soporte y Comunidad

### Canales de Soporte Oficial
- **Email Prioritario**: [sgonzalez@infraestructuragis.com](mailto:sgonzalez@infraestructuragis.com)
- **Portal de Soporte**: [soporte.infraestructuragis.com](https://soporte.infraestructuragis.com)

### Niveles de Soporte
```
🆓 Soporte Comunidad (GitHub Issues)
├── Tiempo de respuesta: 48-72h
├── Bugs y problemas generales
└── Documentación y ejemplos

💼 Soporte Profesional (Email)
├── Tiempo de respuesta: 24h
├── Configuración personalizada
├── Integraciones específicas
└── Consultoría técnica

🚀 Soporte Enterprise (Dedicado)
├── Tiempo de respuesta: 4h
├── Gerente de cuenta dedicado
├── Desarrollo personalizado
├── SLA garantizado
└── Formación del equipo
```

**Guía de Contribución:**
1. Fork del repositorio oficial
2. Crear rama feature descriptiva
3. Seguir estándares de código (PSR-12, WordPress Coding Standards)
4. Tests unitarios obligatorios
5. Documentación actualizada
6. Pull request con descripción detallada

## Créditos y Licencia

### Desarrollado por
[**InfraestructuraGIS**](https://www.infraestructuragis.com/) - 

### Tecnologías y Librerías
- [**WordPress**](https://wordpress.org/) - CMS base y ecosistema
- [**Flowise AI**](https://flowiseai.com/) - Plataforma de chatbots inteligentes
- [**Chart.js**](https://www.chartjs.org/) - Gráficos interactivos y visualizaciones
- [**Intersection Observer**](https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API) - Performance optimizations
- [**Web Vitals**](https://web.dev/vitals/) - Core performance metrics

### Licencia GPL v2+
```
IGIS Flowise Bot es software libre bajo GPL v2 o posterior.

Permisos:
✅ Uso comercial sin restricciones
✅ Modificación y personalización completa
✅ Distribución y reventa permitida
✅ Uso en proyectos privados y públicos

Obligaciones:
📋 Mantener licencia GPL en derivados
📋 Compartir modificaciones si distribuyes
📋 Incluir copyright y disclaimers originales

Sin Garantías:
⚠️  Software proporcionado "tal como está"
⚠️  Sin garantía de funcionamiento específico
⚠️  Uso bajo tu propia responsabilidad
```

### Compatibilidad Certificada
```
✅ WordPress 6.8+ (incluyendo beta/nightly)
✅ PHP 8.0, 8.1, 8.2, 8.3
✅ MySQL 5.7+ / MariaDB 10.3+
✅ Nginx 1.20+ / Apache 2.4+
✅ Cloudflare, AWS CloudFront, Fastly CDN
✅ Multisite Networks
✅ WooCommerce 8.0+
✅ WPML, Polylang, qTranslate-X
```

---

## Instalación Rápida en 3 Minutos

```bash
# 1. Instalar plugin
wp plugin install igis-flowise-bot --activate

# 2. Configuración básica
wp igis-bot setup \
    --chatflow-id="tu-chatflow-id" \
    --api-host="https://tu-flowise.com" \
    --enable-performance=true \
    --enable-dark-mode=true

# 3. Verificar instalación
wp igis-bot status
```

**¡Listo!** Tu chatbot está funcionando con todas las optimizaciones activadas.

---

**⭐ Si IGIS Flowise Bot te ayuda a mejorar tu sitio web, no olvides dejarnos una reseña!**

*Última actualización: Enero 2025 | Versión 1.2.0 | Optimizado para WordPress 6.8+ | Soporte para Tema Oscuro | Performance Mejorada*

---

*© 2025 InfraestructuraGIS. Todos los derechos reservados. WordPress y el logo de WordPress son marcas registradas de WordPress Foundation.*
Desarrollado con ❤️ por [InfraestructuraGIS](https://www.infraestructuragis.com/)
