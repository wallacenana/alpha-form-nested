<?php
// Webhook
function alpha_integration_webhook($form_id, $data)
{
    error_log('[WEBHOOK] Dados recebidos: ' . print_r($data, true));

    if (empty($data['url']) || !filter_var($data['url'], FILTER_VALIDATE_URL)) {
        error_log('[WEBHOOK] URL ausente ou inválida.');
        return false;
    }

    $response = wp_remote_post($data['url'], [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode([
            'form_id' => $form_id,
            'data'    => $data['payload'] ?? []
        ])
    ]);

    if (is_wp_error($response)) {
        error_log('[WEBHOOK] Erro na requisição: ' . $response->get_error_message());
        return false;
    }

    return true;
}
