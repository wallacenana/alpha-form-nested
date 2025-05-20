<?php
function alpha_action_mailchimp($mode)
{
    if ($mode === 'fetch_lists') {
        global $wpdb;
        $table = $wpdb->prefix . 'alpha_form_nested_integrations';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE name = 'mailchimp' LIMIT 1");

        if (!$row || !$row->status) {
            wp_send_json_error(['message' => 'Mailchimp não está integrado.']);
        }

        $data = json_decode($row->data ?? '{}', true);
        $key = $data['api_key'] ?? '';
        $server = $data['server_prefix'] ?? '';

        if (!$key || !$server) {
            wp_send_json_error(['message' => 'Credenciais inválidas.']);
        }

        $endpoint = "https://{$server}.api.mailchimp.com/3.0/lists";
        $response = wp_remote_get($endpoint, [
            'headers' => [
                'Authorization' => 'apikey ' . $key
            ]
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Erro ao conectar à API.']);
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $lists = [];

        foreach ($body['lists'] as $list) {
            $lists[$list['id']] = $list['name'];
        }

        wp_send_json_success(['lists' => $lists]);
    }
}


function alpha_integration_mailchimp($form_id, $data)
{
    $log = ['form_id' => $form_id, 'data' => $data];

    if (empty($data['list_id']) || empty($data['data'])) {
        error_log('[MAILCHIMP] Dados insuficientes: ' . print_r($log, true));
        return false;
    }

    // Se for custom, usar API do próprio POST
    $api_key = $data['source_type'] === 'custom' ? $data['api_key'] : '';
    $server  = $data['source_type'] === 'custom' ? $data['server'] : '';

    // Se default, busca no banco
    if ($data['source_type'] === 'default') {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM {$wpdb->prefix}alpha_form_nested_integrations WHERE name = %s AND status = 1",
                'mailchimp'
            ),
            ARRAY_A
        );

        if ($row && !empty($row['data'])) {
            $config = json_decode($row['data'], true);
            $api_key = $config['api_key'] ?? '';
            $server  = $config['server_prefix'] ?? '';
        }
    }

    // Validação final
    if (!$api_key || !$server) {
        error_log('[MAILCHIMP] API ou Server faltando');
        return false;
    }

    // Envia
    $url = "https://{$server}.api.mailchimp.com/3.0/lists/{$data['list_id']}/members";

    $response = wp_remote_post($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("user:$api_key"),
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode([
            'email_address' => $data['data']['email_address'],
            'status'        => 'subscribed',
            'merge_fields'  => $data['data'],
        ]),
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        error_log('[MAILCHIMP] Erro na requisição: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!empty($body['status']) && in_array($body['status'], ['subscribed', 'pending'])) {
        error_log('[MAILCHIMP] Inscrito com sucesso: ' . $body['email_address']);
        return true;
    } else {
        error_log('[MAILCHIMP] Erro no retorno: ' . print_r($body, true));
        return false;
    }
}
