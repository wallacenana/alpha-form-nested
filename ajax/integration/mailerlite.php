<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'save') {
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');

    if (!$api_key) {
        wp_send_json_error(['message' => 'Informe a chave da API.']);
    }

    $data = json_encode(['api_key' => $api_key]);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE name = %s", 'mailerlite'));

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'mailerlite', 'data' => $data]);
    }

    wp_send_json_success();
}

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'validate') {
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');

    if (!$api_key) {
        wp_send_json_error(['message' => 'Informe a chave da API.']);
    }

    $response = wp_remote_get('https://api.mailerlite.com/api/v2/subscribers', [
        'headers' => [
            'X-MailerLite-ApiKey' => $api_key,
            'Content-Type' => 'application/json'
        ],
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro ao conectar com o MailerLite.']);
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => "Erro na resposta ($code)"]);
    }
}
