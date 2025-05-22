<?php

function alpha_integration_email($form_id, $data)
{
    $subject = sanitize_text_field($data['email_subject'] ?? 'Nova submissão recebida');

    // Destino
    if (!empty($data['email_type']) && $data['email_type'] === 'custom') {
        $to = sanitize_text_field($data['custom_emails'] ?? '');
    } else {
        $to = get_option('admin_email');
    }

    if (!$to) {
        return false;
    }

    // Dados enviados (campos do form)
    $campos = $data['data'] ?? [];
    if (!is_array($campos) || empty($campos)) {
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
        return true;
    } else {
        return false;
    }
}
