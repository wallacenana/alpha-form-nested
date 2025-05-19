<?php

namespace AlphaForm\Module\Widget\Controls;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Alpha_Elements extends Widget_Base
{
    public function get_name()
    {
        return 'alpha_elements';
    }

    public function get_title()
    {
        return 'Alpha Elements';
    }

    public function get_icon()
    {
        return 'eicon-gallery-justified';
    }

    public function get_categories()
    {
        return ['alpha-form'];
    }

    public function get_script_depends()
    {
        return ['alpha-form-chart'];
    }

    protected function register_controls()
    {
        $this->start_controls_section('section_content', [
            'label' => __('Dados do Gráfico', 'alpha-form'),
        ]);

        $this->add_control('element_name', [
            'label' => __('Nome do elemento', 'alpha-form'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Meu Elemento Alpha',
        ]);

        $repeater = new Repeater();

        $repeater->add_control('label', [
            'label' => __('Rótulo', 'alpha-form'),
            'type' => Controls_Manager::TEXT,
            'default' => 'Label',
        ]);

        $repeater->add_control('value', [
            'label' => __('Valor', 'alpha-form'),
            'type' => Controls_Manager::NUMBER,
            'default' => 10,
        ]);

        $repeater->add_control('background_color', [
            'label' => __('Cor de Fundo', 'alpha-form'),
            'type' => Controls_Manager::COLOR,
            'default' => '#FF6384',
        ]);

        $repeater->add_control('border_color', [
            'label' => __('Cor da Borda', 'alpha-form'),
            'type' => Controls_Manager::COLOR,
            'default' => '#FF6384',
        ]);

        $this->add_control('chart_data', [
            'label' => __('Itens do Gráfico', 'alpha-form'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ label }}}',
        ]);

        $this->add_control('chart_type', [
            'label' => __('Tipo de Gráfico', 'alpha-form'),
            'type' => Controls_Manager::SELECT,
            'default' => 'bar',
            'options' => [
                'bar' => 'Barra',
                'line' => 'Linha',
                'pie' => 'Pizza',
                'doughnut' => 'Rosquinha',
                'radar' => 'Radar',
                'polarArea' => 'Área Polar',
            ],
        ]);

        $this->add_control(
            'show_legend',
            [
                'label' => __('Exibir Legenda?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // EIXO X
        $this->add_control(
            'show_axis_x',
            [
                'label' => __('Exibir Eixo X?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'x_grid',
            [
                'label' => __('Exibir Grid X?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_axis_x' => 'yes',                    
                ],
            ]
        );

        $this->add_control(
            'x_border',
            [
                'label' => __('Exibir Borda do Eixo X?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_axis_x' => 'yes',                    
                ],
            ]
        );

        $this->add_control(
            'x_ticks',
            [
                'label' => __('Exibir Ticks do Eixo X?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_axis_x' => 'yes',                    
                ],
            ]
        );

        $this->add_control(
            'x_max_ticks',
            [
                'label' => __('Máximo de Ticks no Eixo X', 'alpha-form'),
                'type' => Controls_Manager::NUMBER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_axis_x' => 'yes',                    
                ],
            ]
        );

        // EIXO Y
        $this->add_control(
            'show_axis_y',
            [
                'label' => __('Exibir Eixo Y?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'y_grid',
            [
                'label' => __('Exibir Grid Y?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_axis_y' => 'yes',                    
                ],
            ]
        );

        $this->add_control(
            'y_border',
            [
                'label' => __('Exibir Borda do Eixo Y?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_axis_y' => 'yes',                    
                ],
            ]
        );

        $this->add_control(
            'y_ticks',
            [
                'label' => __('Exibir Ticks do Eixo Y?', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __('Sim', 'alpha-form'),
                'no' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_axis_y' => 'yes',                    
                ],
            ]
        );

        $this->add_control('y_max_ticks', [
            'label' => __('Máximo de Ticks no Eixo Y', 'alpha-form'),
            'type' => Controls_Manager::NUMBER,
            'default' => 5,
            'condition' => [
                'show_axis_y' => 'yes',                
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section(
            'style_linhas',
            [
                'label' => __('Caixa geral', 'alpha-form'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control('x_grid_style', [
            'label' => __('Estilo da Grade X', 'alpha-form'),
            'type' => Controls_Manager::SELECT,
            'default' => 'solid',
            'options' => [
                'solid' => 'Sólida',
                'dashed' => 'Tracejada',
                'dotted' => 'Pontilhada'
            ],
        ]);

        $this->add_control('x_grid_color', [
            'label' => __('Cor da Grade X', 'alpha-form'),
            'type' => Controls_Manager::COLOR,
            'default' => '#cccccc',
        ]);

        $this->add_control('y_grid_style', [
            'label' => __('Estilo da Grade Y', 'alpha-form'),
            'type' => Controls_Manager::SELECT,
            'default' => 'solid',
            'options' => [
                'solid' => 'Sólida',
                'dashed' => 'Tracejada',
                'dotted' => 'Pontilhada'
            ],
        ]);

        $this->add_control('y_grid_color', [
            'label' => __('Cor da Grade Y', 'alpha-form'),
            'type' => Controls_Manager::COLOR,
            'default' => '#cccccc',
        ]);

        $this->add_control('dataset_border_width', [
            'label' => __('Espessura da Borda', 'alpha-form'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 10]],
            'default' => ['size' => 1],
        ]);

        $this->add_control('border_radius', [
            'label' => __('Borda Arredondada', 'alpha-form'),
            'type' => Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 1, 'max' => 50]],
            'default' => ['size' => 6],
        ]);

        $this->end_controls_section();
    }



    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        $chart_type = esc_js($settings['chart_type']);
        $element_name = esc_js($settings['element_name']);
        $show_legend = $settings['show_legend'] === 'yes' ? 'true' : 'false';

        // Eixo X
        $show_axis_x = $settings['show_axis_x'] === 'yes';
        $x_grid = $settings['x_grid'] === 'yes';
        $x_border = $settings['x_border'] === 'yes';
        $x_ticks = $settings['x_ticks'] === 'yes';
        $x_max_ticks = intval($settings['x_max_ticks']);

        // Eixo Y
        $show_axis_y = $settings['show_axis_y'] === 'yes';
        $y_grid = $settings['y_grid'] === 'yes';
        $y_border = $settings['y_border'] === 'yes';
        $y_ticks = $settings['y_ticks'] === 'yes';
        $y_max_ticks = intval($settings['y_max_ticks']);


        $labels = [];
        $values = [];
        $bg_colors = [];
        $bd_colors = [];

        $radius = intval($settings['border_radius']['size'] ?? 0);
        if (!empty($settings['chart_data'])) {
            foreach ($settings['chart_data'] as $item) {
                $labels[] = '"' . esc_js($item['label']) . '"';
                $values[] = floatval($item['value']);
                $bg_colors[] = '"' . esc_js($item['background_color'] ?? '#000') . '"';
                $bd_colors[] = '"' . esc_js($item['border_color'] ?? '#000') . '"';
            }
        }

        $labels = array_map('esc_js', array_column($settings['chart_data'], 'label'));
        $values = array_map('floatval', array_column($settings['chart_data'], 'value'));
        $bg_colors = array_map(function ($item) {
            return esc_js($item['background_color'] ?? '#000');
        }, $settings['chart_data']);
        $bd_colors = array_map(function ($item) {
            return esc_js($item['border_color'] ?? '#000');
        }, $settings['chart_data']);

        $colorx = esc_js($settings['x_grid_color'] ?? '#cccccc');
        $colory = esc_js($settings['y_grid_color'] ?? '#cccccc');

        echo '<script src="' . ALPHA_FORM_URL . 'assets/js/chart.js"></script>';
        echo '<div class="alpha-form-chart-wrapper">';
        echo '<canvas id="chart_' . esc_attr($widget_id) . '"></canvas>';
        echo '</div>';

        echo "<script>
            (function(){
                // sobrescreve o draw padrão com borda arredondada
                Chart.elements.Rectangle.prototype.draw = function() {
                    var ctx = this._chart.ctx;
                    var vm = this._view;
                    var left, right, top, bottom;
                    var radius = {$radius};                   

                    if (!vm.horizontal) {
                        left = vm.x - vm.width / 2;
                        right = vm.x + vm.width / 2;
                        top = vm.y;
                        bottom = vm.base;
                    } else {
                        left = vm.base;
                        right = vm.x;
                        top = vm.y - vm.height / 2;
                        bottom = vm.y + vm.height / 2;
                    }

                    ctx.beginPath();
                    ctx.fillStyle = vm.backgroundColor;
                    ctx.strokeStyle = vm.borderColor;
                    ctx.lineWidth = vm.borderWidth;

                    var cornerRadius = Math.min(radius, Math.abs(bottom - top) / 2, Math.abs(right - left) / 2);
                    var x = left;
                    var y = top;
                    var width = right - left;
                    var height = bottom - top;

                    ctx.moveTo(x + cornerRadius, y);
                    ctx.lineTo(x + width - cornerRadius, y);
                    ctx.quadraticCurveTo(x + width, y, x + width, y + cornerRadius);
                    ctx.lineTo(x + width, y + height - cornerRadius);
                    ctx.quadraticCurveTo(x + width, y + height, x + width - cornerRadius, y + height);
                    ctx.lineTo(x + cornerRadius, y + height);
                    ctx.quadraticCurveTo(x, y + height, x, y + height - cornerRadius);
                    ctx.lineTo(x, y + cornerRadius);
                    ctx.quadraticCurveTo(x, y, x + cornerRadius, y);

                    ctx.fill();
                    if (vm.borderWidth) ctx.stroke();
                };

                const ctx = document.getElementById('chart_{$widget_id}').getContext('2d');
                new Chart(ctx, {
                    type: '{$chart_type}',
                    data: {
                        labels: " . json_encode($labels) . ",
                        datasets: [{
                            label: '{$element_name}',
                            backgroundColor: " . json_encode($bg_colors) . ",
                            borderColor: " . json_encode($bd_colors) . ",
                            data: " . json_encode($values) . ",
                            borderWidth: " . intval($settings['dataset_border_width']['size']) . ",
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: {$show_legend}
                        },
                        scales: {
                            xAxes: [{
                                display: " . ($show_axis_x ? 'true' : 'false') . ",
                                gridLines: {
                                    display: " . ($y_grid ? 'true' : 'false') . ",
                                    drawBorder: " . ($x_border ? 'true' : 'false') . ",
                                    borderDash: " . json_encode(
                                            $settings['x_grid_style'] === 'dashed' ? [10, 5] : ($settings['x_grid_style'] === 'dotted' ? [2, 3] : [])
                                        ) . ",
                                    color: '{$colorx}'
                                },
                                ticks: {
                                    display: " . ($x_ticks ? 'true' : 'false') . ",
                                    maxTicksLimit: {$x_max_ticks}
                                }
                            }],
                            yAxes: [{
                                display: " . ($show_axis_y ? 'true' : 'false') . ",
                                gridLines: {
                                    display: " . ($x_grid ? 'true' : 'false') . ",
                                    drawBorder: " . ($y_border ? 'true' : 'false') . ",
                                    borderDash: " . json_encode($settings['y_grid_style'] === 'dashed' ? [10, 5] : ($settings['y_grid_style'] === 'dotted' ? [2, 3] : [])) . ",
                                    color: '{$colory}'
                                },
                                ticks: {
                                    display: " . ($y_ticks ? 'true' : 'false') . ",
                                    maxTicksLimit: {$y_max_ticks},
                                    min: 0,
                                    stepSize: 10,
                                    beginAtZero: true,
                                }
                            }]
                        }
                    }
                });
            })();
            </script>";
    }
}
