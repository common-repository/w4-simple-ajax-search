<?php
class w4_sas_setting_page
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_w4_sas_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'w4_sas_op_page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_w4_sas_plugin_page()
    {
        // This page will be under "Settings"
        
        add_options_page(
            'W4 SAS Settings', 
            'W4 Simple Ajax Search Settings', 
            'manage_options', 
            'w4-sas-admin', 
            array( $this, 'create_w4_sas_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_w4_sas_admin_page()
    {
        // Set class property
        $this->options = get_option( 'w4_sas_option' );
        ?>
        <div class="wrap">
            <h1>W4 Simple Ajax Search Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'w4_sas_option_name' );
                do_settings_sections( 'w4-sas-setting-admin' );
                submit_button();
            ?>
            </form>
            
            
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function w4_sas_op_page_init()
    {        
        register_setting(
            'w4_sas_option_name', // Option group
            'w4_sas_option', // Option name
            array( $this, 'w4_sas_sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'General Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'w4-sas-setting-admin' // Page
        );  

        add_settings_field(
            'placholder', // ID
            'Input field placeholder', // Title 
            array( $this, 'w4_sas_placholder_callback' ), // Callback
            'w4-sas-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'no_results_text', 
            'No results text', 
            array( $this, 'w4_sas_no_results_text_callback' ), 
            'w4-sas-setting-admin', 
            'setting_section_id'
        );    
        
        
         add_settings_field(
            'post_types', 
            'Post types to search in', 
            array( $this, 'w4_sas_post_types_callback' ), 
            'w4-sas-setting-admin', 
            'setting_section_id'
        ); 
        
       
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function w4_sas_sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['placholder'] ) )
            $new_input['placholder'] = sanitize_text_field( $input['placholder'] );

        if( isset( $input['no_results_text'] ) )
            $new_input['no_results_text'] = sanitize_text_field( $input['no_results_text'] );
        
        if( isset( $input['post_types'] ) )
            
            $chkboxArray = $input['post_types'];
        foreach($chkboxArray as $kay=>$value){
            $chkboxArray[$kay] = sanitize_text_field($value);
        }
            
            $new_input['post_types']  =  $chkboxArray;
            
           
    

 
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        //print '----';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function w4_sas_placholder_callback()
    {
        
        $placeholder='';
        $value;
        if(!isset( $this->options['placholder'] )||$this->options['placholder']==''){
            $placeholder='Default: Type search term here';
        }
        if(isset( $this->options['placholder'] )){
           $value = esc_attr( $this->options['placholder']);
        }
           
        
        
        
        printf(
            '<input type="text" id="placholder" placeholder="%s" name="w4_sas_option[placholder]" value="%s" />',
            $placeholder,
            $value
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function w4_sas_no_results_text_callback()
    {
        $placeholder='';
        $value;
        if(!isset( $this->options['no_results_text'] )||$this->options['no_results_text']==''){
            $placeholder='Default: No results';
        }
        if(isset( $this->options['no_results_text'] )){
           $value = esc_attr( $this->options['no_results_text']);
        }
           
        printf(
            '<input type="text" id="no_results_text" placeholder="%s" name="w4_sas_option[no_results_text]" value="%s" />',$placeholder,$value
        );
    }  
    
    public function w4_sas_post_types_callback()
    {?>
<?php  
    $args = array('public'=>true) ;
     
     $post_types = get_post_types( $args); 
         
        
        $op=get_option('w4_sas_option');
       
        $value = $op['post_types'];
       
         $html ='<ul>';
        foreach ( $post_types as  $key=>$post_type){
            if($value){
            $checked = in_array($post_type, $value) ? 'checked' : '';
            }
    $post_type_obj = get_post_type_object( $post_type );
   $post_label = $post_type_obj->labels->name;
    $html .= '<li><input type="checkbox" id="ckbx'.$key.'" name="w4_sas_option[post_types][]" value="'.$post_type.'" '. $checked .'/>';      
 $html .= '<label for="ckbx'.$key.'">'. $post_label.'</label></li>';

        }
         $html .='</ul>';
      echo $html;  
   

?>

<?php
     
        
    }
}

if( is_admin() ){
    
  
    $my_settings_page = new w4_sas_setting_page();
 $op=get_option('w4_sas_option');
$value = $op['post_types'];

        
       
       if(!$value){$op['post_types'] = array('post');}
      

          update_option('w4_sas_option',$op);
}

function my_enqueue($hook) {
   
    if( $hook == 'settings_page_w4-sas-admin' )
             
    wp_register_style('options_page_style', plugins_url('includes/options_style.css',__FILE__));
    wp_enqueue_style('options_page_style');
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );