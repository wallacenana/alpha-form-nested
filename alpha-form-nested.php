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

require_once ALPHA_FORM_PATH . 'core/glkf9.php';
require_once ALPHA_FORM_PATH . 'core/plk32.php';

// Init
add_action('plugins_loaded', function () {
	glkf9_trigger();
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
		require_once ALPHA_FORM_PATH . 'modules/widgets/controls/base.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Controls\Alpha_Form_Minimal());

		// Formulário
		require_once ALPHA_FORM_PATH . 'modules/widgets/controls/inputs.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Controls\Alpha_Inputs());

		// Botão Next
		require_once ALPHA_FORM_PATH . 'modules/widgets/controls/btn-next.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Controls\Alpha_Next());

		// Botão Prev
		require_once ALPHA_FORM_PATH . 'modules/widgets/controls/btn-prev.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Controls\Alpha_Prev());

		// Barra de progresso
		require_once ALPHA_FORM_PATH . 'modules/widgets/controls/progress.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Controls\Alpha_Progress());

		// Elements
		require_once ALPHA_FORM_PATH . 'modules/widgets/controls/elements.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Controls\Alpha_Elements());

		// concluido
		require_once ALPHA_FORM_PATH . 'modules/widgets/controls/btn-complete.php';
		$widgets_manager->register(new \AlphaForm\Module\Widget\Controls\Alpha_Complete());
	});
});
// Includes principais
require_once ALPHA_FORM_PATH . 'includes/plugin.php';
