<?php /*
  Plugin Name: WPJB Custom Importer
  Description: Import Jobs from custom xml to wp job board plugin.
  Version: 1.0
 */
global $wpdb;
$siteurl = get_bloginfo('url');
define('JCI_PLUGIN_URL', WP_PLUGIN_URL.'/wpjb-custom-import');

/* Load all functions */
require_once ( 'admin/index.php' );

add_action('admin_menu','jci_backend_menu');

function jci_backend_menu() {
	add_menu_page('WPJB Custom Import','WPJB Custom Import','manage_options','jci_import','jci_import');	
}

// this hook will cause our creation function to run when the plugin is activated
register_activation_hook( __FILE__, 'jci_plugin_install' );

function jci_plugin_install() {
	global $wpdb; // do NOT forget this global

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active('wpjobboard/index.php') ) {
		}	
	 else {
		deactivate_plugins( 'wpjb-custom-import/wpjb-custom-import.php', false );
		wp_die( "<strong>WPJB Custom Importer</strong> requires <strong>WPJobBoard Plugin</strong> and has been deactivated! Please install/activate <strong>WPJobBoard Plugin</strong> and try again.<br /><br />Back to the WordPress <a href='".get_admin_url(null, 'plugins.php')."'>Plugins page</a>.");
		}
 		update_option('disable_jci_admin_message',1);

}

add_action( 'admin_init', 'jci_check_em_plugin' );
function jci_check_em_plugin() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active('wpjobboard/index.php') ) {
		}	
	 else {
			deactivate_plugins( 'wpjb-custom-import/wpjb-custom-import.php', false );
			add_action ( 'admin_notices', 'jci_admin_notices', 100 );
		}
}
function jci_admin_notices() {

    echo "<div class='error'><p>WPJB Custom Importer Plugin has been deactivated since it requires WPJobBoard Plugin. Please activate WPJobBoard Plugin and try again.</p></div>";
}

function jci_admin_messages() {
	//If we're editing the events page show hello to new user
	$dismiss_link_joiner = ( count($_GET) > 0 ) ? '&amp;':'?';
	
	if( current_user_can('activate_plugins') ){
		//New User Intro
		if (isset ( $_GET ['disable_jci_admin_message'] ) && $_GET ['disable_jci_admin_message'] == 'true'){
			// Disable Hello to new user if requested
			update_option('disable_jci_admin_message',0);
		}elseif ( get_option ( 'disable_jci_admin_message' ) ) {
			
			$advice = sprintf( __("<p>WPJB Custom Importer is ready to go! Check out the <a href='%s'>Import Page</a>. <a href='%s' title='Don not show this advice again'>Dismiss</a></p>", 'jci'), jci_get_url('import'),  $_SERVER['REQUEST_URI'].$dismiss_link_joiner.'disable_jci_admin_message=true');
			?>
			<div id="message" class="updated">
				<?php echo $advice; ?>
			</div>
			<?php
		}
	}
}

add_action ( 'admin_notices', 'jci_admin_messages', 100 );

// Add settings link on plugin page
function jci_settings_link($links) { 
  $settings_link = '<a href="admin.php?page=jci_import">Import</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'jci_settings_link' );

// Add Scripts and css in backend.
add_action( 'admin_enqueue_scripts', 'jci_enqueue_scripts' );
function jci_enqueue_scripts( $hook_suffix ) {
	// first check that $hook_suffix is appropriate for your admin page
	wp_enqueue_script( 'jci-settings-scripts', JCI_PLUGIN_URL . '/admin/js/admin-scripts.js', array( 'jquery' ), false, true );
	wp_enqueue_style( 'jci-settings-styles', JCI_PLUGIN_URL . '/admin/css/admin.css', array(), '', 'all' );
	wp_enqueue_media();
}

// Allow permit for xml upload.
add_filter('upload_mimes', 'jci_upload_xml');
function jci_upload_xml($mimes) {
    $mimes = array_merge($mimes, array('xml' => 'application/xml'));
    return $mimes;
}
?>
