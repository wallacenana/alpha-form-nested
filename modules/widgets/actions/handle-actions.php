<?php

add_action('wp_ajax_alphaform_load_lists', 'alphaform_handle_load_lists');
function alphaform_handle_load_lists()
{
    check_ajax_referer('alpha_form_nonce');

    $prefix = isset($_POST['prefix']) ? sanitize_text_field(wp_unslash($_POST['prefix'])) : '';

    if (!$prefix) {
        wp_send_json_error(['message' => 'Prefixo não informado.']);
    }

    $lists = alphaform_fetch_lists_by_prefix($prefix);

    if (!$lists) {
        wp_send_json_error(['message' => 'Nenhuma lista encontrada ou erro na API.']);
    }

    wp_send_json_success($lists);
}


function alphaform_fetch_lists_by_prefix($prefix)
{
    global $wpdb;

    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", $prefix)
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    if (!$row || !$row->status) return false;

    $data = json_decode($row->data ?? '{}', true);
    if (!$data) return false;

    switch ($prefix) {
        case 'mailchimp':
            if (empty($data['api_key']) || empty($data['server_prefix'])) return false;
            $key = $data['api_key'];
            $server = $data['server_prefix'];
            $response = wp_remote_get("https://{$server}.api.mailchimp.com/3.0/lists", [
                'headers' => ['Authorization' => 'Basic ' . base64_encode("user:$key")]
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $output = [];
            foreach ($body['lists'] ?? [] as $item) {
                $output[$item['id']] = $item['name'];
            }
            return ['lists' => $output];

        case 'active-campaign':
            if (empty($data['api_key']) || empty($data['api_url'])) return false;
            $response = wp_remote_get(rtrim($data['api_url'], '/') . '/api/3/lists', [
                'headers' => ['Api-Token' => $data['api_key']]
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $output = [];
            foreach ($body['lists'] ?? [] as $item) {
                $output[$item['id']] = $item['name'];
            }
            return ['lists' => $output];

        case 'getresponse':
            if (empty($data['api_key'])) return false;
            $response = wp_remote_get("https://api.getresponse.com/v3/campaigns", [
                'headers' => ['X-Auth-Token' => "api-key {$data['api_key']}"]
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $output = [];
            foreach ($body as $item) {
                $output[$item['campaignId']] = $item['name'];
            }
            return ['lists' => $output];

        case 'drip':
            if (empty($data['account_id']) || empty($data['api_key'])) return false;
            $response = wp_remote_get("https://api.getdrip.com/v2/{$data['account_id']}/campaigns", [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$data['api_key']}:"),
                    'Content-Type' => 'application/json'
                ]
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $output = [];
            foreach ($body['campaigns'] ?? [] as $item) {
                $output[$item['id']] = $item['name'];
            }
            return ['lists' => $output];

        case 'convertkit':
            if (empty($data['api_secret'])) return false;
            $response = wp_remote_get("https://api.convertkit.com/v3/forms?api_secret={$data['api_secret']}");
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $output = [];
            foreach ($body['forms'] ?? [] as $item) {
                $output[$item['id']] = $item['name'];
            }
            return ['lists' => $output];

        case 'mailerlite':
            if (empty($data['api_key'])) return false;
            $response = wp_remote_get("https://api.mailerlite.com/api/v2/groups", [
                'headers' => ['X-MailerLite-ApiKey' => $data['api_key']]
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $output = [];
            foreach ($body as $item) {
                $output[$item['id']] = $item['name'];
            }
            return ['lists' => $output];

        case 'clicksend':
            if (empty($data['username']) || empty($data['api_key'])) return false;
            $auth = base64_encode("{$data['username']}:{$data['api_key']}");
            $response = wp_remote_get("https://rest.clicksend.com/v3/lists", [
                'headers' => ['Authorization' => "Basic $auth"]
            ]);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $output = [];
            foreach ($body['data']['data'] ?? [] as $item) {
                $output[$item['list_id']] = $item['list_name'];
            }
            return ['lists' => $output];

        default:
            return false;
    }
}
