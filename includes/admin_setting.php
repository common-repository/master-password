<?php
/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
require_once( MAPA_INCLUDES_PATH. 'class.settings-api.php'  );
if ( !class_exists('Mapa_Settings_API' ) ):
class Mapa_Settings_API {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;
		
		register_activation_hook( 'MAPA_FILE', array($this, 'register_activation') );
		register_deactivation_hook( 'MAPA_FILE', array($this, 'register_deactivation') );
		
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }
	
	function register_activation() {
		update_option('mapa_is_plugin_notice_disp' , 1);
		$settings_fiels = $this->get_settings_fields();
		
		foreach($settings_fiels as $section=>$fields ) {
			$default_settings_val_arr = array();
			foreach( $fields as $field) {
				if( !empty($field['default'] ) ) {
					$default_settings_val_arr[ $field['name'] ] =  $field['default'];
				}
			}
			
			if( !empty( $default_settings_val_arr ) ) {
				update_option( $section,  $default_settings_val_arr);
			}
		}
	}
	
	function register_deactivation() {
		$sections = $this->get_settings_sections();
		foreach( $sections as $section) {
			delete_option( $section['id'] );
		}
		
		
	}
		
		

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( esc_html__('Master Password', MAPA_TEXT_DOMAIN), esc_html__('Master Password', MAPA_TEXT_DOMAIN), 'administrator', 'master-password', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'mapa_settings',
                'title' => __( 'Master Password Settings', MAPA_TEXT_DOMAIN )
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'mapa_settings' => array(              
                array(
                    'name'  => 'mapa_enable_master_password',
                    'label' => __( 'Enable Master Password', MAPA_TEXT_DOMAIN ),
                    'desc'  => __( 'Yes', MAPA_TEXT_DOMAIN ),
                    'type'  => 'checkbox',
					'default' => 'on' 
                ),
				array(
                    'name'  => 'mapa_master_password_is_admin_password',
                    'label' => __( 'Will Admin Password works as master password?', MAPA_TEXT_DOMAIN ),
                    'desc'  => __( 'Yes', MAPA_TEXT_DOMAIN ),
                    'type'  => 'checkbox',
					'default' => 'on' 
                ),
                array(
                    'name'              => 'mapa_master_password',
                    'label'             => __( 'Master Password', MAPA_TEXT_DOMAIN ),
                    'desc'              => __( 'Please set master password here', MAPA_TEXT_DOMAIN ),
                    'type'              => 'text',
                    'default'           => '12345',
                ),
                array(
                    'name'  => 'mapa_can_admin_user_access',
                    'label' => __( 'Can Admin Role User Use Master Password?', MAPA_TEXT_DOMAIN ),
                    'desc'  => __( 'Yes', MAPA_TEXT_DOMAIN ),
                    'type'  => 'checkbox'
                )
            ),
            
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

       // $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;
new Mapa_Settings_API();

$mapa_is_plugin_notice_disp = get_option('mapa_is_plugin_notice_disp' , 1);
if($mapa_is_plugin_notice_disp == 0) {
	add_action( 'admin_notices', 'mapa_plugin_activation_notice' ); 
}


 
function mapa_plugin_activation_notice(){
	global $pagenow;	
	
	if( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == 'master-password' )
	{
		return;	
	}
    ?>
    <div class="updated notice is-dismissible">
      <p><?php esc_html_e('For master password setting, please configure it on ', MAPA_TEXT_DOMAIN);?><a href="<?php echo  admin_url( 'options-general.php?page=master-password' );?>"><?php esc_html_e('setting page', MAPA_TEXT_DOMAIN);?></a>
      .</p>
    </div>
    <?php
	update_option('mapa_is_plugin_notice_disp' , 1);
}


function mapa_enqueue_admin_script($hook) {
	// Load only on ?page=mypluginname
	if($hook != 'settings_page_master-password') {
			return;
	}
	
	
	wp_enqueue_script( 'mapa-custom-js', plugins_url('js/custom.js', MAPA_FILE) );
}
add_action( 'admin_enqueue_scripts', 'mapa_enqueue_admin_script' );