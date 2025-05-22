<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

if (ALPHA_INTEGRATION_MODE === 'save') {
    $url = isset($_POST['api_url']) ? sanitize_text_field(wp_unslash($_POST['api_url'])) : '';
    $key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';

    $data = json_encode([
        'api_url' => $url,
        'api_key' => $key
    ]);

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM {$table} WHERE name = %s", 'active-campaign')
    );

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'active-campaign', 'data' => $data]);
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	

    wp_send_json_success();
}

if (ALPHA_INTEGRATION_MODE === 'validate') {
    $url = isset($_POST['api_url']) ? rtrim(sanitize_text_field(wp_unslash($_POST['api_url'])), '/') : '';
    $key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';

    $response = wp_remote_get("$url/api/3/users", [
        'headers' => [
            'Api-Token' => $key,
            'Content-Type' => 'application/json'
        ]
    ]);

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success();
    }
}
