<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Experts_Posttype')) {
    
    class RBS_TRAVEL_Experts_Posttype {
        
        public function __construct() {
            $this->init();
        }
        
        private function init() {
            add_action('init', array($this, 'register_expert_post_type'));
            add_action('add_meta_boxes', array($this, 'add_expert_meta_boxes'));
            add_action('save_post_rbs-expert', array($this, 'save_expert_meta'));
            add_filter('manage_rbs-expert_posts_columns', array($this, 'expert_columns'));
            add_action('manage_rbs-expert_posts_custom_column', array($this, 'expert_custom_column'), 10, 2);
        }
        
        /**
         * Register Expert Custom Post Type
         */
        public function register_expert_post_type() {
            $labels = array(
                'name'                  => __('Reisexperts', 'rbs-travel'),
                'singular_name'         => __('Reisexpert', 'rbs-travel'),
                'menu_name'             => __('Reisexperts', 'rbs-travel'),
                'name_admin_bar'        => __('Reisexpert', 'rbs-travel'),
                'add_new'               => __('Nieuwe Expert', 'rbs-travel'),
                'add_new_item'          => __('Nieuwe Reisexpert Toevoegen', 'rbs-travel'),
                'new_item'              => __('Nieuwe Reisexpert', 'rbs-travel'),
                'edit_item'             => __('Reisexpert Bewerken', 'rbs-travel'),
                'view_item'             => __('Bekijk Reisexpert', 'rbs-travel'),
                'all_items'             => __('👤 Reisexperts', 'rbs-travel'),
                'search_items'          => __('Zoek Reisexperts', 'rbs-travel'),
                'not_found'             => __('Geen reisexperts gevonden', 'rbs-travel'),
                'not_found_in_trash'    => __('Geen reisexperts in prullenbak', 'rbs-travel')
            );
            
            $args = array(
                'labels'                => $labels,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'show_in_menu'          => 'rbstravel',  // Show under RRP System menu
                'query_var'             => true,
                'rewrite'               => array('slug' => 'expert'),
                'capability_type'       => 'post',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_position'         => null,
                'menu_icon'             => 'dashicons-businessman',
                'supports'              => array('title', 'editor', 'thumbnail'),
                'show_in_rest'          => true,
                'rest_base'             => 'rbs-experts',
                'rest_controller_class' => 'WP_REST_Posts_Controller'
            );
            
            register_post_type('rbs-expert', $args);
        }
        
        /**
         * Add Meta Boxes for Expert Data
         */
        public function add_expert_meta_boxes() {
            add_meta_box(
                'expert_details',
                '👤 Expert Gegevens',
                array($this, 'render_expert_details_box'),
                'rbs-expert',
                'normal',
                'high'
            );
            
            add_meta_box(
                'expert_countries',
                '🌍 Landen Specialisatie',
                array($this, 'render_expert_countries_box'),
                'rbs-expert',
                'side',
                'default'
            );
        }
        
        /**
         * Render Expert Details Meta Box
         */
        public function render_expert_details_box($post) {
            wp_nonce_field('expert_details_nonce', 'expert_details_nonce');
            
            $expert_email = get_post_meta($post->ID, '_expert_email', true);
            $expert_phone = get_post_meta($post->ID, '_expert_phone', true);
            $expert_specialisation = get_post_meta($post->ID, '_expert_specialisation', true);
            $expert_years = get_post_meta($post->ID, '_expert_years', true);
            $expert_url = get_post_meta($post->ID, '_expert_url', true);
            
            ?>
            <style>
                .expert-meta-field { margin-bottom: 20px; }
                .expert-meta-field label { display: block; font-weight: 600; margin-bottom: 5px; color: #1f2937; }
                .expert-meta-field input[type="text"],
                .expert-meta-field input[type="email"],
                .expert-meta-field input[type="url"],
                .expert-meta-field input[type="number"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
                .expert-meta-field small { color: #6b7280; display: block; margin-top: 5px; }
            </style>
            
            <div class="expert-meta-field">
                <label for="expert_specialisation">✨ Specialisatie</label>
                <input type="text" id="expert_specialisation" name="expert_specialisation" value="<?php echo esc_attr($expert_specialisation); ?>" placeholder="bijv. Japan Expert, Zuid-Amerika Specialist">
                <small>Wat is de expertise van deze reisexpert?</small>
            </div>
            
            <div class="expert-meta-field">
                <label for="expert_email">✉️ Email</label>
                <input type="email" id="expert_email" name="expert_email" value="<?php echo esc_attr($expert_email); ?>" placeholder="expert@reizen.nl">
                <small>Contact email voor deze expert (optioneel)</small>
            </div>
            
            <div class="expert-meta-field">
                <label for="expert_phone">📞 Telefoon</label>
                <input type="text" id="expert_phone" name="expert_phone" value="<?php echo esc_attr($expert_phone); ?>" placeholder="+31 6 12345678">
                <small>Telefoonnummer (optioneel)</small>
            </div>
            
            <div class="expert-meta-field">
                <label for="expert_years">📅 Jaren Ervaring</label>
                <input type="number" id="expert_years" name="expert_years" value="<?php echo esc_attr($expert_years); ?>" placeholder="10" min="0">
                <small>Aantal jaren ervaring in de reisbranche</small>
            </div>
            
            <div class="expert-meta-field">
                <label for="expert_url">🔗 Expert Pagina URL (Extern)</label>
                <input type="url" id="expert_url" name="expert_url" value="<?php echo esc_attr($expert_url); ?>" placeholder="https://jouwsite.nl/team/expert-naam/">
                <small><strong>Optioneel:</strong> Link naar externe expert pagina. Als ingevuld, wordt "Ontmoet [Expert]" knop naar deze URL geleid.</small>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background: #f0f9ff; border-left: 4px solid #3b82f6; border-radius: 4px;">
                <strong style="color: #1e40af;">📸 Expert Foto</strong>
                <p style="color: #1e3a8a; margin: 10px 0 0 0; font-size: 13px;">
                    Zoek rechts in de sidebar naar <strong>"Uitgelichte afbeelding"</strong> en klik daar om een foto te uploaden. 
                    Deze foto wordt gebruikt in de expert widget op reispagina's.
                </p>
            </div>
            <?php
        }
        
        /**
         * Render Expert Countries Meta Box
         */
        public function render_expert_countries_box($post) {
            $expert_countries = get_post_meta($post->ID, '_expert_countries', true);
            if (!is_array($expert_countries)) {
                $expert_countries = array();
            }
            
            // Get all unique countries from travel destinations
            global $wpdb;
            $countries = $wpdb->get_col("
                SELECT DISTINCT meta_value 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = 'travel_destinations'
            ");
            
            $all_countries = array();
            foreach ($countries as $dest_json) {
                $destinations = json_decode($dest_json, true);
                if (is_array($destinations)) {
                    foreach ($destinations as $dest) {
                        if (isset($dest['country'])) {
                            $all_countries[] = $dest['country'];
                        }
                    }
                }
            }
            
            // FALLBACK: Als geen landen gevonden, gebruik populaire reislanden
            if (empty($all_countries)) {
                $all_countries = array(
                    'Nederland', 'België', 'Duitsland', 'Frankrijk', 'Spanje', 'Italië', 
                    'Portugal', 'Griekenland', 'Turkije', 'Oostenrijk', 'Zwitserland',
                    'Verenigde Staten', 'Canada', 'Mexico', 'Costa Rica',
                    'Brazilië', 'Argentinië', 'Peru', 'Chili',
                    'Japan', 'China', 'Thailand', 'Vietnam', 'Indonesië', 'Maleisië', 'Singapore',
                    'India', 'Sri Lanka', 'Nepal', 'Zuid-Korea',
                    'Australië', 'Nieuw-Zeeland', 'Zuid-Afrika', 'Egypte', 'Marokko', 'Kenia', 'Tanzania',
                    'Verenigd Koninkrijk', 'Ierland', 'IJsland', 'Noorwegen', 'Zweden', 'Denemarken', 'Finland',
                    'Kroatië', 'Slovenië', 'Tsjechië', 'Polen', 'Hongarije', 'Roemenië'
                );
            }
            
            $all_countries = array_unique($all_countries);
            sort($all_countries);
            
            ?>
            <style>
                .countries-checklist { max-height: 300px; overflow-y: auto; padding: 10px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; }
                .countries-checklist label { display: block; padding: 5px 0; }
                .countries-checklist input[type="checkbox"] { margin-right: 8px; }
            </style>
            
            <div style="margin-bottom: 15px; padding: 12px; background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 4px;">
                <strong style="color: #065f46;">🌍 Hoe werkt het?</strong>
                <p style="color: #047857; margin: 8px 0 0 0; font-size: 13px; line-height: 1.6;">
                    Vink de landen aan waar deze expert specialist in is. Wanneer bezoekers een reis naar bijv. <strong>Japan</strong> bekijken, 
                    wordt automatisch deze expert getoond als Japan aangevinkt is.
                </p>
            </div>
            
            <div class="countries-checklist">
                <?php foreach ($all_countries as $country): ?>
                    <?php if (!empty($country)): ?>
                        <label>
                            <input type="checkbox" name="expert_countries[]" value="<?php echo esc_attr($country); ?>" <?php checked(in_array($country, $expert_countries)); ?>>
                            <?php echo esc_html($country); ?>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <p style="margin-top: 10px; padding: 10px; background: #f0f9ff; border-left: 4px solid #3b82f6; border-radius: 4px; font-size: 13px;">
                <strong>💡 Tip:</strong> Als geen expert gekoppeld is aan een land, wordt de fallback expert getoond (in te stellen bij Instellingen).
            </p>
            <?php
        }
        
        /**
         * Save Expert Meta Data
         */
        public function save_expert_meta($post_id) {
            // Check nonce
            if (!isset($_POST['expert_details_nonce']) || !wp_verify_nonce($_POST['expert_details_nonce'], 'expert_details_nonce')) {
                return;
            }
            
            // Check autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            
            // Check permissions
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
            
            // Save expert details
            if (isset($_POST['expert_email'])) {
                update_post_meta($post_id, '_expert_email', sanitize_email($_POST['expert_email']));
            }
            
            if (isset($_POST['expert_phone'])) {
                update_post_meta($post_id, '_expert_phone', sanitize_text_field($_POST['expert_phone']));
            }
            
            if (isset($_POST['expert_specialisation'])) {
                update_post_meta($post_id, '_expert_specialisation', sanitize_text_field($_POST['expert_specialisation']));
            }
            
            if (isset($_POST['expert_years'])) {
                update_post_meta($post_id, '_expert_years', intval($_POST['expert_years']));
            }
            
            if (isset($_POST['expert_url'])) {
                update_post_meta($post_id, '_expert_url', esc_url_raw($_POST['expert_url']));
            }
            
            // Save countries
            if (isset($_POST['expert_countries']) && is_array($_POST['expert_countries'])) {
                $countries = array_map('sanitize_text_field', $_POST['expert_countries']);
                update_post_meta($post_id, '_expert_countries', $countries);
            } else {
                delete_post_meta($post_id, '_expert_countries');
            }
        }
        
        /**
         * Custom Columns for Expert List
         */
        public function expert_columns($columns) {
            $new_columns = array();
            $new_columns['cb'] = $columns['cb'];
            $new_columns['title'] = 'Expert Naam';
            $new_columns['specialisation'] = 'Specialisatie';
            $new_columns['countries'] = 'Landen';
            $new_columns['contact'] = 'Contact';
            $new_columns['date'] = $columns['date'];
            return $new_columns;
        }
        
        /**
         * Custom Column Content
         */
        public function expert_custom_column($column, $post_id) {
            switch ($column) {
                case 'specialisation':
                    $spec = get_post_meta($post_id, '_expert_specialisation', true);
                    echo $spec ? esc_html($spec) : '<span style="color: #9ca3af;">—</span>';
                    break;
                    
                case 'countries':
                    $countries = get_post_meta($post_id, '_expert_countries', true);
                    if (is_array($countries) && !empty($countries)) {
                        echo '<span style="color: #6366f1; font-weight: 600;">' . count($countries) . ' landen</span>';
                        echo '<br><small style="color: #6b7280;">' . implode(', ', array_slice($countries, 0, 3));
                        if (count($countries) > 3) {
                            echo '... +' . (count($countries) - 3);
                        }
                        echo '</small>';
                    } else {
                        echo '<span style="color: #ef4444;">Geen landen</span>';
                    }
                    break;
                    
                case 'contact':
                    $email = get_post_meta($post_id, '_expert_email', true);
                    $phone = get_post_meta($post_id, '_expert_phone', true);
                    if ($email) {
                        echo '✉️ ' . esc_html($email) . '<br>';
                    }
                    if ($phone) {
                        echo '📞 ' . esc_html($phone);
                    }
                    if (!$email && !$phone) {
                        echo '<span style="color: #9ca3af;">—</span>';
                    }
                    break;
            }
        }
    }
}

// Initialize
if (is_admin()) {
    new RBS_TRAVEL_Experts_Posttype();
}
