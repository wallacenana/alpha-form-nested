<?php
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
