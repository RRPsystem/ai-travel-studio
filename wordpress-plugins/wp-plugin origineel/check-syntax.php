<?php
/**
 * PHP Syntax Checker for RBS Travel Plugin
 * Run this file directly: php check-syntax.php
 */

echo "=== RBS Travel Plugin Syntax Checker ===\n\n";

$plugin_path = __DIR__ . '/rbs-travel/';
$errors = array();
$checked = 0;

// Get all PHP files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($plugin_path)
);

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    
    // Skip backup files
    if (strpos($file->getPathname(), '.backup') !== false) {
        continue;
    }
    if (strpos($file->getPathname(), 'BACKUP') !== false) {
        continue;
    }
    if (strpos($file->getPathname(), 'backup') !== false) {
        continue;
    }
    
    $checked++;
    $relative_path = str_replace($plugin_path, '', $file->getPathname());
    
    // Check syntax using php -l
    $output = array();
    $return_var = 0;
    exec('php -l "' . $file->getPathname() . '" 2>&1', $output, $return_var);
    
    if ($return_var !== 0) {
        $errors[] = array(
            'file' => $relative_path,
            'error' => implode("\n", $output)
        );
        echo "❌ ERROR: $relative_path\n";
        echo "   " . implode("\n   ", $output) . "\n\n";
    } else {
        echo "✓ OK: $relative_path\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Checked: $checked files\n";
echo "Errors: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\n=== ERRORS FOUND ===\n";
    foreach ($errors as $error) {
        echo "\nFile: " . $error['file'] . "\n";
        echo $error['error'] . "\n";
    }
}
