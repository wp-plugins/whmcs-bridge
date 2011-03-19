<?php
function cc_whmcs_bridge_options() {
	global $cc_whmcs_bridge_name,$cc_whmcs_bridge_shortname,$cc_login_type,$current_user;
	$cc_whmcs_bridge_name = "WHMCS Bridge";
	$cc_whmcs_bridge_shortname = "cc_whmcs_bridge";

	$cc_whmcs_bridge_options[] = array(  "name" => "Integration Settings",
            "type" => "heading",
			"desc" => "This section customizes the way WHMCS Bridge interacts with Wordpress.");
	$cc_whmcs_bridge_options[] = array(	"name" => "WHMCS URL",
			"desc" => "The site URL of your WHMCS installation",
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
			
	if (file_exists(dirname(__FILE__).'/../whmcs-bridge-sso')) {
		$cc_whmcs_bridge_options[] = array(	"name" => "SSO license key",
				"desc" => 'Only required if you are using the Single Sign On extension. You can obtain a license key <a href="http://www.clientcentral.info/cart.php?gid=4">here</a>.',
				"id" => $cc_whmcs_bridge_shortname."_sso_license_key",
				"type" => "text");
	}
			
	$cc_whmcs_bridge_options[] = array(	"name" => "Footer",
			"desc" => "Specify where you want the ChoppedCode footer to appear. If you disable the footer here,<br />we count on you to link back to our site some other way.",
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

		if( isset($_REQUEST['action']) && 'uninstall' == $_REQUEST['action'] ) {
			cc_whmcs_bridge_uninstall();
			foreach ($cc_whmcs_bridge_options as $value) {
				delete_option( $value['id'] );
				update_option( $value['id'], $value['std'] );
			}
			header("Location: options-general.php?page=cc-ce-bridge-cp&uninstalled=true");
			die;
		}
	}

	//add_menu_page($cc_whmcs_bridge_name, $cc_whmcs_bridge_name, 'administrator', 'cc-ce-bridge-cp','cc_whmcs_bridge_admin');
	//add_submenu_page('cc-ce-bridge-cp', $cc_whmcs_bridge_name.'- Integration', 'Integration', 'administrator', 'cc-ce-bridge-cp', 'cc_whmcs_bridge_admin');
	add_options_page($cc_whmcs_bridge_name, $cc_whmcs_bridge_name, 'administrator', 'cc-ce-bridge-cp','cc_whmcs_bridge_admin');
	//add_options_page('My Plugin Options', 'My Plugin', 'manage_options', 'my-unique-identifier', 'my_plugin_options');
}

function cc_whmcs_bridge_admin() {

	global $cc_whmcs_bridge_name, $cc_whmcs_bridge_shortname;

	$cc_whmcs_bridge_options=cc_whmcs_bridge_options();

	if ( isset($_REQUEST['installed']) ) echo '<div id="message" class="updated fade"><p><strong>'.$cc_whmcs_bridge_name.' installed.</strong></p></div>';
	if ( isset($_REQUEST['uninstalled']) ) echo '<div id="message" class="updated fade"><p><strong>'.$cc_whmcs_bridge_name.' uninstalled.</strong></p></div>';
	if ( isset($_REQUEST['error']) ) echo '<div id="message" class="updated fade"><p>The following error occured: <strong>'.$_REQUEST['error'].'</strong></p></div>';
	
	?>
<div class="wrap">
<div id="cc-left" style="position:relative;float:left;width:70%">
<h2><b><?php echo $cc_whmcs_bridge_name; ?></b></h2>

	<?php
	$cc_ew=cc_whmcs_bridge_check();
	$cc_errors=$cc_ew['errors'];
	$cc_warnings=$cc_ew['warnings'];
	if ($cc_errors) {
		echo '<div style="background-color:pink" id="message" class="updated fade"><p>';
		echo '<strong>Errors - you need to resolve these errors before continuing:</strong><br /><br />';
		foreach ($cc_errors as $cc_error) echo $cc_error.'<br />';
		echo '</p></div>';
	}
	if ($cc_warnings) {
		echo '<div style="background-color:peachpuff" id="message" class="updated fade"><p>';
		echo '<strong>Warnings - you might want to have a look at these issues to avoid surprises or unexpected behaviour:</strong><br /><br />';
		foreach ($cc_warnings as $cc_warning) echo $cc_warning.'<br />';
		echo '</p></div>';
	}
	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	if (empty($cc_whmcs_bridge_version)) {
		echo 'Please proceed with a clean install or deactivate your plugin';
		$submit='Install';
	} elseif ($cc_whmcs_bridge_version != CC_WHMCS_BRIDGE_VERSION) {
		echo 'You downloaded version '.CC_WHMCS_BRIDGE_VERSION.' and need to upgrade your database (currently at version '.$cc_whmcs_bridge_version.') by clicking Upgrade below.';
		$submit='Upgrade';
	} elseif ($cc_whmcs_bridge_version == CC_WHMCS_BRIDGE_VERSION) {
		echo 'Your version is up to date!';
		$submit='Update';
	}
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
<?php if ($cc_whmcs_bridge_version) { ?>
<hr />
<form method="post">
<p class="submit"><input name="uninstall" type="submit" value="Uninstall" /> <input type="hidden"
	name="action" value="uninstall"
/></p>
</form>
<?php } ?>
<hr />
<img src="<?php echo CC_WHMCS_BRIDGE_URL?>/choppedcode.png" height="50px" />
<p>For more info and support, you can find us at <a href="http://www.choppedcode.com">ChoppedCode</a>.</p>
</div> <!-- end cc-left -->
<?php
	require(dirname(__FILE__).'/support-us.inc.php');
	
	echo '</div>'; //end wrap
}
add_action('admin_menu', 'cc_whmcs_bridge_add_admin'); ?>