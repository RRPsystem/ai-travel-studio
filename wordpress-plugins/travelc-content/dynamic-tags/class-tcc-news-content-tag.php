<?php
/**
 * TravelC News Content Dynamic Tag
 */

if (!defined('ABSPATH')) {
    exit;
}

class TCC_News_Content_Tag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'tcc-news-content';
    }

    public function get_title() {
        return 'TravelC Nieuws Inhoud';
    }

    public function get_group() {
        return 'travelc';
    }

    public function get_categories() {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }

    public function render() {
        $news = tcc_get_news_data();
        
        if (!$news) {
            echo '';
            return;
        }
        
        $content = $news['content'] ?? '';
        
        // Handle array content (from API)
        if (is_array($content) && isset($content['html'])) {
            $content = $content['html'];
        }
        
        // Strip embedded CSS/style tags from builder output
        $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
        
        // Strip CSS comments and rules
        $content = preg_replace('/\/\*\s*(Reset|Component|Utility).*?\*\/.*?(?=<|$)/is', '', $content);
        
        // Clean up any remaining raw CSS lines
        $lines = explode("\n", $content);
        $filtered_lines = array_filter($lines, function($line) {
            $trimmed = trim($line);
            if (empty($trimmed)) return true;
            // Skip lines that look like CSS rules
            if (preg_match('/^[a-z\.\#\*\@\[\:]/i', $trimmed) && strpos($trimmed, '{') !== false) {
                return false;
            }
            if (preg_match('/^\s*(margin|padding|font-|color|background|border|display|position|width|height|box-sizing|line-height)[\s:]/i', $trimmed)) {
                return false;
            }
            if (preg_match('/^\s*[\}\{]\s*$/', $trimmed)) {
                return false;
            }
            return true;
        });
        $content = implode("\n", $filtered_lines);
        
        echo wpautop(wp_kses_post(trim($content)));
    }
}
