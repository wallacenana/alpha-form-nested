<?php

function alpha_form_drop_response_table()
{
	global $wpdb;

	$table_name1 = esc_sql($wpdb->prefix . 'alpha_form_nested_responses');
	$table_name2 = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query("DROP TABLE IF EXISTS {$table_name1}");

	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query("DROP TABLE IF EXISTS {$table_name2}");
}
