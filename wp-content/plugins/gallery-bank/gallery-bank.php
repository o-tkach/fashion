<?php
/*
Plugin Name: Gallery Bank Lite Edition
Plugin URI: http://tech-banker.com
Description: Gallery Bank is an easy to use Responsive WordPress Gallery Plugin for photos, videos, galleries and albums.
Author: Tech Banker
Version: 3.1.27
Author URI: http://tech-banker.com
License: GPLv3 or later
*/
if(!defined("ABSPATH")) exit; //exit if accessed directly
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	 Define	 Constants	///////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!defined("GALLERY_FILE")) define("GALLERY_FILE","gallery-bank/gallery-bank.php");
if (!defined("GALLERY_MAIN_DIR")) define("GALLERY_MAIN_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank");
if (!defined("GALLERY_MAIN_UPLOAD_DIR")) define("GALLERY_MAIN_UPLOAD_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank/gallery-uploads/");
if (!defined("GALLERY_MAIN_THUMB_DIR")) define("GALLERY_MAIN_THUMB_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank/thumbs/");
if (!defined("GALLERY_MAIN_ALB_THUMB_DIR")) define("GALLERY_MAIN_ALB_THUMB_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank/album-thumbs/");
if (!defined("GALLERY_BK_PLUGIN_DIRNAME")) define("GALLERY_BK_PLUGIN_DIRNAME", plugin_basename(dirname(__FILE__)));
if (!defined("GALLERY_BK_PLUGIN_DIR")) define("GALLERY_BK_PLUGIN_DIR",	plugin_dir_path( __FILE__ ));
if (!defined("GALLERY_BK_THUMB_URL")) define("GALLERY_BK_THUMB_URL", content_url()."/gallery-bank/gallery-uploads/");
if (!defined("GALLERY_BK_THUMB_SMALL_URL")) define("GALLERY_BK_THUMB_SMALL_URL", content_url()."/gallery-bank/thumbs/");
if (!defined("GALLERY_BK_ALBUM_THUMB_URL")) define("GALLERY_BK_ALBUM_THUMB_URL", content_url()."/gallery-bank/album-thumbs/");
if (!defined("GALLERY_BK_PLUGIN_BASENAME")) define("GALLERY_BK_PLUGIN_BASENAME", plugin_basename(__FILE__));
if (!defined("gallery_bank")) define("gallery_bank", "gallery-bank");
if (!defined("tech_bank")) define("tech_bank", "tech-banker");

if (!is_dir(GALLERY_MAIN_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_DIR);
}
if (!is_dir(GALLERY_MAIN_UPLOAD_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_UPLOAD_DIR);
}
if (!is_dir(GALLERY_MAIN_THUMB_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_THUMB_DIR);
}
if (!is_dir(GALLERY_MAIN_ALB_THUMB_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_ALB_THUMB_DIR);
}
/*************************************************************************************/
if (file_exists(GALLERY_BK_PLUGIN_DIR . "/lib/gallery-bank-class.php"))
{
	require_once(GALLERY_BK_PLUGIN_DIR . "/lib/gallery-bank-class.php");
}
/*************************************************************************************/
if(!function_exists("plugin_install_script_for_gallery_bank"))
{
	function plugin_install_script_for_gallery_bank()
	{
		global $wpdb,$current_user;
		if (!is_user_logged_in())
		{
			return;
		}
		if(is_super_admin())
		{
			$gb_role = "administrator";
		}
		else
		{
			$gb_role = $wpdb->prefix . "capabilities";
			$current_user->role = array_keys($current_user->$gb_role);
			$gb_role = $current_user->role[0];
		}
		if (is_multisite())
		{
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach($blog_ids as $blog_id)
			{
				switch_to_blog($blog_id);
				if(file_exists(GALLERY_BK_PLUGIN_DIR. "/lib/install-script.php"))
				{
					include GALLERY_BK_PLUGIN_DIR . "/lib/install-script.php";
				}
				restore_current_blog();
			}
		}
		else
		{
			if(file_exists(GALLERY_BK_PLUGIN_DIR. "/lib/install-script.php"))
			{
				include_once GALLERY_BK_PLUGIN_DIR . "/lib/install-script.php";
			}
		}
	}
}

/*************************************************************************************/
if(!function_exists("plugin_uninstall_script_for_gallery_bank"))
{
	function plugin_uninstall_script_for_gallery_bank()
	{
		wp_clear_scheduled_hook("gallery_bank_auto_update");
	}
}
/*************************************************************************************/
if(!function_exists("gallery_bank_plugin_load_text_domain"))
{
	function gallery_bank_plugin_load_text_domain()
	{
		if (function_exists("load_plugin_textdomain"))
		{
			load_plugin_textdomain(gallery_bank, false, GALLERY_BK_PLUGIN_DIRNAME . "/lang");
		}
	}
}
/*************************************************************************************/
if(!function_exists("add_gallery_bank_icon"))
{
	function add_gallery_bank_icon($meta = TRUE)
	{
		global $wp_admin_bar,$wpdb,$current_user;
		if (!is_user_logged_in())
		{
			return;
		}
		if(is_super_admin())
		{
			$gb_role = "administrator";
		}
		else
		{
			$gb_role = $wpdb->prefix . "capabilities";
			$current_user->role = array_keys($current_user->$gb_role);
			$gb_role = $current_user->role[0];
		}
		switch ($gb_role)
		{
			case "administrator":
				$wp_admin_bar->add_menu(array(
				"id" => "gallery_bank_links",
				"title" => __("<img src=\"" . plugins_url("/assets/images/icon.png",__FILE__)."\" width=\"25\"
				height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Gallery Bank"),
				"href" => __(site_url() . "/wp-admin/admin.php?page=gallery_bank"),
			));

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_dashboard_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank",
			"title" => __("Dashboard", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "shortcode_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_shortcode",
			"title" => __("Short-Codes", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "sorting_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_album_sorting",
			"title" => __("Album Sorting", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "global_settings_links",
			"href" => site_url() . "/wp-admin/admin.php?page=global_settings",
			"title" => __("Global Settings", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_auto_update_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_auto_plugin_update",
			"title" => __("Plugin Updates", gallery_bank))
			);
			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_feature_request_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_feature_request",
			"title" => __("Feature Requests", gallery_bank))
			);
			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "system_status_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_system_status",
			"title" => __("System Status", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_recommended_plugins_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_recommended_plugins",
			"title" => __("Recommendations", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "purchase_pro_version_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_purchase",
			"title" => __("Premium Editions", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_other_services_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_other_services",
			"title" => __("Our Other Services", gallery_bank))
			);
			break;
			case "editor":
				$wp_admin_bar->add_menu(array(
			"id" => "gallery_bank_links",
			"title" => __("<img src=\"" . plugins_url("/assets/images/icon.png",__FILE__)."\" width=\"25\"
			height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Gallery Bank"),
			"href" => __(site_url() . "/wp-admin/admin.php?page=gallery_bank"),
			));

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_dashboard_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank",
			"title" => __("Dashboard", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "shortcode_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_shortcode",
			"title" => __("Short-Codes", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "sorting_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_album_sorting",
			"title" => __("Album Sorting", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "global_settings_links",
			"href" => site_url() . "/wp-admin/admin.php?page=global_settings",
			"title" => __("Global Settings", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_auto_update_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_auto_plugin_update",
			"title" => __("Plugin Updates", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_feature_request_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_feature_request",
			"title" => __("Feature Requests", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "system_status_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_system_status",
			"title" => __("System Status", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_recommended_plugins_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_recommended_plugins",
			"title" => __("Recommendations", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "purchase_pro_version_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_purchase",
			"title" => __("Premium Editions", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_other_services_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_other_services",
			"title" => __("Our Other Services", gallery_bank))
			);
			break;
			case "author":
			$wp_admin_bar->add_menu(array(
			"id" => "gallery_bank_links",
			"title" => __("<img src=\"" . plugins_url("/assets/images/icon.png",__FILE__)."\" width=\"25\"
			height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Gallery Bank"),
			"href" => __(site_url() . "/wp-admin/admin.php?page=gallery_bank"),
			));

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_dashboard_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank",
			"title" => __("Dashboard", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "shortcode_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_shortcode",
			"title" => __("Short-Codes", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "sorting_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_album_sorting",
			"title" => __("Album Sorting", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "global_settings_links",
			"href" => site_url() . "/wp-admin/admin.php?page=global_settings",
			"title" => __("Global Settings", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_auto_update_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_auto_plugin_update",
			"title" => __("Plugin Updates", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_feature_request_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_feature_request",
			"title" => __("Feature Requests", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "system_status_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_system_status",
			"title" => __("System Status", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_recommended_plugins_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_recommended_plugins",
			"title" => __("Recommendations", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "purchase_pro_version_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_purchase",
			"title" => __("Premium Editions", gallery_bank))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_other_services_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_other_services",
			"title" => __("Our Other Services", gallery_bank))
			);
			break;
		}
	}
}
if(!function_exists("gallery_bank_custom_plugin_row"))
{
	function gallery_bank_custom_plugin_row($links,$file)
	{
		if ($file == GALLERY_BK_PLUGIN_BASENAME)
		{
			$gallery_bank_row_meta = array(
					"docs"	=> "<a href='".esc_url( apply_filters("gallery_bank_docs_url","http://tech-banker.com/products/wp-gallery-bank/knowledge-base/"))."' title='".esc_attr(__( "View Gallery Bank Documentation",gallery_bank))."'>".__("Docs",gallery_bank)."</a>",
					"gopremium" => "<a href='" .esc_url( apply_filters("gallery_bank_premium_editions_url", "http://tech-banker.com/products/wp-gallery-bank/pricing/"))."' title='".esc_attr(__( "View Gallery Bank Premium Editions",gallery_bank))."'>".__("Go for Premium!",gallery_bank)."</a>",
			);
			return array_merge($links,$gallery_bank_row_meta);
		}
		return (array)$links;
	}
}

$version = get_option("gallery-bank-pro-edition");
if($version != "")
{
	add_action("admin_init", "plugin_install_script_for_gallery_bank");
}
//--------------------------------------------------------------------------------------------------------------//
// CODE FOR PLUGIN UPDATE MESSAGE
//--------------------------------------------------------------------------------------------------------------//
if(!function_exists("gallery_bank_plugin_update_message"))
{
	function gallery_bank_plugin_update_message($args)
	{
		$response = wp_remote_get( 'https://plugins.svn.wordpress.org/gallery-bank/trunk/readme.txt' );
		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) )
		{
			// Output Upgrade Notice
			$matches= null;
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote($args['Version']) . '\s*=|$)~Uis';
			$upgrade_notice = '';
			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));
				$upgrade_notice .= '<div class="gallery_plugin_message">';
				foreach ( $changelog as $index => $line ) {
					$upgrade_notice .= "<p>".$line."</p>";
				}
				$upgrade_notice .= '</div> ';
				echo $upgrade_notice;
			}
		}
	}
}

//--------------------------------------------------------------------------------------------------------------//
// CODE FOR PLUGIN AUTOMATIC UPDATE
//--------------------------------------------------------------------------------------------------------------//
$is_option_auto_update = get_option("gallery-bank-automatic_update");

if($is_option_auto_update == "" || $is_option_auto_update == "1")
{
	if (!wp_next_scheduled("gallery_bank_auto_update"))
	{
		wp_schedule_event(time(), "daily", "gallery_bank_auto_update");
	}
	add_action("gallery_bank_auto_update", "plugin_autoUpdate");
}
else
{
	wp_clear_scheduled_hook("gallery_bank_auto_update");
}
if(!function_exists("plugin_autoUpdate"))
{
	function plugin_autoUpdate()
	{
		try
		{
			require_once(ABSPATH . "wp-admin/includes/class-wp-upgrader.php");
			require_once(ABSPATH . "wp-admin/includes/misc.php");
			define("FS_METHOD", "direct");
			require_once(ABSPATH . "wp-includes/update.php");
			require_once(ABSPATH . "wp-admin/includes/file.php");
			wp_update_plugins();
			ob_start();
			$plugin_upgrader = new Plugin_Upgrader();
			$plugin_upgrader->upgrade("gallery-bank/gallery-bank.php");
			$output = @ob_get_contents();
			@ob_end_clean();
		}
		catch(Exception $e)
		{

		}
	}
}

add_filter("plugin_row_meta","gallery_bank_custom_plugin_row", 10, 2);
add_action("admin_bar_menu", "add_gallery_bank_icon", 100);
add_action("plugins_loaded", "gallery_bank_plugin_load_text_domain");
add_action("in_plugin_update_message-".GALLERY_FILE,"gallery_bank_plugin_update_message");
register_activation_hook(__FILE__, "plugin_install_script_for_gallery_bank");
register_uninstall_hook(__FILE__, "plugin_uninstall_script_for_gallery_bank");
/*************************************************************************************/
?>
