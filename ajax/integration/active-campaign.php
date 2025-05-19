<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';

if (ALPHA_INTEGRATION_MODE === 'save') {
    $url = sanitize_text_field($_POST['api_url'] ?? '');
    $key = sanitize_text_field($_POST['api_key'] ?? '');

    if (!$url || !$key) {
        wp_send_json_error(['message' => 'Preencha todos os campos.']);
    }

    $data = json_encode([
        'api_url' => $url,
        'api_key' => $key
    ]);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE name = %s", 'active-campaign'));

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'active-campaign', 'data' => $data]);
    }

    wp_send_json_success();
}

if (ALPHA_INTEGRATION_MODE === 'validate') {
    $url = rtrim(sanitize_text_field($_POST['api_url'] ?? ''), '/');
    $key = sanitize_text_field($_POST['api_key'] ?? '');

    if (!$url || !$key) {
        wp_send_json_error(['message' => 'Preencha todos os campos.']);
    }

    $response = wp_remote_get("$url/api/3/users", [
        'headers' => [
            'Api-Token' => $key,
            'Content-Type' => 'application/json'
        ]
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro ao conectar à API.']);
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => "Erro: resposta $code"]);
    }
}
