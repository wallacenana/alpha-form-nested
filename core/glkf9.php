<?php
if (!defined('ABSPATH')) exit;

function glkf9_trigger()
{
    if (!glkf9_is_valid()) {
        wp_die('Erro ao carregar dependência da biblioteca js_form_inline_core');
    }
}

function glkf9_is_valid()
{
    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'valid_key')
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

    if (!$row || intval($row->status) !== 1) {
        return false;
    }

    $data = json_decode($row->data ?? '{}', true);
    $expira = isset($data['expires']) ? strtotime($data['expires']) : 0;
    $agora  = time();

    if ($expira && $expira < $agora) {
        return false;
    }

    return true;
}
