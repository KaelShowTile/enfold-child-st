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
        ['name' => 'Hampton Handmade White 50x200', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'matt', 'code' => 'SW01392', 'size' => '50x200'],
        ['name' => 'Hampton Handmade White 100x100', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'gloss', 'code' => 'SW01281', 'size' => '100x100'],
        ['name' => 'Hampton Handmade White 100x100', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'matt', 'code' => 'SW01282', 'size' => '100x100'],
        ['name' => 'Hampton Handmade White 76x302', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'gloss', 'code' => 'SW01393', 'size' => '76x302'],
        ['name' => 'Hampton Handmade White 76x302', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'matt', 'code' => 'SW01394', 'size' => '76x302'],
        ['name' => 'Grande Ceppo Taupe', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR01112', 'size' => '1200x1200'],
        ['name' => 'Grande Ceppo White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR01113', 'size' => '1200x1200'],
        ['name' => 'Flute Mini Bamboo Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07351', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Ming Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07352', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Indian Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07353', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo New York', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07520', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Rosa Portagallo', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07521', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Taj Mahal', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07522', 'size' => 'Chip 15x150 - Sheet 304x330'],
        ['name' => 'Flute Mini Bamboo Utica', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20524', 'size' => 'Chip 15x150 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Calacatta', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07560', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Travertine', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20523', 'size' => 'Chip 15x152 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Portugal', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20559', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20560', 'size' => 'Chip 15x150 - Sheet 305x305'],
        ['name' => 'Flute Mini Bamboo Red Travertine', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07547', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Everest Terrazzo White Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21047', 'size' => '300x300'],
        ['name' => 'Everest Terrazzo White Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21047', 'size' => '300x600'],
        ['name' => 'Everest Terrazzo White Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21047', 'size' => '600x600'],
        ['name' => 'Everest Terrazzo Taupe Grip', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21048', 'size' => '300x600'],
        ['name' => 'Everest Terrazzo Taupe Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21050', 'size' => '300x300'],
        ['name' => 'Everest Terrazzo Taupe Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21050', 'size' => '300x600'],
        ['name' => 'Everest Terrazzo Taupe Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21050', 'size' => '600x600'],
        ['name' => 'Everest Terrazzo Charcoal Grip', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21054', 'size' => '300x600'],
        ['name' => 'Everest Terrazzo Charcoal Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21056', 'size' => '300x300'],
        ['name' => 'Everest Terrazzo Charcoal Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21056', 'size' => '300x600'],
        ['name' => 'Everest Terrazzo Charcoal Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21056', 'size' => '600x600'],
        ['name' => 'Everest Terrazzo Grey Grip', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21051', 'size' => '300x600'],
        ['name' => 'Everest Terrazzo Grey Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21053', 'size' => '300x300'],
        ['name' => 'Everest Terrazzo Grey Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21053', 'size' => '300x600'],
        ['name' => 'Everest Terrazzo Grey Matt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21053', 'size' => '600x600'],
        ['name' => 'Cube Mosaic White Gloss', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05068', 'size' => 'Chip 48x48 - Sheet 266x305'],
        ['name' => 'Cube Mosaic Grey Gloss', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05069', 'size' => 'Chip 48x48 - Sheet 266x305'],
        ['name' => 'Cube Mosaic Mixed Gloss', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05070', 'size' => 'Chip 48x48 - Sheet 266x305'],
        ['name' => 'Cube Mosaic Carrara Matt', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05285', 'size' => 'Chip 48x48 - Sheet 266x305'],
        ['name' => 'Cube Mosaic Black Gloss', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05071', 'size' => 'Chip 48x48 - Sheet 266x305'],
        ['name' => 'Hilton Champagne', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03425', 'size' => '300x300'],
        ['name' => 'Hilton Champagne', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03425', 'size' => '300x600'],
        ['name' => 'Hilton Champagne', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03425', 'size' => '600x600'],
        ['name' => 'Hilton Champagne', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03425', 'size' => '600x1200'],
        ['name' => 'Hilton Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03426', 'size' => '300x300'],
        ['name' => 'Hilton Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03426', 'size' => '300x600'],
        ['name' => 'Hilton Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03426', 'size' => '600x600'],
        ['name' => 'Hilton Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03426', 'size' => '600x1200'],
        ['name' => 'Hilton Coral', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03427', 'size' => '300x300'],
        ['name' => 'Hilton Coral', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03427', 'size' => '300x600'],
        ['name' => 'Hilton Coral', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03427', 'size' => '600x600'],
        ['name' => 'Hilton Coral', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03427', 'size' => '600x1200'],
        ['name' => 'Hilton Ice Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03428', 'size' => '300x300'],
        ['name' => 'Hilton Ice Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03428', 'size' => '300x600'],
        ['name' => 'Hilton Ice Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03428', 'size' => '600x600'],
        ['name' => 'Hilton Ice Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03428', 'size' => '600x1200'],
        ['name' => 'Hilton Pearl', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03429', 'size' => '300x300'],
        ['name' => 'Hilton Pearl', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03429', 'size' => '300x600'],
        ['name' => 'Hilton Pearl', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03429', 'size' => '600x600'],
        ['name' => 'Hilton Pearl', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03429', 'size' => '600x1200'],
        ['name' => 'Hilton Champagne', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03126', 'size' => '400x600x20'],
        ['name' => 'Hilton Clay', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03127', 'size' => '400x600x20'],
        ['name' => 'Hilton Coral', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03128', 'size' => '400x600x20'],
        ['name' => 'Hilton Ice Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03129', 'size' => '400x600x20'],
        ['name' => 'Hilton Pearl', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'OD03130', 'size' => '400x600x20'],
        ['name' => 'Lantic Grey Sandblasted', 'indent' => '', 'design' => 'Marble Stone', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24053', 'size' => '406x610x12'],
        ['name' => 'Lantic Grey Sandblasted', 'indent' => '', 'design' => 'Marble Stone', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24053', 'size' => '406x610x30'],
        ['name' => 'Lantic Grey Sandblasted French Pattern', 'indent' => '', 'design' => 'Marble Stone', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24055', 'size' => '12mm thickness'],
        ['name' => 'Lantic Grey Sandblasted French Pattern', 'indent' => '', 'design' => 'Marble Stone', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24055', 'size' => '30mm thickness'],
        ['name' => 'Lantic Grey Sandblasted Bullnose', 'indent' => '', 'design' => 'Marble Stone', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'sandblasted', 'code' => 'NS24057', 'size' => '406x610x30'],
        ['name' => 'Lilli Pilli Shelley', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13084', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Bridge', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13085', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Burnley', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13086', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Coppin', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13087', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Highett', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13088', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Swan', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13090', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Brighton', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13091', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Baker', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13092', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Clifton', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13093', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Lennox', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13094', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Buckingham', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13095', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Elizabeth', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13096', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Kelso', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13097', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Wellington', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13098', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Church', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13099', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Stanely', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13100', 'size' => '200x200'],
        ['name' => 'Lilli Pilli Victoria', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV13101', 'size' => '200x200'],
        ['name' => 'Madrid White Gloss 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'VV16109', 'size' => '147x147'],
        ['name' => 'Madrid White Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16110', 'size' => '147x147'],
        ['name' => 'Madrid Terra Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16112', 'size' => '147x147'],
        ['name' => 'Madrid Grey Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16113', 'size' => '147x147'],
        ['name' => 'Madrid Flamingo Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16115', 'size' => '147x147'],
        ['name' => 'Madrid Aquamar Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16116', 'size' => '147x147'],
        ['name' => 'Madrid Marina Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16119', 'size' => '147x147'],
        ['name' => 'Madrid Giuliana Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'VV16120', 'size' => '147x147'],
        ['name' => 'Madrid Romina Gloss 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'VV16121', 'size' => '147x147'],
        ['name' => 'Madrid Bambu Matt 147x147', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV16122', 'size' => '147x147'],
        ['name' => 'Manor Earth', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03495', 'size' => '300x300'],
        ['name' => 'Manor Earth', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03495', 'size' => '300x600'],
        ['name' => 'Manor Earth', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03495', 'size' => '600x600'],
        ['name' => 'Manor Earth', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03495', 'size' => '600x1200'],
        ['name' => 'Manor Earth', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03154', 'size' => '600x600'],
        ['name' => 'Manor Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03496', 'size' => '300x300'],
        ['name' => 'Manor Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03496', 'size' => '300x600'],
        ['name' => 'Manor Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03496', 'size' => '600x600'],
        ['name' => 'Manor Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03496', 'size' => '600x1200'],
        ['name' => 'Manor Moon', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03155', 'size' => '600x600'],
        ['name' => 'Manor Earth Subway Tumbled Edge', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03497', 'size' => '150x150'],
        ['name' => 'Manor Moon Subway Tumbled Edge', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03498', 'size' => '150x150'],
        ['name' => 'Volakas Hexagon 48mm', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10189', 'size' => 'Chip 48mm - Sheet 300x315'],
        ['name' => 'Volakas Hexagon 70mm', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10190', 'size' => 'Chip 70mm - Sheet 250x289'],
        ['name' => 'Volakas Herringbone 25x98', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10191', 'size' => 'Chip 25x98 - Sheet 282x305'],
        ['name' => 'Volakas Herringbone 50x200', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10192', 'size' => 'Chip 50x200 - Sheet 285x293'],
        ['name' => 'Volakas Mini Flute Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10087', 'size' => 'Chip 20x151 - Sheet 305x305'],
        ['name' => 'Volakas & Gris Travertine Mosaic', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10088', 'size' => 'Chip 65mm - Sheet 310x310'],
        ['name' => 'Viola Arch', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20368', 'size' => 'Chip 68x148 - Sheet 297x272x10'],
        ['name' => 'Viola Penny', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20369', 'size' => 'Chip 25mm - Sheet 310x312x10'],
        ['name' => 'Viola Split 3mm Joint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20370', 'size' => 'Chip 25/35/55x75 - Sheet 312x317x10'],
        ['name' => 'Viola Split 10mm Joint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20549', 'size' => 'Chip 25/35/45x55 - Sheet 305x315x10'],
        ['name' => 'Viola Stack', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20371', 'size' => 'Chip 15x98 - 298x305x10'],
        ['name' => 'Viola Square Mosaic', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20372', 'size' => 'Chip 25x25 - Sheet 302x302x10'],
        ['name' => 'Viola Square Subway', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20373', 'size' => '101x101x10'],
        ['name' => 'Viola Subway', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20374', 'size' => '75x305x10'],
        ['name' => 'Vino Grigio Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V4', 'finish' => 'matt', 'code' => 'OD04135', 'size' => '125x250'],
        ['name' => 'Vino Rosa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V4', 'finish' => 'matt', 'code' => 'OD04136', 'size' => '125x250'],
        ['name' => 'Vino Rosso Terracotta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V4', 'finish' => 'matt', 'code' => 'OD04137', 'size' => '125x250'],
        ['name' => 'Vino Bianco White Matt', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V4', 'finish' => 'matt', 'code' => 'OD04138', 'size' => '125x250'],
        ['name' => 'Vincent Kit Kat Deep Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07099', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07100', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Dark Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07101', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Gold', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07384', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Metal', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07385', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Crackle White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07413', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Sand', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07414', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07095', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Sky Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07094', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Sage', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07096', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Toffee', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07085', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Mint Speckle', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07086', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Cotton Speckle', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07087', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Caramel', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07088', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Biscuit', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07089', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Agate', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07081', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Artichoke', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07082', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Midnight Fog', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07083', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Pale Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07084', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07097', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07093', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Vincent Kit Kat Pink', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07102', 'size' => 'Chip 12x92 - Sheet 284x295'],
        ['name' => 'Richstone Cream', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01403', 'size' => '300x600'],
        ['name' => 'Richstone Cream', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01403', 'size' => '600x600'],
        ['name' => 'Richstone Light Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01404', 'size' => '300x600'],
        ['name' => 'Richstone Light Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01404', 'size' => '600x600'],
        ['name' => 'Rockland 2.0 Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01513', 'size' => '600x600'],
        ['name' => 'Rockland 2.0 Sand', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01513', 'size' => '600x1200'],
        ['name' => 'Rockland 2.0 White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01514', 'size' => '600x600'],
        ['name' => 'Rockland 2.0 White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01514', 'size' => '600x1200'],
        ['name' => 'Sakura White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07375', 'size' => 'Chip 15x145 - Sheet 296x315'],
        ['name' => 'Sakura Light Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07376', 'size' => 'Chip 15x145 - Sheet 296x315'],
        ['name' => 'Sakura Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07377', 'size' => 'Chip 15x145 - Sheet 296x315'],
        ['name' => 'Sakura Dark Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07378', 'size' => 'Chip 15x145 - Sheet 296x315'],
        ['name' => 'Sakura Midnight', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07379', 'size' => 'Chip 15x145 - Sheet 296x315'],
        ['name' => 'Sakura Brown', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07380', 'size' => 'Chip 15x145 - Sheet 296x315'],
        ['name' => 'Spanish Pool Tile Avalon 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01152', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Spanish Pool Tile Byron Bay 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01151', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Spanish Pool Tile Coogee 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01157', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Spanish Pool Tile Cronulla 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01158', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Spanish Pool Tile Noosa 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01153', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Spanish Pool Tile Palm Beach 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01154', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Spanish Pool Tile Whitehaven 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01150', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Spanish Pool Tile Whitsunday 2.0', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT01155', 'size' => 'Chip 25x25 - Sheet 334x334'],
        ['name' => 'Star Grey', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV04135', 'size' => '200x200'],
        ['name' => 'Star Terracotta', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV04136', 'size' => '200x200'],
        ['name' => 'Star Black', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV04137', 'size' => '200x200'],
        ['name' => 'Star Green', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'VV04138', 'size' => '200x200'],
        ['name' => 'Tenby White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01217', 'size' => '595x1200'],
        ['name' => 'Tenby White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01217', 'size' => '1200x1200'],
        ['name' => 'Tenby Beige', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01218', 'size' => '1200x1200'],
        ['name' => 'Tenby Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01219', 'size' => '595x1200'],
        ['name' => 'Tenby Grey', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'inout', 'code' => 'SL01219', 'size' => '1200x1200'],
        ['name' => 'New Stone Greige', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'SL01428', 'size' => '750x1500'],
        ['name' => 'New Stone Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'SL01429', 'size' => '750x1500'],
        ['name' => 'My Terracotta Body', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03156', 'size' => '300x300x10'],
        ['name' => 'My Terracotta Step Tread', 'indent' => '', 'design' => 'Terracotta', 'material' => 'Terracotta', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03157', 'size' => '300x300x15'],
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
