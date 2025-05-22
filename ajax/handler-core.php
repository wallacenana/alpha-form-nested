<?php
add_action('wp_ajax_alpha_hook_trigger', 'alpha_form_handle_license');

function alpha_form_handle_license()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $license = isset($_POST['license']) ? sanitize_text_field(wp_unslash($_POST['license'])) : '';
    if (!$license) {
        wp_send_json_error(['message' => 'Licença vazia.']);
    }

    $domain = site_url();
    $validate_url = "https://psplits.com/wp-json/alphaform/v2/validate?license={$license}&domain=" . urlencode($domain);

    $response = wp_remote_get($validate_url);
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro ao validar a licença.']);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['success'])) {
        wp_send_json_error(['message' => $body['message'] ?? 'Licença inválida.']);
    }

    $data = [
        'chave'   => $body['license_key'] ?? $license,
        'domain'  => $domain,
        'expires' => $body['expires'] ?? '',
        'status'  => $body['status'] ?? 'invalid'
    ];

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

    $payload = json_encode($data);
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE name = %s",
            'valid_key'
        )
    );

    if ($exists) {
        $wpdb->update($table, [
            'data'   => $payload,
            'status' => ($data['status'] === 'valid') ? 1 : 0
        ], ['id' => $exists]);
    } else {
        $wpdb->insert($table, [
            'name'   => 'valid_key',
            'data'   => $payload,
            'status' => ($data['status'] === 'valid') ? 1 : 0
        ]);
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	

    wp_send_json_success(['message' => 'Licença ativada com sucesso.']);
}
