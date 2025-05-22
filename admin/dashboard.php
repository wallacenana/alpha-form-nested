<?php
global $wpdb;
$table = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');
$cache_key = 'alpha_form_valid_key';
$data = wp_cache_get($cache_key);

// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching	
if (false === $data) {
    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_integrations';
    $data = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE name = %s LIMIT 1", 'valid_key')
    );
    if ($data) {
        wp_cache_set($cache_key, $data);
    }
}
// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching	


$license = esc_attr($data['chave'] ?? '');
$status  = intval($row->status ?? 0);
$expires = esc_html($data['expires'] ?? '');
?>

<div class="alpha-topbar">
    <p class="alpha-description">Eleve o padrão dos seus formulários com o Alpha Form Premium. </p>
    <p class="alpha-description"><b>Tenha controle total,
            recursos avançados e uma experiência à altura do que você entrega</b>. Ative agora mesmo sua versão PRO.</p>
    <a href="https://alphaform.com.br/investimento" target="_blank" class="alpha-btn-cta">Seja pro 👑</a>
</div>
<div class="alpha-form-wrap">
    <h1>Validação da Licença Alpha Form</h1>

    <form method="post" id="alpha-form-license-form">
        <?php wp_nonce_field('alpha_form_nonce', 'alpha_form_nonce_field'); ?>

        <p>
            <input type="password" name="license" id="alpha_form_license_key" value="<?php esc_attr($license); ?>" placeholder="Digite sua chave" style="width: 350px;" <?php $license ? 'disabled' : '' ?> />
            <button type="button" id="toggle-edit-license" title="Editar licença">✏️</button>
        </p>

        <p><button type="submit" class="button button-primary">Validar agora</button></p>

        <div id="alpha_form_status_message" style="margin-top: 15px; font-weight: bold;">
            <?php if ($license): ?>
                <?php $status ? '✅ Licença ativa' : '❌ Licença inválida' ?>
                <?php if ($expires): ?><br><small>Expira em: <?php esc_html($expires); ?></small><?php endif; ?>
            <?php endif; ?>
        </div>
    </form>
    <div class="alpha-form-dashboard">
        <h2>Overview</h2>
        <div class="alpha-form-cards">
            <a href="admin.php?page=alpha-form-forms" class="alpha-form-card alpha-skeleton card">
                <div class="alpha-cima">
                    <span class="label">Total de Formulários</span>
                    <div class="icon"><i class="dashicons dashicons-forms"></i></div>
                </div>
                <h3 class="alpha-result" id="alpha-form-count">x</h3>
                <div class="alpha-base">

                    <span class="percent">Ver mais</span>
                </div>
            </a>

            <a href="admin.php?page=alpha-form-forms" class="alpha-form-card alpha-skeleton card">
                <div class="alpha-cima">
                    <span class="label">Total de Respostas</span>
                    <div class="icon"><i class="dashicons dashicons-chart-line"></i></div>
                </div>
                <h3 class="alpha-result" id="alpha-response-count">x</h3>
                <div class="alpha-base">
                    <span class="percent">Ver mais</span>
                </div>
            </a>

            <a href="admin.php?page=alpha-form-integrations" class="alpha-form-card alpha-skeleton card">
                <div class="alpha-cima">
                    <span class="label">Integrações Ativas</span>
                    <div class="icon"><i class="dashicons dashicons-share-alt2"></i></div>
                </div>
                <h3 class="alpha-result" id="alpha-integrations-count">x</h3>
                <div class="alpha-base">

                    <span class="percent">Ver mais</span>
                </div>
            </a>

            <a href="#" target="_blank" class="alpha-form-card alpha-card-promo alpha-skeleton card" id="alpha-promo-card" style="display: none;">
                <div class="alpha-promo-content">
                    <h3 class="alpha-promo-title">Título</h3>
                    <p class="alpha-promo-text">Texto da promoção</p>
                    <span class="alpha-promo-cta">CTA</span>
                </div>
            </a>

        </div>
    </div>
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