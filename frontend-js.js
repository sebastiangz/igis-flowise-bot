jQuery(document).ready(function($) {
    // Generar o recuperar ID de sesión
    let sessionId = localStorage.getItem('igis_bot_session_id');
    if (!sessionId) {
        sessionId = generateSessionId();
        localStorage.setItem('igis_bot_session_id', sessionId);
    }
    
    // Variable para almacenar el ID de la conversación actual
    let conversationId = null;
    
    // Inicializar eventos después de que el chatbot esté cargado
    initChatbotEvents();
    
    /**
     * Inicializa los listeners de eventos para el chatbot
     */
    function initChatbotEvents() {
        // Evento de apertura del chat
        document.addEventListener('flowise:chatOpen', function() {
            logConversationStart();
        });
        
        // Evento de envío de mensaje
        document.addEventListener('flowise:messageSubmitted', function(e) {
            if (!conversationId) return;
            
            logMessage(e.detail.message, 'user');
        });
        
        // Evento de recepción de mensaje
        document.addEventListener('flowise:messageReceived', function(e) {
            if (!conversationId) return;
            
            logMessage(e.detail.message, 'bot');
        });
        
        // Evento de cierre del chat
        document.addEventListener('flowise:chatClose', function() {
            if (!conversationId) return;
            
            logConversationEnd();
        });
    }
    
    /**
     * Genera un ID de sesión único
     */
    function generateSessionId() {
        return Math.random().toString(36).substring(2, 15) + 
               Math.random().toString(36).substring(2, 15);
    }
    
    /**
     * Registra el inicio de una conversación
     */
    function logConversationStart() {
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
                if (response.success && response.data && response.data.conversation_id) {
                    conversationId = response.data.conversation_id;
                    console.debug('IGIS Bot: Conversación iniciada', conversationId);
                }
            },
            error: function(error) {
                console.error('IGIS Bot: Error al iniciar conversación', error);
            }
        });
    }
    
    /**
     * Registra un mensaje en la conversación
     */
    function logMessage(message, type) {
        $.ajax({
            url: igisBotFrontend.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_log_message',
                nonce: igisBotFrontend.nonce,
                conversation_id: conversationId,
                message: message,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    console.debug(`IGIS Bot: Mensaje de ${type} registrado`);
                }
            },
            error: function(error) {
                console.error(`IGIS Bot: Error al registrar mensaje de ${type}`, error);
            }
        });
    }
    
    /**
     * Registra el fin de una conversación
     */
    function logConversationEnd() {
        $.ajax({
            url: igisBotFrontend.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_log_conversation',
                nonce: igisBotFrontend.nonce,
                session_id: sessionId,
                status: 'completed'
            },
            success: function(response) {
                if (response.success) {
                    console.debug('IGIS Bot: Conversación finalizada', conversationId);
                    
                    // Opcionalmente, podemos conservar el ID de sesión para futuras conversaciones
                    // o generar uno nuevo para la siguiente conversación
                    // sessionId = generateSessionId();
                    // localStorage.setItem('igis_bot_session_id', sessionId);
                }
            },
            error: function(error) {
                console.error('IGIS Bot: Error al finalizar conversación', error);
            }
        });
    }
    
    /**
     * Detecta inactividad para finalizar conversaciones abandonadas
     */
    function setupInactivityDetection() {
        let inactivityTimeout;
        const timeoutDuration = 15 * 60 * 1000; // 15 minutos por defecto
        
        function resetInactivityTimer() {
            clearTimeout(inactivityTimeout);
            inactivityTimeout = setTimeout(function() {
                if (conversationId) {
                    logConversationEnd();
                }
            }, timeoutDuration);
        }
        
        // Eventos que reinician el temporizador de inactividad
        document.addEventListener('flowise:messageSubmitted', resetInactivityTimer);
        document.addEventListener('flowise:chatOpen', resetInactivityTimer);
        
        // Iniciar temporizador cuando se cargue la página
        resetInactivityTimer();
    }
    
    // Si está habilitado el guardado de conversaciones, configurar detección de inactividad
    if (igisBotFrontend.saveConversations) {
        setupInactivityDetection();
    }
    
    /**
     * Gestiona eventos de analítica si está activado
     */
    if (igisBotFrontend.analyticsEnabled) {
        // Evento de visualización del botón del chatbot
        document.addEventListener('flowise:botLoaded', function() {
            trackEvent('bot_viewed');
        });
        
        // Evento de clic en el botón
        document.addEventListener('flowise:chatOpen', function() {
            trackEvent('bot_opened');
        });
        
        // Evento de envío de mensaje
        document.addEventListener('flowise:messageSubmitted', function() {
            trackEvent('message_sent');
        });
        
        // Evento de recepción de respuesta
        document.addEventListener('flowise:messageReceived', function() {
            trackEvent('message_received');
        });
    }
    
    /**
     * Registra un evento de analítica
     */
    function trackEvent(eventName, eventData = {}) {
        // Si está configurado Google Analytics
        if (typeof gtag === 'function' && igisBotFrontend.analyticsTrackingId) {
            gtag('event', eventName, {
                'event_category': 'IGIS Bot',
                'event_label': document.title,
                ...eventData
            });
        }
        
        // Enviar evento al servidor para análisis interno
        $.ajax({
            url: igisBotFrontend.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_track_event',
                nonce: igisBotFrontend.nonce,
                event_name: eventName,
                event_data: JSON.stringify(eventData)
            },
            success: function() {
                console.debug(`IGIS Bot: Evento ${eventName} registrado`);
            }
        });
    }
    
    /**
     * Funcionalidades para dispositivos móviles
     */
    function setupMobileSpecificFeatures() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            // Ajustes específicos para móviles
            document.addEventListener('flowise:botLoaded', function() {
                const chatContainer = document.querySelector('.flowise-chat-window');
                if (chatContainer) {
                    // Hacer que el chat ocupe toda la pantalla en móviles
                    chatContainer.style.maxWidth = '100%';
                    chatContainer.style.maxHeight = '100%';
                    chatContainer.style.borderRadius = '0';
                }
            });
            
            // Escuchar cambios de orientación
            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    const chatContainer = document.querySelector('.flowise-chat-window');
                    if (chatContainer) {
                        // Ajustar el tamaño después de cambiar la orientación
                        chatContainer.style.maxHeight = window.innerHeight + 'px';
                    }
                }, 300);
            });
        }
    }
    
    // Configurar características específicas para móviles
    setupMobileSpecificFeatures();
    
    /**
     * Guardar histórico de mensajes localmente para recuperación
     */
    function setupLocalMessageHistory() {
        let messageHistory = JSON.parse(localStorage.getItem('igis_bot_message_history') || '{}');
        
        // Escuchar mensajes enviados y recibidos
        document.addEventListener('flowise:messageSubmitted', function(e) {
            addMessageToHistory('user', e.detail.message);
        });
        
        document.addEventListener('flowise:messageReceived', function(e) {
            addMessageToHistory('bot', e.detail.message);
        });
        
        function addMessageToHistory(sender, message) {
            if (!sessionId) return;
            
            if (!messageHistory[sessionId]) {
                messageHistory[sessionId] = [];
            }
            
            messageHistory[sessionId].push({
                sender: sender,
                message: message,
                timestamp: new Date().toISOString()
            });
            
            // Limitar el tamaño del historial (guardar los últimos 50 mensajes)
            if (messageHistory[sessionId].length > 50) {
                messageHistory[sessionId] = messageHistory[sessionId].slice(-50);
            }
            
            // Guardar en localStorage
            localStorage.setItem('igis_bot_message_history', JSON.stringify(messageHistory));
        }
    }
    
    // Si está habilitado, configurar guardado de histórico local
    if (igisBotFrontend.saveLocalHistory) {
        setupLocalMessageHistory();
    }
    
    /**
     * Integración con cookies de consentimiento GDPR
     */
    function setupGdprConsent() {
        // Comprobar si se necesita consentimiento antes de iniciar el bot
        if (igisBotFrontend.requireConsent) {
            // Esconder el botón del chat hasta tener consentimiento
            const style = document.createElement('style');
            style.id = 'igis-bot-consent-style';
            style.innerHTML = '.flowise-chatbot-button { display: none !important; }';
            document.head.appendChild(style);
            
            // Verificar consentimiento cuando cambie el estado de las cookies
            document.addEventListener('cookieConsentChanged', function(e) {
                if (e.detail && e.detail.marketing === true) {
                    // Eliminar el estilo que oculta el botón
                    const hideStyle = document.getElementById('igis-bot-consent-style');
                    if (hideStyle) {
                        hideStyle.remove();
                    }
                }
            });
            
            // Para compatibilidad con plugins comunes de cookies
            // CookieBot
            window.addEventListener('CookiebotOnAccept', function() {
                if (Cookiebot.consent.marketing) {
                    document.getElementById('igis-bot-consent-style')?.remove();
                }
            });
            
            // OneTrust
            window.addEventListener('OneTrustGroupsUpdated', function() {
                if (window.OnetrustActiveGroups && window.OnetrustActiveGroups.includes('C0004')) {
                    document.getElementById('igis-bot-consent-style')?.remove();
                }
            });
        }
    }
    
    // Si está configurado, gestionar el consentimiento GDPR
    if (typeof igisBotFrontend.requireConsent !== 'undefined') {
        setupGdprConsent();
    }
});
