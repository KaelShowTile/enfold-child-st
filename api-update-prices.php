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
        return new WP_Error('invalid_data', 'No updates array found in request body.', array('status' => 400));
    }

    $results = array();

    foreach ($updates as $update) {
        $product_id = isset($update['product_id']) ? intval($update['product_id']) : 0;
        $product_code = isset($update['product_code']) ? trim(sanitize_text_field($update['product_code'])) : '';
        $finish = isset($update['finish']) ? trim(sanitize_text_field($update['finish'])) : '';
        $size = isset($update['size']) ? trim(sanitize_text_field($update['size'])) : '';
        $price = isset($update['price']) ? sanitize_text_field($update['price']) : '';
        $suffix = isset($update['suffix']) ? sanitize_text_field($update['suffix']) : '';

        // Checking if necessary data is present
        if (!$product_id || ($product_code === '' && $finish === '') || $size === '' || $price === '') {
            $results[] = array(
                'product_id' => $product_id,
                'product_code' => $product_code,
                'finish' => $finish,
                'size' => $size,
                'status' => 'error',
                'message' => 'Missing required fields (product_id, product_code or finish, size, price).'
            );
            continue;
        }

        // Get the entire repeater field data
        $tile_finish = get_field('tile_finish', $product_id);
        $updated = false;
        $error_message = 'Matching finish or size not found in this product.';

        if ($tile_finish && is_array($tile_finish)) {
            $matched_finish_index = st_find_matching_finish_index($tile_finish, $product_code, $finish);

            if (is_wp_error($matched_finish_index)) {
                $error_message = $matched_finish_index->get_error_message();
            } else {
                $finish_row = $tile_finish[$matched_finish_index];
                if (isset($finish_row['tile_size']) && is_array($finish_row['tile_size'])) {
                    foreach ($finish_row['tile_size'] as $size_index => $size_row) {
                        $current_size_name = isset($size_row['tile_size_name']) ? trim((string) $size_row['tile_size_name']) : '';
                        // Compare size name (case-insensitive)
                        if (strcasecmp($current_size_name, $size) === 0) {
                            // Update the price and suffix in the array
                            $tile_finish[$matched_finish_index]['tile_size'][$size_index]['tile_size_price'] = $price;
                            $tile_finish[$matched_finish_index]['tile_size'][$size_index]['suffixtile_size_suffix'] = $suffix;
                            $updated = true;
                            break;
                        }
                    }
                }

                if (!$updated) {
                    $error_message = 'Matching size not found in the matched finish.';
                }
            }
        } else {
            $error_message = 'No tile_finish repeater data found for this product.';
        }

        if ($updated) {
            // Update the ACF field in the database. Using the field key is generally safer for complex repeaters.
            // ACF Repeater Key for tile_finish is field_68d3799c30127
            update_field('field_68d3799c30127', $tile_finish, $product_id);

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
        }
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
function st_find_matching_finish_index($tile_finish, $product_code, $finish)
{
    $code_matches = array();
    $finish_matches = array();

    foreach ($tile_finish as $finish_index => $finish_row) {
        $current_code = isset($finish_row['product_code']) ? trim((string) $finish_row['product_code']) : '';
        $raw_finish = isset($finish_row['finish_name']) ? $finish_row['finish_name'] : '';
        $current_finish = is_array($raw_finish) && isset($raw_finish['value'])
            ? trim((string) $raw_finish['value'])
            : trim((string) $raw_finish);

        if ($product_code !== '' && $current_code !== '' && strcasecmp($current_code, $product_code) === 0) {
            $code_matches[] = $finish_index;
        }

        if ($finish !== '' && $current_finish !== '' && strcasecmp($current_finish, $finish) === 0) {
            $finish_matches[] = $finish_index;
        }
    }

    // Priority 1: Match by product_code
    if (count($code_matches) === 1) {
        return $code_matches[0];
    }
    if (count($code_matches) > 1) {
        return new WP_Error('duplicate_product_code', 'Multiple finishes with the same product_code found in this product.');
    }

    // Priority 2: Fallback to finish_name when unique
    if (count($finish_matches) === 1) {
        return $finish_matches[0];
    }
    if (count($finish_matches) > 1) {
        return new WP_Error('ambiguous_finish_name', 'Product code not matched and multiple finishes with the same finish_name found.');
    }

    return new WP_Error('finish_not_found', 'Matching product_code or finish_name not found in this product.');
}
