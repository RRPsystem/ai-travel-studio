<?php
if (!defined('ABSPATH')) exit;

class TCC_Map_Tag extends \Elementor\Core\DynamicTags\Data_Tag {
    
    public function get_name() {
        return 'tcc-map';
    }
    
    public function get_title() {
        return 'TravelC Landkaart';
    }
    
    public function get_group() {
        return 'travelc';
    }
    
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY];
    }
    
    protected function register_controls() {
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
        $destination = tcc_get_destination_data($slug);
        
        if (!$destination || empty($destination['map_image'])) {
            return [];
        }
        
        return [
            'url' => $destination['map_image'],
            'id' => '',
        ];
    }
}
