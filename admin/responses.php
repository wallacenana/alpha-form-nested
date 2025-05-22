<?php
if (!current_user_can('manage_options')) {
    wp_die('Acesso negado.');
}

if (
    !isset($_GET['_wpnonce']) ||
    !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'alpha_form_responses_list')
) {
    wp_die('Acesso negado (nonce inválido).');
}

global $wpdb;
$table = $wpdb->prefix . 'alpha_form_nested_responses';

$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval(wp_unslash($_GET['paged']))) : 1;
$offset = ($current_page - 1) * $per_page;

$form_id = isset($_GET['form_id']) ? sanitize_text_field(wp_unslash($_GET['form_id'])) : '';

// Total de registros
$cache_key_total = 'alpha_nested_total_' . ($form_id ?: 'all');
$total = wp_cache_get($cache_key_total, 'alpha_form');

// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
if (false === $total) {
    if ($form_id) {
        $sql_total = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}alpha_form_nested_responses WHERE form_id = %s", $form_id);
    } else {
        $sql_total = "SELECT COUNT(*) FROM {$wpdb->prefix}alpha_form_nested_responses";
    }
    $total = $wpdb->get_var($sql_total);
    wp_cache_set($cache_key_total, $total, 'alpha_form', 600);
}

// Resultados paginados
$cache_key_results = 'alpha_nested_results_' . ($form_id ?: 'all') . "_page_$current_page";
$results = wp_cache_get($cache_key_results, 'alpha_form');

if (false === $results) {
    if ($form_id) {
        $sql_results = $wpdb->prepare(
            "SELECT id, form_id, form_name, session_id, post_id, created_at
             FROM {$wpdb->prefix}alpha_form_nested_responses
             WHERE form_id = %s
             ORDER BY created_at DESC
             LIMIT %d OFFSET %d",
            $form_id, $per_page, $offset
        );

    } else {
        $sql_results = $wpdb->prepare(
            "SELECT id, form_id, form_name, session_id, post_id, created_at
             FROM {$wpdb->prefix}alpha_form_nested_responses
             ORDER BY created_at DESC
             LIMIT %d OFFSET %d",
            $per_page, $offset
        );

    }

    $results = $wpdb->get_results($sql_results);
    wp_cache_set($cache_key_results, $results, 'alpha_form', 300);
}
// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
?>

<div class="wrap alpha-form-wrap">
    <h1 class="wp-heading-inline">Respostas dos Formulários</h1>
    <?php if ($form_id): ?>
        <p>Exibindo respostas de <code><?php echo esc_html($form_id); ?></code></p>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Formulário</th>
                <th>Página</th>
                <th>Data de Envio</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($results): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><?php echo esc_html($row->form_name ?: $row->form_id); ?></td>
                        <td>
                            <?php
                            $post_id = intval($row->post_id);
                            $title = get_the_title($post_id);
                            $link = admin_url('post.php?post=' . $post_id . '&action=elementor');
                            echo $title ? '<a href="' . esc_url($link) . '" target="_blank">' . esc_html($title) . '</a>' : '<em>Não encontrado</em>';
                            ?>
                        </td>
                        <td><?php echo esc_html(gmdate('d/m/Y H:i', strtotime($row->created_at))); ?></td>
                        <td>
                            <a href="<?php echo esc_url(
                                            wp_nonce_url(
                                                admin_url('admin.php?page=alpha-form-view-response&id=' . intval($row->id)),
                                                'alpha_form_view_response',
                                                '_wpnonce'
                                            )
                                        ); ?>" class="button button-small">Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nenhuma resposta encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php
    $total_pages = ceil($total / $per_page);
    if ($total_pages > 1):
        $base_url = admin_url('admin.php?page=alpha-form-responses');
        if ($form_id) $base_url .= '&form_id=' . urlencode($form_id);
        echo '<div class="tablenav"><div class="tablenav-pages">';
        echo wp_kses_post(paginate_links([
            'base' => $base_url . '&paged=%#%',
            'format' => '',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text' => '<i class="dashicons dashicons-arrow-left-alt2"></i>',
            'next_text' => '<i class="dashicons dashicons-arrow-right-alt2"></i>',
        ]));

        echo '</div></div>';
    endif;
    ?>
</div>