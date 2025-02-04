<?php 

if (!defined('ABSPATH')) {
    exit;
}

// Form processing
if( class_exists('Risbl_Admin') ) :

    add_action('risbl_admin', function(){

        $url = admin_url('admin.php?page=risbl-admin-a');

        if( isset($_GET['username']) ) {

            wp_safe_redirect($url);
            exit;
        }

    });

endif;


// Build fields

if( class_exists('Risbl_Admin_Field') ) :
    function risbl_admin_shop_marketing_field1($page1) {

        ob_start();

        echo '<table class="form-table" role="presentation"><tbody>';

        // Define fields
        $field = Risbl_Admin_Field();

        echo $field->group(array(
            'row_title' => 'Members',
            'layout' => 'wp-admin-form-table',
            'fields' => array(
                $field->input([
                    'id' => 'member1',
                    'type' => 'text',
                    'label' => 'Member 1',
                    'default_value' => '',
                    'part_of' => $page1->is_group('group-1'),
                ]),
                $field->input([
                    'id' => 'member2',
                    'type' => 'text',
                    'label' => 'Member 2',
                    'default_value' => '',
                    'part_of' => $page1->is_group('group-1'),
                ]),
                $field->radio([
                    'id' => 'member_type',
                    'label' => 'Member type',
                    'options' => ['male' => 'Male', 'female' => 'Female'],
                    'default_value' => 'male',
                    'part_of' => $page1->is_group('group-1'),
                ]),
            ),
        ));

        // Create a textarea
        echo $field->textarea([
            'id' => 'feedback',
            'label' => __('Feedback', 'risbl-admin'),
            'description' => __('Enter yout unique feedback here.', 'risbl-admin'),
            'default_value' => 'Your feedback here...',
            'placeholder' => 'Write your thoughts...',
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Create an input field
        echo $field->input([
            'id' => 'username',
            'label' => __('Username', 'risbl-admin'),
            'description' => __('Enter yout unique username here.', 'risbl-admin'),
            'type' => 'text',
            'placeholder' => 'Enter your username',
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Radio
        echo $field->radio([
            'id' => 'gender',
            'label' => 'Gender',
            'options' => ['male' => 'Male', 'female' => 'Female'],
            'default_value' => 'male',
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Dropdown
        echo $field->dropdown([
            'id' => 'country',
            'label' => 'Select Your Country',
            'options' => ['us' => 'USA', 'ca' => 'Canada', 'uk' => 'UK'],
            'default_value' => 'us',
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Multiselect
        echo $field->multiselect([
            'id' => 'skills',
            'label' => 'Select Your Skills',
            'options' => ['php' => 'PHP', 'js' => 'JavaScript', 'css' => 'CSS'],
            'default_value' => ['php', 'css'],
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Render a color picker
        echo $field->color_picker([
            'id' => 'background_color',
            'label' => 'Background Color',
            // 'class' => 'risbl-admin-color-picker',
            'default_value' => '#ffffff', // Default color
            'description' => 'Choose a background color for the section.',
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Render a media upload field
        echo $field->media_upload([
            'id' => 'featured_image',
            'label' => 'Featured Image',
            'default_value' => '', // Optional default URL
            'description' => 'Upload an image for the featured section.',
            'allowed_types' => 'image', // Restrict to images only
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Checkbox
        echo $field->checkbox([
            'id' => 'accept_terms',
            'row_title' => 'User consent',
            'label' => 'Accept Terms and Conditions',
            'default_value' => true,
            'layout' => 'wp-admin-form-table',
            'part_of' => $page1->is_group('group-1'),
        ]);

        echo '</tbody></table>';

        // Render a nonce field
        echo $field->nonce([
            'id' => 'settings_nonce',
            'action' => 'save_settings',
            'part_of' => $page1->is_group('group-1'),
        ]);

        // Create a submit field
        echo $field->submit([
            'id' => 'sbumit-form',
            'label' => __('Submit', 'risbl-admin'),
            'class' => 'button button-primary',
            'part_of' => $page1->is_group('group-1'),
        ]);


        // Get All fields
        $all_fields = $field->get_all_fields();

        // Define all fields end

        return ob_get_clean();

    } // Ends of risbl_admin_shop_marketing_field1($page1)

endif; // End fo class exists

// Build admin screen
if( class_exists('Risbl_Admin') ) :

    // Define the first admin page
    $page1 = Risbl_Admin();

    $page1->config([
        'parent_admin' => 'index.php',
        'parent_admin_slug' => 'index.php',
        'page_title' => __('Shop Marketing', 'risbl-admin'),
        'menu_title' => __('Shop Marketing', 'risbl-admin'),
        'capability' => 'manage_options',
        'menu_slug' => 'risbl-admin-a',
        'icon_url' => 'dashicons-editor-kitchensink',
        'position' => 9999,
        'tabs'  => array(
            'general' => __('General', 'risbl-admin'),
            'advanced' => __('Advanced', 'risbl-admin'),
            'web-hook' => __('Web Hook', 'risbl-admin'),
            'info'     => __('Info', 'risbl-admin'),
        ),
        'tab_param' => 'hae_screen',
        'tab_index' => 'general',
        'has_group' => 'yes',
        'group_index' => 'group-1',
        'form_wrap' => array('<form method="get" action="">', '</form>'),
    ])->add_setting_screen();

    $page1->add_page('general', 'prefix_risbl_admin_general');
    $page1->add_page('advanced', 'prefix_risbl_admin_advanced');
    $page1->add_page('web-hook', 'prefix_risbl_admin_webhook');
    $page1->add_page('info', 'prefix_risbl_admin_info');
    $page1->add_page('info2', 'prefix_risbl_admin_info2'); // Page without tabs menu
    $page1->add_page('info3', 'prefix_risbl_admin_info3'); // Page without tabs menu
    $page1->add_page('info4', 'prefix_risbl_admin_info4'); // Page without tabs menu

    function prefix_risbl_admin_info2($page1) {

        $go_back_url = add_query_arg(array(
            $page1->tab_param => 'info',
        ), $page1->admin_url);

        echo '<div>' . sprintf('<a href="%1s">%2s</a>', $go_back_url, 'Back to Info') . '</div>';

        echo '<h2>Info 2</h2>';

    }

    function prefix_risbl_admin_info3($page1) {

        $go_back_url = add_query_arg(array(
            $page1->tab_param => 'info',
        ), $page1->admin_url);

        echo '<div>' . sprintf('<a href="%1s">%2s</a>', $go_back_url, 'Back to Info') . '</div>';

        echo '<h2>Info 3</h2>';

    }

    function prefix_risbl_admin_info4($page1) {

        $go_back_url = add_query_arg(array(
            $page1->tab_param => 'info',
        ), $page1->admin_url);

        echo '<div>' . sprintf('<a href="%1s">%2s</a>', $go_back_url, 'Back to Info') . '</div>';

        echo '<h2>Info 4</h2>';

    }

    function prefix_risbl_admin_general($page1) {

        echo '<div>General settings</div>';

        $group_args = array();
        $group_args[] = array(
            'slug'      => 'group-1',
            'label'     => __('Group 1', 'risbl-admin'),
            'parent_tab' => 'general',
            'is_index'  => 'yes',
        );
        $group_args[] = array(
            'slug'      => 'group-2',
            'label'     => __('Group 2', 'risbl-admin'),
            'parent_tab' => 'general',
            'is_index'  => 'no',   
        );
        $group_args[] = array(
            'slug'      => 'group-3',
            'label'     => __('Group 3', 'risbl-admin'),
            'parent_tab' => 'general',  
        );
        $group_args[] = array(
            'slug'      => 'group-4',
            'label'     => __('Group 4', 'risbl-admin'),
            'parent_tab' => 'general',  
        );
        $group_args[] = array(
            'slug'      => 'group-5',
            'label'     => __('Group 5', 'risbl-admin'),
            'parent_tab' => 'general',  
        );

        $page1->add_group($group_args);

        if( $page1->is_group('group-1') ) : // Group 1 content
            echo '<h4>Group 1</h4>';
            echo risbl_admin_shop_marketing_field1($page1);
        endif; // Group 1 content ends

        if( $page1->is_group('group-2') ) : // Group 2 content

            echo '<h4>Group 2</h4>';

        endif; // Group 2 content ends

        if( $page1->is_group('group-3') ) : // Group 3 content

            echo '<h4>Group 3</h4>';

        endif; // Group 3 content ends

        if( $page1->is_group('group-4') ) : // Group 4 content

            echo '<h4>Group 4</h4>';

        endif; // Group 4 content ends

        if( $page1->is_group('group-5') ) : // Group 5 content

            echo '<h4>Group 5</h4>';

        endif; // Group 5 content ends

        echo 'Haeee';


    }

    function prefix_risbl_admin_advanced($page1) {

        echo '<div>Advanced settings</div>';

    }

    function prefix_risbl_admin_webhook($page1) {

        echo '<div>Web Hook settings</div>';

    }

    function prefix_risbl_admin_info($page1) {

        echo '<div><h3>Get more info</h3></div>';

        $info2_link = add_query_arg(array(
                            $page1->tab_param => 'info2',
                            'new_param' => '123',
                        ), $page1->admin_url);

        $info3_link = add_query_arg(array(
                            $page1->tab_param => 'info3',
                            'new_param' => '123',
                        ), $page1->admin_url);

        $info4_link = add_query_arg(array(
                            $page1->tab_param => 'info4',
                            'new_param' => '123',
                        ), $page1->admin_url);

        echo '<ul>';
        echo '<li>' . sprintf('<a href="%1s">%2s</a>', $info2_link, 'Info 2') . '</li>';
        echo '<li>' . sprintf('<a href="%1s">%2s</a>', $info3_link, 'Info 3') . '</li>';
        echo '<li>' . sprintf('<a href="%1s">%2s</a>', $info4_link, 'Info 4') . '</li>';
        echo '</ul>';

    }

    // Hook into the render process for 'risbl-admin-a'
    add_action('risbl_admin_render_page__risbl-admin-a', function () {
        echo '<p>This is content added via the render_page_risbl-admin-a hook.</p>';
    });

    /**
     * Page 2
     * =============================
     */

    $page2 = Risbl_Admin();
    
    $page2->config([
        'page_title' => __('Risbl Admin B', 'risbl-admin'),
        'menu_title' => __('Risbl Admin B', 'risbl-admin'),
        'capability' => 'manage_options',
        'menu_slug' => 'risbl-admin-b',
        'icon_url' => 'dashicons-editor-kitchensink',
        'position' => 28,
        'tabs'  => array(
            'general' => __('General', 'risbl-admin'),
            'customer' => __('Customer', 'risbl-admin'),
            'web-hook' => __('Web Hook', 'risbl-admin'),
            'info'     => __('Info', 'risbl-admin'),
        ),
        'tab_param' => 'hae_screen',
        'tab_index' => 'general',
        'has_group' => 'yes',
        'group_index' => 'product',
        'form_wrap' => null,
    ])->add_setting_screen();

    $page2->add_page('general', 'prefix_risbl_admin_adminb_general');
    $page2->add_page('customer', 'prefix_risbl_admin_adminb_customer');
    $page2->add_page('web-hook', 'prefix_risbl_admin_adminb_webhook');
    $page2->add_page('info', 'prefix_risbl_admin_adminb_info');

    function prefix_risbl_admin_adminb_general($page2) {

        echo '// General';

    }

    function prefix_risbl_admin_adminb_customer($page2) {

        echo '// / Customer';

    }

    function prefix_risbl_admin_adminb_webhook($page2) {

        /**
         * Create group within tab content
         */
        $group_args = array();
        $group_args[] = array(
            'slug'          => 'product',
            'label'         => __('Product', 'risbl-admin'),
            'parent_tab'    => 'web-hook',
            'is_index'      => 'yes',  
        );
        $group_args[] = array(
            'slug'          => 'checkout',
            'label'         => __('Checkout', 'risbl-admin'),
            'parent_tab'    => 'web-hook',  
        );

        $page2->add_group($group_args);

        if( $page2->is_group('product') ) :
            echo '<h4>== Product ==</h4>';
        endif;

        if( $page2->is_group('checkout') ) :
            echo '<h4>== Checkout ==</h4>';
        endif;
        /**
         * Create group ends
         */


    }

    function prefix_risbl_admin_adminb_info($page2) {

        echo '// Info';

    }


endif;