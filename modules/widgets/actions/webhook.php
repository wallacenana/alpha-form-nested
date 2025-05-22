<?php
// Webhook
function alpha_integration_webhook($form_id, $data)
{
    if (empty($data['url']) || !filter_var($data['url'], FILTER_VALIDATE_URL)) {
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
        return false;
    }

    return true;
}
