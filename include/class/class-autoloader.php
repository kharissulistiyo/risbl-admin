<?php

/**
 * Enable autoloading of plugin classes in namespace.
 * This works for all classes in the same namespace.
 * @param string $class_name The fully-qualified class name.
 */
function risbl_admin_autoload($class_name) {
    // Only autoload classes from this namespace.
    if ( false === strpos( $class_name, __NAMESPACE__ ) ) {
        return;
    }

    // Remove namespace from class name.
    $class_file = str_replace( __NAMESPACE__ . '\\', '', $class_name );

    // Convert class name format to file name format.
    $class_file = str_replace( '_', '-', strtolower( $class_file ) );

    // Convert sub-namespaces into directories.
    $class_path = explode( '\\', $class_file );
    $class_file = array_pop( $class_path );
    $class_path = implode( '/', $class_path );

    // Directories to search for classes.
    $directories = array(
        __DIR__,                            // Root directory
        __DIR__ . '/customer',              // Subdirectory: customer
        // Add more subdirectories here if needed
    );

    // Autoload all PHP files in the directories.
    foreach ($directories as $directory) {
        if (is_dir($directory)) {
            $files = scandir($directory);
            foreach ($files as $file) {
                if (is_file($directory . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $class_file = $directory . '/' . $file;
					require_once $class_file;
                }
            }
        }
    }

}

spl_autoload_register(__NAMESPACE__ . '\risbl_admin_autoload');