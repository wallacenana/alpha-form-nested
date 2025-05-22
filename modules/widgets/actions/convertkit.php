<?php

function alpha_action_convertkit($mode)
{
    if ($mode !== 'fetch_lists') {
        wp_send_json_error(['message' => 'Modo inválido para ConvertKit.']);
    }

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'convertkit')
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

    if (!$row || !$row->status) {
        wp_send_json_error(['message' => 'ConvertKit não está integrado.']);
    }

    $data = json_decode($row->data ?? '{}', true);
    $api_secret = $data['api_secret'] ?? '';

    if (!$api_secret) {
        wp_send_json_error(['message' => 'Credencial ConvertKit ausente.']);
    }

    $url = "https://api.convertkit.com/v3/forms?api_secret={$api_secret}";

    $response = wp_remote_get($url, [
        'timeout' => 10
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro na requisição: ' . $response->get_error_message()]);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $lists = [];

    foreach ($body['forms'] ?? [] as $form) {
        $lists[$form['id']] = $form['name'];
    }

    wp_send_json_success(['lists' => $lists]);
}


function alpha_integration_convertkit($form_id, $data)
{
    if (empty($data['api_secret']) || empty($data['form_id']) || empty($data['data']['email'])) {
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
        return false;
    }

    return true;
}
