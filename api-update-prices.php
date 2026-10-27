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

    if (empty($parameters['updates']) || !is_array($parameters['updates'])) {
        return new WP_Error('invalid_data', 'No updates array found in request body.', array('status' => 400));
    }

    $results = array();

    foreach ($parameters['updates'] as $update) {
        $product_id = isset($update['product_id']) ? intval($update['product_id']) : 0;
        $finish = isset($update['finish']) ? sanitize_text_field($update['finish']) : '';
        $size = isset($update['size']) ? sanitize_text_field($update['size']) : '';
        $price = isset($update['price']) ? sanitize_text_field($update['price']) : '';
        $suffix = isset($update['suffix']) ? sanitize_text_field($update['suffix']) : '';

        // Checking if necessary data is present
        if (!$product_id || !$finish || !$size || $price === '') {
            $results[] = array(
                'product_id' => $product_id,
                'status' => 'error',
                'message' => 'Missing required fields (product_id, finish, size, price).'
            );
            continue;
        }

        // Get the entire repeater field data
        $tile_finish = get_field('tile_finish', $product_id);
        $updated = false;

        if ($tile_finish && is_array($tile_finish)) {
            // Loop through outer repeater (Finish)
            foreach ($tile_finish as $finish_index => $finish_row) {
                // If finish_name is a Select field returning an array, extract the value. Otherwise use it directly.
                $current_finish_name = is_array($finish_row['finish_name']) ? $finish_row['finish_name']['value'] : $finish_row['finish_name'];

                // Compare finish name (case-insensitive)
                if (strcasecmp($current_finish_name, $finish) === 0) {
                    // Match found for finish, now look for size in inner repeater
                    if (isset($finish_row['tile_size']) && is_array($finish_row['tile_size'])) {
                        foreach ($finish_row['tile_size'] as $size_index => $size_row) {
                            // Compare size name (case-insensitive)
                            if (strcasecmp($size_row['tile_size_name'], $size) === 0) {
                                // Update the price in the array
                                $tile_finish[$finish_index]['tile_size'][$size_index]['tile_size_price'] = $price;
                                $tile_finish[$finish_index]['tile_size'][$size_index]['suffixtile_size_suffix'] = $suffix;
                                $updated = true;
                                break 2; // Break out of both loops once found
                            }
                        }
                    }
                }
            }
        }

        if ($updated) {
            // Update the ACF field in the database. Using the field key is generally safer for complex repeaters.
            // ACF Repeater Key for tile_finish is field_68d3799c30127
            update_field('field_68d3799c30127', $tile_finish, $product_id);

            $results[] = array(
                'product_id' => $product_id,
                'finish' => $finish,
                'size' => $size,
                'status' => 'success',
                'message' => 'Price updated successfully.'
            );
        } else {
            $results[] = array(
                'product_id' => $product_id,
                'finish' => $finish,
                'size' => $size,
                'status' => 'error',
                'message' => 'Matching finish or size not found in this product.'
            );
        }
    }

    return rest_ensure_response(array(
        'status' => 'completed',
        'results' => $results
    ));
}
