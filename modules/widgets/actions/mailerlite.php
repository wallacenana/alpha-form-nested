<?php
function alpha_action_mailerlite($mode)
{
    if ($mode !== 'fetch_lists') {
        wp_send_json_error(['message' => 'Modo inválido para MailerLite.']);
    }

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'mailerlite')
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    if (!$row || !$row->status) {
        wp_send_json_error(['message' => 'MailerLite não está integrado.']);
    }

    $data = json_decode($row->data ?? '{}', true);
    $api_key = $data['api_key'] ?? '';

    if (!$api_key) {
        wp_send_json_error(['message' => 'Chave da API ausente.']);
    }

    $url = "https://api.mailerlite.com/api/v2/groups";

    $response = wp_remote_get($url, [
        'headers' => [
            'X-MailerLite-ApiKey' => $api_key,
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
        $lists[$item['id']] = $item['name'];
    }

    wp_send_json_success(['lists' => $lists]);
}

function alpha_integration_mailerlite($form_id, $data)
{
    // Verifica se tem os dados básicos
    if (empty($data['group_id']) || empty($data['data']['email'])) {
        return false;
    }

    // Decide se busca do banco ou usa dados do POST
    $api_key = $data['source_type'] === 'custom' ? $data['api_key'] : '';

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    if ($data['source_type'] === 'default') {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM {$wpdb->prefix}alpha_form_nested_integrations WHERE name = %s AND status = 1",
                'mailerlite'
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

    // Prepara o envio
    $url = "https://api.mailerlite.com/api/v2/groups/{$data['group_id']}/subscribers";

    $body = [
        'email' => $data['data']['email']
    ];

    $response = wp_remote_post($url, [
        'headers' => [
            'X-MailerLite-ApiKey' => $api_key,
            'Content-Type'        => 'application/json'
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
