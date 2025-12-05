<?php
/**
 * Plugin Name:       Tarjetas Interactivas B-CARD (Independiente)
 * Description:       Añade un shortcode [tarjetas_interactivas] sin necesidad de ACF.
 * Version:           2.0
 * Author:            Oscar Cerpa
 */

// Evitar acceso directo
if (!defined('ABSPATH')) exit;

// Constantes del plugin
define('BCARD_PLUGIN_URL', plugin_dir_url(__FILE__));

// === 1. CUSTOM POST TYPE (Sin cambios) ===
function bcard_register_post_type() {
    $labels = [ 'name' => 'Tarjetas Interactivas', 'singular_name' => 'Tarjeta Interactiva' /* ...otros labels... */ ];
    $args = [
        'labels' => $labels, 'public' => true, 'show_ui' => true, 'show_in_menu' => true,
        'menu_position' => 20, 'menu_icon' => 'dashicons-format-gallery',
        'supports' => ['title', 'editor', 'page-attributes'],
    ];
    register_post_type('tarjeta_interactiva', $args);
}
add_action('init', 'bcard_register_post_type');

// === 2. ENQUEUE DE ESTILOS Y SCRIPTS (Front-end y Admin) ===
function bcard_enqueue_assets() {
    wp_enqueue_style('bcard-styles', BCARD_PLUGIN_URL . 'css/bcard-styles.css', [], '2.1');
    wp_enqueue_script('bcard-scripts', BCARD_PLUGIN_URL . 'js/bcard-scripts.js', [], '2.0', true);
}
add_action('wp_enqueue_scripts', 'bcard_enqueue_assets');

// Cargar scripts solo en el panel de administración para el selector de medios
function bcard_enqueue_admin_assets($hook) {
    global $post;
    if ($hook == 'post-new.php' || $hook == 'post.php') {
        if (isset($post->post_type) && $post->post_type === 'tarjeta_interactiva') {
            wp_enqueue_media(); // Necesario para el selector de medios de WordPress
            wp_enqueue_script(
                'bcard-admin-js',
                BCARD_PLUGIN_URL . 'admin/bcard-admin.js',
                ['jquery'], '2.0', true
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'bcard_enqueue_admin_assets');

// === 3. CREACIÓN DEL META BOX Y SUS CAMPOS (Reemplazo de ACF) ===
// Añadir el Meta Box al CPT
function bcard_add_meta_box() {
    add_meta_box(
        'bcard_data_metabox',
        'Datos de la Tarjeta Interactiva',
        'bcard_metabox_html_callback',
        'tarjeta_interactiva', // El CPT donde aparecerá
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'bcard_add_meta_box');

// HTML del Meta Box
function bcard_metabox_html_callback($post) {
    // Nonce de seguridad
    wp_nonce_field('bcard_save_meta_box_data', 'bcard_meta_box_nonce');

    // Obtener valores guardados
    $button_text = get_post_meta($post->ID, '_bcard_button_text', true);
    $button_url = get_post_meta($post->ID, '_bcard_button_url', true);
    $card_type = get_post_meta($post->ID, '_bcard_card_type', true);
    $image_id = get_post_meta($post->ID, '_bcard_image_id', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
    ?>
    <style>
        .bcard-meta-field { margin: 15px 0; }
        .bcard-meta-field label { display: block; font-weight: bold; margin-bottom: 5px; }
        .bcard-meta-field input, .bcard-meta-field select { width: 100%; max-width: 500px; }
        #bcard_image_preview { max-width: 300px; height: auto; border: 1px solid #ccc; padding: 5px; margin-top: 10px; }
        #bcard_image_preview[src=""] { display: none; }
    </style>
    
    <div class="bcard-meta-field">
        <label for="bcard_button_text">Texto del Botón</label>
        <input type="text" id="bcard_button_text" name="bcard_button_text" value="<?php echo esc_attr($button_text); ?>">
    </div>

    <div class="bcard-meta-field">
        <label for="bcard_button_url">URL del Botón</label>
        <input type="url" id="bcard_button_url" name="bcard_button_url" value="<?php echo esc_url($button_url); ?>">
    </div>

    <div class="bcard-meta-field">
        <label for="bcard_card_type">Tipo de Tarjeta</label>
        <select name="bcard_card_type" id="bcard_card_type">
            <option value="abierta" <?php selected($card_type, 'abierta'); ?>>Tarjeta expandible</option>
            <option value="cerrada" <?php selected($card_type, 'cerrada'); ?>>Tarjeta compacta</option>
        </select>
    </div>

    <div class="bcard-meta-field">
        <label for="bcard_image">Imagen de Fondo</label>
        <input type="hidden" name="bcard_image_id" id="bcard_image_id" value="<?php echo esc_attr($image_id); ?>" />
        <button type="button" class="button" id="bcard_upload_image_button">Seleccionar Imagen</button>
        <button type="button" class="button" id="bcard_remove_image_button" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Quitar Imagen</button>
        <div>
            <img id="bcard_image_preview" src="<?php echo esc_url($image_url); ?>">
        </div>
    </div>
    <?php
}

// Guardar los datos del Meta Box
function bcard_save_postdata($post_id) {
    // Verificar nonce
    if (!isset($_POST['bcard_meta_box_nonce']) || !wp_verify_nonce($_POST['bcard_meta_box_nonce'], 'bcard_save_meta_box_data')) {
        return;
    }
    // No guardar en autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Guardar datos sanitizados
    update_post_meta($post_id, '_bcard_button_text', sanitize_text_field($_POST['bcard_button_text']));
    update_post_meta($post_id, '_bcard_button_url', esc_url_raw($_POST['bcard_button_url']));
    update_post_meta($post_id, '_bcard_card_type', sanitize_text_field($_POST['bcard_card_type']));
    update_post_meta($post_id, '_bcard_image_id', sanitize_text_field($_POST['bcard_image_id']));
}
add_action('save_post', 'bcard_save_postdata');

// === 4. SHORTCODE (Modificado para usar get_post_meta) ===
function bcard_display_shortcode($atts) {
    $args = [ 'post_type' => 'tarjeta_interactiva', 'posts_per_page' => 3, 'orderby' => 'menu_order', 'order' => 'ASC' ];
    $cards_query = new WP_Query($args);

    if ($cards_query->have_posts()) {
        $output = '<div class="bcard-container">';
        while ($cards_query->have_posts()) {
            $cards_query->the_post();
            $post_id = get_the_ID();

            // Usamos get_post_meta en lugar de get_field()
            $button_text = get_post_meta($post_id, '_bcard_button_text', true);
            $button_url = get_post_meta($post_id, '_bcard_button_url', true) ?: '#';
            $card_type = get_post_meta($post_id, '_bcard_card_type', true) ?: 'cerrada';
            $image_id = get_post_meta($post_id, '_bcard_image_id', true);
            $bg_image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : ''; // Usar 'large' para mejor calidad
            
            $bg_style = $bg_image_url ? 'style="background-image: url(' . esc_url($bg_image_url) . ');"' : '';

            // El resto de la lógica HTML es idéntica
            $output .= '<div class="bcard bcard--' . esc_attr($card_type) . '" ' . $bg_style . ' tabindex="0">';
                $output .= '<div class="bcard-overlay"></div>';
                $output .= '<div class="bcard-content">';
                if ('abierta' === $card_type) {
                    $output .= '<div class="bcard-header"><h2>' . get_the_title() . '</h2><div class="bcard-arrow-icon">→</div></div>';
                    $output .= '<div class="bcard-body"><p>' . get_the_content() . '</p>';
                    $output .= '<a href="' . esc_url($button_url) . '" class="bcard-btn"><span class="bcard-btn-icon">&rsaquo;</span><span class="bcard-btn-text">' . esc_html($button_text) . '</span></a></div>';
                } else {
                    $output .= '<div class="bcard-header-static"><h2>' . get_the_title() . '</h2></div>';
                    $output .= '<a href="' . esc_url($button_url) . '" class="bcard-btn-static"><span class="bcard-btn-icon">★</span><span class="bcard-btn-text">' . esc_html($button_text) . '</span></a>';
                }
                $output .= '</div></div>';
        }
        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output = '<p>No hay tarjetas interactivas para mostrar. Por favor, créalas en el panel de administración.</p>';
    }
    return $output;
}
add_shortcode('tarjetas_interactivas', 'bcard_display_shortcode');