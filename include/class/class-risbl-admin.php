<?php 

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if( !class_exists('Risbl_Admin') ) :

    class Risbl_Admin {
        public $page_args = [];
        public $parent_admin;
        public $parent_admin_slug;
        public $callback;
        public $admin_url;
        public $get_tab;
        public $tab_name;
        public $is_admin_home;
        public $tab_param;
        public $tab_index;
        public $current_tab_url;
        public $current_group;
        public $has_group;
        public $group_index;
        public $current_group_url;
        public $current_page;

        /**
         * Constructor initializes default values.
         */
        public function __construct() {
            // Default arguments for the menu page.
            $this->page_args = [
                'parent_admin'      => '',
                'parent_admin_slug' => '',
                'page_title'        => __('Risbl Admin', 'risbl-admin'),
                'menu_title'        => __('Default Admin', 'risbl-admin'),
                'capability'        => 'manage_options',
                'menu_slug'         => 'default-admin',
                'icon_url'          => '',
                'position'          => null,
                'tabs'              => array(),
                'tab_param'         => '',
                'tab_index'         => '',
                'has_group'         => '',
                'group_index'       => '',
                'form_wrap'         => array(),
            ];

            // Default callback
            $this->callback = [$this, 'render_admin_page'];

            // Default admin URL
            $this->admin_url = [$this, 'admin_url'];

            // Default tab home
            $this->is_admin_home = (count($_GET) === 1) ? true : false;

            // Tab index
            $this->tab_index = [$this, 'tab_index'];

            // Current page
            $this->current_page = '';

            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);

        }

        public function enqueue_admin_styles() {

            // Define the CSS file path relative to your plugin directory
            $css_url = RISBL_ADMIN_PLUGIN_URL . 'assets/style.css';

            // Define the JS file
            $js_url = RISBL_ADMIN_PLUGIN_URL . 'assets/script.js';

            // Enqueue the CSS file
            wp_enqueue_style('risbl-admin-styles', $css_url, [], '1.0.0', 'all');

            // Add color picker
            wp_enqueue_style('wp-color-picker');

            wp_enqueue_media();
            
            wp_enqueue_script('risbl-admin-script', $js_url, array('wp-i18n', 'jquery', 'wp-color-picker'), '1.0', true);

        }

        public function admin_url() {
            $parent_admin_slug = ( !empty($this->parent_admin_slug) && '' !== $this->parent_admin_slug ) ? $this->parent_admin_slug : 'admin.php';
            return admin_url($parent_admin_slug.'?page='.$this->menu_slug());
        }

        /**
         * Configure the page with custom arguments.
         *
         * @param array $args Array of custom arguments to override defaults.
         * @return $this
         */
        public function config(array $args) {
            $this->page_args                = array_merge($this->page_args, $args);
            $this->parent_admin             = $this->page_args['parent_admin'];
            $this->parent_admin_slug        = $this->page_args['parent_admin_slug'];
            $this->admin_url                = $this->admin_url();
            $this->tab_index                = $this->tab_index();
            $this->tab_param                = $this->tab_param();
            $param                          = $this->tab_param;

            $this->get_tab                  = isset($_GET[$param]) && !empty($_GET[$param]) ? sanitize_text_field($_GET[$param]) : '';
            $this->current_tab_url          = empty($this->get_tab) ? $this->admin_url : add_query_arg(array(
                                            $param => $this->get_tab,
                                        ), $this->admin_url);

            $this->has_group                = $this->page_args['has_group'];
            $this->group_index              = $this->page_args['group_index'];
            $default_group_index            = ( ('yes' === $this->has_group) && !empty($this->group_index) ) ? $this->group_index : '';
            $this->current_group            = isset($_GET['group']) && !empty($_GET['group']) ? sanitize_text_field($_GET['group']) : $default_group_index;

            $this->current_group_url        = ( $this->has_group === 'yes' ) ? add_query_arg(array('group' => $this->current_group), $this->current_tab_url) : '';

            $this->current_page             = $this->get_tab;

            // If a callback is provided in the array, override the default.
            if (isset($args['callback'])) {
                $this->callback = $args['callback'];
            }

            return $this; // Allows method chaining
        }

        /**
         * Method to register the menu page.
         */
        public function add_setting_screen() {
            if( empty($this->parent_admin) || ('' === $this->parent_admin) ) {
                add_action('admin_menu', [$this, 'register_menu_page']);
            }
            if( !empty($this->parent_admin) && ('' !== $this->parent_admin) ) {
                add_action('admin_menu', [$this, 'register_submenu_page']);
            }
            add_action('risbl_admin_render_page_header__' . $this->menu_slug(), [$this, 'tabs_html'], 5, 0);
        }

        /**
         * Dynamically add content using a callback.
         *
         * @param string $tab_name The name of the tab or section.
         * @param callable $section The callback function to generate content.
         */
        public function add_page($tab_name, $section) {

            add_action('risbl_admin_render_page__' . $this->menu_slug(), function () use ($tab_name, $section) {
                if (is_callable($section)) {
                    if( ( $tab_name === $this->get_tab ) || $this->is_tab_index($tab_name) ) :
                        echo '<div class="risbl-admin-tab-content">';
                        call_user_func($section, $this);
                        echo '</div>';
                    endif;
                } else {
                    echo '<p>Invalid content callback provided.</p>';
                }
            }, 5, 1);

        }

        /**
         * Dynamically add group
         */

        public function add_group($args) {

            echo $this->render_group_nav($args);

            /*
            add_action('risbl_admin_render_page__' . $this->menu_slug(), function() use ($args){

            }); // Action ends
            */

        }

        /**
         * Render group nav
         */
        public function render_group_nav($args) {
            if( $this->has_group != 'yes' ) {
                return;
            }
            ob_start();

            if( is_array($args) && count($args) < 1 ) {
                return;
            }
            $html = '';
            $html .= '<div class="risbl-admin-tab-content__group-nav">';
            $count_group = count($args);
            $i = 0;
            $separator = '&nbsp;|&nbsp;';
            if( is_array($args) && count($args) > 0 ) {
                foreach ($args as $group) {                      

                    $slug               = isset($group['slug']) ? $group['slug'] : '';
                    $label              = isset($group['label']) ? $group['label'] : '';
                    $parent             = isset($group['parent_tab']) ? $group['parent_tab'] : '';
                    $is_index           = isset($group['is_index']) && ('yes' === $group['is_index']) ? true : false;
                    $is_index_in_parent = ($parent === $this->tab_index) ? true : false;
                    $tab_url_parent     = $this->current_tab_url;

                    $group_url = add_query_arg(array(
                                    $this->tab_param => $parent,
                                    'group' => $slug,
                                ), $tab_url_parent);
                    
                    $group_nav = array(
                        'group-link'    => sprintf('<a href="%1s">%2s</a> ', $group_url, $label),
                        'group-current' => sprintf('<span>%1s</span> ', $label),
                    );

                    $link = ( $slug === $this->current_group || ($is_index && (count($_GET) === 1)) ) ? $group_nav['group-current'] : $group_nav['group-link'];

                    $index_slug = '';
                    if (isset($group['is_index']) && $group['is_index'] === 'yes') {
                        $index_slug = $slug;
                    }  

                    /*
                    if( $slug === $index_slug && !$this->current_group && ($is_index && $is_index_in_parent ) ) {
                        $link = $group_nav['group-current'];
                    } */

                    if( $slug === $index_slug && !$this->current_group && ($is_index && $is_index_in_parent ) && isset($_GET[$this->tab_param]) && ($parent === $_GET[$this->tab_param]) ) {
                        $link = $group_nav['group-current'];
                    }

                    $sep = (++$i === $count_group) ? '' : $separator;

                    $html .= $link . $sep;

                }
            } 
            $html .= '</div>';
            echo $html;

            return ob_get_clean();
        }

        /**
         * Method to register tabs.
         */
        public function tabs() {
            return isset($this->page_args['tabs']) && (count($this->page_args['tabs']) > 1) ? $this->page_args['tabs'] : array();
        }

        /**
         * Method to define tab param.
         */
        public function tab_param() {
            return isset($this->page_args['tab_param']) && !empty($this->page_args['tab_param']) ? $this->page_args['tab_param'] : 'risbl_admin_tab';
        }

        /**
         * Method to define is tab index.
         */
        public function is_tab_index($tab_name) {
            return ( $tab_name === $this->tab_index && $this->is_admin_home ) ? true : false;
        }

        public function is_group($group_slug) {
            return ($group_slug === $this->current_group) ? true : false;
        }

        public function tabs_html() {

            $setting_menu_slug  = $this->page_args['menu_slug'];
            $tabs               = $this->tabs();
            $param              = $this->tab_param;
            $tabItems           = count($tabs);
            $separator          = '<span class="separator">&nbsp;|&nbsp;</span>';
            $html               = '';
            $i                  = 0;

            foreach ($tabs as $key => $value) {

                $tab_url = add_query_arg(array(
                                            $param => $key,
                                        ), $this->admin_url);
                
                $current_class = 'current-page';
                $label = $value;

                $tab_menu = apply_filters('risbl_admin_tab_menu_item', array(
                    'page-link'      => sprintf('<a href="%1s">%2s</a>', esc_url($tab_url), esc_attr($label)),
                    'page-current'   => sprintf('<span class="%1s">%2s</span>', esc_attr($current_class), $label),
                ), $setting_menu_slug, $key, $tab_url, $label);

                $link_active = $tab_menu['page-link'];
                $current_tab = $tab_menu['page-current'];

                if( $key === $this->tab_index && $this->is_admin_home ) {
                    $link_active = $current_tab;
                }

                $tab_link = ( $key === $this->get_tab ) ? $current_tab : $link_active;

                $html .= $tab_link; 

                if(++$i === $tabItems) { // Last item
                    $separator = '';
                }

                $html .= $separator;

            }

            $return = '<div class="risbl-admin-tabs-wrapper">' . $html . '</div>';

            if( $this->current_page != '' && !array_key_exists($this->current_page, $tabs) ) {
                $return = '';
            }

            echo $return;

        }

        /**
         * Method to register the page slug.
         */
        public function menu_slug() {
            return $this->page_args['menu_slug'];
        }

        /**
         * Method to register tab index.
         */
        public function tab_index() {
            return $this->page_args['tab_index'];
        }

        /**
         * Method to add the menu page.
         */
        public function register_menu_page() {
            add_menu_page(
                $this->page_args['page_title'],
                $this->page_args['menu_title'],
                $this->page_args['capability'],
                $this->menu_slug(),
                [$this, 'render_admin_screen'], // Use the render_admin_screen method
                $this->page_args['icon_url'],
                $this->page_args['position']
            );
        }

        /**
         * Method to add submenu page.
         */
        public function register_submenu_page() {
            add_submenu_page(
                $this->page_args['parent_admin'],
                $this->page_args['page_title'],
                $this->page_args['menu_title'],
                $this->page_args['capability'],
                $this->menu_slug(),
                [$this, 'render_admin_screen'], // Use the render_admin_screen method
                $this->page_args['position']
            );
        }

        public function form_wrap() {

            $form =  array('<form method="GET" action="">', '</form>');

            $form_wrap = isset($this->page_args['form_wrap']) && !empty($this->page_args['form_wrap']) ? $this->page_args['form_wrap'] : array();

            if( is_array($form_wrap) && (count($form_wrap) > 0) ) {
                $form = $form_wrap;
            }

            if( count($form_wrap) < 1 ) {
                $form = array();
            }

            return $form;
        }

        /**
         * Wrapper for the callback that triggers an action hook.
         */
        public function render_admin_screen() { 

            $form_wrap  = $this->form_wrap();
            $form_open  = ( is_array($form_wrap) && isset($form_wrap[0]) ) ? $form_wrap[0] : '';
            $form_close = ( is_array($form_wrap) && isset($form_wrap[1]) ) ? $form_wrap[1] : '';

            echo '<div class="wrap">';
            echo $form_open;
            echo '<div class="risbl-admin-panel">';

            echo '<div class="risbl-admin-panel__heading-area">';
                // Call the actual callback
                call_user_func($this->callback);
                // Header area hook
                do_action('risbl_admin_render_page_header__' . $this->menu_slug());
            echo '</div>';

            echo '<div class="risbl-admin-panel__content-area">';
            do_action('risbl_admin_admin_notice'); // Display admin notice here.
            // Trigger a custom action hook based on the menu slug
            do_action('risbl_admin_render_page__' . $this->menu_slug());
            echo '</div>';

            echo '</div><!-- /.risbl-admin-panel -->';
            echo $form_close;
            echo '</div>';

        }

        /**
         * Default callback to render the admin page.
         */
        public function render_admin_page() {
            echo '<h1 class="wp-heading-inline">' . esc_html($this->page_args['page_title']) . '</h1>';
            // Add more content here.
        }



    }

endif;