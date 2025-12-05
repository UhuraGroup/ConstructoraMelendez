jQuery(document).ready(function ($) {
    'use strict';
    
    // Variable para el frame del cargador de medios
    var frame;

    // Evento click del botón para subir imagen
    $('#bcard_upload_image_button').on('click', function (event) {
        event.preventDefault();

        // Si ya existe un frame, abrirlo
        if (frame) {
            frame.open();
            return;
        }

        // Crear un nuevo frame de medios
        frame = wp.media({
            title: 'Selecciona o Sube una Imagen de Fondo',
            button: {
                text: 'Usar esta imagen'
            },
            multiple: false // Solo permitir seleccionar una imagen
        });

        // Cuando se selecciona una imagen
        frame.on('select', function () {
            // Obtener la información del adjunto
            var attachment = frame.state().get('selection').first().toJSON();

            // Poner el ID en el campo oculto
            $('#bcard_image_id').val(attachment.id);

            // Poner la URL en la previsualización
            $('#bcard_image_preview').attr('src', attachment.sizes.medium.url).show();

            // Mostrar el botón de quitar
            $('#bcard_remove_image_button').show();
        });

        // Abrir el frame
        frame.open();
    });

    // Evento click del botón para quitar imagen
    $('#bcard_remove_image_button').on('click', function (event) {
        event.preventDefault();

        // Vaciar los campos
        $('#bcard_image_id').val('');
        $('#bcard_image_preview').attr('src', '').hide();

        // Ocultar este botón
        $(this).hide();
    });
});