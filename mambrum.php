<?php
/*
	Plugin Name: Mambrum
	Plugin URI:
	Description: Adds your Mambrum configuration to your site
	Tags: mambrum, configuration
	Author: Mambrum Inc
	Author URI: https://www.mambrum.com
	Version: 1.2
	Requires PHP: 5.2

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version
	2 of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/

	Copyright 2017 Monzilla Media. All rights reserved.
*/

if (!defined('ABSPATH')) die();
$mambrum_path    = plugin_basename(__FILE__);

function mambrum_init101()
{
	load_plugin_textdomain('mambrum', false, dirname(plugin_basename(__FILE__)) .'/languages/');
}
add_action('plugins_loaded', 'mambrum_init101');



function mambrum_start()
{

	$options = get_option('mambrum_options', array());
	extract($options);

	if ((!is_user_logged_in() || !current_user_can('administrator') || (current_user_can('administrator'))))
	{
		?>
		<script>
			(function(g,c,d,e,h,f,a,b)
			{a=c.createElement(d);
			a.async=1;a.src=e;
			a.setAttribute("data-mb-cid",f);
			a.setAttribute("data-mb-autoinit",'1');
			b=c.getElementsByTagName(d)[0];
			b.parentNode.insertBefore(a,b)})
			(window,document,"script","https://cdn.mambrum.com/m/mb.js","mb",<?php echo '"'.$options['mambrum_id'].'"';?>);
        </script>
	<?php

	}

}



// include code in header or footer
add_action('admin_head', 'mambrum_start');
add_action('wp_head', 'mambrum_start');
/*if (isset($mambrum_options['mambrum_location']) && $mambrum_options['mambrum_location'] == 'header') {
	if (isset($mambrum_options['admin_area']) && $mambrum_options['admin_area']) {
		add_action('admin_head', 'mambrum_start');
	}
	add_action('wp_head', 'mambrum_start');
} else {
	if (isset($mambrum_options['admin_area']) && $mambrum_options['admin_area']) {
		add_action('admin_footer', 'mambrum_start');
	}
	add_action('wp_footer', 'mambrum_start');
}*/



// display settings link on plugin page
function mambrum_plugin_action_links($links, $file)
{
	global $mambrum_path;
	if ($file == $mambrum_path) {
		$mambrum_links = '<a href="' . get_admin_url() . 'options-general.php?page=' . $mambrum_path . '">' . esc_html__('Settings', 'mambrum') .'</a>';
		array_unshift($links, $mambrum_links);
	}
	return $links;
}
add_filter ('plugin_action_links', 'mambrum_plugin_action_links', 10, 2);

// rate plugin link
function add_mambrum_links($links, $file)
{

	if ($file == plugin_basename(__FILE__))
	{
		$href  = 'https://wordpress.org/support/plugin/mambrum/reviews/?rate=5#new-post';
		$title = esc_html__('Give us a 5-star rating at WordPress.org', 'mambrum');
		$text  = esc_html__('Rate this plugin', 'mambrum') .'&nbsp;&raquo;';

		$links[] = '<a target="_blank" href="'. $href .'" title="'. $title .'">'. $text .'</a>';

	}
	return $links;

}
add_filter('plugin_row_meta', 'add_mambrum_links', 10, 2);

// delete plugin settings
function mambrum_delete_plugin_options()
{
	delete_option('mambrum_options');
}
if ($mambrum_options['default_options'] == 1) {
	register_uninstall_hook (__FILE__, 'mambrum_delete_plugin_options');
}

// define default settings
function mambrum_add_defaults()
{
	$tmp = get_option('mambrum_options');
	if(($tmp['default_options'] == '1') || (!is_array($tmp))) {
		$values = array(
			'client_id'          => 'MB-XXXXX-X',
			'mambrum_location'    => 'header',
			'version_alert'   => 0,
			'default_options' => 0,
		);
		update_option('mambrum_options', $values);
	}
}
register_activation_hook (__FILE__, 'mambrum_add_defaults');

// whitelist settings
function mambrum_init()
 {
	register_setting('mambrum_plugin_options', 'mambrum_options', 'mambrum_validate_options');
}
add_action ('admin_init', 'mambrum_init');

// sanitize and validate input
function mambrum_validate_options($input)
{

	global $mambrum_location;

	$input['mambrum_id'] = wp_filter_nohtml_kses($input['mambrum_id']);

	if (!isset($input['mambrum_location'])) $input['mambrum_location'] = null;
	if (!array_key_exists($input['mambrum_location'], $mambrum_location)) $input['mambrum_location'] = null;

	if (!isset($input['version_alert'])) $input['version_alert'] = null;
	$input['version_alert'] = ($input['version_alert'] == 1 ? 1 : 0);

	if (!isset($input['default_options'])) $input['default_options'] = null;
	$input['default_options'] = ($input['default_options'] == 1 ? 1 : 0);

	if (!isset($input['mambrum_enable'])) $input['mambrum_enable'] = null;
	$input['mambrum_enable'] = ($input['mambrum_enable'] == 1 ? 1 : 0);

	if (!isset($input['mambrum_universal'])) $input['mambrum_universal'] = null;
	$input['mambrum_universal'] = ($input['mambrum_universal'] == 1 ? 1 : 0);

	if (!isset($input['mambrum_display_ads'])) $input['mambrum_display_ads'] = null;
	$input['mambrum_display_ads'] = ($input['mambrum_display_ads'] == 1 ? 1 : 0);

	if (!isset($input['link_attr'])) $input['link_attr'] = null;
	$input['link_attr'] = ($input['link_attr'] == 1 ? 1 : 0);

	if (!isset($input['mambrum_anonymize'])) $input['mambrum_anonymize'] = null;
	$input['mambrum_anonymize'] = ($input['mambrum_anonymize'] == 1 ? 1 : 0);

	if (!isset($input['mambrum_force_ssl'])) $input['mambrum_force_ssl'] = null;
	$input['mambrum_force_ssl'] = ($input['mambrum_force_ssl'] == 1 ? 1 : 0);

	if (!isset($input['admin_area'])) $input['admin_area'] = null;
	$input['admin_area'] = ($input['admin_area'] == 1 ? 1 : 0);

	if (!isset($input['disable_admin'])) $input['disable_admin'] = null;
	$input['disable_admin'] = ($input['disable_admin'] == 1 ? 1 : 0);

	if (!isset($input['mambrum_custom_loc'])) $input['mambrum_custom_loc'] = null;
	$input['mambrum_custom_loc'] = ($input['mambrum_custom_loc'] == 1 ? 1 : 0);

	if (isset($input['tracker_object'])) $input['tracker_object'] = stripslashes(trim($input['tracker_object']));

	if (isset($input['mambrum_custom_code'])) $input['mambrum_custom_code'] = stripslashes(trim($input['mambrum_custom_code']));

	if (isset($input['mambrum_custom'])) $input['mambrum_custom'] = stripslashes(trim($input['mambrum_custom']));

	return $input;
}

// define dropdown options
$mambrum_location = array(
	'header' => array(
		'value' => 'header',
		'label' => esc_html__('Include code in the document head (via wp_head)', 'mambrum')
	),
	'footer' => array(
		'value' => 'footer',
		'label' => esc_html__('Include code in the document footer (via wp_footer)', 'mambrum')
	),
);

// add the options page
function mambrum_add_options_page()
{
	global $mambrum_plugin;
	add_options_page($mambrum_plugin, 'Mambrum', 'manage_options', __FILE__, 'mambrum_render_form');
}
add_action ('admin_menu', 'mambrum_add_options_page');

// create the options page
function mambrum_render_form()
{

	global $mambrum_plugin, $mambrum_options, $mambrum_path, $mambrum_homeurl, $mambrum_version, $mambrum_location;

	if (isset($mambrum_options['version_alert'])) $version_alert = $mambrum_options['version_alert'] ? true : false;
	if (isset($mambrum_options['mambrum_universal'])) $mambrum_universal = $mambrum_options['mambrum_universal'] ? true : false;

	$display_alert =  ' style="display:none;"';

	?>

	<style type="text/css">
		.dismiss-alert { margin: 15px; }
		.dismiss-alert-wrap { display: inline-block; padding: 7px 0 10px 0; }
		.dismiss-alert .description { display: inline-block; margin: -2px 15px 0 0; }
		.mm-panel-overview {
			padding: 0 15px 10px 100px;
			background-image: url(<?php echo plugins_url(); ?>/mambrum/mambrum-logo.jpg);
			background-repeat: no-repeat; background-position: 15px 0; background-size: 80px 80px;
			}
		.mm-panel-usage { padding-bottom: 10px; }

		#mm-plugin-options h1 small { line-height: 12px; font-size: 12px; color: #bbb; }
		#mm-plugin-options h2 { margin: 0; padding: 12px 0 12px 15px; font-size: 16px; cursor: pointer; }
		#mm-plugin-options h3 { margin: 20px 15px; font-size: 14px; }

		#mm-plugin-options p { margin-left: 15px; }
		#mm-plugin-options p.mm-alt { margin: 15px 0; }
		#mm-plugin-options .mm-item-caption,
		#mm-plugin-options .mm-item-caption code { font-size: 11px; }
		#mm-plugin-options ul,
		#mm-plugin-options ol { margin: 15px 15px 15px 40px; }
		#mm-plugin-options li { margin: 10px 0; list-style-type: disc; }
		#mm-plugin-options abbr { cursor: help; border-bottom: 1px dotted #dfdfdf; }

		.mm-table-wrap { margin: 15px; }
		.mm-table-wrap td { padding: 15px; vertical-align: middle; }
		.mm-table-wrap .widefat td { vertical-align: middle; }
		.mm-table-wrap .widefat th { width: 25%; vertical-align: middle; }
		.mm-table-wrap td input[type="checkbox"] { position: relative; top: -2px; }
		.mm-table-wrap td textarea { width: 90%; }
		.mm-code { background-color: #fafae0; color: #333; font-size: 14px; }
		.mm-radio-inputs { margin: 7px 0; }
		.mm-radio-inputs span { padding-left: 5px; }

		#setting-error-settings_updated { margin: 8px 0 15px 0; }
		#setting-error-settings_updated p { margin: 7px 0; }
		#mm-plugin-options .button-primary { margin: 0 0 15px 15px; }

		#mm-panel-toggle { margin: 5px 0; }
		#mm-credit-info { margin-top: -5px; }
	</style>

	<div id="mm-plugin-options" class="wrap">
		<h1><?php echo "Mambrum";/*$mambrum_plugin;*/ ?> <small><?php echo 'v1.2' . $mambrum_version; ?></small></h1>

		<form method="post" action="options.php">
			<?php $mambrum_options = get_option('mambrum_options'); settings_fields('mambrum_plugin_options'); ?>

			<div class="metabox-holder">
				<div class="meta-box-sortables ui-sortable">

					<div id="mm-panel-primary" class="postbox">
						<h2><?php esc_html_e('Plugin Settings', 'mambrum'); ?></h2>
						<div class="toggle<?php if (!isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<p><?php esc_html_e('Enter your Website ID.', 'mambrum'); ?></p>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="mambrum_options[mambrum_id]"><?php esc_html_e('Website ID', 'mambrum') ?></label></th>
										<td><input type="text" size="20" maxlength="20" name="mambrum_options[mambrum_id]" value="<?php echo $mambrum_options['mambrum_id']; ?>" /></td>
									</tr>
								</table>
							</div>
							<input type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'mambrum'); ?>" />
						</div>
					</div>

					<div id="mm-panel-current" class="postbox">
						<div class="toggle">
							<?php require_once('support-panel.php'); ?>
						</div>
					</div>

				</div>
			</div>



		</form>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			// toggle panels
			jQuery('.default-hidden').hide();
			jQuery('#mm-panel-toggle a').click(function(){
				jQuery('.toggle').slideToggle(300);
				return false;
			});
			jQuery('h2').click(function(){
				jQuery(this).next().slideToggle(300);
			});
			jQuery('#mm-panel-usage-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-usage .toggle').slideToggle(300);
				return true;
			});
			jQuery('#mm-panel-primary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-primary .toggle').slideToggle(300);
				return true;
			});
			//dismiss_alert
			if (!jQuery('.dismiss-alert-wrap input').is(':checked')){
				jQuery('.dismiss-alert-wrap input').one('click',function(){
					jQuery('.dismiss-alert-wrap').after('<input type="submit" class="button-secondary" value="<?php esc_attr_e('Save Preference', 'mambrum'); ?>" />');
				});
			}
			// prevent accidents
			if (!jQuery('#mm_restore_defaults').is(':checked')){
				jQuery('#mm_restore_defaults').click(function(event){
					var r = confirm("<?php esc_html_e('Are you sure you want to restore all default options? (this action cannot be undone)', 'mambrum'); ?>");
					if (r == true){
						jQuery('#mm_restore_defaults').attr('checked', true);
					} else {
						jQuery('#mm_restore_defaults').attr('checked', false);
					}
				});
			}
		});
	</script>

<?php }
