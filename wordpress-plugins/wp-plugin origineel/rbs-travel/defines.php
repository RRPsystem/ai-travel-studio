<?php
/**
 * RBS Travel Plugin - Constants Definition
 * All constants are protected against redefinition
 */

// Prevent direct access and double loading
if (defined('RBS_TRAVEL_DEFINES_LOADED')) {
    return;
}
define('RBS_TRAVEL_DEFINES_LOADED', true);

// Plugin Identifier
if (!defined('RBS_TRAVEL')) {
    define('RBS_TRAVEL', 1);
}
if (!defined('RBS_TRAVEL_VERSION')) {
    define('RBS_TRAVEL_VERSION', '5.14.56');
}

// Define plugin paths
if (!defined('RBS_TRAVEL_PLUGIN_PATH')) {
    define('RBS_TRAVEL_PLUGIN_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_PLUGIN_PATH_INCLUDES')) {
    define('RBS_TRAVEL_PLUGIN_PATH_INCLUDES', RBS_TRAVEL_PLUGIN_PATH . 'includes' . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_PLUGIN_PATH_OBJECTS')) {
    define('RBS_TRAVEL_PLUGIN_PATH_OBJECTS', RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'objects' . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_PLUGIN_PATH_HELPERS')) {
    define('RBS_TRAVEL_PLUGIN_PATH_HELPERS', RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'helpers' . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_PLUGIN_PATH_SHORTCODES')) {
    define('RBS_TRAVEL_PLUGIN_PATH_SHORTCODES', RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'shortcodes' . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_PLUGIN_PATH_TEMPLATES')) {
    define('RBS_TRAVEL_PLUGIN_PATH_TEMPLATES', RBS_TRAVEL_PLUGIN_PATH . 'templates' . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_PLUGIN_PATH_ASSETS')) {
    define('RBS_TRAVEL_PLUGIN_PATH_ASSETS', RBS_TRAVEL_PLUGIN_PATH . 'assets' . DIRECTORY_SEPARATOR);
}

// Upload paths (require $upload_directory variable to be set before including this file)
if (!defined('RBS_TRAVEL_UPLOADS_PATH') && isset($upload_directory)) {
    define('RBS_TRAVEL_UPLOADS_PATH', $upload_directory);
}
if (!defined('RBS_TRAVEL_UPLOADS_PATH_TMP') && defined('RBS_TRAVEL_UPLOADS_PATH')) {
    define('RBS_TRAVEL_UPLOADS_PATH_TMP', RBS_TRAVEL_UPLOADS_PATH . 'tmp' . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_UPLOADS_PATH_LOGS') && defined('RBS_TRAVEL_UPLOADS_PATH')) {
    define('RBS_TRAVEL_UPLOADS_PATH_LOGS', RBS_TRAVEL_UPLOADS_PATH . 'logs' . DIRECTORY_SEPARATOR);
}
if (!defined('RBS_TRAVEL_UPLOADS_PATH_MAPS') && defined('RBS_TRAVEL_UPLOADS_PATH')) {
    define('RBS_TRAVEL_UPLOADS_PATH_MAPS', RBS_TRAVEL_UPLOADS_PATH . 'maps' . DIRECTORY_SEPARATOR);
}

// Plugin URLs
if (!defined('RBS_TRAVEL_PLUGIN_URL')) {
    define('RBS_TRAVEL_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('RBS_TRAVEL_PLUGIN_URL_ASSETS')) {
    define('RBS_TRAVEL_PLUGIN_URL_ASSETS', RBS_TRAVEL_PLUGIN_URL . 'assets/');
}
if (!defined('RBS_TRAVEL_UPLOAD_URL') && isset($upload_url)) {
    define('RBS_TRAVEL_UPLOAD_URL', $upload_url);
}

// API URL
if (!defined('RBS_TRAVEL_API_URL')) {
    define('RBS_TRAVEL_API_URL', 'https://online.travelcompositor.com/resources');
}