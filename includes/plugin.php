<?php

// Ativação e desinstalação
register_activation_hook(ALPHA_FORM_PATH . 'alpha-form-nested.php', 'alpha_form_activate');
register_uninstall_hook(ALPHA_FORM_PATH . 'alpha-form-nested.php', 'alpha_form_uninstall');

require_once ALPHA_FORM_PATH . 'ajax/handler-core.php';
require_once ALPHA_FORM_PATH . 'ajax/handler-integrations.php';
require_once ALPHA_FORM_PATH . 'ajax/alpha_form_save_progress.php';
require_once ALPHA_FORM_PATH . 'ajax/end-point.php';
require_once ALPHA_FORM_PATH . 'modules/widgets/actions/handle-actions.php';
require_once ALPHA_FORM_PATH . 'modules/widgets/actions/handle-submit.php';

function alpha_form_activate()
{
    require_once ALPHA_FORM_PATH . 'includes/db-install.php';
    alpha_form_create_response_table();
}

function alpha_form_uninstall()
{
    require_once ALPHA_FORM_PATH . 'includes/db-uninstall.php';
    alpha_form_drop_response_table();
}

add_action('admin_enqueue_scripts', 'alpha_form_nested_load_admin_assets');
function alpha_form_nested_load_admin_assets($hook)
{
    // Só carrega se for a página do dashboard
    if (strpos($hook, 'alpha-form-') === false) return;

    // Scripts
    wp_enqueue_script('alpha-dashboard-js', ALPHA_FORM_URL . 'assets/js/alpha-dashboard.js', ['jquery'], '1.0', true);
    wp_localize_script('alpha-dashboard-js', 'alphaFormVars', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('alpha_form_nonce'),
        'pluginUrl' => ALPHA_FORM_PATH,
    ]);

    wp_enqueue_script('chart-js', ALPHA_FORM_URL . 'assets/js/vendor/chart.js', [], '4.4.0', true);
    wp_enqueue_script('select2-js', ALPHA_FORM_URL . 'assets/js/vendor/select2.min.js', ['jquery'], '4.0.13', true);

    // Estilos
    wp_enqueue_style('alpha-dashboard-css', ALPHA_FORM_URL . 'assets/css/alpha-dashboard.css', [], '1.0');
    wp_enqueue_style('select2-css', ALPHA_FORM_URL . 'assets/css/select2.min.css', [], '4.0.13');
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

    wp_enqueue_script(
        'alpha-accordion',
        ALPHA_FORM_URL . 'assets/js/alpha-accordion.js',
        ['elementor-editor', 'elementor-common'],
        '1.0.0',
        true
    );

    wp_localize_script('alpha-form-editor', 'alphaFormVars', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('alpha_form_nonce'),
        'pluginUrl' => ALPHA_FORM_PATH,
    ]);
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

    wp_localize_script('alpha-form-front', 'alphaFormVars', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('alpha_form_nonce'),
        'pluginUrl' => ALPHA_FORM_PATH,
    ]);
});

add_action('elementor/elements/categories_registered', function ($elements_manager) {
    $elements_manager->add_category(
        'alpha-form',
        [
            'title' => 'Alpha Form',
            'icon'  => 'eicon-form-horizontal',
        ]
    );
});

add_action('elementor/frontend/before_enqueue_scripts', function () {
    $path = ALPHA_FORM_PATH . 'assets/js/chart.js';
    wp_enqueue_script(
        'alpha-form-chart',
        ALPHA_FORM_URL . 'assets/js/chart.js',
        [],
        file_exists($path) ? filemtime($path) : time(), // fallback em dev
        true
    );
});



add_action('admin_menu', function () {
    $icon = ALPHA_FORM_URL . 'assets/img/alpha-logo.png';

    add_menu_page(
        'Alpha Form',
        'Alpha Form',
        'manage_options',
        'alpha-form-dashboard',
        'alpha_form_render_dashboard_page',
        $icon,
        60
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'alpha-form-dashboard',
        'alpha_form_render_dashboard_page'
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Integrações',
        'Integrações',
        'manage_options',
        'alpha-form-integrations',
        'alpha_form_render_integrations_page'
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Estatísticas',
        'Estatísticas',
        'manage_options',
        'alpha-form-stats',
        'alpha_form_render_stats_page'
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Formulários',
        'Formulários',
        'manage_options',
        'alpha-form-forms',
        'alpha_form_render_forms_page'
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Respostas',
        null,
        'manage_options',
        'alpha-form-responses',
        'alpha_form_render_responses_page'
    );

    add_submenu_page(
        'alpha-form-dashboard',
        'Visualizar Resposta',
        null,
        'manage_options',
        'alpha-form-view-response',
        'alpha_form_render_view_response_page'
    );
});

add_action('admin_head', function () {
    echo '
	<style>
		#toplevel_page_alpha-form-dashboard .wp-menu-image img {
			width: 20px !important;
			height: 20px !important;
			object-fit: contain;
			padding: 5px 0 0 0;
		}
	</style>';
});

function alpha_form_render_dashboard_page()
{
    include ALPHA_FORM_PATH . 'admin/dashboard.php';
}

function alpha_form_render_integrations_page()
{
    include ALPHA_FORM_PATH . 'admin/integrations.php';
}

function alpha_form_render_stats_page()
{
    include ALPHA_FORM_PATH . 'admin/stats.php';
}

function alpha_form_render_forms_page()
{
    include ALPHA_FORM_PATH . 'admin/forms.php';
}

function alpha_form_render_responses_page()
{
    include ALPHA_FORM_PATH . 'admin/responses.php';
}

function alpha_form_render_view_response_page()
{
    include ALPHA_FORM_PATH . 'admin/view-response.php';
}

function alpha_form_enqueue_ajax_script()
{
    wp_enqueue_script(
        'alpha-form-ajax',
        ALPHA_FORM_URL . 'assets/js/alpha-form.js',
        ['jquery'],
        filemtime(ALPHA_FORM_PATH . 'assets/js/alpha-form.js'),
        true
    );

    wp_localize_script('alpha-form-ajax', 'alphaFormVars', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('alpha_form_nonce'),
        'pluginUrl' => ALPHA_FORM_URL,
    ]);
}
add_action('admin_enqueue_scripts', 'alpha_form_enqueue_ajax_script');
