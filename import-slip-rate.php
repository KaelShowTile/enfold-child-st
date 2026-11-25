<?php
if ( ! defined( 'ABSPATH' ) ) {
    die();
}

/**
 * Template Name: Import Slip Rate
 *
 * Temporary admin-only page template to bulk import ACF tile_slip_rate
 * values from a CSV file into tile / private-tile posts.
 */

// Restrict access strictly to administrators
if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
    status_header( 403 );
    wp_die(
        '<h1>403 Forbidden</h1><p>Only site administrators can access this import tool.</p>',
        'Access Denied',
        array( 'response' => 403 )
    );
}

/**
 * Normalize text for reliable matching (entities decoded, all whitespace removed, UTF-8 lowercase).
 */
function st_slip_import_normalize_text( $value ) {
    if ( function_exists( 'st_normalize_match_text' ) ) {
        return st_normalize_match_text( $value );
    }
    $text = html_entity_decode( (string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $text = preg_replace( '/[\s\p{Z}]+/u', '', $text );
    return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
}

/**
 * Match a unique finish row index in tile_finish repeater.
 * Priority 1: product_code (if provided)
 * Priority 2: finish_name (only when unique; fails if multiple rows share the same finish_name)
 *
 * @param array  $tile_finish  ACF tile_finish repeater rows.
 * @param string $product_code Optional product_code from CSV.
 * @param string $finish_name  finish_name from CSV.
 * @param string $matched_by   Output: 'product_code' or 'finish_name'.
 * @return int|WP_Error 0-based row index or WP_Error.
 */
function st_slip_import_find_finish_index( $tile_finish, $product_code, $finish_name, &$matched_by = '' ) {
    $code_matches   = array();
    $finish_matches = array();
    $norm_code      = st_slip_import_normalize_text( $product_code );
    $norm_finish    = st_slip_import_normalize_text( $finish_name );

    foreach ( $tile_finish as $index => $row ) {
        $row_code = isset( $row['product_code'] ) ? $row['product_code'] : '';
        $raw_fin  = isset( $row['finish_name'] ) ? $row['finish_name'] : '';
        $row_fin  = ( is_array( $raw_fin ) && isset( $raw_fin['value'] ) ) ? $raw_fin['value'] : $raw_fin;

        if ( $norm_code !== '' && st_slip_import_normalize_text( $row_code ) === $norm_code ) {
            $code_matches[] = $index;
        }
        if ( $norm_finish !== '' && st_slip_import_normalize_text( $row_fin ) === $norm_finish ) {
            $finish_matches[] = $index;
        }
    }

    // Priority 1: Match by product_code
    if ( count( $code_matches ) === 1 ) {
        $matched_by = 'product_code';
        return $code_matches[0];
    }
    if ( count( $code_matches ) > 1 ) {
        return new WP_Error( 'duplicate_product_code', 'Multiple finishes share the same product_code in WordPress.' );
    }

    // Priority 2: Fallback to finish_name when unique
    if ( count( $finish_matches ) === 1 ) {
        $matched_by = 'finish_name';
        return $finish_matches[0];
    }
    if ( count( $finish_matches ) > 1 ) {
        return new WP_Error( 'ambiguous_finish_name', 'Multiple finishes share the same finish_name (no unique product_code match).' );
    }

    return new WP_Error( 'finish_not_found', 'No matching product_code or finish_name found in this post.' );
}

/**
 * Format existing tile_finish rows as a readable string for error diagnostics.
 */
function st_slip_import_describe_finishes( $tile_finish ) {
    if ( empty( $tile_finish ) || ! is_array( $tile_finish ) ) {
        return 'None';
    }
    $parts = array();
    foreach ( $tile_finish as $idx => $row ) {
        $raw_fin = isset( $row['finish_name'] ) ? $row['finish_name'] : '';
        $fin_val = ( is_array( $raw_fin ) && isset( $raw_fin['value'] ) ) ? $raw_fin['value'] : $raw_fin;
        $code    = isset( $row['product_code'] ) && $row['product_code'] !== '' ? $row['product_code'] : '-';
        $parts[] = sprintf( '#%d: %s (code: %s)', $idx + 1, $fin_val, $code );
    }
    return implode( ' | ', $parts );
}

/**
 * Process an uploaded CSV file and update ACF tile_slip_rate values.
 *
 * @param string $csv_path            Path to uploaded CSV file.
 * @param bool   $dry_run             If true, do not modify the database.
 * @param bool   $overwrite_existing  If false, skip rows that already have a non-empty tile_slip_rate.
 * @return array|WP_Error Report summary and row-level logs.
 */
function st_slip_import_process_csv( $csv_path, $dry_run = true, $overwrite_existing = true ) {
    if ( ! file_exists( $csv_path ) || ! is_readable( $csv_path ) ) {
        return new WP_Error( 'file_unreadable', 'Uploaded CSV file could not be read.' );
    }

    @set_time_limit( 300 );

    $handle = fopen( $csv_path, 'r' );
    if ( ! $handle ) {
        return new WP_Error( 'file_open_failed', 'Failed to open CSV file.' );
    }

    // Read header row
    $header = fgetcsv( $handle );
    if ( ! $header || ! is_array( $header ) ) {
        fclose( $handle );
        return new WP_Error( 'empty_csv', 'CSV file is empty or invalid.' );
    }

    // Remove UTF-8 BOM from first header cell if present
    $header[0] = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $header[0] );

    // Map normalized column names to column indices
    $col_map = array();
    foreach ( $header as $idx => $col_name ) {
        $key = strtolower( trim( (string) $col_name ) );
        $col_map[ $key ] = $idx;
    }

    // Resolve required and optional columns (supporting common aliases)
    $col_post_id      = isset( $col_map['post_id'] ) ? $col_map['post_id'] : ( isset( $col_map['product_id'] ) ? $col_map['product_id'] : null );
    $col_finish_name  = isset( $col_map['finish_name'] ) ? $col_map['finish_name'] : ( isset( $col_map['finish'] ) ? $col_map['finish'] : null );
    $col_slip_rating  = isset( $col_map['slip_rating'] ) ? $col_map['slip_rating'] : ( isset( $col_map['tile_slip_rate'] ) ? $col_map['tile_slip_rate'] : ( isset( $col_map['slip_rate'] ) ? $col_map['slip_rate'] : null ) );
    $col_product_code = isset( $col_map['product_code'] ) ? $col_map['product_code'] : ( isset( $col_map['code'] ) ? $col_map['code'] : null );

    $missing_cols = array();
    if ( $col_post_id === null ) {
        $missing_cols[] = 'post_id';
    }
    if ( $col_finish_name === null && $col_product_code === null ) {
        $missing_cols[] = 'finish_name';
    }
    if ( $col_slip_rating === null ) {
        $missing_cols[] = 'slip_rating';
    }

    if ( ! empty( $missing_cols ) ) {
        fclose( $handle );
        return new WP_Error(
            'missing_columns',
            'CSV header is missing required column(s): ' . implode( ', ', $missing_cols ) . '. Found headers: ' . implode( ', ', $header )
        );
    }

    $report = array(
        'dry_run'          => $dry_run,
        'has_product_code' => ( $col_product_code !== null ),
        'total_rows'       => 0,
        'updated_count'    => 0,
        'skipped_count'    => 0,
        'failed_count'     => 0,
        'updated_rows'     => array(),
        'skipped_rows'     => array(),
        'failed_rows'      => array(),
    );

    $allowed_post_types = array( 'tile', 'private-tile' );
    $post_cache         = array();
    $finish_cache       = array();
    $line_number        = 1; // Header is line 1

    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
        $line_number++;

        // Skip completely empty lines
        if ( $row === array( null ) || count( array_filter( $row, function ( $v ) { return trim( (string) $v ) !== ''; } ) ) === 0 ) {
            continue;
        }

        $report['total_rows']++;

        $raw_post_id  = isset( $row[ $col_post_id ] ) ? trim( (string) $row[ $col_post_id ] ) : '';
        $post_id      = intval( $raw_post_id );
        $finish_name  = ( $col_finish_name !== null && isset( $row[ $col_finish_name ] ) ) ? trim( sanitize_text_field( $row[ $col_finish_name ] ) ) : '';
        $product_code = ( $col_product_code !== null && isset( $row[ $col_product_code ] ) ) ? trim( sanitize_text_field( $row[ $col_product_code ] ) ) : '';
        $slip_rating  = isset( $row[ $col_slip_rating ] ) ? trim( sanitize_text_field( $row[ $col_slip_rating ] ) ) : '';

        $base_entry = array(
            'line'         => $line_number,
            'post_id'      => $post_id,
            'product_code' => $product_code,
            'finish_name'  => $finish_name,
            'slip_rating'  => $slip_rating,
        );

        // Skip rows with empty slip_rating
        if ( $slip_rating === '' ) {
            $report['skipped_count']++;
            $report['skipped_rows'][] = array_merge( $base_entry, array(
                'reason' => 'Empty slip_rating value in CSV.',
            ) );
            continue;
        }

        // Validate required row values
        if ( $post_id <= 0 || ( $product_code === '' && $finish_name === '' ) ) {
            $report['failed_count']++;
            $report['failed_rows'][] = array_merge( $base_entry, array(
                'reason'             => 'Missing valid post_id or finish_name/product_code.',
                'available_finishes' => '-',
            ) );
            continue;
        }

        // Load and cache post object
        if ( ! array_key_exists( $post_id, $post_cache ) ) {
            $post_cache[ $post_id ] = get_post( $post_id );
        }
        $post = $post_cache[ $post_id ];

        if ( ! $post ) {
            $report['failed_count']++;
            $report['failed_rows'][] = array_merge( $base_entry, array(
                'reason'             => 'Post ID does not exist in WordPress.',
                'available_finishes' => '-',
            ) );
            continue;
        }

        if ( ! in_array( $post->post_type, $allowed_post_types, true ) ) {
            $report['failed_count']++;
            $report['failed_rows'][] = array_merge( $base_entry, array(
                'reason'             => sprintf( 'Post type is "%s" (expected tile or private-tile).', $post->post_type ),
                'available_finishes' => '-',
            ) );
            continue;
        }

        // Load and cache tile_finish repeater for this post
        if ( ! array_key_exists( $post_id, $finish_cache ) ) {
            $finish_cache[ $post_id ] = get_field( 'tile_finish', $post_id );
        }
        $tile_finish = $finish_cache[ $post_id ];

        if ( empty( $tile_finish ) || ! is_array( $tile_finish ) ) {
            $report['failed_count']++;
            $report['failed_rows'][] = array_merge( $base_entry, array(
                'reason'             => 'No tile_finish repeater rows found in this post.',
                'available_finishes' => 'None',
            ) );
            continue;
        }

        // Match target finish row
        $matched_by    = '';
        $matched_index = st_slip_import_find_finish_index( $tile_finish, $product_code, $finish_name, $matched_by );

        if ( is_wp_error( $matched_index ) ) {
            $report['failed_count']++;
            $report['failed_rows'][] = array_merge( $base_entry, array(
                'reason'             => $matched_index->get_error_message(),
                'available_finishes' => st_slip_import_describe_finishes( $tile_finish ),
            ) );
            continue;
        }

        $matched_row   = $tile_finish[ $matched_index ];
        $existing_rate = isset( $matched_row['tile_slip_rate'] ) ? trim( (string) $matched_row['tile_slip_rate'] ) : '';

        // Skip if existing value is present and overwrite is disabled
        if ( ! $overwrite_existing && $existing_rate !== '' ) {
            $report['skipped_count']++;
            $report['skipped_rows'][] = array_merge( $base_entry, array(
                'reason' => sprintf( 'Already has slip rate "%s" (overwrite disabled).', $existing_rate ),
            ) );
            continue;
        }

        // Skip if already identical
        if ( $existing_rate === $slip_rating ) {
            $report['skipped_count']++;
            $report['skipped_rows'][] = array_merge( $base_entry, array(
                'reason' => sprintf( 'Already set to "%s" (no change needed).', $existing_rate ),
            ) );
            continue;
        }

        // Perform database write unless Dry Run is enabled
        if ( ! $dry_run ) {
            // Update only the tile_slip_rate sub-field (1-based row index in ACF) without rewriting nested sizes/images
            $acf_updated = false;
            if ( function_exists( 'update_sub_field' ) ) {
                $acf_updated = update_sub_field(
                    array( 'field_68d3799c30127', $matched_index + 1, 'field_6b060c0c27bf1' ),
                    $slip_rating,
                    $post_id
                );
            }

            if ( ! $acf_updated ) {
                $meta_key     = 'tile_finish_' . $matched_index . '_tile_slip_rate';
                $meta_ref_key = '_' . $meta_key;
                update_post_meta( $post_id, $meta_key, $slip_rating );
                update_post_meta( $post_id, $meta_ref_key, 'field_6b060c0c27bf1' );
            }

            if ( function_exists( 'acf_flush_value_cache' ) ) {
                acf_flush_value_cache( $post_id, 'tile_finish' );
            }

            // Keep in-memory cache in sync in case the CSV updates another finish on the same post
            $finish_cache[ $post_id ][ $matched_index ]['tile_slip_rate'] = $slip_rating;
        }

        $raw_matched_fin = isset( $matched_row['finish_name'] ) ? $matched_row['finish_name'] : '';
        $matched_fin_val = ( is_array( $raw_matched_fin ) && isset( $raw_matched_fin['value'] ) ) ? $raw_matched_fin['value'] : $raw_matched_fin;

        $report['updated_count']++;
        $report['updated_rows'][] = array_merge( $base_entry, array(
            'post_title'           => $post->post_title,
            'matched_by'           => $matched_by,
            'finish_row_num'       => $matched_index + 1,
            'matched_finish_name'  => $matched_fin_val,
            'matched_product_code' => isset( $matched_row['product_code'] ) ? $matched_row['product_code'] : '',
            'old_value'            => $existing_rate,
            'new_value'            => $slip_rating,
        ) );
    }

    fclose( $handle );
    return $report;
}

// Handle form submission
$import_error  = '';
$import_report = null;

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['st_import_slip_rate_submit'] ) ) {
    check_admin_referer( 'st_import_slip_rate_action', 'st_import_slip_rate_nonce' );

    $dry_run            = isset( $_POST['dry_run'] ) && $_POST['dry_run'] === '1';
    $overwrite_existing = isset( $_POST['overwrite_existing'] ) && $_POST['overwrite_existing'] === '1';

    if ( empty( $_FILES['slip_rate_csv'] ) || $_FILES['slip_rate_csv']['error'] !== UPLOAD_ERR_OK ) {
        $import_error = 'Please upload a valid CSV file.';
    } else {
        $file_name = sanitize_file_name( $_FILES['slip_rate_csv']['name'] );
        $ext       = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );

        if ( $ext !== 'csv' ) {
            $import_error = 'Invalid file type. Only .csv files are accepted.';
        } else {
            $result = st_slip_import_process_csv( $_FILES['slip_rate_csv']['tmp_name'], $dry_run, $overwrite_existing );
            if ( is_wp_error( $result ) ) {
                $import_error = $result->get_error_message();
            } else {
                $import_report = $result;
            }
        }
    }
}

get_header();
?>

<style>
    .st-slip-import-wrapper {
        max-width: 1180px;
        margin: 40px auto 70px;
        padding: 0 20px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1d2327;
    }
    .st-slip-card {
        background: #fff;
        border: 1px solid #dcdcde;
        border-radius: 8px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }
    .st-slip-card h1 {
        margin: 0 0 8px;
        font-size: 26px;
        line-height: 1.3;
    }
    .st-slip-card h2 {
        margin: 0 0 14px;
        font-size: 18px;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }
    .st-slip-subtitle {
        color: #50575e;
        margin: 0 0 20px;
        font-size: 14px;
    }
    .st-slip-notice {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    .st-slip-notice-error {
        background: #fcf0f1;
        border-left: 4px solid #d63638;
        color: #8a1f11;
    }
    .st-slip-notice-info {
        background: #f0f6fc;
        border-left: 4px solid #2271b1;
        color: #1d2327;
    }
    .st-slip-notice-success {
        background: #edfaef;
        border-left: 4px solid #00a32a;
        color: #005c17;
    }
    .st-slip-notice-warning {
        background: #fcf9e8;
        border-left: 4px solid #dba617;
        color: #6b4e00;
    }
    .st-slip-form-row {
        margin-bottom: 16px;
    }
    .st-slip-form-row label.st-label-block {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 14px;
    }
    .st-slip-checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 16px 0 22px;
        padding: 14px 16px;
        background: #f6f7f7;
        border-radius: 6px;
    }
    .st-slip-checkbox-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        cursor: pointer;
        margin: 0;
    }
    .st-slip-btn {
        display: inline-block;
        padding: 10px 22px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 5px;
        border: 1px solid #2271b1;
        background: #2271b1;
        color: #fff !important;
        cursor: pointer;
        text-decoration: none;
    }
    .st-slip-btn:hover {
        background: #135e96;
        border-color: #135e96;
    }
    .st-slip-btn-secondary {
        background: #f6f7f7;
        color: #2271b1 !important;
        border-color: #2271b1;
        padding: 7px 14px;
        font-size: 13px;
    }
    .st-slip-btn-secondary:hover {
        background: #f0f6fc;
    }
    .st-slip-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }
    .st-slip-stat-box {
        background: #fff;
        border: 1px solid #dcdcde;
        border-left: 4px solid #2271b1;
        border-radius: 6px;
        padding: 14px 18px;
    }
    .st-slip-stat-box.is-updated { border-left-color: #00a32a; }
    .st-slip-stat-box.is-skipped { border-left-color: #dba617; }
    .st-slip-stat-box.is-failed  { border-left-color: #d63638; }
    .st-slip-stat-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #646970;
        font-weight: 600;
    }
    .st-slip-stat-num {
        font-size: 28px;
        font-weight: 700;
        margin-top: 4px;
    }
    .st-slip-table-wrap {
        overflow-x: auto;
    }
    .st-slip-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .st-slip-table th,
    .st-slip-table td {
        border: 1px solid #e2e4e7;
        padding: 8px 10px;
        text-align: left;
        vertical-align: top;
    }
    .st-slip-table th {
        background: #f6f7f7;
        font-weight: 600;
        white-space: nowrap;
    }
    .st-slip-table tr:nth-child(even) td {
        background: #fafafa;
    }
    .st-badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
    }
    .st-badge-code   { background: #e7f5ea; color: #007017; }
    .st-badge-finish { background: #e8f0fe; color: #1967d2; }
    .st-code-block {
        background: #f6f7f7;
        border: 1px solid #dcdcde;
        padding: 10px 14px;
        border-radius: 5px;
        font-family: Consolas, Monaco, monospace;
        font-size: 13px;
        overflow-x: auto;
        white-space: pre;
    }
</style>

<div class="container_wrap container_wrap_first main_color">
    <div class="st-slip-import-wrapper">
        <div class="st-slip-card">
            <h1>Import Tile Slip Rate (CSV)</h1>
            <p class="st-slip-subtitle">
                Admin-only tool for importing <code>tile_slip_rate</code> (<code>field_6b060c0c27bf1</code>) into the ACF <code>tile_finish</code> repeater on <code>tile</code> / <code>private-tile</code> posts.
            </p>

            <?php if ( $import_error !== '' ) : ?>
                <div class="st-slip-notice st-slip-notice-error">
                    <strong>Import Error:</strong> <?php echo esc_html( $import_error ); ?>
                </div>
            <?php endif; ?>

            <div class="st-slip-notice st-slip-notice-info">
                <strong>CSV Format Requirements:</strong>
                <ul style="margin:6px 0 8px 20px; list-style:disc;">
                    <li>Required columns: <code>post_id</code>, <code>finish_name</code>, <code>slip_rating</code></li>
                    <li>Optional column: <code>product_code</code> — if provided, matches <code>product_code</code> first; otherwise falls back to <code>finish_name</code> (fails if multiple rows share the same <code>finish_name</code>).</li>
                    <li>Matching ignores case and whitespace automatically. Empty <code>slip_rating</code> rows are skipped.</li>
                </ul>
                <div class="st-code-block">post_id,product_code,finish_name,slip_rating
2005,ST00025,matt,P3
2005,ST00026,external,P5
392,,honed,P2</div>
            </div>

            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'st_import_slip_rate_action', 'st_import_slip_rate_nonce' ); ?>

                <div class="st-slip-form-row">
                    <label class="st-label-block" for="slip_rate_csv">Select CSV File (.csv)</label>
                    <input type="file" id="slip_rate_csv" name="slip_rate_csv" accept=".csv,text/csv" required>
                </div>

                <div class="st-slip-checkbox-group">
                    <label>
                        <input type="checkbox" name="dry_run" value="1" <?php echo ( ! isset( $_POST['st_import_slip_rate_submit'] ) || ! empty( $_POST['dry_run'] ) ) ? 'checked' : ''; ?>>
                        <strong>Dry Run (Preview Only)</strong> — Validate matches and preview changes without modifying the database.
                    </label>
                    <label>
                        <input type="checkbox" name="overwrite_existing" value="1" <?php echo ( ! isset( $_POST['st_import_slip_rate_submit'] ) || ! empty( $_POST['overwrite_existing'] ) ) ? 'checked' : ''; ?>>
                        <strong>Overwrite Existing Values</strong> — Update <code>tile_slip_rate</code> even if it already has a value. (Uncheck to only fill blank ones.)
                    </label>
                </div>

                <button type="submit" name="st_import_slip_rate_submit" value="1" class="st-slip-btn">
                    Upload &amp; Process CSV
                </button>
            </form>
        </div>

        <?php if ( $import_report !== null ) : ?>
            <?php if ( $import_report['dry_run'] ) : ?>
                <div class="st-slip-notice st-slip-notice-warning">
                    <strong>DRY RUN MODE:</strong> No database changes were saved. Review the results below, then uncheck <em>Dry Run</em> and re-upload to apply the import.
                </div>
            <?php else : ?>
                <div class="st-slip-notice st-slip-notice-success">
                    <strong>IMPORT COMPLETED:</strong> <?php echo intval( $import_report['updated_count'] ); ?> finish row(s) have been updated in the WordPress database.
                </div>
            <?php endif; ?>

            <div class="st-slip-stats">
                <div class="st-slip-stat-box">
                    <div class="st-slip-stat-label">Total CSV Rows</div>
                    <div class="st-slip-stat-num"><?php echo intval( $import_report['total_rows'] ); ?></div>
                </div>
                <div class="st-slip-stat-box is-updated">
                    <div class="st-slip-stat-label"><?php echo $import_report['dry_run'] ? 'Will Update' : 'Updated'; ?></div>
                    <div class="st-slip-stat-num"><?php echo intval( $import_report['updated_count'] ); ?></div>
                </div>
                <div class="st-slip-stat-box is-skipped">
                    <div class="st-slip-stat-label">Skipped</div>
                    <div class="st-slip-stat-num"><?php echo intval( $import_report['skipped_count'] ); ?></div>
                </div>
                <div class="st-slip-stat-box is-failed">
                    <div class="st-slip-stat-label">Failed / Ambiguous</div>
                    <div class="st-slip-stat-num"><?php echo intval( $import_report['failed_count'] ); ?></div>
                </div>
            </div>

            <?php if ( ! empty( $import_report['failed_rows'] ) ) : ?>
                <div class="st-slip-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid #eee; padding-bottom:8px;">
                        <h2 style="margin:0; border:none; padding:0; color:#d63638;">
                            Failed Rows (<?php echo intval( $import_report['failed_count'] ); ?>)
                        </h2>
                        <button type="button" id="st-download-failed-csv" class="st-slip-btn st-slip-btn-secondary">
                            Download Failed Rows (CSV)
                        </button>
                    </div>
                    <div class="st-slip-table-wrap">
                        <table class="st-slip-table">
                            <thead>
                                <tr>
                                    <th>CSV Line</th>
                                    <th>Post ID</th>
                                    <th>Product Code</th>
                                    <th>Finish Name</th>
                                    <th>Slip Rating</th>
                                    <th>Failure Reason</th>
                                    <th>Existing Finishes in WP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $import_report['failed_rows'] as $row ) : ?>
                                    <tr>
                                        <td><?php echo intval( $row['line'] ); ?></td>
                                        <td><?php echo intval( $row['post_id'] ); ?></td>
                                        <td><code><?php echo esc_html( $row['product_code'] !== '' ? $row['product_code'] : '-' ); ?></code></td>
                                        <td><?php echo esc_html( $row['finish_name'] !== '' ? $row['finish_name'] : '-' ); ?></td>
                                        <td><?php echo esc_html( $row['slip_rating'] ); ?></td>
                                        <td style="color:#8a1f11;"><?php echo esc_html( $row['reason'] ); ?></td>
                                        <td style="font-size:12px; color:#50575e;"><?php echo esc_html( $row['available_finishes'] ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                (function() {
                    var btn = document.getElementById('st-download-failed-csv');
                    if (!btn) return;
                    var failedRows = <?php echo wp_json_encode( $import_report['failed_rows'] ); ?>;
                    btn.addEventListener('click', function() {
                        var headers = ['csv_line', 'post_id', 'product_code', 'finish_name', 'slip_rating', 'reason', 'existing_finishes'];
                        var csvEscape = function(val) {
                            var str = String(val === null || val === undefined ? '' : val);
                            return '"' + str.replace(/"/g, '""') + '"';
                        };
                        var lines = [headers.join(',')];
                        failedRows.forEach(function(r) {
                            lines.push([
                                csvEscape(r.line),
                                csvEscape(r.post_id),
                                csvEscape(r.product_code),
                                csvEscape(r.finish_name),
                                csvEscape(r.slip_rating),
                                csvEscape(r.reason),
                                csvEscape(r.available_finishes)
                            ].join(','));
                        });
                        var blob = new Blob(['\uFEFF' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'slip-rate-import-failed.csv';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    });
                })();
                </script>
            <?php endif; ?>

            <?php if ( ! empty( $import_report['skipped_rows'] ) ) : ?>
                <div class="st-slip-card">
                    <h2>Skipped Rows (<?php echo intval( $import_report['skipped_count'] ); ?>)</h2>
                    <div class="st-slip-table-wrap">
                        <table class="st-slip-table">
                            <thead>
                                <tr>
                                    <th>CSV Line</th>
                                    <th>Post ID</th>
                                    <th>Product Code</th>
                                    <th>Finish Name</th>
                                    <th>Slip Rating</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $import_report['skipped_rows'] as $row ) : ?>
                                    <tr>
                                        <td><?php echo intval( $row['line'] ); ?></td>
                                        <td><?php echo intval( $row['post_id'] ); ?></td>
                                        <td><code><?php echo esc_html( $row['product_code'] !== '' ? $row['product_code'] : '-' ); ?></code></td>
                                        <td><?php echo esc_html( $row['finish_name'] !== '' ? $row['finish_name'] : '-' ); ?></td>
                                        <td><?php echo esc_html( $row['slip_rating'] !== '' ? $row['slip_rating'] : '(empty)' ); ?></td>
                                        <td><?php echo esc_html( $row['reason'] ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $import_report['updated_rows'] ) ) : ?>
                <div class="st-slip-card">
                    <h2><?php echo $import_report['dry_run'] ? 'Preview of Changes' : 'Updated Rows'; ?> (<?php echo intval( $import_report['updated_count'] ); ?>)</h2>
                    <div class="st-slip-table-wrap">
                        <table class="st-slip-table">
                            <thead>
                                <tr>
                                    <th>CSV Line</th>
                                    <th>Post ID &amp; Title</th>
                                    <th>Matched Finish Row</th>
                                    <th>Matched By</th>
                                    <th>Old Slip Rate</th>
                                    <th>New Slip Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $import_report['updated_rows'] as $row ) : ?>
                                    <tr>
                                        <td><?php echo intval( $row['line'] ); ?></td>
                                        <td>
                                            <strong>#<?php echo intval( $row['post_id'] ); ?></strong>
                                            <?php echo esc_html( $row['post_title'] ); ?>
                                        </td>
                                        <td>
                                            Row #<?php echo intval( $row['finish_row_num'] ); ?>:
                                            <?php echo esc_html( $row['matched_finish_name'] ); ?>
                                            <?php if ( $row['matched_product_code'] !== '' ) : ?>
                                                (<code><?php echo esc_html( $row['matched_product_code'] ); ?></code>)
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="st-badge <?php echo $row['matched_by'] === 'product_code' ? 'st-badge-code' : 'st-badge-finish'; ?>">
                                                <?php echo esc_html( $row['matched_by'] ); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc_html( $row['old_value'] !== '' ? $row['old_value'] : '(empty)' ); ?></td>
                                        <td><strong><?php echo esc_html( $row['new_value'] ); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
