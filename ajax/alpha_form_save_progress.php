<?php
if (!defined('ABSPATH')) exit;
add_action('wp_ajax_alpha_form_save_step', 'alpha_form_save_step');

function alpha_form_save_step()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_nested_responses';

    $form_id       = sanitize_text_field($_POST['form_id']);
    $session_id    = sanitize_text_field($_POST['session_id']);
    $field_key     = sanitize_text_field($_POST['field_key']);
    $device_type   = sanitize_text_field($_POST['device_type']);
    $browser_info  = sanitize_text_field($_POST['browser_info']);
    $formName      = sanitize_text_field($_POST['formName']);
    $value         = json_decode(stripslashes($_POST['value']), true);
    $last_quest    = intval($_POST['last_quest'] ?? 0);
    $post_id       = intval($_POST['post_id'] ?? 0);
    $status        = json_decode(stripslashes($_POST['status']), true);
    $tempo_json_raw = stripslashes($_POST['tempo_json'] ?? '');
    $tempo_data    = json_decode($tempo_json_raw, true);

    $ip      = sanitize_text_field($_POST['ip'] ?? '');
    $city    = sanitize_text_field($_POST['city'] ?? '');
    $region  = sanitize_text_field($_POST['region'] ?? '');
    $country = sanitize_text_field($_POST['country'] ?? '');

    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE session_id = %s AND form_id = %s",
        $session_id,
        $form_id
    ));

    $respostas = [];
    if ($existing && $existing->respostas_json) {
        $respostas = json_decode($existing->respostas_json, true);
    }
    $respostas[$field_key] = $value;
    $respostas_json = wp_json_encode($respostas);

    $data = [
        'post_id'        => $post_id,
        'form_id'        => $form_id,
        'session_id'     => $session_id,
        'ip_address'     => $ip,
        'city'           => $city,
        'region'         => $region,
        'country'        => $country,
        'browser_info'   => $browser_info,
        'device_type'    => $device_type,
        'form_name'      => $formName,
        'page_view'      => intval($status['pageView'] ?? 0),
        'start_form'     => intval($status['startForm'] ?? 0),
        'complete'       => intval($status['complete'] ?? 0),
        'last_quest'     => $last_quest,
        'respostas_json' => $respostas_json,
        'tempo_json'     => wp_json_encode($tempo_data),
        'updated_at'     => current_time('mysql'),
    ];

    if ($existing) {
        $wpdb->update($table, $data, [
            'session_id' => $session_id,
            'form_id' => $form_id,
        ]);
    } else {
        $data['created_at'] = current_time('mysql');
        $wpdb->insert($table, $data);
    }

    wp_send_json_success();
}


add_action('wp_ajax_alpha_form_mark_complete', 'alpha_form_mark_complete');
add_action('wp_ajax_nopriv_alpha_form_mark_complete', 'alpha_form_mark_complete');

function alpha_form_mark_complete()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $form_id    = sanitize_text_field($_POST['form_id'] ?? '');
    $session_id = sanitize_text_field($_POST['session_id'] ?? '');

    if (!$form_id || !$session_id) {
        wp_send_json_error(['message' => 'Dados incompletos.']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_nested_responses';

    $updated = $wpdb->update(
        $table,
        ['complete' => 1, 'updated_at' => current_time('mysql', 1)],
        ['form_id' => $form_id, 'session_id' => $session_id]
    );

    wp_send_json_success(['updated' => $updated]);
}
