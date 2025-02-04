<?php 

if (!defined('ABSPATH')) {
    exit;
}

if( !class_exists('Risbl_Admin_Action') ) :

    class Risbl_Admin_Action {

        /**
         * Constructor initializes default values.
         */
        public function __construct() {

            add_action('wp_loaded', [$this, 'setting_action']);

        }

        public function setting_action() {
            do_action('risbl_admin'); // Use this hook to process either form submission or http request
        }

    }

    new Risbl_Admin_Action();

endif;