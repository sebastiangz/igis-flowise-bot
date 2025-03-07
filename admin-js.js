jQuery(document).ready(function($) {
    // Inicializar los selectores de color
    $('.color-picker').wpColorPicker();

    // Manejar la navegación por pestañas
    $('.nav-tab-wrapper a').on('click', function(e) {
        e.preventDefault();
        
        // Remover clase activa de todas las pestañas
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        
        // Agregar clase activa a la pestaña actual
        $(this).addClass('nav-tab-active');
        
        // Ocultar todas las secciones
        $('.settings-section').hide();
        
        // Mostrar la sección correspondiente
        const targetSection = $(this).attr('href').substring(1);
        $('#section-' + targetSection).show();
        
        // Actualizar el hash de la URL para persistencia
        window.location.hash = targetSection;
    });

    // Mostrar la pestaña activa basada en el hash de la URL o mostrar la primera pestaña
    function showActiveTab() {
        let activeSection = window.location.hash.substring(1);
        
        if (activeSection && $('#section-' + activeSection).length) {
            $('.nav-tab-wrapper a[href="#' + activeSection + '"]').click();
        } else {
            // Por defecto, mostrar la primera pestaña
            $('.nav-tab-wrapper a:first').click();
        }
    }
    
    showActiveTab();

    // Mostrar/ocultar campos dependientes
    function toggleDependentFields() {
        // Tooltip fields
        const showTooltip = $('input[name="igis_bot_options[show_tooltip]"]').is(':checked');
        $('.tooltip-dependent').toggle(showTooltip);

        // Disclaimer fields
        const showDisclaimer = $('input[name="igis_bot_options[show_disclaimer]"]').is(':checked');
        $('.disclaimer-dependent').toggle(showDisclaimer);

        // Sound fields
        const enableSendSound = $('input[name="igis_bot_options[enable_send_sound]"]').is(':checked');
        $('.send-sound-dependent').toggle(enableSendSound);
        
        const enableReceiveSound = $('input[name="igis_bot_options[enable_receive_sound]"]').is(':checked');
        $('.receive-sound-dependent').toggle(enableReceiveSound);
        
        // Campos de analytics
        const analyticsEnabled = $('input[name="igis_bot_options[analytics_enabled]"]').is(':checked');
        $('.analytics-dependent').toggle(analyticsEnabled);
        
        // Campos para guardar conversaciones
        const saveConversations = $('input[name="igis_bot_options[save_conversations]"]').is(':checked');
        $('.save-conversations-dependent').toggle(saveConversations);
    }

    // Inicializar estados
    toggleDependentFields();

    // Event listeners para campos dependientes
    $('input[name="igis_bot_options[show_tooltip]"], input[name="igis_bot_options[show_disclaimer]"], input[name="igis_bot_options[enable_send_sound]"], input[name="igis_bot_options[enable_receive_sound]"], input[name="igis_bot_options[analytics_enabled]"], input[name="igis_bot_options[save_conversations]"]').on('change', toggleDependentFields);

    // Media Uploader para imágenes y sonidos
    $('.upload-media-button').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const targetField = button.data('target');
        const inputField = $('input[name="igis_bot_options[' + targetField + ']"]');
        
        const mediaUploader = wp.media({
            title: 'Seleccionar Archivo',
            button: {
                text: 'Usar este archivo'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            
            // Actualizar la vista previa
            const previewContainer = button.siblings('.media-preview');
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
    $('.preview-bot').on('click', function() {
        $('#bot-preview').fadeIn();
        
        // Crear un iframe simulado para la vista previa
        const options = collectFormOptions();
        renderBotPreview(options);
    });
    
    $('.close-preview').on('click', function() {
        $('#bot-preview').fadeOut();
    });
    
    function collectFormOptions() {
        const options = {};
        
        // Recolectar todos los valores del formulario
        $('form#igis-bot-settings-form').find('input, select, textarea').each(function() {
            const input = $(this);
            let name = input.attr('name');
            
            if (name && name.startsWith('igis_bot_options[') && name.endsWith(']')) {
                // Extraer el nombre de la opción
                const optionName = name.substring(16, name.length - 1);
                
                // Obtener el valor según el tipo
                let value;
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
    
    function renderBotPreview(options) {
        const previewContent = $('.bot-preview-content');
        
        // Limpiar contenido anterior
        previewContent.empty();
        
        // Crear contenedor de simulación
        previewContent.html(`
            <div class="preview-container" style="position: relative; height: 400px; border: 1px solid #ddd; background-color: #f5f5f5; overflow: hidden;">
                <div class="preview-chatbot-button" style="
                    position: absolute;
                    right: ${options.button_position_right || 20}px;
                    bottom: ${options.button_position_bottom || 20}px;
                    width: ${options.button_size || 48}px;
                    height: ${options.button_size || 48}px;
                    border-radius: 50%;
                    background-color: ${options.button_color || '#3B81F6'};
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                ">
                    <img src="${options.custom_icon || 'https://raw.githubusercontent.com/walkxcode/dashboard-icons/main/svg/google-messages.svg'}" 
                         alt="Bot icon" 
                         style="width: 60%; height: 60%; filter: ${options.icon_color === 'white' ? 'brightness(0) invert(1)' : ''}"
                    >
                </div>
                
                ${options.show_tooltip ? `
                <div class="preview-tooltip" style="
                    position: absolute;
                    right: ${parseInt(options.button_position_right || 20) + parseInt(options.button_size || 48) + 10}px;
                    bottom: ${parseInt(options.button_position_bottom || 20) + parseInt(options.button_size || 48)/2 - 10}px;
                    background-color: ${options.tooltip_bg_color || 'black'};
                    color: ${options.tooltip_text_color || 'white'};
                    padding: 8px 12px;
                    border-radius: 4px;
                    font-size: ${options.tooltip_font_size || 16}px;
                    max-width: 200px;
                ">
                    ${options.tooltip_message || 'Hi There 👋!'}
                    <div style="position: absolute; right: -5px; top: 50%; margin-top: -5px; width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 5px solid ${options.tooltip_bg_color || 'black'};"></div>
                </div>
                ` : ''}
                
                <div class="preview-chat-window" style="
                    display: none;
                    position: absolute;
                    right: ${options.button_position_right || 20}px;
                    bottom: ${parseInt(options.button_position_bottom || 20) + parseInt(options.button_size || 48) + 10}px;
                    width: ${options.window_width || 320}px;
                    height: ${options.window_height || 450}px;
                    background-color: ${options.window_background_color || '#ffffff'};
                    border-radius: 8px;
                    box-shadow: 0 5px 40px rgba(0,0,0,0.16);
                    overflow: hidden;
                    flex-direction: column;
                ">
                    <div class="preview-chat-header" style="
                        padding: 12px 16px;
                        background-color: ${options.button_color || '#3B81F6'};
                        color: white;
                        font-weight: bold;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                        ${options.window_title || 'IGIS Bot'}
                        <span style="cursor: pointer;">&times;</span>
                    </div>
                    
                    <div class="preview-chat-body" style="
                        flex: 1;
                        padding: 16px;
                        overflow-y: auto;
                        display: flex;
                        flex-direction: column;
                        gap: 12px;
                    ">
                        <div class="preview-bot-message" style="
                            align-self: flex-start;
                            background-color: ${options.bot_message_bg_color || '#f7f8ff'};
                            color: ${options.bot_message_text_color || '#303235'};
                            padding: 12px;
                            border-radius: 6px;
                            max-width: 80%;
                            font-size: ${options.font_size || 16}px;
                        ">
                            ${options.welcome_message || 'Hello! How can I help you today?'}
                        </div>
                        
                        <div class="preview-user-message" style="
                            align-self: flex-end;
                            background-color: ${options.user_message_bg_color || '#3B81F6'};
                            color: ${options.user_message_text_color || '#ffffff'};
                            padding: 12px;
                            border-radius: 6px;
                            max-width: 80%;
                            font-size: ${options.font_size || 16}px;
                        ">
                            Ejemplo de mensaje del usuario
                        </div>
                    </div>
                    
                    <div class="preview-chat-input" style="
                        padding: 12px;
                        border-top: 1px solid #eee;
                        display: flex;
                        gap: 8px;
                    ">
                        <input type="text" placeholder="${options.input_placeholder || 'Type your question'}" style="
                            flex: 1;
                            padding: 8px 12px;
                            border: 1px solid #ddd;
                            border-radius: 4px;
                            background-color: ${options.input_bg_color || '#ffffff'};
                            color: ${options.input_text_color || '#303235'};
                        ">
                        <button style="
                            background-color: ${options.input_send_button_color || '#3B81F6'};
                            color: white;
                            border: none;
                            border-radius: 4px;
                            padding: 8px 16px;
                            cursor: pointer;
                        ">Send</button>
                    </div>
                </div>
            </div>
        `);
        
        // Activar interacciones de la vista previa
        previewContent.find('.preview-chatbot-button').on('click', function() {
            previewContent.find('.preview-tooltip').hide();
            previewContent.find('.preview-chat-window').css('display', 'flex');
        });
        
        previewContent.find('.preview-chat-header span').on('click', function() {
            previewContent.find('.preview-chat-window').hide();
            if (options.show_tooltip) {
                previewContent.find('.preview-tooltip').show();
            }
        });
    }
    
    // Función para la página de estadísticas
    if ($('.igis-bot-stats-wrap').length) {
        loadStatsData();
    }
    
    function loadStatsData() {
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
                alert(igisBotAdmin.strings.errorLoading);
            }
        });
    }
    
    function renderCharts(data) {
        // Gráfico de conversaciones
        if ($('#conversationsChart').length && data.conversations_chart) {
            new Chart(document.getElementById('conversationsChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.conversations_chart.map(item => item.date),
                    datasets: [{
                        label: 'Conversaciones',
                        data: data.conversations_chart.map(item => item.count),
                        backgroundColor: 'rgba(59, 129, 246, 0.2)',
                        borderColor: 'rgba(59, 129, 246, 1)',
                        borderWidth: 1,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Conversaciones por día'
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Gráfico de mensajes
        if ($('#messagesChart').length && data.messages_chart) {
            new Chart(document.getElementById('messagesChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.messages_chart.map(item => item.date),
                    datasets: [
                        {
                            label: 'Mensajes de Usuario',
                            data: data.messages_chart.map(item => item.user),
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Respuestas del Bot',
                            data: data.messages_chart.map(item => item.bot),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Mensajes por día'
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Funcionalidad para la página de conversaciones
    if ($('.igis-bot-conversations-wrap').length) {
        loadConversations();
        
        $('#filter-conversations').on('click', function() {
            loadConversations();
        });
    }
    
    function loadConversations() {
        const tableBody = $('#conversations-list');
        const dateFilter = $('select[name="filter_date"]').val();
        
        tableBody.html('<tr><td colspan="6" class="loading">Cargando conversaciones...</td></tr>');
        
        $.ajax({
            url: igisBotAdmin.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_get_conversations',
                nonce: igisBotAdmin.nonce,
                date_filter: dateFilter,
                page: 1
            },
            success: function(response) {
                if (response.success && response.data && response.data.conversations) {
                    renderConversations(response.data);
                } else {
                    tableBody.html('<tr><td colspan="6">No se encontraron conversaciones.</td></tr>');
                }
            },
            error: function() {
                tableBody.html('<tr><td colspan="6">Error al cargar los datos. Intente de nuevo.</td></tr>');
            }
        });
    }
    
    function renderConversations(data) {
        const tableBody = $('#conversations-list');
        tableBody.empty();
        
        if (data.conversations.length === 0) {
            tableBody.html('<tr><td colspan="6">No se encontraron conversaciones.</td></tr>');
            return;
        }
        
        data.conversations.forEach(function(conversation) {
            const row = $('<tr></tr>');
            
            row.append('<td>' + conversation.id + '</td>');
            row.append('<td>' + formatDateTime(conversation.started_at) + '</td>');
            row.append('<td>' + (conversation.username || 'Anónimo') + '</td>');
            row.append('<td>' + (conversation.message_count || 0) + '</td>');
            row.append('<td><span class="status-' + conversation.status + '">' + formatStatus(conversation.status) + '</span></td>');
            
            const actions = $('<td class="actions"></td>');
            const viewBtn = $('<button class="button button-small view-conversation">Ver</button>');
            const deleteBtn = $('<button class="button button-small button-link-delete delete-conversation">Eliminar</button>');
            
            viewBtn.data('id', conversation.id);
            deleteBtn.data('id', conversation.id);
            
            actions.append(viewBtn);
            actions.append(' | ');
            actions.append(deleteBtn);
            
            row.append(actions);
            tableBody.append(row);
        });
        
        // Agregar paginación
        updatePagination(data.total, data.pages);
        
        // Event listeners para acciones
        $('.view-conversation').on('click', function() {
            const conversationId = $(this).data('id');
            viewConversation(conversationId);
        });
        
        $('.delete-conversation').on('click', function() {
            const conversationId = $(this).data('id');
            if (confirm(igisBotAdmin.strings.confirmDelete)) {
                deleteConversation(conversationId);
            }
        });
    }
    
    function updatePagination(total, pages) {
        const paginationContainer = $('.tablenav-pages');
        
        if (pages <= 1) {
            paginationContainer.empty();
            return;
        }
        
        let html = '<span class="displaying-num">' + total + ' elementos</span>';
        html += '<span class="pagination-links">';
        
        if (pages > 1) {
            html += '<a class="first-page button" href="#"><span>«</span></a>';
            html += '<a class="prev-page button" href="#"><span>‹</span></a>';
            html += '<span class="paging-input">';
            html += '<input class="current-page" type="text" value="1" size="1">';
            html += ' de <span class="total-pages">' + pages + '</span>';
            html += '</span>';
            html += '<a class="next-page button" href="#"><span>›</span></a>';
            html += '<a class="last-page button" href="#"><span>»</span></a>';
        }
        
        html += '</span>';
        
        paginationContainer.html(html);
        
        // Event listeners para paginación
        $('.first-page').on('click', function(e) {
            e.preventDefault();
            changePage(1);
        });
        
        $('.prev-page').on('click', function(e) {
            e.preventDefault();
            const currentPage = parseInt($('.current-page').val());
            if (currentPage > 1) {
                changePage(currentPage - 1);
            }
        });
        
        $('.next-page').on('click', function(e) {
            e.preventDefault();
            const currentPage = parseInt($('.current-page').val());
            if (currentPage < pages) {
                changePage(currentPage + 1);
            }
        });
        
        $('.last-page').on('click', function(e) {
            e.preventDefault();
            changePage(pages);
        });
        
        $('.current-page').on('keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                const page = parseInt($(this).val());
                if (page > 0 && page <= pages) {
                    changePage(page);
                }
            }
        });
    }
    
    function changePage(page) {
        $('.current-page').val(page);
        
        $.ajax({
            url: igisBotAdmin.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_get_conversations',
                nonce: igisBotAdmin.nonce,
                date_filter: $('select[name="filter_date"]').val(),
                page: page
            },
            success: function(response) {
                if (response.success && response.data && response.data.conversations) {
                    renderConversations(response.data);
                }
            }
        });
    }
    
    function viewConversation(conversationId) {
        // Crear modal para ver la conversación
        const modal = $('<div class="igis-bot-modal"></div>');
        const modalContent = $('<div class="modal-content"></div>');
        const modalHeader = $('<div class="modal-header"><h2>Detalles de la Conversación</h2><span class="close-modal">&times;</span></div>');
        const modalBody = $('<div class="modal-body"><div class="loading">Cargando...</div></div>');
        
        modalContent.append(modalHeader);
        modalContent.append(modalBody);
        modal.append(modalContent);
        $('body').append(modal);
        
        // Mostrar modal
        modal.fadeIn();
        
        // Cargar datos de la conversación
        $.ajax({
            url: igisBotAdmin.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_get_conversation_details',
                nonce: igisBotAdmin.nonce,
                conversation_id: conversationId
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderConversationDetails(modalBody, response.data);
                } else {
                    modalBody.html('<div class="error">Error al cargar los detalles.</div>');
                }
            },
            error: function() {
                modalBody.html('<div class="error">Error al cargar los detalles. Intente de nuevo.</div>');
            }
        });
        
        // Event listener para cerrar modal
        $('.close-modal').on('click', function() {
            modal.fadeOut(function() {
                modal.remove();
            });
        });
        
        // Cerrar modal al hacer clic fuera del contenido
        $(window).on('click', function(e) {
            if ($(e.target).is(modal)) {
                modal.fadeOut(function() {
                    modal.remove();
                });
            }
        });
    }
    
    function renderConversationDetails(container, data) {
        const conversation = data.conversation;
        const messages = data.messages;
        
        container.empty();
        
        // Información de la conversación
        const infoHtml = `
            <div class="conversation-info">
                <div class="info-item">
                    <span class="label">ID:</span> ${conversation.id}
                </div>
                <div class="info-item">
                    <span class="label">Usuario:</span> ${conversation.username || 'Anónimo'}
                </div>
                <div class="info-item">
                    <span class="label">Fecha de inicio:</span> ${formatDateTime(conversation.started_at)}
                </div>
                <div class="info-item">
                    <span class="label">Estado:</span> ${formatStatus(conversation.status)}
                </div>
                <div class="info-item">
                    <span class="label">Duración:</span> ${calculateDuration(conversation.started_at, conversation.ended_at)}
                </div>
            </div>
        `;
        
        // Mensajes
        let messagesHtml = '<div class="conversation-messages">';
        
        if (messages.length === 0) {
            messagesHtml += '<div class="no-messages">No hay mensajes en esta conversación.</div>';
        } else {
            messages.forEach(function(message) {
                const isBot = message.type === 'bot';
                messagesHtml += `
                    <div class="message ${isBot ? 'bot' : 'user'}">
                        <div class="message-header">
                            <span class="message-sender">${isBot ? 'Bot' : 'Usuario'}</span>
                            <span class="message-time">${formatTime(message.timestamp)}</span>
                        </div>
                        <div class="message-content">${formatMessage(message.message)}</div>
                    </div>
                `;
            });
        }
        
        messagesHtml += '</div>';
        
        container.append(infoHtml);
        container.append(messagesHtml);
    }
    
    function deleteConversation(conversationId) {
        $.ajax({
            url: igisBotAdmin.ajaxUrl,
            method: 'POST',
            data: {
                action: 'igis_bot_delete_conversation',
                nonce: igisBotAdmin.nonce,
                conversation_id: conversationId
            },
            success: function(response) {
                if (response.success) {
                    loadConversations();
                } else {
                    alert(response.data || 'Error al eliminar la conversación.');
                }
            },
            error: function() {
                alert('Error al eliminar la conversación. Intente de nuevo.');
            }
        });
    }
    
    // Funciones de utilidad
    function formatStatus(status) {
        switch (status) {
            case 'active':
                return 'Activa';
            case 'completed':
                return 'Completada';
            default:
                return status;
        }
    }
    
    function formatDateTime(dateTimeStr) {
        const date = new Date(dateTimeStr);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
    
    function formatTime(dateTimeStr) {
        const date = new Date(dateTimeStr);
        return date.toLocaleTimeString();
    }
    
    function formatMessage(message) {
        return message.replace(/\n/g, '<br>');
    }
    
    function calculateDuration(startTimeStr, endTimeStr) {
        if (!endTimeStr) return 'En progreso';
        
        const startTime = new Date(startTimeStr);
        const endTime = new Date(endTimeStr);
        const durationMs = endTime - startTime;
        
        // Convertir a minutos y segundos
        const minutes = Math.floor(durationMs / 60000);
        const seconds = Math.floor((durationMs % 60000) / 1000);
        
        if (minutes === 0) {
            return seconds + ' segundos';
        } else {
            return minutes + ' min ' + seconds + ' seg';
        }
    }
});
