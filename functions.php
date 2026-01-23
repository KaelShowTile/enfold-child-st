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
        ['name' => 'Himalayas Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03212', 'size' => '300x300'],
        ['name' => 'Himalayas Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03212', 'size' => '300x600'],
        ['name' => 'Himalayas Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03212', 'size' => '600x600'],
        ['name' => 'Himalayas Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03212', 'size' => '600x1200'],
        ['name' => 'Himalayas Salt', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03124', 'size' => '600x600x20'],
        ['name' => 'Himalayas Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03213', 'size' => '300x300'],
        ['name' => 'Himalayas Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03213', 'size' => '300x600'],
        ['name' => 'Himalayas Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03213', 'size' => '600x600'],
        ['name' => 'Himalayas Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03213', 'size' => '600x1200'],
        ['name' => 'Himalayas Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03125', 'size' => '600x600x20'],
        ['name' => 'Himalayas Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03214', 'size' => '300x300'],
        ['name' => 'Himalayas Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03214', 'size' => '300x600'],
        ['name' => 'Himalayas Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03214', 'size' => '600x600'],
        ['name' => 'Himalayas Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03214', 'size' => '600x1200'],
        ['name' => 'Himalayas Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03126', 'size' => '600x600x20'],
        ['name' => 'Himalayas Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03215', 'size' => '300x300'],
        ['name' => 'Himalayas Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03215', 'size' => '300x600'],
        ['name' => 'Himalayas Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03215', 'size' => '600x600'],
        ['name' => 'Himalayas Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03215', 'size' => '600x1200'],
        ['name' => 'Himalayas Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03127', 'size' => '600x600x20'],
        ['name' => 'Himalayas Salt Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SW03604', 'size' => '75x300'],
        ['name' => 'Himalayas Cinder Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SW03605', 'size' => '75x300'],
        ['name' => 'Himalayas Sand Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SW03606', 'size' => '75x300'],
        ['name' => 'Himalayas Coal Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SW03607', 'size' => '75x300'],
        ['name' => 'Living Timber Natural', 'indent' => '', 'design' => 'Timber Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TL01887', 'size' => '200x1200'],
        ['name' => 'Living Timber Brown', 'indent' => '', 'design' => 'Timber Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TL01888', 'size' => '200x1200'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03163', 'size' => '300x300'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03163', 'size' => '300x600'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03163', 'size' => '600x600'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03163', 'size' => '600x1200'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03163', 'size' => '900x900'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03163', 'size' => '1200x1200'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03167', 'size' => '300x600'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03167', 'size' => '600x600'],
        ['name' => 'Maestro Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03167', 'size' => '600x1200'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03164', 'size' => '300x300'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03164', 'size' => '300x600'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03164', 'size' => '600x600'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03164', 'size' => '600x1200'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03164', 'size' => '900x900'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03164', 'size' => '1200x1200'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03124', 'size' => '300x600'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03124', 'size' => '600x600'],
        ['name' => 'Maestro Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03124', 'size' => '600x1200'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03165', 'size' => '300x300'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03165', 'size' => '300x600'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03165', 'size' => '600x600'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03165', 'size' => '600x1200'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03165', 'size' => '900x900'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03165', 'size' => '1200x1200'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03125', 'size' => '300x600'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03125', 'size' => '600x600'],
        ['name' => 'Maestro Cinder', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03125', 'size' => '600x1200'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03166', 'size' => '300x300'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03166', 'size' => '300x600'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03166', 'size' => '600x600'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL03166', 'size' => '600x1200'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03166', 'size' => '900x900'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03170', 'size' => '300x600'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03170', 'size' => '600x600'],
        ['name' => 'Maestro Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'SL03170', 'size' => '600x1200'],
        ['name' => 'Maestro Sand Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03055', 'size' => '600x600x20'],
        ['name' => 'Maestro Sand Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'OD03091', 'size' => 'Pencil Edge 400x600x20'],
        ['name' => 'Maestro Moon Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03056', 'size' => '600x600x20'],
        ['name' => 'Maestro Moon Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'OD03090', 'size' => 'Pencil Edge 400x600x20'],
        ['name' => 'Maestro Cinder Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03057', 'size' => '600x600x20'],
        ['name' => 'Maestro Cinder Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'OD03092', 'size' => 'Pencil Edge 400x600x20'],
        ['name' => 'Maestro Coal Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03058', 'size' => '600x600x20'],
        ['name' => 'Maestro Moon Crazy Pave', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'OD03086', 'size' => 'Random body'],
        ['name' => 'Maestro Cinder Crazy Pave', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'OD03087', 'size' => 'Random body'],
        ['name' => 'Maestro Sand Crazy Pave', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'OD03088', 'size' => 'Random body'],
        ['name' => 'Maestro Coal Crazy Pave', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'OD03089', 'size' => 'Random body'],
        ['name' => 'Maestro Sand Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW03361', 'size' => '75x300'],
        ['name' => 'Maestro Moon Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW03362', 'size' => '75x300'],
        ['name' => 'Maestro Cinder Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW03363', 'size' => '75x300'],
        ['name' => 'Maestro Coal Subway', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW03364', 'size' => '75x300'],
        ['name' => 'Maestro Sand Chevron', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03428', 'size' => 'Sheet 73x260'],
        ['name' => 'Maestro Moon Chevron', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03429', 'size' => 'Sheet 73x260'],
        ['name' => 'Maestro Cinder Chevron', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03430', 'size' => 'Sheet 73x260'],
        ['name' => 'Maestro Coal Chevron', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03431', 'size' => 'Sheet 73x260'],
        ['name' => 'Maestro Sand Hexagon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03432', 'size' => 'Sheet 265x305'],
        ['name' => 'Maestro Moon Hexagon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03433', 'size' => 'Sheet 265x305'],
        ['name' => 'Maestro Cinder Hexagon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03434', 'size' => 'Sheet 265x305'],
        ['name' => 'Maestro Coal Hexagon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS03435', 'size' => 'Sheet 265x305'],
        ['name' => 'Tundra Grigio Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP01078', 'size' => '600x600x20'],
        ['name' => 'Tundra Grigio Bullnose', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP01079', 'size' => 'Bullnose 400x600x20'],
        ['name' => 'Tundra Grigio', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01341', 'size' => '300x600'],
        ['name' => 'Tundra Grigio', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01341', 'size' => '600x600'],
        ['name' => 'Tundra Grigio', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01341', 'size' => '600x1200'],
        ['name' => 'Twilight Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03499', 'size' => '300x600'],
        ['name' => 'Twilight Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03499', 'size' => '600x600'],
        ['name' => 'Twilight Coal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03150', 'size' => '600x600'],
        ['name' => 'Twilight Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03500', 'size' => '300x300'],
        ['name' => 'Twilight Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03500', 'size' => '300x600'],
        ['name' => 'Twilight Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03500', 'size' => '600x600'],
        ['name' => 'Twilight Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03500', 'size' => '600x1200'],
        ['name' => 'Twilight Ivory', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03151', 'size' => '600x600'],
        ['name' => 'Twilight Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03501', 'size' => '300x300'],
        ['name' => 'Twilight Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03501', 'size' => '300x600'],
        ['name' => 'Twilight Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03501', 'size' => '600x600'],
        ['name' => 'Twilight Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03501', 'size' => '600x1200'],
        ['name' => 'Twilight Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03152', 'size' => '600x600'],
        ['name' => 'Twilight White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03502', 'size' => '300x300'],
        ['name' => 'Twilight White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03502', 'size' => '300x600'],
        ['name' => 'Twilight White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03502', 'size' => '600x600'],
        ['name' => 'Twilight White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03502', 'size' => '600x1200'],
        ['name' => 'Twilight White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03153', 'size' => '600x600'],
        ['name' => 'Twilight Ivory 3D Line', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL03503', 'size' => '3D Line 300x600'],
        ['name' => 'Twilight Silver 3D Line', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL03504', 'size' => '3D Line 300x600'],
        ['name' => 'Twilight White 3D Line', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL03505', 'size' => '3D Line 300x600'],
        ['name' => 'Twilight Ivory Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03113', 'size' => '400x600x20'],
        ['name' => 'Twilight Silver Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03114', 'size' => '400x600x20'],
        ['name' => 'Twilight White Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03115', 'size' => '400x600x20'],
        ['name' => 'Twit Fusion Cream', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07315', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Gialla', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07316', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Bone', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07317', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Pink', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07318', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07319', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Light Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07320', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Mid Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07321', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Dark Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07322', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Aqua', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07323', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Fusion Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07324', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07415', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07416', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Pink', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07417', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07418', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07419', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Seafoam', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07420', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Moss', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07421', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07422', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07424', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Twit Kit Kat Cobalt', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07508', 'size' => 'Chip 15x145 - Sheet 296x296'],
        ['name' => 'Unglazed 23mm Off White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05502', 'size' => 'Chip 23x23 - Sheet 300x300'],
        ['name' => 'Unglazed 23mm Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05503', 'size' => 'Chip 23x23 - Sheet 300x300'],
        ['name' => 'Unglazed 48mm Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05492', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Terracotta', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05499', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Light Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05500', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Off White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05501', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Cornflower Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05506', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Pistachio Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05507', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Speckled Antique White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05577', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Speckled Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05578', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Speckled Taupe', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05579', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 48mm Speckled Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05580', 'size' => 'Chip 48x48 - Sheet 306x306'],
        ['name' => 'Unglazed 97mm Off White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05504', 'size' => 'Chip 97x97 - Sheet 300x300'],
        ['name' => 'Unglazed 97mm Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05505', 'size' => 'Chip 97x97 - Sheet 300x300'],
        ['name' => 'Unglazed 97mm Charcoal', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05584', 'size' => 'Chip 97x97 - Sheet 300x300'],
        ['name' => 'Unglazed 97mm Cement', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05585', 'size' => 'Chip 97x97 - Sheet 300x300'],
        ['name' => 'Unglazed 97mm Chestnut Red', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05508', 'size' => 'Chip 97x97 - Sheet 300x300'],
        ['name' => 'Unglazed Kit Kat Concave Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05583', 'size' => 'Chip 20x145 - Sheet 296x299'],
        ['name' => 'Unglazed Kit Kat Concave Chestnut Red', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05582', 'size' => 'Chip 20x145 - Sheet 296x299'],
        ['name' => 'Unglazed Kit Kat Concave White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05581', 'size' => 'Chip 20x145 - Sheet 296x299'],
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
