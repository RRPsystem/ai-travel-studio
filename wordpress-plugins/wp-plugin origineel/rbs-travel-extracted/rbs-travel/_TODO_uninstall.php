<?php
//https://digwp.com/2019/11/wordpress-uninstall-php/


if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

function rbs_delete_plugin() {
    
    /*
     * ex:
     * - delete 'post_meta'     
     *      >> delete_post_meta_by_key($post_meta_name);
     * - delete 'options'       
     *      >> delete_option($option_name);
     */    
    
}

rbs_delete_plugin();
