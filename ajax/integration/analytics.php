<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';

if (ALPHA_INTEGRATION_MODE === 'save') {
    $measurement_id = sanitize_text_field($_POST['measurement_id'] ?? '');

    if (!$measurement_id) {
        wp_send_json_error(['message' => 'Informe o Measurement ID.']);
    }

    $data = json_encode(['measurement_id' => $measurement_id]);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE name = %s", 'analytics'));

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'analytics', 'data' => $data]);
    }

    wp_send_json_success();
}

if (ALPHA_INTEGRATION_MODE === 'validate') {
    wp_send_json_success(['message' => 'Analytics salvo.']); // Sem validação real
}
