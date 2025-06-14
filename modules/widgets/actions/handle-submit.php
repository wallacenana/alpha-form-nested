<?php
add_action('wp_ajax_alpha_form_handle_integration', 'alpha_form_handle_integration');
add_action('wp_ajax_nopriv_alpha_form_handle_integration', 'alpha_form_handle_integration');

function alpha_form_handle_integration()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $integration = isset($_POST['integration']) ? sanitize_text_field(wp_unslash($_POST['integration'])) : '';
    $form_id     = isset($_POST['form_id'])     ? sanitize_text_field(wp_unslash($_POST['form_id']))     : '';
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON será decodificado e tratado após
    $data_raw = isset($_POST['data']) ? stripslashes(wp_unslash($_POST['data'])) : '';

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- json_decode usado, campos serão tratados individualmente
    $data_raw = isset($_POST['data']) ? stripslashes(wp_unslash($_POST['data'])) : '';
    $data     = json_decode($data_raw, true);


    if (!$integration || !is_array($data)) {
        wp_send_json_error(['message' => 'Dados inválidos.']);
    }

    if (!empty($data['source_type']) && $data['source_type'] === 'default') {
        global $wpdb;
        $table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT data FROM {$table} WHERE name = %s AND status = 1", $integration),
            ARRAY_A
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	


        if ($row && !empty($row['data'])) {
            $credentials = json_decode($row['data'], true);
            if (is_array($credentials)) {
                $data = array_merge($data, $credentials);
            }
        }
    }

    $path = __DIR__ . '/' . $integration . '.php';

    if (!file_exists($path)) {
        wp_send_json_error(['message' => 'Integração não encontrada.']);
    }

    require_once $path;
    $fn = "alpha_integration_{$integration}";

    if (function_exists($fn)) {
        $result = call_user_func($fn, $form_id, $data);
        wp_send_json_success(['result' => $result]);
    }

    wp_send_json_error(['message' => 'Função de integração ausente.']);
}
