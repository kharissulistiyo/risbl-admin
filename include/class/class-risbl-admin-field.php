<?php 

if (!defined('ABSPATH')) {
    exit;
}

if( !class_exists('Risbl_Admin_Field') ) :

    class Risbl_Admin_Field {
        // Public variable for field configuration
        public $field_config = [];
        public $get_fields = [];
    
        // Constructor to initialize global field configuration (optional)
        public function __construct($field_config = []) {
            $this->field_config = $field_config;
        }

        public function get_all_fields() {
            return $this->get_fields;
        }
    
        // Method to get default arguments
        public function get_default_args() {
            return [
                'id'                => 'field',          // Default name
                'label'             => '',             // Default field label
                'row_title'         => '',             // Default field title
                'description'       => '',             // Default description
                'default_value'     => '',      // Default value
                'placeholder'       => '',        // Default placeholder
                'class'             => '',  // Default CSS class
                'type'              => 'text',            // Default input type
                'options'           => array(),          // For select, multiselect, radio
                'layout'            => '', // Default field layout
                'part_of'           => false, // Display condition
            ];
        }
    
        // Method to merge and resolve arguments
        public function args($args = []) {
            return wp_parse_args($args, wp_parse_args($this->field_config, $this->get_default_args()));
        }

        public function field_wrapper() {
            return array('<div class="risbl-admin-field-wrap">', '</div>');
        }

        public function field_label($id, $label) {
            return sprintf('<label for="%s">%s</label>',
                            esc_attr($id), esc_attr($label));
        }

        public function group($args = []) {

            $group_args = wp_parse_args( $args, array(
                'fields' => array(),
                'row_title' => '',
                'layout' => '',
            ) );

            $row_title = isset($args['row_title']) ? $args['row_title'] : '' ;
            $layout    = isset($args['layout']) ? $args['layout'] : '';

            $col_tag   = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;
            
            $wrapper = is_array($this->field_wrapper()) ? $this->field_wrapper() : array();
            $item_wrap = array('<div>', '</div>');

            $html = '';

            $html .= isset($wrapper[0]) ? $wrapper[0] : '';
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= ('' !== $row_title) ? $row_title : '';
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';
            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            $html .= '<div class="risbl-admin-field-group">';
            if( isset($group_args['fields']) && is_array($group_args['fields']) && count($group_args['fields']) > 0 ) {

                foreach ($group_args['fields'] as $field) {
                    $html .= $item_wrap[0] . $field . $item_wrap[1];
                }

            }
            $html .= '</div>';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';
            $html .= isset($wrapper[1]) ? $wrapper[1] : '';

            return $html;

        }

        public function field_col_tag($tag, $layout) {

            $html = '';

            if( 'wp-admin-form-table' === $layout) :
                switch ($tag) {
                    case 'th-open':
                        $html = '<tr><th scope="row">';
                        break;
                    case 'th-close':
                        $html = '</th>';
                        break;
                    case 'td-open':
                        $html = '<td>';
                        break;
                    case 'td-close':
                        $html = '</td></tr>';
                        break;
                    default:
                        // 
                        break;
                }
            endif;

            return $html;
        }
    
        // Method to render a textarea field
        public function textarea($args = []) {
            // Resolve arguments using $this->args()
            $args = $this->args($args);

            // Extract values
            $name               = isset($args['id']) ? $args['id'] : '';
            $label              = isset($args['label']) ? $args['label'] : '';
            $description        = isset($args['description']) ? $args['description'] : '';
            $default_value      = isset($args['default_value']) ? $args['default_value'] : '';
            $placeholder        = isset($args['placeholder']) ? $args['placeholder'] : '';
            $class              = isset($args['class']) ? $args['class'] : '';
            $layout             = isset($args['layout']) ? $args['layout'] : '';
            $part_of            = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;

            $field_id = 'risbl-admin-field-'.$name;
            $wrapper = is_array($this->field_wrapper()) ? $this->field_wrapper() : array();
            
            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );

            $html = '';
            $html .= isset($wrapper[0]) ? $wrapper[0] : '';
            
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';

            $html .= $this->field_label($field_id, $label);

            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';

            // Render the textarea

            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';

            $html .= sprintf(
                '<textarea id="%s" name="%s" class="%s" placeholder="%s">%s</textarea>',
                esc_attr($field_id),
                esc_attr($name),
                esc_attr($class),
                esc_attr($placeholder),
                esc_textarea($default_value)
            );
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';

            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';

            $html .= isset($wrapper[1]) ? $wrapper[1] : '';

            return !$part_of ? '' : $html;

        }
    
        // Method to render an input field
        public function input($args = []) {
            // Resolve arguments using $this->args()
            $args = $this->args($args);
    
            // Extract values
            $name               = isset($args['id']) ? $args['id'] : '';
            $label              = isset($args['label']) ? $args['label'] : '';
            $description        = isset($args['description']) ? $args['description'] : '';
            $default_value      = isset($args['default_value']) ? $args['default_value'] : '';
            $placeholder        = isset($args['placeholder']) ? $args['placeholder'] : '';
            $class              = isset($args['class']) ? $args['class'] : '';
            $type               = isset($args['type']) ? $args['type'] : '';
            $layout             = isset($args['layout']) ? $args['layout'] : '';
            $part_of            = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;

            $field_id = 'risbl-admin-field-'.$name;
            $wrapper = is_array($this->field_wrapper()) ? $this->field_wrapper() : array();
            
            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );

            $html = '';
            $html .= isset($wrapper[0]) ? $wrapper[0] : '';
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= $this->field_label($field_id, $label);
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';

            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            // Render the input field
            $html .= sprintf(
                '<input id="%s" type="%s" name="%s" class="%s" placeholder="%s" value="%s" />',
                esc_attr($field_id),
                esc_attr($type),
                esc_attr($name),
                esc_attr($class),
                esc_attr($placeholder),
                esc_attr($default_value)
            );
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';

            $html .= isset($wrapper[1]) ? $wrapper[1] : '';

            return !$part_of ? '' : $html;

        }

        public function nonce($args = []) {
            // Resolve arguments using $this->args()
            $args = $this->args($args);
        
            $name               = isset($args['id']) ? $args['id'] : 'risbl_admin_field_nonce';
            $action             = isset($args['action']) ? $args['action'] : $name; // Default action same as name
            $part_of            = isset($args['part_of']) ? $args['part_of'] : false; // Display condition
        
            $field_id = 'risbl-admin-field-' . $name;
        
            $this->get_fields[$name] = array(
                'action' => $action,
            );
        
            // Render the nonce field
            $html = sprintf(
                '<input type="hidden" id="%s" name="%s" value="%s" />',
                esc_attr($field_id),
                esc_attr($name),
                esc_attr(wp_create_nonce($action)) // Use explicit action
            );
        
            return !$part_of ? '' : $html;
        }             
        
        public function submit($args = []) {
            // Resolve arguments using $this->args()
            $args = $this->args($args);

            $name               = isset($args['id']) ? $args['id'] : '';
            $label              = isset($args['label']) ? $args['label'] : '';
            $class              = isset($args['class']) ? $args['class'] : 'button button-primary';
            $part_of            = isset($args['part_of']) ? $args['part_of'] : false; // Display condition
            
            $field_id = 'risbl-admin-field-'.$name;

            $this->get_fields[$name] = array(
                'label'         => $label,
            );

            $html = '';
            $html .= isset($wrapper[0]) ? $wrapper[0] : '';
            // Render the input field
            $html .= '<div>' . sprintf('<button type="submit" id="%s" class="%s">%s</button>',
                esc_attr($field_id),
                esc_attr($class),
                esc_attr($label)
            ) . '</div>';
            $html .= isset($wrapper[1]) ? $wrapper[1] : '';

            return !$part_of ? '' : $html;

        }

        public function checkbox($args = []) {
            $args                   = $this->args($args);
            $name                   = isset($args['id']) ? $args['id'] : '';
            $label                  = isset($args['label']) ? $args['label'] : '' ;
            $row_title              = isset($args['row_title']) ? $args['row_title'] : '' ;
            $description            = isset($args['description']) ? $args['description'] : '';
            $default_value          = isset($args['default_value']) ? $args['default_value'] : '';
            $class                  = isset($args['class']) ? $args['class'] : '' ;
            $layout                 = isset($args['layout']) ? $args['layout'] : '';
            $part_of                = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag                = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;

            $field_id               = 'risbl-admin-field-' . $name;
            $wrapper                = $this->field_wrapper();
            $checked                = $default_value ? 'checked' : '';

            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );

            $html = $wrapper[0];
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= ('' !== $row_title) ? $row_title : '';
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';
            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            $html .= sprintf(
                '<input id="%s" type="checkbox" name="%s" class="%s" value="1" %s />',
                esc_attr($field_id),
                esc_attr($name),
                esc_attr($class),
                esc_attr($checked)
            );
            $html .= $this->field_label($field_id, $label);
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';
            $html .= $wrapper[1];

            return !$part_of ? '' : $html;
        }
    
        public function radio($args = []) {
            $args               = $this->args($args);
            $name               = isset($args['id']) ? $args['id'] : '';
            $label              = isset($args['label']) ? $args['label'] : '' ;
            $description        = isset($args['description']) ? $args['description'] : '';
            $default_value      = isset($args['default_value']) ? $args['default_value'] : '';
            $class              = isset($args['class']) ? $args['class'] : '' ;
            $options            = isset($args['options']) ? $args['options'] : array() ;
            $layout             = isset($args['layout']) ? $args['layout'] : '';
            $part_of            = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;


            $field_id           = 'risbl-admin-field-' . $name;
            $wrapper            = $this->field_wrapper();
    
            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );

            $html = $wrapper[0];
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= $this->field_label($field_id, $label);
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';

            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            foreach ($options as $value => $option_label) {
                $checked = ($value == $default_value) ? 'checked' : '';
                $html .= sprintf(
                    '<label><input type="radio" name="%s" class="%s" value="%s" %s /> %s</label>&nbsp;&nbsp;&nbsp;&nbsp;',
                    esc_attr($name),
                    esc_attr($class),
                    esc_attr($value),
                    esc_attr($checked),
                    esc_html($option_label)
                );
            }
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';
            $html .= $wrapper[1];

            return !$part_of ? '' : $html;
        }
    
        public function dropdown($args = []) {
            $args               = $this->args($args);
            $name               = isset($args['id']) ? $args['id'] : '';
            $label              = isset($args['label']) ? $args['label'] : '' ;
            $description        = isset($args['description']) ? $args['description'] : '';
            $options            = isset($args['options']) ? $args['options'] : array() ;
            $class              = isset($args['class']) ? $args['class'] : '' ;
            $default_value      = isset($args['default_value']) ? $args['default_value'] : '';
            $layout             = isset($args['layout']) ? $args['layout'] : '';
            $part_of            = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;

            $field_id           = 'risbl-admin-field-' . $name;
            $wrapper            = $this->field_wrapper();
    
            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );

            $html = $wrapper[0];
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= $this->field_label($field_id, $label);
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';

            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            $html .= sprintf('<select id="%s" name="%s" class="%s">', esc_attr($field_id), esc_attr($name), esc_attr($class));
            foreach ($options as $value => $option_label) {
                $selected = ($value == $default_value) ? 'selected' : '';
                $html .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($value),
                    esc_attr($selected),
                    esc_html($option_label)
                );
            }
            $html .= '</select>';
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';
            $html .= $wrapper[1];

            return !$part_of ? '' : $html;
        }
    
        public function multiselect($args = []) {
            $args               = $this->args($args);
            $name               = isset($args['id']) ? $args['id'] : '';
            $label              = isset($args['label']) ? $args['label'] : '' ;
            $description        = isset($args['description']) ? $args['description'] : '';
            $options            = isset($args['options']) ? $args['options'] : array() ;
            $class              = isset($args['class']) ? $args['class'] : '' ;
            $default_value      = isset($args['default_value']) && is_array($args['default_value']) ? $args['default_value'] : [];
            $layout             = isset($args['layout']) ? $args['layout'] : '';
            $part_of            = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;
            
            $field_id           = 'risbl-admin-field-' . $name;
            $wrapper            = $this->field_wrapper();
    
            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );
    
            $html = $wrapper[0];
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= $this->field_label($field_id, $label);
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';
            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            $html .= sprintf('<select id="%s" name="%s[]" class="%s" multiple>', esc_attr($field_id), esc_attr($name), esc_attr($class));
            foreach ($options as $value => $option_label) {
                $selected = in_array($value, $default_value) ? 'selected' : '';
                $html .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($value),
                    esc_attr($selected),
                    esc_html($option_label)
                );
            }
            $html .= '</select>';
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';
            $html .= $wrapper[1];

            return !$part_of ? '' : $html;
        }

        public function color_picker($args = []) {
            // Resolve arguments using $this->args()
            $args           = $this->args($args);
    
            // Extract values
            $name           = isset($args['id']) ? $args['id'] : '';
            $label          = isset($args['label']) ? $args['label'] : '';
            $description    = isset($args['description']) ? $args['description'] : '';
            $default_value  = isset($args['default_value']) ? $args['default_value'] : '';
            $class          = isset($args['class']) && !empty($args['class']) ? $args['class'] : 'risbl-admin-color-picker';
            $layout         = isset($args['layout']) ? $args['layout'] : '';
            $part_of        = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;

            $field_id       = 'risbl-admin-field-' . $name;
            $wrapper        = $this->field_wrapper();
    
            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );

            // Render the color picker
            $html = $wrapper[0];
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= $this->field_label($field_id, $label);
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';
            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            $html .= sprintf(
                '<input id="%s" type="text" name="%s" class="%s" value="%s" />',
                esc_attr($field_id),
                esc_attr($name),
                esc_attr($class),
                esc_attr($default_value)
            );
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';
            $html .= $wrapper[1];

            return !$part_of ? '' : $html;

        }

        public function media_upload($args = []) {
            // Resolve arguments using $this->args()
            $args = $this->args($args);
    
            // Extract values
            $name           = isset($args['id']) ? $args['id'] : '';
            $label          = isset($args['label']) ? $args['label'] : '';
            $description    = isset($args['description']) ? $args['description'] : '';
            $default_value  = isset($args['default_value']) ? $args['default_value'] : '';
            $class          = isset($args['class']) ? $args['class'] : 'risbl-admin-media-upload';
            $allowed_types  = isset($args['allowed_types']) ? $args['allowed_types'] : ''; // File types (e.g., 'image,video')
            $layout         = isset($args['layout']) ? $args['layout'] : '';
            $part_of        = isset($args['part_of']) ? $args['part_of'] : false; // Display condition

            $col_tag = ('' !== $layout) && ('wp-admin-form-table' === $layout) ? true : false;

            $field_id = 'risbl-admin-field-' . $name;
            $wrapper = $this->field_wrapper();
    
            $this->get_fields[$name] = array(
                'default_value' => $default_value,
                'label'         => $label,
            );

            // Render the media upload field
            $html = $wrapper[0];
            $html .= $col_tag ? $this->field_col_tag('th-open', $layout) : '';
            $html .= $this->field_label($field_id, $label);
            $html .= $col_tag ? $this->field_col_tag('th-close', $layout) : '';
            $html .= $col_tag ? $this->field_col_tag('td-open', $layout) : '';
            $html .= sprintf(
                '<input id="%s" type="hidden" name="%s" class="%s" value="%s" data-allowed-types="%s" />',
                esc_attr($field_id),
                esc_attr($name),
                esc_attr($class),
                esc_attr($default_value),
                esc_attr($allowed_types)
            );
            $html .= sprintf(
                '<button type="button" class="button risbl-admin-media-upload-button" data-target="%s">Upload</button>',
                esc_attr($field_id)
            );
            $html .= sprintf(
                '<div id="%s-preview" class="risbl-admin-media-upload-preview">%s</div>',
                esc_attr($field_id),
                !empty($default_value) ? sprintf('<img src="%s" alt="" style="max-width: 100px;"/>', esc_url($default_value)) : ''
            );
            $html .= !empty($description) ? '<div class="risbl-admin-field-desc">' . esc_html($description) . '</div>' : '';
            $html .= $col_tag ? $this->field_col_tag('td-close', $layout) : '';
            $html .= $wrapper[1];

            return !$part_of ? '' : $html;

        }

    }
    
    // Class ends
    
    // Example usage
    
    /*
    // Instantiate without arguments
    $field = new Risbl_Admin_Field();
    
    // Create a textarea
    echo $field->textarea([
        'name' => 'feedback',
        'default_value' => 'Your feedback here...',
        'placeholder' => 'Write your thoughts...'
    ]);
    
    // Create an input field
    echo $field->input([
        'name' => 'username',
        'type' => 'text',
        'placeholder' => 'Enter your username'
    ]);
    
    // Create a field with shared arguments
    $shared_field = new Risbl_Admin_Field([
        'class' => 'shared-class',
        'placeholder' => 'Shared placeholder'
    ]);
    
    echo $shared_field->textarea([
        'name' => 'comments'
    ]);
    */
    

endif;