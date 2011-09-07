<?php
function cc_whmcs_bridge_options() {
	global $cc_whmcs_bridge_name,$cc_whmcs_bridge_shortname,$cc_login_type,$current_user;
	$cc_whmcs_bridge_name = "WHMCS Bridge";
	$cc_whmcs_bridge_shortname = "cc_whmcs_bridge";

	$cc_whmcs_bridge_options[] = array(  "name" => "Integration Settings",
            "type" => "heading",
			"desc" => "This section customizes the way WHMCS Bridge interacts with Wordpress.");
	$cc_whmcs_bridge_options[] = array(	"name" => "WHMCS URL",
			"desc" => "The site URL of your WHMCS installation. Make sure this is exactly the same as the settings field 'WHMCS System URL' in WHMCS. If you want to use SSL (https), make sure this URL and the 'WHMCS System URL' are using the https URL. In all cases make sure the WHMCS setting 'WHMCS SSL System URL' is left blank.",
			"id" => $cc_whmcs_bridge_shortname."_url",
			"type" => "text");
		$cc_whmcs_bridge_options[] = array(	"name" => "WHMCS admin user",
			"desc" => 'This is your WHMCS admin user, used for connections, upgrades and synchronisation of new users.<br />Make sure you authorise your IP in your WHMCS portal (General Settings - Security - API IP Access Restriction).',
			"id" => $cc_whmcs_bridge_shortname."_admin_login",
			"type" => "text");
	$cc_whmcs_bridge_options[] = array(	"name" => "WHMCS admin password",
			"desc" => "The password of the WHMCS admin user.",
			"id" => $cc_whmcs_bridge_shortname."_admin_password",
			"type" => "password");
	if (get_option('cc_whmcs_bridge_sso_active')) {
		require(get_option('cc_whmcs_bridge_sso_active').'/includes/controlpanel.inc.php');
	}
	
	$cc_whmcs_bridge_options[] = array(  "name" => "Styling Settings",
            "type" => "heading",
			"desc" => "This section customizes the look and feel.");
	$cc_whmcs_bridge_options[] = array(	"name" => "Don't load jQuery",
			"desc" => "If you have a theme using jQuery, you can avoid loading it twice by ticking this box",
			"id" => $cc_whmcs_bridge_shortname."_jquery",
			"type" => "checkbox");
	$cc_whmcs_bridge_options[] = array(	"name" => "Custom styles",
			"desc" => 'Enter your custom CSS styles here',
			"id" => $cc_whmcs_bridge_shortname."_css",
			"type" => "textarea");
	$cc_whmcs_bridge_options[] = array(	"name" => "Load WHMCS styles",
			"desc" => 'Select if you want to load the WHMCS style.css style sheet',
			"id" => $cc_whmcs_bridge_shortname."_style",
			"type" => "checkbox");
	
	$cc_whmcs_bridge_options[] = array(  "name" => "Other Settings",
            "type" => "heading",
			"desc" => "This section customizes miscellaneous settings.");
	$cc_whmcs_bridge_options[] = array(	"name" => "Debug",
			"desc" => "If you have problems with the plugin, activate the debug mode to generate a debug log for our support team",
			"id" => $cc_whmcs_bridge_shortname."_debug",
			"type" => "checkbox");
	$cc_whmcs_bridge_options[] = array(	"name" => "Footer",
			"desc" => "Specify where you want the Zingiri footer to appear. If you disable the footer here,<br />we count on you to link back to our site some other way.",
			"id" => $cc_whmcs_bridge_shortname."_footer",
			"std" => 'Page',
			"type" => "select",
			"options" => array('Site','Page','None'));

	return $cc_whmcs_bridge_options;
}

function cc_whmcs_bridge_add_admin() {

	global $cc_whmcs_bridge_name, $cc_whmcs_bridge_shortname;

	$cc_whmcs_bridge_options=cc_whmcs_bridge_options();

	if ( $_GET['page'] == "cc-ce-bridge-cp" ) {
		
		if ( isset($_REQUEST['action']) && 'install' == $_REQUEST['action'] ) {
			delete_option('cc_whmcs_bridge_log');
			foreach ($cc_whmcs_bridge_options as $value) {
				update_option( $value['id'], $_REQUEST[ $value['id'] ] );
			}

			foreach ($cc_whmcs_bridge_options as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
				} else { delete_option( $value['id'] );
				}
			}
			cc_whmcs_bridge_install();
			if (function_exists('cc_whmcs_bridge_sso_update')) cc_whmcs_bridge_sso_update();
			header("Location: options-general.php?page=cc-ce-bridge-cp&installed=true");
			die;
		}
	}

	add_options_page($cc_whmcs_bridge_name, $cc_whmcs_bridge_name, 'administrator', 'cc-ce-bridge-cp','cc_whmcs_bridge_admin');
}

function cc_whmcs_bridge_admin() {

	global $cc_whmcs_bridge_name, $cc_whmcs_bridge_shortname;

	$cc_whmcs_bridge_options=cc_whmcs_bridge_options();

	if ( isset($_REQUEST['installed']) ) echo '<div id="message" class="updated fade"><p><strong>'.$cc_whmcs_bridge_name.' installed.</strong></p></div>';
	if ( isset($_REQUEST['error']) ) echo '<div id="message" class="updated fade"><p>The following error occured: <strong>'.$_REQUEST['error'].'</strong></p></div>';
	
	?>
<div class="wrap">
<div id="cc-left" style="position:relative;float:left;width:80%">
<h2><b><?php echo $cc_whmcs_bridge_name; ?></b></h2>

	<?php
	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	$submit='Update';
	?>
<form method="post">

<table class="optiontable">

<?php if ($cc_whmcs_bridge_options) foreach ($cc_whmcs_bridge_options as $value) {

	if ($value['type'] == "text" || $value['type'] == "password") { ?>

	<tr align="left">
		<th scope="row"><?php echo $value['name']; ?>:</th>
		<td><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"
			type="<?php echo $value['type']; ?>"
			value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>"
			size="40"
		/></td>

	</tr>
	<tr>
		<td colspan=2><small><?php echo $value['desc']; ?> </small>
		<hr />
		</td>
	</tr>

	<?php } elseif ($value['type'] == "checkbox") { ?>

	<tr align="left">
		<th scope="row"><?php echo $value['name']; ?>:</th>
		<td><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"
			type="checkbox"
			value="checked"
			<?php if ( get_option( $value['id'] ) != "") { echo " checked"; } ?>
		/></td>

	</tr>
	<tr>
		<td colspan=2><small><?php echo $value['desc']; ?> </small>
		<hr />
		</td>
	</tr>


	<?php } elseif ($value['type'] == "textarea") { ?>
	<tr align="left">
		<th scope="row"><?php echo $value['name']; ?>:</th>
		<td><textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="50"
			rows="8"
		/>
		<?php if ( get_option( $value['id'] ) != "") { echo stripslashes (get_option( $value['id'] )); }
		else { echo $value['std'];
		} ?>
</textarea></td>

	</tr>
	<tr>
		<td colspan=2><small><?php echo $value['desc']; ?> </small>
		<hr />
		</td>
	</tr>

	<?php } elseif ($value['type'] == "select") { ?>

	<tr align="left">
		<th scope="top"><?php echo $value['name']; ?>:</th>
		<td><select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
			<option <?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; }?>><?php echo $option; ?></option>
			<?php } ?>
		</select></td>

	</tr>
	<tr>
		<td colspan=2><small><?php echo $value['desc']; ?> </small>
		<hr />
		</td>
	</tr>

	<?php } elseif ($value['type'] == "heading") { ?>

	<tr valign="top">
		<td colspan="2" style="text-align: left;">
		<h2 style="color: green;"><?php echo $value['name']; ?></h2>
		</td>
	</tr>
	<tr>
		<td colspan=2><small>
		<p style="color: red; margin: 0 0;"><?php echo $value['desc']; ?></P>
		</small>
		<hr />
		</td>
	</tr>

	<?php } 
} //end foreach
?>
</table>

<p class="submit"><input name="install" type="submit" value="<?php echo $submit;?>" /> <input
	type="hidden" name="action" value="install"
/></p>
</form>
<hr />
<?php  
	if ($cc_whmcs_bridge_version && get_option('cc_whmcs_bridge_debug')) {
		echo '<h2 style="color: green;">Debug log</h2>';
		echo '<textarea rows=10 cols=80>';
		$r=get_option('cc_whmcs_bridge_log');
		if ($r) {
			$v=$r;
			foreach ($v as $m) {
				echo date('H:i:s',$m[0]).' '.$m[1].chr(13).chr(10);
				echo $m[2].chr(13).chr(10);
			}
		}
		echo '</textarea><hr />';
	}
?>

</div> <!-- end cc-left -->
<?php
	require(dirname(__FILE__).'/support-us.inc.php');
}
add_action('admin_menu', 'cc_whmcs_bridge_add_admin'); ?>