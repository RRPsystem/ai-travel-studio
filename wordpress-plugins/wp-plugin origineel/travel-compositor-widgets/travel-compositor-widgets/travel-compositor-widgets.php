<?php
/**
 * Plugin Name: Travel Compositor Widgets
 * Plugin URI: https://www.ai-websitestudio.nl
 * Description: Embeddable travel searchbox widgets voor hero sections - Vlucht + Hotel, Accommodatie, Fly & Drive, en meer.
 * Version: 1.0.0
 * Author: AI Website Studio
 * Author URI: https://www.ai-websitestudio.nl
 * Text Domain: tc-widgets
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TC_WIDGETS_VERSION', '1.0.0');
define('TC_WIDGETS_PATH', plugin_dir_path(__FILE__));
define('TC_WIDGETS_URL', plugin_dir_url(__FILE__));

class TC_Widgets {
    
    private static $instance = null;
    
    private $default_config = array(
        'microsite' => 'rondreis-planner',
        'base_url' => 'https://rondreis-planner.nl',
        'primary_color' => '#066168',
        'language' => 'NL',
        'currency' => 'EUR'
    );
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Shortcodes
        add_shortcode('tc_searchbox', array($this, 'render_searchbox'));
        add_shortcode('tc_flight_hotel', array($this, 'render_flight_hotel'));
        add_shortcode('tc_hotel', array($this, 'render_hotel'));
        add_shortcode('tc_flight', array($this, 'render_flight'));
        add_shortcode('tc_car', array($this, 'render_car'));
        add_shortcode('tc_routing', array($this, 'render_routing'));
        add_shortcode('tc_ai_planner', array($this, 'render_ai_planner'));
    }
    
    public function enqueue_scripts() {
        // jQuery UI
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_style('tc-jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css', array(), '1.12.1');
        
        // Dutch datepicker locale
        wp_enqueue_script('jquery-ui-datepicker-nl', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.8.18/i18n/jquery.ui.datepicker-nl.min.js', array('jquery-ui-datepicker'), '1.8.18');
        
        // Font Awesome
        wp_enqueue_style('tc-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
        
        // Plugin CSS
        wp_enqueue_style('tc-widgets', TC_WIDGETS_URL . 'assets/css/tc-widgets.css', array(), TC_WIDGETS_VERSION);
        
        // Plugin JS
        wp_enqueue_script('tc-widgets', TC_WIDGETS_URL . 'assets/js/tc-widgets.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-autocomplete'), TC_WIDGETS_VERSION, true);
        
        // Pass config to JS
        wp_localize_script('tc-widgets', 'tcWidgetsConfig', $this->get_config());
    }
    
    private function get_config() {
        return array(
            'microsite' => get_option('tc_microsite', $this->default_config['microsite']),
            'baseUrl' => get_option('tc_base_url', $this->default_config['base_url']),
            'primaryColor' => get_option('tc_primary_color', $this->default_config['primary_color']),
            'language' => get_option('tc_language', $this->default_config['language']),
            'currency' => get_option('tc_currency', $this->default_config['currency'])
        );
    }
    
    // Admin menu
    public function add_admin_menu() {
        add_options_page(
            'Travel Compositor Widgets',
            'TC Widgets',
            'manage_options',
            'tc-widgets',
            array($this, 'render_admin_page')
        );
    }
    
    public function register_settings() {
        register_setting('tc_widgets_settings', 'tc_microsite');
        register_setting('tc_widgets_settings', 'tc_base_url');
        register_setting('tc_widgets_settings', 'tc_primary_color');
        register_setting('tc_widgets_settings', 'tc_language');
        register_setting('tc_widgets_settings', 'tc_currency');
    }
    
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Travel Compositor Widgets</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('tc_widgets_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Microsite ID</th>
                        <td>
                            <input type="text" name="tc_microsite" value="<?php echo esc_attr(get_option('tc_microsite', $this->default_config['microsite'])); ?>" class="regular-text">
                            <p class="description">Je Travel Compositor microsite ID (bijv. "rondreis-planner")</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Base URL</th>
                        <td>
                            <input type="url" name="tc_base_url" value="<?php echo esc_attr(get_option('tc_base_url', $this->default_config['base_url'])); ?>" class="regular-text">
                            <p class="description">De base URL van je microsite (bijv. "https://rondreis-planner.nl")</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Primaire kleur</th>
                        <td>
                            <input type="color" name="tc_primary_color" value="<?php echo esc_attr(get_option('tc_primary_color', $this->default_config['primary_color'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Taal</th>
                        <td>
                            <select name="tc_language">
                                <option value="NL" <?php selected(get_option('tc_language', 'NL'), 'NL'); ?>>Nederlands</option>
                                <option value="EN" <?php selected(get_option('tc_language', 'NL'), 'EN'); ?>>English</option>
                                <option value="DE" <?php selected(get_option('tc_language', 'NL'), 'DE'); ?>>Deutsch</option>
                                <option value="FR" <?php selected(get_option('tc_language', 'NL'), 'FR'); ?>>Français</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Valuta</th>
                        <td>
                            <select name="tc_currency">
                                <option value="EUR" <?php selected(get_option('tc_currency', 'EUR'), 'EUR'); ?>>Euro (€)</option>
                                <option value="USD" <?php selected(get_option('tc_currency', 'EUR'), 'USD'); ?>>US Dollar ($)</option>
                                <option value="GBP" <?php selected(get_option('tc_currency', 'EUR'), 'GBP'); ?>>British Pound (£)</option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2>Beschikbare Shortcodes</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Shortcode</th>
                        <th>Beschrijving</th>
                        <th>Voorbeeld</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[tc_searchbox]</code></td>
                        <td>Complete searchbox met alle tabs</td>
                        <td><code>[tc_searchbox tabs="tripplanner,ai,flighthotel,hotel"]</code></td>
                    </tr>
                    <tr>
                        <td><code>[tc_flight_hotel]</code></td>
                        <td>Alleen Vlucht + Hotel</td>
                        <td><code>[tc_flight_hotel title="Boek je vakantie"]</code></td>
                    </tr>
                    <tr>
                        <td><code>[tc_hotel]</code></td>
                        <td>Alleen Accommodatie</td>
                        <td><code>[tc_hotel title="Zoek accommodatie"]</code></td>
                    </tr>
                    <tr>
                        <td><code>[tc_flight]</code></td>
                        <td>Alleen Vluchten</td>
                        <td><code>[tc_flight]</code></td>
                    </tr>
                    <tr>
                        <td><code>[tc_car]</code></td>
                        <td>Alleen Huurauto</td>
                        <td><code>[tc_car]</code></td>
                    </tr>
                    <tr>
                        <td><code>[tc_routing]</code></td>
                        <td>Fly & Drive / Autorondreis</td>
                        <td><code>[tc_routing]</code></td>
                    </tr>
                    <tr>
                        <td><code>[tc_ai_planner]</code></td>
                        <td>AI Trip Planner</td>
                        <td><code>[tc_ai_planner placeholder="Beschrijf je droomreis..."]</code></td>
                    </tr>
                </tbody>
            </table>
            
            <h3>Algemene Parameters</h3>
            <ul>
                <li><code>primary_color</code> - Override de primaire kleur (bijv. "#ff6600")</li>
                <li><code>title</code> - Custom titel boven de widget</li>
                <li><code>class</code> - Extra CSS classes</li>
            </ul>
        </div>
        <?php
    }
    
    // =========================================================================
    // SHORTCODE RENDERERS
    // =========================================================================
    
    /**
     * Complete searchbox met tabs
     */
    public function render_searchbox($atts) {
        $atts = shortcode_atts(array(
            'tabs' => 'tripplanner,ai,flighthotel,hotel,routing,car',
            'primary_color' => '',
            'title' => '',
            'class' => ''
        ), $atts);
        
        $tabs = array_map('trim', explode(',', $atts['tabs']));
        $style = $atts['primary_color'] ? 'style="--tc-primary: ' . esc_attr($atts['primary_color']) . '"' : '';
        
        ob_start();
        include TC_WIDGETS_PATH . 'templates/searchbox.php';
        return ob_get_clean();
    }
    
    /**
     * Vlucht + Hotel widget
     */
    public function render_flight_hotel($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Vlucht + Hotel zoeken',
            'primary_color' => '',
            'class' => ''
        ), $atts);
        
        $style = $atts['primary_color'] ? 'style="--tc-primary: ' . esc_attr($atts['primary_color']) . '"' : '';
        
        ob_start();
        include TC_WIDGETS_PATH . 'templates/flight-hotel.php';
        return ob_get_clean();
    }
    
    /**
     * Hotel only widget
     */
    public function render_hotel($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Accommodatie zoeken',
            'primary_color' => '',
            'class' => ''
        ), $atts);
        
        $style = $atts['primary_color'] ? 'style="--tc-primary: ' . esc_attr($atts['primary_color']) . '"' : '';
        
        ob_start();
        include TC_WIDGETS_PATH . 'templates/hotel.php';
        return ob_get_clean();
    }
    
    /**
     * Flight only widget
     */
    public function render_flight($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Vluchten zoeken',
            'primary_color' => '',
            'class' => ''
        ), $atts);
        
        $style = $atts['primary_color'] ? 'style="--tc-primary: ' . esc_attr($atts['primary_color']) . '"' : '';
        
        ob_start();
        include TC_WIDGETS_PATH . 'templates/flight.php';
        return ob_get_clean();
    }
    
    /**
     * Car rental widget
     */
    public function render_car($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Huurauto zoeken',
            'primary_color' => '',
            'class' => ''
        ), $atts);
        
        $style = $atts['primary_color'] ? 'style="--tc-primary: ' . esc_attr($atts['primary_color']) . '"' : '';
        
        ob_start();
        include TC_WIDGETS_PATH . 'templates/car.php';
        return ob_get_clean();
    }
    
    /**
     * Fly & Drive / Routing widget
     */
    public function render_routing($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Fly & Drive',
            'primary_color' => '',
            'class' => ''
        ), $atts);
        
        $style = $atts['primary_color'] ? 'style="--tc-primary: ' . esc_attr($atts['primary_color']) . '"' : '';
        
        ob_start();
        include TC_WIDGETS_PATH . 'templates/routing.php';
        return ob_get_clean();
    }
    
    /**
     * AI Planner widget
     */
    public function render_ai_planner($atts) {
        $atts = shortcode_atts(array(
            'title' => 'AI Trip Planner',
            'placeholder' => 'Beschrijf je droomreis... (bijv. "Een romantische week in Italië met wijnproeverijen")',
            'primary_color' => '',
            'class' => ''
        ), $atts);
        
        $style = $atts['primary_color'] ? 'style="--tc-primary: ' . esc_attr($atts['primary_color']) . '"' : '';
        
        ob_start();
        include TC_WIDGETS_PATH . 'templates/ai-planner.php';
        return ob_get_clean();
    }
}

// Initialize
TC_Widgets::get_instance();
