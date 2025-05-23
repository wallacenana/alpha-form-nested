<?php

function alpha_action_drip($mode)
{
    if ($mode !== 'fetch_lists') {
        wp_send_json_error(['message' => 'Modo inválido para Drip.']);
    }

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'drip')
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    if (!$row || !$row->status) {
        wp_send_json_error(['message' => 'Drip não está integrado.']);
    }

    $data = json_decode($row->data ?? '{}', true);
    $api_key = $data['api_key'] ?? '';
    $account_id = $data['account_id'] ?? '';

    if (!$api_key || !$account_id) {
        wp_send_json_error(['message' => 'Credenciais Drip ausentes.']);
    }

    $url = "https://api.getdrip.com/v2/{$account_id}/campaigns";

    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("{$api_key}:"),
            'Content-Type'  => 'application/json'
        ],
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro na requisição: ' . $response->get_error_message()]);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $lists = [];

    foreach ($body['campaigns'] ?? [] as $item) {
        $lists[$item['id']] = $item['name'];
    }

    wp_send_json_success(['lists' => $lists]);
}

function alpha_integration_drip($form_id, $data)
{
    if (empty($data['account_id']) || empty($data['campaign_id']) || empty($data['data']['email'])) {
        return false;
    }

    // Define a origem da API key
    $api_key = $data['source_type'] === 'custom' ? $data['api_key'] : '';

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    if ($data['source_type'] === 'default') {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM {$wpdb->prefix}alpha_form_nested_integrations WHERE name = %s AND status = 1",
                'drip'
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

    // Monta a URL e o corpo da requisição
    $url = "https://api.getdrip.com/v2/{$data['account_id']}/campaigns/{$data['campaign_id']}/subscribers";

    $body = [
        'subscribers' => [
            [
                'email'         => $data['data']['email'],
                'custom_fields' => $data['data']
            ]
        ]
    ];

    // Envia
    $response = wp_remote_post($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("{$api_key}:"),
            'Content-Type'  => 'application/json'
        ],
        'body'    => json_encode($body),
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    return ($code >= 200 && $code < 300);
}
