<?php

namespace AlphaForm\Module\Widget\Controls;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit;

class Alpha_Prev extends Widget_Base
{

	public function get_name()
	{
		return 'alpha-prev';
	}

	public function get_title()
	{
		return esc_html__('Botão Anterior', 'alpha-form');
	}

	public function get_icon()
	{
		return 'eicon-chevron-left';
	}

	public function get_categories()
	{
		return ['alpha-form'];
	}

	public function get_keywords()
	{
		return ['alpha', 'form', 'prev', 'avançar', 'avanço'];
	}

	protected function register_controls()
	{
		$this->start_controls_section('section_content', [
			'label' => esc_html__('Configurações', 'alpha-form'),
		]);

		$this->add_control('text', [
			'label' => esc_html__('Texto do Botão', 'alpha-form'),
			'type' => Controls_Manager::TEXT,
			'default' => 'Anterior',
			'render_type' => 'template',
			'dynamic' => [
				'active' => true,
			],
			'ai' => [
				'active' => false,
			],
		]);

		$this->add_control('icon', [
			'label' => esc_html__('Ícone', 'alpha-form'),
			'type' => Controls_Manager::ICONS,
			'skin' => 'inline',
			'label_block' => false,
			'default' => [
				'value' => 'fa-solid',
				'library' => 'fa-solid',
			],
			'recommended' => [
				'fa-solid' => [
					'arrow-left',
					'chevron-left',
					'angle-left',
					'caret-left',
					'circle-arrow-left',
					'long-arrow-alt-left',
					'arrow-circle-left',
					'angle-double-left',
					'hand-point-left',
					'step-forward',
				],
				'fa-regular' => [
					'hand-point-left',
					'arrow-alt-circle-left',
				],
			],
		]);

		$this->add_control('icon_position', [
			'label' => esc_html__('Posição do Ícone', 'alpha-form'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'before' => [
					'title' => esc_html__('Antes', 'alpha-form'),
					'icon' => 'eicon-arrow-left',
				],
				'after' => [
					'title' => esc_html__('Depois', 'alpha-form'),
					'icon' => 'eicon-arrow-left',
				],
			],
			'default' => 'after',
			'toggle' => false,
			'condition' => [
				'icon[value]!' => '',
			],
		]);

		$this->add_control('ajax_button_prev', [
			'label'       => __('Atualizar lista de formulários', 'alpha-form'),
			'type'        => \Elementor\Controls_Manager::BUTTON,
			'text'        => __('Atualizar', 'alpha-form'),
			'button_type' => 'success',
			'event'       => 'alphaform:editor:load_widget_id',
		]);

		$this->add_control('form_target', [
			'label' => esc_html__('Formulário Alvo', 'alpha-form'),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => [],
			'label_block' => true,
		]);

		$this->end_controls_section();


		// seção style
		$this->start_controls_section('section_style', [
			'label' => esc_html__('Estilo do Botão', 'alpha-form'),
			'tab' => Controls_Manager::TAB_STYLE,
		]);

		$this->start_controls_tabs('style_tabs');

		$this->start_controls_tab('style_normal_tab', [
			'label' => esc_html__('Normal', 'alpha-form'),
		]);

		$this->add_control('text_color', [
			'label' => esc_html__('Cor do Texto', 'alpha-form'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_PRIMARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-prev, {{WRAPPER}} .alpha-form-prev svg' => 'color: {{VALUE}}; fill: {{VALUE}}',
			],
		]);

		$this->add_control('bg_color', [
			'label' => esc_html__('Cor de Fundo', 'alpha-form'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_SECONDARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-prev' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('style_hover_tab', [
			'label' => esc_html__('Hover', 'alpha-form'),
		]);

		$this->add_control('text_color_hover', [
			'label' => esc_html__('Texto (Hover)', 'alpha-form'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_PRIMARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-prev:hover,{{WRAPPER}} .alpha-form-prev:hover svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
			],
		]);

		$this->add_control('bg_color_hover', [
			'label' => esc_html__('Fundo (Hover)', 'alpha-form'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_SECONDARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-prev:hover' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'button_typography',
			'selector' => '{{WRAPPER}} .alpha-form-prev',
			'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY],
		]);

		$this->add_group_control(Group_Control_Border::get_type(), [
			'name' => 'button_border',
			'selector' => '{{WRAPPER}} .alpha-form-prev',
		]);

		$this->add_responsive_control('padding', [
			'label' => esc_html__('Padding', 'alpha-form'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'default' => [
				'top' => 12,
				'left' => 24,
				'bottom' => 12,
				'left' => 24,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-prev' => 'padding: {{TOP}}{{UNIT}} {{left}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control(
			'heading_form_item_title_icon_btn_n',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__('Icon', 'alpha-form'),
				'separator' => 'before',
			]
		);

		$this->add_control('icon_size', [
			'label' => esc_html__('Tamanho do Ícone', 'alpha-form'),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 8,
				'unit' => 'px',
			],
			'range' => [
				'px' => [
					'min' => 8,
					'max' => 64,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-prev .alpha-form-button-icon svg' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('icon_spacing', [
			'label' => esc_html__('Espaçamento entre Ícone e Texto', 'alpha-form'),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'size' => 10,
				'unit' => 'px',
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-prev .alpha-form-button-inner' => 'gap: {{SIZE}}{{UNIT}};',
			],
		]);


		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$form_target = $settings['form_target'] ?? '';

		$this->add_render_attribute('button', [
			'class' => 'alpha-form-prev',
			'type'  => 'button',
			'data-alpha' => 'prev',
			'data-a-f-target' => esc_attr($form_target),
		]);

		echo '<button ' . $this->get_render_attribute_string('button') . '>';

		// Abre o wrapper do conteúdo do botão
		echo '<span class="alpha-form-button-inner">';

		// Ícone antes do texto
		if (!empty($settings['icon']['value']) && $settings['icon_position'] === 'before') {
			echo '<span class="alpha-form-button-icon before">';
			Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
			echo '</span>';
		}

		// Texto do botão
		if (!empty($settings['text'])) {
			echo '<span class="alpha-form-button-text">' . esc_html($settings['text']) . '</span>';
		}

		// Ícone depois do texto
		if (!empty($settings['icon']['value']) && $settings['icon_position'] === 'after') {
			echo '<span class="alpha-form-button-icon after">';
			Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
			echo '</span>';
		}

		// Fecha o wrapper
		echo '</span></button>';
	}
}
