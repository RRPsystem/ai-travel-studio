<?php
/**
 * Theme Colors Helper
 * Auto-detect and integrate WordPress theme colors
 */
defined('RBS_TRAVEL') or die();

if (!class_exists('RBS_Travel_Theme_Colors')) {
    
    class RBS_Travel_Theme_Colors {
        
        /**
         * Get theme colors from various WordPress sources
         * 
         * @return array Array of color values
         */
        public static function get_theme_colors() {
            $colors = array(
                'primary' => null,
                'secondary' => null,
                'accent' => null,
                'text' => null,
                'background' => null
            );
            
            // Try Method 1: theme.json (Block Themes)
            if (class_exists('WP_Theme_JSON_Resolver')) {
                $theme_json_colors = self::get_theme_json_colors();
                if (!empty($theme_json_colors)) {
                    $colors = array_merge($colors, $theme_json_colors);
                }
            }
            
            // Try Method 2: Customizer (Classic Themes)
            $customizer_colors = self::get_customizer_colors();
            if (!empty($customizer_colors)) {
                $colors = array_merge($colors, $customizer_colors);
            }
            
            // Try Method 3: Editor Color Palette
            $editor_colors = self::get_editor_palette_colors();
            if (!empty($editor_colors)) {
                $colors = array_merge($colors, $editor_colors);
            }
            
            // Fallback to defaults if no colors found
            $colors = array_filter($colors); // Remove nulls
            if (empty($colors)) {
                $colors = self::get_default_colors();
            }
            
            return $colors;
        }
        
        /**
         * Get colors from theme.json (Block Themes)
         */
        private static function get_theme_json_colors() {
            $colors = array();
            
            try {
                if (class_exists('WP_Theme_JSON_Resolver')) {
                    $theme_json = WP_Theme_JSON_Resolver::get_merged_data();
                    $settings = $theme_json->get_settings();
                    
                    if (isset($settings['color']['palette']['theme'])) {
                        foreach ($settings['color']['palette']['theme'] as $color) {
                            $slug = $color['slug'] ?? '';
                            $value = $color['color'] ?? '';
                            
                            // Map common slugs
                            if (in_array($slug, ['primary', 'secondary', 'accent', 'text', 'background'])) {
                                $colors[$slug] = $value;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // Silent fail
            }
            
            return $colors;
        }
        
        /**
         * Get colors from WordPress Customizer
         */
        private static function get_customizer_colors() {
            $colors = array();
            
            // Common customizer setting names
            $common_settings = array(
                'primary_color' => 'primary',
                'secondary_color' => 'secondary',
                'accent_color' => 'accent',
                'text_color' => 'text',
                'background_color' => 'background',
                // Alternative names
                'main_color' => 'primary',
                'brand_color' => 'primary',
                'link_color' => 'primary',
                'heading_color' => 'text',
            );
            
            foreach ($common_settings as $setting => $key) {
                $value = get_theme_mod($setting);
                if ($value && !isset($colors[$key])) {
                    // Add # if missing
                    $value = self::ensure_hex_color($value);
                    $colors[$key] = $value;
                }
            }
            
            return $colors;
        }
        
        /**
         * Get colors from Editor Color Palette
         */
        private static function get_editor_palette_colors() {
            $colors = array();
            
            $editor_palette = get_theme_support('editor-color-palette');
            if ($editor_palette && is_array($editor_palette[0])) {
                foreach ($editor_palette[0] as $color) {
                    $slug = $color['slug'] ?? '';
                    $value = $color['color'] ?? '';
                    
                    if (in_array($slug, ['primary', 'secondary', 'accent', 'text', 'background'])) {
                        $colors[$slug] = $value;
                    }
                }
            }
            
            return $colors;
        }
        
        /**
         * Get default fallback colors
         */
        private static function get_default_colors() {
            return array(
                'primary' => '#667eea',
                'secondary' => '#764ba2',
                'accent' => '#85d200',
                'text' => '#2d3748',
                'background' => '#ffffff'
            );
        }
        
        /**
         * Ensure color has # prefix
         */
        private static function ensure_hex_color($color) {
            $color = trim($color);
            if (substr($color, 0, 1) !== '#') {
                $color = '#' . $color;
            }
            return $color;
        }
        
        /**
         * Generate CSS variables from theme colors
         */
        public static function generate_css_variables($colors = null) {
            if ($colors === null) {
                $colors = self::get_theme_colors();
            }
            
            $css = ':root {' . "\n";
            foreach ($colors as $name => $value) {
                $css .= "    --rbs-theme-{$name}: {$value};\n";
            }
            $css .= '}' . "\n";
            
            return $css;
        }
        
        /**
         * Output inline CSS with theme colors
         */
        public static function output_theme_color_css() {
            $colors = self::get_theme_colors();
            $css = self::generate_css_variables($colors);
            
            echo '<style id="rbs-travel-theme-colors">' . "\n";
            echo $css;

            $primary = null;
            $secondary = null;
            $text = null;
            $background = null;
            
            if (class_exists('\\RBS_TRAVEL\\INCLUDES\\RBS_TRAVEL_Settings')) {
                $primary = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('primary_color', null);
                $secondary = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('secondary_color', null);
                $text = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('text_color', null);
            }

            if (!$primary && isset($colors['primary'])) $primary = $colors['primary'];
            if (!$secondary && isset($colors['secondary'])) $secondary = $colors['secondary'];
            if (!$text && isset($colors['text'])) $text = $colors['text'];
            if (!$background && isset($colors['background'])) $background = $colors['background'];
            
            if (!$primary) $primary = '#667eea';
            if (!$secondary) $secondary = '#764ba2';
            if (!$text) $text = '#2d3748';
            if (!$background) $background = '#ffffff';
            
            // Map to plugin-specific variables
            echo '
/* Apply theme colors to travel plugin */
:root {
    --rbs-primary: ' . esc_html($primary) . ';
    --rbs-secondary: ' . esc_html($secondary) . ';
    --rbs-accent: var(--rbs-theme-accent);
    --rbs-text: ' . esc_html($text) . ';
    --rbs-background: ' . esc_html($background) . ';
}
';
            
            echo '</style>' . "\n";
        }
        
        /**
         * Get inline CSS string for direct output
         */
        public static function get_inline_css() {
            $colors = self::get_theme_colors();
            
            return "
            .rbs-btn-search,
            .rbs-card-price-tag,
            .rbs-view-btn.active {
                background: {$colors['primary']};
            }
            
            .rbs-card-destination,
            .rbs-stats strong {
                color: {$colors['primary']};
            }
            
            .rbs-search-input:focus,
            .rbs-filter-select:focus {
                border-color: {$colors['primary']};
            }
            
            .rbs-card-title {
                color: {$colors['text']};
            }
            ";
        }
    }
}
