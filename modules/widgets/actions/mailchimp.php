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
