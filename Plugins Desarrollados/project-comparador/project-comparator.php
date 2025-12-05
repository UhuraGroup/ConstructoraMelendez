<?php
/**
 * Plugin Name:       Comparador de Proyectos ACF
 * Description:       Añade un comparador de proyectos dinámico basado en un CPT y ACF. Usar con el shortcode [project_comparator].
 * Version:           1.1.0
 * Author:            Oscar Cerpa
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// 1. Enqueue (cargar) los scripts y estilos necesarios
function project_comparator_enqueue_assets() {
    // Solo carga los archivos si el shortcode está presente en la página actual
    if ( is_a( get_post( get_the_ID() ), 'WP_Post' ) && has_shortcode( get_post( get_the_ID() )->post_content, 'project_comparator' ) ) {
        
        // Cargar el archivo CSS
        wp_enqueue_style(
            'project-comparator-styles',
            plugin_dir_url( __FILE__ ) . 'comparator-styles.css',
            array(),
            '1.0.0'
        );

        // Cargar el archivo JavaScript
        wp_enqueue_script(
            'project-comparator-scripts',
            plugin_dir_url( __FILE__ ) . 'comparator-scripts.js',
            array( 'jquery' ), // Depende de jQuery para simplicidad
            '1.0.0',
            true // Cargar en el footer
        );
        
        // 2. Preparar y pasar los datos de los proyectos a JavaScript
        $all_projects_data = [];
        $args = array(
            'post_type' => 'proyecto', // <-- ¡IMPORTANTE! Revisa que este sea el slug de tu CPT
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        $projects_query = new WP_Query($args);

        if ($projects_query->have_posts()) {
            while ($projects_query->have_posts()) {
                $projects_query->the_post();
                $project_id = get_the_ID();
                
                $grupo_areas = get_field('areas_del_inmueble', $project_id);

                // Obtener datos de ACF
                // <-- ¡IMPORTANTE! Revisa que estos sean los nombres de tus campos ACF
                $logo = get_field('logo', $project_id);
                
                $all_projects_data[] = [
                    'id' => $project_id,
                    'name' => get_the_title(),
                    'logoUrl' => $logo ? $logo['url'] : plugin_dir_url( __FILE__ ) . 'placeholder.png', // Usa un placeholder si no hay logo
                    'tipo' => get_field('tipo_de_inmueble', $project_id),
                    'precio' => number_format(get_field('precio_cop', $project_id), 0, ',', '.'),
                    
                    'areaConstruida' => $grupo_areas ? $grupo_areas['area_total_construida'] : 'N/A',
                    'areaPrivada' => $grupo_areas ? $grupo_areas['area_privada'] : 'N/A',
                ];
            }
        }
        wp_reset_postdata();

        // Pasa los datos al script de JavaScript de forma segura
        wp_localize_script('project-comparator-scripts', 'projectData', $all_projects_data);
    }
}
add_action('wp_enqueue_scripts', 'project_comparator_enqueue_assets');


// 3. Crear el Shortcode [project_comparator]
function project_comparator_shortcode_callback() {
    // Obtener la lista de proyectos para los dropdowns
    $projects = get_posts(array(
        'post_type' => 'proyecto', // <-- ¡IMPORTANTE! Revisa que este sea el slug de tu CPT
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));

    // Iniciar el buffer de salida para capturar el HTML
    ob_start();
    ?>
    <div class="comparator-container">
        <div class="comparator-selectors">
            <p class="selector-title">SELECCIONA LOS PROYECTOS<br>QUE DESEAS COMPARAR.</p>
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="project-selector-wrapper" id="selector-wrapper-<?php echo $i; ?>">
                    <div class="project-logo-preview" id="logo-preview-<?php echo $i; ?>"></div>
                    <select class="project-selector" id="project-selector-<?php echo $i; ?>" data-column="<?php echo $i; ?>">
                        <option value="">Selecciona un proyecto</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo $project->ID; ?>"><?php echo esc_html($project->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endfor; ?>
        </div>

        <div class="comparator-table-wrapper">
            <table class="comparator-table">
                <tbody>
                    <tr>
                        <th class="first-col"></th>
                        <td id="comp-name-1" class="comp-name"></td>
                        <td id="comp-name-2" class="comp-name"></td>
                        <td id="comp-name-3" class="comp-name"></td>
                    </tr>
                    <tr>
                        <th class="first-col">Tipo de inmueble</th>
                        <td id="comp-tipo-1"></td>
                        <td id="comp-tipo-2"></td>
                        <td id="comp-tipo-3"></td>
                    </tr>
                    <tr>
                        <th class="first-col">Precio (millones de pesos) desde</th>
                        <td id="comp-precio-1"></td>
                        <td id="comp-precio-2"></td>
                        <td id="comp-precio-3"></td>
                    </tr>
                    <tr>
                        <th class="first-col">Área construida (m²) desde</th>
                        <td id="comp-areaConstruida-1"></td>
                        <td id="comp-areaConstruida-2"></td>
                        <td id="comp-areaConstruida-3"></td>
                    </tr>
                    <tr>
                        <th class="first-col">Área privada (m²) desde</th>
                        <td id="comp-areaPrivada-1"></td>
                        <td id="comp-areaPrivada-2"></td>
                        <td id="comp-areaPrivada-3"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    // Devolver el HTML capturado
    return ob_get_clean();
}
add_shortcode('project_comparator', 'project_comparator_shortcode_callback');