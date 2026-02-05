<?php
if (!defined('ABSPATH')) exit;

class TCC_Region_Tag extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'tcc-region';
    }
    
    public function get_title() {
        return 'TravelC Regio';
    }
    
    public function get_group() {
        return 'travelc';
    }
    
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    
    protected function register_controls() {
        $this->add_control(
            'region_number',
            [
                'label' => 'Regio Nummer',
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
                'default' => 'name',
                'options' => [
                    'name' => 'Naam',
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
        $number = (int) $this->get_settings('region_number');
        $field = $this->get_settings('field');
        
        $destination = tcc_get_destination_data($slug);
        
        if (!$destination) {
            // Debug: no destination found
            if (current_user_can('manage_options')) {
                echo '<!-- TCC Region: No destination found -->';
            }
            return;
        }
        
        // Regions can be array or JSON string
        $regions = $destination['regions'] ?? [];
        if (is_string($regions)) {
            $regions = json_decode($regions, true) ?: [];
        }
        
        if (empty($regions) || !is_array($regions)) {
            // Debug: no regions
            if (current_user_can('manage_options')) {
                echo '<!-- TCC Region: No regions data. Raw: ' . esc_html(print_r($destination['regions'] ?? 'null', true)) . ' -->';
            }
            return;
        }
        
        $index = $number - 1;
        
        if (!isset($regions[$index])) {
            // Debug: index not found
            if (current_user_can('manage_options')) {
                echo '<!-- TCC Region: Index ' . $index . ' not found. Total regions: ' . count($regions) . ' -->';
            }
            return;
        }
        
        $region = $regions[$index];
        
        // Handle both array and object formats
        if (is_array($region)) {
            if ($field === 'name') {
                echo esc_html($region['name'] ?? '');
            } else {
                echo esc_html($region['description'] ?? '');
            }
        } elseif (is_string($region)) {
            // If region is just a string (name only)
            if ($field === 'name') {
                echo esc_html($region);
            }
        }
    }
}
