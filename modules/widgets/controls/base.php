<?php

namespace AlphaForm\Module\Widget\Controls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Plugin;
use Elementor\Repeater;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Nested Alpha Form widget.
 *
 * Elementor widget that displays a collapsible display of content in an
 * form style.
 *
 * @since 3.15.0
 */
class Alpha_Form_Minimal extends Widget_Nested_Base
{

    private $optimized_markup = null;
    private $widget_container_selector = '';

    public function get_name()
    {
        return 'alpha-form';
    }

    public function get_title()
    {
        return esc_html__('Alpha Form Base', 'alpha-form');
    }

    public function get_icon()
    {
        return 'eicon-accordion';
    }

    public function get_keywords()
    {
        return ['nested', 'form', 'toggle', 'alpha', 'formulario'];
    }

    public function get_style_depends(): array
    {
        return ['widget-nested-form'];
    }

    public function get_script_depends()
    {
        return ['alpha-accordion'];
    }

    public function get_categories()
    {
        return ['alpha-form'];
    }

    protected function item_content_container(int $index)
    {
        return [
            'elType' => 'container',
            'settings' => [
                '_title' => sprintf(__('item #%s', 'alpha-form'), $index),
                'content_width' => 'full',
            ],
        ];
    }

    protected function get_default_children_elements()
    {
        return [
            $this->item_content_container(1),
            $this->item_content_container(2),
            $this->item_content_container(3),
        ];
    }

    protected function get_default_repeater_title_setting_key()
    {
        return 'item_title';
    }

    protected function get_default_children_title()
    {
        return esc_html__('Item #%d', 'alpha-form');
    }

    protected function get_default_children_placeholder_selector()
    {
        return '.alpha-f-n';
    }

    protected function get_default_children_container_placeholder_selector()
    {
        return '.alpha-f-n-item';
    }

    protected function get_html_wrapper_class()
    {
        return 'widget-alpha-form-n';
    }

    protected function register_controls()
    {
        if (null === $this->optimized_markup) {
            $this->optimized_markup = Plugin::$instance->experiments->is_feature_active('e_optimized_markup') && ! $this->has_widget_inner_wrapper();
            $this->widget_container_selector = $this->optimized_markup ? '' : ' > .elementor-widget-container';
        }

        $this->start_controls_section('section_items', [
            'label' => esc_html__('Estrutura do Formulário', 'alpha-form'),
        ]);
        $this->add_control(
            'form_name',
            [
                'label' => __('Nome do Formulário', 'alpha-form'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'Alpha Form',
                'default' => 'Alpha Form',
                'render_type' => 'none',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'item_title',
            [
                'label' => esc_html__('Title', 'alpha-form'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Item Title', 'alpha-form'),
                'placeholder' => esc_html__('Item Title', 'alpha-form'),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'ai' => [
                    'active' => false,
                ],
                'render_type' => 'none',
            ]
        );

        $repeater->add_control(
            'element_css_id',
            [
                'label' => esc_html__('CSS ID', 'alpha-form'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'ai' => [
                    'active' => false,
                ],
                'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'alpha-form'),
                'style_transfer' => false,
            ]
        );

        $this->add_control(
            'items',
            [
                'label' => esc_html__('Items', 'alpha-form'),
                'type' => Control_Nested_Repeater::CONTROL_TYPE,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'item_title' => esc_html__('Item #1', 'alpha-form'),
                    ],
                    [
                        'item_title' => esc_html__('Item #2', 'alpha-form'),
                    ],
                    [
                        'item_title' => esc_html__('Item #3', 'alpha-form'),
                    ],
                ],
                'title_field' => '{{{ item_title }}}',
                'button_text' => esc_html__('Add Item', 'alpha-form'),
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_editor',
            [
                'label' => esc_html__('Itens do editor', 'alpha-form'),
            ]
        );

        $this->add_control(
            'heading_form_item_title_icon',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Icon', 'alpha-form'),
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'form_item_title_icon',
            [
                'label' => esc_html__('Expand', 'alpha-form'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-plus',
                    'library' => 'fa-solid',
                ],
                'skin' => 'inline',
                'label_block' => false,
            ]
        );

        $this->add_control(
            'form_item_title_icon_active',
            [
                'label' => esc_html__('Collapse', 'alpha-form'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon_active',
                'default' => [
                    'value' => 'fas fa-minus',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'form_item_title_icon[value]!' => '',
                ],
                'skin' => 'inline',
                'label_block' => false,
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'alpha-form'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'selectors_dictionary' => [
                    'h1' => 'alpha-f-n-title-font-size: 2.5rem;',
                    'h2' => 'alpha-f-n-title-font-size: 2rem;',
                    'h3' => 'alpha-f-n-title-font-size: 1,75rem;',
                    'h4' => 'alpha-f-n-title-font-size: 1.5rem;',
                    'h5' => 'alpha-f-n-title-font-size: 1rem;',
                    'h6' => 'alpha-f-n-title-font-size: 1rem; ',
                    'div' => 'alpha-f-n-title-font-size: 1rem;',
                    'span' => 'alpha-f-n-title-font-size: 1rem; ',
                    'p' => 'alpha-f-n-title-font-size: 1rem;',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '{{VALUE}}',
                ],
                'default' => 'div',
                'separator' => 'before',
                'render_type' => 'template',

            ]
        );

        $this->end_controls_section();

        //sessão de controllers
        $this->start_controls_section(
            'section_form_view',
            [
                'label' => __('Vizualizações nos formulários', 'alpha-form-premium'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Localização do usuário
        $this->add_control(
            'enable_geolocation',
            [
                'label' => __('Ativar geolocalização', 'alpha-form-premium'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'alpha-form-premium'),
                'label_off' => __('Não', 'alpha-form-premium'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        // Ocultar tela de envio
        $this->add_control(
            'show_submit_screen',
            [
                'label' => __('Mostrar tela de envio', 'alpha-form-premium'),
                'description' => __('Se desabilitado não envia para o último campo. Ideal para páginas de captura com alta conversão.'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'alpha-form-premium'),
                'label_off' => __('Não', 'alpha-form-premium'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_required_mark',
            [
                'label' => __('Marcar obrigatórios', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'alpha-form'),
                'label_off' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->add_style_tab();
    }

    private function add_style_tab()
    {
        $this->add_form_style_section();
        $this->add_header_style_section();
        $this->add_content_style_section();
    }

    private function add_form_style_section()
    {
        $this->start_controls_section(
            'section_form_style',
            [
                'label' => esc_html__('Acordeão editor', 'alpha-form'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'form_item_title_space_between',
            [
                'label' => esc_html__('Espaço entre itens', 'alpha-form'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                    'em' => [
                        'max' => 20,
                    ],
                    'rem' => [
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .alpha-f-n' => 'gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->start_controls_tabs('form_border_and_background');

        foreach (['normal', 'hover', 'active'] as $state) {
            $this->add_border_and_radius_style($state);
        }

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    private function add_content_style_section()
    {
        $low_specificity_form_item_selector = ":where( {{WRAPPER}}{$this->widget_container_selector} > .alpha-f-n > .alpha-f-n-item ) > .e-con";

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__('Conteúdo', 'alpha-form'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => $low_specificity_form_item_selector,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'selector' => $low_specificity_form_item_selector,
                'fields_options' => [
                    'color' => [
                        'label' => esc_html__('Border Color', 'alpha-form'),
                    ],
                    'width' => [
                        'label' => esc_html__('Border Width', 'alpha-form'),
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'content_border_radius',
            [
                'label' => esc_html__('Border Radius', 'alpha-form'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'selectors' => [
                    $low_specificity_form_item_selector => '--border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__('Padding', 'alpha-form'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    $low_specificity_form_item_selector => '--padding-top: {{TOP}}{{UNIT}}; --padding-right: {{RIGHT}}{{UNIT}}; --padding-bottom: {{BOTTOM}}{{UNIT}}; --padding-left: {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function add_header_style_section()
    {
        $this->start_controls_section(
            'section_header_style',
            [
                'label' => esc_html__('Header', 'alpha-form'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_header_style_title',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Title', 'alpha-form'),

            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => ":where( {{WRAPPER}}{$this->widget_container_selector} > .alpha-f-n > .alpha-f-n-item > .alpha-f-n-item-title > .alpha-f-n-item-title-header ) > .alpha-f-n-item-title-text",
                'fields_options' => [
                    'font_size' => [
                        'selectors' => [
                            '{{WRAPPER}}' => '--n-form-title-font-size: {{SIZE}}{{UNIT}}',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * @string $state
     */
    private function add_border_and_radius_style($state)
    {
        $selector = "{{WRAPPER}}{$this->widget_container_selector} > .alpha-f-n > .alpha-f-n-item > .alpha-f-n-item-title";

        $translated_tab_text = esc_html__('Normal', 'alpha-form');

        switch ($state) {
            case 'hover':
                $selector .= ':hover';
                $translated_tab_text = esc_html__('Hover', 'alpha-form');
                break;
            case 'active':
                $selector = "{{WRAPPER}}{$this->widget_container_selector} > .alpha-f-n > .alpha-f-n-item[open] > .alpha-f-n-item-title";
                $translated_tab_text = esc_html__('Active', 'alpha-form');
                break;
        }

        $this->start_controls_tab(
            'form_' . $state . '_border_and_background',
            [
                'label' => $translated_tab_text,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'form_background_' . $state,
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'fields_options' => [
                    'color' => [
                        'label' => esc_html__('Color', 'alpha-form'),
                    ],
                ],
                'selector' => $selector,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_border_' . $state,
                'selector' => $selector,
            ]
        );

        $this->end_controls_tab();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $items = $settings['items'];
        $show_required_mark = 'data-show-required=' . $settings["show_required_mark"] . '' ?? '';
        $form_name = $settings['form_name'] ?? 'Alpha Form';
        $id_int = substr($this->get_id_int(), 0, 3);
        $items_title_html = '';
        $this->add_render_attribute('alpha-form', 'class', 'alpha-f-n');
        $this->add_render_attribute('alpha-form', 'aria-label', 'Alpha Form. Open links with Enter or Space, close with Escape, and navigate with Arrow Keys');
        $default_state = $settings['default_state'] ?? 'expanded';
        $form_id = $this->get_id();


        echo '<form class="alpha-form" data-alpha-widget-id="' . esc_attr($form_id) . '" data-form-name="' . esc_attr($form_name) . '" ' . esc_attr($show_required_mark) . '>';
        foreach ($items as $index => $item) {
            $form_count = $index + 1;
            $item_setting_key = $this->get_repeater_setting_key('item_title', 'items', $index);
            $item_summary_key = $this->get_repeater_setting_key('item_summary', 'items', $index);
            $item_classes = ['alpha-f-n-item'];
            $item_id = empty($item['element_css_id']) ? 'alpha-f-n-item-' . $id_int . $index : $item['element_css_id'];
            $is_open = 'expanded' === $default_state && 0 === $index ? 'open' : '';
            $aria_expanded = 'expanded' === $default_state && 0 === $index;
            $step_class  = 'alpha-form-step' . ($index === 0 ? ' active' : '');

            $this->add_render_attribute($item_setting_key, [
                'id' => $item_id,
                'class' => $item_classes,
            ]);

            $this->add_render_attribute($item_summary_key, [
                'class' => ['alpha-f-n-item-title'],
                'data-form-index' => $form_count,
                'tabindex' => 0 === $index ? 0 : -1,
                'aria-expanded' => $aria_expanded ? 'true' : 'false',
                'aria-controls' => $item_id,
            ]);

            $title_render_attributes = $this->get_render_attribute_string($item_setting_key);
            $title_render_attributes = $title_render_attributes . ' ' . $is_open;

            // items content.
            ob_start();
            $this->print_child($index, $item_id);
            $item_content = ob_get_clean();

            ob_start();
?>
            <div class="alpha-form-field <?php echo esc_attr($step_class) ?>">
                <?php echo $item_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
            </div>
        <?php
            $items_title_html .= ob_get_clean();
        }

        ?>
        <div <?php $this->print_render_attribute_string('alpha-form'); ?>>
            <?php echo $items_title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
            ?>
        </div>
    <?php
        echo '</form>';
    }



    protected function add_attributes_to_container($container, $item_id)
    {
        $container->add_render_attribute('_wrapper', [
            'role' => 'region',
            'aria-labelledby' => $item_id,
        ]);
    }

    protected function get_initial_config(): array
    {
        return array_merge(parent::get_initial_config(), [
            'support_improved_repeaters' => true,
            'target_container' => ['.alpha-f-n'],
            'node' => 'div',
            'is_interlaced' => true,
        ]);
    }

    protected function content_template_single_repeater_item()
    {
    ?>
        <#
            const elementUid=view.getIDInt().toString().substring( 0, 3 ) + view.collection.length;

            const itemWrapperAttributes={ 'id' : 'alpha-f-n-item-' + elementUid, 'class' : [ 'alpha-f-n-item' , 'e-normal' ],
            };

            const itemTitleAttributes={ 'class' : [ 'alpha-f-n-item-title' ], 'data-form-index' : view.collection.length + 1, 'tabindex' : -1, 'aria-expanded' : 'false' , 'aria-controls' : 'alpha-f-n-item-' + elementUid,
            };

            const itemTitleTextAttributes={ 'class' : [ 'alpha-f-n-item-title-text' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'items' , 'data-binding-setting' : ['item_title'], 'data-binding-index' : view.collection.length + 1, 'data-binding-dynamic' : 'true' ,
            };

            view.addRenderAttribute( 'div-container' , itemWrapperAttributes, null, true );
            view.addRenderAttribute( 'summary-container' , itemTitleAttributes, null, true );
            view.addRenderAttribute( 'text-container' , itemTitleTextAttributes, null, true );
            #>

            <div {{{ view.getRenderAttributeString( 'div-container' ) }}}>
                <summary {{{ view.getRenderAttributeString( 'summary-container' ) }}}>
                    <span class="alpha-f-n-item-title-header">
                        <div {{{ view.getRenderAttributeString( 'text-container' ) }}}>{{{ data.item_title }}}</div>
                    </span>
                </summary>
            </div>
        <?php
    }

    protected function content_template()
    {
        ?>
            <div class="alpha-f-n" data-alpha-widget-id="{{ view.getID() }}" data-form-name="{{{ settings['form_name'] }}}">
                <# if ( settings['items'] ) {
                    const elementUid=view.getIDInt().toString().substring( 0, 3 ),
                    titleHTMLTag=elementor.helpers.validateHTMLTag( settings.title_tag ),
                    defaultState=settings.default_state,
                    itemTitleIcon=elementor.helpers.renderIcon( view, settings['form_item_title_icon'], { 'aria-hidden' : true }, 'i' , 'object' ) ?? '' ,
                    itemTitleIconActive=''===settings.form_item_title_icon_active.value
                    ? itemTitleIcon
                    : elementor.helpers.renderIcon( view, settings['form_item_title_icon_active'], { 'aria-hidden' : true }, 'i' , 'object' );
                    #>

                    <# _.each( settings['items'], function( item, index ) {
                        const itemCount=index + 1,
                        itemUid=elementUid + index,
                        itemTitleTextKey='item-title-text-' + itemUid,
                        itemWrapperKey=itemUid,
                        itemTitleKey='item-' + itemUid,
                        ariaExpanded='expanded'===defaultState && 0===index ? 'true' : 'false' ;

                        if ( '' !==item.element_css_id ) {
                        itemId=item.element_css_id;
                        } else {
                        itemId='alpha-f-n-item-' + itemUid;
                        }

                        const itemWrapperAttributes={ 'id' : itemId, 'class' : [ 'alpha-f-n-item' , 'e-normal' ],
                        };

                        if ( defaultState==='expanded' && index===0) {
                        itemWrapperAttributes['open']=true;
                        }

                        view.addRenderAttribute( itemWrapperKey, itemWrapperAttributes );

                        view.addRenderAttribute( itemTitleKey, { 'class' : ['alpha-f-n-item-title'], 'data-form-index' : itemCount, 'tabindex' : 0===index ? 0 : -1, 'aria-expanded' : ariaExpanded, 'aria-controls' : itemId,
                        });

                        view.addRenderAttribute( itemTitleTextKey, { 'class' : ['alpha-f-n-item-title-text'], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'items' , 'data-binding-setting' : ['item_title'], 'data-binding-index' : itemCount, 'data-binding-dynamic' : 'true' , 'data-binding-dynamic-css-id' : 'element_css_id' , 'data-binding-single-item-html-wrapper-tag' : 'div' ,
                        });
                        #>

                        <div {{{ view.getRenderAttributeString( itemWrapperKey ) }}}>
                            <summary {{{ view.getRenderAttributeString( itemTitleKey ) }}}>
                                <span class="alpha-f-n-item-title-header">
                                    <{{{ titleHTMLTag }}} {{{ view.getRenderAttributeString( itemTitleTextKey ) }}}>
                                        {{{ item.item_title }}}
                                    </{{{ titleHTMLTag }}}>
                                </span>
                                <# if (settings.form_item_title_icon.value) { #>
                                    <span class="alpha-f-n-item-title-icon">
                                        <span class="e-opened">{{{ itemTitleIconActive.value }}}</span>
                                        <span class="e-closed">{{{ itemTitleIcon.value }}}</span>
                                    </span>
                                    <# } #>
                            </summary>
                        </div>
                        <# } ); #>
                            <# } #>
            </div>
    <?php
    }
}
