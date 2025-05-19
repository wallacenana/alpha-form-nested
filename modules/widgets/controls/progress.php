<?php

namespace AlphaForm\Module\Widget\Controls;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit;

class Alpha_Progress extends Widget_Base
{

    public function get_name()
    {
        return 'alpha-progress';
    }

    public function get_title()
    {
        return esc_html__('Alpha Progress', 'alpha-form');
    }

    public function get_icon()
    {
        return 'eicon-skill-bar';
    }

    public function get_categories()
    {
        return ['alpha-form'];
    }

    public function get_keywords()
    {
        return ['alpha', 'form', 'progress', 'barra'];
    }
    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => esc_html__('Configurações', 'alpha-form'),
        ]);

        $this->add_control('show_percentage', [
            'label' => esc_html__('Exibir Porcentagem', 'alpha-form'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Sim', 'alpha-form'),
            'label_off' => esc_html__('Não', 'alpha-form'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('percentage_text_prefix', [
            'label' => esc_html__('Texto antes da porcentagem', 'alpha-form'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Completo',
            'placeholder' => 'Completo',
            'condition' => [
                'show_percentage' => 'yes',
            ],
        ]);

        $this->add_control('percentage_text_align', [
            'label' => esc_html__('Alinhamento do Texto', 'alpha-form'),
            'type' => Controls_Manager::CHOOSE,
            'label_block' => false,
            'options' => [
                'left' => [
                    'title' => esc_html__('Esquerda', 'alpha-form'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Centro', 'alpha-form'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Direita', 'alpha-form'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-text' => 'text-align: {{VALUE}};',
            ],
            'condition' => [
                'show_percentage' => 'yes',
            ],
        ]);


        $this->add_control('percentage_position', [
            'label' => esc_html__('Posição do Texto', 'alpha-form'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'column-reverse' => [
                    'title' => esc_html__('Acima', 'alpha-form'),
                    'icon' => 'eicon-arrow-up',
                ],
                'column' => [
                    'title' => esc_html__('Abaixo', 'alpha-form'),
                    'icon' => 'eicon-arrow-down',
                ],
            ],
            'default' => 'column-reverse',
            'toggle' => false,
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-wrapper' => 'flex-direction: {{VALUE}}',
            ],
            'condition' => [
                'show_percentage' => 'yes',
            ],
        ]);

        $this->add_control('ajax_button', [
            'label'       => __('Atualizar lista de formulários', 'alpha-form'),
            'type'        => Controls_Manager::BUTTON,
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

        // sessão de estilo
        $this->start_controls_section('section_style', [
            'label' => esc_html__('Estilo', 'alpha-form'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('percentage_text_color', [
            'label' => esc_html__('Cor do Texto da Porcentagem', 'alpha-form'),
            'type' => Controls_Manager::COLOR,
            'global' => ['default' => Global_Colors::COLOR_PRIMARY],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-text' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'percentage_text_typography',
            'label' => esc_html__('Tipografia do Texto', 'alpha-form'),
            'selector' => '{{WRAPPER}} .alpha-form-progress-text',
            'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY],
        ]);

        $this->add_control('bar_bg_color', [
            'label' => esc_html__('Cor de Fundo da Barra', 'alpha-form'),
            'type' => Controls_Manager::COLOR,
            'global' => ['default' => Global_Colors::COLOR_SECONDARY],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-bar-bg' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('bar_fill_color', [
            'label' => esc_html__('Cor da Barra Preenchida', 'alpha-form'),
            'type' => Controls_Manager::COLOR,
            'global' => ['default' => Global_Colors::COLOR_PRIMARY],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-bar-fill' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('bar_border_radius', [
            'label' => esc_html__('Borda Arredondada', 'alpha-form'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'size' => 8,
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-bar-bg' => 'border-radius: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .alpha-form-progress-bar-fill' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);


        $this->add_responsive_control('bar_height', [
            'label' => esc_html__('Altura da Barra', 'alpha-form'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em', '%'],
            'default' => [
                'size' => 20,
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 4,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .alpha-form-progress-bar-bg' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .alpha-form-progress-bar-fill' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('progress_gap', [
            'label' => esc_html__('Espaçamento', 'alpha-form'),
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
                '{{WRAPPER}} .alpha-form-progress-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'show_percentage' => 'yes',
            ],
        ]);
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        echo '<div class="alpha-form-progress-wrapper" data-target="' . $settings['form_target'] . '">';
        echo '<div class="alpha-form-progress-bar-bg">';
        echo '<div class="alpha-form-progress-bar-fill" style="width: 0%;"></div>';
        echo '</div>';

        if ($settings['show_percentage'] === 'yes') {
            echo '<div class="alpha-form-progress-text">';
            echo '<span class="alpha-form-progress-percent">0%</span> ' . esc_html($settings['percentage_text_prefix']);
            echo '</div>';
        }

        echo '</div>';
    }

    protected function content_template()
    {
?>
        <#
            var prefix=settings.percentage_text_prefix || 'Completo' ;
            var showText=settings.show_percentage==='yes' ;
            var position=settings.percentage_position || 'bottom' ;
            var wrapperClass='alpha-form-progress-wrapper has-percentage ' + (position==='top' ? 'percentage-top' : 'percentage-bottom' );
            #>

            <div class="{{ wrapperClass }}">
                <div class="alpha-form-progress-bar-bg">
                    <div class="alpha-form-progress-bar-fill" style="width: 80%;"></div>
                </div>

                <# if (showText) { #>
                    <div class="alpha-form-progress-text">
                        <span class="alpha-form-progress-percent">80%</span> {{ prefix }}
                    </div>
                    <# } #>
            </div>
    <?php
    }
}
