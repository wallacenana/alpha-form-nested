<?php

namespace AlphaForm\Module\Widget\Controls;

use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
	exit;
}

class Alpha_Complete extends Widget_Base
{
	public function get_name()
	{
		return 'alpha-complete';
	}

	public function get_title()
	{
		return esc_html__('Alpha Completo', 'alpha-form');
	}

	public function get_icon()
	{
		return 'eicon-check';
	}

	public function get_keywords()
	{
		return ['nested', 'form', 'toggle', 'alpha', 'formulario', 'enviar', 'envio'];
	}

	public function get_categories()
	{
		return ['alpha-form'];
	}

	public function register_controls()
	{
		$this->start_controls_section(
			'section_form_fields',
			[
				'label' => __('Campos do Formulário', 'alpha-form'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'field_label_complete',
			[
				'label' => __('Título', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Concluido com sucesso',
				'label_block' => true,
			]
		);
		$this->add_control(
			'field_descricao_complete',
			[
				'label' => __('Descrição', 'alpha-form'),
				'type' => Controls_Manager::WYSIWYG,
				'default' => 'Clique no botão abaixo para enviar'
			]
		);


		$this->add_control(
			'complete_button_text',
			[
				'label' => __('Texto do botão', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Enviar',
				'placeholder' => 'ex: Enviar',
			]
		);

		$this->add_control('icon_complete', [
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

		$this->add_control('btn_icon_position_complete', [
			'label' => esc_html__('Posição do Ícone', 'alpha-form'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'before' => [
					'title' => esc_html__('Antes', 'alpha-form'),
					'icon' => 'eicon-arrow-left',
				],
				'after' => [
					'title' => esc_html__('Depois', 'alpha-form'),
					'icon' => 'eicon-arrow-right',
				],
			],
			'default' => 'after',
			'toggle' => false,
		]);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_input_section_complete',
			[
				'label' => __('Caixa geral', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_gap_complete',
			[
				'label' => __('Espaço entre itens', 'alpha-form'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'range' => [
					'px' => ['min' => 0, 'max' => 100],
					'%'  => ['min' => 0, 'max' => 100],
					'em' => ['min' => 0, 'max' => 10],
				],
				'default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .alpha-inputs' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'style_label_section_complete',
			[
				'label' => __('Título', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'h3_typography_complete',
				'selector' => '{{WRAPPER}} .alpha-form-titulo',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_control(
			'label_text_color_complete',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-form-titulo' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->add_responsive_control(
			'label_text_alinhamento_complete',
			[
				'label' => esc_html__('Alinhamento', 'alpha-form'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'alpha-form'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'alpha-form'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'alpha-form'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .alpha-form-titulo' => 'display:block; width: 100%; text-align: {{VALUE}}!important',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_descricao_section_complete',
			[
				'label' => __('Descrição', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'p_typography_complete',
				'selector' => '{{WRAPPER}} .alpha-form-description p',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);
		$this->add_control(
			'descricao_text_color_complete',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-form-description p' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],

			]
		);
		$this->add_responsive_control(
			'descricao_text_alinhamento_complete',
			[
				'label' => esc_html__('Alinhamento', 'alpha-form'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'alpha-form'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'alpha-form'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'alpha-form'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .alpha-form-description' => 'display:block; width: 100%; text-align: {{VALUE}}!important',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_buttons_section_complete',
			[
				'label' => __('Botão', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'btn_typography_complete',
				'selector' => '{{WRAPPER}} .alpha-form-next.form',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		// Aba ativo
		$this->start_controls_tabs('tabs_button_styles_complete');

		// Aba Normal
		$this->start_controls_tab(
			'tab_button_normal_complete',
			[
				'label' => __('Normal', 'alpha-form'),
			]
		);
		$this->add_control(
			'btn_text_color_complete',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .alpha-form-next.form' => 'color: {{VALUE}};fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'btn_text_bg_complete',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .alpha-form-next.form' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btn_border_complete',
				'selector' => '{{WRAPPER}} .alpha-form-next.form',
			]
		);

		$this->add_responsive_control(
			'button_border_radius_complete',
			[
				'label' => __('Arredondamento', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-form-next.form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_padding_complete',
			[
				'label' => __('Espaçamento Interno', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-form-next.form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'fields_options' => [
					'top' => [
						'default' => 4,
					],
					'right' => [
						'default' => 15,
					],
					'bottom' => [
						'default' => 4,
					],
					'left' => [
						'default' => 15,
					],
				],
			]
		);
		$this->end_controls_tab();

		// Aba Hover
		$this->start_controls_tab(
			'tab_button_hover_complete',
			[
				'label' => esc_html('Hover', 'alpha-form'),
			]
		);
		$this->add_control(
			'btn_text_color_hover_complete',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .alpha-form-next.form:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'btn_text_bg_hover_complete',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .alpha-form-next.form:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btn_border_hover_complete',
				'selector' => '{{WRAPPER}} .alpha-form-next.form:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow_hover_complete',
				'label'    => __('Sombra do Input', 'alpha-form'),
				'selector' => '{{WRAPPER}} .alpha-form-next.form:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control('icon_size_complete', [
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
				'{{WRAPPER}} .alpha-form-next.form .alpha-form-button-icon svg' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('icon_spacing_complete', [
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
				'{{WRAPPER}} .alpha-form-next.form .alpha-form-button-inner' => 'gap: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->end_controls_section();
	}

	public function render()
	{
		$settings = $this->get_settings_for_display();

		$label = $settings['field_label_complete'] ?? '';
		$description = $settings['field_descricao_complete'] ?? '';
		$next_button_text = $settings['complete_button_text'] ?? '';
		$allowed_html = array(
			'a' => array(
				'href' => true,
				'title' => true,
				'target' => true,
				'rel' => true,
			),
			'br' => [],
			'em' => [],
			'strong' => [],
			'b' => [],
			'i' => [],
			'u' => [],
			'span' => array(
				'class' => true,
				'style' => true,
			),
			'div' => array(
				'class' => true,
				'style' => true,
			),
			'p' => array(
				'class' => true,
				'style' => true,
			),
			'h1' => array('class' => true, 'style' => true),
			'h2' => array('class' => true, 'style' => true),
			'h3' => array('class' => true, 'style' => true),
			'ul' => ['class' => true],
			'ol' => ['class' => true],
			'li' => ['class' => true],
			'img' => array(
				'src' => true,
				'alt' => true,
				'width' => true,
				'height' => true,
				'class' => true,
				'style' => true,
			),
		);
		$this->add_render_attribute('button', [
			'class' => 'alpha-form-next form',
			'type'  => 'submit',
			'data-alpha' => 'submit',
		]);

		echo '<div class="alpha-inputs">';

		// Label
		if ($label) {
			echo wp_kses('<h3 class="alpha-form-titulo">' . $label . '</h3>', $allowed_html);
		}

		// Descrição
		if (!empty($description)) {
			echo '<div class="alpha-form-description">' . wp_kses($description, $allowed_html) . '</div>';
		}

		// Botão
		echo '<button ' . $this->get_render_attribute_string('button') . '>';

		// Abre o wrapper do conteúdo do botão
		echo '<span class="alpha-form-button-inner" data-alpha="next">';

		// Ícone antes do texto
		if (!empty($settings['icon_complete']['value']) && $settings['btn_icon_position_complete'] === 'before') {
			echo '<span class="alpha-form-button-icon before" data-alpha="next">';
			Icons_Manager::render_icon($settings['icon_complete'], ['aria-hidden' => 'true']);
			echo '</span>';
		}

		// Texto do botão
		if (!empty($next_button_text)) {
			echo '<span class="alpha-form-button-text" data-alpha="next">' . esc_html($next_button_text) . '</span>';
		}

		// Ícone depois do texto
		if (!empty($settings['icon_complete']['value']) && $settings['btn_icon_position_complete'] === 'after') {
			echo '<span class="alpha-form-button-icon after" data-alpha="next">';
			Icons_Manager::render_icon($settings['icon_complete'], ['aria-hidden' => 'true']);
			echo '</span>';
		}

		// Fecha o wrapper
		echo '</span></button>';
		echo '</div>';
	}
}
