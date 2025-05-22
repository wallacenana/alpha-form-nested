<?php

if (!defined('ABSPATH')) exit;

function alpha_form_get_widget_totals_nested()
{
    global $wpdb;

    $table = $wpdb->prefix . 'alpha_form_nested_responses';
    $cache_key = 'alpha_form_widget_totals_nested';
    $cache_group = 'alpha_form';

    $results = wp_cache_get($cache_key, $cache_group);

    if (false === $results) {
        $results = $wpdb->get_results("
            SELECT 
                form_id, 
                MAX(form_name) AS form_name, 
                MAX(post_id) AS post_id,
                COUNT(*) AS total
            FROM $table
            WHERE form_id IS NOT NULL AND form_id != ''
            GROUP BY form_id
        ", ARRAY_A);

        if (!empty($results)) {
            wp_cache_set($cache_key, $results, $cache_group, 300);
        }
    }

    return $results;
}

$results = alpha_form_get_widget_totals_nested();

?>

<div class="wrap alpha-form-wrap">
    <h1 class="wp-heading-inline">Formulários</h1>
    <p class="description">Lista de todos os formulários enviados pelo Alpha Form, agrupados por <strong>form_id</strong>.</p>

    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th>Nome do Formulário</th>
                <th>Página</th>
                <th width="15%">Respostas</th>
                <th width="15%">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($results)) : ?>
                <?php foreach ($results as $row) :
                    $post_id = intval($row['post_id']);
                    $post_title = get_the_title($post_id);
                    $post_url = admin_url('post.php?post=' . $post_id . '&action=elementor');
                    $form_id = esc_html($row['form_id']);
                ?>
                    <tr>
                        <td><code><?php echo $form_id; ?></code></td>
                        <td><?php echo esc_html($row['form_name']) ?: '<em>Sem nome</em>'; ?></td>
                        <td>
                            <?php if ($post_title && $post_url) : ?>
                                <a href="<?php echo esc_url($post_url); ?>" target="_blank"><?php echo esc_html($post_title); ?></a>
                            <?php else : ?>
                                <em>Não encontrado</em>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo intval($row['total']); ?></strong></td>
                        <td>
                            <a href="<?php echo esc_url(
                                            wp_nonce_url(
                                                admin_url('admin.php?page=alpha-form-responses&form_id=' . urlencode($form_id)),
                                                'alpha_form_responses_list',
                                                '_wpnonce'
                                            )
                                        ); ?>" class="button">Ver Respostas</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">Nenhum formulário encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
