<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'save') {
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');
    $server_prefix = sanitize_text_field($_POST['server_prefix'] ?? '');

    if (!$api_key || !$server_prefix) {
        wp_send_json_error(['message' => 'Preencha todos os campos.']);
    }

    $data = json_encode([
        'api_key' => $api_key,
        'server_prefix' => $server_prefix
    ]);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE name = %s", 'mailchimp'));

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'mailchimp', 'data' => $data]);
    }

    wp_send_json_success();
}

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'validate') {
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');
    $server_prefix = sanitize_text_field($_POST['server_prefix'] ?? '');

    if (!$api_key || !$server_prefix) {
        wp_send_json_error(['message' => 'Preencha todos os campos.']);
    }

    $endpoint = "https://{$server_prefix}.api.mailchimp.com/3.0/";

    $response = wp_remote_get($endpoint, [
        'headers' => [
            'Authorization' => 'apikey ' . $api_key,
            'Content-Type'  => 'application/json'
        ],
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro de conexão com o Mailchimp.']);
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => "Erro: resposta $code"]);
    }
}
