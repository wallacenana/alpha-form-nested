<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'save') {
    $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';

    $data = json_encode(['api_key' => $api_key]);

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM {$table} WHERE name = %s", 'getresponse')
    );

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'getresponse', 'data' => $data]);
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	

    wp_send_json_success();
}

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'validate') {
    $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';

    $response = wp_remote_get('https://api.getresponse.com/v3/accounts', [
        'headers' => [
            'X-Auth-Token' => 'api-key ' . $api_key,
            'Content-Type' => 'application/json'
        ],
        'timeout' => 10
    ]);

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success();
    }
}
