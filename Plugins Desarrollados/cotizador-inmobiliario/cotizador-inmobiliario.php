<?php
/**
 * Plugin Name:       Cotizador Inmobiliario (Versión ACF)
 * Description:       Añade un cotizador de proyectos inmobiliarios que funciona con un CPT y campos de ACF existentes.
 * Version:           2.1
 * Author:            Oscar Cerpa - Lucker
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// =========================================================================
//  NOTA: Este plugin asume que el plugin Advanced Custom Fields (ACF) está activo.
//  También asume que existe un CPT llamado 'proyecto' con campos de número 
//  de ACF llamados 'precio_cop', 'precio_usd', y 'precio_eur'.
// =========================================================================

// 1. Crear Página de Opciones en el Administrador para PLAZO e Interés
function ci_add_admin_menu() {
    add_options_page('Opciones del Cotizador', 'Cotizador Inmobiliario', 'manage_options', 'cotizador_inmobiliario', 'ci_options_page_html');
}
add_action('admin_menu', 'ci_add_admin_menu');

function ci_settings_init() {
    register_setting('ci_pluginPage', 'ci_settings');
    add_settings_section('ci_pluginPage_section', 'Configuraciones Globales del Cálculo', null, 'ci_pluginPage');
    add_settings_field('ci_plazo_meses', 'Plazo del crédito (en meses)', 'ci_plazo_field_html', 'ci_pluginPage', 'ci_pluginPage_section');
    add_settings_field('ci_tasa_interes', 'Tasa de Interés Anual (%)', 'ci_tasa_field_html', 'ci_pluginPage', 'ci_pluginPage_section');
}
add_action('admin_init', 'ci_settings_init');

function ci_plazo_field_html() {
    $options = get_option('ci_settings');
    echo '<input type="number" name="ci_settings[ci_plazo_meses]" value="' . esc_attr($options['ci_plazo_meses'] ?? '180') . '" class="regular-text">';
    echo '<p class="description">Este valor NO se muestra al usuario. Es el plazo para calcular la cuota fija. Ejemplo: 180 para 15 años.</p>';
}

function ci_tasa_field_html() {
    $options = get_option('ci_settings');
    echo '<input type="number" step="0.01" name="ci_settings[ci_tasa_interes]" value="' . esc_attr($options['ci_tasa_interes'] ?? '10.5') . '" class="regular-text">';
    echo '<p class="description">Tasa de interés efectiva anual para el cálculo. Ejemplo: 10.5 para 10.5%.</p>';
}

function ci_options_page_html() {
    echo '<div class="wrap"><h1>' . esc_html(get_admin_page_title()) . '</h1><form action="options.php" method="post">';
    settings_fields('ci_pluginPage');
    do_settings_sections('ci_pluginPage');
    submit_button('Guardar Cambios');
    echo '</form></div>';
}

// 2. Registrar y Encolar Scripts y Estilos
function ci_enqueue_assets() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'cotizador_inmobiliario')) {
        wp_enqueue_style('ci-styles', plugin_dir_url(__FILE__) . 'assets/css/cotizador-styles.css', [], '2.1');
        wp_enqueue_script('ci-logic', plugin_dir_url(__FILE__) . 'assets/js/cotizador-logic.js', ['jquery'], '2.1', true);
        wp_localize_script('ci-logic', 'cotizador_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cotizador-nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'ci_enqueue_assets');

// 3. Crear el Shortcode [cotizador_inmobiliario]
function ci_shortcode_html() {
    if (!function_exists('get_field')) {
        return '<p style="color:red; font-weight:bold; text-align:center;">Error: El plugin Advanced Custom Fields (ACF) es necesario y no está activo.</p>';
    }

    $proyectos = new WP_Query(['post_type' => 'proyecto', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC']);
    ob_start();
    ?>
    <div class="cotizador-container">
        <div class="cotizador-form">
            <div class="cotizador-input-group">
                <label for="proyecto-selector">Selecciona un proyecto</label>
                <select id="proyecto-selector">
                    <option value="">-- Elige un proyecto --</option>
                    <?php
                    if ($proyectos->have_posts()) {
                        while ($proyectos->have_posts()) {
                            $proyectos->the_post();
                            $precio_cop = get_field('precio_cop', get_the_ID());
                            $precio_usd = get_field('precio_usd', get_the_ID());
                            $precio_eur = get_field('precio_eur', get_the_ID());
                            echo '<option 
                                    value="' . get_the_ID() . '" 
                                    data-price-cop="' . esc_attr($precio_cop) . '"
                                    data-price-usd="' . esc_attr($precio_usd) . '"
                                    data-price-eur="' . esc_attr($precio_eur) . '">'
                                . get_the_title() . 
                               '</option>';
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </select>
            </div>
            <div class="cotizador-input-group">
                <label for="porcentaje-cuota-inicial">Cuota inicial</label>
                <select id="porcentaje-cuota-inicial"><option value="20">20%</option><option value="30">30%</option></select>
            </div>
            <div class="cotizador-input-group">
                <label for="moneda">Moneda</label>
                <select id="moneda"><option value="COP">COP</option><option value="USD">USD</option><option value="EUR">EUR</option></select>
            </div>
            <button id="calcular-btn">Calcular</button>
        </div>

        <div class="cotizador-results">
            <div class="result-item"><span>Precio de proyecto:</span><strong id="res-precio-proyecto">$0</strong></div>
            <div class="result-item"><span>Cuota inicial:</span><strong id="res-cuota-inicial">$0</strong></div>
            <div class="result-item"><span>Monto financiado:</span><strong id="res-monto-financiado">$0</strong></div>
            <div class="result-item"><span>Cuota fija:</span><strong id="res-cuota-fija">$0</strong></div>
            <div class="result-item"><span>Ingresos aproximados:</span><strong id="res-ingresos">$0</strong></div>
        </div>
        <div class="disclaimer">Los valores presentados son referenciales y pueden variar según las condiciones del proyecto y la financiación. Un asesor de Constructora Meléndez podrá brindarte información detallada y actualizada.</div>
        <div id="cotizador-loader" style="display:none; text-align:center; padding: 20px; font-style: italic;">Calculando...</div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('cotizador_inmobiliario', 'ci_shortcode_html');

// 4. Manejador de la petición AJAX para los cálculos
function ci_handle_calculation() {
    check_ajax_referer('cotizador-nonce', 'nonce');

    $proyecto_id = isset($_POST['proyecto_id']) ? intval($_POST['proyecto_id']) : 0;
    $pct_cuota_inicial = isset($_POST['pct_cuota_inicial']) ? floatval($_POST['pct_cuota_inicial']) : 0;
    $moneda = isset($_POST['moneda']) ? sanitize_text_field($_POST['moneda']) : 'COP';

    if (!function_exists('get_field') || $proyecto_id === 0) {
        wp_send_json_error(['message' => 'ACF no está activo o el proyecto no es válido.']);
        return;
    }

    $options = get_option('ci_settings');
    $plazo_meses = intval($options['ci_plazo_meses'] ?? 180);
    $tasa_anual = floatval($options['ci_tasa_interes'] ?? 10.5);

    $meta_key = 'precio_' . strtolower($moneda);
    $precio_proyecto = floatval(get_field($meta_key, $proyecto_id));
    
    $currency_symbol = '$';
    if ($moneda === 'USD') $currency_symbol = 'USD $';
    if ($moneda === 'EUR') $currency_symbol = '€';
    
    if ($precio_proyecto > 0) {
        $monto_cuota_inicial = $precio_proyecto * ($pct_cuota_inicial / 100);
        $monto_financiado = $precio_proyecto - $monto_cuota_inicial;
        $tasa_mensual = ($tasa_anual / 100) / 12;
        $cuota_fija = 0;
        if ($tasa_mensual > 0 && $plazo_meses > 0) {
           $cuota_fija = ($monto_financiado * $tasa_mensual * pow(1 + $tasa_mensual, $plazo_meses)) / (pow(1 + $tasa_mensual, $plazo_meses) - 1);
        }
        $ingresos_aprox = $cuota_fija / 0.3;
        
        $format = fn($num) => $currency_symbol . ' ' . number_format(round($num), 0, ',', '.');
        
        wp_send_json_success([
            'precio_proyecto' => $format($precio_proyecto),
            'cuota_inicial'   => $format($monto_cuota_inicial),
            'monto_financiado'=> $format($monto_financiado),
            'cuota_fija'      => $format($cuota_fija),
            'ingresos_aprox'  => $format($ingresos_aprox)
        ]);
    } else {
        wp_send_json_error(['message' => 'El proyecto seleccionado no tiene un precio definido para la moneda ' . $moneda . '. Por favor, edítelo en el administrador.']);
    }
}
add_action('wp_ajax_nopriv_calculate_quote', 'ci_handle_calculation');
add_action('wp_ajax_calculate_quote', 'ci_handle_calculation');