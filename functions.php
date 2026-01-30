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
        ['name' => 'Brass Mix Brick Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13362', 'size' => 'Chip 48x145 - Sheet 298x298'],
        ['name' => 'Brass Mix Brick Nero Marquina', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13363', 'size' => 'Chip 48x145 - Sheet 298x298'],
        ['name' => 'Brass Mix Formes Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13358', 'size' => 'Chip 73x84 - Sheet 298x256'],
        ['name' => 'Brass Mix Formes Nero Marquina', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13359', 'size' => 'Chip 73x84 - Sheet 298x256'],
        ['name' => 'Brass Mix Herringbone Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13356', 'size' => 'Chip 25x75 - Sheet 320x280'],
        ['name' => 'Brass Mix Herringbone Nero Marquina', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13357', 'size' => 'Chip 25x75 - Sheet 320x280'],
        ['name' => 'Brass Mix Hexagon Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13360', 'size' => 'Chip 55x95.2 - Sheet 345x298'],
        ['name' => 'Brass Mix Hexagon Nero Marquina', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13361', 'size' => 'Chip 55x95.2 - Sheet 345x298'],
        ['name' => 'Brass Mix Pyramid Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13354', 'size' => 'Chip 71x70 - Sheet 327x292'],
        ['name' => 'Brass Mix Pyramid Nero Marquina', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS13355', 'size' => 'Chip 71x70 - Sheet 327x292'],
        ['name' => 'Dorothy Blanco', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16107', 'size' => '200x200'],
        ['name' => 'Dorothy Corfu', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16104', 'size' => '200x200'],
        ['name' => 'Dorothy Creta', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16103', 'size' => '200x200'],
        ['name' => 'Dorothy Milos', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16105', 'size' => '200x200'],
        ['name' => 'Dorothy Mustard', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16108', 'size' => '200x200'],
        ['name' => 'Dorothy Mykonos', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16102', 'size' => '200x200'],
        ['name' => 'Dorothy Naxos', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16106', 'size' => '200x200'],
        ['name' => 'Marvel Diva Galaxy', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03441', 'size' => '600x1200'],
        ['name' => 'Marvel Gala Amazzonite', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03244', 'size' => '600x1200'],
        ['name' => 'Marvel Gala Amazzonite', 'indent' => 'yes', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02152', 'size' => '1200x2780'],
        ['name' => 'Marvel Gala Calacatta Black', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03243', 'size' => '600x1200'],
        ['name' => 'Marvel Gala Calacatta Black', 'indent' => 'yes', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02151', 'size' => '1200x2780'],
        ['name' => 'Marvel Gala Crystal White', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03240', 'size' => '600x1200'],
        ['name' => 'Marvel Gala Crystal White', 'indent' => 'yes', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02148', 'size' => '1200x2780'],
        ['name' => 'Marvel Gala Desert Soul', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03241', 'size' => '600x1200'],
        ['name' => 'Marvel Gala Desert Soul', 'indent' => 'yes', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02149', 'size' => '1200x2780'],
        ['name' => 'Marvel Gala Exotic Green', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03242', 'size' => '600x1200'],
        ['name' => 'Marvel Gala Exotic Green', 'indent' => 'yes', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02150', 'size' => '1200x2780'],
        ['name' => 'Marvel Onyx Alabster', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02356', 'size' => '600x1200'],
        ['name' => 'Marvel Onyx Alabster', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02190', 'size' => '1200x2780'],
        ['name' => 'Marvel Onyx Noir', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02354', 'size' => '600x1200'],
        ['name' => 'Marvel Onyx Noir', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02191', 'size' => '1200x2780'],
        ['name' => 'Marvel Onyx Pearl', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02355', 'size' => '600x1200'],
        ['name' => 'Marvel Onyx Pearl', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02192', 'size' => '1200x2780'],
        ['name' => 'Marvel Onyx White', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02353', 'size' => '600x1200'],
        ['name' => 'Marvel Onyx White', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02193', 'size' => '1200x2780'],
        ['name' => 'Marvel Shine Calacatta Delicato', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL02149', 'size' => '600x600'],
        ['name' => 'Marvel Shine Calacatta Delicato', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL02150', 'size' => '600x1200'],
        ['name' => 'Marvel Shine Calacatta Delicato', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02151', 'size' => '750x1500'],
        ['name' => 'Marvel Shine Calacatta Delicato', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02055', 'size' => '1200x2780'],
        ['name' => 'Marvel Shine Calacatta Imperiale', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL02152', 'size' => '600x600'],
        ['name' => 'Marvel Shine Calacatta Imperiale', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL02153', 'size' => '600x1200'],
        ['name' => 'Marvel Shine Calacatta Imperiale', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02154', 'size' => '750x1500'],
        ['name' => 'Marvel Shine Calacatta Imprerial', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS02072', 'size' => '1200x2780'],
        ['name' => 'Marvel Shine Calacatta Prestigio', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL02156', 'size' => '300x600'],
        ['name' => 'Marvel Shine Calacatta Prestigio', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL02156', 'size' => '600x600'],
        ['name' => 'Marvel Shine Calacatta Prestigio', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'PS02073', 'size' => '1200x2780'],
        ['name' => 'Marvel Shine Calacatta Prestigio', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL02157', 'size' => '600x1200'],
        ['name' => 'Marvel Shine Calacatta Prestigio', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02158', 'size' => '750x1500'],
        ['name' => 'Marvel Shine Calacatta Prestigio Gold Hex', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'MS07289', 'size' => 'Sheet 251x290'],
        ['name' => 'Marvel Shine Calacatta Prestigio Rose Hex', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'MS07290', 'size' => 'Sheet 251x290'],
        ['name' => 'Marvel Shine Statuario Supremo', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL02159', 'size' => '300x600'],
        ['name' => 'Marvel Shine Statuario Supremo', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL02159', 'size' => '600x600'],
        ['name' => 'Marvel Shine Statuario Supremo', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'PS02071', 'size' => '1200x2780'],
        ['name' => 'Marvel Shine Statuario Supremo', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL02161', 'size' => '600x1200'],
        ['name' => 'Marvel Shine Statuario Supremo', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL02162', 'size' => '750x1500'],
        ['name' => 'Marvel Shine Statuario Supremo Platino Hex', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'MS07291', 'size' => 'Sheet 251x290'],
        ['name' => 'Marvel Travertine Pearl Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03277', 'size' => '600x600'],
        ['name' => 'Marvel Travertine Pearl Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03277', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine Pearl Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL03279', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine Pearl Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02589', 'size' => 'Basketweave 350x468'],
        ['name' => 'Marvel Travertine Pearl Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02590', 'size' => 'Cross Chisselled 295x600'],
        ['name' => 'Marvel Travertine Pearl Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02591', 'size' => 'Diamond 600x600'],
        ['name' => 'Marvel Travertine Pearl Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02592', 'size' => 'Origami 280x412'],
        ['name' => 'Marvel Travertine Pearl Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02593', 'size' => 'Tessellation 220x260'],
        ['name' => 'Marvel Travertine Pearl Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03285', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine Pearl Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL03288', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine Sand Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03280', 'size' => '600x600'],
        ['name' => 'Marvel Travertine Sand Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03280', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine Sand Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02594', 'size' => 'Basketweave 350x468'],
        ['name' => 'Marvel Travertine Sand Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02595', 'size' => 'Cross Chisselled 295x600'],
        ['name' => 'Marvel Travertine Sand Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02596', 'size' => 'Diamond 600x600'],
        ['name' => 'Marvel Travertine Sand Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02597', 'size' => 'Origami 280x412'],
        ['name' => 'Marvel Travertine Sand Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02598', 'size' => 'Tessellation 220x260'],
        ['name' => 'Marvel Travertine Sand Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03286', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine Sand Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL03281', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine White Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03282', 'size' => '600x600'],
        ['name' => 'Marvel Travertine White Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03282', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine White Cross-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL03284', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine White Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02599', 'size' => 'Basketweave 350x468'],
        ['name' => 'Marvel Travertine White Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02600', 'size' => 'Cross Chisselled 295x600'],
        ['name' => 'Marvel Travertine White Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02601', 'size' => 'Diamond 600x600'],
        ['name' => 'Marvel Travertine White Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02602', 'size' => 'Origami 280x412'],
        ['name' => 'Marvel Travertine White Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS02603', 'size' => 'Tessellation 220x260'],
        ['name' => 'Marvel Travertine White Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03287', 'size' => '600x1200'],
        ['name' => 'Marvel Travertine White Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL03289', 'size' => '600x1200'],
        ['name' => 'Marvel X Calacatta Apuano', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03268', 'size' => '300x600'],
        ['name' => 'Marvel X Calacatta Apuano', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03268', 'size' => '600x600'],
        ['name' => 'Marvel X Calacatta Apuano', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03268', 'size' => '600x1200'],
        ['name' => 'Marvel X Calacatta Apuano', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03269', 'size' => '750x1500'],
        ['name' => 'Marvel X Calacatta Apuano', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL03270', 'size' => '1200x2780'],
        ['name' => 'Marvel X Calacatta Perla', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03265', 'size' => '300x600'],
        ['name' => 'Marvel X Calacatta Perla', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03265', 'size' => '600x600'],
        ['name' => 'Marvel X Calacatta Perla', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03265', 'size' => '600x1200'],
        ['name' => 'Marvel X Calacatta Perla', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03266', 'size' => '750x1500'],
        ['name' => 'Marvel X Calacatta Perla', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL03267', 'size' => '1200x2780'],
        ['name' => 'Marvel X Fior Di Bosco', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03271', 'size' => '300x600'],
        ['name' => 'Marvel X Fior Di Bosco', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03271', 'size' => '600x600'],
        ['name' => 'Marvel X Fior Di Bosco', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03271', 'size' => '600x1200'],
        ['name' => 'Marvel X Fior Di Bosco', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03272', 'size' => '750x1500'],
        ['name' => 'Marvel X Fior Di Bosco', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL0373', 'size' => '1200x2780'],
        ['name' => 'Marvel X Grey Cloud', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03274', 'size' => '300x600'],
        ['name' => 'Marvel X Grey Cloud', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03274', 'size' => '600x600'],
        ['name' => 'Marvel X Grey Cloud', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03274', 'size' => '600x1200'],
        ['name' => 'Marvel X Grey Cloud', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03275', 'size' => '750x1500'],
        ['name' => 'Marvel X Grey Cloud', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'silk', 'code' => 'SL0376', 'size' => '1200x2780'],
        ['name' => 'XL Marvel Travertine Pearl Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03290', 'size' => '1200x2780'],
        ['name' => 'XL Marvel Travertine Sand Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03291', 'size' => '1200x2780'],
        ['name' => 'XL Marvel Travertine White Vein-Cut', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03292', 'size' => '1200x2780'],
        ['name' => 'Zellige 2.0 Bottle', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19279', 'size' => '50x150'],
        ['name' => 'Zellige 2.0 Bottle', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19285', 'size' => '100x100'],
        ['name' => 'Zellige 2.0 Clay', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19275', 'size' => '50x150'],
        ['name' => 'Zellige 2.0 Clay', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19281', 'size' => '100x100'],
        ['name' => 'Zellige 2.0 Cloud', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19274', 'size' => '50x150'],
        ['name' => 'Zellige 2.0 Cloud', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19280', 'size' => '100x100'],
        ['name' => 'Zellige 2.0 Mist', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19278', 'size' => '50x150'],
        ['name' => 'Zellige 2.0 Mist', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19284', 'size' => '100x100'],
        ['name' => 'Zellige 2.0 Sand', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19276', 'size' => '50x150'],
        ['name' => 'Zellige 2.0 Sand', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19282', 'size' => '100x100'],
        ['name' => 'Zellige 2.0 Terra', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW19277', 'size' => '50x150'],
        ['name' => 'Zellige 2.0 Terra', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Handmade Zellige', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW19283', 'size' => '100x100'],
        ['name' => 'Maya Ash Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06579', 'size' => '100x100'],
        ['name' => 'Maya Cement', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06580', 'size' => '100x100'],
        ['name' => 'Maya Cotto', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06581', 'size' => '100x100'],
        ['name' => 'Maya Cotto', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06582', 'size' => '100x100'],
        ['name' => 'Maya Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06583', 'size' => '100x100'],
        ['name' => 'Maya Mattone', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06584', 'size' => '100x100'],
        ['name' => 'Maya Mattone', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06585', 'size' => '100x100'],
        ['name' => 'Maya Mint Gloss', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06586', 'size' => '100x100'],
        ['name' => 'Maya Off White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06587', 'size' => '100x100'],
        ['name' => 'Maya Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06588', 'size' => '100x100'],
        ['name' => 'Maya Taupe', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06589', 'size' => '100x100'],
        ['name' => 'Maya White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06590', 'size' => '100x100'],
        ['name' => 'Maya White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06591', 'size' => '100x100'],
        ['name' => 'Metropolis Bone 3D Flute', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL03460', 'size' => '600x1200'],
        ['name' => 'Metropolis Clay 3D Flute', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL03461', 'size' => '600x1200'],
        ['name' => 'Metropolis Gypsum 3D Flute', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL03462', 'size' => '600x1200'],
        ['name' => 'Metropolis Smoke 3D Flute', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL03463', 'size' => '600x1200'],
        ['name' => 'Metropolis Bone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03464', 'size' => '300x300'],
        ['name' => 'Metropolis Bone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03464', 'size' => '300x600'],
        ['name' => 'Metropolis Bone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03464', 'size' => '600x600'],
        ['name' => 'Metropolis Bone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03464', 'size' => '600x1200'],
        ['name' => 'Metropolis Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03465', 'size' => '300x300'],
        ['name' => 'Metropolis Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03465', 'size' => '300x600'],
        ['name' => 'Metropolis Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03465', 'size' => '600x600'],
        ['name' => 'Metropolis Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03465', 'size' => '600x1200'],
        ['name' => 'Metropolis Gypsum', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03466', 'size' => '300x300'],
        ['name' => 'Metropolis Gypsum', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03466', 'size' => '300x600'],
        ['name' => 'Metropolis Gypsum', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03466', 'size' => '600x600'],
        ['name' => 'Metropolis Gypsum', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03466', 'size' => '600x1200'],
        ['name' => 'Metropolis Smoke', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03467', 'size' => '300x300'],
        ['name' => 'Metropolis Smoke', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03467', 'size' => '300x600'],
        ['name' => 'Metropolis Smoke', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03467', 'size' => '600x600'],
        ['name' => 'Metropolis Smoke', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03467', 'size' => '600x1200'],
        ['name' => 'Metropolis Bone Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03573', 'size' => 'Sheet 298x298'],
        ['name' => 'Metropolis Clay Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03574', 'size' => 'Sheet 298x298'],
        ['name' => 'Metropolis Gypsum Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03575', 'size' => 'Sheet 298x298'],
        ['name' => 'Metropolis Smoke Mosaic', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'MS03576', 'size' => 'Sheet 298x298'],
        ['name' => 'Nero Marquina Chevron & Gold', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08456', 'size' => 'Sheet 300x30xx8'],
        ['name' => 'Nero Marquina Mix Hexagon', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08457', 'size' => 'Sheet 274x317'],
        ['name' => 'Nero Marquina Square & Carrara Border', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08458', 'size' => 'Sheet 305x305x10'],
        ['name' => 'Nero Marquina Border & Carrara Square', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08459', 'size' => 'Sheet 305x305x10'],
        ['name' => 'Nero Marquina & Gold  & Carrara Chevron', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08460', 'size' => 'Sheet 300x302x8'],
        ['name' => 'Nero Marquina & Carrara Modern', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08462', 'size' => 'Sheet 279x279x7'],
        ['name' => 'Nero Marquina & Carrara Palace', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08463', 'size' => 'Sheet 305x305x10'],
        ['name' => 'Carrara Bamboo', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20329', 'size' => '30x300'],
        ['name' => 'Carrara Concave', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20330', 'size' => '30x300'],
        ['name' => 'Carrara Feather', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08461', 'size' => 'Chip 50.8x104.5- Sheet 315x344x7'],
        ['name' => 'Carrara Coastal', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08464', 'size' => 'Chip 104x104 - Frame 243x243 - Sheet 210x210'],
        ['name' => 'Carrara Penny Round', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08479', 'size' => 'Chip 48mm - Sheet 298x305'],
        ['name' => 'Carrara Penny Round', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08480', 'size' => 'Chip 23mm - Sheet 287x305'],
        ['name' => 'Carrara Beveled Subway', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08437', 'size' => 'Chip 75x150 - Sheet 302x306'],
        ['name' => 'Carrara Castle Thassos', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08438', 'size' => 'Sheet 305x305'],
        ['name' => 'Carrara Modern Thassos', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08439', 'size' => 'Sheet 279x279'],
        ['name' => 'Carrara Mini Interlock', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08440', 'size' => 'Sheet 305x310'],
        ['name' => 'Carrara Jumbo Feather', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08441', 'size' => 'Sheet 260x336'],
        ['name' => 'Carrara Square Subway', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08442', 'size' => '100x100'],
        ['name' => 'Carrara Square Mosaic', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08443', 'size' => 'Chip 23x23 - Sheet 300x300'],
        ['name' => 'Carrara Jumbo Parquet', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08444', 'size' => 'Sheet 299x299'],
        ['name' => 'Carrara Arabesque', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07299', 'size' => 'Sheet 305x327'],
        ['name' => 'Carrara Flower', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08445', 'size' => 'Sheet 290x325'],
        ['name' => 'Carrara Flower Pink & Green', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08446', 'size' => 'Sheet 290x325'],
        ['name' => 'Carrara Fan', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07364', 'size' => 'Chip 15x15 - Frame 252x455 - Sheet 252x252'],
        ['name' => 'Carrara Herringbone 20x64', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08234', 'size' => 'Chip 20x64 - Sheet 248x280'],
        ['name' => 'Carrara Herringbone 25x98', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08235', 'size' => 'Chip 25x98 - Sheet 282x305'],
        ['name' => 'Carrara Herringbone 50x200', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08237', 'size' => 'Chip 50x200 - Sheet 285x293'],
        ['name' => 'Carrara Herringbone 25x75', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08238', 'size' => 'Chip 25x75 - Sheet 305x325'],
        ['name' => 'Carrara Herringbone 25x130', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08239', 'size' => 'Chip 25x130 - Sheet 266x372'],
        ['name' => 'Carrara Basketweave Cinderella Grey', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08466', 'size' => '25x48 - Sheet 305x305'],
        ['name' => 'Carrara Basketweave Nero Dot', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08471', 'size' => '25x48 - Sheet 305x305'],
        ['name' => 'Carrara Basketweave Nero Dot', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08472', 'size' => '25x48 - Sheet 305x305'],
        ['name' => 'Carrara Basketweave Thassos Dot', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08477', 'size' => '25x48 - Sheet 305x305'],
        ['name' => 'Carrara Basketweave Jumbo Nero Dot', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08478', 'size' => '56x80 - Sheet 278x278'],
        ['name' => 'Fishscale Stone Mosaic Carrara', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08487', 'size' => 'Chip 69x78 - Sheet 225x235'],
        ['name' => 'Fishscale Stone Mosaic Carrara', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS08488', 'size' => 'Chip 69x78 - Sheet 225x235'],
        ['name' => 'Fishscale Stone Mosaic Pietra Grey', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08489', 'size' => 'Chip 69x78 - Sheet 225x235'],
        ['name' => 'Fishscale Stone Mosaic Mint Honed', 'indent' => '', 'design' => '', 'material' => '', 'application' => '', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08490', 'size' => 'Chip 69x78 - Sheet 225x235'],
        ['name' => 'Connect Rosso', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07433', 'size' => 'Chip 35x150 - Sheet 294x302'],
        ['name' => 'Connect Rome Travertine', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08434', 'size' => 'Chip 35x150 - Sheet 294x302'],
        ['name' => 'Connect Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS08436', 'size' => 'Chip 35x150 - Sheet 294x302'],
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
