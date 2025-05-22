<?php
if (!defined('ABSPATH')) exit;

check_ajax_referer('alpha_form_nonce', 'nonce');

global $wpdb;
$table = esc_sql($wpdb->prefix . 'alpha_form_get_integrations');

$raw = isset($_POST['integrations']) ? wp_unslash($_POST['integrations']) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized	

if (!is_array($raw)) {
    $raw = [$raw];
}

$requested = array_map('sanitize_text_field', $raw);

$requested = array_map(function ($item) {
    return sanitize_text_field(wp_unslash($item));
}, $requested);

// Se nada for passado, evita erro
if (empty($requested)) {
    wp_send_json_success(['integrations' => []]);
}

$placeholders = implode(',', array_fill(0, count($requested), '%s'));

$query = "SELECT name, data FROM $table WHERE status = 1 AND name IN ($placeholders)"; // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
$rows = $wpdb->get_results($wpdb->prepare($query, $requested));
// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	

$response = [];

foreach ($rows as $row) {
    $response[$row->name] = json_decode($row->data, true);
}

wp_send_json_success(['integrations' => $response]);
