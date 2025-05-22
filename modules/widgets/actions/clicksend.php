<?php
<?php

function alpha_action_clicksend($mode)
{
    if ($mode !== 'fetch_lists') {
        wp_send_json_error(['message' => 'Modo inválido para ClickSend.']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_nested_integrations';
    $row = $wpdb->get_row("SELECT * FROM $table WHERE name = 'clicksend' LIMIT 1");

    if (!$row || !$row->status) {
        wp_send_json_error(['message' => 'ClickSend não está integrado.']);
    }

    $data = json_decode($row->data ?? '{}', true);
    $username = $data['username'] ?? '';
    $api_key = $data['api_key'] ?? '';

    if (!$username || !$api_key) {
        wp_send_json_error(['message' => 'Credenciais ClickSend ausentes.']);
    }

    $url = "https://rest.clicksend.com/v3/lists";

    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("{$username}:{$api_key}")
        ]
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro na requisição: ' . $response->get_error_message()]);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $lists = [];

    foreach ($body['data']['data'] ?? [] as $item) {
        $lists[$item['list_id']] = $item['list_name'];
    }

    wp_send_json_success(['lists' => $lists]);
}


function alpha_integration_clicksend($form_id, $data)
{
    error_log('[CLICKSEND] Dados recebidos: ' . print_r($data, true));

    if (empty($data['username']) || empty($data['api_key']) || empty($data['list_id']) || empty($data['data']['email'])) {
        error_log('[CLICKSEND] Dados obrigatórios ausentes.');
        return false;
    }

    $url = "https://rest.clicksend.com/v3/lists/{$data['list_id']}/contacts";

    $body = [
        'email' => $data['data']['email'],
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("{$data['username']}:{$data['api_key']}"),
            'Content-Type'  => 'application/json'
        ],
        'body' => json_encode($body)
    ]);

    if (is_wp_error($response)) {
        error_log('[CLICKSEND] Erro: ' . $response->get_error_message());
        return false;
    }

    return true;
}
