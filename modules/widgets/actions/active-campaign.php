<?php

function alpha_action_active_campaign($mode)
{
    if ($mode === 'fetch_lists') {
        global $wpdb;
        $table = $wpdb->prefix . 'alpha_form_nested_integrations';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE name = 'active-campaign' LIMIT 1");

        if (!$row || !$row->status) {
            wp_send_json_error(['message' => 'ActiveCampaign não está integrado.']);
        }

        $data = json_decode($row->data ?? '{}', true);
        $key = $data['api_key'] ?? '';
        $url = rtrim($data['api_url'] ?? '', '/');

        if (!$key || !$url) {
            wp_send_json_error(['message' => 'Credenciais inválidas.']);
        }

        $endpoint = "{$url}/api/3/lists";
        $response = wp_remote_get($endpoint, [
            'headers' => [
                'Api-Token' => $key
            ]
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Erro ao conectar à API.']);
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $lists = [];

        foreach ($body['lists'] ?? [] as $list) {
            $lists[$list['id']] = $list['name'];
        }

        wp_send_json_success(['lists' => $lists]);
    }
}


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
