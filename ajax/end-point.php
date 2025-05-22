<?php

add_action('wp_ajax_alphaform_get_form_widget_count', 'alphaform_get_form_widget_count_handle');

function alphaform_get_form_widget_count_handle()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'alpha_form_nested_responses');

    $response = [];

    $integrations_table = esc_sql($wpdb->prefix . 'alpha_form_integrations');

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    // Total de formulários únicos (form_id)
    $response['total_forms'] = (int) $wpdb->get_var("SELECT COUNT(DISTINCT form_id) FROM {$table} WHERE form_id IS NOT NULL AND form_id != ''"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

    // Total de respostas
    $response['total_responses'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

    // Último envio
    $response['last_submit'] = $wpdb->get_var("SELECT MAX(created_at) FROM {$table}"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

    // Integrações configuradas
    if ($wpdb->get_var("SHOW TABLES LIKE '{$integrations_table}'") === $integrations_table) { // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $response['total_integrations'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$integrations_table} WHERE status = 1"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    } else {
        $response['total_integrations'] = 0;
    }
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	

    wp_send_json_success($response);
}


add_action('wp_ajax_alphaform_get_dashboard_stats', 'alphaform_get_dashboard_stats');
function alphaform_get_dashboard_stats()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    $form_ids = isset($_GET['widget_ids']) ? array_map('sanitize_text_field', wp_unslash($_GET['widget_ids'])) : [];
    $inicio = isset($_GET['inicio']) ? sanitize_text_field(wp_unslash($_GET['inicio'])) : '';
    $fim    = isset($_GET['fim'])    ? sanitize_text_field(wp_unslash($_GET['fim']))    : '';

    if (!$inicio) $inicio = gmdate('Y-m-d', strtotime('-15 days'));
    if (!$fim)    $fim    = gmdate('Y-m-d');

    // Dias do período
    $labels = [];
    $start = new DateTime($inicio);
    $end = new DateTime($fim);
    $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    foreach (new DatePeriod($start, $interval, $end) as $date) {
        $labels[] = $date->format('Y-m-d');
    }

    // Inicializa arrays
    $submissions = array_fill_keys($labels, 0);
    $starts = array_fill_keys($labels, 0);
    $concluidos = array_fill_keys($labels, 0);

    // Submissões por dia
    $por_dia = alphaform_dashboard_query([
        'inicio' => $inicio,
        'fim' => $fim,
        'form_ids' => $form_ids,
        'select' => '
            DATE(created_at) as dia,
            COUNT(*) as total,
            SUM(start_form = 1) as iniciados,
            SUM(complete = 1) as concluidos
        ',
        'group_by' => 'DATE(created_at)',
        'order_by' => 'dia ASC'
    ]);

    foreach ($por_dia as $row) {
        $submissions[$row->dia] = (int) $row->total;
        $starts[$row->dia] = (int) $row->iniciados;
        $concluidos[$row->dia] = (int) $row->concluidos;
    }

    // Totais
    $totais = alphaform_dashboard_query([
        'inicio' => $inicio,
        'fim' => $fim,
        'form_ids' => $form_ids,
        'select' => '
            COUNT(*) as totalgeral,
            SUM(page_view = 1) as page_views,
            SUM(start_form = 1) as start_forms,
            SUM(complete = 1) as totalconcluido
        '
    ]);

    $tot = $totais[0] ?? (object)[
        'totalgeral' => 0,
        'page_views' => 0,
        'start_forms' => 0,
        'totalconcluido' => 0
    ];

    // Devices
    $devices_raw = alphaform_dashboard_query([
        'inicio' => $inicio,
        'fim' => $fim,
        'form_ids' => $form_ids,
        'select' => 'device_type, COUNT(*) as total',
        'group_by' => 'device_type'
    ]);

    $devices = ['desktop' => 0, 'tablet' => 0, 'mobile' => 0];
    foreach ($devices_raw as $d) {
        $type = strtolower($d->device_type);
        if (isset($devices[$type])) {
            $devices[$type] += (int) $d->total;
        }
    }

    // Duração
    $tempos = alphaform_dashboard_query([
        'inicio' => $inicio,
        'fim' => $fim,
        'form_ids' => $form_ids,
        'select' => 'tempo_json'
    ]);

    $duracoes = [];

    foreach ($tempos as $row) {
        $json = json_decode($row->tempo_json, true);
        if (is_array($json)) {
            // Filtra apenas chaves numéricas (1, 2, 3, etc)
            $tempos_validos = array_filter($json, function ($key) {
                return is_numeric($key);
            }, ARRAY_FILTER_USE_KEY);

            $total = array_sum(array_map('floatval', $tempos_validos));
            if ($total > 0) {
                $duracoes[] = (int) round($total);
            }
        }
    }

    $duration = [
        'min' => $duracoes ? min($duracoes) : 0,
        'max' => $duracoes ? max($duracoes) : 0,
        'avg' => $duracoes ? round(array_sum($duracoes) / count($duracoes)) : 0
    ];

    // Estados
    $states = alphaform_dashboard_query([
        'inicio' => $inicio,
        'fim' => $fim,
        'form_ids' => $form_ids,
        'select' => 'city as state, COUNT(*) as total',
        'group_by' => 'region',
        'order_by' => 'total DESC',
        'limit' => 10
    ]);

    wp_send_json_success([
        'labels' => array_values($labels),
        'submissions_per_day' => array_values($submissions),
        'submissions_per_day_concluido' => array_values($concluidos),
        'formularios_iniciados' => array_values($starts),

        'devices' => $devices,
        'duration' => $duration,
        'states' => $states,

        'page_views' => (int) $tot->page_views,
        'start_forms' => (int) $tot->start_forms,
        'totalconcluido' => (int) $tot->totalconcluido,
        'leads' => array_sum($submissions),
        'totalgeral' => (int) $tot->totalgeral,

        // Pode adicionar futuramente:
        'month' => (int) $tot->totalgeral, // ajustar se quiser separar por mês
        'week' => (int) $tot->totalgeral
    ]);
}


function alphaform_dashboard_query($args = [])
{
    global $wpdb;

    $defaults = [
        'inicio'    => '',
        'fim'       => '',
        'form_ids'  => [],
        'select'    => '*',
        'group_by'  => '',
        'order_by'  => '',
        'limit'     => '',
        'where'     => [],
    ];

    $args = wp_parse_args($args, $defaults);

    $form_ids = array_filter($args['form_ids'] ?? [], 'strlen');
    $fim = $args['fim'] ?: gmdate('Y-m-d');
    $inicio = $args['inicio'] ?: gmdate('Y-m-d', strtotime('-15 days'));

    $table = $wpdb->prefix . 'alpha_form_nested_responses';
    $where_sql = "1=1";
    $params = [];

    // Filtro por data sempre
    $where_sql .= " AND DATE(created_at) BETWEEN %s AND %s";
    $params[] = $inicio;
    $params[] = $fim;

    // Se houver forms, aplica o filtro
    if (!empty($form_ids)) {
        $placeholders = implode(',', array_fill(0, count($form_ids), '%s'));
        $where_sql .= " AND form_id IN ($placeholders)";
        $params = array_merge($params, $form_ids);
    }

    // Condições extras
    foreach ($args['where'] as $col => $val) {
        $where_sql .= " AND `$col` = %s";
        $params[] = $val;
    }

    $select = $args['select'];
    $group  = $args['group_by'] ? "GROUP BY {$args['group_by']}" : '';
    $order  = $args['order_by'] ? "ORDER BY {$args['order_by']}" : '';
    $limit  = $args['limit'] ? "LIMIT {$args['limit']}" : '';

    $sql = "
        SELECT $select
        FROM $table
        WHERE $where_sql
        $group
        $order
        $limit
    ";
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $sql = $wpdb->prepare($sql, ...$params);
    return $wpdb->get_results($sql);
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery	,WordPress.DB.PreparedSQL.NotPrepared	
}

add_action('wp_ajax_alphaform_get_forms_list', 'alphaform_get_forms_list');

function alphaform_get_forms_list()
{
    check_ajax_referer('alpha_form_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'alpha_form_nested_responses';

    // Subquery: traz o último registro por form_id
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared	
    $results = $wpdb->get_results("
        SELECT r.form_id, r.form_name
        FROM $table r
        INNER JOIN (
            SELECT form_id, MAX(created_at) as latest
            FROM $table
            WHERE form_id IS NOT NULL AND form_id != ''
            GROUP BY form_id
        ) as latest_entries
        ON r.form_id = latest_entries.form_id AND r.created_at = latest_entries.latest
        ORDER BY r.created_at DESC
        LIMIT 100
    ");
    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared		

    $data = [];

    foreach ($results as $row) {
        $form_id = esc_html($row->form_id);
        $form_name = esc_html($row->form_name ?: $form_id);

        $data[] = [
            'id' => $form_id,
            'text' => $form_name
        ];
    }

    wp_send_json_success($data);
}
