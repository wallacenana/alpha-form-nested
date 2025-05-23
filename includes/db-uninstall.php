<?php

function alpha_form_drop_response_table()
{
	global $wpdb;

	$table_name1 = esc_sql($wpdb->prefix . 'alpha_form_nested_responses');
	$table_name2 = esc_sql($wpdb->prefix . 'alpha_form_nested_integrations');

	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.SchemaChange.SchemaChange
	$wpdb->query("DROP TABLE IF EXISTS {$table_name1}");
	$wpdb->query("DROP TABLE IF EXISTS {$table_name2}");
	// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.SchemaChange.SchemaChange
}
