jQuery(document).ready(function($) {

    // La variable 'projectData' está disponible gracias a wp_localize_script desde PHP

    // Función para actualizar el texto del placeholder según el tamaño de la pantalla
    function updatePlaceholderText() {
        // Detecta si estamos en vista móvil (basado en el media query CSS de 768px)
        const isMobile = window.matchMedia("(max-width: 768px)").matches;
        
        $('.project-selector option[value=""]').each(function() {
            if (isMobile) {
                // Si es móvil, usamos el texto corto
                $(this).text('Selecciona'); 
            } else {
                // Si es desktop, volvemos al texto largo original
                $(this).text('Selecciona un proyecto');
            }
        });
    }

    // Ejecutar la función al cargar y al redimensionar la ventana
    updatePlaceholderText();
    $(window).on('resize', updatePlaceholderText);
    
    // Función para actualizar una columna de la tabla de comparación
    function updateComparisonColumn(column, projectId) {
        const logoPreview = $(`#logo-preview-${column}`);
        const nameCell = $(`#comp-name-${column}`);
        const tipoCell = $(`#comp-tipo-${column}`);
        const precioCell = $(`#comp-precio-${column}`);
        const areaConstruidaCell = $(`#comp-areaConstruida-${column}`);
        const areaPrivadaCell = $(`#comp-areaPrivada-${column}`);

        // Si no se seleccionó un proyecto, limpiar la columna
        if (!projectId) {
            logoPreview.html('');
            nameCell.html('');
            tipoCell.html('');
            precioCell.html('');
            areaConstruidaCell.html('');
            areaPrivadaCell.html('');
            return;
        }

        // Buscar los datos del proyecto seleccionado en nuestro array
        const selectedProject = projectData.find(p => p.id == projectId);

        if (selectedProject) {
            // Actualizar el logo
            logoPreview.html(`<img src="${selectedProject.logoUrl}" alt="${selectedProject.name}">`);
            
            // Actualizar la tabla
            nameCell.html(selectedProject.name);
            tipoCell.html(selectedProject.tipo || 'N/A');
            precioCell.html(selectedProject.precio ? `$${selectedProject.precio}` : 'N/A');
            areaConstruidaCell.html(selectedProject.areaConstruida ? `${selectedProject.areaConstruida} m²` : 'N/A');
            areaPrivadaCell.html(selectedProject.areaPrivada ? `${selectedProject.areaPrivada} m²` : 'N/A');
        }
    }

    // Asignar el evento 'change' a cada selector
    $('.project-selector').on('change', function() {
        const selectedProjectId = $(this).val();
        const columnToUpdate = $(this).data('column');
        updateComparisonColumn(columnToUpdate, selectedProjectId);
    });

});