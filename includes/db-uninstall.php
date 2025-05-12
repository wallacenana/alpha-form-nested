<?php

function alpha_form_drop_response_table() {
	global $wpdb;

	$table_name1 = $wpdb->prefix . 'alpha_form_nested_responses';
	$table_name2 = $wpdb->prefix . 'alpha_form_nested_integrations';

	$wpdb->query("DROP TABLE IF EXISTS $table_name1");
	$wpdb->query("DROP TABLE IF EXISTS $table_name2");
}
