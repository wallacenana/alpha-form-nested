<?php

// Drip
function alpha_integration_drip($form_id, $data)
{
    error_log('[DRIP] Dados recebidos: ' . print_r($data, true));

    if (empty($data['api_key']) || empty($data['account_id']) || empty($data['campaign_id']) || empty($data['data'])) {
        error_log('[DRIP] Dados obrigatórios ausentes.');
        return false;
    }

    $url = "https://api.getdrip.com/v2/{$data['account_id']}/campaigns/{$data['campaign_id']}/subscribers";

    $body = [
        'subscribers' => [
            [
                'email' => $data['data']['email'] ?? '',
                'custom_fields' => $data['data']
            ]
        ]
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("{$data['api_key']}:"),
            'Content-Type'  => 'application/json'
        ],
        'body' => json_encode($body)
    ]);

    if (is_wp_error($response)) {
        error_log('[DRIP] Erro: ' . $response->get_error_message());
        return false;
    }

    return true;
}
