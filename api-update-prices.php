<?php
/**
 * Custom REST API Endpoint to update tile prices
 */

// Register custom REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('st/v1', '/update-prices', array(
        'methods' => 'POST',
        'callback' => 'st_update_prices_callback',
        'permission_callback' => 'st_update_prices_permissions_check',
    ));
});

// Permission check (Highly recommended to uncomment and use an API key in production)
function st_update_prices_permissions_check($request)
{

    $api_key = $request->get_header('x-api-key');
    if ($api_key !== '9Ei02q2CEi08u') {
        return new WP_Error('rest_forbidden', esc_html__('Invalid API Key.'), array('status' => 401));
    }
    return true;
}

// Callback function to handle the API request
function st_update_prices_callback($request)
{
    $parameters = $request->get_json_params();

    if (!empty($parameters['updates']) && is_array($parameters['updates'])) {
        $updates = $parameters['updates'];
    } elseif (!empty($parameters['product_id'])) {
        $updates = array($parameters);
    } elseif (is_array($parameters) && isset($parameters[0]['product_id'])) {
        $updates = $parameters;
    } else {
        st_write_price_update_failure_log(array(
            array(
                'product_id' => 0,
                'reason' => 'invalid_request_body',
                'message' => 'No updates array found in request body.',
                'api_input' => $parameters,
            ),
        ), 0, 0);
        return new WP_Error('invalid_data', 'No updates array found in request body.', array('status' => 400));
    }

    $results = array();
    $failed_logs = array();
    $success_count = 0;

    foreach ($updates as $update) {
        $product_id = isset($update['product_id']) ? intval($update['product_id']) : 0;
        $product_code = isset($update['product_code']) ? trim(sanitize_text_field($update['product_code'])) : '';
        $finish = isset($update['finish']) ? trim(sanitize_text_field($update['finish'])) : '';
        $size = isset($update['size']) ? trim(sanitize_text_field($update['size'])) : '';
        $price = isset($update['price']) ? sanitize_text_field($update['price']) : '';
        $suffix = isset($update['suffix']) ? sanitize_text_field($update['suffix']) : '';

        $api_input = array(
            'product_id' => $product_id,
            'product_code' => $product_code,
            'finish' => $finish,
            'size' => $size,
            'price' => $price,
            'suffix' => $suffix,
        );
        $api_normalized = array(
            'product_code' => st_normalize_match_text($product_code),
            'finish' => st_normalize_match_text($finish),
            'size' => st_normalize_tile_size($size),
        );

        // Checking if necessary data is present
        if (!$product_id || ($product_code === '' && $finish === '') || $size === '' || $price === '') {
            $missing_fields = array();
            if (!$product_id) {
                $missing_fields[] = 'product_id';
            }
            if ($product_code === '' && $finish === '') {
                $missing_fields[] = 'product_code_or_finish';
            }
            if ($size === '') {
                $missing_fields[] = 'size';
            }
            if ($price === '') {
                $missing_fields[] = 'price';
            }

            $error_message = 'Missing required fields (' . implode(', ', $missing_fields) . ').';
            $results[] = array(
                'product_id' => $product_id,
                'product_code' => $product_code,
                'finish' => $finish,
                'size' => $size,
                'status' => 'error',
                'message' => $error_message
            );
            $failed_logs[] = array(
                'product_id' => $product_id,
                'reason' => 'missing_required_fields',
                'message' => $error_message,
                'api_input' => $api_input,
                'api_normalized' => $api_normalized,
                'db_context' => array('missing_fields' => $missing_fields),
            );
            continue;
        }

        // Get the entire repeater field data
        $tile_finish = get_field('tile_finish', $product_id);
        $updated = false;
        $error_reason = 'unknown_error';
        $error_message = 'Matching finish or size not found in this product.';
        $db_context = array();

        if ($tile_finish && is_array($tile_finish)) {
            $matched_by = '';
            $matched_finish_index = st_find_matching_finish_index($tile_finish, $product_code, $finish, $matched_by);

            if (is_wp_error($matched_finish_index)) {
                $error_reason = $matched_finish_index->get_error_code();
                $error_message = $matched_finish_index->get_error_message();
                $db_context = array(
                    'available_finishes' => st_build_finish_debug_context($tile_finish),
                );
            } else {
                $finish_row = $tile_finish[$matched_finish_index];
                $normalized_target_size = $api_normalized['size'];
                $available_sizes = array();

                if (isset($finish_row['tile_size']) && is_array($finish_row['tile_size'])) {
                    foreach ($finish_row['tile_size'] as $size_index => $size_row) {
                        $current_size_name = isset($size_row['tile_size_name']) ? (string) $size_row['tile_size_name'] : '';
                        $normalized_current_size = st_normalize_tile_size($current_size_name);
                        $available_sizes[] = array(
                            'index' => $size_index,
                            'raw' => $current_size_name,
                            'normalized' => $normalized_current_size,
                        );

                        // Compare normalized size (ignores case, spaces, and multiplication symbol variants)
                        if ($normalized_current_size === $normalized_target_size) {
                            // Update the price and suffix in the array
                            $tile_finish[$matched_finish_index]['tile_size'][$size_index]['tile_size_price'] = $price;
                            $tile_finish[$matched_finish_index]['tile_size'][$size_index]['suffixtile_size_suffix'] = $suffix;
                            $updated = true;
                            break;
                        }
                    }
                }

                if (!$updated) {
                    $raw_matched_finish = isset($finish_row['finish_name']) ? $finish_row['finish_name'] : '';
                    $matched_finish_name = is_array($raw_matched_finish) && isset($raw_matched_finish['value'])
                        ? (string) $raw_matched_finish['value']
                        : (string) $raw_matched_finish;

                    $error_reason = empty($available_sizes) ? 'no_tile_size_data' : 'size_not_found';
                    $error_message = empty($available_sizes)
                        ? 'No tile_size rows found in the matched finish.'
                        : 'Matching size not found in the matched finish.';
                    $db_context = array(
                        'matched_by' => $matched_by,
                        'finish_index' => $matched_finish_index,
                        'matched_product_code' => isset($finish_row['product_code']) ? (string) $finish_row['product_code'] : '',
                        'matched_finish_name' => $matched_finish_name,
                        'available_sizes' => $available_sizes,
                    );
                }
            }
        } else {
            $post = get_post($product_id);
            $error_reason = $post ? 'no_tile_finish_data' : 'product_not_found';
            $error_message = $post
                ? 'No tile_finish repeater data found for this product.'
                : 'Product post not found for this product_id.';
            $db_context = array(
                'post_exists' => (bool) $post,
                'post_type' => $post ? $post->post_type : null,
                'post_status' => $post ? $post->post_status : null,
                'post_title' => $post ? $post->post_title : null,
            );
        }

        if ($updated) {
            // Update the ACF field in the database. Using the field key is generally safer for complex repeaters.
            // ACF Repeater Key for tile_finish is field_68d3799c30127
            update_field('field_68d3799c30127', $tile_finish, $product_id);
            $success_count++;

            $results[] = array(
                'product_id' => $product_id,
                'product_code' => $product_code,
                'finish' => $finish,
                'size' => $size,
                'status' => 'success',
                'message' => 'Price updated successfully.'
            );
        } else {
            $results[] = array(
                'product_id' => $product_id,
                'product_code' => $product_code,
                'finish' => $finish,
                'size' => $size,
                'status' => 'error',
                'message' => $error_message
            );
            $failed_logs[] = array(
                'product_id' => $product_id,
                'reason' => $error_reason,
                'message' => $error_message,
                'api_input' => $api_input,
                'api_normalized' => $api_normalized,
                'db_context' => $db_context,
            );
        }
    }

    // Write failed records to custom log file in wp-content/uploads/price-update-logs/
    if (!empty($failed_logs)) {
        st_write_price_update_failure_log($failed_logs, count($updates), $success_count);
    }

    return rest_ensure_response(array(
        'status' => 'completed',
        'results' => $results
    ));
}

/**
 * Find the unique matching finish row index in tile_finish repeater.
 * Prioritizes product_code match; falls back to finish_name only when exactly one match exists.
 *
 * @param array  $tile_finish  ACF tile_finish repeater rows.
 * @param string $product_code Product code to match.
 * @param string $finish       Finish name to match as fallback.
 * @return int|WP_Error Matching finish row index or WP_Error on ambiguity/not found.
 */
function st_find_matching_finish_index($tile_finish, $product_code, $finish, &$matched_by = '')
{
    $code_matches = array();
    $finish_matches = array();
    $normalized_target_code = st_normalize_match_text($product_code);
    $normalized_target_finish = st_normalize_match_text($finish);

    foreach ($tile_finish as $finish_index => $finish_row) {
        $current_code = isset($finish_row['product_code']) ? $finish_row['product_code'] : '';
        $raw_finish = isset($finish_row['finish_name']) ? $finish_row['finish_name'] : '';
        $current_finish = is_array($raw_finish) && isset($raw_finish['value'])
            ? $raw_finish['value']
            : $raw_finish;

        if ($normalized_target_code !== '' && st_normalize_match_text($current_code) === $normalized_target_code) {
            $code_matches[] = $finish_index;
        }

        if ($normalized_target_finish !== '' && st_normalize_match_text($current_finish) === $normalized_target_finish) {
            $finish_matches[] = $finish_index;
        }
    }

    // Priority 1: Match by product_code
    if (count($code_matches) === 1) {
        $matched_by = 'product_code';
        return $code_matches[0];
    }
    if (count($code_matches) > 1) {
        return new WP_Error('duplicate_product_code', 'Multiple finishes with the same product_code found in this product.');
    }

    // Priority 2: Fallback to finish_name when unique
    if (count($finish_matches) === 1) {
        $matched_by = 'finish_name';
        return $finish_matches[0];
    }
    if (count($finish_matches) > 1) {
        return new WP_Error('ambiguous_finish_name', 'Product code not matched and multiple finishes with the same finish_name found.');
    }

    return new WP_Error('finish_not_found', 'Matching product_code or finish_name not found in this product.');
}

/**
 * Build diagnostic summary of a product's tile_finish repeater for failure logs.
 *
 * @param array $tile_finish ACF tile_finish repeater rows.
 * @return array Summary of finish rows with raw and normalized values.
 */
function st_build_finish_debug_context($tile_finish)
{
    $summary = array();
    foreach ($tile_finish as $index => $row) {
        $raw_code = isset($row['product_code']) ? (string) $row['product_code'] : '';
        $raw_finish = isset($row['finish_name']) ? $row['finish_name'] : '';
        $finish_val = is_array($raw_finish) && isset($raw_finish['value'])
            ? (string) $raw_finish['value']
            : (string) $raw_finish;

        $sizes = array();
        if (isset($row['tile_size']) && is_array($row['tile_size'])) {
            foreach ($row['tile_size'] as $s_index => $s_row) {
                $raw_size = isset($s_row['tile_size_name']) ? (string) $s_row['tile_size_name'] : '';
                $sizes[] = array(
                    'index' => $s_index,
                    'raw' => $raw_size,
                    'normalized' => st_normalize_tile_size($raw_size),
                );
            }
        }

        $summary[] = array(
            'index' => $index,
            'product_code_raw' => $raw_code,
            'product_code_normalized' => st_normalize_match_text($raw_code),
            'finish_raw' => $finish_val,
            'finish_normalized' => st_normalize_match_text($finish_val),
            'sizes' => $sizes,
        );
    }

    return $summary;
}

/**
 * Write failed price update records to a dedicated daily log file in wp-content/uploads/price-update-logs/.
 *
 * @param array $failed_entries List of failure log entries.
 * @param int   $total_count    Total items received in the batch.
 * @param int   $success_count  Number of items updated successfully.
 * @return string|false Path to the log file on success, false on failure.
 */
function st_write_price_update_failure_log($failed_entries, $total_count = 0, $success_count = 0)
{
    if (empty($failed_entries)) {
        return false;
    }

    $upload_dir = wp_upload_dir();
    if (!empty($upload_dir['error'])) {
        return false;
    }

    $log_dir = trailingslashit($upload_dir['basedir']) . 'price-update-logs';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
        $index_file = trailingslashit($log_dir) . 'index.php';
        if (!file_exists($index_file)) {
            @file_put_contents($index_file, "<?php\n// Silence is golden.\n");
        }
    }

    $date_str = current_time('Y-m-d');
    $timestamp = current_time('Y-m-d H:i:s');
    $log_file = trailingslashit($log_dir) . 'price-update-failed-' . $date_str . '.log';

    $lines = array();
    $lines[] = str_repeat('=', 80);
    $lines[] = sprintf(
        '[%s] Price Update Batch - Total: %d | Success: %d | Failed: %d',
        $timestamp,
        $total_count,
        $success_count,
        count($failed_entries)
    );
    $lines[] = str_repeat('=', 80);

    foreach ($failed_entries as $entry) {
        $lines[] = sprintf(
            '[%s] FAILED | product_id: %s | reason: %s | message: %s',
            $timestamp,
            isset($entry['product_id']) ? $entry['product_id'] : 0,
            isset($entry['reason']) ? $entry['reason'] : 'unknown',
            isset($entry['message']) ? $entry['message'] : ''
        );

        if (isset($entry['api_input'])) {
            $lines[] = '  API Input     : ' . wp_json_encode($entry['api_input'], JSON_UNESCAPED_SLASHES);
        }
        if (isset($entry['api_normalized'])) {
            $lines[] = '  API Normalized: ' . wp_json_encode($entry['api_normalized'], JSON_UNESCAPED_SLASHES);
        }
        if (isset($entry['db_context'])) {
            $lines[] = '  DB Context    : ' . wp_json_encode($entry['db_context'], JSON_UNESCAPED_SLASHES);
        }
        $lines[] = str_repeat('-', 80);
    }

    $lines[] = '';
    $written = @file_put_contents($log_file, implode(PHP_EOL, $lines), FILE_APPEND | LOCK_EX);

    return ($written !== false) ? $log_file : false;
}

/**
 * Normalize text for reliable matching by decoding entities, removing all whitespace, and lowercasing.
 *
 * @param mixed $value Raw text value.
 * @return string Normalized string.
 */
function st_normalize_match_text($value)
{
    $text = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/[\s\p{Z}]+/u', '', $text);

    return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
}

/**
 * Normalize tile size string by unifying multiplication/cross symbols, dashes, whitespace, and case.
 *
 * @param mixed $size Raw size string.
 * @return string Normalized size string.
 */
function st_normalize_tile_size($size)
{
    $text = html_entity_decode((string) $size, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Unify various multiplication/cross symbols (math ×, *, fullwidth Ｘ/ｘ/＊, lookalikes) to 'x'
    $multiply_symbols = array('×', '✕', '✖', '✗', '✘', '⨯', '∗', '*', '＊', 'Ｘ', 'ｘ', 'Х', 'х', 'Χ', 'χ');
    $text = str_replace($multiply_symbols, 'x', $text);

    // Unify Unicode dash variants to ASCII hyphen
    $dash_symbols = array('–', '—', '－', '−');
    $text = str_replace($dash_symbols, '-', $text);

    return st_normalize_match_text($text);
}
