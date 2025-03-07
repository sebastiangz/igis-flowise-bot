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
        $(`#section-${targetSection}`).show();
    });

    // Mostrar/ocultar campos dependientes
    function toggleDependentFields() {
        // Tooltip fields
        const showTooltip = $('#show-tooltip').is(':checked');
        $('.tooltip-dependent').toggle(showTooltip);

        // Disclaimer fields
        const showDisclaimer = $('#show-disclaimer').is(':checked');
        $('.disclaimer-dependent').toggle(showDisclaimer);

        // Sound fields
        const enableSendSound = $('#enable-send-sound').is(':checked');
        $('.send-sound-dependent').toggle(enableSendSound);
        
        const enableReceiveSound = $('#enable-receive-sound').is(':checked');
        $('.receive-sound-dependent').toggle(enableReceiveSound);
    }

    // Inicializar estados
    toggleDependentFields();

    // Event listeners para campos dependientes
    $('#show-tooltip, #show-disclaimer, #enable-send-sound, #enable-receive-sound').on('change', toggleDependentFields);

    // Media Uploader para imágenes y sonidos
    $('.upload-media-button').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const targetInput = $(`#${button.data('target')}`);
        
        const mediaUploader = wp.media({
            title: 'Select File',
            button: {
                text: 'Use this file'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            targetInput.val(attachment.url);
        });

        mediaUploader.open();
    });

    // Preview del bot
    function updateBotPreview() {
        // Actualizar los estilos del botón de preview
        const previewButton = $('.bot-preview-button');
        previewButton.css({
            'background-color': $('#button-color').val(),
            'right': $('#button-position-right').val() + 'px',
            'bottom': $('#button-position-bottom').val() + 'px',
            'width': $('#button-size').val() + 'px',
            'height': $('#button-size').val() + 'px'
        });

        // Actualizar el icono
        const iconPreview = $('.bot-preview-icon');
        iconPreview.css('color', $('#icon-color').val());
    }

    // Event listeners para actualización en tiempo real del preview
    $('.preview-dependent').on('change input', updateBotPreview);
    $('.color-picker').wpColorPicker({
        change: updateBotPreview
    });

    // Inicializar preview
    updateBotPreview();
});
