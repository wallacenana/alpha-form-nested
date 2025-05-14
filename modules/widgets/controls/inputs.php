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

class Alpha_Inputs extends Widget_Base
{
	public function get_name()
	{
		return 'alpha-inputs';
	}

	public function get_title()
	{
		return esc_html__('Alpha Inputs', 'alpha-form');
	}

	public function get_icon()
	{
		return 'eicon-form-horizontal';
	}

	public function get_keywords()
	{
		return ['nested', 'form', 'toggle', 'alpha', 'formulario'];
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
			'field_type',
			[
				'label' => __('Tipo do Campo', 'alpha-form'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'text'      => 'Texto',
					'email'     => 'Email',
					'textarea'  => 'Área de Texto',
					'tel'       => 'Telefone',
					'url'       => 'URL',
					'number'    => 'Número',
					'password'  => 'Senha',
					'radio'     => 'Escolha Única',
					'checkbox'  => 'Múltiplas Escolhas',
					'select'    => 'Select',
					'date'      => 'Data',
					'time'      => 'Hora',
					'hidden'    => 'Oculto',
					'acceptance' => 'Aceitação',
					'intro'     => 'Texto simples',
					'cpf'       => 'CPF',
					'cnpj'      => 'CNPJ',
					'cep'       => 'CEP',
					'currency'  => 'Moeda',
				],
				'default' => 'text',
			]
		);

		$this->start_controls_tabs('form_fields_tabs');

		$this->start_controls_tab('form_fields_conteudo_tab', [
			'label' => esc_html__('Conteúdo', 'alpha-form'),
		]);


		$this->add_control(
			'acceptance_text',
			[
				'label' => __('Texto da Aceitação', 'alpha-form'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => 'Li e aceito a política de privacidade.',
				'condition' => [
					'field_type' => 'acceptance',
				],
			]
		);
		$this->add_control(
			'field_label_n',
			[
				'label' => __('Pergunta', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Digite a questão',
				'label_block' => true,
			]
		);
		$this->add_control(
			'field_descricao',
			[
				'label' => __('Descrição', 'alpha-form'),
				'type' => Controls_Manager::WYSIWYG,
				'default' => '',
				'condition' => [
					'field_type!' => ['hidden'],
				],
			]
		);


		$this->add_control(
			'field_placeholder',
			[
				'label' => __('Placeholder', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'field_type!' => ['select', 'radio', 'checkbox', 'date', 'time', 'intro', 'hidden'],
				],
			]
		);


		$this->add_control(
			'field_options',
			[
				'label' => __('Opções (uma por linha)', 'alpha-form'),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'condition' => [
					'field_type' => ['select', 'radio', 'checkbox'],
				],
				'description' => __('Insira cada opção em uma linha separada. Para diferenciar entre rótulo e valor, separe-os com um caractere de barra vertical ("|"). Por exemplo: First Name|f_name', 'alpha-form'),
			]
		);


		$this->add_control(
			'next_button_text',
			[
				'label' => __('Texto do botão', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Próximo',
				'placeholder' => 'ex: Continuar',
				'condition' => [
					'field_type' => ['text', 'email', 'textarea', 'tel', 'url', 'number', 'password', 'date', 'time', 'intro', 'checkbox', 'cpf', 'cnpj', 'cep', 'currency'],
				],
			]
		);
		$this->add_control(
			'aux_text',
			[
				'label' => __('Texto auxiliar', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Preencha este campo',
				'placeholder' => 'ex: Continuar'
			]
		);

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

		$this->add_control('btn_icon_position', [
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
			'condition' => [
				'icon[value]!' => '',
			],
		]);

		$this->add_control(
			'key-hint',
			[
				'label' => __('Mostrar marcadores', 'alpha-form'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Sim', 'alpha-form'),
				'label_off' => __('Não', 'alpha-form'),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'field_type' => ['radio', 'checkbox'],
				],
			]
		);

		$this->add_control(
			'required',
			[
				'label' => __('Obrigatório', 'alpha-form'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Sim', 'alpha-form'),
				'label_off' => __('Não', 'alpha-form'),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'field_type' => [
						'text',
						'email',
						'tel',
						'textarea',
						'select',
						'radio',
						'checkbox',
						'date',
						'cpf',
						'cnpj',
						'cep',
						'currency'
					],
				],
			]
		);

		$this->add_control(
			'error_message',
			[
				'label' => __('Mensagem de erro', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Este campo é obrigatório.',
				'placeholder' => 'Digite a mensagem de erro',
				'separator' => 'before',
				'ai' => [
					'active' => false,
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'form_fields_advanced_tab',
			[
				'label' => esc_html__('Avançado', 'alpha-form'),
				'condition' => [
					'field_type!' => 'html',
				],
			]
		);
		$this->add_control(
			'field_value_n',
			[
				'label' => __('Valor Padrão', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'field_type' => ['text', 'email', 'textarea', 'tel', 'url', 'number', 'password', 'hidden', 'text', 'cpf', 'cnpj', 'cep', 'currency'],
				],
			]
		);
		$this->add_control(
			'field_name',
			[
				'label' => __('Nome do input', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'field_pattern',
			[
				'label' => __('Padrão (Pattern)', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'description' => __('Regex ou padrão de validação HTML5. Ex: [0-9]{3}-[0-9]{2}', 'alpha-form'),
				'condition' => [
					'field_type' => ['text', 'email', 'password'],
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'style_input_section',
			[
				'label' => __('Caixa geral', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_gap',
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
			'style_label_section',
			[
				'label' => __('Perguntas', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'h3_typography',
				'selector' => '{{WRAPPER}} .alpha-form-titulo',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_control(
			'label_text_color',
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
			'label_text_alinhamento',
			[
				'label' => esc_html__('Alinhamento', 'alpha-form-premium'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'alpha-form-premium'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'alpha-form-premium'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'alpha-form-premium'),
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
			'style_descricao_section',
			[
				'label' => __('Descrição', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'p_typography',
				'selector' => '{{WRAPPER}} .alpha-form-description p',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);
		$this->add_control(
			'descricao_text_color',
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
			'descricao_text_alinhamento',
			[
				'label' => esc_html__('Alinhamento', 'alpha-form-premium'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'alpha-form-premium'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'alpha-form-premium'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'alpha-form-premium'),
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
			'style_inputs_section',
			[
				'label' => __('Campos', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .alpha-input-field',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->start_controls_tabs('tabs_input_styles');

		// Aba Normal
		$this->start_controls_tab(
			'tab_input_normal',
			[
				'label' => __('Normal', 'alpha-form'),
			]
		);
		$this->add_control(
			'input_text_color',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-input-field' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_control(
			'input_text_bg',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-input-field' => 'background: {{VALUE}};',
				],
				'default' => '#ffffff',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .alpha-input-field',
			]
		);

		$this->add_responsive_control(
			'input_border_radius',
			[
				'label' => __('Arredondamento', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-input-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		// Aba Focus
		$this->start_controls_tab(
			'tab_input_focus',
			[
				'label' => esc_html('Focus', 'alpha-form'),
			]
		);

		$this->add_control(
			'input_text_color_focus',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-input-field:focus' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_control(
			'input_text_bg_focus',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-input-field:focus' => 'background: {{VALUE}};',
				],
				'default' => '#ffffff',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border_focus',
				'selector' => '{{WRAPPER}} .alpha-input-field:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'input_box_shadow_focus',
				'label'    => __('Sombra do Input', 'alpha-form'),
				'selector' => '{{WRAPPER}} .alpha-input-field:focus',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();



		$this->start_controls_section(
			'style_buttons_section',
			[
				'label' => __('Botão', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'btn_typography',
				'selector' => '{{WRAPPER}} .alpha-form-next.form',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		// Aba ativo
		$this->start_controls_tabs('tabs_button_styles');

		// Aba Normal
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __('Normal', 'alpha-form'),
			]
		);
		$this->add_control(
			'btn_text_color',
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
			'btn_text_bg',
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
				'name' => 'btn_border',
				'selector' => '{{WRAPPER}} .alpha-form-next.form',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => __('Arredondamento', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-form-next.form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		// Aba Hover
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html('Hover', 'alpha-form'),
			]
		);
		$this->add_control(
			'btn_text_color_hover',
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
			'btn_text_bg_hover',
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
				'name' => 'btn_border_hover',
				'selector' => '{{WRAPPER}} .alpha-form-next.form:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow_hover',
				'label'    => __('Sombra do Input', 'alpha-form'),
				'selector' => '{{WRAPPER}} .alpha-form-next.form:hover',
			]
		);
		$this->end_controls_tab();
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
				'{{WRAPPER}} .alpha-form-next.form .alpha-form-button-icon svg' => 'width: {{SIZE}}{{UNIT}};',
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
				'{{WRAPPER}} .alpha-form-next.form .alpha-form-button-inner' => 'gap: {{SIZE}}{{UNIT}};',
			],
		]);
		$this->end_controls_section();

		// texto auxiliar
		$this->start_controls_section(
			'style_aux_section',
			[
				'label' => __('Texto auxiliar', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'aux_typography',
				'selector' => '{{WRAPPER}} .alpha-aux p',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_control(
			'label_aux_color',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-aux p' => 'margin: 0; color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_control('label_aux_position', [
			'label' => esc_html__('Posição do texto auxiliar', 'alpha-form'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'row' => [
					'title' => esc_html__('Direita', 'alpha-form'),
					'icon' => 'eicon-arrow-right',
				],
				'column' => [
					'title' => esc_html__('Abaixo', 'alpha-form'),
					'icon' => 'eicon-arrow-down',
				],
			],
			'default' => 'row',
			'toggle' => false,
			'prefix_class' => 'alpha-aux-dir-',
		]);


		$this->end_controls_section();

		// texto error
		$this->start_controls_section(
			'style_error_section',
			[
				'label' => __('Mensagem de erro', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'error_typography',
				'selector' => '{{WRAPPER}} .alpha-error-message',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 13,
							'weight' => 400,
						],
					],
				],
			]
		);

		$this->add_control(
			'label_error_color',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-error-message' => 'margin: 0; color: {{VALUE}};',
				],
				'default' => '#ff0042',
			]
		);

		$this->add_control(
			'error_text_bg',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-error-message' => 'background: {{VALUE}};',
				],
				'default' => '#ff004220',
			]
		);

		$this->add_responsive_control(
			'error_padding',
			[
				'label' => __('Espaçamento Interno', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-error-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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



		$this->add_responsive_control('error_margin_top', [
			'label' => esc_html__('Espaçamento vertical', 'alpha-form'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['px', 'em', 'rem'],
			'range' => [
				'px' => [
					'min' => -100,
					'max' => 100,
					'step' => 1,
				],
			],
			'default' => [
				'unit' => 'px',
				'size' => 8,
			],
			'selectors' => [
				'{{WRAPPER}} .alpha-error-message' => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'error_border',
				'selector' => '{{WRAPPER}} .alpha-error-message',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => 1,
							'right' => 1,
							'bottom' => 1,
							'left' => 1,
							'unit' => 'px',
						],
					],
					'color' => [
						'default' => '#ff0042',
					],
				],
			]
		);

		$this->add_responsive_control(
			'error_border_radius',
			[
				'label' => __('Arredondamento', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-error-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render()
	{
		$settings = $this->get_settings_for_display();

		$type = $settings['field_type'] ?? '';
		$pattern = $settings['field_pattern'] ?? '';
		$aux_text = $settings['aux_text'] ?? '';
		$label = $settings['field_label_n'] ?? '';
		$name = !empty($settings['field_name']) ? $settings['field_name'] : 'field_' . $type . '_' . substr($this->get_id(), 0, 6);
		$value = $settings['field_value_n'] ?? '';
		$description = $settings['field_descricao'] ?? '';
		$placeholder = $settings['field_placeholder'] ?? '';
		$next_button_text = $settings['next_button_text'] ?? '';
		$key_hint = $settings['key-hint'] ?? '';
		$required = !empty($settings['required']) ? 'required' : '';
		$special_masks = ['cpf', 'cnpj', 'cep', 'currency', 'credit_card'];
		$mask = in_array($type, $special_masks) ? ' data-mask="' . esc_attr($type) . '"' : '';
		$class = 'alpha-input-field';
		// $show_required = $settings['show_required_mark'] === 'yes';
		// $requiredMark = $show_required && esc_html($required) ? '<span class="alpha-mask-required">*</span>' : '';
		$this->add_render_attribute('button', [
			'class' => 'alpha-form-next form',
			'type'  => 'button',
			'data-alpha' => 'next',
		]);
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

		echo '<div class="alpha-inputs">';

		// Label
		if ($label) {
			echo wp_kses('<h3 class="alpha-form-titulo">' . $label . '</h3>', $allowed_html);
		}

		// Descrição
		if (!empty($description)) {
			echo '<div class="alpha-form-description">' . wp_kses($description, $allowed_html) . '</div>';
		}

		// Campo
		switch ($type) {
			case 'textarea':
				echo '<textarea class="' . esc_attr($class) . '" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" placeholder="' . esc_attr($placeholder) . '" ' . esc_attr($required) . '>' . esc_textarea($value) . '</textarea>';
				break;

			case 'checkbox':
			case 'radio':
				$input_type = esc_attr($type);
				$options = explode("\n", $settings['field_options'] ?? '');
				if (!empty($options)) {
					echo '<div class="alpha-form-options">';
					foreach ($options as $opt) {
						$opt = trim($opt);
						if (!$opt) continue;

						$parts = explode('|', $opt);
						$option_label = isset($parts[0]) ? trim($parts[0]) : '';
						$option_value = isset($parts[1]) ? trim($parts[1]) : sanitize_title($option_label);

						echo '<label><input type="' . $input_type . '" name="' . esc_attr($name) . ($type === 'checkbox' ? '[]' : '') . '" value="' . esc_attr($option_value) . '" ' . esc_attr($required) . '> ' . esc_html($option_label) . '</label>';
					}
					echo '</div>';
				}
				break;

			case 'hidden':
				echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" />';
				break;

			case 'select':
				$options = explode("\n", $settings['field_options'] ?? '');
				echo '<select name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" ' . esc_attr($required) . '>';
				foreach ($options as $opt) {
					$opt = trim($opt);
					if (!$opt) continue;
					$parts = explode('|', $opt);
					$option_label = isset($parts[0]) ? trim($parts[0]) : '';
					$option_value = isset($parts[1]) ? trim($parts[1]) : sanitize_title($option_label);
					echo '<option value="' . esc_attr($option_value) . '">' . esc_html($option_label) . '</option>';
				}
				echo '</select>';
				break;

			case 'acceptance':
				echo '<label><input type="checkbox" name="' . esc_attr($name) . '" ' . esc_attr($required) . '> ' . esc_html($settings['acceptance_text']) . '</label>';
				break;

			case 'intro':
				echo '<div class="alpha-form-intro">' . wp_kses_post($description) . '</div>';
				break;

			default:
				echo '<input  class="' . esc_attr($class) . '" type="' . esc_attr($type) . '" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" ' . esc_attr($placeholder) . ' ' .  esc_attr($value) . ' ' . esc_attr($pattern) . ' ' . esc_attr($required) . esc_attr($mask) . ' autofocus />';
				break;
		}

		if ($required) {
			$style = \Elementor\Plugin::$instance->editor->is_edit_mode() ? 'display:block;' : 'display:none;';
			echo '<div class="alpha-error-message" id="alpha-form-error-preview" style="' . esc_attr($style) . '">' . esc_html($settings['error_message']) . '</div>';
		}
		if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
			echo '<script>
        (function () {
            const styleId = "alpha-form-error-preview";

            function toggleErrorPreviewStyle() {
                const bodyClassList = document.body.classList;
                const alreadyInjected = document.getElementById(styleId);

                if (bodyClassList.contains("elementor-editor-preview")) {
                    if (alreadyInjected) alreadyInjected.remove();
                } else {
                    if (!alreadyInjected) {
                        const style = document.createElement("style");
                        style.id = styleId;
                        style.innerHTML = `
                            .alpha-error-message {
                                display: block !important;
                            }
                        `;
                        document.head.appendChild(style);
                    }
                }
            }

            toggleErrorPreviewStyle();

            new MutationObserver(toggleErrorPreviewStyle)
                .observe(document.body, { attributes: true, attributeFilter: ["class"] });
        })();
    </script>';
		}

		// Botão
		echo '<div class="alpha-aux">';
		if (!in_array($type, ['hidden', 'intro', 'radio'], true) && $next_button_text) {
			echo '<button ' . $this->get_render_attribute_string('button') . '>';

			// Abre o wrapper do conteúdo do botão
			echo '<span class="alpha-form-button-inner" data-alpha="next">';

			// Ícone antes do texto
			if (!empty($settings['icon']['value']) && $settings['btn_icon_position'] === 'before') {
				echo '<span class="alpha-form-button-icon before" data-alpha="next">';
				Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
				echo '</span>';
			}

			// Texto do botão
			if (!empty($settings['next_button_text'])) {
				echo '<span class="alpha-form-button-text" data-alpha="next">' . esc_html($settings['next_button_text']) . '</span>';
			}

			// Ícone depois do texto
			if (!empty($settings['icon']['value']) && $settings['btn_icon_position'] === 'after') {
				echo '<span class="alpha-form-button-icon after" data-alpha="next">';
				Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
				echo '</span>';
			}

			// Fecha o wrapper
			echo '</span></button>';
		}
		if ($aux_text)
			echo '<p class="aux"> ' . esc_html($aux_text) . '</p>';
		echo '</div>';
		echo '</div>';
	}
}
