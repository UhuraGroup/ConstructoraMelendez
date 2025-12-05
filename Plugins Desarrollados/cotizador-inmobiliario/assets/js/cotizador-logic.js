jQuery(document).ready(function ($) {
    
    const projectSelector = $('#proyecto-selector');
    const currencySelector = $('#moneda');
    const calculateBtn = $('#calcular-btn');
    const resultsDiv = $('.cotizador-results');
    const loader = $('#cotizador-loader');
    
    // Función para formatear números como moneda
    function formatCurrency(number, currency) {
        // Asegurarse de que el número sea válido, si no, devolver 0.
        const num = Number(number);
        if (isNaN(num)) {
            number = 0;
        }

        const symbols = {
            COP: '$',
            USD: 'USD $',
            EUR: '€'
        };
        // Intl.NumberFormat es ideal para formato de moneda local
        let formattedNumber = new Intl.NumberFormat('es-CO').format(Math.round(number));
        return (symbols[currency] || '$') + ' ' + formattedNumber;
    }

    // Función para actualizar el precio del proyecto mostrado al instante
    function updateDisplayedPrice() {
        const selectedProject = projectSelector.find('option:selected');
        const selectedCurrency = currencySelector.val().toUpperCase();
        
        // Si no hay proyecto seleccionado, mostrar 0.
        if (!selectedProject.val()) {
            $('#res-precio-proyecto').text(formatCurrency(0, selectedCurrency));
            return;
        }

        const price = selectedProject.data('price-' + selectedCurrency.toLowerCase());
        $('#res-precio-proyecto').text(formatCurrency(price || 0, selectedCurrency));
    }

    // Evento para actualizar el precio si se cambia el proyecto o la moneda
    projectSelector.add(currencySelector).on('change', function() {
        updateDisplayedPrice();
    });

    // Acción del botón Calcular
    calculateBtn.on('click', function () {
        const proyectoId = projectSelector.val();
        const pctCuotaInicial = $('#porcentaje-cuota-inicial').val();
        const moneda = currencySelector.val();

        if (!proyectoId) {
            alert('Por favor, seleccione un proyecto.');
            return;
        }
        
        resultsDiv.hide();
        loader.show();
        calculateBtn.prop('disabled', true);

        $.ajax({
            url: cotizador_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'calculate_quote',
                nonce: cotizador_ajax.nonce,
                proyecto_id: proyectoId,
                pct_cuota_inicial: pctCuotaInicial,
                moneda: moneda
            },
            success: function (response) {
                if (response.success) {
                    const data = response.data;
                    $('#res-precio-proyecto').text(data.precio_proyecto);
                    $('#res-cuota-inicial').text(data.cuota_inicial);
                    $('#res-monto-financiado').text(data.monto_financiado);
                    $('#res-cuota-fija').text(data.cuota_fija);
                    $('#res-ingresos').text(data.ingresos_aprox);
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function (xhr, status, error) {
                alert('Ha ocurrido un error de conexión. Por favor, revise la consola para más detalles.');
                console.error("AJAX Error:", status, error);
            },
            complete: function() {
                loader.hide();
                resultsDiv.show();
                calculateBtn.prop('disabled', false);
            }
        });
    });

    // Actualizar el precio una vez al cargar la página
    updateDisplayedPrice();
});