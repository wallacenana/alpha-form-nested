<?php
if (!defined('ABSPATH')) exit;

check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

if (ALPHA_INTEGRATION_MODE === 'save') {
    $pixel_id = isset($_POST['pixel_id']) ? sanitize_text_field(wp_unslash($_POST['pixel_id'])) : '';

    $data = json_encode(['pixel_id' => $pixel_id]);

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM {$table} WHERE name = %s", 'facebook')
    );

    if ($exists) {
        $wpdb->update($table, ['data' => $data], ['id' => $exists]);
    } else {
        $wpdb->insert($table, ['name' => 'facebook', 'data' => $data]);
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    wp_send_json_success();
}

if (ALPHA_INTEGRATION_MODE === 'validate') {
    // Não tem validação real de pixel, então retorna sempre sucesso
    wp_send_json_success(['message' => 'Pixel salvo.']);
}
