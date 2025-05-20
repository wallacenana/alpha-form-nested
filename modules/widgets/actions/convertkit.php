<?php
function alpha_integration_convertkit($form_id, $data)
{
    error_log('[CONVERTKIT] Dados recebidos: ' . print_r($data, true));

    if (empty($data['api_secret']) || empty($data['form_id']) || empty($data['data']['email'])) {
        error_log('[CONVERTKIT] Campos obrigatórios ausentes.');
        return false;
    }

    $url = "https://api.convertkit.com/v3/forms/{$data['form_id']}/subscribe";

    $body = [
        'email'      => $data['data']['email'],
        'api_secret' => $data['api_secret']
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode($body)
    ]);

    if (is_wp_error($response)) {
        error_log('[CONVERTKIT] Erro: ' . $response->get_error_message());
        return false;
    }

    return true;
}
