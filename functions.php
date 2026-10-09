<?php
defined('ABSPATH') || exit;
require_once get_template_directory() . '/github-updates.php';
add_action('rest_api_init', function () {
    register_rest_route('makhan/v1', '/enquiry', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => 'makhan_submit_enquiry',
    ));
});
function makhan_submit_enquiry($request) {
    if (strlen($request->get_body()) > 20000) {
        return new WP_Error('too_large', 'Please shorten your enquiry.', array('status' => 413));
    }
    $data = $request->get_json_params();
    if (!is_array($data)) {
        return new WP_Error('invalid_request', 'Invalid enquiry.', array('status' => 400));
    }
    foreach (array('name', 'email', 'message', 'cover', 'website') as $field) {
        if (isset($data[$field]) && !is_string($data[$field])) {
            return new WP_Error('invalid_field', 'Invalid enquiry field.', array('status' => 400));
        }
    }
    if (!empty($data['website'])) {
        return new WP_Error('invalid_request', 'Invalid enquiry.', array('status' => 400));
    }
    $address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    $key = 'makhan_enquiry_' . hash_hmac('sha256', $address, wp_salt('auth'));
    $count = (int) get_transient($key);
    if ($count >= 10) {
        return new WP_Error('rate_limit', 'Please try later or email our team.', array('status' => 429));
    }
    set_transient($key, $count + 1, HOUR_IN_SECONDS);
    $name = sanitize_text_field($data['name'] ?? '');
    $raw_email = $data['email'] ?? '';
    $email = sanitize_email($raw_email);
    $message = sanitize_textarea_field($data['message'] ?? '');
    $cover = sanitize_text_field($data['cover'] ?? '');
    if (!$name || strlen($name) > 200 || !is_email($raw_email) || preg_match('/[\r\n]/', $raw_email) || !$message || strlen($message) > 10000 || strlen($cover) > 200) {
        return new WP_Error('invalid_fields', 'Please check your name, email and message.', array('status' => 400));
    }
    $body = "Website enquiry\n\nName: $name\nEmail: $email\nCover: $cover\n\n$message\n";
    $sent = wp_mail('info@makhaninsurance.com', 'M.A. Khan website enquiry', $body, array('Reply-To: ' . $email));
    if (!$sent) {
        return new WP_Error('mail_failed', 'Could not submit your enquiry. Please email our team.', array('status' => 503));
    }
    // Transport acceptance does not guarantee mailbox receipt.
    return new WP_REST_Response(array('success' => true), 200);
}
