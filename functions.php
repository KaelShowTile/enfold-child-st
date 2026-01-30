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
/*
add_action('init', function() {
    if (!isset($_GET['run_tile_multi_import'])) return;

    $tile_rows = [  
        ['name' => 'Hexagon Carrara + Nero Border', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08455', 'size' => 'Sheet 275x315 - Frame 311x388'],
        ['name' => 'Hexagon Carrara 48', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08491', 'size' => 'Chip 48x48 - Sheet 298x305'],
        ['name' => 'Hexagon Carrara 48', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08492', 'size' => 'Chip 48x48 - Sheet 298x305'],
        ['name' => 'Hexagon Carrara 70', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08493', 'size' => 'Chip 70x80 - Sheet 250x289'],
        ['name' => 'Hexagon Carrara 70', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08494', 'size' => 'Chip 70x80 - Sheet 250x289'],
        ['name' => 'Hexagon Carrara 73', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08495', 'size' => 'Chip 73x80 - Sheet 256x298'],
        ['name' => 'Hexagon Carrara 90', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08496', 'size' => 'Chip 90x10 - Sheet 275x316'],
        ['name' => 'Hexagon Carrara 98', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08497', 'size' => 'Chip 98mm - Sheet 258x298'],
        ['name' => 'Dante Earl Grey Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03051', 'size' => '600x600x20'],
        ['name' => 'Dante Nutmeg Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03052', 'size' => '600x600x20'],
        ['name' => 'Dante Pepper Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03053', 'size' => '600x600x20'],
        ['name' => 'Dante Salt Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03054', 'size' => '600x600x20'],
        ['name' => 'Dante Glacier Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03055', 'size' => '600x600x20'],
        ['name' => 'Dante Earl Grey Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03080', 'size' => 'Pencil Edge 398x600'],
        ['name' => 'Dante Nutmeg Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03081', 'size' => 'Pencil Edge 398x600'],
        ['name' => 'Dante Salt Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03082', 'size' => 'Pencil Edge 398x600'],
        ['name' => 'Dante Pepper Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03095', 'size' => 'Pencil Edge 398x600'],
        ['name' => 'Dante Glacier Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03096', 'size' => 'Pencil Edge 398x600'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03246', 'size' => '300x300'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03246', 'size' => '300x600'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03246', 'size' => '600x600'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03246', 'size' => '600x1200'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03246', 'size' => '900x900'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03250', 'size' => '300x600'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03250', 'size' => '600x600'],
        ['name' => 'Dante Earl Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03250', 'size' => '600x1200'],
        ['name' => 'Dante Earl Grey Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SW03410', 'size' => '100x200'],
        ['name' => 'Dante Earl Grey Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03468', 'size' => 'Chip 48x98 - Sheet 300x300'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03247', 'size' => '300x300'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03247', 'size' => '300x600'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03247', 'size' => '600x600'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03247', 'size' => '600x1200'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03247', 'size' => '1200x1200'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03251', 'size' => '300x600'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03251', 'size' => '600x600'],
        ['name' => 'Dante Nutmeg', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03251', 'size' => '600x1200'],
        ['name' => 'Dante Nutmeg Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SW03411', 'size' => '100x200'],
        ['name' => 'Dante Nutmeg Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03469', 'size' => 'Chip 48x98 - Sheet 300x300'],
        ['name' => 'Dante Pepper', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03248', 'size' => '300x300'],
        ['name' => 'Dante Pepper', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03248', 'size' => '300x600'],
        ['name' => 'Dante Pepper', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03248', 'size' => '600x600'],
        ['name' => 'Dante Pepper', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03248', 'size' => '600x1200'],
        ['name' => 'Dante Pepper', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03252', 'size' => '300x600'],
        ['name' => 'Dante Pepper', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03252', 'size' => '600x600'],
        ['name' => 'Dante Pepper', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03252', 'size' => '600x1200'],
        ['name' => 'Dante Pepper Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SW03412', 'size' => '100x200'],
        ['name' => 'Dante Pepper Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03470', 'size' => 'Chip 48x98 - Sheet 300x300'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03249', 'size' => '300x300'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03249', 'size' => '300x600'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03249', 'size' => '600x600'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03249', 'size' => '600x1200'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03249', 'size' => '900x900'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03253', 'size' => '300x600'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03253', 'size' => '600x600'],
        ['name' => 'Dante Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03253', 'size' => '600x1200'],
        ['name' => 'Dante Salt Subway In&Out', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SW03413', 'size' => '100x200'],
        ['name' => 'Dante Salt Mosaic In&Out', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03471', 'size' => 'Chip 48x98 - Sheet 300x300'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03305', 'size' => '300x300'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03305', 'size' => '300x600'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03305', 'size' => '600x600'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03305', 'size' => '600x1200'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03305', 'size' => '900x900'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03305', 'size' => '1200x1200'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03307', 'size' => '300x600'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03307', 'size' => '600x600'],
        ['name' => 'Dante Glacier', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03307', 'size' => '600x1200'],
        ['name' => 'Dante Glacier Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SW03442', 'size' => '100x200'],
        ['name' => 'Dante Glacier Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03475', 'size' => 'Chip 48x98 - Sheet 300x300'],
        ['name' => 'Floral Rossi Border', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07446', 'size' => 'Sheet 300x300'],
        ['name' => 'Floral Rossi Hexagon', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07447', 'size' => 'Chip 23x23 - Sheet 319x322'],
        ['name' => 'Floral Rossi Penny Round', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07448', 'size' => 'Chip 30mm'],
        ['name' => 'Floral Rossi Herringbone', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07449', 'size' => 'Sheet 305x430'],
        ['name' => 'Floral Rossi Subway', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07450', 'size' => '75x305'],
        ['name' => 'Floral Rossi Large Hexagon', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07451', 'size' => '200x230'],
        ['name' => 'Floral Rossi Long hexagon', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07452', 'size' => 'Chip 49x149 - Sheet 278x310'],
        ['name' => 'Floral Rossi Split Fiorano', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07453', 'size' => 'Sheet 295x297'],
        ['name' => 'Floral Rossi Stack Bond', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07454', 'size' => 'Chip 10x90 - Sheet 280x293'],
        ['name' => 'New Valley Bianco Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP01101', 'size' => 'Bullnose 400x600x20'],
        ['name' => 'New Valley Cotton Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP01102', 'size' => 'Bullnose 400x600x20'],
        ['name' => 'New Valley Gris Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP01103', 'size' => 'Bullnose 400x600x20'],
        ['name' => 'New valley Bianco Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP01104', 'size' => '600x600x20'],
        ['name' => 'New valley Cotton Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP01105', 'size' => '600x600x20'],
        ['name' => 'New valley Gris Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP01106', 'size' => '600x600x20'],
        ['name' => 'New Valley Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03123', 'size' => '600x600'],
        ['name' => 'New Valley Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03124', 'size' => '600x600'],
        ['name' => 'New Valley Gris', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03125', 'size' => '600x600'],
        ['name' => 'New Valley Bianco Cobblestone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'NS01083', 'size' => 'Chip 98x98 - Offset 300x300'],
        ['name' => 'New Valley Cotton Cobblestone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'NS01084', 'size' => 'Chip 98x98 - Offset 300x300'],
        ['name' => 'New Valley Gris Cobblestone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'NS01085', 'size' => 'Chip 98x98 - Offset 300x300'],
        ['name' => 'New Valley Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03407', 'size' => '300x600'],
        ['name' => 'New Valley Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03407', 'size' => '600x600'],
        ['name' => 'New Valley Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03408', 'size' => '300x600'],
        ['name' => 'New Valley Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03407', 'size' => '600x600'],
        ['name' => 'New Valley Gris', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03409', 'size' => '300x600'],
        ['name' => 'New Valley Gris', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03407', 'size' => '600x600'],
        ['name' => 'Soft Carrara', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01436', 'size' => '300x600'],
        ['name' => 'Soft Carrara', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01436', 'size' => '600x600'],
        ['name' => 'Soft Carrara', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL01439', 'size' => '300x600'],
        ['name' => 'Soft Carrara', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL01439', 'size' => '600x600'],
        ['name' => 'Prestige Charcoal', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04169', 'size' => '150x150'],
        ['name' => 'Prestige Charcoal', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04169', 'size' => '300x300'],
        ['name' => 'Prestige Charcoal', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04169', 'size' => '300x600'],
        ['name' => 'Prestige Charcoal', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04169', 'size' => '600x600'],
        ['name' => 'Prestige Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04170', 'size' => '150x150'],
        ['name' => 'Prestige Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04170', 'size' => '300x300'],
        ['name' => 'Prestige Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04170', 'size' => '300x600'],
        ['name' => 'Prestige Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04170', 'size' => '600x600'],
        ['name' => 'Prestige White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04171', 'size' => '150x150'],
        ['name' => 'Prestige White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04171', 'size' => '300x300'],
        ['name' => 'Prestige White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04171', 'size' => '300x600'],
        ['name' => 'Prestige White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04171', 'size' => '600x600'],
        ['name' => 'System Anthracite', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01093', 'size' => '200x200'],
        ['name' => 'System Anthracite', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01093', 'size' => '300x300'],
        ['name' => 'System Anthracite Diamond', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01094', 'size' => '200x200'],
        ['name' => 'System Anthracite Corner Internal', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01095', 'size' => 'Internal'],
        ['name' => 'System Anthracite Corner External', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01096', 'size' => 'External'],
        ['name' => 'System Anthracite Cove', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01097', 'size' => '100X200'],
        ['name' => 'System Anthracite Cove', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01097', 'size' => '100x300'],
        ['name' => 'System Ivory Tac Tile 300X300', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01078', 'size' => '300x300'],
        ['name' => 'System Grey Tac Tile 300X300', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01079', 'size' => '300x300'],
        ['name' => 'System Anthracite Tac Tile 300X300', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01098', 'size' => '300x300'],
        ['name' => 'System Grey Corner External', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01100', 'size' => 'External'],
        ['name' => 'System Grey Corner Internal', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01101', 'size' => 'Internal'],
        ['name' => 'System Grey Cove', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01102', 'size' => '100x300'],
        ['name' => 'System Grey', 'indent' => '', 'design' => 'Commercial Tile', 'material' => 'Porcelain Tile | Full Bodied', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'grip', 'code' => 'OD01103', 'size' => '300x300'],
        ['name' => 'Limestone Tumbled Soft Crema', 'indent' => '', 'design' => 'Limestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24051', 'size' => '406x610x12'],
        ['name' => 'Limestone Tumbled Soft Crema', 'indent' => '', 'design' => 'Limestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24052', 'size' => '406x610x30'],
        ['name' => 'Limestone Tumbled Earth', 'indent' => '', 'design' => 'Limestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24061', 'size' => '406x610x12'],
        ['name' => 'Limestone Tumbled Earth', 'indent' => '', 'design' => 'Limestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24062', 'size' => '406x610x30'],
        ['name' => 'Tuscany Cotto Burgundy Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21109', 'size' => '60x240'],
        ['name' => 'Tuscany Cotto Grey Taupe', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21110', 'size' => '60x240'],
        ['name' => 'Tuscany Cotto Burgundy Red', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21111', 'size' => '150x150'],
        ['name' => 'Tuscany Cotto Burgundy Red', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21111', 'size' => '300x300'],
        ['name' => 'Tuscany Cotto Grey Taupe', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21112', 'size' => '150x150'],
        ['name' => 'Tuscany Cotto Grey Taupe', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21112', 'size' => '300x300'],
        ['name' => 'Tuscany Cotto Sand', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21113', 'size' => '60x240'],
        ['name' => 'Tuscany Cotto Natural', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21114', 'size' => '60x240'],
        ['name' => 'Tuscany Cotto Sand', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21115', 'size' => '150x150'],
        ['name' => 'Tuscany Cotto Sand', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21115', 'size' => '300x300'],
        ['name' => 'Tuscany Cotto Natural', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21116', 'size' => '150x150'],
        ['name' => 'Tuscany Cotto Natural', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21116', 'size' => '300x300'],
        ['name' => 'Royalstone White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'SL01342', 'size' => '750x1500'],
        ['name' => 'Royalstone Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'SL01343', 'size' => '750x1500'],
        ['name' => 'Travertine 4D', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'SL01389', 'size' => '600x1200'],
        ['name' => 'Vogue Stone Pure', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01375', 'size' => '300x600'],
        ['name' => 'Vogue Stone Pure', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01375', 'size' => '600x600'],
        ['name' => 'Vogue Stone Nero', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01376', 'size' => '600x600'],
        ['name' => 'Vogue Stone Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01399', 'size' => '300x600'],
        ['name' => 'Vogue Stone Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01399', 'size' => '600x600'],
        ['name' => 'Vogue Stone Natural', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL01400', 'size' => '300x600'],
        ['name' => 'Vogue Stone Natural', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL01400', 'size' => '600x600'],
        ['name' => 'Tundra Grey Honed', 'indent' => '', 'design' => 'Tundra', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS01042', 'size' => '600x1200x18'],
        ['name' => 'Tundra Grey Sandblasted', 'indent' => '', 'design' => 'Tundra', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS01078', 'size' => 'Half-Bullnose 406x610x30'],
        ['name' => 'Tundra Grey Sandblasted', 'indent' => '', 'design' => 'Tundra', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS01040', 'size' => '406x610x12'],
        ['name' => 'Tundra Grey Sandblasted', 'indent' => '', 'design' => 'Tundra', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS01040', 'size' => '406x610x30'],
        ['name' => 'Rhombus White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05081', 'size' => 'Chip 48mm - Sheet 266x305'],
        ['name' => 'Rhombus Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05082', 'size' => 'Chip 48mm - Sheet 266x305'],
        ['name' => 'Santiago Light Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03321', 'size' => '300x300'],
        ['name' => 'Santiago Light Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03321', 'size' => '300x600'],
        ['name' => 'Santiago Light Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03321', 'size' => '600x600'],
        ['name' => 'Santiago Light Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03321', 'size' => '600x1200'],
        ['name' => 'Santiago Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03322', 'size' => '300x300'],
        ['name' => 'Santiago Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03322', 'size' => '300x600'],
        ['name' => 'Santiago Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03322', 'size' => '600x600'],
        ['name' => 'Santiago Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03322', 'size' => '600x1200'],
        ['name' => 'Santiago Light Grey Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03064', 'size' => '400x600x20'],
        ['name' => 'Santiago Grey Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03065', 'size' => '400x600x20'],
        ['name' => 'Santiago Light Grey Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03066', 'size' => 'Pencil Edge 400x600'],
        ['name' => 'Santiago Grey Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03067', 'size' => 'Pencil Edge 400x600'],
        ['name' => 'Charm Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01196', 'size' => '76x152'],
        ['name' => 'Charm Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01197', 'size' => '76x152'],
        ['name' => 'Charm Blush Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01198', 'size' => '76x152'],
        ['name' => 'Charm Blush Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01199', 'size' => '76x152'],
        ['name' => 'Charm Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01200', 'size' => '76x152'],
        ['name' => 'Charm Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01201', 'size' => '76x152'],
        ['name' => 'Charm Sky Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01202', 'size' => '76x152'],
        ['name' => 'Charm Sky Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01203', 'size' => '76x152'],
        ['name' => 'Charm Sea Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01204', 'size' => '76x152'],
        ['name' => 'Charm Sea Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01205', 'size' => '76x152'],
        ['name' => 'Charm Smoke Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01206', 'size' => '76x152'],
        ['name' => 'Charm Smoke Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01207', 'size' => '76x152'],
        ['name' => 'Charm Midnight', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01208', 'size' => '76x152'],
        ['name' => 'Charm Midnight', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01209', 'size' => '76x152'],
        ['name' => 'Charm Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01212', 'size' => '100x100'],
        ['name' => 'Charm Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01213', 'size' => '100x100'],
        ['name' => 'Charm Blush Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01214', 'size' => '100x100'],
        ['name' => 'Charm Blush Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01215', 'size' => '100x100'],
        ['name' => 'Charm Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01216', 'size' => '100x100'],
        ['name' => 'Charm Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01217', 'size' => '100x100'],
        ['name' => 'Charm Sky Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01218', 'size' => '100x100'],
        ['name' => 'Charm Sky Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01219', 'size' => '100x100'],
        ['name' => 'Charm Sea Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01220', 'size' => '100x100'],
        ['name' => 'Charm Sea Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01221', 'size' => '100x100'],
        ['name' => 'Charm Smoke Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01222', 'size' => '100x100'],
        ['name' => 'Charm Smoke Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01223', 'size' => '100x100'],
        ['name' => 'Charm Midnight', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01224', 'size' => '100x100'],
        ['name' => 'Charm Midnight', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01225', 'size' => '100x100'],
        ['name' => 'Milson Bianco Alta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07339', 'size' => '75x300'],
        ['name' => 'Milson Blue Alta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07340', 'size' => '75x300'],
        ['name' => 'Milson Nero Alta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07341', 'size' => '75x300'],
        ['name' => 'Milson Porpora Alta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07342', 'size' => '75x300'],
        ['name' => 'Milson Rosa Alta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07343', 'size' => '75x300'],
        ['name' => 'Milson Salvia Alta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07344', 'size' => '75x300'],
        ['name' => 'Milson Bianco Bassa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07345', 'size' => '75x300'],
        ['name' => 'Milson Blue Bassa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07346', 'size' => '75x300'],
        ['name' => 'Milson Nero Bassa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07347', 'size' => '75x300'],
        ['name' => 'Milson Porpora Bassa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07348', 'size' => '75x300'],
        ['name' => 'Milson Rosa Bassa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07349', 'size' => '75x300'],
        ['name' => 'Milson Salvia Bassa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07350', 'size' => '75x300'],
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

*/

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
