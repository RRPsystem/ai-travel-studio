<?php
defined('RBS_TRAVEL') or die();

function rbstravel_sc_idealist($atts = array()) {
    if (is_admin()) {
        return;
    }

    //return '<pre>' . print_r($atts, true) . '</pre>';

    $ideas = rbstravel_get_ideas(array(), true);

    
    $vars = array();
 
    $vars['ideas'] = $ideas;

    $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealistlayout';
    $args = array(
        'add_wrapper' => false,                
    );

    echo rbstravel_template_loader($template, $vars, null, $args);     

    return;    

    
    // foreach ($vars['ideas'] as $idea) {
    //      echo print_r($idea);
    // }



    return '<pre>' .  'ccc' . '</pre>';    
} 
add_shortcode('rbstravel_idealist', 'rbstravel_sc_idealist');