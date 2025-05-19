<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_alpha_form_validate_integration', 'alpha_handle_validate_integration');
add_action('wp_ajax_alpha_form_save_integration', 'alpha_handle_save_integration');

function alpha_handle_validate_integration()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $type = sanitize_text_field($_POST['integration'] ?? '');
    define('ALPHA_INTEGRATION_MODE', 'validate');

    $path = ALPHA_FORM_PATH . 'ajax/integration/' . $type . '.php';

    if (file_exists($path)) {
        require_once $path;
    } else {
        wp_send_json_error(['message' => 'Integração não encontrada.']);
    }
}

function alpha_handle_save_integration()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $type = sanitize_text_field($_POST['integration'] ?? '');
    define('ALPHA_INTEGRATION_MODE', 'save');

    switch ($type) {
        case 'active-campaign':
        case 'mailchimp':
            require_once ALPHA_FORM_PATH . 'ajax/integration/' . $type . '.php';
            break;

        default:
            wp_send_json_error(['message' => 'Integração inválida.']);
    }
}
