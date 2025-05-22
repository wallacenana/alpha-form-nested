<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'save') {
    $username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
    $api_key  = isset($_POST['api_key'])  ? sanitize_text_field(wp_unslash($_POST['api_key']))  : '';

    $data = json_encode([
        'username' => $username,
        'api_key'  => $api_key
    ]);

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM {$table} WHERE name = %s", 'clicksend')
    );

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'clicksend', 'data' => $data]);
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	

    wp_send_json_success();
}

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'validate') {
    $username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
    $api_key  = isset($_POST['api_key'])  ? sanitize_text_field(wp_unslash($_POST['api_key']))  : '';

    $response = wp_remote_get('https://rest.clicksend.com/v3/account', [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $api_key),
            'Content-Type' => 'application/json'
        ],
        'timeout' => 10
    ]);

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success();
    }
}
