<?php
if (!defined('ABSPATH')) exit;

/**
 * Individual Photo Tag - allows selecting specific photo by number
 * Use in Image widget to get Foto 1, Foto 2, etc.
 */
class TCC_Photo_Tag extends \Elementor\Core\DynamicTags\Data_Tag {
    
    public function get_name() {
        return 'tcc-photo';
    }
    
    public function get_title() {
        return 'TravelC Foto (nummer)';
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
        
        $this->add_control(
            'photo_number',
            [
                'label' => 'Foto Nummer',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => 'Foto 1',
                    '2' => 'Foto 2',
                    '3' => 'Foto 3',
                    '4' => 'Foto 4',
                    '5' => 'Foto 5',
                    '6' => 'Foto 6',
                    '7' => 'Foto 7',
                    '8' => 'Foto 8',
                    '9' => 'Foto 9',
                    '10' => 'Foto 10',
                ],
            ]
        );
    }
    
    public function get_value(array $options = []) {
        $slug = $this->get_settings('slug');
        $photo_number = (int) $this->get_settings('photo_number');
        $destination = tcc_get_destination_data($slug);
        
        if (!$destination) {
            return [];
        }
        
        $images = $destination['images'] ?? [];
        $index = $photo_number - 1; // Convert to 0-based index
        
        if (isset($images[$index]) && !empty($images[$index])) {
            return [
                'url' => $images[$index],
                'id' => '',
            ];
        }
        
        return [];
    }
}
