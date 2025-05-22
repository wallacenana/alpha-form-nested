<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_alpha_form_save_step', 'alpha_form_save_step');

function alpha_form_save_step()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_responses');

    $form_id       = isset($_POST['form_id'])      ? sanitize_text_field(wp_unslash($_POST['form_id'])) : '';
    $session_id    = isset($_POST['session_id'])   ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';
    $field_key     = isset($_POST['field_key'])    ? sanitize_text_field(wp_unslash($_POST['field_key'])) : '';
    $device_type   = isset($_POST['device_type'])  ? sanitize_text_field(wp_unslash($_POST['device_type'])) : '';
    $browser_info  = isset($_POST['browser_info']) ? sanitize_text_field(wp_unslash($_POST['browser_info'])) : '';
    $formName      = isset($_POST['formName'])     ? sanitize_text_field(wp_unslash($_POST['formName'])) : '';

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $value = isset($_POST['value']) ? json_decode(stripslashes(wp_unslash($_POST['value'])), true) : [];

    $last_quest    = isset($_POST['last_quest']) ? intval($_POST['last_quest']) : 0;
    $post_id       = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $status        = isset($_POST['status']) ? json_decode(stripslashes(wp_unslash($_POST['status'])), true) : [];

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $tempo_json_raw = isset($_POST['tempo_json']) ? stripslashes(wp_unslash($_POST['tempo_json'])) : '';
    $tempo_data     = json_decode($tempo_json_raw, true);

    $ip      = isset($_POST['ip'])      ? sanitize_text_field(wp_unslash($_POST['ip'])) : '';
    $city    = isset($_POST['city'])    ? sanitize_text_field(wp_unslash($_POST['city'])) : '';
    $region  = isset($_POST['region'])  ? sanitize_text_field(wp_unslash($_POST['region'])) : '';
    $country = isset($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : '';

    if (empty($form_id) || empty($session_id)) {
        wp_send_json_error(['message' => 'Dados obrigatórios ausentes.']);
    }

    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_responses');
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    $existing = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table} WHERE session_id = %s AND form_id = %s",
            $session_id,
            $form_id
        )
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	


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

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    if ($existing) {
        $wpdb->update($table, $data, [
            'session_id' => $session_id,
            'form_id' => $form_id,
        ]);
    } else {
        $data['created_at'] = current_time('mysql');
        $wpdb->insert($table, $data);
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    wp_send_json_success();
}


add_action('wp_ajax_alpha_form_mark_complete', 'alpha_form_mark_complete');
add_action('wp_ajax_nopriv_alpha_form_mark_complete', 'alpha_form_mark_complete');

function alpha_form_mark_complete()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $form_id    = isset($_POST['form_id']) ? sanitize_text_field(wp_unslash($_POST['form_id'])) : '';
    $session_id = isset($_POST['session_id']) ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';


    if (!$form_id || !$session_id) {
        wp_send_json_error(['message' => 'Dados incompletos.']);
    }

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_responses');

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
    $updated = $wpdb->update(
        $table,
        ['complete' => 1, 'updated_at' => current_time('mysql', 1)],
        ['form_id' => $form_id, 'session_id' => $session_id]
    );
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    wp_send_json_success(['updated' => $updated]);
}
