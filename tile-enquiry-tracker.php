<?php
/**
 * Tile Enquiry Tracking and Statistics
 * 
 * Tracks enquiries sent from idea-basket / submit-tile page,
 * stores enquiry records and individual product selections,
 * and provides an admin dashboard for date-range statistical reports.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ST_ENQUIRY_TRACKER_DB_VER', '1.0' );

/**
 * Create custom database tables for tile enquiries
 */
function st_enquiry_tracker_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $table_enquiries = $wpdb->prefix . 'tile_enquiries';
    $table_items     = $wpdb->prefix . 'tile_enquiry_items';

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Table 1: Stores enquiry metadata and customer details (excluding content HTML)
    $sql_enquiries = "CREATE TABLE {$table_enquiries} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        submission_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        customer_name VARCHAR(255) NOT NULL DEFAULT '',
        customer_type VARCHAR(100) NOT NULL DEFAULT '',
        contact_no VARCHAR(100) NOT NULL DEFAULT '',
        company_name VARCHAR(255) DEFAULT '',
        customer_email VARCHAR(255) NOT NULL DEFAULT '',
        project_reference VARCHAR(255) DEFAULT '',
        need_sample VARCHAR(10) NOT NULL DEFAULT 'no',
        subscribe_newsletter VARCHAR(10) NOT NULL DEFAULT 'no',
        customer_address TEXT DEFAULT '',
        PRIMARY KEY  (id),
        KEY submission_time (submission_time),
        KEY customer_email (customer_email(191)),
        KEY need_sample (need_sample)
    ) {$charset_collate};";

    // Table 2: Stores product selections for each enquiry (FK: enquiry_id)
    $sql_items = "CREATE TABLE {$table_items} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        enquiry_id BIGINT(20) UNSIGNED NOT NULL,
        product_name VARCHAR(255) NOT NULL DEFAULT '',
        product_colour VARCHAR(100) NOT NULL DEFAULT '',
        product_finish VARCHAR(100) NOT NULL DEFAULT '',
        product_size VARCHAR(100) NOT NULL DEFAULT '',
        product_code VARCHAR(100) DEFAULT '',
        note TEXT DEFAULT '',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY enquiry_id (enquiry_id),
        KEY product_name (product_name(191)),
        KEY product_code (product_code),
        KEY created_at (created_at)
    ) {$charset_collate};";

    dbDelta( $sql_enquiries );
    dbDelta( $sql_items );
}

/**
 * Check and run DB migrations if needed
 */
function st_enquiry_tracker_check_db() {
    if ( get_option( 'st_enquiry_tracker_db_ver' ) !== ST_ENQUIRY_TRACKER_DB_VER ) {
        st_enquiry_tracker_create_tables();
        update_option( 'st_enquiry_tracker_db_ver', ST_ENQUIRY_TRACKER_DB_VER );
    }
}
add_action( 'admin_init', 'st_enquiry_tracker_check_db' );
add_action( 'after_switch_theme', 'st_enquiry_tracker_create_tables' );

/**
 * Record enquiry and its products into the database
 *
 * @param array $form_data The customer form fields.
 * @param array $items Array of product item objects.
 * @return int|false Inserted enquiry ID or false on failure.
 */
function st_record_tile_enquiry( $form_data, $items = array() ) {
    global $wpdb;

    // Ensure tables exist before inserting
    st_enquiry_tracker_check_db();

    $table_enquiries = $wpdb->prefix . 'tile_enquiries';
    $table_items     = $wpdb->prefix . 'tile_enquiry_items';

    $now = current_time( 'mysql' );

    // 1. Insert into Table 1 (tile_enquiries)
    $enquiry_data = array(
        'submission_time'      => $now,
        'customer_name'        => isset( $form_data['customer_name'] ) ? sanitize_text_field( $form_data['customer_name'] ) : '',
        'customer_type'        => isset( $form_data['customer_type'] ) ? sanitize_text_field( $form_data['customer_type'] ) : '',
        'contact_no'           => isset( $form_data['contact_no'] ) ? sanitize_text_field( $form_data['contact_no'] ) : '',
        'company_name'         => isset( $form_data['company_name'] ) ? sanitize_text_field( $form_data['company_name'] ) : '',
        'customer_email'       => isset( $form_data['customer_email'] ) ? sanitize_email( $form_data['customer_email'] ) : '',
        'project_reference'    => isset( $form_data['project_reference'] ) ? sanitize_text_field( $form_data['project_reference'] ) : '',
        'need_sample'          => ( isset( $form_data['need_sample'] ) && $form_data['need_sample'] === 'yes' ) ? 'yes' : 'no',
        'subscribe_newsletter' => ( isset( $form_data['subscribe_newsletter'] ) && $form_data['subscribe_newsletter'] === 'yes' ) ? 'yes' : 'no',
        'customer_address'     => isset( $form_data['customer_address'] ) ? sanitize_textarea_field( $form_data['customer_address'] ) : '',
    );

    $inserted = $wpdb->insert(
        $table_enquiries,
        $enquiry_data,
        array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
    );

    if ( ! $inserted ) {
        error_log( 'st_record_tile_enquiry error: failed to insert enquiry into ' . $table_enquiries . ': ' . $wpdb->last_error );
        return false;
    }

    $enquiry_id = $wpdb->insert_id;

    // 2. Fallback: If $items is empty but basket_content HTML is provided, parse products from HTML
    if ( empty( $items ) && ! empty( $form_data['basket_content'] ) ) {
        $items = st_enquiry_tracker_parse_items_from_html( $form_data['basket_content'] );
    }

    // 3. Insert each product into Table 2 (tile_enquiry_items)
    if ( ! empty( $items ) && is_array( $items ) ) {
        foreach ( $items as $item ) {
            $name   = isset( $item['name'] ) ? sanitize_text_field( $item['name'] ) : '';
            $colour = isset( $item['colour'] ) && ! empty( $item['colour'] ) ? sanitize_text_field( $item['colour'] ) : $name;
            $finish = isset( $item['finish'] ) ? sanitize_text_field( $item['finish'] ) : '';
            $size   = isset( $item['size'] ) ? sanitize_text_field( $item['size'] ) : '';
            $code   = isset( $item['code'] ) ? sanitize_text_field( $item['code'] ) : '';
            $note   = isset( $item['note'] ) ? sanitize_textarea_field( $item['note'] ) : '';

            // Clean up prefix labels if present (e.g. "Finish: Matt" -> "Matt")
            $finish = preg_replace( '/^Finish:\s*/i', '', $finish );
            $size   = preg_replace( '/^Size:\s*/i', '', $size );
            $code   = preg_replace( '/^Code:\s*/i', '', $code );

            $wpdb->insert(
                $table_items,
                array(
                    'enquiry_id'     => $enquiry_id,
                    'product_name'   => $name,
                    'product_colour' => $colour,
                    'product_finish' => $finish,
                    'product_size'   => $size,
                    'product_code'   => $code,
                    'note'           => $note,
                    'created_at'     => $now,
                ),
                array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
            );
        }
    }

    return $enquiry_id;
}

/**
 * Fallback parser to extract product details from basket_content HTML
 */
function st_enquiry_tracker_parse_items_from_html( $html ) {
    $items = array();
    if ( empty( $html ) ) {
        return $items;
    }

    // Extract each product table block
    preg_match_all( '/<table[^>]*>.*?<\/table>/is', $html, $tables );
    if ( empty( $tables[0] ) ) {
        return $items;
    }

    foreach ( $tables[0] as $table_html ) {
        $name = '';
        $finish = '';
        $size = '';
        $code = '';
        $note = '';

        if ( preg_match( '/<h3[^>]*>(.*?)<\/h3>/i', $table_html, $m ) ) {
            $name = trim( strip_tags( $m[1] ) );
        }
        if ( preg_match( '/Finish:<\/strong>\s*(.*?)</i', $table_html, $m ) ) {
            $finish = trim( strip_tags( $m[1] ) );
        }
        if ( preg_match( '/Size:<\/strong>\s*(.*?)</i', $table_html, $m ) ) {
            $size = trim( strip_tags( $m[1] ) );
        }
        if ( preg_match( '/Code:<\/strong>\s*(.*?)</i', $table_html, $m ) ) {
            $code = trim( strip_tags( $m[1] ) );
        }
        if ( preg_match( '/Note:<\/strong>.*?<p[^>]*>(.*?)<\/p>/is', $table_html, $m ) ) {
            $note = trim( strip_tags( $m[1] ) );
        }

        if ( ! empty( $name ) || ! empty( $code ) ) {
            $items[] = array(
                'name'   => $name,
                'colour' => $name,
                'finish' => $finish,
                'size'   => $size,
                'code'   => $code,
                'note'   => $note,
            );
        }
    }

    return $items;
}

/**
 * Register Admin Menu for Enquiry Statistics
 */
function st_enquiry_tracker_admin_menu() {
    add_menu_page(
        __( 'Tile Enquiries', 'enfold' ),
        __( 'Tile Enquiries', 'enfold' ),
        'manage_options',
        'tile-enquiries',
        'st_render_enquiry_stats_page',
        'dashicons-chart-pie',
        26
    );
}
add_action( 'admin_menu', 'st_enquiry_tracker_admin_menu' );

/**
 * Handle CSV Export
 */
function st_enquiry_tracker_handle_csv_export() {
    if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'tile-enquiries' ) {
        return;
    }
    if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'export_csv' ) {
        return;
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Permission denied' );
    }

    check_admin_referer( 'st_enquiry_export_nonce' );

    global $wpdb;
    $table_enquiries = $wpdb->prefix . 'tile_enquiries';
    $table_items     = $wpdb->prefix . 'tile_enquiry_items';

    $start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
    $end_date   = isset( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';

    $where_enq  = '1=1';
    $where_item = '1=1';
    if ( ! empty( $start_date ) ) {
        $where_enq  .= $wpdb->prepare( ' AND submission_time >= %s', $start_date . ' 00:00:00' );
        $where_item .= $wpdb->prepare( ' AND created_at >= %s', $start_date . ' 00:00:00' );
    }
    if ( ! empty( $end_date ) ) {
        $where_enq  .= $wpdb->prepare( ' AND submission_time <= %s', $end_date . ' 23:59:59' );
        $where_item .= $wpdb->prepare( ' AND created_at <= %s', $end_date . ' 23:59:59' );
    }

    $export_type = isset( $_GET['type'] ) && $_GET['type'] === 'products' ? 'products' : 'enquiries';

    $filename = 'tile-' . $export_type . '-' . ( $start_date ? $start_date : 'all' ) . '-to-' . ( $end_date ? $end_date : 'now' ) . '.csv';

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=' . $filename );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    $output = fopen( 'php://output', 'w' );
    // UTF-8 BOM for Excel compatibility
    fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) );

    if ( $export_type === 'products' ) {
        fputcsv( $output, array( 'Rank', 'Product Name', 'Colour', 'Finish', 'Size', 'Product Code', 'Total Requested' ) );
        $rows = $wpdb->get_results(
            "SELECT product_name, product_colour, product_finish, product_size, product_code, COUNT(id) AS total_count
             FROM {$table_items}
             WHERE {$where_item}
             GROUP BY product_name, product_colour, product_finish, product_size, product_code
             ORDER BY total_count DESC"
        );
        $rank = 1;
        foreach ( $rows as $r ) {
            fputcsv( $output, array(
                $rank++,
                $r->product_name,
                $r->product_colour,
                $r->product_finish,
                $r->product_size,
                $r->product_code,
                $r->total_count,
            ) );
        }
    } else {
        fputcsv( $output, array( 'ID', 'Date/Time', 'Customer Name', 'Role/Type', 'Phone', 'Email', 'Company', 'Project Ref', 'Need Sample', 'Newsletter', 'Address', 'Products Requested' ) );
        $enquiries = $wpdb->get_results( "SELECT * FROM {$table_enquiries} WHERE {$where_enq} ORDER BY submission_time DESC" );
        foreach ( $enquiries as $enq ) {
            $items_list = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_items} WHERE enquiry_id = %d", $enq->id ) );
            $products_str = array();
            foreach ( $items_list as $it ) {
                $p_str = $it->product_name;
                if ( $it->product_finish ) $p_str .= ' [' . $it->product_finish . ']';
                if ( $it->product_size )   $p_str .= ' (' . $it->product_size . ')';
                if ( $it->product_code )   $p_str .= ' #' . $it->product_code;
                if ( $it->note )           $p_str .= ' Note: ' . $it->note;
                $products_str[] = $p_str;
            }
            fputcsv( $output, array(
                $enq->id,
                $enq->submission_time,
                $enq->customer_name,
                $enq->customer_type,
                $enq->contact_no,
                $enq->customer_email,
                $enq->company_name,
                $enq->project_reference,
                strtoupper( $enq->need_sample ),
                strtoupper( $enq->subscribe_newsletter ),
                $enq->customer_address,
                implode( "\n", $products_str ),
            ) );
        }
    }

    fclose( $output );
    exit;
}
add_action( 'admin_init', 'st_enquiry_tracker_handle_csv_export' );

/**
 * Render Admin Statistics Page
 */
function st_render_enquiry_stats_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    global $wpdb;
    $table_enquiries = $wpdb->prefix . 'tile_enquiries';
    $table_items     = $wpdb->prefix . 'tile_enquiry_items';

    // Ensure tables exist
    st_enquiry_tracker_check_db();

    // Date range filter: default to last 30 days
    $range_type = isset( $_GET['range_type'] ) ? sanitize_text_field( $_GET['range_type'] ) : '30days';
    $start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
    $end_date   = isset( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';

    if ( empty( $start_date ) && empty( $end_date ) && $range_type !== 'all' ) {
        if ( $range_type === 'today' ) {
            $start_date = date( 'Y-m-d' );
            $end_date   = date( 'Y-m-d' );
        } elseif ( $range_type === '7days' ) {
            $start_date = date( 'Y-m-d', strtotime( '-6 days' ) );
            $end_date   = date( 'Y-m-d' );
        } elseif ( $range_type === 'month' ) {
            $start_date = date( 'Y-m-01' );
            $end_date   = date( 'Y-m-d' );
        } else {
            // Default 30 days
            $range_type = '30days';
            $start_date = date( 'Y-m-d', strtotime( '-29 days' ) );
            $end_date   = date( 'Y-m-d' );
        }
    }

    // Build SQL query conditions
    $where_enq  = '1=1';
    $where_item = '1=1';

    if ( ! empty( $start_date ) ) {
        $where_enq  .= $wpdb->prepare( ' AND submission_time >= %s', $start_date . ' 00:00:00' );
        $where_item .= $wpdb->prepare( ' AND created_at >= %s', $start_date . ' 00:00:00' );
    }
    if ( ! empty( $end_date ) ) {
        $where_enq  .= $wpdb->prepare( ' AND submission_time <= %s', $end_date . ' 23:59:59' );
        $where_item .= $wpdb->prepare( ' AND created_at <= %s', $end_date . ' 23:59:59' );
    }

    // 1. Metric: Total Emails / Enquiries
    $total_enquiries = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_enquiries} WHERE {$where_enq}" );

    // 2. Metric: Sample Requests
    $sample_requests = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_enquiries} WHERE {$where_enq} AND need_sample = 'yes'" );

    // 3. Metric: Total Tile Items requested
    $total_items = (int) $wpdb->get_var(
        "SELECT COUNT(i.id) FROM {$table_items} i 
         JOIN {$table_enquiries} e ON i.enquiry_id = e.id 
         WHERE {$where_enq}"
    );

    // 4. Metric: Unique Products
    $unique_products = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT CONCAT(i.product_name, '|', i.product_finish, '|', i.product_size)) 
         FROM {$table_items} i 
         JOIN {$table_enquiries} e ON i.enquiry_id = e.id 
         WHERE {$where_enq}"
    );

    // 5. Product Leaderboard Statistics (Product Name + Colour + Finish + Size)
    $product_stats = $wpdb->get_results(
        "SELECT i.product_name, i.product_colour, i.product_finish, i.product_size, i.product_code, COUNT(i.id) AS request_count 
         FROM {$table_items} i 
         JOIN {$table_enquiries} e ON i.enquiry_id = e.id 
         WHERE {$where_enq} 
         GROUP BY i.product_name, i.product_colour, i.product_finish, i.product_size, i.product_code 
         ORDER BY request_count DESC, i.product_name ASC"
    );

    // 6. Recent Enquiries List (Pagination: 20 per page)
    $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
    $per_page     = 20;
    $offset       = ( $current_page - 1 ) * $per_page;
    $total_pages  = ceil( $total_enquiries / $per_page );

    $enquiry_list = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table_enquiries} WHERE {$where_enq} ORDER BY submission_time DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        )
    );

    // Fetch items for the loaded enquiries
    $enquiry_ids = wp_list_pluck( $enquiry_list, 'id' );
    $items_by_enquiry = array();
    if ( ! empty( $enquiry_ids ) ) {
        $ids_placeholder = implode( ',', array_map( 'intval', $enquiry_ids ) );
        $all_items = $wpdb->get_results( "SELECT * FROM {$table_items} WHERE enquiry_id IN ({$ids_placeholder}) ORDER BY id ASC" );
        foreach ( $all_items as $item_obj ) {
            $items_by_enquiry[ $item_obj->enquiry_id ][] = $item_obj;
        }
    }

    $export_nonce = wp_create_nonce( 'st_enquiry_export_nonce' );
    $base_url = admin_url( 'admin.php?page=tile-enquiries' );
    ?>
    <div class="wrap st-enquiry-wrap">
        <h1 class="wp-heading-inline">
            <span class="dashicons dashicons-chart-pie" style="font-size:28px; width:28px; height:28px; vertical-align:middle; margin-right:8px;"></span>
            <?php esc_html_e( 'Tile Enquiry & Sample Statistics', 'enfold' ); ?>
        </h1>

        <!-- Range Selection Form -->
        <div class="st-filter-card" style="background:#fff; border:1px solid #ccd0d4; padding:15px 20px; border-radius:6px; margin:15px 0 20px; box-shadow:0 1px 3px rgba(0,0,0,.05);">
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display:flex; flex-wrap:wrap; align-items:center; gap:12px;">
                <input type="hidden" name="page" value="tile-enquiries">

                <div style="display:inline-flex; align-items:center; gap:6px;">
                    <label for="start_date" style="font-weight:600;">From:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr( $start_date ); ?>" class="regular-text" style="width:145px;">
                </div>

                <div style="display:inline-flex; align-items:center; gap:6px;">
                    <label for="end_date" style="font-weight:600;">To:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>" class="regular-text" style="width:145px;">
                </div>

                <button type="submit" class="button button-primary">Filter</button>

                <!-- Quick Presets -->
                <div class="st-quick-links" style="margin-left:10px; display:inline-flex; gap:6px;">
                    <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'tile-enquiries', 'range_type' => 'today', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d') ), admin_url('admin.php') ) ); ?>" class="button <?php echo $range_type === 'today' ? 'button-primary' : ''; ?>">Today</a>
                    <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'tile-enquiries', 'range_type' => '7days', 'start_date' => date('Y-m-d', strtotime('-6 days')), 'end_date' => date('Y-m-d') ), admin_url('admin.php') ) ); ?>" class="button <?php echo $range_type === '7days' ? 'button-primary' : ''; ?>">Last 7 Days</a>
                    <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'tile-enquiries', 'range_type' => '30days', 'start_date' => date('Y-m-d', strtotime('-29 days')), 'end_date' => date('Y-m-d') ), admin_url('admin.php') ) ); ?>" class="button <?php echo $range_type === '30days' ? 'button-primary' : ''; ?>">Last 30 Days</a>
                    <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'tile-enquiries', 'range_type' => 'month', 'start_date' => date('Y-m-01'), 'end_date' => date('Y-m-d') ), admin_url('admin.php') ) ); ?>" class="button <?php echo $range_type === 'month' ? 'button-primary' : ''; ?>">This Month</a>
                    <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'tile-enquiries', 'range_type' => 'all', 'start_date' => '', 'end_date' => '' ), admin_url('admin.php') ) ); ?>" class="button <?php echo $range_type === 'all' ? 'button-primary' : ''; ?>">All Time</a>
                </div>

                <!-- Export CSV buttons -->
                <div style="margin-left:auto; display:inline-flex; gap:8px;">
                    <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'tile-enquiries', 'action' => 'export_csv', 'type' => 'enquiries', 'start_date' => $start_date, 'end_date' => $end_date ), admin_url('admin.php') ), 'st_enquiry_export_nonce' ) ); ?>" class="button" title="Export Enquiries CSV">
                        <span class="dashicons dashicons-download" style="vertical-align:middle; line-height:1.3;"></span> Export Enquiries
                    </a>
                    <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'tile-enquiries', 'action' => 'export_csv', 'type' => 'products', 'start_date' => $start_date, 'end_date' => $end_date ), admin_url('admin.php') ), 'st_enquiry_export_nonce' ) ); ?>" class="button" title="Export Product Summary CSV">
                        <span class="dashicons dashicons-media-spreadsheet" style="vertical-align:middle; line-height:1.3;"></span> Export Products
                    </a>
                </div>
            </form>
        </div>

        <!-- Metric KPI Cards -->
        <div class="st-kpi-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin-bottom:25px;">
            <div style="background:#fff; border-left:4px solid #2271b1; padding:18px 20px; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.08);">
                <div style="font-size:13px; text-transform:uppercase; color:#646970; font-weight:600; margin-bottom:5px;">Total Enquiries Sent</div>
                <div style="font-size:28px; font-weight:700; color:#1d2327;"><?php echo number_format( $total_enquiries ); ?></div>
                <div style="font-size:12px; color:#8c8f94; margin-top:4px;">Emails submitted via Idea Basket</div>
            </div>

            <div style="background:#fff; border-left:4px solid #00a32a; padding:18px 20px; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.08);">
                <div style="font-size:13px; text-transform:uppercase; color:#646970; font-weight:600; margin-bottom:5px;">Sample Requests</div>
                <div style="font-size:28px; font-weight:700; color:#00a32a;"><?php echo number_format( $sample_requests ); ?></div>
                <div style="font-size:12px; color:#8c8f94; margin-top:4px;">
                    <?php echo $total_enquiries > 0 ? round(($sample_requests / $total_enquiries) * 100, 1) . '% of total enquiries' : '0%'; ?>
                </div>
            </div>

            <div style="background:#fff; border-left:4px solid #dba617; padding:18px 20px; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.08);">
                <div style="font-size:13px; text-transform:uppercase; color:#646970; font-weight:600; margin-bottom:5px;">Total Products Selected</div>
                <div style="font-size:28px; font-weight:700; color:#1d2327;"><?php echo number_format( $total_items ); ?></div>
                <div style="font-size:12px; color:#8c8f94; margin-top:4px;">
                    Avg. <?php echo $total_enquiries > 0 ? round($total_items / $total_enquiries, 1) : 0; ?> tiles per enquiry
                </div>
            </div>

            <div style="background:#fff; border-left:4px solid #72aee6; padding:18px 20px; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.08);">
                <div style="font-size:13px; text-transform:uppercase; color:#646970; font-weight:600; margin-bottom:5px;">Unique Tile Variants</div>
                <div style="font-size:28px; font-weight:700; color:#1d2327;"><?php echo number_format( $unique_products ); ?></div>
                <div style="font-size:12px; color:#8c8f94; margin-top:4px;">Distinct tile/finish/size combinations</div>
            </div>
        </div>

        <!-- Section 1: Product Leaderboard (Number of Each Product) -->
        <div class="postbox" style="margin-bottom:25px;">
            <div class="postbox-header" style="padding:12px 15px; border-bottom:1px solid #dcdcde;">
                <h2 class="hndle" style="font-size:16px; margin:0;">
                    <span class="dashicons dashicons-star-filled" style="color:#dba617; margin-right:4px;"></span>
                    <?php esc_html_e( 'Product Sample Frequency (Quantity per Product)', 'enfold' ); ?>
                    <span style="font-weight:normal; font-size:13px; color:#646970; margin-left:6px;">
                        (<?php echo count( $product_stats ); ?> products found in selected period)
                    </span>
                </h2>
            </div>
            <div class="inside" style="margin:0; padding:0;">
                <?php if ( empty( $product_stats ) ): ?>
                    <p style="padding:20px; text-align:center; color:#646970;">No product enquiries recorded for this date range.</p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped table-view-list" style="border:none;">
                        <thead>
                            <tr>
                                <th style="width:50px; text-align:center;">#</th>
                                <th>Product / Tile Name</th>
                                <th>Colour / Variety</th>
                                <th>Finish</th>
                                <th>Size</th>
                                <th>Code</th>
                                <th style="width:130px; text-align:center;">Requests</th>
                                <th style="width:200px;">Share (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            foreach ( $product_stats as $stat ): 
                                $percent = $total_items > 0 ? round( ( $stat->request_count / $total_items ) * 100, 1 ) : 0;
                            ?>
                            <tr>
                                <td style="text-align:center; font-weight:600; color:#646970;"><?php echo $rank++; ?></td>
                                <td><strong><?php echo esc_html( $stat->product_name ); ?></strong></td>
                                <td><?php echo esc_html( $stat->product_colour ? $stat->product_colour : '-' ); ?></td>
                                <td><?php echo esc_html( $stat->product_finish ? $stat->product_finish : '-' ); ?></td>
                                <td><?php echo esc_html( $stat->product_size ? $stat->product_size : '-' ); ?></td>
                                <td><code><?php echo esc_html( $stat->product_code ? $stat->product_code : '-' ); ?></code></td>
                                <td style="text-align:center;">
                                    <span style="display:inline-block; padding:3px 10px; background:#e7f5ea; color:#007017; font-weight:bold; border-radius:12px; font-size:13px;">
                                        <?php echo number_format( $stat->request_count ); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="flex:1; background:#e0e0e0; height:8px; border-radius:4px; overflow:hidden;">
                                            <div style="width:<?php echo esc_attr( $percent ); ?>%; background:#2271b1; height:100%;"></div>
                                        </div>
                                        <span style="font-size:12px; color:#646970; width:45px; text-align:right;"><?php echo $percent; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 2: Enquiry Submissions Log -->
        <div class="postbox">
            <div class="postbox-header" style="padding:12px 15px; border-bottom:1px solid #dcdcde;">
                <h2 class="hndle" style="font-size:16px; margin:0;">
                    <span class="dashicons dashicons-email-alt" style="margin-right:4px;"></span>
                    <?php esc_html_e( 'Enquiry Submissions Log', 'enfold' ); ?>
                    <span style="font-weight:normal; font-size:13px; color:#646970; margin-left:6px;">
                        (Showing <?php echo count( $enquiry_list ); ?> of <?php echo $total_enquiries; ?>)
                    </span>
                </h2>
            </div>
            <div class="inside" style="margin:0; padding:0;">
                <?php if ( empty( $enquiry_list ) ): ?>
                    <p style="padding:20px; text-align:center; color:#646970;">No enquiries found for this period.</p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped table-view-list" style="border:none;">
                        <thead>
                            <tr>
                                <th style="width:45px;">ID</th>
                                <th style="width:130px;">Date & Time</th>
                                <th>Customer Name</th>
                                <th>Role</th>
                                <th>Contact Info</th>
                                <th>Company / Project</th>
                                <th style="width:100px; text-align:center;">Samples</th>
                                <th style="width:80px; text-align:center;">Tiles</th>
                                <th style="width:110px; text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $enquiry_list as $enq ): 
                                $items = isset( $items_by_enquiry[ $enq->id ] ) ? $items_by_enquiry[ $enq->id ] : array();
                            ?>
                            <tr>
                                <td>#<?php echo esc_html( $enq->id ); ?></td>
                                <td><?php echo esc_html( date_i18n( 'Y-m-d H:i', strtotime( $enq->submission_time ) ) ); ?></td>
                                <td>
                                    <strong><?php echo esc_html( $enq->customer_name ); ?></strong>
                                    <?php if ( $enq->subscribe_newsletter === 'yes' ): ?>
                                        <span class="dashicons dashicons-yes-alt" title="Subscribed to Newsletter" style="color:#00a32a; font-size:16px; vertical-align:middle;"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="display:inline-block; padding:2px 8px; background:#f0f0f1; border-radius:3px; font-size:12px; text-transform:capitalize;">
                                        <?php echo esc_html( $enq->customer_type ? $enq->customer_type : '-' ); ?>
                                    </span>
                                </td>
                                <td>
                                    <div><a href="mailto:<?php echo esc_attr( $enq->customer_email ); ?>"><?php echo esc_html( $enq->customer_email ); ?></a></div>
                                    <div style="color:#646970; font-size:12px;"><?php echo esc_html( $enq->contact_no ); ?></div>
                                </td>
                                <td>
                                    <?php if ( $enq->company_name ): ?>
                                        <div><strong>Co:</strong> <?php echo esc_html( $enq->company_name ); ?></div>
                                    <?php endif; ?>
                                    <?php if ( $enq->project_reference ): ?>
                                        <div style="font-size:12px; color:#646970;"><strong>Ref:</strong> <?php echo esc_html( $enq->project_reference ); ?></div>
                                    <?php endif; ?>
                                    <?php if ( ! $enq->company_name && ! $enq->project_reference ): ?>
                                        <span style="color:#8c8f94;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center;">
                                    <?php if ( $enq->need_sample === 'yes' ): ?>
                                        <span style="display:inline-block; padding:2px 8px; background:#dcfce7; color:#15803d; font-weight:600; border-radius:3px; font-size:12px;">YES</span>
                                    <?php else: ?>
                                        <span style="color:#8c8f94; font-size:12px;">No</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center; font-weight:600;">
                                    <?php echo count( $items ); ?>
                                </td>
                                <td style="text-align:center;">
                                    <button type="button" class="button button-small st-toggle-details-btn" data-target="enquiry-details-<?php echo esc_attr( $enq->id ); ?>">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            <!-- Collapsible Detailed Row -->
                            <tr id="enquiry-details-<?php echo esc_attr( $enq->id ); ?>" style="display:none; background:#fafafa;">
                                <td colspan="9" style="padding:15px 20px; border-left:3px solid #2271b1;">
                                    <div style="display:grid; grid-template-columns:1fr 2fr; gap:20px;">
                                        <!-- Customer Information -->
                                        <div>
                                            <h4 style="margin:0 0 8px 0; color:#1d2327;">Customer & Shipping Address</h4>
                                            <p style="margin:0 0 4px; font-size:13px;"><strong>Name:</strong> <?php echo esc_html( $enq->customer_name ); ?></p>
                                            <p style="margin:0 0 4px; font-size:13px;"><strong>Email:</strong> <?php echo esc_html( $enq->customer_email ); ?></p>
                                            <p style="margin:0 0 4px; font-size:13px;"><strong>Phone:</strong> <?php echo esc_html( $enq->contact_no ); ?></p>
                                            <p style="margin:0 0 4px; font-size:13px;"><strong>Role:</strong> <?php echo esc_html( $enq->customer_type ); ?></p>
                                            <p style="margin:0 0 4px; font-size:13px;"><strong>Company:</strong> <?php echo esc_html( $enq->company_name ? $enq->company_name : 'Not provided' ); ?></p>
                                            <p style="margin:0 0 4px; font-size:13px;"><strong>Project Ref:</strong> <?php echo esc_html( $enq->project_reference ? $enq->project_reference : 'Not provided' ); ?></p>
                                            <p style="margin:0 0 4px; font-size:13px;"><strong>Address:</strong><br><span style="color:#50575e;"><?php echo nl2br( esc_html( $enq->customer_address ) ); ?></span></p>
                                        </div>

                                        <!-- Selected Products List -->
                                        <div>
                                            <h4 style="margin:0 0 8px 0; color:#1d2327;">Selected Tiles (<?php echo count( $items ); ?> items)</h4>
                                            <?php if ( empty( $items ) ): ?>
                                                <p style="color:#8c8f94; font-size:13px;">No item details recorded for this enquiry.</p>
                                            <?php else: ?>
                                                <table class="widefat striped" style="font-size:12px; margin-top:5px;">
                                                    <thead>
                                                        <tr>
                                                            <th>Tile Name</th>
                                                            <th>Finish</th>
                                                            <th>Size</th>
                                                            <th>Code</th>
                                                            <th>Customer Note</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ( $items as $it ): ?>
                                                        <tr>
                                                            <td><strong><?php echo esc_html( $it->product_name ); ?></strong></td>
                                                            <td><?php echo esc_html( $it->product_finish ? $it->product_finish : '-' ); ?></td>
                                                            <td><?php echo esc_html( $it->product_size ? $it->product_size : '-' ); ?></td>
                                                            <td><code><?php echo esc_html( $it->product_code ? $it->product_code : '-' ); ?></code></td>
                                                            <td style="color:#50575e; font-style:italic;"><?php echo esc_html( $it->note ? $it->note : '-' ); ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if ( $total_pages > 1 ): ?>
                        <div class="tablenav bottom" style="padding:10px 15px;">
                            <div class="tablenav-pages">
                                <span class="displaying-num"><?php echo sprintf( '%d enquiries', $total_enquiries ); ?></span>
                                <?php
                                echo paginate_links( array(
                                    'base'      => add_query_arg( 'paged', '%#%' ),
                                    'format'    => '',
                                    'prev_text' => '&laquo;',
                                    'next_text' => '&raquo;',
                                    'total'     => $total_pages,
                                    'current'   => $current_page,
                                ) );
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Toggle details script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.st-toggle-details-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var targetId = this.getAttribute('data-target');
                var targetRow = document.getElementById(targetId);
                if (targetRow) {
                    if (targetRow.style.display === 'none' || targetRow.style.display === '') {
                        targetRow.style.display = 'table-row';
                        this.textContent = 'Hide Details';
                    } else {
                        targetRow.style.display = 'none';
                        this.textContent = 'View Details';
                    }
                }
            });
        });
    });
    </script>
    <?php
}

