<?php
/**
 * Debug Error Logger
 * Shows PHP errors on screen for debugging
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler to show errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_type = match($errno) {
        E_ERROR => 'Fatal Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_STRICT => 'Strict',
        E_DEPRECATED => 'Deprecated',
        default => 'Error'
    };
    
    echo "<div style='background: #ff6b6b; color: white; padding: 15px; margin: 10px; border-radius: 5px; font-family: monospace;'>";
    echo "<strong>🔴 RBS Travel Debug - {$error_type}:</strong><br>";
    echo "<code>{$errstr}</code><br>";
    echo "<small>File: {$errfile} on line {$errline}</small>";
    echo "</div>";
    
    // Also log to file
    $log_file = __DIR__ . '/debug-log.txt';
    $log_entry = date('Y-m-d H:i:s') . " - {$error_type}: {$errstr} in {$errfile}:{$errline}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    return false; // Continue with normal error handling
});

// Exception handler
set_exception_handler(function($e) {
    echo "<div style='background: #ff0000; color: white; padding: 15px; margin: 10px; border-radius: 5px; font-family: monospace;'>";
    echo "<strong>🔴 RBS Travel Debug - Exception:</strong><br>";
    echo "<code>" . $e->getMessage() . "</code><br>";
    echo "<small>File: " . $e->getFile() . " on line " . $e->getLine() . "</small><br>";
    echo "<pre style='font-size: 11px; margin-top: 10px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
    
    // Also log to file
    $log_file = __DIR__ . '/debug-log.txt';
    $log_entry = date('Y-m-d H:i:s') . " - Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
});

// Shutdown handler for fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<div style='background: #ff0000; color: white; padding: 15px; margin: 10px; border-radius: 5px; font-family: monospace;'>";
        echo "<strong>🔴 RBS Travel Debug - Fatal Error:</strong><br>";
        echo "<code>" . $error['message'] . "</code><br>";
        echo "<small>File: " . $error['file'] . " on line " . $error['line'] . "</small>";
        echo "</div>";
        
        // Also log to file
        $log_file = dirname(__FILE__) . '/debug-log.txt';
        $log_entry = date('Y-m-d H:i:s') . " - Fatal: " . $error['message'] . " in " . $error['file'] . ":" . $error['line'] . "\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
});
