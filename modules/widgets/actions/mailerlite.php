<?php
<?php

function alpha_action_mailerlite($mode)
{
    if ($mode !== 'fetch_lists') {
        wp_send_json_error(['message' => 'Modo inválido para MailerLite.']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_nested_integrations';
    $row = $wpdb->get_row("SELECT * FROM $table WHERE name = 'mailerlite' LIMIT 1");

    if (!$row || !$row->status) {
        wp_send_json_error(['message' => 'MailerLite não está integrado.']);
    }

    $data = json_decode($row->data ?? '{}', true);
    $api_key = $data['api_key'] ?? '';

    if (!$api_key) {
        wp_send_json_error(['message' => 'Chave da API ausente.']);
    }

    $url = "https://api.mailerlite.com/api/v2/groups";

    $response = wp_remote_get($url, [
        'headers' => [
            'X-MailerLite-ApiKey' => $api_key,
            'Content-Type' => 'application/json'
        ],
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro na requisição: ' . $response->get_error_message()]);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $lists = [];

    foreach ($body ?? [] as $item) {
        $lists[$item['id']] = $item['name'];
    }

    wp_send_json_success(['lists' => $lists]);
}

function alpha_integration_mailerlite($form_id, $data)
{
    error_log('[MAILERLITE] Dados recebidos: ' . print_r($data, true));

    if (empty($data['api_key']) || empty($data['group_id']) || empty($data['data']['email'])) {
        error_log('[MAILERLITE] Campos obrigatórios ausentes.');
        return false;
    }

    $url = "https://api.mailerlite.com/api/v2/groups/{$data['group_id']}/subscribers";

    $body = [
        'email' => $data['data']['email']
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'X-MailerLite-ApiKey' => $data['api_key'],
            'Content-Type'        => 'application/json'
        ],
        'body' => json_encode($body)
    ]);

    if (is_wp_error($response)) {
        error_log('[MAILERLITE] Erro: ' . $response->get_error_message());
        return false;
    }

    return true;
}
