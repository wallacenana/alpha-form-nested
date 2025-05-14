<?php

namespace AlphaForm\Module\Widget\Controls;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Alpha_Elements extends Widget_Base
{

    public function get_name()
    {
        return 'alpha-elements';
    }

    public function get_title()
    {
        return esc_html__('Alpha Elements', 'alpha-form');
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['alpha-form'];
    }

    public function get_keywords()
    {
        return ['alpha', 'form', 'elementos', 'fields'];
    }
    protected function register_controls()
    {
        $this->start_controls_section('section_elements', [
            'label' => esc_html__('Elementos do Formulário', 'alpha-form'),
        ]);

        $this->add_control('form_elements', [
            'label' => esc_html__('Escolha os campos', 'alpha-form'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => [
                'name' => 'Nome',
                'email' => 'Email',
                'phone' => 'Telefone',
                'message' => 'Mensagem',
            ],
            'default' => ['name', 'email'],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $elements = $settings['form_elements'];

        if (!$elements || !is_array($elements)) return;

        echo '<div class="alpha-form-elements">';
        foreach ($elements as $field) {
            switch ($field) {
                case 'name':
                    echo '<div class="alpha-form-field"><input type="text" name="name" placeholder="Seu nome"></div>';
                    break;
                case 'email':
                    echo '<div class="alpha-form-field"><input type="email" name="email" placeholder="Seu email" required></div>';
                    break;
                case 'phone':
                    echo '<div class="alpha-form-field"><input type="tel" name="phone" placeholder="Seu telefone"></div>';
                    break;
                case 'message':
                    echo '<div class="alpha-form-field"><textarea name="message" placeholder="Sua mensagem"></textarea></div>';
                    break;
            }
        }
        echo '</div>';
    }
}
