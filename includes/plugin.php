<?php

// Ativação e desinstalação
register_activation_hook(ALPHA_FORM_PATH . 'alpha-form.php', 'alpha_form_activate');
register_uninstall_hook(ALPHA_FORM_PATH . 'alpha-form.php', 'alpha_form_uninstall');

function alpha_form_activate()
{
    require_once ALPHA_FORM_PATH . 'includes/db-install.php';
    alpha_form_create_response_table();
}

function alpha_form_uninstall()
{
    error_log("teste uni");
    require_once ALPHA_FORM_PATH . 'includes/db-uninstall.php';
    alpha_form_drop_response_table();
}

// Editor (painel lateral do Elementor)
add_action('elementor/preview/enqueue_styles', function () {
	wp_enqueue_style(
		'alpha-form-editor-style',
		ALPHA_FORM_URL . 'assets/css/alpha-editor-style.css',
		[],
		'1.0.1'
	);
});

// Preview do editor e frontend
add_action('elementor/frontend/after_enqueue_styles', function () {
	wp_enqueue_style(
		'alpha-form-style',
		ALPHA_FORM_URL . 'assets/css/alpha-style.css',
		[],
		'1.0.0'
	);
});


// Scripts e estilos
add_action('elementor/editor/after_enqueue_scripts', function () {
    wp_enqueue_script(
        'alpha-form-editor',
        ALPHA_FORM_URL . 'assets/js/alpha-editor.js',
        ['elementor-editor', 'elementor-common'],
        '1.0.0',
        true
    );
});
add_action('elementor/editor/before_enqueue_scripts', function () {
    wp_enqueue_script(
        'alpha-accordion',
        ALPHA_FORM_URL . 'assets/js/alpha-accordion.js',
        ['elementor-editor', 'elementor-common'],
        '1.0.0',
        true
    );
});


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'alpha-form-front',
        ALPHA_FORM_URL . 'assets/js/alpha-front.js',
        [],
        '1.0.0',
        true
    );
    wp_enqueue_style(
        'alpha-form-front-style',
        ALPHA_FORM_URL . 'assets/css/alpha-front-style.css',
        [],
        '1.0.0'
    );
});

add_action('elementor/elements/categories_registered', function ($elements_manager) {
    if (! $elements_manager->get_category('alpha-form')) {
        $elements_manager->add_category(
            'alpha-form',
            [
                'title' => 'Alpha Form',
                'icon'  => 'eicon-form-horizontal',
            ]
        );
    }
});
