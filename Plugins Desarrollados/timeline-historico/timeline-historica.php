<?php
/**
 * Plugin Name:         Timeline Histórica Personalizable
 * Description:         Añade una línea de tiempo histórica con íconos SVG personalizados mediante un CPT y un shortcode.
 * Version:             1.6.2
 * Author:              Tu Nombre
 * Author URI:          https://tu-web.com
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         timeline-historica
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// --- Las funciones de CPT, Meta Box, y guardado de datos no cambian ---
function th_register_cpt() {
    $labels = [ 'name' => _x('Eventos del Timeline', 'post type general name', 'timeline-historica'), 'singular_name' => _x('Evento', 'post type singular name', 'timeline-historica'), 'menu_name' => _x('Timeline Histórica', 'admin menu', 'timeline-historica'), 'name_admin_bar' => _x('Evento', 'add new on admin bar', 'timeline-historica'), 'add_new' => _x('Añadir Nuevo', 'evento', 'timeline-historica'), 'add_new_item' => __('Añadir Nuevo Evento', 'timeline-historica'), 'new_item' => __('Nuevo Evento', 'timeline-historica'), 'edit_item' => __('Editar Evento', 'timeline-historica'), 'view_item' => __('Ver Evento', 'timeline-historica'), 'all_items' => __('Todos los Eventos', 'timeline-historica'), 'search_items' => __('Buscar Eventos', 'timeline-historica'), 'not_found' => __('No se encontraron eventos.', 'timeline-historica'), 'not_found_in_trash' => __('No se encontraron eventos en la papelera.', 'timeline-historica'), ];
    $args = [ 'labels' => $labels, 'public' => true, 'publicly_queryable' => true, 'show_ui' => true, 'show_in_menu' => true, 'query_var' => true, 'rewrite' => ['slug' => 'evento-timeline'], 'capability_type' => 'post', 'has_archive' => true, 'hierarchical' => false, 'menu_position' => 20, 'menu_icon' => 'dashicons-clock', 'supports' => ['title', 'editor'], ];
    register_post_type('th_evento', $args);
}
add_action('init', 'th_register_cpt');
function th_allow_svg_upload($mimes) { $mimes['svg'] = 'image/svg+xml'; return $mimes; }
add_filter('upload_mimes', 'th_allow_svg_upload');
function th_fix_svg_thumb_display($response, $attachment, $meta) { if ('image/svg+xml' === $response['mime']) { $response['sizes']['thumbnail'] = [ 'url' => $response['url'], 'width' => $response['width'], 'height' => $response['height'], ]; } return $response; }
add_filter('wp_prepare_attachment_for_js', 'th_fix_svg_thumb_display', 10, 3);
function th_add_meta_boxes() { add_meta_box('th_evento_details', 'Detalles del Evento', 'th_render_meta_box', 'th_evento', 'side', 'high'); }
add_action('add_meta_boxes', 'th_add_meta_boxes');
function th_render_meta_box($post) {
    wp_nonce_field('th_save_evento_data', 'th_meta_box_nonce');
    $year = get_post_meta($post->ID, '_th_evento_year', true);
    $icon_id = get_post_meta($post->ID, '_th_evento_icon_id', true);
    $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
    ?>
    <p><label for="th_evento_year"><strong>Año del Evento:</strong></label><br><input type="text" id="th_evento_year" name="th_evento_year" value="<?php echo esc_attr($year); ?>" style="width:100%;"></p><hr>
    <div><strong>Ícono SVG:</strong><div class="th-icon-preview-wrapper" style="margin-top:10px; padding:10px; border:1px dashed #ddd; min-height:60px; background:#f9f9f9; text-align:center;"><?php if ($icon_url): ?><img src="<?php echo esc_url($icon_url); ?>" style="max-width:50px; max-height:50px;"><?php endif; ?></div><input type="hidden" name="th_evento_icon_id" id="th_evento_icon_id" value="<?php echo esc_attr($icon_id); ?>"><button type="button" class="button" id="th_upload_icon_button" style="width:100%; margin-top:5px;">Seleccionar Ícono</button><button type="button" class="button button-link-delete" id="th_remove_icon_button" style="width:100%; margin-top:5px; text-align:center; <?php echo $icon_id ? '' : 'display:none;'; ?>">Quitar Ícono</button></div>
    <script>jQuery(document).ready(function($){ var mediaUploader; $('#th_upload_icon_button').click(function(e) { e.preventDefault(); if (mediaUploader) { mediaUploader.open(); return; } mediaUploader = wp.media.frames.file_frame = wp.media({ title: 'Seleccionar Ícono SVG', button: { text: 'Usar este ícono' }, multiple: false, library: { type: 'image/svg+xml' } }); mediaUploader.on('select', function() { var attachment = mediaUploader.state().get('selection').first().toJSON(); $('#th_evento_icon_id').val(attachment.id); $('.th-icon-preview-wrapper').html('<img src="' + attachment.url + '" style="max-width:50px; max-height:50px;">'); $('#th_remove_icon_button').show(); }); mediaUploader.open(); }); $('#th_remove_icon_button').click(function(e) { e.preventDefault(); $('#th_evento_icon_id').val(''); $('.th-icon-preview-wrapper').html(''); $(this).hide(); }); });</script>
    <?php
}
function th_save_post_meta($post_id) { if (!isset($_POST['th_meta_box_nonce']) || !wp_verify_nonce($_POST['th_meta_box_nonce'], 'th_save_evento_data')) return; if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return; if (!current_user_can('edit_post', $post_id)) return; if (isset($_POST['th_evento_year'])) { update_post_meta($post_id, '_th_evento_year', sanitize_text_field($_POST['th_evento_year'])); } if (isset($_POST['th_evento_icon_id'])) { update_post_meta($post_id, '_th_evento_icon_id', sanitize_text_field($_POST['th_evento_icon_id'])); } }
add_action('save_post', 'th_save_post_meta');
function th_enqueue_admin_scripts($hook) { global $post_type; if ('th_evento' == $post_type && ('post.php' == $hook || 'post-new.php' == $hook)) { wp_enqueue_media(); } }
add_action('admin_enqueue_scripts', 'th_enqueue_admin_scripts');
function th_enqueue_styles() { wp_register_style('th-style', plugin_dir_url(__FILE__) . 'css/style.css', [], '1.6.1'); }
add_action('wp_enqueue_scripts', 'th_enqueue_styles');

/**
 * CREAR EL SHORTCODE [timeline_historica]
 * La clase 'evento-activo' se añade siempre al primer elemento. El CSS se encarga de la lógica visual.
 */
function th_render_timeline_shortcode() {
    wp_enqueue_style('th-style');
    $args = ['post_type' => 'th_evento', 'posts_per_page' => -1, 'meta_key' => '_th_evento_year', 'orderby' => 'meta_value_num', 'order' => 'ASC'];
    $events_query = new WP_Query($args);
    if (!$events_query->have_posts()) { return '<p>No hay eventos en la línea de tiempo todavía.</p>'; }
    
    ob_start();
    echo '<div id="mi-linea-de-tiempo-unica">';
    echo '  <div class="brx-timeline-line"></div>';
    echo '  <div class="brx-timeline-events-wrapper">';
    
    $is_first_event = true; 
    
    while ($events_query->have_posts()) {
        $events_query->the_post();
        $post_id = get_the_ID();
        $year = get_post_meta($post_id, '_th_evento_year', true);
        $icon_id = get_post_meta($post_id, '_th_evento_icon_id', true);
        $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'full') : '';

        // Se determina la clase para el evento. El primer evento siempre tendrá la clase 'evento-activo'.
        $event_classes = 'brx-timeline-event';
        if ($is_first_event) {
            $event_classes .= ' evento-activo';
            $is_first_event = false;
        }

        echo '<div class="' . esc_attr($event_classes) . '">';

        // Contenido que va arriba de la línea (ícono, año, tooltip)
        echo '  <div class="brx-timeline-content">';
        echo '      <div class="brx-timeline-marker">';
        if (!empty($icon_url)) {
            echo '      <img src="' . esc_url($icon_url) . '" class="brx-timeline-icon" alt="Ícono para ' . esc_attr(get_the_title()) . '">';
        }
        echo '          <div class="brx-timeline-year">' . esc_html($year) . '</div>';
        echo '      </div>';
        echo '      <div class="brx-timeline-description">';
        echo '          <h3>' . get_the_title() . '</h3>';
        echo '          <div>' . get_the_content() . '</div>';
        echo '      </div>';
        echo '  </div>';
        // El punto rojo que va sobre la línea
        echo '  <div class="brx-timeline-axis-point"></div>';
        echo '</div>';
    }
    
    echo '  </div>';
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('timeline_historica', 'th_render_timeline_shortcode');