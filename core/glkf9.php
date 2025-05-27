<?php
if (!defined('ABSPATH')) exit;

// Apenas define a função, sem rodar nada aqui direto.
function glkf9_is_valid()
{
    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'valid_key')
    );

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
