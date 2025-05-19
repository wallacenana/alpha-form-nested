<?php
add_action('wp_ajax_alpha_form_save_progress', 'alpha_form_save_progress');
add_action('wp_ajax_nopriv_alpha_form_save_progress', 'alpha_form_save_progress');

function alpha_form_save_progress()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_nested_responses';

    $post_id = intval($_POST['post_id'] ?? 0);
    $form_id = sanitize_text_field($_POST['form_id'] ?? '');
    $session_id = sanitize_text_field($_POST['session_id'] ?? '');
    $page_view = intval($_POST['pageView'] ?? 0);
    $start_form = intval($_POST['startForm'] ?? 0);
    $complete = intval($_POST['complete'] ?? 0);
    $last_quest = sanitize_text_field($_POST['lastQuest'] ?? '');
    $tempo = json_encode($_POST['tempo'] ?? []);
    $respostas = json_encode($_POST['respostas'] ?? []);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $city = sanitize_text_field($_POST['city'] ?? '');
    $region = sanitize_text_field($_POST['region'] ?? '');
    $country = sanitize_text_field($_POST['country'] ?? '');

    $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE session_id = %s AND form_id = %s", $session_id, $form_id));

    $data = compact(
        'post_id',
        'form_id',
        'session_id',
        'ip',
        'city',
        'region',
        'country',
        'page_view',
        'start_form',
        'complete',
        'last_quest',
        'tempo',
        'respostas'
    );

    if ($existing) {
        $wpdb->update($table, $data, ['session_id' => $session_id, 'form_id' => $form_id]);
    } else {
        $wpdb->insert($table, $data);
    }

    wp_send_json_success(['message' => 'Salvo com sucesso']);
}
