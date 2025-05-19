<?php
if (!defined('ABSPATH')) exit;

check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';


if (ALPHA_INTEGRATION_MODE === 'save') {
    $pixel_id = sanitize_text_field($_POST['pixel_id'] ?? '');

    if (!$pixel_id) {
        wp_send_json_error(['message' => 'Informe o Pixel ID.']);
    }

    $data = json_encode(['pixel_id' => $pixel_id]);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE name = %s", 'facebook'));

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'facebook', 'data' => $data]);
    }

    wp_send_json_success();
}

if (ALPHA_INTEGRATION_MODE === 'validate') {
    // Não tem validação real de pixel, então retorna sempre sucesso
    wp_send_json_success(['message' => 'Pixel salvo.']);
}
