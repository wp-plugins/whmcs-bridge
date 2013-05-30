<?php
	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	$submit='Update';
	?>
<form method="post">
<?php require(dirname(__FILE__).'/../includes/cpedit.inc.php')?>
<p class="submit"><input name="install" type="submit" value="<?php echo $submit;?>" /> <input
	type="hidden" name="action" value="install"
/></p>
</form>
