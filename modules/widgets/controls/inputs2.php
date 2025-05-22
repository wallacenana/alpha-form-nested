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
					'stext'     => 'Texto simples',
					'cel'       => 'Celular (BR)',
					'cpf'       => 'CPF',
					'cnpj'      => 'CNPJ',
					'cep'       => 'CEP',
					'currency'  => 'Moeda',
				],
				'default' => 'radio',
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
					'field_type!' => ['select', 'radio', 'checkbox', 'date', 'time', 'stext', 'hidden', 'acceptance'],
				],
			]
		);


		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'label',
			[
				'label' => __('Label da opção', 'alpha-form'),
				'type'  => Controls_Manager::TEXT,
				'default' => __('Opção 1', 'alpha-form'),
			]
		);

		$repeater->add_control(
			'icon_library',
			[
				'label'   => __('Ícone', 'alpha-form'),
				'type'    => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => [
					'check'           => '✔️ Confirmação',
					'confused'        => '😕 Confuso',
					'angry'           => '😠 Irritado',
					'dizziness'       => '🥴 Tonto',
					'cool-1'          => '😎 Descolado',
					'poisoned'        => '🤢 Envenenado',
					'angel'           => '😇 Anjo',
					'vomit'           => '🤮 Vomitando',
					'zombie'          => '🧟 Zumbi',
					'vomit'           => '🤢 Enjoado',
					'tongue-out-2'    => '😜 Língua de fora',
					'squint'          => '😆 Apertando os olhos',
					'wink'            => '😉 Piscando',
					'tongue'          => '😜 Língua',
					'think'           => '🤔 Pensando',
					'exhausted'       => '🥵 Exausto',
					'sinister-smile'  => '😏 Sorriso malicioso',
					'jealous'         => '😒 Com ciúmes',
					'laugh-1'         => '😂 Rindo muito',
					'smile-1'         => '😊 Sem jeito',
					'sleep'           => '😴 Dormindo',
					'laugh'           => '😄 Rindo',
					'get-ill'         => '🤒 Doente',
					'fear-1'          => '😱 Apavorado',
					'fear'            => '😨 Assustado',
					'cool'            => '😎 Estilo',
					'shut-up'         => '🤐 Boca fechada',
					'shocked-1'       => '😲 Chocado',
					'sad-1'           => '😢 Triste',
					'mute'            => '😶 Silêncio',
					'sad'             => '☹️ Decepcionado',
					'deadpan-1'       => '😑 Sem reação',
					'face-mask'       => '😷 Máscara',
					'kiss-1'          => '😘 Beijo',
					'kiss-2'          => '😗 Beijo leve',
					'kiss'            => '😘 Beijo marcante',
					'laugh-and-cry'   => '🤣 Gargalhando',
					'in-love'         => '😍 Apaixonado',
					'happy-2'         => '😁 Feliz',
					'happy'           => '🙂 Contente',
					'injuried'        => '🤕 Machucado',
					'devil'           => '😈 Diabinho',
					'cry'             => '😭 Chorando muito',
					'cry-1'           => '😥 Chorando',
					'happy-1'         => '😃 Alegre',
					'poker-face'      => '😐 Poker Face',
					'stupid-b'        => '🤪 Tonto/Doido',
				],
				'default' => 'happy-2'
			]
		);

		$repeater->add_control(
			'value',
			[
				'label' => __('Valor', 'alpha-form'),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'target',
			[
				'label' => __('Target', 'alpha-form'),
				'type'  => Controls_Manager::TEXT,
				'placeholder' => 'Ex: 3',
				'description' => 'Insira o numero da próxima pergunta quando clicar neste item'
			]
		);

		$this->add_control(
			'field_choices',
			[
				'label' => __('Opções do campo', 'alpha-form'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'label'         => __('Opção 1', 'alpha-form'),
						'icon_library'  => 'kiss-2',
					],
					[
						'label' => __('Opção 2', 'alpha-form'),
						'icon_library'  => 'angry',
					],
					[
						'label' => __('Opção 3', 'alpha-form'),
						'icon_library'  => 'cool-1',
					],
					[
						'label' => __('Opção 4', 'alpha-form'),
						'icon_library'  => 'in-love',
					],
					[
						'label' => __('Opção 5', 'alpha-form'),
						'icon_library'  => 'fear-1',
					],
					[
						'label' => __('Opção 6', 'alpha-form'),
						'icon_library'  => 'poisoned',
					],
				],
				'condition' => [
					'field_type' => ['select', 'radio', 'checkbox'],
				],
				'title_field' => '{{{ label }}}',
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
					'field_type!' => ['hidden', 'acceptance'],
				],
			]
		);
		$this->add_control(
			'aux_text',
			[
				'label' => __('Texto auxiliar', 'alpha-form'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Preencha este campo',
				'placeholder' => 'ex: Continuar',
				'condition' => [
					'field_type!' => [
						'hidden',
						'stext'
					]
				],
			]
		);

		$this->add_control('icon', [
			'label' => esc_html__('Ícone', 'alpha-form'),
			'type' => Controls_Manager::ICONS,
			'skin' => 'inline',
			'label_block' => false,
			'default' => [
				'value' => '',
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
			'condition' => [
				'field_type!' => 'acceptance',
				'checkbox',
				'select'
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
				'default' => 'no',
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
						'currency',
						'cel'
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

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => __('Espaçamento Interno', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-input-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_responsive_control(
			'btn_padding',
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
		$this->end_controls_tabs();

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
				'selector' => '{{WRAPPER}} .alpha-error-message, {{WRAPPER}} .alpha-error',
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

		// texto key hint
		$this->start_controls_section(
			'style_option_section',
			[
				'label' => __('Opções', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control('options_direction', [
			'label' => esc_html__('Direção', 'alpha-form'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'row' => [
					'title' => esc_html__('Horizontal', 'alpha-form'),
					'icon' => 'eicon-arrow-right',
				],
				'column' => [
					'title' => esc_html__('Vertical', 'alpha-form'),
					'icon' => 'eicon-arrow-down',
				],
			],
			'default' => 'column',
			'toggle' => false,
			'selectors' => [
				'{{WRAPPER}} .alpha-inputs-options' => 'width: 100%; display: flex; flex-wrap: wrap; flex-direction: {{VALUE}};'
			],
		]);

		$this->add_responsive_control('options_gap', [
			'label' => esc_html__('Espaçamento entre opções', 'alpha-form'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['px', 'em'],
			'default' => [
				'size' => 10,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .alpha-inputs-options' => 'gap: {{SIZE}}{{UNIT}};',
			],
		]);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'option_typography',
				'selector' => '{{WRAPPER}} .alpha-option',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 16,
							'weight' => 400,
						],
					],
				],
			]
		);

		// Aba ativo
		$this->start_controls_tabs('tabs_option_styles');
		// Aba Normal
		$this->start_controls_tab(
			'tab_option_normal',
			[
				'label' => __('Normal', 'alpha-form'),
			]
		);

		$this->add_control(
			'label_option_color',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-option' => 'margin: 0; color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_control(
			'option_text_bg',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-option' => 'background: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'option_border',
				'selector' => '{{WRAPPER}} .alpha-option',
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
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'option_border_radius',
			[
				'label' => __('Arredondamento', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-option' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_option_hover',
			[
				'label' => __('Hover', 'alpha-form'),
			]
		);

		$this->add_control(
			'label_option_color_hover',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-option:hover' => 'margin: 0; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'option_text_bg_hover',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-option:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'option_border_hover',
				'selector' => '{{WRAPPER}} .alpha-option:hover',
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
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'option_shadow_hover',
				'label'    => __('Sombra do Input', 'alpha-form'),
				'selector' => '{{WRAPPER}} .alpha-option:hover',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_option_checkec',
			[
				'label' => __('Checado', 'alpha-form'),
			]
		);
		$this->add_control(
			'label_option_color_check',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-option:has(input:checked)' => 'margin: 0; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'option_text_bg_check',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-option:has(input:checked)' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'option_border_check',
				'selector' => '{{WRAPPER}} .alpha-option:has(input:checked)',
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
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'option_shadow_focus',
				'label'    => __('Sombra do Input', 'alpha-form'),
				'selector' => '{{WRAPPER}} .alpha-option:has(input:checked)',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'option_padding',
			[
				'label' => __('Espaçamento Interno', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-option' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		// key hint

		$this->add_control(
			'heading_form_item_title_hey_hint',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__('Key hint', 'alpha-form'),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'key_hint_typography',
				'selector' => '{{WRAPPER}} label.alpha-letter-active::before',
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
			'label_key_hint_color',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} label.alpha-letter-active::before' => 'margin: 0; color: {{VALUE}};',
				],
				'default' => '#ff0042',
			]
		);

		$this->add_control(
			'key_hint_bg',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} label.alpha-letter-active::before' => 'background: {{VALUE}};',
				],
				'default' => '#ff004220',
			]
		);

		$this->add_responsive_control(
			'key_hint_padding',
			[
				'label' => __('Espaçamento Interno', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} label.alpha-letter-active::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'key_hint_border',
				'selector' => '{{WRAPPER}} label.alpha-letter-active::before',
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
			'key_hint_border_radius',
			[
				'label' => __('Arredondamento', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} label.alpha-letter-active::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();




		//campos do select
		$this->start_controls_section(
			'style_select_section',
			[
				'label' => __('Select', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'select_typography',
				'selector' => '{{WRAPPER}} .alpha-select',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'fields_options' => [
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 16,
							'weight' => 400,
						],
					],
				],
			]
		);

		// Aba ativo
		$this->start_controls_tabs('tabs_select_styles');
		// Aba Normal
		$this->start_controls_tab(
			'tab_select_normal',
			[
				'label' => __('Normal', 'alpha-form'),
			]
		);

		$this->add_control(
			'label_select_color',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-select' => 'margin: 0; color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_control(
			'select_text_bg',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-select' => 'background: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'select_border',
				'selector' => '{{WRAPPER}} .alpha-select',
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
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'select_border_radius',
			[
				'label' => __('Arredondamento', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_select_hover',
			[
				'label' => __('Hover', 'alpha-form'),
			]
		);

		$this->add_control(
			'label_select_color_hover',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-select:hover' => 'margin: 0; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'select_text_bg_hover',
			[
				'label' => __('Background', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alpha-select:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'select_border_hover',
				'selector' => '{{WRAPPER}} .alpha-select:hover',
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
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'select_padding',
			[
				'label' => __('Espaçamento Interno', 'alpha-form'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .alpha-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->end_controls_section();

		// aceptance input

		$this->start_controls_section(
			'style_acceptance_section',
			[
				'label' => __('Aceitação', 'alpha-form'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'acceptance_typography',
				'selector' => '{{WRAPPER}} label.acceptance',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);
		$this->add_control(
			'acceptance_text_color',
			[
				'label' => __('Cor do Texto', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} label.acceptance' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],

			]
		);

		$this->add_responsive_control(
			'acceptance_checkbox_size',
			[
				'label' => __('Tamanho do Checkbox', 'alpha-form'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 18,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} input.acceptance' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'acceptance_checkbox_color',
			[
				'label' => __('Cor do Checkbox', 'alpha-form'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input.acceptance' => 'accent-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'acceptance_gap',
			[
				'label' => __('Espaçamento entre Checkbox e Texto', 'alpha-form'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} label.acceptance' => 'display:flex; align-itms:center; gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_type' => 'acceptance',
				],
			]
		);


		$this->end_controls_section();
	}

	public function render()
	{
		$settings = $this->get_settings_for_display();

		$type = $settings['field_type'] ?? '';
		$pattern = $settings['field_pattern'] ? 'pattern="' . esc_attr($settings['field_pattern']) . '"' : '';
		$aux_text = $settings['aux_text'] ?? '';
		$label = $settings['field_label_n'] ?? '';
		$name = !empty($settings['field_name']) ? $settings['field_name'] : 'field_' . $type . '_' . substr($this->get_id(), 0, 6);
		$value = $settings['field_value_n'] ?? '';
		$description = $settings['field_descricao'] ?? '';
		$placeholder = $settings['field_placeholder'] ?? '';
		$next_button_text = $settings['next_button_text'] ?? '';
		$show_hint = $settings['key-hint'] ?? 'no';
		$required = !empty($settings['required']) ? 'required' : '';
		$special_masks = ['cpf', 'cnpj', 'cep', 'currency', 'cel'];
		$mask = in_array($type, $special_masks) ? ' data-mask=' . esc_attr($type) . '' : '';
		$class = 'alpha-input-field';
		$id = $this->get_id();

		$this->add_render_attribute('button', [
			'class' => 'alpha-form-next form',
			'type'  => 'button',
			'data-alpha' => 'next',
			'style' => $type === 'radio' ? 'display: none' : '',
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
				echo '<textarea class="' . esc_attr($class) . '" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" ' . esc_attr($required) . '>' . esc_textarea($value) . '</textarea>';
				break;

			case 'radio':
			case 'checkbox':
			case 'select':
				$options = $settings['field_choices'] ?? [];
				$input_type = $type;
				echo '<div class="alpha-inputs-options">';
				if ($type === 'select') {
					echo '<select name="' . esc_attr($name) . '" ' . $required . ' class="alpha-select" id="' . esc_attr($id) . '">';

					foreach ($options as $choice) {
						$label = trim($choice['label'] ?? '');
						$value = trim($choice['value']) ? trim($choice['value']) : sanitize_title($label);
						$next  = trim($choice['target'] ?? '');

						$attrs = 'value="' . esc_attr($value) . '"';
						if ($next) {
							$attrs .= ' data-next="' . esc_attr($next) . '"';
						}

						echo '<option ' . $attrs . '>' . esc_html($label) . '</option>';
					}

					echo '</select>';
				} else {
					foreach ($options as $index => $choice) {
						$label = trim($choice['label'] ?? '');
						$value = trim($choice['value']) ? trim($choice['value']) : sanitize_title($label);
						$next  = trim($choice['target'] ?? '');
						$icon  = trim($choice['icon_library'] ?? '');
						$input_id = esc_attr($id . '_' . $index);

						$attrs = 'type="' . esc_attr($input_type) . '" name="' . esc_attr($name) . '" id="' . esc_attr($input_id) . '" value="' . esc_attr($value) . '" ' . $required;
						if ($next) {
							$attrs .= ' data-next="' . esc_attr($next) . '"';
						}

						$label_attrs = '';
						$letter = chr(65 + $index);
						if ($show_hint) {
							$label_attrs .= ' data-letter="' . $letter . '"';
							$label_attrs .= ' data-icon="✓"';
						}
						echo '<label for="' . $input_id . '" class="alpha-option" ' . $label_attrs . '>';

						if ($icon) {
							$icon_url = strpos($icon, 'http') === 0 ? $icon : ALPHA_FORM_URL . 'assets/elements/icones/' . $icon . '.svg';
							echo '<img src="' . esc_url($icon_url) . '" class="alpha-option-icon" />';
						}
						echo esc_html($label);
						echo '<input ' . $attrs . '> ';
						echo '</label>';
					}
					echo '</div>';
				}

				break;

			case 'hidden':
				echo '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" id="' . esc_attr($id) . '"/>';
				break;


			case 'acceptance':
				echo '<label class="acceptance"><input type="checkbox" class="acceptance" name="' . esc_attr($name) . '" ' . esc_attr($required) . ' id="' . esc_attr($id) . '"> ' . esc_html($settings['acceptance_text']) . '</label>';
				break;

			case 'stext':
				break;

			default:
				echo '<input  class="' . esc_attr($class) . '" type="' . esc_attr($type) . '" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" value="' .  esc_attr($value) . '" ' . $pattern . ' ' . esc_attr($required) . esc_attr($mask) . ' autofocus />';
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
			echo '<script>
					document.querySelectorAll("label[data-letter]").forEach(label => {
						if (!label.classList.contains("alpha-letter-active")) {
							const letter = label.getAttribute("data-letter");
							if (letter) {
								label.classList.add("alpha-letter-active");
								label.setAttribute("data-letter-display", letter);
							}
						}
					});
				</script>';
		}

		if (
			!empty($settings['next_button_text']) ||
			!empty($settings['icon']['value']) ||
			!empty($settings['aux_text'])
		) {

			// Botão
			echo '<div class="alpha-aux">';
			if (!in_array($type, ['hidden'], true) && $next_button_text) {
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
		}
		echo '</div>';
	}
}
