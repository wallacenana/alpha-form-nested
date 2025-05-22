<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table =  esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

if (ALPHA_INTEGRATION_MODE === 'save') {
    $measurement_id = isset($_POST['measurement_id']) ? sanitize_text_field(wp_unslash($_POST['measurement_id'])) : '';

    $data = json_encode(['measurement_id' => $measurement_id]);

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM {$table} WHERE name = %s", 'analytics')
    );

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'analytics', 'data' => $data]);
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	

    wp_send_json_success();
}

if (ALPHA_INTEGRATION_MODE === 'validate') {
    wp_send_json_success(['message' => 'Analytics salvo.']); // Sem validação real
}
