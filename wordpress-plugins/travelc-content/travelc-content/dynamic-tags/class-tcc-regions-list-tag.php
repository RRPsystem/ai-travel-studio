<?php
if (!defined('ABSPATH')) exit;

class TCC_Regions_List_Tag extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'tcc-regions-list';
    }
    
    public function get_title() {
        return 'TravelC Regio\'s Lijst';
    }
    
    public function get_group() {
        return 'travelc';
    }
    
    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }
    
    protected function register_controls() {
        $this->add_control(
            'format',
            [
                'label' => 'Weergave',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'cards',
                'options' => [
                    'cards' => 'Kaarten (naam + beschrijving)',
                    'list' => 'Lijst (alleen namen)',
                    'detailed' => 'Gedetailleerd (koppen + tekst)',
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
        $format = $this->get_settings('format');
        
        $destination = tcc_get_destination_data($slug);
        
        if (!$destination) {
            return;
        }
        
        // Regions can be array or JSON string
        $regions = $destination['regions'] ?? [];
        if (is_string($regions)) {
            $regions = json_decode($regions, true) ?: [];
        }
        
        if (empty($regions) || !is_array($regions)) {
            return;
        }
        
        switch ($format) {
            case 'cards':
                echo '<div class="tcc-regions-cards" style="display:flex;flex-direction:column;gap:0.5rem;">';
                foreach ($regions as $region) {
                    if (!is_array($region)) continue;
                    $name = esc_html($region['name'] ?? '');
                    $desc = esc_html($region['description'] ?? '');
                    echo '<div class="tcc-region-card" style="margin-bottom:0.25rem;">';
                    echo '<h4 class="tcc-region-name" style="margin:0 0 0.25rem 0;font-size:1.1em;font-weight:600;">' . $name . '</h4>';
                    if ($desc) {
                        echo '<p class="tcc-region-description" style="margin:0;color:#666;">' . $desc . '</p>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                break;
                
            case 'list':
                echo '<ul class="tcc-regions-list">';
                foreach ($regions as $region) {
                    if (!is_array($region)) continue;
                    $name = esc_html($region['name'] ?? '');
                    echo '<li>' . $name . '</li>';
                }
                echo '</ul>';
                break;
                
            case 'detailed':
                echo '<div class="tcc-regions-detailed">';
                foreach ($regions as $region) {
                    if (!is_array($region)) continue;
                    $name = esc_html($region['name'] ?? '');
                    $desc = esc_html($region['description'] ?? '');
                    echo '<div class="tcc-region-item">';
                    echo '<h3 class="tcc-region-title">' . $name . '</h3>';
                    if ($desc) {
                        echo '<p class="tcc-region-text">' . $desc . '</p>';
                    }
                    echo '</div>';
                }
                echo '</div>';
                break;
        }
    }
}
