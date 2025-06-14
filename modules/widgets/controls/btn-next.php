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

class Alpha_Next extends Widget_Base
{

	public function get_name()
	{
		return 'alpha-next';
	}

	public function get_title()
	{
		return esc_html__('Botão Próximo', 'alpha-form-nested');
	}

	public function get_icon()
	{
		return 'eicon-chevron-right';
	}

	public function get_categories()
	{
		return ['alpha-form-nested'];
	}

	public function get_keywords()
	{
		return ['alpha', 'form', 'next', 'avançar', 'avanço'];
	}

	protected function register_controls()
	{
		$this->start_controls_section('section_content', [
			'label' => esc_html__('Configurações', 'alpha-form-nested'),
		]);

		$this->add_control('text', [
			'label' => esc_html__('Texto do Botão', 'alpha-form-nested'),
			'type' => Controls_Manager::TEXT,
			'default' => 'Próximo',
			'render_type' => 'template',
			'dynamic' => [
				'active' => true,
			],
			'ai' => [
				'active' => false,
			],
		]);

		$this->add_control('icon_next', [
			'label' => esc_html__('Ícone', 'alpha-form-nested'),
			'type' => Controls_Manager::ICONS,
			'skin' => 'inline',
			'label_block' => false,
			'default' => [
				'value' => 'fa-solid',
				'library' => 'fa-solid',
			],
			'recommended' => [
				'fa-solid' => [
					'arrow-right',
					'chevron-right',
					'angle-right',
					'caret-right',
					'circle-arrow-right',
					'long-arrow-alt-right',
					'arrow-circle-right',
					'angle-double-right',
					'hand-point-right',
					'step-forward',
				],
				'fa-regular' => [
					'hand-point-right',
					'arrow-alt-circle-right',
				],
			],
		]);

		$this->add_control('icon_position_next', [
			'label' => esc_html__('Posição do Ícone', 'alpha-form-nested'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'before' => [
					'title' => esc_html__('Antes', 'alpha-form-nested'),
					'icon' => 'eicon-arrow-left',
				],
				'after' => [
					'title' => esc_html__('Depois', 'alpha-form-nested'),
					'icon' => 'eicon-arrow-right',
				],
			],
			'default' => 'after',
			'toggle' => false,
			'condition' => [
				'icon_next[value]!' => '',
			],
		]);

		$this->add_control('ajax_button', [
			'label'       => __('Atualizar lista de formulários', 'alpha-form-nested'),
			'type'        => \Elementor\Controls_Manager::BUTTON,
			'text'        => __('Atualizar', 'alpha-form-nested'),
			'button_type' => 'success',
			'event'       => 'alphaform:editor:load_widget_id',
		]);

		$this->add_control('form_target', [
			'label' => esc_html__('Formulário Alvo', 'alpha-form-nested'),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => [],
			'label_block' => true,
		]);

		$this->end_controls_section();


		// seção style
		$this->start_controls_section('section_style', [
			'label' => esc_html__('Estilo do Botão', 'alpha-form-nested'),
			'tab' => Controls_Manager::TAB_STYLE,
		]);

		$this->start_controls_tabs('style_tabs');

		$this->start_controls_tab('style_normal_tab', [
			'label' => esc_html__('Normal', 'alpha-form-nested'),
		]);

		$this->add_control('text_color', [
			'label' => esc_html__('Cor do Texto', 'alpha-form-nested'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_PRIMARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-next, {{WRAPPER}} .alpha-form-next svg' => 'color: {{VALUE}}; fill: {{VALUE}}',
			],
		]);

		$this->add_control('bg_color', [
			'label' => esc_html__('Cor de Fundo', 'alpha-form-nested'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_SECONDARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-next' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('style_hover_tab', [
			'label' => esc_html__('Hover', 'alpha-form-nested'),
		]);

		$this->add_control('text_color_hover', [
			'label' => esc_html__('Texto', 'alpha-form-nested'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_PRIMARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-next:hover,{{WRAPPER}} .alpha-form-next:hover svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
			],
		]);

		$this->add_control('bg_color_hover', [
			'label' => esc_html__('Fundo', 'alpha-form-nested'),
			'type' => Controls_Manager::COLOR,
			'global' => ['default' => Global_Colors::COLOR_SECONDARY],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-next:hover' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'button_typography',
			'selector' => '{{WRAPPER}} .alpha-form-next',
			'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY],
		]);

		$this->add_group_control(Group_Control_Border::get_type(), [
			'name' => 'button_border',
			'selector' => '{{WRAPPER}} .alpha-form-next',
		]);

		$this->add_control('btn_width-next', [
			'label' => __('Largura automática', 'alpha-form'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __('Sim', 'alpha-form'),
			'label_off' => __('Não', 'alpha-form'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		$this->add_responsive_control('btn_widthnext', [
			'label' => __('Largura do botão', 'alpha-form'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'em', 'rem', 'vw'],
			'range' => [
				'px' => ['min' => 10, 'max' => 1000],
				'%'  => ['min' => 5, 'max' => 100],
				'em' => ['min' => 1, 'max' => 50],
				'rem' => ['min' => 1, 'max' => 50],
				'vw' => ['min' => 5, 'max' => 100],
			],
			'default' => [
				'size' => 180,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-next' => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'btn_width-next!' => 'yes',
			],
		]);

		$this->add_responsive_control('padding', [
			'label' => esc_html__('Padding', 'alpha-form-nested'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'default' => [
				'top' => 12,
				'right' => 24,
				'bottom' => 12,
				'left' => 24,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .alpha-form-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control(
			'heading_form_item_title_icon_btn_n',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__('Icon', 'alpha-form-nested'),
				'separator' => 'before',
			]
		);

		$this->add_control('icon_size', [
			'label' => esc_html__('Tamanho do Ícone', 'alpha-form-nested'),
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
				'{{WRAPPER}} .alpha-form-next .alpha-form-button-icon svg' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('icon_spacing', [
			'label' => esc_html__('Espaçamento entre Ícone e Texto', 'alpha-form-nested'),
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
				'{{WRAPPER}} .alpha-form-next .alpha-form-button-inner' => 'gap: {{SIZE}}{{UNIT}};',
			],
		]);


		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$form_target = $settings['form_target'] ?? '';

		echo '<button class="alpha-form-next" type="button" data-alpha="next" data-a-f-target=' . esc_attr($form_target) . '>';

		// Abre o wrapper do conteúdo do botão
		echo '<span class="alpha-form-button-inner">';

		// Ícone antes do texto
		if (!empty($settings['icon_next']['value']) && $settings['icon_position_next'] === 'before') {
			echo '<span class="alpha-form-button-icon before">';
			Icons_Manager::render_icon($settings['icon_next'], ['aria-hidden' => 'true']);
			echo '</span>';
		}

		// Texto do botão
		if (!empty($settings['text'])) {
			echo '<span class="alpha-form-button-text">' . esc_html($settings['text']) . '</span>';
		}

		// Ícone depois do texto
		if (!empty($settings['icon_next']['value']) && $settings['icon_position_next'] === 'after') {
			echo '<span class="alpha-form-button-icon after">';
			Icons_Manager::render_icon($settings['icon_next'], ['aria-hidden' => 'true']);
			echo '</span>';
		}

		// Fecha o wrapper
		echo '</span></button>';
	}
}
