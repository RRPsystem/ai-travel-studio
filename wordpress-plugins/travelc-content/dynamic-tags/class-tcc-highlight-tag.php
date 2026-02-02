<?php
if (!defined('ABSPATH')) exit;

class TCC_Highlight_Tag extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'tcc-highlight';
    }
    
    public function get_title() {
        return 'TravelC Hoogtepunt';
    }
    
    public function get_group() {
        return 'travelc';
    }
    
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    
    protected function register_controls() {
        $this->add_control(
            'highlight_number',
            [
                'label' => 'Hoogtepunt Nummer',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 10,
            ]
        );
        
        $this->add_control(
            'field',
            [
                'label' => 'Veld',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'title',
                'options' => [
                    'title' => 'Titel',
                    'description' => 'Beschrijving',
                ],
            ]
        );
        
        $this->add_control(
            'slug',
            [
                'label' => 'Bestemming Slug',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'Leeg = automatisch detecteren',
            ]
        );
    }
    
    public function render() {
        $slug = $this->get_settings('slug');
        $number = (int) $this->get_settings('highlight_number');
        $field = $this->get_settings('field');
        
        $destination = tcc_get_destination_data($slug);
        
        if (!$destination || empty($destination['highlights'])) {
            return;
        }
        
        $highlights = $destination['highlights'];
        $index = $number - 1;
        
        if (!isset($highlights[$index])) {
            return;
        }
        
        $highlight = $highlights[$index];
        
        if ($field === 'title') {
            echo esc_html($highlight['title'] ?? '');
        } else {
            echo esc_html($highlight['description'] ?? '');
        }
    }
}
