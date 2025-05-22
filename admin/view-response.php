<?php
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado.');
}

if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'alpha_form_view_response')) {
    wp_die('Acesso negado (nonce inválido).');
}

$response_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$response_id) {
    echo '<div class="notice notice-error"><p>ID da resposta não informado.</p></div>';
    return;
}

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_responses';
$cache_key = 'alpha_nested_response_' . $response_id;

$response = wp_cache_get($cache_key, 'alpha_form');

// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
if (false === $response) {
    $sql = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}alpha_form_nested_responses WHERE id = %d",
        $response_id
    );
    $response = $wpdb->get_row($sql);

    if ($response) {
        wp_cache_set($cache_key, $response, 'alpha_form', 600);
    }
}
// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching


if (!$response) {
    echo '<div class="notice notice-error"><p>Resposta não encontrada.</p></div>';
    return;
}

// Dados brutos salvos
$data = json_decode($response->respostas_json ?? '{}', true);

// Se quiser usar labels do widget original (se existir função equivalente)
// $labels = alphaform_map_labels_from_widget($response->post_id, $response->form_id);
$labels = []; // ou mantém vazio por enquanto
?>

<div class="wrap alpha-form-wrap">
    <h1 class="wp-heading-inline">Detalhes da Resposta</h1>
    <hr class="wp-header-end">

    <table class="widefat fixed striped">
        <tbody>
            <tr>
                <th>ID</th>
                <td><?php echo esc_html($response->id); ?></td>
            </tr>
            <tr>
                <th>Formulário</th>
                <td><?php echo esc_html($response->form_name ?: $response->form_id); ?></td>
            </tr>
            <tr>
                <th>Post ID</th>
                <td><?php echo esc_html($response->post_id); ?></td>
            </tr>
            <tr>
                <th>Data de Envio</th>
                <td><?php echo esc_html(gmdate('d/m/Y H:i', strtotime($response->created_at))); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php
                    if ((int)$response->complete === 1) {
                        echo '<span class="text-success">Concluído</span>';
                    } elseif ((int)$response->start_form === 1) {
                        echo '<span class="text-info">Não Concluído</span>';
                    } elseif ((int)$response->page_view === 1) {
                        echo '<span class="text-warning">Não Iniciado</span>';
                    } else {
                        echo '<span class="text-muted">Desconhecido</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Cidade</th>
                <td><?php echo esc_html($response->city ?: 'Desconhecida'); ?></td>
            </tr>
            <tr>
                <th>Região</th>
                <td><?php echo esc_html($response->region ?: 'Desconhecida'); ?></td>
            </tr>
            <tr>
                <th>País</th>
                <td><?php echo esc_html($response->country ?: 'Desconhecido'); ?></td>
            </tr>
            <tr>
                <th>Dispositivo</th>
                <td><?php echo esc_html($response->device_type ?: 'Não detectado'); ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Respostas</h2>
    <table class="widefat striped">
        <thead>
            <tr>
                <th>Etapa</th>
                <th>Campo</th>
                <th>Resposta</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($data)) {
                foreach ($data as $step => $fields) {
                    if ($step === '__init') continue;

                    // Força cada etapa a ser um array
                    if (is_array($fields)) {
                        foreach ($fields as $field_id => $value) {
                            echo '<tr>';
                            echo '<td>' . esc_html($step) . '</td>';
                            echo '<td>' . esc_html($labels['field_' . $field_id] ?? $field_id) . '</td>';

                            echo '<td>';
                            if (is_array($value)) {
                                echo '<ul style="margin:0; padding-left:1.2em;">';
                                foreach ($value as $item) {
                                    echo '<li>' . esc_html($item) . '</li>';
                                }
                                echo '</ul>';
                            } elseif (is_scalar($value)) {
                                echo esc_html($value);
                            } else {
                                echo '<code>' . esc_html(json_encode($value)) . '</code>';
                            }
                            echo '</td>';

                            echo '</tr>';
                        }
                    }
                }
            } else {
                echo '<tr><td colspan="3"><em>Nenhuma resposta encontrada.</em></td></tr>';
            }
            ?>
        </tbody>
    </table>

</div>