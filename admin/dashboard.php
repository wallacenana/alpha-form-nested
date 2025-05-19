<?php
global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_integrations';
$row = $wpdb->get_row("SELECT * FROM $table WHERE name = 'valid_key' LIMIT 1");

$data = json_decode($row->data ?? '{}', true);
$license = esc_attr($data['chave'] ?? '');
$status  = intval($row->status ?? 0);
$expires = esc_html($data['expires'] ?? '');
?>

<div class="wrap">
    <h1>Validação da Licença Alpha Form</h1>

    <form method="post" id="alpha-form-license-form">
        <?php wp_nonce_field('alpha_form_nonce', 'alpha_form_nonce_field'); ?>

        <p>
            <input type="password" name="license" id="alpha_form_license_key" value="<?= $license ?>" placeholder="Digite sua chave" style="width: 350px;" <?= $license ? 'disabled' : '' ?> />
            <button type="button" id="toggle-edit-license" title="Editar licença">✏️</button>
        </p>

        <p><button type="submit" class="button button-primary">Validar agora</button></p>

        <div id="alpha_form_status_message" style="margin-top: 15px; font-weight: bold;">
            <?php if ($license): ?>
                <?= $status ? '✅ Licença ativa' : '❌ Licença inválida' ?>
                <?php if ($expires): ?><br><small>Expira em: <?= $expires ?></small><?php endif; ?>
            <?php endif; ?>
        </div>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('alpha_form_license_key');
        const toggleButton = document.getElementById('toggle-edit-license');
        if (!input || !toggleButton) return;

        let isEditing = false;

        toggleButton.addEventListener('click', () => {
            if (!isEditing) {
                if (confirm("Deseja realmente editar a chave da licença?")) {
                    input.removeAttribute('disabled');
                    input.setAttribute('type', 'text');
                    input.focus();
                    isEditing = true;
                    toggleButton.textContent = '🔒';
                }
            } else {
                input.setAttribute('disabled', 'true');
                input.setAttribute('type', 'password');
                isEditing = false;
                toggleButton.textContent = '✏️';
            }
        });
    });
</script>