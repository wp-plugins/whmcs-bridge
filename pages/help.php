			<h3>The bridge page</h3>
			<p>A WHMCS front end page has been created on your Wordpress site. This page is the main interaction page between Wordpress and WHMCS.</p>
		<p>The full url is:<a href="<?php echo cc_whmcs_bridge_home($home,$pid);?>"><code><?php echo cc_whmcs_bridge_home($home,$pid);?></code></a>. You can edit the page link by editing the page and changing the permalink.</p>
		<p style="color:red">Do not delete this page!</p>
			
<?php
if (file_exists(dirname(__FILE__).'/../../whmcs-bridge-sso/pages/help.php')) require(dirname(__FILE__).'/../../whmcs-bridge-sso/pages/help.php');
?>