<?php
/**
 * Plugin Name: Risbl Admin
 * Plugin URI: https://admin.risbl.com
 * Description: A developer tool for plugin features administration panel UI (user interface) creation.
 * Version: 0.0.1
 * Author: Kharis Sulistiyono
 * Author URI: https://kharis.risbl.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: risbl-admin
 * Domain Path: /languages
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin path constant.
define('RISBL_ADMIN_PLUGIN_PATH', plugin_dir_path(__FILE__));

define('RISBL_ADMIN_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation hook.
function risbl_admin_activate() {
    // Activation code here.
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'risbl_admin_activate');

// Deactivation hook.
function risbl_admin_deactivate() {
    // Deactivation code here.
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'risbl_admin_deactivate');

// Load the main class inside 'plugins_loaded'.
function risbl_admin_plugin_loaded() {

      // Meanwhile, there is no need class autoloader.
      // require_once RISBL_ADMIN_PLUGIN_PATH . 'include/class/class-autoloader.php';

      $files = array(
        'risbl-admin'               => 'class/class-risbl-admin.php',
        'risbl-field'               => 'class/class-risbl-admin-field.php',
        'risbl-admin-action'        => 'class-risbl-admin-action.php',
        'functions'                 => 'functions.php',
      );
      
      foreach ($files as $key => $file) {
        $path_file = RISBL_ADMIN_PLUGIN_PATH . 'include/' . $file;
          if (file_exists($path_file)) {
          require_once $path_file;
        }
      }

      // Sample plugin menu and fields.
      // In this file you can study how plugin menu and setting fields area built.
      // You can remove this line and this file.
      require_once RISBL_ADMIN_PLUGIN_PATH . 'plugin-samples/samples.php';

}
add_action('plugins_loaded', 'risbl_admin_plugin_loaded');

// Load plugin textdomain.
function risbl_admin_load_textdomain() {
    load_plugin_textdomain('risbl-admin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'risbl_admin_load_textdomain');
