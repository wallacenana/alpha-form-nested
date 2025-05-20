<?php
function alpha_integration_active_campaign($form_id, $data)
{
    if (empty($data['api_key']) || empty($data['api_url']) || empty($data['list_id']) || empty($data['data'])) {
        error_log('[ACTIVE CAMPAIGN] Dados incompletos');
        return false;
    }

    $url = rtrim($data['api_url'], '/') . '/api/3/contacts';
    $body = [
        'contact' => [
            'email' => $data['data']['email_address'] ?? '',
            'firstName' => $data['data']['FNAME'] ?? '',
            'lastName'  => $data['data']['LNAME'] ?? '',
            'phone'     => $data['data']['PHONE'] ?? '',
        ]
    ];

    $headers = [
        'Api-Token' => $data['api_key'],
        'Content-Type' => 'application/json'
    ];

    $response = wp_remote_post($url, [
        'headers' => $headers,
        'body'    => wp_json_encode($body),
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        error_log('[ACTIVE CAMPAIGN] Erro: ' . $response->get_error_message());
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    error_log('[ACTIVE CAMPAIGN] Status: ' . $code);
    error_log('[ACTIVE CAMPAIGN] Body: ' . $body);

    return $code === 201 || $code === 200;
}
