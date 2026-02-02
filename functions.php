<?php
//Child theme setting
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
function enqueue_child_theme_styles(){
  	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// Enqueue backend styles
add_action( 'admin_enqueue_scripts', 'enqueue_backend_styles' );
function enqueue_backend_styles() {
    wp_enqueue_style( 'st-backend-style', get_stylesheet_directory_uri() . '/assets/css/st_backend_style.css' );
}

// Enqueue boostrap scripts and styles
add_action( 'wp_enqueue_scripts', 'enqueue_boostrap_scripts' );
function enqueue_boostrap_scripts() {
    wp_enqueue_style( 'boostrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css' );
    wp_enqueue_script( 'boostrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', true );
}

// Enqueue Swiper scripts and styles
add_action( 'wp_enqueue_scripts', 'enqueue_swiper_scripts' );
function enqueue_swiper_scripts() {
    wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css' );
    wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js', array(), '12.0.0', true );
    // Swiper init
    wp_enqueue_script( 'single-tile-js', get_stylesheet_directory_uri() . '/assets/js/single-tile.js', array('swiper-js'), '1.0.0', true );
}

// Enqueue archive collection category JS
add_action( 'wp_enqueue_scripts', 'enqueue_archive_collection_scripts' );
function enqueue_archive_collection_scripts() {
    if ( is_tax( 'product_category' ) ) {
        wp_enqueue_script( 'archive-collection-category-js', get_stylesheet_directory_uri() . '/assets/js/archive-collection-category.js', array('jquery'), '1.0.0', true );
        wp_localize_script( 'archive-collection-category-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
}

// AJAX handler for load more collection
add_action('wp_ajax_load_more_collections', 'load_more_collections');
add_action('wp_ajax_nopriv_load_more_collections', 'load_more_collections');
function load_more_collections() {
    $offset = intval($_POST['offset']);
    $limit = intval($_POST['limit']) ?: 12;
    $term_ids = isset($_POST['term_ids']) ? $_POST['term_ids'] : null;

    // Handle comma-separated term IDs
    if ($term_ids && strpos($term_ids, ',') !== false) {
        $term_ids = array_map('intval', explode(',', $term_ids));
    } elseif ($term_ids) {
        $term_ids = intval($term_ids);
    }

    $html = get_collections_html($offset, $limit, $term_ids, true);
    wp_send_json_success($html);
}

// AJAX handler for load more project
add_action('wp_ajax_load_more_projects', 'load_more_projects');
add_action('wp_ajax_nopriv_load_more_projects', 'load_more_projects');
function load_more_projects() {
    $offset = intval($_POST['offset']);
    $limit = intval($_POST['limit']) ?: 12;
    $term_ids = isset($_POST['term_ids']) ? $_POST['term_ids'] : null;

    // Handle comma-separated term IDs
    if ($term_ids && strpos($term_ids, ',') !== false) {
        $term_ids = array_map('intval', explode(',', $term_ids));
    } elseif ($term_ids) {
        $term_ids = intval($term_ids);
    }

    $html = get_project_html($offset, $limit, $term_ids, true);
    wp_send_json_success($html);
}

// AJAX handler for filter collections - commented out for local filtering
// add_action('wp_ajax_filter_collections', 'filter_collections');
// add_action('wp_ajax_nopriv_filter_collections', 'filter_collections');
// function filter_collections() {
//     $term_ids = isset($_POST['term_ids']) ? array_map('intval', $_POST['term_ids']) : array();
//     $limit = 6; // or adjust as needed
//     $html = get_collections_html(0, $limit, $term_ids);
//     wp_send_json_success($html);
// }

//IMPORT PRODUCT

add_action('init', function() {
    if (!isset($_GET['run_tile_multi_import'])) return;

    $tile_rows = [  
        ['name' => 'Wall Tile Black Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT04037', 'size' => '100x100'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01030', 'size' => '100x100'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT01051', 'size' => '100x100'],
['name' => 'Wall Tile Black Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT06039', 'size' => '100x200'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01025', 'size' => '100x200'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT01026', 'size' => '100x200'],
['name' => 'Wall Tile Black Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01010', 'size' => '100x300'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01003', 'size' => '100x300'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT01004', 'size' => '100x300'],
['name' => 'XL Arabescato', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22001', 'size' => '1200x1200'],
['name' => 'XL Blue Roma', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22224', 'size' => '1200x1200'],
['name' => 'XL Bright Precious', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22209', 'size' => '1200x1200'],
['name' => 'XL Calacatta Viola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22223', 'size' => '1200x1200'],
['name' => 'XL Cellini', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22172', 'size' => '1200x1200'],
['name' => 'XL Invisible Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22325', 'size' => '1200x1200'],
['name' => 'XL Lilac', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22110', 'size' => '1200x1200'],
['name' => 'XL Marquina Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22111', 'size' => '1200x1200'],
['name' => 'XL Pierre Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22107', 'size' => '1200x1200'],
['name' => 'XL Pietra Taupe', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22225', 'size' => '1200x1200'],
['name' => 'XL Statuario Superiore', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22109', 'size' => '1200x1200'],
['name' => 'XL White Beauty', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22208', 'size' => '1200x1200'],
['name' => 'XL Arabescato', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22106', 'size' => '1200x2700x6'],
['name' => 'XL Invisible Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22170', 'size' => '1200x2700x6'],
['name' => 'XL Pierre Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22107', 'size' => '1200x2700x6'],
['name' => 'XL Pietra Taupe', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22225', 'size' => '1200x2700x6'],
['name' => 'XL Statuario Superiore', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22109', 'size' => '1200x2700x6'],
['name' => 'XL Super White Dolomite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22177', 'size' => '1200x2700x6'],
['name' => 'XL Tivoli Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22226', 'size' => '1200x2780x6'],
['name' => 'XL Tivoli White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22227', 'size' => '1200x2780x6'],
['name' => 'XL Blue Roma', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22223', 'size' => '1200x2800x5.6'],
['name' => 'XL Blue Roma', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22209', 'size' => '1200x2800x6'],
['name' => 'XL Calacatta Viola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22224', 'size' => '1200x2800x6'],
['name' => 'XL Lilac', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22010', 'size' => '1200x2800x6'],
['name' => 'XL Marquina Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22111', 'size' => '1200x2800x6'],
['name' => 'XL White Beauty', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22208', 'size' => '1200x2800x6'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01066', 'size' => '150x150'],
['name' => 'XL Arabescato Viola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22175', 'size' => '1600x3200x12'],
['name' => 'XL Ballet', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22089', 'size' => '1600x3200x12'],
['name' => 'XL Calacatta Gold', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22083', 'size' => '1600x3200x12'],
['name' => 'XL Calacatta Oro', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22085', 'size' => '1600x3200x12'],
['name' => 'XL Calacatta Ultimate', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22091', 'size' => '1600x3200x12'],
['name' => 'XL Caravaggio Gold', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22176', 'size' => '1600x3200x12'],
['name' => 'XL Grigio Veneziano', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22090', 'size' => '1600x3200x12'],
['name' => 'XL Invisible Gold', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22202', 'size' => '1600x3200x12'],
['name' => 'XL Light Red Calacatta', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22174', 'size' => '1600x3200x12'],
['name' => 'XL Metro Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'PS22092', 'size' => '1600x3200x12'],
['name' => 'XL Pacista', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'PS22206', 'size' => '1600x3200x12'],
['name' => 'XL Pietra Grigia', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22086', 'size' => '1600x3200x12'],
['name' => 'XL Statuario Reale', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22084', 'size' => '1600x3200x12'],
['name' => 'XL Super Black', 'indent' => '', 'design' => '', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'PS22094', 'size' => '1600x3200x12'],
['name' => 'XL Super White', 'indent' => '', 'design' => '', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'PS22093', 'size' => '1600x3200x12'],
['name' => 'XL Super White Dolomite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22178', 'size' => '1600x3200x12'],
['name' => 'XL Absolute', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22087', 'size' => '1600x3200x12mm'],
['name' => 'XL Bardiglio', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22101', 'size' => '1600X3200x6'],
['name' => 'XL Basaltina Nero', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22119', 'size' => '1600x3200x6'],
['name' => 'XL Calacatta Gold', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22114', 'size' => '1600x3200x6'],
['name' => 'XL Calacatta Ultimate', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22191', 'size' => '1600x3200x6'],
['name' => 'XL Calacatta Ultimate', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'PS22291', 'size' => '1600x3200x6'],
['name' => 'XL Invisible Gold', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'PS22214', 'size' => '1600x3200x6'],
['name' => 'XL Lilac', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22110', 'size' => '1600x3200x6'],
['name' => 'XL Macchia Antica', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22098', 'size' => '1600x3200x6'],
['name' => 'XL Macchia Vecchia', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22212', 'size' => '1600x3200x6'],
['name' => 'XL Pacista', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'PS22213', 'size' => '1600x3200x6'],
['name' => 'XL Statuario Reale', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22115', 'size' => '1600x3200x6'],
['name' => 'XL Statuario Reale', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22015', 'size' => '1600x3200x6'],
['name' => 'XL Statuario Extra', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22008', 'size' => '1600x3200x6'],
['name' => 'XL Statuario Extra', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22108', 'size' => '1600x3200x6'],
['name' => 'XL Absolute', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22118', 'size' => '1600x3200x6.5'],
['name' => 'XL Arabescato Viola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22167', 'size' => '1600x3200x6.5'],
['name' => 'XL Ballet', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22120', 'size' => '1600x3200x6.5'],
['name' => 'XL Calacatta Oro', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22116', 'size' => '1600x3200x6.5'],
['name' => 'XL Caravaggio Gold', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22168', 'size' => '1600x3200x6.5'],
['name' => 'XL Emotion Euphoria', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22095', 'size' => '1600x3200x6.5'],
['name' => 'XL Grigio Veneziano', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22121', 'size' => '1600x3200x6.5'],
['name' => 'XL Light Red Calacatta', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22166', 'size' => '1600x3200x6.5'],
['name' => 'XL Pietra Grigia', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22117', 'size' => '1600x3200x6.5'],
['name' => 'XL Statuario Premium', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22055', 'size' => '1600x3200x6.5'],
['name' => 'XL Statuario Premium', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'PS22155', 'size' => '1600x3200x6.5'],
['name' => 'XL Blue Roma', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22222', 'size' => '1620x3240x12'],
['name' => 'XL Macchia Antica', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22203', 'size' => '1640x3230x12'],
['name' => 'Wall Tile Black Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT04050', 'size' => '200x200'],
['name' => 'Wall Tile Black Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT04010', 'size' => '200x200'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01042', 'size' => '200x200'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT04013', 'size' => '200x200'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01050', 'size' => '200x300'],
['name' => 'Piazza Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03320', 'size' => '300x300'],
['name' => 'Piazza Chalk', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03394', 'size' => '300x300'],
['name' => 'Voltesso Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02363', 'size' => '300x300'],
['name' => 'Voltesso Graphite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02364', 'size' => '300x300'],
['name' => 'Voltesso Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02361', 'size' => '300x300'],
['name' => 'Voltesso Light', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02362', 'size' => '300x300'],
['name' => 'Piazza Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03320', 'size' => '300x600'],
['name' => 'Piazza Chalk', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03394', 'size' => '300x600'],
['name' => 'Voltesso Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02363', 'size' => '300x600'],
['name' => 'Voltesso Graphite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02364', 'size' => '300x600'],
['name' => 'Voltesso Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02361', 'size' => '300x600'],
['name' => 'Voltesso Light', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02362', 'size' => '300x600'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01001', 'size' => '300x600'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT01002', 'size' => '300x600'],
['name' => 'XL Arabescato', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22001', 'size' => '300x600'],
['name' => 'XL Basaltina Nero', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22088', 'size' => '300x600'],
['name' => 'XL Macchia Vecchia', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22212', 'size' => '300x600'],
['name' => 'XL Marquina Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22111', 'size' => '300x600'],
['name' => 'XL Pierre Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22107', 'size' => '300x600'],
['name' => 'XL Pietra Taupe', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22225', 'size' => '300x600'],
['name' => 'XL Statuario Superiore', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22109', 'size' => '300x600'],
['name' => 'XL Macchia Vecchia', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22204', 'size' => '3200x1600x20'],
['name' => 'Metropolis Bone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03132', 'size' => '400x600x20'],
['name' => 'Metropolis Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03133', 'size' => '400x600x20'],
['name' => 'Metropolis Gypsum', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03134', 'size' => '400x600x20'],
['name' => 'Metropolis Smoke', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03135', 'size' => '400x600x20'],
['name' => 'Piazza Bianco Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP3062', 'size' => '400x600x20'],
['name' => 'Piazza Chalk Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP3093', 'size' => '400x600x20'],
['name' => 'Forma Antracita', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07277', 'size' => '56x224'],
['name' => 'Forma Arena', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07278', 'size' => '56x224'],
['name' => 'Forma Barro', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07279', 'size' => '56x224'],
['name' => 'Forma Chocolate', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07284', 'size' => '56x224'],
['name' => 'Forma Granada', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07280', 'size' => '56x224'],
['name' => 'Forma Gris', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07281', 'size' => '56x224'],
['name' => 'Forma Musgo', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07282', 'size' => '56x224'],
['name' => 'Forma Oceano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07283', 'size' => '56x224'],
['name' => 'Piazza Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03320', 'size' => '600x1200'],
['name' => 'Piazza Chalk', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03394', 'size' => '600x1200'],
['name' => 'Voltesso Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02363', 'size' => '600x1200'],
['name' => 'Voltesso Graphite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02364', 'size' => '600x1200'],
['name' => 'Voltesso Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02361', 'size' => '600x1200'],
['name' => 'Voltesso Light', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02362', 'size' => '600x1200'],
['name' => 'Calacatta Oro', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01117', 'size' => '600x1200'],
['name' => 'Calacatta Oro', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01118', 'size' => '600x1200'],
['name' => 'XL Arabescato', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22001', 'size' => '600x1200'],
['name' => 'XL Basaltina Nero', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22088', 'size' => '600x1200'],
['name' => 'XL Blue Roma', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22224', 'size' => '600x1200'],
['name' => 'XL Bright Precious', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22209', 'size' => '600x1200'],
['name' => 'XL Calacatta Viola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22223', 'size' => '600x1200'],
['name' => 'XL Cellini', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22172', 'size' => '600x1200'],
['name' => 'XL Invisible Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22325', 'size' => '600x1200'],
['name' => 'XL Lilac', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22110', 'size' => '600x1200'],
['name' => 'XL Macchia Vecchia', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22212', 'size' => '600x1200'],
['name' => 'XL Marquina Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22111', 'size' => '600x1200'],
['name' => 'XL Pierre Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22107', 'size' => '600x1200'],
['name' => 'XL Pietra Taupe', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22225', 'size' => '600x1200'],
['name' => 'XL Statuario Superiore', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22109', 'size' => '600x1200'],
['name' => 'XL Super White Dolomite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22324', 'size' => '600x1200'],
['name' => 'XL White Beauty', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22208', 'size' => '600x1200'],
['name' => 'Piazza Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03320', 'size' => '600x600'],
['name' => 'Piazza Chalk', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03394', 'size' => '600x600'],
['name' => 'Voltesso Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02363', 'size' => '600x600'],
['name' => 'Voltesso Graphite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02364', 'size' => '600x600'],
['name' => 'Voltesso Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02361', 'size' => '600x600'],
['name' => 'Voltesso Light', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02362', 'size' => '600x600'],
['name' => 'XL Basaltina Nero', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22088', 'size' => '600x600'],
['name' => 'XL Blue Roma', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22224', 'size' => '600x600'],
['name' => 'XL Bright Precious', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22209', 'size' => '600x600'],
['name' => 'XL Calacatta Viola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22223', 'size' => '600x600'],
['name' => 'XL Cellini', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22172', 'size' => '600x600'],
['name' => 'XL Invisible Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22325', 'size' => '600x600'],
['name' => 'XL Macchia Vecchia', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22212', 'size' => '600x600'],
['name' => 'XL Marquina Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22111', 'size' => '600x600'],
['name' => 'XL Pierre Black', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22107', 'size' => '600x600'],
['name' => 'XL Pietra Taupe', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22225', 'size' => '600x600'],
['name' => 'XL Statuario Superiore', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22109', 'size' => '600x600'],
['name' => 'XL Super White Dolomite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22324', 'size' => '600x600'],
['name' => 'XL White Beauty', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'SL22208', 'size' => '600x600'],
['name' => 'Basalt', 'indent' => '', 'design' => 'Basalt', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS01026', 'size' => '600x600x15'],
['name' => 'Voltesso Cinder Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03029', 'size' => '600x600x20'],
['name' => 'Voltesso Graphite Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03031', 'size' => '600x600x20'],
['name' => 'Voltesso Ivory Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03025', 'size' => '600x600x20'],
['name' => 'Voltesso Light Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03027', 'size' => '600x600x20'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT06058', 'size' => '65x400'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT06057', 'size' => '65x400'],
['name' => 'Wall Tile Black Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT04019', 'size' => '75x150'],
['name' => 'Wall Tile Black Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT06020', 'size' => '75x150'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01012', 'size' => '75x150'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT01013', 'size' => '75x150'],
['name' => 'Wall Tile Black Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01054', 'size' => '75x300'],
['name' => 'Wall Tile Black Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT01055', 'size' => '75x300'],
['name' => 'Wall Tile Dark Grey Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT13061', 'size' => '75x300'],
['name' => 'Wall Tile Dark Grey Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT13062', 'size' => '75x300'],
['name' => 'Wall Tile Light Grey Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT13058', 'size' => '75x300'],
['name' => 'Wall Tile Light Grey Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT13063', 'size' => '75x300'],
['name' => 'Wall Tile Taupe Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT13059', 'size' => '75x300'],
['name' => 'Wall Tile Taupe Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT13060', 'size' => '75x300'],
['name' => 'Wall Tile White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'WT01052', 'size' => '75x300'],
['name' => 'Wall Tile White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'WT01053', 'size' => '75x300'],
['name' => 'XL Cellini', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22172', 'size' => '900x2700x6'],
['name' => 'Kit Kat Cool White Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS08535', 'size' => 'Chip 22x145 - Sheet 296x299mm'],
['name' => 'Kit Kat Grey Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS08536', 'size' => 'Chip 22x145 - Sheet 296x299mm'],
['name' => 'Kit Kat Light Grey Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS08537', 'size' => 'Chip 22x145 - Sheet 296x299mm'],
['name' => 'Kit Kat Red Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS08538', 'size' => 'Chip 22x145 - Sheet 296x299mm'],
['name' => 'Kit Kat Warm White Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS08539', 'size' => 'Chip 22x145 - Sheet 296x299mm'],
['name' => 'Kit Kat Honey Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS08540', 'size' => 'Chip 22x145 - Sheet 296x300mm'],
['name' => 'Voltesso Cinder Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS02542', 'size' => 'Chip 45x95 - Sheet 298x298'],
['name' => 'Voltesso Graphite Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS02543', 'size' => 'Chip 45x95 - Sheet 298x298'],
['name' => 'Voltesso Ivory Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS02540', 'size' => 'Chip 45x95 - Sheet 298x298'],
['name' => 'Voltesso Light Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS02541', 'size' => 'Chip 45x95 - Sheet 298x298'],
['name' => 'Voltesso Cinder Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03030', 'size' => 'Drop Edge 400x600x20-60mm'],
['name' => 'Voltesso Graphite Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03032', 'size' => 'Drop Edge 400x600x20-60mm'],
['name' => 'Voltesso Ivory Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03026', 'size' => 'Drop Edge 400x600x20-60mm'],
['name' => 'Voltesso Light Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03028', 'size' => 'Drop Edge 400x600x20-60mm'],
['name' => 'Hand Crafted Antic Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06039', 'size' => '75x150'],
['name' => 'Hand Crafted Aqua', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06107', 'size' => '100x100'],
['name' => 'Hand Crafted Aqua', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06029', 'size' => '75x150'],
['name' => 'Hand Crafted Aqua', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06082', 'size' => '75x300'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06104', 'size' => '100x100'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06298', 'size' => '50x250'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06157', 'size' => '50x500'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06015', 'size' => '75x150'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06065', 'size' => '75x300'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06146', 'size' => '100x100'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06171', 'size' => '75x150'],
['name' => 'Hand Crafted Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06271', 'size' => '75x300'],
['name' => 'Hand Crafted Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06120', 'size' => '150x150'],
['name' => 'Hand Crafted Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06045', 'size' => '75x150'],
['name' => 'Hand Crafted Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06088', 'size' => '75x300'],
['name' => 'Hand Crafted Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06122', 'size' => '150x150'],
['name' => 'Hand Crafted Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06102', 'size' => '75x300'],
['name' => 'Hand Crafted Bone', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06016', 'size' => '75x150'],
['name' => 'Hand Crafted Bone', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06066', 'size' => '75x300'],
['name' => 'Hand Crafted Celery', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06027', 'size' => '75x150'],
['name' => 'Hand Crafted Celery', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06068', 'size' => '75x300'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06306', 'size' => '50x250'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06105', 'size' => '100x100'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06160', 'size' => '50x500'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06017', 'size' => '75x150'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06067', 'size' => '75x300'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06147', 'size' => '100x100'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06051', 'size' => '75x150'],
['name' => 'Hand Crafted Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06098', 'size' => '75x300'],
['name' => 'Hand Crafted Cloud', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06019', 'size' => '75x150'],
['name' => 'Hand Crafted Cloud', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06089', 'size' => '75x300'],
['name' => 'Hand Crafted Denim', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06041', 'size' => '75x150'],
['name' => 'Hand Crafted Denim', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06084', 'size' => '75x300'],
['name' => 'Hand Crafted Fossil', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06037', 'size' => '75x150'],
['name' => 'Hand Crafted Fossil', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06070', 'size' => '75x300'],
['name' => 'Hand Crafted Fossil', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06096', 'size' => '75x300'],
['name' => 'Hand Crafted Fossil', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06052', 'size' => '75x150'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06135', 'size' => '100x100'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06145', 'size' => '100x100'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06161', 'size' => '50x500'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06299', 'size' => '50x250'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06018', 'size' => '75x150'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06090', 'size' => '75x300'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06053', 'size' => '75x150'],
['name' => 'Hand Crafted Gris Titano', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06099', 'size' => '75x300'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06129', 'size' => '100x100'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06300', 'size' => '50x250'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06162', 'size' => '50x500'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06031', 'size' => '75x150'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06081', 'size' => '75x300'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06149', 'size' => '100x100'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06177', 'size' => '75x150'],
['name' => 'Hand Crafted Jade', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06178', 'size' => '75x300'],
['name' => 'Hand Crafted Marfil Ivory', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06094', 'size' => '75x300'],
['name' => 'Hand Crafted Marfil Ivory', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06014', 'size' => '75x150'],
['name' => 'Hand Crafted Marfil Ivory', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06064', 'size' => '75x300'],
['name' => 'Hand Crafted Marfil Ivory', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06047', 'size' => '75x150'],
['name' => 'Hand Crafted Marina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06242', 'size' => '75x150'],
['name' => 'Hand Crafted Marina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06128', 'size' => '100x100'],
['name' => 'Hand Crafted Marina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06117', 'size' => '150x150'],
['name' => 'Hand Crafted Marina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06301', 'size' => '50x250'],
['name' => 'Hand Crafted Marina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06170', 'size' => '50x500'],
['name' => 'Hand Crafted Marina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06086', 'size' => '75x300'],
['name' => 'Hand Crafted Marina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06042', 'size' => '75x150'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06106', 'size' => '100x100'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06302', 'size' => '50x250'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06163', 'size' => '50x500'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06036', 'size' => '75x150'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06069', 'size' => '75x300'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06050', 'size' => '75x150'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06176', 'size' => '75x150'],
['name' => 'Hand Crafted Menta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06095', 'size' => '75x300'],
['name' => 'Hand Crafted Midnight Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06044', 'size' => '75x150'],
['name' => 'Hand Crafted Midnight Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06087', 'size' => '75x300'],
['name' => 'Hand Crafted Verde Militaria', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06142', 'size' => '75x150'],
['name' => 'Hand Crafted Verde Militaria', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06140', 'size' => '100x100'],
['name' => 'Hand Crafted Verde Militaria', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06144', 'size' => '75x150'],
['name' => 'Hand Crafted Verde Militaria', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06130', 'size' => '100x100'],
['name' => 'Hand Crafted Verde Militaria', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06174', 'size' => '75x300'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06131', 'size' => '100x100'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06303', 'size' => '50x250'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06164', 'size' => '50x500'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06038', 'size' => '75x150'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06071', 'size' => '75x300'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06148', 'size' => '100x100'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06097', 'size' => '75x300'],
['name' => 'Hand Crafted Ocean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06054', 'size' => '75x150'],
['name' => 'Hand Crafted Olive', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06032', 'size' => '75x150'],
['name' => 'Hand Crafted Olive', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06074', 'size' => '75x300'],
['name' => 'Hand Crafted Pistachio', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06028', 'size' => '75x150'],
['name' => 'Hand Crafted Pistachio', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06073', 'size' => '75x300'],
['name' => 'Hand Crafted Rojo Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06079', 'size' => '75x300'],
['name' => 'Hand Crafted Rojo Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06024', 'size' => '75x150'],
['name' => 'Hand Crafted Hot Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06113', 'size' => '100x100'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06307', 'size' => '50x250'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06132', 'size' => '100x100'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06030', 'size' => '75x150'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06072', 'size' => '75x300'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06165', 'size' => '50x500'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06150', 'size' => '100x100'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06175', 'size' => '75x150'],
['name' => 'Hand Crafted Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06172', 'size' => '75x300'],
['name' => 'Hand Crafted Soft Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06173', 'size' => '75x300'],
['name' => 'Hand Crafted Soft Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06136', 'size' => '100x100'],
['name' => 'Hand Crafted Soft Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06141', 'size' => '75x150'],
['name' => 'Hand Crafted Soft Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06139', 'size' => '100x100'],
['name' => 'Hand Crafted Soft Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06143', 'size' => '75x150'],
['name' => 'Hand Crafted Sky Blue Spiritzig', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06040', 'size' => '75x150'],
['name' => 'Hand Crafted Sky Blue Spiritzig', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06083', 'size' => '75x300'],
['name' => 'Hand Crafted Verde Cobre', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06137', 'size' => '100x100'],
['name' => 'Hand Crafted Verde Cobre', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06305', 'size' => '50x250'],
['name' => 'Hand Crafted Verde Cobre', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06034', 'size' => '75x150'],
['name' => 'Hand Crafted Verde Cobre', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06076', 'size' => '75x300'],
['name' => 'Hand Crafted Verde Cobre', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06166', 'size' => '50x500'],
['name' => 'Hand Crafted Yellow', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06022', 'size' => '75x150'],
['name' => 'Hand Crafted Yellow', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06078', 'size' => '75x300'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06153', 'size' => '100x200'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06151', 'size' => '130x130'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06114', 'size' => '150x150'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06155', 'size' => '150x500'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06126', 'size' => '50x250'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06158', 'size' => '50x500'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06123', 'size' => '65x130'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06013', 'size' => '75x150'],
['name' => 'Hand Crafted White Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06063', 'size' => '75x300'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06154', 'size' => '100x200'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06152', 'size' => '130x130'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06121', 'size' => '150x150'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06156', 'size' => '150x500'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06127', 'size' => '50x250'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06159', 'size' => '50x500'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06124', 'size' => '65x130'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06046', 'size' => '75x150'],
['name' => 'Hand Crafted White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06093', 'size' => '75x300'],
['name' => 'Metropolis Bone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03128', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Metropolis Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03129', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Metropolis Gypsum', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03130', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Metropolis Smoke', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03131', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Piazza Bianco Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP3063', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Piazza Chalk Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP3094', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Piazza Bianco Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02318', 'size' => 'Sheet 298x298'],
['name' => 'Piazza Chalk Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL02319', 'size' => 'Sheet 298x298'],
['name' => 'Art Deco Bardiglio & Carrara & Nero', 'indent' => '', 'design' => 'Decor Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08088', 'size' => 'Sheet 306x306'],
['name' => 'Art Deco Rosa & Carrara & Nero', 'indent' => '', 'design' => 'Decor Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08087', 'size' => 'Sheet 306x306'],
['name' => 'Art Deco Rosso & New York & Nero Marquina', 'indent' => 'yes', 'design' => 'Decor Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10089', 'size' => 'Sheet 306x306'],
['name' => 'Art Deco Verdi Cristallo & Carrara & Nero Marquina', 'indent' => '', 'design' => 'Decor Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08086', 'size' => 'Sheet 306x306'],
['name' => 'XL White Beauty', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'natural', 'code' => 'PS22205', 'size' => 'XL12 White Beauty Natural 3200x1600x12'],
['name' => 'Luce Onyx White', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17194', 'size' => '1200x2800'],
['name' => 'Luce Onyx White', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17194', 'size' => '1200x2600'],
['name' => 'Luce Onyx Harlequin', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17195', 'size' => '1200x2800'],
['name' => 'Luce Onyx Harlequin', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17195', 'size' => '1200x2600'],
['name' => 'Luce Onyx Green Jade', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17196', 'size' => '1200x2800'],
['name' => 'Luce Bahia Azul', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17197', 'size' => '1200x2800'],
['name' => 'Luce Bahia Azul', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17197', 'size' => '1200x2600'],
['name' => 'Luce Onyx Ivory', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17198', 'size' => '1200x2800'],
['name' => 'Luce Onyx Arco Red', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17199', 'size' => '1200x2800'],
['name' => 'Luce Marble White', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17200', 'size' => '1200x2800'],
['name' => 'Luce Marble White', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17200', 'size' => '1200x2600'],
['name' => 'Luce Golden Marble', 'indent' => '', 'design' => 'Backlit Technology', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS17201', 'size' => '1200x2800'],
    ];

    // --- CONFIGURE ACF KEYS HERE ---
    $keys = [
        'tile_finish'    => 'field_68d3799c30127', // Key for the Finish Repeater
        'finish_name'    => 'field_68d379d530128', // Key for Finish Name (Select)
        'product_code'   => 'field_68d5fd67c4017', // Key for Product Code
        'tile_size'      => 'field_68d37a2c3012a', // Key for the Size Repeater (Nested)
        'tile_size_name' => 'field_68d37a5c3012b', // Key for Size Name
    ];

    foreach ($tile_rows as $row) {
        $row_name = trim($row['name']);
        
        $query = new WP_Query([
            'post_type' => 'tile', 'title' => $row_name, 'posts_per_page' => 1, 'post_status' => 'publish', 'fields' => 'ids'
        ]);
        $post_id = !empty($query->posts) ? $query->posts[0] : null;

        if (!$post_id) {
            $post_id = wp_insert_post(['post_title' => $row_name, 'post_type' => 'tile', 'post_status' => 'publish']);
            // Standard fields update by slug is fine
            update_field('tile_design', $row['design'], $post_id);
            update_field('tile_material', $row['material'], $post_id);
            update_field('tile_application', $row['application'], $post_id);
            update_field('tile_variation', $row['variation'], $post_id);
        }

        // --- THE REPEATER LOGIC (Using Keys for stability) ---
        $current_repeater = get_field($keys['tile_finish'], $post_id) ?: [];
        $found_index = -1;

        foreach ($current_repeater as $index => $item) {
            // We check both the name and the key index to be 100% safe
            $item_finish = $item['finish_name'] ?? $item[$keys['finish_name']] ?? '';
            $item_code   = $item['product_code'] ?? $item[$keys['product_code']] ?? '';

            if (strtolower(trim($item_finish)) == strtolower(trim($row['finish'])) && 
                strtolower(trim($item_code)) == strtolower(trim($row['code']))) {
                $found_index = $index;
                break;
            }
        }

        if ($found_index >= 0) {
            // Add size to existing row
            $sizes = $current_repeater[$found_index][$keys['tile_size']] ?? $current_repeater[$found_index]['tile_size'] ?? [];
            $sizes[] = [ $keys['tile_size_name'] => $row['size'] ];
            $current_repeater[$found_index][$keys['tile_size']] = $sizes;
        } else {
            // Create new finish row
            $current_repeater[] = [
                $keys['finish_name']  => $row['finish'],
                $keys['product_code'] => $row['code'],
                $keys['tile_size']    => [
                    [ $keys['tile_size_name'] => $row['size'] ]
                ]
            ];
        }

        // Save using the Parent Field Key
        update_field($keys['tile_finish'], $current_repeater, $post_id);
    }
    echo "Import using Field Keys finished!";
    exit;
});


// Enqueue all collection scripts
add_action( 'wp_enqueue_scripts', 'enqueue_all_collection_scripts' );
function enqueue_all_collection_scripts() {
    if ( is_page_template( 'all-collection.php' ) ) {
        wp_enqueue_script( 'all-collection-js', get_stylesheet_directory_uri() . '/assets/js/all-collection.js', array('jquery'), '1.0.0', true );
        $upload_dir = site_url() . '/wp-content/uploads';
        wp_localize_script( 'all-collection-js', 'collection_data', array(
            'xml_url' => $upload_dir . '/collections.xml'
        ) );
    }
}

// Enqueue load more scripts
add_action( 'wp_enqueue_scripts', 'enqueue_load_more_scripts' );
function enqueue_load_more_scripts() {
    // Load on taxonomy pages or all-collection page
    if ( is_tax( 'product_category' ) || is_page_template( 'all-collection.php' ) ) {
        wp_enqueue_script( 'load-more-items-js', get_stylesheet_directory_uri() . '/assets/js/load-more-items.js', array('jquery'), '1.0.0', true );
        wp_localize_script( 'load-more-items-js', 'st_ajax_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
    }
}

// Enqueue load more scripts
add_action( 'wp_enqueue_scripts', 'enqueue_load_more_project_scripts' );
function enqueue_load_more_project_scripts() {
    // Load on taxonomy pages or all-collection page
    if ( is_tax( 'project_category' ) || is_page_template( 'all-project.php' ) ) {
        wp_enqueue_script( 'load-more-project-js', get_stylesheet_directory_uri() . '/assets/js/load-more-project.js', array('jquery'), '1.0.0', true );
        wp_localize_script( 'load-more-project-js', 'st_ajax_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
    }
}

// Enqueue idea basket scripts
add_action( 'wp_enqueue_scripts', 'enqueue_idea_basket_scripts' );
function enqueue_idea_basket_scripts() {
    // Load on idea-basket page
    if ( is_page_template( 'idea-basket.php' ) ) {
        wp_enqueue_script( 'idea-basket-js', get_stylesheet_directory_uri() . '/assets/js/idea-basket.js', array('jquery'), '1.0.0', true );
    }
}

// Sets compression quality for both GD and ImageMagick engines
add_filter('wp_editor_set_quality', function($quality) { return 60; });

// Specifically targeting ImageMagick for more aggressive stripping of metadata
add_filter('wp_image_editor_before_save', function($editor) {
    if (method_exists($editor, 'set_quality')) {
        $editor->set_quality(60);
    }
    // This part removes EXIF data from cropped versions to save space
    if (method_exists($editor, 'strip_metadata')) {
        $editor->strip_metadata();
    }
    return $editor;
}, 10);

// Add custom image size
add_theme_support( 'post-thumbnails' );
add_image_size( 'project-vertical', 320, 480, true );

// Fix: Removed the leading space in 'add_custom_size'
add_filter( 'image_size_names_choose', 'add_custom_size' ); 
function add_custom_size( $sizes ) {
    return array_merge( $sizes, array(
        'project-vertical' => __( 'Project Vertical' ),
    ) );
}

// hard crop medium and large image size
update_option( 'medium_crop', 1 ); 
update_option( 'large_crop', 1 );

// limit output content length
function stCutText($text) {
    $maxLength = 140;

    // Check if text is already within limit
    if (strlen($text) <= $maxLength) {
        return $text;
    }

    // Truncate to 80 characters and find last space
    $truncated = substr($text, 0, $maxLength);
    $lastSpace = strrpos($truncated, ' ');

    if ($lastSpace !== false) {
        // Cut at last complete word
        return substr($truncated, 0, $lastSpace) . '...';
    } else {
        // No spaces found, hard truncate
        return $truncated . '...';
    }
}

// Function to get collections HTML
function get_collections_html($offset = 0, $limit = 12, $term_ids = null, $load_more = false, $container_class = 'collection-list-container fliter-collection') {
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $limit,
        'offset' => $offset,
    );
    
    if (is_tax('product_category')) {
        $term = get_queried_object();
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_category',
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ),
        );
    } elseif ($term_ids !== null && !empty($term_ids)) {
        $terms = is_array($term_ids) ? $term_ids : array($term_ids);
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_category',
                'field'    => 'term_id',
                'terms'    => $terms,
            ),
        );
    }
    // If term_ids is null and not on tax page, load all collections
    $query = new WP_Query($args);
    $slider_image_no = 0;
    $slider_thumb_no = 0;
    
    $html = '';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $title = get_the_title();
            $link = get_permalink();
            $collection_id = get_the_ID();
            $collection_thumb_preview = get_the_post_thumbnail($collection_id, 'medium');
            $collection_thumb_thumb = get_the_post_thumbnail($collection_id, 'thumbnail');
            
            //output html
            $html .= '<div class="collection-card">';
            $html .= '<div class="collection-inner-slider">';

            //get thumbnail images from each tiles
            $collection_tiles = get_field('tiles_in_collection', $collection_id);
            if($collection_tiles){ 
                $html .= '<div class="swiper collection-inner-slider-preview">';
                $html .= '<div class="swiper-wrapper">';
                $html .= '<div class="swiper-slide">' . $collection_thumb_preview . '</div>';
		        foreach($collection_tiles as $tile){
                    $slider_image_no = $slider_image_no + 1;
                    if($slider_image_no < 6){
                        $preview = get_the_post_thumbnail($tile, 'medium');
                        $html .= '<div class="swiper-slide">' . $preview . '</div>';
                    }
                }
                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div thumbsSlider="" class="swiper collection-inner-slider-thumb">';
                $html .= '<div class="swiper-wrapper">';
                $html .= '<div class="swiper-slide slider-thumbnail">' . $collection_thumb_thumb . '</div>';
                foreach($collection_tiles as $tile){
                    $slider_thumb_no = $slider_thumb_no + 1;
                    if($slider_thumb_no < 6){
                        $thumbnail = get_the_post_thumbnail($tile, 'thumbnail');
                        $html .= '<div class="swiper-slide slider-thumbnail">' . $thumbnail . '</div>';
                    } 
                }
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
            $html .= '<a href="' . esc_url($link) . '">';
            $html .= '<h3>' . esc_html($title) . '</h3>';
            $html .= '</a>';
            $html .= '</div>';
        }
        wp_reset_postdata();
    }

    // Add load more button if enabled and there are more posts
    if ($load_more) {
        // Get total count for this query
        $total_args = $args;
        $total_args['posts_per_page'] = -1; // Get all posts for count
        $total_query = new WP_Query($total_args);
        $total_posts = $total_query->found_posts;
        wp_reset_postdata();

        $current_count = $offset + $query->post_count;
        if ($current_count < $total_posts) {
            $next_offset = $current_count;
            $term_id_param = '';
            if (is_tax('product_category')) {
                $term = get_queried_object();
                $term_id_param = $term->term_id;
            } elseif ($term_ids !== null && !empty($term_ids)) {
                $term_id_param = is_array($term_ids) ? implode(',', $term_ids) : $term_ids;
            }

            $html .= '<div class="load-more-container" style="text-align: center; margin: 20px 0;">';
            $html .= '<button class="load-more-filtered-btn btn st-link-button small-style" data-offset="' . $next_offset . '" data-limit="' . $limit . '" data-term-ids="' . $term_id_param . '" data-total="' . $total_posts . '">Load More Collections</button>';
            $html .= '</div>';
        }
    }

    if($offset == 0){
        $html = '<div class="' . esc_attr($container_class) . '">' . $html . '</div>';
    }
    
    return $html;
}

function get_project_html($offset = 0, $limit = 12, $term_ids = null, $load_more = false, $container_class = 'project-list-container no-fliter') {
    $args = array(
        'post_type' => 'project',
        'posts_per_page' => $limit,
        'offset' => $offset,
    );
    
    if (is_tax('project_category')) {
        $term = get_queried_object();
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'project_category',
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ),
        );
    } elseif ($term_ids !== null && !empty($term_ids)) {
        $terms = is_array($term_ids) ? $term_ids : array($term_ids);
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'project_category',
                'field'    => 'term_id',
                'terms'    => $terms,
            ),
        );
    }
    // If term_ids is null and not on tax page, load all projects
    $query = new WP_Query($args);
    
    $html = '';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $title = get_the_title();
            $link = get_permalink();
            $project_id = get_the_ID();
            $project_thumb =  get_the_post_thumbnail($project_id, 'project-vertical' );
            $project_type = get_field('project_type', $project_id);
            $project_des = stCutText(get_field('project_description', $project_id));
            //output html
            $html .= '<div class="single-project-card single-project-card-container">';
            $html .= '<a href="' . $link . '">' . $project_thumb . '</a>';
            $html .= '<span>' . $project_type . '</span>';
            $html .= '<a href="' . $link . '"><h5>' . $title . '</h5></a>';
            $html .= '<p>' . $project_des . '</p>';
            $html .= '</div>';
        }
        wp_reset_postdata();
    }

    // Add load more button if enabled and there are more posts
    if ($load_more) {
        // Get total count for this query
        $total_args = $args;
        $total_args['posts_per_page'] = -1; // Get all posts for count
        $total_query = new WP_Query($total_args);
        $total_posts = $total_query->found_posts;
        wp_reset_postdata();

        $current_count = $offset + $query->post_count;
        if ($current_count < $total_posts) {
            $next_offset = $current_count;
            $term_id_param = '';
            if (is_tax('product_category')) {
                $term = get_queried_object();
                $term_id_param = $term->term_id;
            } elseif ($term_ids !== null && !empty($term_ids)) {
                $term_id_param = is_array($term_ids) ? implode(',', $term_ids) : $term_ids;
            }

            $html .= '<div class="load-more-container" style="text-align: center; margin: 20px 0;">';
            $html .= '<button class="load-more-filtered-btn btn st-link-button small-style" data-offset="' . $next_offset . '" data-limit="' . $limit . '" data-term-ids="' . $term_id_param . '" data-total="' . $total_posts . '">Load More Collections</button>';
            $html .= '</div>';
        }
    }

    if($offset == 0){
        $html = '<div class="' . esc_attr($container_class) . '">' . $html . '</div>';
    }
    
    return $html;
}

// Function to get total collections count
function get_total_collections() {
    $term = get_queried_object();
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_category',
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ),
        ),
    );
    $query = new WP_Query($args);
    return $query->found_posts;
}

// Function to generate collections XML
function generate_collections_xml() {
    $upload_dir = wp_upload_dir();
    $xml_file = $upload_dir['basedir'] . '/collections.xml';

    $dom = new DOMDocument('1.0', 'UTF-8');
    $root = $dom->createElement('collections');
    $dom->appendChild($root);

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $collection_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();

            $collection = $dom->createElement('collection');
            $collection->setAttribute('id', $collection_id);
            $root->appendChild($collection);

            $title_elem = $dom->createElement('title', htmlspecialchars($title));
            $collection->appendChild($title_elem);

            $link_elem = $dom->createElement('permalink', $link);
            $collection->appendChild($link_elem);

            // Categories
            $terms = get_the_terms($collection_id, 'product_category');
            if ($terms && !is_wp_error($terms)) {
                $categories = $dom->createElement('categories');
                foreach ($terms as $term) {
                    $cat = $dom->createElement('category', htmlspecialchars($term->name));
                    $cat->setAttribute('id', $term->term_id);
                    $categories->appendChild($cat);
                }
                $collection->appendChild($categories);
            }

            if ( is_wp_error( $terms ) ) {
                error_log( 'WP Error Message: ' . $terms->get_error_message() ); // Tells you if taxonomy is invalid
            } elseif ( empty( $terms ) ) {
                error_log( 'Terms are empty/false. The post exists but has no terms assigned in this taxonomy.' );
            } else {
                error_log( 'Terms found: ' . print_r( $terms, true ) );
            }

            // Images
            $images = $dom->createElement('images');
            $collection->appendChild($images);

            // Collection thumbs
            $medium_url = get_the_post_thumbnail_url($collection_id, 'medium');
            $thumb_url = get_the_post_thumbnail_url($collection_id, 'thumbnail');
            if ($medium_url) {
                $img = $dom->createElement('image');
                $img->setAttribute('type', 'collection_medium');
                $img->setAttribute('url', $medium_url);
                $images->appendChild($img);
            }
            if ($thumb_url) {
                $img = $dom->createElement('image');
                $img->setAttribute('type', 'collection_thumb');
                $img->setAttribute('url', $thumb_url);
                $images->appendChild($img);
            }

            // Tiles
            $collection_tiles = get_field('tiles_in_collection', $collection_id);
            if ($collection_tiles) {
                foreach ($collection_tiles as $tile_id) {
                    $tile_medium = get_the_post_thumbnail_url($tile_id, 'medium');
                    $tile_thumb = get_the_post_thumbnail_url($tile_id, 'thumbnail');
                    if ($tile_medium) {
                        $img = $dom->createElement('image');
                        $img->setAttribute('type', 'tile_medium');
                        $img->setAttribute('url', $tile_medium);
                        $images->appendChild($img);
                    }
                    if ($tile_thumb) {
                        $img = $dom->createElement('image');
                        $img->setAttribute('type', 'tile_thumb');
                        $img->setAttribute('url', $tile_thumb);
                        $images->appendChild($img);
                    }
                }
            }
        }
        wp_reset_postdata();
    }

    $dom->save($xml_file);
}


// Schedule daily XML generation at 3:00 AM
if (!wp_next_scheduled('generate_collections_xml')) {
    wp_schedule_event(strtotime('03:00:00'), 'daily', 'generate_collections_xml');
}
add_action('generate_collections_xml', 'generate_collections_xml');

//generate tag cloud
function display_terms_hierarchically( $terms, $parent_id = 0, $level = 0 ) {
    foreach ( $terms as $term ) {
        if ( $term->parent == $parent_id ) {
            $has_children = false;
            foreach ( $terms as $child_term ) {
                if ( $child_term->parent == $term->term_id ) {
                    $has_children = true;
                    break;
                }
            }
            $classes = 'category-item';
            if ( $level > 0 ) {
                $classes .= ' child-category';
                $collection_category_ids[] = $term->term_id;
            }
            if ( $level == 1 && $has_children ) {
                // Output accordion
                echo '<div class="accordion" id="collection-category-fliter-accordion">';
                echo '<div class="accordion-item">';

                echo '<span class="accordion-header">';
                echo '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-'. $term->term_id .'" aria-expanded="true" aria-controls="collapse-'. $term->term_id .'">' .str_repeat( '', $level ) . esc_html( $term->name ) . '</button>';
                echo '</span>';

                echo '<div id="collapse-'. $term->term_id .'"" class="accordion-collapse collapse" data-bs-parent="#collection-category-fliter-accordion">';
                echo '<div class="accordion-body">';
                display_terms_hierarchically( $terms, $term->term_id, $level + 1 );
                echo '</div>';
                echo '</div>';

                echo '</div>';
                echo '</div>';
            } else {
                echo '<span class="' . $classes . '" data-term-id="' . $term->term_id . '" data-level="' . $level . '" data-term-name="' . esc_html( $term->name ) . '">' . str_repeat( '&nbsp;&nbsp;', $level ) . esc_html( $term->name ) . '</span><br>';
                display_terms_hierarchically( $terms, $term->term_id, $level + 1 );
            }
        }
    }
}

//functions to sync related tiles of project(ACF object field) AND related projects(custom post meta) of tile 
// Global variable to store old related_tile values
global $old_related_tiles;
$old_related_tiles = array();

// Hook to store old related_tile values before ACF saves
add_action('acf/pre_save_post', 'store_old_related_tiles', 10, 1);
function store_old_related_tiles($post_id) {
    if (get_post_type($post_id) !== 'project') {
        return;
    }
    global $old_related_tiles;
    $old_related_tiles[$post_id] = get_field('related_tile', $post_id, false); // false to get raw IDs
}

// Hook to update related_project meta on tiles after ACF saves
add_action('acf/save_post', 'update_tile_related_projects', 20, 1); // priority 20 to run after ACF saves
function update_tile_related_projects($post_id) {
    if (get_post_type($post_id) !== 'project') {
        return;
    }
    global $old_related_tiles;

    $new_related_tiles = get_field('related_tile', $post_id, false); // new values
    $old_related_tiles_list = isset($old_related_tiles[$post_id]) ? $old_related_tiles[$post_id] : array();

    // Ensure they are arrays
    if (!is_array($new_related_tiles)) $new_related_tiles = array();
    if (!is_array($old_related_tiles_list)) $old_related_tiles_list = array();

    // Find added and removed tiles
    $added_tiles = array_diff($new_related_tiles, $old_related_tiles_list);
    $removed_tiles = array_diff($old_related_tiles_list, $new_related_tiles);

    // Add project to added tiles
    foreach ($added_tiles as $tile_id) {
        $current_projects = get_post_meta($tile_id, 'related_project', true);
        if (!is_array($current_projects)) $current_projects = array();
        if (!in_array($post_id, $current_projects)) {
            $current_projects[] = $post_id;
            update_post_meta($tile_id, 'related_project', $current_projects);
        }
    }

    // Remove project from removed tiles
    foreach ($removed_tiles as $tile_id) {
        $current_projects = get_post_meta($tile_id, 'related_project', true);
        if (!is_array($current_projects)) $current_projects = array();
        if (($key = array_search($post_id, $current_projects)) !== false) {
            unset($current_projects[$key]);
            update_post_meta($tile_id, 'related_project', array_values($current_projects)); // reindex
        }
    }

    // Clean up global
    unset($old_related_tiles[$post_id]);
}
