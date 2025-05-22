<?php

function alpha_action_getresponse($mode)
{
    if ($mode !== 'fetch_lists') {
        wp_send_json_error(['message' => 'Modo inválido para GetResponse.']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_nested_integrations';
    $row = $wpdb->get_row("SELECT * FROM $table WHERE name = 'getresponse' LIMIT 1");

    if (!$row || !$row->status) {
        wp_send_json_error(['message' => 'GetResponse não está integrado.']);
    }

    $data = json_decode($row->data ?? '{}', true);
    $api_key = $data['api_key'] ?? '';

    if (!$api_key) {
        wp_send_json_error(['message' => 'Chave da API ausente.']);
    }

    $url = "https://api.getresponse.com/v3/campaigns";

    $response = wp_remote_get($url, [
        'headers' => [
            'X-Auth-Token' => "api-key {$api_key}",
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
        $lists[$item['campaignId']] = $item['name'];
    }

    wp_send_json_success(['lists' => $lists]);
}


function alpha_integration_getresponse($form_id, $data)
{
    error_log('[GETRESPONSE] Dados recebidos: ' . print_r($data, true));

    if (empty($data['api_key']) || empty($data['campaign_id']) || empty($data['data']['email'])) {
        error_log('[GETRESPONSE] Campos obrigatórios ausentes.');
        return false;
    }

    $url = 'https://api.getresponse.com/v3/contacts';
    $body = [
        'email'        => $data['data']['email'],
        'campaign'     => ['campaignId' => $data['campaign_id']],
        'customFieldValues' => []
    ];

    foreach ($data['data'] as $key => $value) {
        if ($key !== 'email') {
            $body['customFieldValues'][] = [
                'customFieldId' => $key,
                'value'         => [$value]
            ];
        }
    }

    $response = wp_remote_post($url, [
        'headers' => [
            'X-Auth-Token' => 'api-key ' . $data['api_key'],
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode($body)
    ]);

    if (is_wp_error($response)) {
        error_log('[GETRESPONSE] Erro: ' . $response->get_error_message());
        return false;
    }

    return true;
}
