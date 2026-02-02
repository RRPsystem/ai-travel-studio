<?php
if (!defined('ABSPATH')) exit;

class TCC_Highlight_Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {
    
    public function get_name() {
        return 'tcc-highlight-image';
    }
    
    public function get_title() {
        return 'TravelC Hoogtepunt Afbeelding';
    }
    
    public function get_group() {
        return 'travelc';
    }
    
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY];
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
            'slug',
            [
                'label' => 'Bestemming Slug',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'Leeg = automatisch detecteren',
            ]
        );
    }
    
    public function get_value(array $options = []) {
        $slug = $this->get_settings('slug');
        $number = (int) $this->get_settings('highlight_number');
        
        $destination = tcc_get_destination_data($slug);
        
        if (!$destination || empty($destination['highlights'])) {
            return [];
        }
        
        $highlights = $destination['highlights'];
        $index = $number - 1;
        
        if (!isset($highlights[$index]) || empty($highlights[$index]['image'])) {
            return [];
        }
        
        return [
            'url' => $highlights[$index]['image'],
            'id' => '',
        ];
    }
}
