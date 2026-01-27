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
        ['name' => 'Classico Travertine Beige', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04141', 'size' => '600x600'],
['name' => 'Classico Travertine Beige', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04447', 'size' => '300x600'],
['name' => 'Classico Travertine Beige', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04447', 'size' => '600x600'],
['name' => 'Classico Travertine Beige', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04447', 'size' => '600x1200'],
['name' => 'Classico Travertine Beige 3D Line', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL04452', 'size' => '3D Line 600x1200'],
['name' => 'Classico Travertine Beige Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP04110', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Classico Travertine Beige Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP04107', 'size' => '400x600x20'],
['name' => 'Classico Travertine Silver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04143', 'size' => '600x600'],
['name' => 'Classico Travertine Silver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04449', 'size' => '300x600'],
['name' => 'Classico Travertine Silver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04449', 'size' => '600x600'],
['name' => 'Classico Travertine Silver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04449', 'size' => '600x1200'],
['name' => 'Classico Travertine Silver 3D Line', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL04451', 'size' => '3D Line 600x1200'],
['name' => 'Classico Travertine Silver Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP04112', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Classico Travertine Silver Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP04109', 'size' => '400x600x20'],
['name' => 'Classico Travertine White', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04142', 'size' => '600x600'],
['name' => 'Classico Travertine White', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04448', 'size' => '300x600'],
['name' => 'Classico Travertine White', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04448', 'size' => '600x600'],
['name' => 'Classico Travertine White', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL04448', 'size' => '600x1200'],
['name' => 'Classico Travertine White 3D Line', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SL04450', 'size' => '3D Line 600x1200'],
['name' => 'Classico Travertine White Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP04111', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Classico Travertine White Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP04108', 'size' => '400x600x20'],
['name' => 'Glebe Beige', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW04301', 'size' => '75x200'],
['name' => 'Glebe Beige', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW04306', 'size' => '100x100'],
['name' => 'Glebe Beige', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW04311', 'size' => '50x150'],
['name' => 'Glebe Bianco White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW04300', 'size' => '75x200'],
['name' => 'Glebe Bianco White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW04305', 'size' => '100x100'],
['name' => 'Glebe Bianco White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V2', 'finish' => 'gloss', 'code' => 'SW04310', 'size' => '50x150'],
['name' => 'Glebe Giada Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04302', 'size' => '75x200'],
['name' => 'Glebe Giada Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04307', 'size' => '100x100'],
['name' => 'Glebe Giada Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04312', 'size' => '50x150'],
['name' => 'Glebe Grigio Charcoal', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04304', 'size' => '75x200'],
['name' => 'Glebe Grigio Charcoal', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04309', 'size' => '100x100'],
['name' => 'Glebe Grigio Charcoal', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04314', 'size' => '50x150'],
['name' => 'Glebe Turchese Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04303', 'size' => '75x200'],
['name' => 'Glebe Turchese Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04308', 'size' => '100x100'],
['name' => 'Glebe Turchese Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => 'V3', 'finish' => 'gloss', 'code' => 'SW04313', 'size' => '50x150'],
['name' => 'Hand Crafted Crackle Blanco White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06007', 'size' => '75x150'],
['name' => 'Hand Crafted Crackle Blanco White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06057', 'size' => '75x300'],
['name' => 'Hand Crafted Crackle Blanco White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06169', 'size' => '100x100'],
['name' => 'Hand Crafted Crackle Cian', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06010', 'size' => '75x150'],
['name' => 'Hand Crafted Crackle Cian', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06060', 'size' => '75x300'],
['name' => 'Hand Crafted Crackle Crocodile', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06012', 'size' => '75x150'],
['name' => 'Hand Crafted Crackle Crocodile', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06061', 'size' => '75x300'],
['name' => 'Hand Crafted Crackle Dove', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06009', 'size' => '75x150'],
['name' => 'Hand Crafted Crackle Dove', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06059', 'size' => '75x300'],
['name' => 'Hand Crafted Crackle Mar', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06011', 'size' => '75x150'],
['name' => 'Hand Crafted Crackle Mar', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06062', 'size' => '75x300'],
['name' => 'Hand Crafted Crackle Mar', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06167', 'size' => '150x150'],
['name' => 'Hand Crafted Crackle Mar', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06168', 'size' => '50x250'],
['name' => 'Hand Crafted Crackle Nacar', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06008', 'size' => '75x150'],
['name' => 'Hand Crafted Crackle Nacar', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW06058', 'size' => '75x300'],
['name' => 'Lava Burgundy', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21385', 'size' => '132x132'],
['name' => 'Lava Burgundy', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21390', 'size' => '62x200'],
['name' => 'Lava Coral Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21384', 'size' => '132x132'],
['name' => 'Lava Coral Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21389', 'size' => '62x200'],
['name' => 'Lava Malachite', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21387', 'size' => '132x132'],
['name' => 'Lava Malachite', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21392', 'size' => '62x200'],
['name' => 'Lava Sahara', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21383', 'size' => '132x132'],
['name' => 'Lava Sahara', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21388', 'size' => '62x200'],
['name' => 'Lava Sea Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21386', 'size' => '132x132'],
['name' => 'Lava Sea Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21391', 'size' => '62x200'],
['name' => 'Luma Ivory', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22544', 'size' => '100x100'],
['name' => 'Luma Ivory', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22545', 'size' => '65x200'],
['name' => 'Luma Platinum', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22550', 'size' => '100x100'],
['name' => 'Luma Platinum', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22551', 'size' => '65x200'],
['name' => 'Luma Silver', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22548', 'size' => '100x100'],
['name' => 'Luma Silver', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22549', 'size' => '65x200'],
['name' => 'Luma White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22546', 'size' => '100x100'],
['name' => 'Luma White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW22547', 'size' => '65x200'],
['name' => 'Magma Avio', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22164', 'size' => '1200x2800x6'],
['name' => 'Magma Black & White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22165', 'size' => '1200x2800x6'],
['name' => 'Magma Natural', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22163', 'size' => '1200x2800x6'],
['name' => 'Malaga Basil Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21400', 'size' => '75x150'],
['name' => 'Malaga Basil Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21406', 'size' => '100x100'],
['name' => 'Malaga Beige Argile', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21398', 'size' => '75x150'],
['name' => 'Malaga Beige Argile', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21404', 'size' => '100x100'],
['name' => 'Malaga Blue Moon', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21399', 'size' => '75x150'],
['name' => 'Malaga Blue Moon', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21405', 'size' => '100x100'],
['name' => 'Malaga Brush Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21397', 'size' => '75x150'],
['name' => 'Malaga Brush Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21403', 'size' => '100x100'],
['name' => 'Malaga Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21396', 'size' => '75x150'],
['name' => 'Malaga Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21402', 'size' => '100x100'],
['name' => 'Malaga White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21395', 'size' => '75x150'],
['name' => 'Malaga White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21401', 'size' => '100x100'],
['name' => 'Mento Azzurro', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07329', 'size' => '60x370'],
['name' => 'Mento Azzurro', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07315', 'size' => '102x102'],
['name' => 'Mento Beige', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07325', 'size' => '60x370'],
['name' => 'Mento Beige', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07211', 'size' => '102x102'],
['name' => 'Mento Bianco', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07320', 'size' => '60x370'],
['name' => 'Mento Bianco', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07319', 'size' => '102x102'],
['name' => 'Mento Blu', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07330', 'size' => '60x370'],
['name' => 'Mento Blu', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07317', 'size' => '102x102'],
['name' => 'Mento Fango', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07322', 'size' => '60x370'],
['name' => 'Mento Fango', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07316', 'size' => '102x102'],
['name' => 'Mento Grigio', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07321', 'size' => '60x370'],
['name' => 'Mento Malva', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07324', 'size' => '60x370'],
['name' => 'Mento Nero', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07323', 'size' => '60x370'],
['name' => 'Mento Nero', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07318', 'size' => '102x102'],
['name' => 'Mento Ocra', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07326', 'size' => '60x370'],
['name' => 'Mento Ocra', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07212', 'size' => '102x102'],
['name' => 'Mento Terra', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07327', 'size' => '60x370'],
['name' => 'Mento Terra', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07213', 'size' => '102x102'],
['name' => 'Mento Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07328', 'size' => '60x370'],
['name' => 'Mento Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07214', 'size' => '102x102'],
['name' => 'Murcia Moonwhite', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01283', 'size' => '125x125'],
['name' => 'Murcia Moonwhite', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01285', 'size' => '62.5x125'],
['name' => 'Murcia Vanila', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01284', 'size' => '125x125'],
['name' => 'Murcia Vanila', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01286', 'size' => '62.5x125'],
['name' => 'Murcia White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01287', 'size' => '62.5x125'],
['name' => 'Murcia White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW01289', 'size' => '125x125'],
['name' => 'Murcia White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01288', 'size' => '62.5x125'],
['name' => 'Murcia White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW01290', 'size' => '125x125'],
['name' => 'Onyx Amber', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22103', 'size' => '1600x3200x6'],
['name' => 'Onyx Amber', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL22412', 'size' => '600x1200x9'],
['name' => 'Onyx Black Diamond', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22207', 'size' => '1600x3200x6'],
['name' => 'Onyx Black Diamond', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL22414', 'size' => '600x1200x9'],
['name' => 'Onyx Crystal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22102', 'size' => '1600x3200x6'],
['name' => 'Onyx Crystal', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL22410', 'size' => '600x1200x9'],
['name' => 'Onyx Gemstone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22105', 'size' => '1600x3200x6'],
['name' => 'Onyx Gemstone', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL22413', 'size' => '600x1200x9'],
['name' => 'Onyx Jade', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'PS22104', 'size' => '1600x3200x6'],
['name' => 'Onyx Jade', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Slab', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL22411', 'size' => '600x1200x9'],
['name' => 'Rakeen Concave Basil', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07427', 'size' => 'Chip 20x145 - Sheet 291x301'],
['name' => 'Rakeen Concave Powder Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07492', 'size' => 'Chip 20x145 - Sheet 291x301'],
['name' => 'Rakeen Concave Bronze', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07425', 'size' => 'Chip 20x145 - Sheet 291x301'],
['name' => 'Rakeen Concave Faint Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07494', 'size' => 'Chip 20x145 - Sheet 291x301'],
['name' => 'Rakeen Concave Kensington', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07495', 'size' => 'Chip 20x145 - Sheet 291x301'],
['name' => 'Rakeen Concave Olive', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07426', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Plain Midnight', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07496', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Plain Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07497', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Plain Pink', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07498', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Speckle Warm White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07499', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Terracotta', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07491', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Speckle White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07277', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Speckle White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07278', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07279', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Cobalt', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07280', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Ocean', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07281', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07282', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Red', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07283', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07284', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Light Brown', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07107', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Dark Brown', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07108', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Light Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07109', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Dark Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07110', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Deep Brown', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07111', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Rakeen Concave Coffee', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07112', 'size' => 'Chip 20x145 - Sheet 296x299'],
['name' => 'Red Travertine Finger Mosaic', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07198', 'size' => 'Chip 15x98 - Sheet 305x305'],
['name' => 'Red Travertine Long Subway Unfilled', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07195', 'size' => '75x305'],
['name' => 'Red Travertine Mini Fiorano', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07196', 'size' => 'Sheet305x305'],
['name' => 'Red Travertine Square Mosaic', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07197', 'size' => 'Chip 48x48 - Sheet 305x305'],
['name' => 'Red Travertine Square Unfilled', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS07194', 'size' => '150x150'],
['name' => 'Riso Bianco Gloss 150x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21365', 'size' => '150x150'],
['name' => 'Riso Bianco Gloss 50x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21369', 'size' => '50x150'],
['name' => 'Riso Blue Gloss 150x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21368', 'size' => '150x150'],
['name' => 'Riso Blue Gloss 50x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21372', 'size' => '50x150'],
['name' => 'Riso Grigio Gloss 150x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21367', 'size' => '150x150'],
['name' => 'Riso Grigio Gloss 50x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21371', 'size' => '50x150'],
['name' => 'Riso Natural Gloss 150x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21366', 'size' => '150x150'],
['name' => 'Riso Natural Gloss 50x150', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21370', 'size' => '50x150'],
['name' => 'Roman Travertine Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20562', 'size' => 'Concave 30x300 - Sheet 300x314x15'],
['name' => 'Roman Travertine Connect', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20571', 'size' => 'Connect 35x150 - Sheet 294x302x10'],
['name' => 'Roman Travertine Herringbone', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20572', 'size' => 'Herringbone 25x75 - Sheet 305x325x7'],
['name' => 'Roman Travertine Mini Arch', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20563', 'size' => 'Arch 48x60 - Sheet 298x306x7'],
['name' => 'Roman Travertine Mini Square', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20569', 'size' => 'Square 20x20 - Sheet 305x305x7'],
['name' => 'Roman Travertine Split 10mm Joint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20568', 'size' => 'Split 25/35/45x55 - Sheet 305x315x7'],
['name' => 'Roman Travertine Split 5mm Joint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20567', 'size' => 'Chip 25/35/45x55 - Sheet 283x304x7'],
['name' => 'Roman Travertine Stack Concave', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20570', 'size' => 'Concave 30x145 - Sheet 292x318x15'],
['name' => 'Roman Travertine Stack Finger', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS20565', 'size' => 'Finger 15x98 - Sheet 298x305x7'],
['name' => 'Rome Travertine Avorio White', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL0416', 'size' => '600x1200'],
['name' => 'Rome Travertine Avorio White', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL0421', 'size' => '600x1200'],
['name' => 'Rome Travertine Crema', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL0420', 'size' => '600x1200'],
['name' => 'Rome Travertine Crema', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL0423', 'size' => '600x1200'],
['name' => 'Rome Travertine Greige', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL0419', 'size' => '600x1200'],
['name' => 'Rome Travertine Greige', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'SL0422', 'size' => '600x1200'],
['name' => 'Seashell Flamed & Tumbled', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS24058', 'size' => '406x610x12'],
['name' => 'Seashell Flamed & Tumbled', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'NS24059', 'size' => '406x610x28'],
['name' => 'Seashell Flamed & Tumbled', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'NS24060', 'size' => 'Bullnose 406x610x28'],
['name' => 'Sevilla Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21279', 'size' => '65x200'],
['name' => 'Sevilla Black', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21285', 'size' => '132x132'],
['name' => 'Sevilla Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21276', 'size' => '65x200'],
['name' => 'Sevilla Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21282', 'size' => '132x132'],
['name' => 'Sevilla Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21277', 'size' => '65x200'],
['name' => 'Sevilla Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21283', 'size' => '132x132'],
['name' => 'Sevilla Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21278', 'size' => '65x200'],
['name' => 'Sevilla Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21284', 'size' => '132x132'],
['name' => 'Sevilla Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21275', 'size' => '65x200'],
['name' => 'Sevilla Pink', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21281', 'size' => '132x132'],
['name' => 'Sevilla White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21274', 'size' => '65x200'],
['name' => 'Sevilla White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW21280', 'size' => '132x132'],
['name' => 'Silver Travertine', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24368', 'size' => '406x406x12'],
['name' => 'Silver Travertine', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24368', 'size' => '406x610x12'],
['name' => 'Silver Travertine', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24368', 'size' => '610x610x12'],
['name' => 'Silver Travertine', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'MS24369', 'size' => '406x406x30'],
['name' => 'Silver Travertine', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'MS24369', 'size' => '406x610x30'],
['name' => 'Silver Travertine', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'MS24369', 'size' => '610x610x30'],
['name' => 'Silver Travertine Coping', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'MS24372', 'size' => 'Bullnose 406x610x30'],
['name' => 'Silver Travertine Coping', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'MS24372', 'size' => 'Drop Edge 406x610x30/60'],
['name' => 'Silver Travertine Coping', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'MS24372', 'size' => 'Pencil Edge 406x1220x30'],
['name' => 'Silver Travertine Coping', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'MS24372', 'size' => 'Bullnose 406x1220x30'],
['name' => 'Silver Travertine French Pattern', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24370', 'size' => '12mm thickness'],
['name' => 'Silver Travertine French Pattern', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24370', 'size' => '30mm thickness'],
['name' => 'Silver Travertine Subway', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24371', 'size' => '75x305x12'],
['name' => 'Silver Travertine Subway', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24371', 'size' => '100x100x30'],
['name' => 'Silver Travertine Subway', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS24371', 'size' => '100x203x30'],
['name' => 'Square 23mm Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05488', 'size' => 'Chip 23x23 - Sheet 300x300'],
['name' => 'Square 23mm White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05487', 'size' => 'Chip 23x23 - Sheet 300x300'],
['name' => 'Square 48mm Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05490', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05489', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Blush', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05495', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Caramel', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05479', 'size' => 'Chip 48x48 - Sheet 306x306'],
['name' => 'Square 48mm Cream', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05494', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Dark Blue', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05498', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Deep Brown', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05481', 'size' => 'Chip 48x48 - Sheet 306x306'],
['name' => 'Square 48mm Fog Grey', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05493', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07482', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Jade', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05497', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05496', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07483', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Pink', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07480', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Terracotta', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05480', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm Vincent Cotton', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05483', 'size' => 'Chip 48x48 - Sheet 306x306'],
['name' => 'Square 48mm Vincent Mint', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05482', 'size' => 'Chip 48x48 - Sheet 306x306'],
['name' => 'Square 48mm White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05491', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS07481', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 48mm White Speckle', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05478', 'size' => 'Chip 48x48 - Sheet 300x300'],
['name' => 'Square 97mm Black', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05486', 'size' => 'Chip 97x97 - Sheet 300x300'],
['name' => 'Square 97mm White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS05484', 'size' => 'Chip 97x97 - Sheet 300x300'],
['name' => 'Square 97mm White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'MS05485', 'size' => 'Chip 97x97 - Sheet 300x300'],
['name' => 'Square Tumbled Bardiglio', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20539', 'size' => '305x305'],
['name' => 'Square Tumbled Bardiglio', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20139', 'size' => '200x200'],
['name' => 'Square Tumbled Botticino', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20226', 'size' => '200x200'],
['name' => 'Square Tumbled Botticino', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20326', 'size' => '150x150'],
['name' => 'Square Tumbled Botticino', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20426', 'size' => '305x305'],
['name' => 'Square Tumbled Botticino', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20526', 'size' => '100x100'],
['name' => 'Square Tumbled Carrara', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20234', 'size' => '150x150'],
['name' => 'Square Tumbled Carrara', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20335', 'size' => '200x200'],
['name' => 'Square Tumbled Carrara', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20538', 'size' => '305x305'],
['name' => 'Square Tumbled Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20530', 'size' => '100x100'],
['name' => 'Square Tumbled Nero Marquina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20537', 'size' => '305x305'],
['name' => 'Square Tumbled Nero Marquina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20236', 'size' => '150x150'],
['name' => 'Square Tumbled Nero Marquina', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20543', 'size' => '100x100'],
['name' => 'Square Tumbled Noce', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20129', 'size' => '305x305'],
['name' => 'Square Tumbled Noce', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20230', 'size' => '150x150'],
['name' => 'Square Tumbled Noce', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20531', 'size' => '100x100'],
['name' => 'Square Tumbled Travertine', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20532', 'size' => '100x100'],
['name' => 'Square Tumbled Travertine Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20533', 'size' => '100x100'],
['name' => 'Square Tumbled Travertine Silver', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20534', 'size' => '100x100'],
['name' => 'Square Tumbled Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20535', 'size' => '100x100'],
['name' => 'Square Tumbled Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20434', 'size' => '200x200'],
['name' => 'Square Tumbled Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20536', 'size' => '305x305'],
['name' => 'Square Tumbled Viola', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20443', 'size' => '100x100'],
['name' => 'Square Tumbled Viola', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20544', 'size' => '305x305'],
['name' => 'Subway Tumbled Botticino', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20525', 'size' => '65x200'],
['name' => 'Subway Tumbled Carrara', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20527', 'size' => '65x200'],
['name' => 'Subway Tumbled Cinder Grey', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20561', 'size' => '65x200'],
['name' => 'Subway Tumbled Mint', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20160', 'size' => '65x200'],
['name' => 'Subway Tumbled Roman Travertine', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20566', 'size' => '65x200'],
['name' => 'Subway Tumbled Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20529', 'size' => '65x200'],
['name' => 'Subway Tumbled Viola', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'MS20528', 'size' => '65x200'],
['name' => 'Sunrise Quartz Body', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01007', 'size' => 'Random Body'],
['name' => 'Sunrise Quartz Corner', 'indent' => '', 'design' => 'Loose Stone Cladding', 'material' => 'Natural Stone', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01008', 'size' => 'Random Corner'],
['name' => 'Terra Bianco White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR06097', 'size' => '600x600'],
['name' => 'Terra Bianco White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06098', 'size' => '300x300'],
['name' => 'Terra Bianco White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06098', 'size' => '300x600'],
['name' => 'Terra Bianco White', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06098', 'size' => '600x600'],
['name' => 'Terra Charcoal', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06101', 'size' => '600x600'],
['name' => 'Terra Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR06099', 'size' => '300x300'],
['name' => 'Terra Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR06099', 'size' => '300x600'],
['name' => 'Terra Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR06099', 'size' => '600x600'],
['name' => 'Terra Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06100', 'size' => '300x300'],
['name' => 'Terra Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06100', 'size' => '300x600'],
['name' => 'Terra Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06100', 'size' => '600x600'],
['name' => 'Terra Light Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06104', 'size' => '300x300'],
['name' => 'Terra Light Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06104', 'size' => '600x600'],
['name' => 'Terra Original', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR06102', 'size' => '600x600'],
['name' => 'Terra Original', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06103', 'size' => '300x300'],
['name' => 'Terra Original', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06103', 'size' => '300x600'],
['name' => 'Terra Original', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR06103', 'size' => '600x600'],
['name' => 'Terrazzo Multi Asphalt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21044', 'size' => '300x600'],
['name' => 'Terrazzo Multi Asphalt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21044', 'size' => '600x600'],
['name' => 'Terrazzo Multi Asphalt', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR21045', 'size' => '300x600'],
['name' => 'Terrazzo Multi Cement', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21041', 'size' => '300x600'],
['name' => 'Terrazzo Multi Cement', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21041', 'size' => '600x600'],
['name' => 'Terrazzo Multi Cement', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR21042', 'size' => '300x600'],
['name' => 'Terrazzo Multi Gravel', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21034', 'size' => '300x600'],
['name' => 'Terrazzo Multi Gravel', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21034', 'size' => '600x600'],
['name' => 'Terrazzo Multi Organic', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21037', 'size' => '300x600'],
['name' => 'Terrazzo Multi Organic', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR21037', 'size' => '600x600'],
['name' => 'Terrazzo Multi Organic', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR21039', 'size' => '300x300'],
['name' => 'Terrazzo Multi Organic', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'TR21039', 'size' => '300x600'],
['name' => 'The Limestone Bianco', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01345', 'size' => '1200x1200'],
['name' => 'The Limestone Earth', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01344', 'size' => '1200x1200'],
['name' => 'T-Marble Warm', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01509', 'size' => '300x600'],
['name' => 'T-Marble Warm', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01509', 'size' => '600x600'],
['name' => 'T-Marble White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01508', 'size' => '300x600'],
['name' => 'T-Marble White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01508', 'size' => '600x600'],
['name' => 'Toledo Arena', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07594', 'size' => '100x100'],
['name' => 'Toledo Arena', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07595', 'size' => '65x200'],
['name' => 'Toledo Cinnamon', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07596', 'size' => '100x100'],
['name' => 'Toledo Cinnamon', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07597', 'size' => '65x200'],
['name' => 'Toledo Silica White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07592', 'size' => '100x100'],
['name' => 'Toledo Silica White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07593', 'size' => '65x200'],
['name' => 'Toledo Storm Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07598', 'size' => '100x100'],
['name' => 'Toledo Storm Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07599', 'size' => '65x200'],
['name' => 'Toledo Terracotta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07600', 'size' => '100x100'],
['name' => 'Toledo Terracotta', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07601', 'size' => '65x200'],
['name' => 'Toledo Weald Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07602', 'size' => '100x100'],
['name' => 'Toledo Weald Green', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Ceramic Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW07603', 'size' => '65x200'],
['name' => 'Toscano Avorio Ivory', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04102', 'size' => '600x1200'],
['name' => 'Toscano Avorio Ivory', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04178', 'size' => '600x1200'],
['name' => 'Toscano Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04105', 'size' => '600x1200'],
['name' => 'Toscano Grigio Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04181', 'size' => '600x1200'],
['name' => 'Toscano Nero Black', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04103', 'size' => '600x1200'],
['name' => 'Toscano Nero Black', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04179', 'size' => '600x1200'],
['name' => 'Toscano Sabbia Sand', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04106', 'size' => '600x1200'],
['name' => 'Toscano Sabbia Sand', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04182', 'size' => '600x1200'],
['name' => 'Toscano Verde Green', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD04104', 'size' => '600x1200'],
['name' => 'Toscano Verde Green', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'TR04180', 'size' => '600x1200'],
['name' => 'Travertine Classic', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'NS01010', 'size' => 'Bullnose 406x610x30'],
['name' => 'Travertine Classic', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'NS01076', 'size' => '406x610x30'],
['name' => 'Travertine Classic', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'NS01003', 'size' => '406x610x12'],
['name' => 'Travertine Classic Crazy Pave', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS26074', 'size' => 'Various Sizes'],
['name' => 'Travertine Classic French Pattern', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'tumbled', 'code' => 'NS01004', 'size' => '12mm thickness'],
['name' => 'Travertine Classic Cobblestone', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS15022', 'size' => '100x100x30mm'],
['name' => 'Travertine Light Honed and Filled', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS01035', 'size' => '305x610x12'],
['name' => 'Travertine Light Honed and Filled', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS01035', 'size' => '610x610x12'],
['name' => 'Travertine Light Honed and Filled', 'indent' => '', 'design' => 'Travertine', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS01035', 'size' => '610x1220x12'],
['name' => 'Trendo Verde', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06326', 'size' => '60x240'],
['name' => 'Trendo Verde Decor', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06331', 'size' => '60x240'],
['name' => 'Trento Bianco', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06321', 'size' => '60x240'],
['name' => 'Trento Bianco Decor', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06328', 'size' => '60x240'],
['name' => 'Trento Bruno', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06322', 'size' => '60x240'],
['name' => 'Trento Bruno Decor', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06329', 'size' => '60x240'],
['name' => 'Trento Natural', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06320', 'size' => '60x240'],
['name' => 'Trento Natural Decor', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06327', 'size' => '60x240'],
['name' => 'Trento Nero', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06323', 'size' => '60x240'],
['name' => 'Trento Nero Decor', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06324', 'size' => '60x240'],
['name' => 'Trento Rosso', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW06325', 'size' => '60x240'],
['name' => 'Trento Rosso Decor', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'SW06330', 'size' => '60x240'],
['name' => 'Trevi Bianco', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03139', 'size' => '300x600'],
['name' => 'Trevi Bianco', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03139', 'size' => '600x600'],
['name' => 'Trevi Bianco', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03139', 'size' => '600x1200'],
['name' => 'Trevi Bianco', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03482', 'size' => '300x300'],
['name' => 'Trevi Bianco', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03482', 'size' => '300x600'],
['name' => 'Trevi Bianco', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03482', 'size' => '600x600'],
['name' => 'Trevi Bianco', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03482', 'size' => '600x1200'],
['name' => 'Trevi Bianco Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03484', 'size' => 'Chip 30x600 - Shet 300x600'],
['name' => 'Trevi Bianco Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03098', 'size' => 'Bullnose 400x600x20'],
['name' => 'Trevi Bianco Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03097', 'size' => '400x600x20'],
['name' => 'Trevi Sabbia', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03140', 'size' => '300x600'],
['name' => 'Trevi Sabbia', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03140', 'size' => '600x600'],
['name' => 'Trevi Sabbia', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'external', 'code' => 'OD03140', 'size' => '600x1200'],
['name' => 'Trevi Sabbia', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03483', 'size' => '300x300'],
['name' => 'Trevi Sabbia', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03483', 'size' => '300x600'],
['name' => 'Trevi Sabbia', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03483', 'size' => '600x600'],
['name' => 'Trevi Sabbia', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03483', 'size' => '600x1200'],
['name' => 'Trevi Sabbia Decor', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL03485', 'size' => 'Chip 30x600 - Shet 300x600'],
['name' => 'Trevi Sabbia Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03100', 'size' => 'Bullnose 400x600x20'],
['name' => 'Trevi Sabbia Paver', 'indent' => '', 'design' => 'Travertine Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03099', 'size' => '400x600x20'],
['name' => 'Uno Azure', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06187', 'size' => '600x600'],
['name' => 'Uno Chester Green', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06188', 'size' => '600x600'],
['name' => 'Uno Dark Grey', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06189', 'size' => '600x600'],
['name' => 'Uno Ivory', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06190', 'size' => '600x600'],
['name' => 'Uno Light Grey', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06191', 'size' => '600x600'],
['name' => 'Uno Marsala Red', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06192', 'size' => '600x600'],
['name' => 'Uno Mud', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06193', 'size' => '600x600'],
['name' => 'Uno Mustard', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06194', 'size' => '600x600'],
['name' => 'Uno Nude', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06195', 'size' => '600x600'],
['name' => 'Uno Ochre', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06196', 'size' => '600x600'],
['name' => 'Uno Orange', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06197', 'size' => '600x600'],
['name' => 'Uno Steel Blue', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06198', 'size' => '600x600'],
['name' => 'Uno Tan', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06199', 'size' => '600x600'],
['name' => 'Uno Umber', 'indent' => '', 'design' => 'Concrete Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL06200', 'size' => '600x600'],
['name' => 'Vincent Herringbone Green', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07074', 'size' => 'Chip 15x50 - Sheet 299x306'],
['name' => 'Vincent Herringbone Sand', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07558', 'size' => 'Chip 15x50 - Sheet 299x306'],
['name' => 'Vincent Herringbone White', 'indent' => '', 'design' => 'Mosaic Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'MS07072', 'size' => 'Chip 15x50 - Sheet 299x306'],
['name' => 'Potifino White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01395', 'size' => '300x300'],
['name' => 'Potifino White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01395', 'size' => '300x600'],
['name' => 'Potifino White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01395', 'size' => '600x600'],
['name' => 'Potifino White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01398', 'size' => '300x600'],
['name' => 'Potifino White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01398', 'size' => '600x600'],
['name' => 'Potifino Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01396', 'size' => '300x300'],
['name' => 'Potifino Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01396', 'size' => '300x600'],
['name' => 'Potifino Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01396', 'size' => '600x600'],
['name' => 'Potifino Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01401', 'size' => '300x600'],
['name' => 'Potifino Silver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01401', 'size' => '600x600'],
['name' => 'Phuket Perla Sandblasted Tumbled', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'NS01098', 'size' => 'Bullnose 400x600x30'],
['name' => 'Phuket Perla Sandblasted Tumbled', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'NS01099', 'size' => '400x600x30'],
['name' => 'Pacific White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01515', 'size' => '300x600'],
['name' => 'Pacific White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01515', 'size' => '600x600'],
['name' => 'Pacific White', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01515', 'size' => '600x1200'],
['name' => 'Pacific Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01516', 'size' => '300x600'],
['name' => 'Pacific Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01516', 'size' => '600x600'],
['name' => 'Pacific Cotton', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SL01516', 'size' => '600x1200'],
['name' => 'Origin Celadon', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07369', 'size' => '51x161'],
['name' => 'Origin Wild Olive', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07370', 'size' => '51x161'],
['name' => 'Origin Sky Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07371', 'size' => '51x161'],
['name' => 'Origin Burnt Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07372', 'size' => '51x161'],
['name' => 'Origin Blue Night', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07373', 'size' => '51x161'],
['name' => 'Origin Caramel', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07374', 'size' => '51x161'],
['name' => 'Origin White', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07375', 'size' => '51x161'],
['name' => 'Origin Celadon', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07376', 'size' => '100x100'],
['name' => 'Origin Wild Olive', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07377', 'size' => '100x100'],
['name' => 'Origin Sky Blue', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07378', 'size' => '100x100'],
['name' => 'Origin Burnt Red', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07379', 'size' => '100x100'],
['name' => 'Origin Blue Night', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07380', 'size' => '100x100'],
['name' => 'Origin Caramel', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07381', 'size' => '100x100'],
['name' => 'Origin White', 'indent' => '', 'design' => 'Subway Tile', 'material' => '', 'application' => 'Wall Tile', 'variation' => 'V4', 'finish' => 'gloss', 'code' => 'SW07382', 'size' => '100x100'],
['name' => 'Opera Bianco Cloud Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03056', 'size' => '600x600x20'],
['name' => 'Opera Bianco Cloud Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03057', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Opera Grigio Storm Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03058', 'size' => '600x600x20'],
['name' => 'Opera Grigio Storm Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03059', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Opera Neutral Sola Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'paver', 'code' => 'PP03060', 'size' => '600x600x20'],
['name' => 'Opera Neutral Sola Paver', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'coping', 'code' => 'PP03061', 'size' => 'Pencil Edge 400x600x20'],
['name' => 'Opera Bianco Cloud', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03309', 'size' => '300x300'],
['name' => 'Opera Bianco Cloud', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03309', 'size' => '300x600'],
['name' => 'Opera Bianco Cloud', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03309', 'size' => '600x600'],
['name' => 'Opera Bianco Cloud', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03309', 'size' => '600x1200'],
['name' => 'Opera Bianco Cloud Scoring', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03310', 'size' => 'Scoring 100x200 - 300x600'],
['name' => 'Opera Grigio Storm', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03311', 'size' => '300x300'],
['name' => 'Opera Grigio Storm', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03311', 'size' => '300x600'],
['name' => 'Opera Grigio Storm', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03311', 'size' => '600x600'],
['name' => 'Opera Grigio Storm', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03311', 'size' => '600x1200'],
['name' => 'Opera Grigio Storm Scoring', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03312', 'size' => 'Scoring 100x200 - 300x600'],
['name' => 'Opera Neutral Sola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03313', 'size' => '300x300'],
['name' => 'Opera Neutral Sola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03313', 'size' => '300x600'],
['name' => 'Opera Neutral Sola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03313', 'size' => '600x600'],
['name' => 'Opera Neutral Sola', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03313', 'size' => '600x1200'],
['name' => 'Opera Neutral Sola Scoring', 'indent' => '', 'design' => 'Stone Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL03314', 'size' => 'Scoring 100x200 - 300x600'],
['name' => 'Clay Avorio', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21117', 'size' => '600x600'],
['name' => 'Clay Rosato', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21118', 'size' => '600x600'],
['name' => 'Clay  Zafferano', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'CL21119', 'size' => '600x600'],
['name' => 'Clay Blanco Subway', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21317', 'size' => '75x300'],
['name' => 'Clay Rosato Subway', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21318', 'size' => '75x300'],
['name' => 'Clay Zafferano Subway', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21319', 'size' => '75x300'],
['name' => 'Clay Avorio Subway', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21320', 'size' => '75x300'],
['name' => 'Clay Blanco', 'indent' => '', 'design' => 'Terracotta Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'matt', 'code' => 'SW21316', 'size' => '600x600'],
['name' => 'Cobblestone 682 Bushhammer', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15014', 'size' => '100x100 - Offset 320x320'],
['name' => 'Cobblestone 684 Flamed', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01028', 'size' => '90x90 - Offset 300x300'],
['name' => 'Cobblestone Black Slate', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15016', 'size' => '90x90 - Straight 300x300'],
['name' => 'Cobblestone Black Slate', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15020', 'size' => '90x90 - Offset 300x300'],
['name' => 'Cobblestone Gold Quartz', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15017', 'size' => '90x90 - Offset 300x300'],
['name' => 'Cobblestone Green Quartz', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15018', 'size' => '90x90 - Offset 300x300'],
['name' => 'Cobblestone Sunrise Quartz', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01088', 'size' => '90x90 - Offset 300x300'],
['name' => 'Cobblestone Sahara Sandstone', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS01087', 'size' => '90x90 - Offset 300x300'],
['name' => 'Cobblestone Porphyry Loose Cobble', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15019', 'size' => 'Loose Cobble 100x100x20-40mm'],
['name' => 'Cobblestone Fantasy Grey Flamed', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15023', 'size' => '90x90 - Offset 300x300'],
['name' => 'Cobblestone Tiger Skin Split Face', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS15024', 'size' => '90x90 - Offset 300x300'],
['name' => 'Quartz Cobble Armitage Quartz', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28083', 'size' => '90x90 - Offset 300x300'],
['name' => 'Quartz Cobble Wellington Gold Quartz', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28084', 'size' => '90x90 - Offset 300x300'],
['name' => 'Quartz Cobble New Quay Quartz', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28085', 'size' => '90x90 - Offset 300x300'],
['name' => 'Quartz Cobble Langdon Quartz', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28086', 'size' => '90x90 - Offset 300x300'],
['name' => 'Granite Cobble Diamond White', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28088', 'size' => '100x100 - Offset 320x320x20'],
['name' => 'Granite Cobble Sesame Grey', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28089', 'size' => '100x100 - Offset 320x320x20'],
['name' => 'Granite Cobble Raven Black', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28090', 'size' => '100x100 - Offset 320x320x20'],
['name' => 'Granite Cobble Raven Black', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28090', 'size' => '100x100 - Offset 320x320x30'],
['name' => 'Granite Cobble Ash Grey', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28091', 'size' => '100x100 - Offset 320x320x20'],
['name' => 'Granite Cobble Fantasy Grey', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28092', 'size' => '100x100 - Offset 320x320x20'],
['name' => 'Granite Cobble Honey Jasper', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28094', 'size' => '100x100 - Offset 320x320x20'],
['name' => 'Granite Cobble Dove Grey', 'indent' => '', 'design' => 'Cobblestone', 'material' => 'Natural Stone', 'application' => 'Floor Tile', 'variation' => '', 'finish' => 'textured', 'code' => 'NS28095', 'size' => '100x100 - Offset 320x320x20'],
['name' => 'Atlas Dark', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01334', 'size' => '600x1200'],
['name' => 'Atlas Dark', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL01335', 'size' => '600x600'],
['name' => 'Atlas Dark', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL01335', 'size' => '600x1200'],
['name' => 'Atlas White', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01336', 'size' => '600x1200'],
['name' => 'Atlas White', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL01337', 'size' => '600x1200'],
['name' => 'Anna Stone', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS01009', 'size' => '600x1200x18'],
['name' => 'Crema Marfil', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'NS01011', 'size' => '600x1200x18'],
['name' => 'Bianco Carrara', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'SL01381', 'size' => '600x1200'],
['name' => 'Bianco Carrara', 'indent' => '', 'design' => 'Marble Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'polished', 'code' => 'SL01393', 'size' => '600x1200'],
['name' => 'Big Terrazzo Light Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'TR01001', 'size' => '300x600'],
['name' => 'Big Terrazzo Light Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'TR01001', 'size' => '600x600'],
['name' => 'Big Terrazzo Mid Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'TR01002', 'size' => '300x600'],
['name' => 'Big Terrazzo Mid Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'TR01002', 'size' => '600x600'],
['name' => 'Big Terrazzo Dark Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'TR01003', 'size' => '300x600'],
['name' => 'Big Terrazzo Dark Grey', 'indent' => '', 'design' => 'Terrazzo Look Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'in&out', 'code' => 'TR01003', 'size' => '600x600'],
['name' => 'Briko Full White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19246', 'size' => '45x230'],
['name' => 'Briko Half White', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19247', 'size' => '45x230'],
['name' => 'Briko Savanah', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19248', 'size' => '45x230'],
['name' => 'Briko Greystone', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19249', 'size' => '45x230'],
['name' => 'Briko Clay', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19250', 'size' => '45x230'],
['name' => 'Briko Seagrass', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19251', 'size' => '45x230'],
['name' => 'Briko Lagoon', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19252', 'size' => '45x230'],
['name' => 'Briko Vanila', 'indent' => '', 'design' => 'Subway Tile', 'material' => 'Porcelain Tile', 'application' => 'Wall Tile', 'variation' => '', 'finish' => 'gloss', 'code' => 'SW19255', 'size' => '45x230'],
['name' => 'Calacatta Gold Square Mosaic', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07113', 'size' => 'Chip 48x48 - Sheet 305x305x10'],
['name' => 'Calacatta Gold Finger Mosaic', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07114', 'size' => 'Chip 15x98 - Sheet 305x305x10'],
['name' => 'Calacatta Gold Herringbone Mosaic', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07115', 'size' => 'Chip 25x95 - Sheet 304x430'],
['name' => 'Calacatta Gold Penny Round Mosaic', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07116', 'size' => 'Chip 19mm - Sheet 300x300x10'],
['name' => 'Calacatta Gold Hexagon Mosaic', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07117', 'size' => 'Chip 48mm - Sheet 300x300x10'],
['name' => 'Calacatta Gold Subway', 'indent' => '', 'design' => 'Marble', 'material' => 'Natural Stone', 'application' => 'Wall Tile / Floor Tile', 'variation' => '', 'finish' => 'honed', 'code' => 'MS07118', 'size' => '75×150'],
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
