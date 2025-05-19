<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'save') {
    $username = sanitize_text_field($_POST['username'] ?? '');
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');

    if (!$username || !$api_key) {
        wp_send_json_error(['message' => 'Preencha o usuário e a chave da API.']);
    }

    $data = json_encode([
        'username' => $username,
        'api_key'  => $api_key
    ]);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE name = %s", 'clicksend'));

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'clicksend', 'data' => $data]);
    }

    wp_send_json_success();
}

if (defined('ALPHA_INTEGRATION_MODE') && ALPHA_INTEGRATION_MODE === 'validate') {
    $username = sanitize_text_field($_POST['username'] ?? '');
    $api_key = sanitize_text_field($_POST['api_key'] ?? '');

    if (!$username || !$api_key) {
        wp_send_json_error(['message' => 'Preencha o usuário e a chave da API.']);
    }

    $response = wp_remote_get('https://rest.clicksend.com/v3/account', [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $api_key),
            'Content-Type' => 'application/json'
        ],
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro ao conectar com o ClickSend.']);
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => "Erro na resposta ($code)"]);
    }
}
