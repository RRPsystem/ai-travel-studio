<?php
if (!defined('ABSPATH')) exit;

class TCC_Fun_Facts_Tag extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'tcc-fun-facts';
    }
    
    public function get_title() {
        return 'TravelC Fun Facts';
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
                'default' => 'list',
                'options' => [
                    'list' => 'Bullet lijst',
                    'numbered' => 'Genummerde lijst',
                    'plain' => 'Platte tekst (komma gescheiden)',
                ],
            ]
        );
        
        $this->add_control(
            'fact_number',
            [
                'label' => 'Specifiek feit (optioneel)',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => 'Alle feiten',
                    '1' => 'Feit 1',
                    '2' => 'Feit 2',
                    '3' => 'Feit 3',
                ],
            ]
        );
    }
    
    public function render() {
        $format = $this->get_settings('format');
        $fact_number = $this->get_settings('fact_number');
        
        $destination = tcc_get_destination_data();
        
        if (!$destination) {
            return;
        }
        
        // Fun facts are stored as array of strings (not label/value objects)
        $fun_facts = $destination['fun_facts'] ?? [];
        if (is_string($fun_facts)) {
            $fun_facts = json_decode($fun_facts, true) ?: [];
        }
        
        if (empty($fun_facts) || !is_array($fun_facts)) {
            return;
        }
        
        // If specific fact requested
        if ($fact_number !== 'all') {
            $index = intval($fact_number) - 1;
            if (isset($fun_facts[$index])) {
                echo esc_html($fun_facts[$index]);
            }
            return;
        }
        
        // All fun facts
        switch ($format) {
            case 'list':
                echo '<ul class="tcc-fun-facts-list">';
                foreach ($fun_facts as $fact) {
                    if (is_string($fact) && !empty($fact)) {
                        echo '<li>' . esc_html($fact) . '</li>';
                    }
                }
                echo '</ul>';
                break;
                
            case 'numbered':
                echo '<ol class="tcc-fun-facts-list">';
                foreach ($fun_facts as $fact) {
                    if (is_string($fact) && !empty($fact)) {
                        echo '<li>' . esc_html($fact) . '</li>';
                    }
                }
                echo '</ol>';
                break;
                
            case 'plain':
            default:
                $items = array_filter($fun_facts, function($f) { return is_string($f) && !empty($f); });
                echo esc_html(implode(', ', $items));
                break;
        }
    }
}
