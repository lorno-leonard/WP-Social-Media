<?php
/*
Plugin Name: Social Media
Plugin URI:
Description: Wordpress Plugin for Social Media
Version: 1.0
Author: 
Author 
*/

define('SM_DIRECTORY', plugin_basename(dirname(__FILE__)));
define('SM_URLPATH', trailingslashit( plugins_url( '', __FILE__ ) ) );

include 'functions.php';

if(is_admin()) {

	// Social Media - Create Tables
	function nova_sm_database() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_social_media = "nova_social_media";
		$table_social_media_options = "nova_social_media_options";

		$sql = "
		CREATE TABLE IF NOT EXISTS $table_social_media (
			id int(10) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			link_address varchar(255) NOT NULL,
			visibility enum('Y','N') NOT NULL,
			icon_image varchar(255) NOT NULL,
			created datetime NOT NULL,
			updated datetime NULL,
			trash tinyint(1) NOT NULL DEFAULT '0',
			UNIQUE KEY id (id)
		);";	  
		dbDelta( $sql );

		$sql = "
		CREATE TABLE IF NOT EXISTS $table_social_media_options (
			id int(10) NOT NULL AUTO_INCREMENT,
			text text NOT NULL,
			color varchar(64) NOT NULL,
			size varchar(64) NOT NULL,
			bar_height varchar(64) NOT NULL,
			bar_color varchar(64) NOT NULL,
			UNIQUE KEY id (id)
		);";	  
		dbDelta( $sql );

		$wpdb->query("INSERT INTO $table_social_media_options (id, text, color, size, bar_height, bar_color) VALUES (NULL, 'We''re Social!', '#999999', '16', '31', '#37373B')");
	}
	register_activation_hook( __FILE__, 'nova_sm_database' );

	// Social Media - Initialize Admin Menu
	function nova_sm_admin_menu() {
		add_menu_page('Social Media', 'Social Media', 'administrator', SM_DIRECTORY . '/social_media.php');
		add_submenu_page( SM_DIRECTORY . '/social_media.php' , 'Social Media', 'Social Media', 'administrator', SM_DIRECTORY . '/social_media.php');
		add_action('admin_head-' . SM_DIRECTORY . '/social_media.php', 'nova_sm_register_head');

		add_submenu_page( SM_DIRECTORY . '/social_media.php' , 'Add Social Media', 'Add Social Media', 'administrator', SM_DIRECTORY . '/add_social_media.php');
		add_action('admin_head-' . SM_DIRECTORY . '/add_social_media.php', 'nova_sm_register_head');

		add_submenu_page( SM_DIRECTORY . '/social_media.php' , 'Social Media Options', 'Social Media Options', 'administrator', SM_DIRECTORY . '/social_media_options.php');
		add_action('admin_head-' . SM_DIRECTORY . '/social_media_options.php', 'nova_sm_register_head');

		add_submenu_page ( NULL, 'Edit Social Media', 'Edit Social Media',  'administrator',  SM_DIRECTORY . '/edit_social_media.php');
		add_action('admin_head-' . SM_DIRECTORY . '/edit_social_media.php', 'nova_sm_register_head');
	}
	add_action('admin_menu', 'nova_sm_admin_menu');

	// Social Media - Register Styles and Scripts
	function nova_sm_register_head() {
		wp_enqueue_style('nova_sm_css', plugins_url().'/epik_social_media/css/style.css');
		wp_enqueue_style('nova_sm_switch', plugins_url().'/epik_social_media/css/nova-switch.css');
		wp_enqueue_style('nova_sm_bootstrap', plugins_url().'/epik_social_media/css/nova-btn-dropdown.css');
		wp_enqueue_style('wp-color-picker');

		wp_enqueue_script('nova_sm_js', plugins_url().'/epik_social_media/js/nova-js.js');
		wp_enqueue_script('nova_sm_quick_edit', plugins_url().'/epik_social_media/js/nova_sm_quick_edit.js');
		wp_enqueue_script('nova_sm_filter', plugins_url().'/epik_social_media/js/nova_sm_filter.js');
		wp_enqueue_script('wp-color-picker');
	}

	// Social Media - Fix wp_redirect() problems with headers
	function nova_sm_output_buffer() {
		ob_start();
	}
	add_action('init', 'nova_sm_output_buffer');
}
else {
	// Social Media - Bar
	function nova_sm_social_media_bar() {
		global $wpdb;
		$upload_dir = wp_upload_dir();

		wp_enqueue_style('nova_sm_social_media_css', plugins_url().'/epik_social_media/css/social_media.css');

		wp_enqueue_script('nova_sm_jquery_cookie', plugins_url().'/epik_social_media/js/jquery.cookie.js');
		wp_enqueue_script('nova_sm_social_media_js', plugins_url().'/epik_social_media/js/script_social.js');

		require( ABSPATH . WPINC . '/pluggable.php' );

		if (is_admin_bar_showing()) {
			wp_enqueue_script('nova_sm_admin_js', plugins_url().'/epik_social_media/js/admin_js.js');
		}

		$options = $wpdb->get_row("SELECT * FROM nova_social_media_options");
		if($options) {
			$text		= $options->text;
			$color 		= $options->color;
			$size 		= $options->size;
			$bar_height = $options->bar_height;
			$bar_color	= $options->bar_color;

			$bar_height_style = $bar_height ? "height: ".$bar_height : '';
			$bar_color_style = $bar_color ? "background: ".$bar_color : '';
		}

		$results = $wpdb->get_results("SELECT * FROM nova_social_media WHERE visibility = 'Y' AND trash = FALSE");
		$social_icons = '';
		if ($results) {
			foreach($results as $v) {
				$name         = $v->name;
				$link_address = $v->link_address;
				$icon_image   = $v->icon_image;

				$social_icons .= '<a target="_blank" title="' . $name . '" href="' . $link_address . '"><img style="max-height:25px; margin: 0 5px;" alt="' . $name . '" src="' . $upload_dir['baseurl'] . $icon_image .'"></a>';
			}
		}

		echo '
			<div class="social-wrap" style="' . $bar_color_style . '">
				<div class="social-bar">
					<b class="socialbar-handler" style="color: ' . $color . '; background: ' . $bar_color . ';">' . $text . '</b>        
					<div class="social-bar-inner" style="display: none; ' . $bar_height_style . '">
						<span>' . $social_icons . '</span>
					</div>
				</div>
			</div>
		';
	}
	
	if( !in_array($GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' )) ) {
		add_action('wp_footer', 'nova_sm_social_media_bar');
	}
}