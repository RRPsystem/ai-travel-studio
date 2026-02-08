<?php
defined('RBS_TRAVEL') or die();

function rbstravel_sc_ideajson($atts = array()) {
    if (is_admin()) {
        return;
    }

    $ideas = rbstravel_get_ideas(array(), true);
    return '<pre>' . $ideas. '</pre>';

} 
add_shortcode('rbstravel_ideajson', 'rbstravel_sc_ideajson');