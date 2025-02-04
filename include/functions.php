<?php 

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if( !function_exists('Risbl_Admin') ) {
    function Risbl_Admin() {
        return new Risbl_Admin();
    }
}

if( !function_exists('Risbl_Admin_Field') ) {
    function Risbl_Admin_Field() {
        return new Risbl_Admin_Field();
    }
}