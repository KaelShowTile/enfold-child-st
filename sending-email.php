<?php

// Register AJAX handler for tile enquiry email
add_action('wp_ajax_send_tile_enquiry_email', 'send_tile_enquiry_email');
add_action('wp_ajax_nopriv_send_tile_enquiry_email', 'send_tile_enquiry_email');

function send_tile_enquiry_email() {
    // Check if form data is provided
    if (!isset($_POST['form_data'])) {
        wp_send_json_error('No form data provided');
        return;
    }

    // Decode the JSON form data
    $form_data = json_decode(stripslashes($_POST['form_data']), true);

    if (!$form_data) {
        wp_send_json_error('Invalid form data');
        return;
    }

    // Sanitize form data
    $customer_name = sanitize_text_field($form_data['customer_name']);
    $customer_type = sanitize_text_field($form_data['customer_type']);
    $contact_no = sanitize_text_field($form_data['contact_no']);
    $company_name = sanitize_text_field($form_data['company_name']);
    $customer_email = sanitize_email($form_data['customer_email']);
    $project_reference = sanitize_text_field($form_data['project_reference']);
    $need_sample = sanitize_text_field($form_data['need_sample']);
    $subscribe_newsletter = isset($form_data['subscribe_newsletter']) ? sanitize_text_field($form_data['subscribe_newsletter']) : 'no';
    $customer_address = sanitize_textarea_field($form_data['customer_address']);
    $basket_content = wp_kses_post($form_data['basket_content']); // Allow basic HTML

    //get admin email
    $admin_email = get_option('admin_email');

    // Validate required fields
    if (empty($customer_name) || empty($customer_type) || empty($contact_no) || empty($customer_email) || empty($customer_address)) {
        wp_send_json_error('Missing required fields');
        return;
    }

    // Validate email
    if (!is_email($customer_email)) {
        wp_send_json_error('Invalid email address');
        return;
    }

    // Prepare email content
    $subjectAdmin = 'Tile Enquiry from ' . $customer_name;
    $subjectCustomer = 'We Received Your Tile Enquire';

    $messageAdmin = "
    <html>
    <head>
        <title>Tile Enquiry</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #f8f9fa; padding: 20px; border-bottom: 2px solid #dee2e6; }
            .content { padding: 20px; }
            .customer-info { background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; }
            .basket-content { margin-top: 30px; }
            .basket-content h3 { color: #495057; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; }
            .submit-form-row { display: flex; margin-bottom: 20px; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; }
            .basket-item-image { flex: 0 0 100px; margin-right: 15px; }
            .basket-item-image img { max-width: 100%; height: auto; }
            .basket-item-details { flex: 1; }
            .basket-item-note { flex: 1; margin-left: 15px; }
            .basket-item-note textarea { width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>New Tile Enquiry</h1>
            <p>A customer has submitted a tile enquiry through the website.</p>
        </div>

        <div class='content'>
            <div class='customer-info'>
                <h2>Customer Information</h2>
                <p><strong>Name:</strong> {$customer_name}</p>
                <p><strong>Type:</strong> {$customer_type}</p>
                <p><strong>Contact Number:</strong> {$contact_no}</p>
                <p><strong>Email:</strong> {$customer_email}</p>
                <p><strong>Company:</strong> " . (!empty($company_name) ? $company_name : 'Not provided') . "</p>
                <p><strong>Project Reference:</strong> " . (!empty($project_reference) ? $project_reference : 'Not provided') . "</p>
                <p><strong>Address:</strong> {$customer_address}</p>
                <p><strong>Needs Samples:</strong> " . ($need_sample === 'yes' ? 'Yes' : 'No') . "</p>
            </div>

            <div class='basket-content'>
                <h3>Selected Tiles</h3>
                {$basket_content}
            </div>
        </div>
    </body>
    </html>
    ";

    $messageCustomer = "
    <html>
    <head>
        <title>Tile Enquiry</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #f8f9fa; padding: 20px; border-bottom: 2px solid #dee2e6; }
            .content { padding: 20px; }
            .customer-info { background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; }
            .basket-content { margin-top: 30px; }
            .basket-content h3 { color: #495057; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; }
            .submit-form-row { display: flex; margin-bottom: 20px; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; }
            .basket-item-image { flex: 0 0 100px; margin-right: 15px; }
            .basket-item-image img { max-width: 100%; height: auto; }
            .basket-item-details { flex: 1; }
            .basket-item-note { flex: 1; margin-left: 15px; }
            .basket-item-note textarea { width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>Thank you for submitting a tile enquiry.</h1>
            <p>We will get back to you as soon as possiable.</p>
        </div>

        <div class='content'>
            <div class='customer-info'>
                <h2>Customer Information</h2>
                <p><strong>Name:</strong> {$customer_name}</p>
                <p><strong>Type:</strong> {$customer_type}</p>
                <p><strong>Contact Number:</strong> {$contact_no}</p>
                <p><strong>Email:</strong> {$customer_email}</p>
                <p><strong>Company:</strong> " . (!empty($company_name) ? $company_name : 'Not provided') . "</p>
                <p><strong>Project Reference:</strong> " . (!empty($project_reference) ? $project_reference : 'Not provided') . "</p>
                <p><strong>Address:</strong> {$customer_address}</p>
                <p><strong>Needs Samples:</strong> " . ($need_sample === 'yes' ? 'Yes' : 'No') . "</p>
            </div>

            <div class='basket-content'>
                <h3>Selected Tiles</h3>
                {$basket_content}
            </div>
        </div>
    </body>
    </html>
    ";

    // Set email headers
    $headersAdmin = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <noreply@' . parse_url(get_site_url(), PHP_URL_HOST) . '>',
        'Reply-To: ' . $customer_email
    );

    $headersCustomer = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <sales@' . parse_url(get_site_url(), PHP_URL_HOST) . '>',
        'Reply-To: sales@showtile.com.au'
    );

    // Send email to admin/support
    $additional_recipients = array(
        'marketing@showtile.com.au'
    );

    // Send to admin
    $admin_sent = wp_mail($admin_email, $subjectAdmin, $messageAdmin, $headersAdmin);

    //Send to customer
    $customer_sent = wp_mail($customer_email, $subjectCustomer, $messageCustomer, $headersCustomer);

    // Send to additional recipients
    $additional_sent = true;
    foreach ($additional_recipients as $recipient) {
        if (!wp_mail($recipient, $subjectAdmin, $messageAdmin, $headersAdmin)) {
            $additional_sent = false;
        }
    }

    if ($admin_sent && $additional_sent) {
        // Subscribe to Mailchimp if checkbox was ticked
        if ($subscribe_newsletter === 'yes') {
            st_subscribe_to_mailchimp($customer_email, $customer_name);
        }
        wp_send_json_success('Email sent successfully');
    } else {
        wp_send_json_error('Failed to send email');
    }
}

/**
 * Subscribe user to Mailchimp List
 */
function st_subscribe_to_mailchimp($email, $name) {
    // --- CONFIGURATION ---
    $api_key = 'e0f6db032d03b021cf937e5937271431-us19'; // e.g., 'abcdef123456...-us19'
    $list_id = '81e01869fd'; // Replace this with your actual Audience ID found in Mailchimp
    // ---------------------

    if (empty($api_key) || empty($list_id) || !is_email($email)) {
        return;
    }

    // Extract data center from API key
    $data_center = substr($api_key, strpos($api_key, '-') + 1);
    $url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';

    // Split name into First and Last
    $name_parts = explode(' ', trim($name), 2);
    $fname = isset($name_parts[0]) ? $name_parts[0] : '';
    $lname = isset($name_parts[1]) ? $name_parts[1] : '';

    $data = array(
        'email_address' => $email,
        'status'        => 'subscribed',
        'merge_fields'  => array(
            'FNAME' => $fname,
            'LNAME' => $lname
        )
    );

    $args = array(
        'body'        => json_encode($data),
        'headers'     => array(
            'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
            'Content-Type'  => 'application/json'
        ),
        'method'      => 'POST',
        'data_format' => 'body',
        'blocking'    => false // Don't wait for response to speed up UX
    );

    wp_remote_post($url, $args);
}
