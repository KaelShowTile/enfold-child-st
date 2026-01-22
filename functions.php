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
        ['name' => 'Broadway Beige', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'satin', 'code' => 'SW04271', 'size' => '68x280'],
        ['name' => 'Broadway Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'satin', 'code' => 'SW04274', 'size' => '68x280'],
        ['name' => 'Broadway Frame Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'gloss', 'code' => 'SW04278', 'size' => '68x280'],
        ['name' => 'Broadway Frame Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'gloss', 'code' => 'SW04279', 'size' => '68x280'],
        ['name' => 'Broadway Frame Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'gloss', 'code' => 'SW04275', 'size' => '68x280'],
        ['name' => 'Broadway Frame Porcelain', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'gloss', 'code' => 'SW04276', 'size' => '68x280'],
        ['name' => 'Broadway Frame White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V1', 'finish' => 'gloss', 'code' => 'SW04277', 'size' => '68x280'],
        ['name' => 'Broadway Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'satin', 'code' => 'SW04273', 'size' => '68x280'],
        ['name' => 'Broadway Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'satin', 'code' => 'SW04272', 'size' => '68x280'],
        ['name' => 'Broadway White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'satin', 'code' => 'SW04270', 'size' => '68x280'],
        ['name' => 'Clayart Avorio White', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04318', 'size' => '200x200'],
        ['name' => 'Clayart Avorio White Decor', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW04322', 'size' => '200x200'],
        ['name' => 'Clayart Avorio White Subway', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04326', 'size' => '53x300'],
        ['name' => 'Clayart Biscotto Beige', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04317', 'size' => '200x200'],
        ['name' => 'Clayart Biscotto Beige Decor', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW04321', 'size' => '200x200'],
        ['name' => 'Clayart Biscotto Beige Subway', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04325', 'size' => '53x300'],
        ['name' => 'Clayart Cotto Terracotta', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04315', 'size' => '200x200'],
        ['name' => 'Clayart Cotto Terracotta Decor', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW04319', 'size' => '200x200'],
        ['name' => 'Clayart Cotto Terracotta Subway', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04323', 'size' => '53x300'],
        ['name' => 'Clayart Grigio Grey', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04316', 'size' => '200x200'],
        ['name' => 'Clayart Grigio Grey Decor', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW04320', 'size' => '200x200'],
        ['name' => 'Clayart Grigio Grey Subway', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW04324', 'size' => '53x300'],
        ['name' => 'Crema Marfil and Rosso', 'indent' => '', 'design' => 'Moroccan Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS07506', 'size' => 'Sheet 302x305'],
        ['name' => 'Cross Art Carrara', 'indent' => '', 'design' => 'Moroccan Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS07503', 'size' => 'Sheet 302x305'],
        ['name' => 'Cross Art Carrara and Peacock', 'indent' => '', 'design' => 'Moroccan Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS07504', 'size' => 'Sheet 302x305'],
        ['name' => 'Cross Art Crema Marfil', 'indent' => '', 'design' => 'Moroccan Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS07505', 'size' => 'Sheet 302x305'],
        ['name' => 'Cross Art Thassos and Azul', 'indent' => '', 'design' => 'Moroccan Look Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS07502', 'size' => 'Sheet 302x305'],
        ['name' => 'Ember Bianco', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07541', 'size' => '60x240'],
        ['name' => 'Ember Brown', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07544', 'size' => '60x240'],
        ['name' => 'Ember Carmel', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07543', 'size' => '60x240'],
        ['name' => 'Ember Denim', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07538', 'size' => '60x240'],
        ['name' => 'Ember Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07537', 'size' => '60x240'],
        ['name' => 'Ember Greige', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07540', 'size' => '60x240'],
        ['name' => 'Ember Light Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07542', 'size' => '60x240'],
        ['name' => 'Ember Steel', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07539', 'size' => '60x240'],
        ['name' => 'Ferrara Emerald Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07306', 'size' => '100x100'],
        ['name' => 'Ferrara Flamingo', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07307', 'size' => '100x100'],
        ['name' => 'Ferrara Light Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07308', 'size' => '100x100'],
        ['name' => 'Ferrara Navy', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07309', 'size' => '100x100'],
        ['name' => 'Ferrara Ocher Yellow', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07310', 'size' => '100x100'],
        ['name' => 'Ferrara Prune Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07311', 'size' => '100x100'],
        ['name' => 'Ferrara Prussian Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07312', 'size' => '100x100'],
        ['name' => 'Ferrara Sage', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07313', 'size' => '100x100'],
        ['name' => 'Ferrara White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07314', 'size' => '100x100'],
        ['name' => 'Fishscale Aqua', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07272', 'size' => 'Chip 83x90 - Sheet 274x293'],
        ['name' => 'Fishscale Ash Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05063', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05067', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Bronze Metal Plated', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05074', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Charcoal', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05066', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Denim Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05073', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Foam Light Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05072', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Green Crackle', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07276', 'size' => 'Chip 83x90 - Sheet 274x293'],
        ['name' => 'Fishscale Grey Crackle', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07273', 'size' => 'Chip 83x90 - Sheet 274x293'],
        ['name' => 'Fishscale Midnight Crackle', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07275', 'size' => 'Chip 83x90 - Sheet 274x293'],
        ['name' => 'Fishscale Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07274', 'size' => 'Chip 83x90 - Sheet 274x293'],
        ['name' => 'Fishscale Pastel Pink', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05302', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Reef Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05064', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale Water Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05065', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05062', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fishscale White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05075', 'size' => 'Chip 87x95 - Sheet 259x273'],
        ['name' => 'Fragments Bianco', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR25160', 'size' => '600x1200'],
        ['name' => 'Fragments Bianco', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR25164', 'size' => '300x600'],
        ['name' => 'Fragments Grigio', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR25163', 'size' => '600x1200'],
        ['name' => 'Fragments Grigio', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR25167', 'size' => '300x600'],
        ['name' => 'Fragments Perla', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR25161', 'size' => '600x1200'],
        ['name' => 'Fragments Perla', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR25165', 'size' => '300x600'],
        ['name' => 'Fragments Taupe', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR25162', 'size' => '600x1200'],
        ['name' => 'Fragments Taupe', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR25166', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Basalt Black', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04098', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Basalt Black', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04098', 'size' => '600x600'],
        ['name' => 'Galaxy Terrazzo Basalt Black', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04102', 'size' => '300x300'],
        ['name' => 'Galaxy Terrazzo Basalt Black', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04102', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Basalt Black', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04102', 'size' => '600x600'],
        ['name' => 'Galaxy Terrazzo Nougat', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04096', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Nougat', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04096', 'size' => '600x600'],
        ['name' => 'Galaxy Terrazzo Nougat', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04100', 'size' => '300x300'],
        ['name' => 'Galaxy Terrazzo Nougat', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04100', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Nougat', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04100', 'size' => '600x600'],
        ['name' => 'Galaxy Terrazzo Silver Pearl', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04097', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Silver Pearl', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04097', 'size' => '600x600'],
        ['name' => 'Galaxy Terrazzo Silver Pearl', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04101', 'size' => '300x300'],
        ['name' => 'Galaxy Terrazzo Silver Pearl', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04101', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Silver Pearl', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04101', 'size' => '600x600'],
        ['name' => 'Galaxy Terrazzo Snow White', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04095', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Snow White', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'lappato', 'code' => 'TR04095', 'size' => '600x600'],
        ['name' => 'Galaxy Terrazzo Snow White', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04099', 'size' => '300x300'],
        ['name' => 'Galaxy Terrazzo Snow White', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04099', 'size' => '300x600'],
        ['name' => 'Galaxy Terrazzo Snow White', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04099', 'size' => '600x600'],
        ['name' => 'Limestone Sky Body', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01090', 'size' => 'Random Body'],
        ['name' => 'Limestone Sky Corner', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01091', 'size' => 'Random Corner'],
        ['name' => 'Limestone Grey Body', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01043', 'size' => 'Random Body'],
        ['name' => 'Limestone Grey Corner', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01044', 'size' => 'Random Corner'],
        ['name' => 'Cappucino Grey Body', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01094', 'size' => 'Random Body'],
        ['name' => 'Cappucino Grey Corner', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01095', 'size' => 'Random Corner'],
        ['name' => 'Glacier Stone Body', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01096', 'size' => 'Random Body'],
        ['name' => 'Glacier Stone Corner', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01097', 'size' => 'Random Corner'],
        ['name' => 'Imperial Gold Quartz', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS07064', 'size' => 'Random Body'],
        ['name' => 'Imperial Olive Quartz', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS07066', 'size' => 'Random Body'],
        ['name' => 'Imperial Rosa Quartz', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS07065', 'size' => 'Random Body'],
        ['name' => 'Imperial White Quartz', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS07041', 'size' => 'Random Body'],
        ['name' => 'Imperial White Quartz', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'NS07042', 'size' => 'Flamed 400x600x20mm'],
        ['name' => 'Italian Hexagon Anchor Lily', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06037', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Antracite', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06046', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Black', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06047', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Cornice', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06039', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Floral', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06034', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Grey', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06045', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Kubic', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06038', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Leaf', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06035', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Lily', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06036', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Mix', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06043', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Porzione', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06040', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Rombo', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06042', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Star', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06041', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Hexagon Terracotta', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06044', 'size' => 'Hexagon 216x250'],
        ['name' => 'Italian Pattern Antracite Dark Grey', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06058', 'size' => '200x200'],
        ['name' => 'Italian Pattern Cardinal', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06050', 'size' => '200x200'],
        ['name' => 'Italian Pattern Charcoal Black', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06059', 'size' => '200x200'],
        ['name' => 'Italian Pattern Geometrical', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06048', 'size' => '200x200'],
        ['name' => 'Italian Pattern Lantern', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06055', 'size' => '200x200'],
        ['name' => 'Italian Pattern Leaf', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06052', 'size' => '200x200'],
        ['name' => 'Italian Pattern Light Grey', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06057', 'size' => '200x200'],
        ['name' => 'Italian Pattern Lily', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06054', 'size' => '200x200'],
        ['name' => 'Italian Pattern Linea', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06049', 'size' => '200x200'],
        ['name' => 'Italian Pattern Star', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06051', 'size' => '200x200'],
        ['name' => 'Italian Pattern Terracotta', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06053', 'size' => '200x200'],
        ['name' => 'Italian Pattern Triangle', 'indent' => '', 'design' => 'Pattern Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06056', 'size' => '200x200'],
        ['name' => 'Katrina Antracite', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01477', 'size' => '600x600'],
        ['name' => 'Katrina Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01474', 'size' => '600x600'],
        ['name' => 'Katrina Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01474', 'size' => '600x1200'],
        ['name' => 'Katrina Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01475', 'size' => '600x600'],
        ['name' => 'Katrina Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01475', 'size' => '600x1200'],
        ['name' => 'Katrina Grigio', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01476', 'size' => '600x600'],
        ['name' => 'Kera Amaranto Jam', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06372', 'size' => '60x240'],
        ['name' => 'Kera Bianco Latte', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06374', 'size' => '60x240'],
        ['name' => 'Kera Blu Notte', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06376', 'size' => '60x240'],
        ['name' => 'Kera Cashmere Melato', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06377', 'size' => '60x240'],
        ['name' => 'Kera Grigio', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06373', 'size' => '60x240'],
        ['name' => 'Kera Nero', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06371', 'size' => '60x240'],
        ['name' => 'Kera Verde Candito', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06378', 'size' => '60x240'],
        ['name' => 'Kera Verde Forest', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06375', 'size' => '60x240'],
        ['name' => 'Limestone 2.0 Bianco', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01385', 'size' => '300x300'],
        ['name' => 'Limestone 2.0 Bianco', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01385', 'size' => '300x600'],
        ['name' => 'Limestone 2.0 Bianco', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01385', 'size' => '600x600'],
        ['name' => 'Limestone 2.0 Bianco', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01385', 'size' => '600x1200'],
        ['name' => 'Limestone 2.0 Chalk', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01457', 'size' => '600x600'],
        ['name' => 'Limestone 2.0 Chalk', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01457', 'size' => '600x1200'],
        ['name' => 'Limestone 2.0 Cotton', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01386', 'size' => '300x300'],
        ['name' => 'Limestone 2.0 Cotton', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01386', 'size' => '300x600'],
        ['name' => 'Limestone 2.0 Cotton', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01386', 'size' => '600x600'],
        ['name' => 'Limestone 2.0 Grigio', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01387', 'size' => '300x300'],
        ['name' => 'Limestone 2.0 Grigio', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01387', 'size' => '300x600'],
        ['name' => 'Limestone 2.0 Grigio', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01387', 'size' => '600x600'],
        ['name' => 'Limestone 2.0 Nero', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01388', 'size' => '300x300'],
        ['name' => 'Limestone 2.0 Nero', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01388', 'size' => '300x600'],
        ['name' => 'Limestone 2.0 Nero', 'indent' => '', 'design' => 'Limestone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01388', 'size' => '600x600'],
        ['name' => 'Lincoln Beige', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06315', 'size' => '60x250'],
        ['name' => 'Lincoln Charcoal', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06316', 'size' => '60x250'],
        ['name' => 'Lincoln Fog', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06317', 'size' => '60x250'],
        ['name' => 'Lincoln Multicolor', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06318', 'size' => '60x250'],
        ['name' => 'Lincoln Sunset', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06319', 'size' => '60x250'],
        ['name' => 'Manhattan Stack Bond', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10146', 'size' => 'Chip 15x98 - Sheet 298x304'],
        ['name' => 'Manhattan Mini Flute', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10049', 'size' => 'Chip 15x151 - Sheet 305x305'],
        ['name' => 'Manhattan Bamboo', 'indent' => 'yes', 'design' => 'Bamboo Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10050', 'size' => '30x920x18mm'],
        ['name' => 'Manhattan Tictax', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10051', 'size' => 'Chup 35x150 - Sheet 291x302'],
        ['name' => 'Manhattan Subway', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10147', 'size' => '75x305x10mm'],
        ['name' => 'Manhattan Hexagon', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10145', 'size' => 'Chip 70x70 - Sheet 250x289'],
        ['name' => 'Manhattan Palladiana', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10052', 'size' => 'Sheet 305x305'],
        ['name' => 'Manhattan Decor', 'indent' => 'yes', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10048', 'size' => 'Sheet 306x306'],
        ['name' => 'Marvel Diva Ice Crystal', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03439', 'size' => '600x1200'],
        ['name' => 'Marvel Diva Ice Crystal', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03442', 'size' => '600x1200'],
        ['name' => 'Marvel Diva Baobab', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03440', 'size' => '600x1200'],
        ['name' => 'Marvel Diva Galaxy', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03441', 'size' => '600x1200'],
        ['name' => 'Marvel Diva Taj Mahal', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03445', 'size' => '600x1200'],
        ['name' => 'Marvel Diva Taj Mahal', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03443', 'size' => '600x1200'],
        ['name' => 'Marvel Diva White Everest', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03446', 'size' => '600x1200'],
        ['name' => 'Marvel Diva White Everest', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL03444', 'size' => '600x1200'],
        ['name' => 'Messina White', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR22218', 'size' => '600x600'],
        ['name' => 'Messina Leaf', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR22219', 'size' => '600x600'],
        ['name' => 'Messina Anthracite', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR22220', 'size' => '600x600'],
        ['name' => 'Metallic Silver', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07428', 'size' => '65x266'],
        ['name' => 'Metallic Bronze', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07429', 'size' => '65x266'],
        ['name' => 'Metallic Gold', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07430', 'size' => '65x266'],
        ['name' => 'Mille Porridge', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25373', 'size' => '75x225'],
        ['name' => 'Mille Putty', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25374', 'size' => '75x225'],
        ['name' => 'Mille Meadow', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25375', 'size' => '75x225'],
        ['name' => 'Mille Mist', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25376', 'size' => '75x225'],
        ['name' => 'Mille Horizon', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25377', 'size' => '75x225'],
        ['name' => 'Mille Lake', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25378', 'size' => '75x225'],
        ['name' => 'Mille Indigo', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25379', 'size' => '75x225'],
        ['name' => 'Mille Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25380', 'size' => '75x225'],
        ['name' => 'Mille White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW25381', 'size' => '75x225'],
        ['name' => 'Mille White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW25382', 'size' => '75x225'],
        ['name' => 'Milo White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21351', 'size' => '50x150'],
        ['name' => 'Milo White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21356', 'size' => '50x150'],
        ['name' => 'Milo Orchard Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21352', 'size' => '50x150'],
        ['name' => 'Milo Orchard Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21357', 'size' => '50x150'],
        ['name' => 'Milo Blue Grass', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21353', 'size' => '50x150'],
        ['name' => 'Milo Blue Grass', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21358', 'size' => '50x150'],
        ['name' => 'Milo Black Hat', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21355', 'size' => '50x150'],
        ['name' => 'Milo Black Hat', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21360', 'size' => '50x150'],
        ['name' => 'Milo Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21354', 'size' => '50x150'],
        ['name' => 'Milo Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21359', 'size' => '50x150'],
        ['name' => 'Mini Crazy Botticino', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'NS08100', 'size' => 'Sheet 300x300'],
        ['name' => 'Mini Crazy Carrara', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'NS08101', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Crazy Carrara & Bardiglio', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS08102', 'size' => 'Sheet 300x300'],
        ['name' => 'Mini Crazy Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS08103', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Crazy Mix Marble', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS08104', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Crazy Multi Marble', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'NS08105', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Crazy Rosso', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'NS08106', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Crazy Verde', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'NS08107', 'size' => 'Sheet 300x300'],
        ['name' => 'Mini Crazy Carrara & Bardiglio & Nero', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS10367', 'size' => 'Sheet 305x305'],
        ['name' => 'Mojo Sea Water', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06431', 'size' => '60x246'],
        ['name' => 'Mojo Cotto', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06432', 'size' => '60x246'],
        ['name' => 'Mojo Denim', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06433', 'size' => '60x246'],
        ['name' => 'Mojo Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06434', 'size' => '60x246'],
        ['name' => 'Mojo Blu', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06283', 'size' => '60x246'],
        ['name' => 'Mojo Light Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06284', 'size' => '60x246'],
        ['name' => 'Mojo White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06285', 'size' => '60x246'],
        ['name' => 'Mojo Ocra', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06286', 'size' => '60x246'],
        ['name' => 'Mini Split Calacatta Oro Mosaic Honed', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07566', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Split Ming Green Rustic Mosaic Honed', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07567', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Split Travertine Rustic Mosaic Honed', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07568', 'size' => 'Sheet 305x305'],
        ['name' => 'Mini Split Carrara Mosaic Honed', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07569', 'size' => 'Sheet 305x305'],
        ['name' => 'Moroccan Pale Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07258', 'size' => '100x100'],
        ['name' => 'Moroccan Bleu Jean', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07259', 'size' => '100x100'],
        ['name' => 'Moroccan Bleu Foncé', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07260', 'size' => '100x100'],
        ['name' => 'Moroccan Emerald Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07261', 'size' => '100x100'],
        ['name' => 'Moroccan Jaune Dóre', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07262', 'size' => '100x100'],
        ['name' => 'Moroccan Ecru Off White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07263', 'size' => '100x100'],
        ['name' => 'Moroccan White Fes', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07264', 'size' => '100x100'],
        ['name' => 'Moroccan Noir Carbone', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07265', 'size' => '100x100'],
        ['name' => 'Moroccan Beige Clear', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07266', 'size' => '100x100'],
        ['name' => 'Moroccan Snow', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07267', 'size' => '100x100'],
        ['name' => 'Moroccan Gris Rosa', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07268', 'size' => '100x100'],
        ['name' => 'Moroccan Pale Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07269', 'size' => '100x100'],
        ['name' => 'Moroccan Raw Natural', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07270', 'size' => '100x100'],
        ['name' => 'Moroccan Atlas Petrole', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07271', 'size' => '100x100'],
        ['name' => 'Moroccan Caramel', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07274', 'size' => '100x100'],
        ['name' => 'Moroccan Rouge Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07275', 'size' => '100x100'],
        ['name' => 'Moroccan Marron', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07276', 'size' => '100x100'],
        ['name' => 'Moroccan Vert Mousse', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Terracotta', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW07273', 'size' => '100x100'],
        ['name' => 'Mountain Marron Beige', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'textured', 'code' => 'OD04111', 'size' => 'Sheet 226x326'],
        ['name' => 'Mountain Antricita Black', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'textured', 'code' => 'OD04112', 'size' => 'Sheet 226x326'],
        ['name' => 'Mountain Pizarra Blue', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'textured', 'code' => 'OD04113', 'size' => 'Sheet 226x326'],
        ['name' => 'Mountain Oxido White', 'indent' => '', 'design' => 'Crazy Pave', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'textured', 'code' => 'OD04114', 'size' => 'Sheet 226x326'],
        ['name' => 'Napoli Brick Brown', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06552', 'size' => '48x450'],
        ['name' => 'Napoli Brick Charcoal', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06553', 'size' => '48x450'],
        ['name' => 'Napoli Brick Cotto', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06554', 'size' => '48x450'],
        ['name' => 'Napoli Brick Mud', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06555', 'size' => '48x450'],
        ['name' => 'Napoli Brick Multicolor', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06556', 'size' => '48x450'],
        ['name' => 'Napoli Brick Napa', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06557', 'size' => '48x450'],
        ['name' => 'Napoli Brick Pearl', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06558', 'size' => '48x450'],
        ['name' => 'Napoli Brick Sand', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06559', 'size' => '48x450'],
        ['name' => 'Napoli Brick Silver', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06560', 'size' => '48x450'],
        ['name' => 'Napoli Brick Smoke', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06561', 'size' => '48x450'],
        ['name' => 'Napoli Brick Sunset', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V3', 'finish' => 'matt', 'code' => 'SW06562', 'size' => '48x450'],
        ['name' => 'Napoli Brick White', 'indent' => '', 'design' => 'Brick Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => 'V1', 'finish' => 'matt', 'code' => 'SW06563', 'size' => '48x450'],
        ['name' => 'Zellige White Gesso', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW21286', 'size' => '100x100'],
        ['name' => 'Zellige Taupe Cammello', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW21287', 'size' => '100x100'],
        ['name' => 'Zellige Terra Corallo', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW21288', 'size' => '100x100'],
        ['name' => 'Zellige Navy', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW21289', 'size' => '100x100'],
        ['name' => 'Zellige Sky Cielo', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW21290', 'size' => '100x100'],
        ['name' => 'Zellige Olive Salvia', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW21291', 'size' => '100x100'],
        ['name' => 'Zellige Blue Petrolio', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW21292', 'size' => '100x100'],
        ['name' => 'Zellige Green Bosco', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW21293', 'size' => '100x100'],
        ['name' => 'Zellige Mint Turchese', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW21294', 'size' => '100x100'],
        ['name' => 'Zellige Ash Lana', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW21295', 'size' => '100x100'],
        ['name' => 'Zellige Grigio Argilla', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW21296', 'size' => '100x100'],
        ['name' => 'Zellige Coal Carbone', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW21297', 'size' => '100x100'],
        ['name' => 'Piccola Denim', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06443', 'size' => '50x150'],
        ['name' => 'Piccola Denim', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06444', 'size' => '50x150'],
        ['name' => 'Piccola Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06445', 'size' => '50x150'],
        ['name' => 'Piccola Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06446', 'size' => '50x150'],
        ['name' => 'Piccola Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06447', 'size' => '50x150'],
        ['name' => 'Piccola Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06448', 'size' => '50x150'],
        ['name' => 'Piccola Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06449', 'size' => '50x150'],
        ['name' => 'Piccola Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06450', 'size' => '50x150'],
        ['name' => 'Piccola White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06451', 'size' => '50x150'],
        ['name' => 'Piccola White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06452', 'size' => '50x150'],
        ['name' => 'Piccola Terracotta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06453', 'size' => '50x150'],
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
