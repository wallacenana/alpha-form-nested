<?php
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
