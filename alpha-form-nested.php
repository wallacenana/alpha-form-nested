<?php

/**
 * Plugin Name: Alpha Form
 * Description: Widget customizado com elementos aninhados para o Alpha Form.
 * Version: 2.0.0
 * Author: Wallace Tavares
 */

if (!defined('ABSPATH')) {
	exit;
}

// Constantes de caminho e URL
define('ALPHA_FORM_PATH', plugin_dir_path(__FILE__));
define('ALPHA_FORM_URL', plugin_dir_url(__FILE__));

// Init
add_action('plugins_loaded', function () {
	if (!defined('ELEMENTOR_PATH')) {
		add_action('admin_notices', function () {
			echo '<div class="notice notice-error"><p><strong>Alpha Form:</strong> requer Elementor ativo.</p></div>';
		});
		return;
	}

	// Inclui o módulo do Alpha Form
	require_once ALPHA_FORM_PATH . 'modules/module.php';

	// Força a ativação do experimento se necessário
	add_action('elementor/experiments/active_experiments', function ($experiments_manager) {
		$experiments_manager->add_experiment(\AlphaForm\Module\Form\Module::get_experimental_data());
	});

	// Inclui e registra o widget
	add_action('elementor/widgets/register', function ($widgets_manager) {
		//Base do form
		require_once ALPHA_FORM_PATH . 'modules/widgets/widget-base-form.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Alpha_Form_Minimal());

		// Botão Next
		require_once ALPHA_FORM_PATH . 'modules/widgets/widget-alpha-next.php';
		$widgets_manager->register(new \AlphaForm\Modules\Widgets\Alpha_Next());

		// Botão Prev
		require_once ALPHA_FORM_PATH . 'modules/widgets/widget-alpha-prev.php';
		$widgets_manager->register(new \AlphaForm\Modules\Widgets\Alpha_Prev());

		// Formulário
		require_once ALPHA_FORM_PATH . 'modules/widgets/widget-alpha-form.php';
		$widgets_manager->register(new \AlphaForm\Modules\Widgets\Alpha_Form());

		// Barra de progresso
		require_once ALPHA_FORM_PATH . 'modules/widgets/widget-alpha-progress.php';
		$widgets_manager->register(new \AlphaForm\Modules\Widgets\Alpha_Progress());
	});
});

// Includes principais
require_once ALPHA_FORM_PATH . 'includes/plugin.php';
