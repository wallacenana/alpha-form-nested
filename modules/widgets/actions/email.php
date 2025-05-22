<?php

function alpha_integration_email($form_id, $data)
{
    error_log('[EMAIL] Dados recebidos: ' . print_r($data, true));

    $subject = sanitize_text_field($data['email_subject'] ?? 'Nova submissão recebida');

    // Destino
    if (!empty($data['email_type']) && $data['email_type'] === 'custom') {
        $to = sanitize_text_field($data['custom_emails'] ?? '');
    } else {
        $to = get_option('admin_email');
    }

    if (!$to) {
        error_log('[EMAIL] Nenhum destinatário definido.');
        return false;
    }

    // Dados enviados (campos do form)
    $campos = $data['data'] ?? [];
    if (!is_array($campos) || empty($campos)) {
        error_log('[EMAIL] Nenhum dado para enviar.');
        return false;
    }

    // Monta corpo do email
    $mensagem = "Segue abaixo os dados do formulário:\n\n";
    foreach ($campos as $campo => $valor) {
        if (is_array($valor)) {
            $valor = implode(', ', $valor);
        }
        $mensagem .= ucfirst($campo) . ': ' . $valor . "\n";
    }

    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    $enviado = wp_mail($to, $subject, $mensagem, $headers);

    if ($enviado) {
        error_log('[EMAIL] Email enviado com sucesso para: ' . $to);
        return true;
    } else {
        error_log('[EMAIL] Falha ao enviar email para: ' . $to);
        return false;
    }
}
