<?php
/**
 * Media Library Fix
 * Ensures WordPress Media Library is loaded for photo uploads
 */

if (!defined('ABSPATH')) exit;

// Enqueue media library on travel idea edit screens
add_action('admin_enqueue_scripts', function($hook) {
    // Only load on post edit screens
    if (!in_array($hook, ['post.php', 'post-new.php'])) {
        return;
    }
    
    global $post;
    if (!$post || $post->post_type !== 'rbs-travel-idea') {
        return;
    }
    
    // Load WordPress Media Library
    wp_enqueue_media();
}, 10);
