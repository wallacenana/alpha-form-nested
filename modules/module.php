<?php
namespace AlphaForm\Module\Form;

use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Core\Base\Module as BaseModule;
use AlphaForm\Module\Form\Controls\Control_Nested_Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const EXPERIMENT_NAME = 'alphaform-nested-elements';

	public static function get_experimental_data() {
		return [
			'name' => self::EXPERIMENT_NAME,
			'title' => 'AlphaForm Nested Elements',
			'description' => 'Suporte a elementos aninhados personalizados para Alpha Form.',
			'release_status' => Experiments_Manager::RELEASE_STATUS_STABLE,
			'default' => Experiments_Manager::STATE_ACTIVE,
			'dependencies' => [ 'container' ],
		];
	}

	public function get_name() {
		return self::EXPERIMENT_NAME;
	}

	public function __construct() {
		parent::__construct();

		add_action( 'elementor/controls/register', function ( $controls_manager ) {
			// Garanta que o controle personalizado esteja incluído corretamente
			if ( class_exists( Control_Nested_Repeater::class ) ) {
				$controls_manager->register( new Control_Nested_Repeater() );
			}
		} );
	}

	public static function init() {
		new self();
	}
}

// Inicializa automaticamente o módulo ao ser carregado
Module::init();
