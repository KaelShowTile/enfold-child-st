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
    if (!isset($_GET['run_tile_import'])) return;

    // Consistently formatted data from your spreadsheet
    $tile_data = [
        ['name' => 'Croma Bleu Clair', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07471', 'size' => '101x116'],
        ['name' => 'Croma White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07472', 'size' => '101x116'],
        ['name' => 'Croma Rose', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07473', 'size' => '101x116'],
        ['name' => 'Croma Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07474', 'size' => '101x116'],
        ['name' => 'Croma Clay', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07475', 'size' => '101x116'],
        ['name' => 'Croma Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07476', 'size' => '101x116'],
        ['name' => 'Croma Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07477', 'size' => '101x116'],
        ['name' => 'Croma Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07478', 'size' => '101x116'],
        ['name' => 'Croma Denim', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07479', 'size' => '101x116'],
        ['name' => 'Ezarri Cocktail Mojito', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09064', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Manhattan', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09065', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Bellini', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09067', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Kir Royal', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09068', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Margarita', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09072', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Gin Fizz', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09073', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail San Francisco', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09074', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Eclipse', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09075', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Caipirinha', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09076', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Grasshopper', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09077', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Tomahawk', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09078', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Bluemoon', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09079', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Blue Lagoon', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09081', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Daikiri', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09066', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Alexander', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09069', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Cosmopolitan', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09070', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Bloody Mary', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09071', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Cocktail Long Island', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'PT09080', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Ocean', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09001', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Azur', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09002', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Coral', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09003', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Jade', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09005', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Perla', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09006', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Marfil', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09094', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Cobre', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09098', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Ebano', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09099', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Iris Green Pearl', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09100', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2545A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09008', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2551A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09009', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2546A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09010', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2539B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09011', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2554C', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09012', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2533A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09013', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2538D', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09014', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2532B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09016', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2531B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09017', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2537E', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09018', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2552A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09019', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2553B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09020', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2555C', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09021', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2548C', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09033', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2549A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09034', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2547A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09035', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2535A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09024', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2542B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09025', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2536C', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09026', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2557D', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09027', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2543D', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09028', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2556C', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09029', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2544A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09030', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2559B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09031', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2558B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09015', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2534A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09022 ', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2541A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09023', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2559B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09031', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2530D', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09032', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Lisa 2531D', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09117', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2508A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09039', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2505A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09040', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2562B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09036', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2503D', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09037', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2512C', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09038', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2521B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09041', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2529B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09042', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2510A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09063', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2502A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09043', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2586B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09044', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2585B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09045', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2507A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09046', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2596B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09047', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2597B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09048', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2525B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09049', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2526B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09050', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2513A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09051', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2511A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09052', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2509C', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09053', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2523B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09054', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2524B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09055', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2564B', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09056', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2506C', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09057', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2504A', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09058', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2522B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09062', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2560A', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09059', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2501B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09060', 'size' => 'Chip 25mm - Sheet 312x495mm'],
        ['name' => 'Ezarri Niebla 2516B', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Glass Tile', 'application' => 'Pool Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'PT09061', 'size' => 'Chip 25mm - Sheet 312x495mm'],
    ];

    foreach ($tile_data as $data) {
        if (get_page_by_title($data['name'], OBJECT, 'tile')) continue;

        $post_id = wp_insert_post([
            'post_title'   => $data['name'],
            'post_type'    => 'tile',
            'post_status'  => 'publish',
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            // FIX: Only send 'yes' in an array if the spreadsheet says 'yes'
            // Otherwise, send an empty array to leave the checkbox unchecked.
            $indent_value = (strtolower($data['indent']) === 'yes') ? ['yes'] : [];
            update_field('indent_item', $indent_value, $post_id);

            update_field('tile_design', $data['design'], $post_id);
            update_field('tile_material', $data['material'], $post_id);
            update_field('tile_application', $data['application'], $post_id);
            update_field('tile_variation', $data['variation'], $post_id);

            $finish_repeater = [[
                'finish_name'  => $data['finish'],
                'product_code' => $data['code'],
                'tile_size'    => [['tile_size_name' => $data['size']]]
            ]];
            update_field('tile_finish', $finish_repeater, $post_id);
        }
    }
    echo "Import Successful!";
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
