<?php
defined('ABSPATH') || exit;

function alpha_form_create_response_table()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $responses_table = $wpdb->prefix . 'alpha_form_nested_responses';
    $integrations_table = $wpdb->prefix . 'alpha_form_nested_integrations';


    $sql1 = "
        CREATE TABLE wp_alpha_form_nested_responses (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        post_id BIGINT,
        form_id VARCHAR(32),
        session_id VARCHAR(64),
        ip_address VARCHAR(45),
        city VARCHAR(100),
        region VARCHAR(100),
        country VARCHAR(100),
        page_view TINYINT(1),
        start_form TINYINT(1),
        complete TINYINT(1),
        last_quest VARCHAR(100),
        tempo_json TEXT,
        respostas_json LONGTEXT,
        created_at DATETIME,
        updated_at DATETIME,
        UNIQUE KEY unique_session (session_id, form_id)
    )
$charset_collate;
    ";

    $sql2 = "
        CREATE TABLE $integrations_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            data LONGTEXT NOT NULL,
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY name (name),
            KEY status (status)
        ) $charset_collate;
    ";


    dbDelta($sql1);
    dbDelta($sql2);
}
