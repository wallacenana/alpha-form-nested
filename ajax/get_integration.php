<?php
if (!defined('ABSPATH')) exit;
check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_get_integrations';

$requested = $_POST['integrations'] ?? [];

if (!is_array($requested)) {
    // Permitir fallback de string única
    $requested = [$requested];
}

$placeholders = implode(',', array_fill(0, count($requested), '%s'));

$query = "SELECT name, data FROM $table WHERE status = 1 AND name IN ($placeholders)";
$rows = $wpdb->get_results($wpdb->prepare($query, $requested));

$response = [];

foreach ($rows as $row) {
    $response[$row->name] = json_decode($row->data, true);
}

wp_send_json_success(['integrations' => $response]);
