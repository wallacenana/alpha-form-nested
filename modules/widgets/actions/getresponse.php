<?php

// GetResponse
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
