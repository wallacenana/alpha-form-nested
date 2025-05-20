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
        return 'item_title_alpha';
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

        $this->start_controls_section('section_items_alpha', [
            'label' => esc_html__('Estrutura do Formulário', 'alpha-form'),
        ]);
        $this->add_control(
            'form_name_alpha',
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
            'item_title_alpha',
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
            'element_css_id_alpha',
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
            'items_alpha',
            [
                'label' => esc_html__('Items', 'alpha-form'),
                'type' => Control_Nested_Repeater::CONTROL_TYPE,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'item_title_alpha' => esc_html__('Item #1', 'alpha-form'),
                    ],
                    [
                        'item_title_alpha' => esc_html__('Item #2', 'alpha-form'),
                    ],
                    [
                        'item_title_alpha' => esc_html__('Item #3', 'alpha-form'),
                    ],
                ],
                'title_field' => '{{{ item_title_alpha }}}',
                'button_text' => esc_html__('Adicionar questão', 'alpha-form'),
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_editor_alpha',
            [
                'label' => esc_html__('Itens do editor', 'alpha-form'),
            ]
        );

        $this->add_control(
            'heading_form_item_title_icon_alpha',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Icone', 'alpha-form'),
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'form_item_title_icon_alpha',
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
            'form_item_title_icon_active_alpha',
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
            'title_tag_alpha',
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
            'section_form_view_alpha',
            [
                'label' => __('Vizualizações nos formulários', 'alpha-form-premium'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Localização do usuário
        $this->add_control(
            'enable_geolocation_alpha',
            [
                'label' => __('Ativar geolocalização', 'alpha-form-premium'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'alpha-form-premium'),
                'label_off' => __('Não', 'alpha-form-premium'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => 'Pega dados de geolocalização (não é uma informação totalmente precisa, mas dá uma região próxima)',

            ]
        );

        $this->add_control(
            'enable_msg_to_exit_alpha',
            [
                'label' => __('Ativar Mensagem ao sair', 'alpha-form-premium'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'alpha-form-premium'),
                'label_off' => __('Não', 'alpha-form-premium'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => 'Quando o usuário tentar sair da página que iniciou o preenchimento, exibe uma mensagem de confirmação de saída',

            ]
        );

        $this->add_control(
            'enable_retornar_dados_alpha',
            [
                'label' => __('Retornar dados ao sair', 'alpha-form-premium'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'alpha-form-premium'),
                'label_off' => __('Não', 'alpha-form-premium'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => 'Quando o usuário retornar a página, continuara o preenchimento de onde parou',
            ]
        );

        $this->add_control(
            'show_required_mark_alpha',
            [
                'label' => __('Marcar obrigatórios', 'alpha-form'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Sim', 'alpha-form'),
                'label_off' => __('Não', 'alpha-form'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => 'Insere um asterisco nas perguntas obrigatórias',

            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_overlay',
            [
                'label' => esc_html__('Overlay de Envio', 'alpha-form'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_overlay',
            [
                'label'        => esc_html__('Exibir overlay durante envio', 'alpha-form'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Sim', 'alpha-form'),
                'label_off'    => esc_html__('Não', 'alpha-form'),
                'default'      => 'yes',
            ]
        );
        $this->add_control(
            'show_editor',
            [
                'label'        => esc_html__('Exibir no editor', 'alpha-form'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Sim', 'alpha-form'),
                'label_off'    => esc_html__('Não', 'alpha-form'),
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'overlay_loader_image',
            [
                'label' => esc_html__('GIF de carregamento', 'alpha-form'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'media_types' => ['image'],
                'condition' => [
                    'show_overlay' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();


        // 🔹 Seção principal
        $this->start_controls_section(
            'section_post_submit_alpha',
            [
                'label' => __('Ações após envio', 'alpha-form'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'post_submit_actions_alpha',
            [
                'label' => __('Ações após envio', 'alpha-form'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'options' => [
                    'collect'         => 'Coletar Submissão',
                    'redirect'        => 'Redirect',
                    'webhook'         => 'Webhook',
                    'active-campaign' => 'ActiveCampaign',
                    'mailchimp'       => 'Mailchimp',
                    'drip'            => 'Drip',
                    'getresponse'     => 'GetResponse',
                    'clicksend'       => 'ClickSend',
                    'convertkit'      => 'ConvertKit',
                    'mailerlite'      => 'MailerLite',
                ],

                'default' => ['collect'],
                'description' => 'Selecione as ações que devem ocorrer após o envio do formulário.',
            ]
        );

        $this->end_controls_section();

        // sessão redirect
        $this->start_controls_section(
            'section_redirect_alpha',
            [
                'label' => __('[Ação] Redirecionamento', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'redirect',
                ],

            ]
        );

        $this->add_control(
            'redirect_url_alpha',
            [
                'label' => __('URL de redirecionamento', 'alpha-form'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'https://seusite.com/obrigado',
                'description' => 'Você pode usar shortcodes como [field-nome] na URL.',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // sessão webhook
        $this->start_controls_section(
            'section_webhook_alpha',
            [
                'label' => __('[Ação] Webhook', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'webhook',
                ],

            ]
        );

        $this->add_control(
            'webhook_url',
            [
                'label' => __('URL do Webhook', 'alpha-form'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'https://api.exemplo.com/webhook',
                'ai' => [
                    'active' => false,
                ],
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // sessão mailchimp
        $this->start_controls_section(
            'section_mailchimp',
            [
                'label' => __('[Ação] Mailchimp', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'mailchimp',
                ],
            ]
        );

        $this->add_control('mailchimp_load_lists', [
            'label' => __('Receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:load_data_mailchimp',
            'description' => 'Clique para popular os campos abaixo',
        ]);

        $this->add_control('mailchimp_source_type', [
            'label' => 'API',
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'custom' => 'Inserir manualmente',
            ],
            'condition' => [
                'post_submit_actions_alpha' => 'mailchimp',
            ],
        ]);

        $this->add_control('mailchimp_custom_api_key', [
            'label' => 'API Key',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave da API',
            'condition' => [
                'mailchimp_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('mailchimp_custom_server', [
            'label' => 'Data Center',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Ex: us21',
            'condition' => [
                'mailchimp_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('mailchimp_list_id', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_options('mailchimp'),
            'condition' => [
                'mailchimp_source_type' => 'default',
            ],

        ]);

        $this->add_control('mailchimp_list_id_custom', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'mailchimp_source_type' => 'custom',
            ],
        ]);

        $this->controles_integracao('mc');

        $this->end_controls_section();



        // sessão active-campaign
        $this->start_controls_section(
            'section_active',
            [
                'label' => __('[Ação] ActiveCampaign', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'active-campaign',
                ],
            ]
        );

        $this->add_control('active-campaign_load_lists', [
            'label' => __('Receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:load_data_active-campaign',
            'description' => 'Clique para popular os campos abaixo',
        ]);

        $this->add_control('active-campaign_source_type', [
            'label' => 'API',
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'custom' => 'Inserir manualmente',
            ],
            'condition' => [
                'post_submit_actions_alpha' => 'active-campaign',
            ],
        ]);

        $this->add_control('active-campaign_custom_api_key', [
            'label' => 'API url',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave da API',
            'condition' => [
                'active-campaign_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('active-campaign_custom_server', [
            'label' => 'API key',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave aqui',
            'condition' => [
                'active-campaign_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('active-campaign_list_id', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_options('active-campaign'),
            'condition' => [
                'active-campaign_source_type' => 'default',
            ],
        ]);

        $this->add_control('active-campaign_list_id_custom', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'active-campaign_source_type' => 'custom',
            ],
        ]);

        $this->controles_integracao('ac');

        $this->end_controls_section();
        // termina a sessão aqui


        // sessão getresponse
        $this->start_controls_section(
            'section_getresponse',
            [
                'label' => __('[Ação] GetResponse', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'getresponse',
                ],
            ]
        );

        $this->add_control('getresponse_load_lists', [
            'label' => __('Receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:load_data_getresponse',
            'description' => 'Clique para popular os campos abaixo',
        ]);

        $this->add_control('getresponse_source_type', [
            'label' => 'API',
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'custom' => 'Inserir manualmente',
            ],
        ]);

        $this->add_control('getresponse_custom_api_key', [
            'label' => 'API Key',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave da API',
            'condition' => [
                'getresponse_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('getresponse_list_id', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_options('getresponse'),
            'condition' => [
                'getresponse_source_type' => 'default',
            ],
        ]);

        $this->add_control('getresponse_list_id_custom', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'getresponse_source_type' => 'custom',
            ],
        ]);

        $this->controles_integracao('gr');

        $this->end_controls_section();
        // termina a sessão aqui



        // sessão convertkit
        $this->start_controls_section(
            'section_convertkit',
            [
                'label' => __('[Ação] Convertkit', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'convertkit',
                ],
            ]
        );

        $this->add_control('convertkit_load_lists', [
            'label' => __('Receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:load_data_convertkit',
            'description' => 'Clique para popular os campos abaixo',
        ]);

        $this->add_control('convertkit_source_type', [
            'label' => 'API',
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'custom' => 'Inserir manualmente',
            ],
        ]);

        $this->add_control('convertkit_custom_api_key', [
            'label' => 'API Key',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave da API',
            'condition' => [
                'convertkit_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('convertkit_list_id', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_options('convertkit'),
            'condition' => [
                'convertkit_source_type' => 'default',
            ],
        ]);

        $this->add_control('convertkit_list_id_custom', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'convertkit_source_type' => 'custom',
            ],
        ]);

        $this->controles_integracao('ck');

        $this->end_controls_section();
        // termina a sessão aqui



        // sessão mailerlite
        $this->start_controls_section(
            'section_mailerlite',
            [
                'label' => __('[Ação] MailerLite', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'mailerlite',
                ],
            ]
        );

        $this->add_control('mailerlite_load_lists', [
            'label' => __('Receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:load_data_mailerlite',
            'description' => 'Clique para popular os campos abaixo',
        ]);

        $this->add_control('mailerlite_source_type', [
            'label' => 'API',
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'custom' => 'Inserir manualmente',
            ],
        ]);

        $this->add_control('mailerlite_custom_api_key', [
            'label' => 'API Key',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave da API',
            'condition' => [
                'mailerlite_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('mailerlite_list_id', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_options('mailerlite'),
            'condition' => [
                'mailerlite_source_type' => 'default',
            ],
        ]);

        $this->add_control('mailerlite_list_id_custom', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'mailerlite_source_type' => 'custom',
            ],
        ]);

        $this->controles_integracao('ml');

        $this->end_controls_section();
        // termina a sessão aqui



        // sessão drip
        $this->start_controls_section(
            'section_drip',
            [
                'label' => __('[Ação] Drip', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'drip',
                ],
            ]
        );

        $this->add_control('drip_load_lists', [
            'label' => __('Receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:load_data_drip',
            'description' => 'Clique para popular os campos abaixo',
        ]);

        $this->add_control('drip_source_type', [
            'label' => 'API',
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'custom' => 'Inserir manualmente',
            ],
        ]);

        $this->add_control('drip_custom_api_key', [
            'label' => 'API Key',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave da API',
            'condition' => [
                'drip_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('drip_list_id', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_options('drip'),
            'condition' => [
                'drip_source_type' => 'default',
            ],
        ]);

        $this->add_control('drip_list_id_custom', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'drip_source_type' => 'custom',
            ],
        ]);

        $this->controles_integracao('drip');

        $this->end_controls_section();
        // termina a sessão aqui



        // sessão clicksend
        $this->start_controls_section(
            'section_clicksend',
            [
                'label' => __('[Ação] ClickSend', 'alpha-form'),
                'condition' => [
                    'post_submit_actions_alpha' => 'clicksend',
                ],
            ]
        );

        $this->add_control('clicksend_load_lists', [
            'label' => __('Receber dados', 'alpha-form-premium'),
            'type' => Controls_Manager::BUTTON,
            'button_type' => 'success',
            'text' => __('Receber', 'alpha-form-premium'),
            'event' => 'alphaform:editor:load_data_clicksend',
            'description' => 'Clique para popular os campos abaixo',
        ]);

        $this->add_control('clicksend_source_type', [
            'label' => 'API',
            'type' => Controls_Manager::SELECT,
            'default' => 'default',
            'options' => [
                'default' => 'Default',
                'custom' => 'Inserir manualmente',
            ],
        ]);

        $this->add_control('clicksend_custom_api_key', [
            'label' => 'API Key',
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Chave da API',
            'condition' => [
                'clicksend_source_type' => 'custom',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);

        $this->add_control('clicksend_list_id', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_options('clicksend'),
            'condition' => [
                'clicksend_source_type' => 'default',
            ],
        ]);

        $this->add_control('clicksend_list_id_custom', [
            'label' => 'Selecione a Lista',
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'clicksend_source_type' => 'custom',
            ],
        ]);

        $this->controles_integracao('cs');

        $this->end_controls_section();
        // termina a sessão aqui



        $low_specificity_form_item_selector = ":where( {{WRAPPER}}{$this->widget_container_selector} > .alpha-f-n > .alpha-f-n-item ) > .e-con";

        $this->start_controls_section('section_style_alpha', [
            'label' => esc_html__('Estilo do editor', 'alpha-form'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control(
            'heading_header_style_title_alpha',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Title', 'alpha-form'),

            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography_alpha',
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

        $this->add_control(
            'editor_header_style_title_alpha',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Ítens do editor', 'alpha-form'),
                'separator' => 'before'

            ]
        );
        $this->add_responsive_control(
            'form_item_title_space_between_alpha',
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

        $this->start_controls_tabs('form_border_and_background_alpha');

        foreach (['normal', 'hover', 'active'] as $state) {
            $this->add_border_and_radius_style($state);
        }

        $this->end_controls_tabs();

        $this->add_control(
            'conteudo_header_style_title_alpha',
            [
                'type' => Controls_Manager::HEADING,
                'label' => esc_html__('Conteúdo', 'alpha-form'),
                'separator' => 'before'

            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background_alpha',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => $low_specificity_form_item_selector,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border_alpha_alpha',
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
            'content_border_radius_alpha',
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
            'content_padding_alpha',
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

        $this->start_controls_section(
            'section_overlay_style',
            [
                'label' => esc_html__('Estilo do Overlay', 'alpha-form'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_overlay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'overlay_background',
            [
                'label'     => esc_html__('Cor de fundo', 'alpha-form'),
                'type'      => Controls_Manager::COLOR,
                'default'   => 'rgba(0, 0, 0, 0.6)',
                'selectors' => [
                    '{{WRAPPER}} .alpha-form-overlay' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        // Background padrão do Elementor

        $this->add_control(
            'overlay_image_size',
            [
                'label' => esc_html__('Tamanho do GIF', 'alpha-form'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 120,
                ],
                'selectors' => [
                    '{{WRAPPER}} .alpha-form-overlay img' => 'width: {{SIZE}}{{UNIT}};',
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
            'form_' . $state . '_border_and_background_alpha',
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

    private function controles_integracao($prefix)
    {
        $all_fields = [];
        if ($prefix === 'mc') {

            $all_fields = [
                ['id' => 'email_address', 'title' => 'Email*'],
                ['id' => 'FNAME',        'title' => 'Primeiro Nome'],
                ['id' => 'LNAME',        'title' => 'Último Nome'],
                ['id' => 'PHONE',        'title' => 'Telefone'],
                ['id' => 'BIRTHDAY',     'title' => 'Aniversário'],
                ['id' => 'ADDRESS',      'title' => 'Endereço'],
                ['id' => 'COMPANY',      'title' => 'Empresa'],
            ];
        }

        if ($prefix === 'mc') {
            $all_fields = [
                ['id' => 'email_address', 'title' => 'Email*'],
                ['id' => 'FNAME',         'title' => 'Primeiro Nome'],
                ['id' => 'LNAME',         'title' => 'Último Nome'],
                ['id' => 'PHONE',         'title' => 'Telefone'],
                ['id' => 'BIRTHDAY',      'title' => 'Aniversário'],
                ['id' => 'ADDRESS',       'title' => 'Endereço'],
                ['id' => 'COMPANY',       'title' => 'Empresa'],
            ];
        }

        if ($prefix === 'ac') {
            $all_fields = [
                ['id' => 'email',        'title' => 'Email*'],
                ['id' => 'first_name',   'title' => 'Primeiro Nome'],
                ['id' => 'last_name',    'title' => 'Último Nome'],
                ['id' => 'phone',        'title' => 'Telefone'],
            ];
        }

        if ($prefix === 'gr') {
            $all_fields = [
                ['id' => 'email',    'title' => 'Email*'],
                ['id' => 'name',     'title' => 'Nome'],
                ['id' => 'phone',    'title' => 'Telefone'],
                ['id' => 'city',     'title' => 'Cidade'],
            ];
        }

        if ($prefix === 'drip') {
            $all_fields = [
                ['id' => 'email',        'title' => 'Email*'],
                ['id' => 'first_name',   'title' => 'Primeiro Nome'],
                ['id' => 'last_name',    'title' => 'Último Nome'],
            ];
        }

        if ($prefix === 'ck') {
            $all_fields = [
                ['id' => 'email',        'title' => 'Email*'],
                ['id' => 'first_name',   'title' => 'Primeiro Nome'],
            ];
        }

        if ($prefix === 'ml') {
            $all_fields = [
                ['id' => 'email',    'title' => 'Email*'],
                ['id' => 'name',     'title' => 'Nome'],
                ['id' => 'phone',    'title' => 'Telefone'],
            ];
        }

        if ($prefix === 'cs') {
            $all_fields = [
                ['id' => 'email',    'title' => 'Email*'],
                ['id' => 'name',     'title' => 'Nome'],
                ['id' => 'phone',    'title' => 'Telefone'],
                ['id' => 'address',  'title' => 'Endereço'],
            ];
        }

        foreach ($all_fields as $field) {
            $this->add_control(
                'map_field_' . $field['id'] . '_' . $prefix . '',
                [
                    'label' => $field['title'],
                    'type' => Controls_Manager::SELECT,
                    'options' => [],
                    'default' => '',
                ]
            );
        }
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $items = $settings['items_alpha'];
        $enable_geolocation = $settings['enable_geolocation_alpha'] ? 'data-location' : '';
        $enable_msg_to_exit = $settings['enable_msg_to_exit_alpha'] ? 'data-exit-block' : '';
        $enable_retornar_dados = $settings['enable_retornar_dados_alpha'] ? 'data-return' : '';
        $show_required_mark = 'data-show-required=' . $settings["show_required_mark_alpha"] . '' ?? '';
        $form_name = $settings['form_name_alpha'] ?? 'Alpha Form';
        $id_int = substr($this->get_id_int(), 0, 3);
        $items_title_html = '';
        $this->add_render_attribute('alpha-form', 'class', 'alpha-f-n');
        $this->add_render_attribute('alpha-form', 'aria-label', 'Alpha Form. Open links with Enter or Space, close with Escape, and navigate with Arrow Keys');
        $default_state = $settings['default_state_alpha'] ?? 'expanded';
        $form_id = $this->get_id();

        $fields_mc  = ["email_address", "FNAME", "LNAME", "PHONE", "BIRTHDAY", "ADDRESS", "COMPANY"];
        $fields_ac  = ["email", "first_name", "last_name", "phone"];
        $fields_gr  = ["email", "name", "phone", "city"];
        $fields_drip = ["email", "first_name", "last_name"];
        $fields_ck  = ["email", "first_name"];
        $fields_ml  = ["email", "name", "phone"];
        $fields_cs  = ["email", "name", "phone", "address"];

        $actions_data = [
            'redirect' => [
                'url' => $settings['redirect_url_alpha'],
            ],
            'mailchimp' => [
                'source_type'      => $settings['mailchimp_source_type'],
                'api_key'          => $settings['mailchimp_custom_api_key'] ?? '',
                'server'           => $settings['mailchimp_custom_server'] ?? '',
                'list_id_custom'   => $settings['mailchimp_list_id_custom'] ?? '',
                'list_id'          => $this->alpha_get_integration_id($settings, 'mailchimp'),
                'fields'           => $this->alpha_get_mapped_fields($settings, 'mc', $fields_mc),
            ],
            'active-campaign' => [
                'source_type'      => $settings['active-campaign_source_type'],
                'api_key'          => $settings['active-campaign_custom_api_key'] ?? '',
                'server'           => $settings['active-campaign_custom_server'] ?? '',
                'list_id_custom'   => $settings['active-campaign_list_id_custom'] ?? '',
                'list_id' => $this->alpha_get_integration_id($settings, 'active-campaign'),
                'fields'  => $this->alpha_get_mapped_fields($settings, 'ac', $fields_ac),
            ],
            'getresponse' => [
                'source_type'      => $settings['getresponse_source_type'],
                'api_key'          => $settings['getresponse_custom_api_key'] ?? '',
                'list_id_custom'   => $settings['getresponse_list_id_custom'] ?? '',
                'list_id' => $this->alpha_get_integration_id($settings, 'getresponse'),
                'fields'  => $this->alpha_get_mapped_fields($settings, 'gr', $fields_gr),
            ],
            'drip' => [
                'source_type'      => $settings['drip_source_type'],
                'api_key'          => $settings['drip_custom_api_key'] ?? '',
                'list_id_custom'   => $settings['drip_list_id_custom'] ?? '',
                'tag'    => $this->alpha_get_integration_id($settings, 'drip'),
                'fields' => $this->alpha_get_mapped_fields($settings, 'drip', $fields_drip),
            ],
            'convertkit' => [
                'source_type'      => $settings['convertkit_source_type'],
                'api_key'          => $settings['convertkit_custom_api_key'] ?? '',
                'list_id_custom'   => $settings['convertkit_list_id_custom'] ?? '',
                'form_id' => $this->alpha_get_integration_id($settings, 'convertkit'),
                'fields'  => $this->alpha_get_mapped_fields($settings, 'ck', $fields_ck),
            ],
            'mailerlite' => [
                'source_type'      => $settings['mailerlite_source_type'],
                'api_key'          => $settings['mailerlite_custom_api_key'] ?? '',
                'list_id_custom'   => $settings['mailerlite_list_id_custom'] ?? '',
                'group_id' => $this->alpha_get_integration_id($settings, 'mailerlite'),
                'fields'   => $this->alpha_get_mapped_fields($settings, 'ml', $fields_ml),
            ],
            'clicksend' => [
                'source_type'      => $settings['clicksend_source_type'],
                'api_key'          => $settings['clicksend_custom_api_key'] ?? '',
                'list_id_custom'   => $settings['clicksend_list_id_custom'] ?? '',
                'list_id' => $this->alpha_get_integration_id($settings, 'clicksend'),
                'fields'  => $this->alpha_get_mapped_fields($settings, 'cs', $fields_cs),
            ],
        ];

        echo '<form class="alpha-form" ' . esc_attr($enable_geolocation) . ' ' . esc_attr($enable_msg_to_exit) . ' ' . esc_attr($enable_retornar_dados) . ' data-alpha-widget-id="' . esc_attr($form_id) . '" data-form-name="' . esc_attr($form_name) . '" ' . esc_attr($show_required_mark) . '>';
        foreach ($items as $index => $item) {
            $form_count = $index + 1;
            $item_setting_key = $this->get_repeater_setting_key('item_title', 'items', $index);
            $item_summary_key = $this->get_repeater_setting_key('item_summary', 'items', $index);
            $item_classes = ['alpha-f-n-item'];
            $item_id = empty($item['element_css_id_alpha']) ? 'alpha-f-n-item-' . $id_int . $index : $item['element_css_id_alpha'];
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

        $actions_data = array_filter($actions_data, function ($v) {
            return !empty(array_filter((array) $v));
        });

        echo '<input type="hidden" data-alpha-submit class="alpha-actions-data alpha-ignore-shortcode" value=\'' . esc_attr(wp_json_encode($actions_data)) . '\' />';
        echo '</form>';
        if ('yes' === $settings['show_overlay'] && !empty($settings['overlay_loader_image']['id'])) {
            echo '<div class="alpha-form-overlay" style="display:none;">';
            echo wp_get_attachment_image(
                $settings['overlay_loader_image']['id'],
                'full',
                false,
                [
                    'class' => 'alpha-form-overlay-gif',
                    'alt'   => esc_attr__('Carregando...', 'alpha-form'),
                ]
            );
            echo '</div>';
        }
        echo '<div class="alpha-form-toast"></div>';
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

            const itemTitleTextAttributes={ 'class' : [ 'alpha-f-n-item-title-text' ], 'data-binding-type' : 'repeater-item' , 'data-binding-repeater-name' : 'item_title_alpha' , 'data-binding-setting' : ['item_title_alpha'], 'data-binding-index' : view.collection.length + 1, 'data-binding-dynamic' : 'true' ,
            };

            view.addRenderAttribute( 'div-container' , itemWrapperAttributes, null, true );
            view.addRenderAttribute( 'summary-container' , itemTitleAttributes, null, true );
            view.addRenderAttribute( 'text-container' , itemTitleTextAttributes, null, true );
            #>

            <div {{{ view.getRenderAttributeString( 'div-container' ) }}}>
                <summary {{{ view.getRenderAttributeString( 'summary-container' ) }}}>
                    <span class="alpha-f-n-item-title-header">
                        <div {{{ view.getRenderAttributeString( 'text-container' ) }}}>{{{ data.item_title_alpha }}}</div>
                    </span>
                </summary>
            </div>
        <?php
    }

    protected function content_template()
    {
        ?>
            <div class="alpha-f-n" data-alpha-widget-id="{{ view.getID() }}" data-form-name="{{{ settings['form_name_alpha'] }}}">
                <# if ( settings['items_alpha'] ) {
                    const elementUid=view.getIDInt().toString().substring( 0, 3 ),
                    titleHTMLTag=elementor.helpers.validateHTMLTag( settings.title_tag_alpha ),
                    defaultState=settings.default_state_alpha,
                    itemTitleIcon=elementor.helpers.renderIcon( view, settings['form_item_title_icon_alpha'], { 'aria-hidden' : true }, 'i' , 'object' ) ?? '' ,
                    itemTitleIconActive=''===settings.form_item_title_icon_active_alpha.value
                    ? itemTitleIcon
                    : elementor.helpers.renderIcon( view, settings['form_item_title_icon_active_alpha'], { 'aria-hidden' : true }, 'i' , 'object' );
                    #>

                    <# _.each( settings['items_alpha'], function( item, index ) {
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
                                        {{{ item.item_title_alpha }}}
                                    </{{{ titleHTMLTag }}}>
                                </span>
                                <# if (settings.form_item_title_icon_alpha.value) { #>
                                    <span class="alpha-f-n-item-title-icon">
                                        <span class="e-opened">{{{ itemTitleIconActive.value }}}</span>
                                        <span class="e-closed">{{{ itemTitleIcon.value }}}</span>
                                    </span>
                                    <# } #>
                            </summary>
                        </div>


                        <# } ); #>
                            <# } #>
                                <# if ( settings.show_overlay==='yes' && settings.show_editor==='yes' && settings.overlay_loader_image && settings.overlay_loader_image.url ) { #>
                                    <div class="alpha-form-overlay">
                                        <img src="{{ settings.overlay_loader_image.url }}" alt="Carregando..." class="alpha-form-overlay-gif" />
                                    </div>
                                    <# } #>

            </div>
    <?php
    }

    private function get_options($integrationName)
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT data FROM {$wpdb->prefix}alpha_form_nested_integrations WHERE name = %s AND status = 1",
                $integrationName
            ),
            ARRAY_A
        );

        if (!$row || empty($row['data'])) return [];

        $settings = json_decode($row['data'], true);
        if (!$settings) return [];

        $url = '';
        $headers = [];
        $responseKey = '';
        $options = [];

        switch ($integrationName) {
            case 'mailchimp':
                if (empty($settings['api_key']) || empty($settings['server_prefix'])) return [];
                $api_key = $settings['api_key'];
                $server = $settings['server_prefix'];
                $url = "https://{$server}.api.mailchimp.com/3.0/lists";
                $headers = ['Authorization' => 'Basic ' . base64_encode("user:$api_key")];
                $responseKey = 'lists';
                break;

            case 'active-campaign':
                if (empty($settings['api_key']) || empty($settings['api_url'])) return [];
                $url = rtrim($settings['api_url'], '/') . '/api/3/lists';
                $headers = ['Api-Token' => $settings['api_key']];
                $responseKey = 'lists';
                break;

            case 'getresponse':
                if (empty($settings['api_key'])) return [];
                $url = "https://api.getresponse.com/v3/campaigns";
                $headers = ['X-Auth-Token' => "api-key {$settings['api_key']}"];
                break;

            case 'drip':
                if (empty($settings['api_key']) || empty($settings['account_id'])) return [];
                $url = "https://api.getdrip.com/v2/{$settings['account_id']}/campaigns";
                $headers = [
                    'Authorization' => 'Basic ' . base64_encode("{$settings['api_key']}:"),
                    'Content-Type' => 'application/json'
                ];
                $responseKey = 'campaigns';
                break;

            case 'convertkit':
                if (empty($settings['api_secret'])) return [];
                $url = "https://api.convertkit.com/v3/forms?api_secret={$settings['api_secret']}";
                break;

            case 'mailerlite':
                if (empty($settings['api_key'])) return [];
                $url = "https://api.mailerlite.com/api/v2/groups";
                $headers = ['X-MailerLite-ApiKey' => $settings['api_key']];
                break;

            case 'clicksend':
                if (empty($settings['username']) || empty($settings['api_key'])) return [];
                $url = "https://rest.clicksend.com/v3/lists";
                $headers = [
                    'Authorization' => 'Basic ' . base64_encode("{$settings['username']}:{$settings['api_key']}")
                ];
                break;

            default:
                return [];
        }

        $response = wp_remote_get($url, [
            'headers' => $headers,
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) return [];

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!$body) return [];

        switch ($integrationName) {
            case 'mailchimp':
            case 'active-campaign':
                foreach ($body[$responseKey] ?? [] as $item) {
                    $options[$item['id']] = $item['name'];
                }
                break;

            case 'getresponse':
                foreach ($body as $item) {
                    $options[$item['campaignId']] = $item['name'];
                }
                break;

            case 'drip':
                foreach ($body['campaigns'] ?? [] as $item) {
                    $options[$item['id']] = $item['name'];
                }
                break;

            case 'convertkit':
                foreach ($body['forms'] ?? [] as $item) {
                    $options[$item['id']] = $item['name'];
                }
                break;

            case 'mailerlite':
                foreach ($body as $item) {
                    $options[$item['id']] = $item['name'];
                }
                break;

            case 'clicksend':
                foreach ($body['data']['data'] ?? [] as $item) {
                    $options[$item['list_id']] = $item['list_name'];
                }
                break;
        }

        return $options;
    }

    private function alpha_get_mapped_fields($settings, $prefix, $field_keys)
    {
        $mapped = [];

        foreach ($field_keys as $key) {
            $control_key = 'map_field_' . $key . '_' . $prefix;

            if (!empty($settings[$control_key])) {
                $mapped[$key] = $settings[$control_key];
            }
        }

        return $mapped;
    }

    private function alpha_get_integration_id($settings, $prefix)
    {
        $possibles = [
            "{$prefix}_list_id_custom",
            "{$prefix}_list_id",
            "{$prefix}_form_id",
            "{$prefix}_group_id",
            "{$prefix}_tag",
        ];

        foreach ($possibles as $key) {
            if (!empty($settings[$key])) {
                return $settings[$key];
            }
        }

        return '';
    }
}
