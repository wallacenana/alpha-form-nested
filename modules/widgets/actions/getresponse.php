<?php

function alpha_action_getresponse($mode)
{
    if ($mode !== 'fetch_lists') {
        wp_send_json_error(['message' => 'Modo inválido para GetResponse.']);
    }

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'getresponse')
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

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
    // Verifica se os dados mínimos estão presentes
    if (empty($data['campaign_id']) || empty($data['data']['email'])) {
        return false;
    }

    // Decide de onde vem a API Key
    $api_key = $data['source_type'] === 'custom' ? $data['api_key'] : '';
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    if ($data['source_type'] === 'default') {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM {$wpdb->prefix}alpha_form_nested_integrations WHERE name = %s AND status = 1",
                'getresponse'
            ),
            ARRAY_A
        );

        if ($row && !empty($row['data'])) {
            $config = json_decode($row['data'], true);
            $api_key = $config['api_key'] ?? '';
        }
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    // Validação final
    if (empty($api_key)) {
        return false;
    }

    // Monta o corpo da requisição
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

    // Envia a requisição
    $response = wp_remote_post('https://api.getresponse.com/v3/contacts', [
        'headers' => [
            'X-Auth-Token' => 'api-key ' . $api_key,
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode($body),
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    return ($code >= 200 && $code < 300);
}
