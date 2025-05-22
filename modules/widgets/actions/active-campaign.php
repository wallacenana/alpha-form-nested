<?php

function alpha_action_active_campaign($mode)
{
    if ($mode === 'fetch_lists') {
        global $wpdb;
        $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'active-campaign')
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

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
    if (empty($data['api_key']) || empty($data['api_url']) || empty($data['list_id']) || empty($data['data'])) {
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
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    return $code === 201 || $code === 200;
}
