<?php

function alpha_action_active_campaign($mode)
{
    if ($mode === 'fetch_lists') {
        global $wpdb;
        $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'active-campaign')
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

        $data = json_decode($row->data ?? '{}', true);
        $key = $data['api_key'] ?? '';
        $url = rtrim($data['api_url'] ?? '', '/');

        $endpoint = "{$url}/api/3/lists";
        $response = wp_remote_get($endpoint, [
            'headers' => [
                'Api-Token' => $key
            ]
        ]);

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
    if (empty($data['list_id']) || empty($data['data']['email_address'])) {
        return false;
    }

    // Decide origem das credenciais
    $api_key = $data['source_type'] === 'custom' ? $data['api_key'] : '';
    $api_url = $data['source_type'] === 'custom' ? $data['api_url'] : '';

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    if ($data['source_type'] === 'default') {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM {$wpdb->prefix}alpha_form_nested_integrations WHERE name = %s AND status = 1",
                'active-campaign'
            ),
            ARRAY_A
        );

        if ($row && !empty($row['data'])) {
            $config  = json_decode($row['data'], true);
            $api_key = $config['api_key'] ?? '';
            $api_url = $config['api_url'] ?? '';
        }
    }
    // phpcs:desable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    // Validação final
    if (empty($api_key) || empty($api_url)) {
        return false;
    }

    // Envia contato
    $url = rtrim($api_url, '/') . '/api/3/contacts';

    $body = [
        'contact' => [
            'email'     => $data['data']['email_address'],
            'firstName' => $data['data']['FNAME'] ?? '',
            'lastName'  => $data['data']['LNAME'] ?? '',
            'phone'     => $data['data']['PHONE'] ?? '',
        ]
    ];

    $headers = [
        'Api-Token'   => $api_key,
        'Content-Type' => 'application/json'
    ];

    $response = wp_remote_post($url, [
        'headers' => $headers,
        'body'    => wp_json_encode($body),
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    return $code === 201 || $code === 200;
}
