<div id="cc-right" style="width:20%;float:right;position:relative" class="update-nag">
	<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
	<h3>Support Us</h3>
	<p>If you like this plugin, please share it with your friends</p>
	<div style="align:center;margin-bottom:15px;text-align:center">
		<a style="margin-bottom:15px" href="http://www.twitter.com/zingiri"><img align="middle" src="http://twitter-badges.s3.amazonaws.com/follow_us-a.png" alt="Follow Zingiri on Twitter"/></a>
	</div>
	<div style="margin-bottom:15px;text-align:center">
		<fb:share-button href="http://www.zingiri.net/plugins-and-addons/whmcs-bridge/" type="button" >
	</div>
	<p>And rate our plugin on Wordpress</p>
	<a href="http://wordpress.org/extend/plugins/whmcs-bridge" alt="Rate our plugin"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"></img></a>
<?php 
if (!function_exists('zing_support_us')) {
	function zing_support_us($plugin,$action='check') {
		$option=$plugin.'-support-us';
		if ($action == 'activate' || get_option($option) == '') {
			update_option($option,time());
		} elseif ($_REQUEST['support-us'] == 'hide') {
			update_option($option,time()+7776000);
		} elseif ($action == 'check') {
			if ((time() - get_option($option)) > 1209600) { //14 days 
				return "<div id='zing-warning' style='background-color:red;color:white;font-size:large;margin:20px;padding:10px;'>Looks like you've been using this plugin for quite a while now. Have you thought about showing your appreciation through a small donation?<br /><br /><a href='http://www.zingiri.net/donations'><img src='https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif' /></a><br /><br />If you already made a donation, you can <a href='?page=".$plugin."&support-us=hide'>hide</a> this message.</div>";
			}
		}
	}
}
echo zing_support_us('cc-ce-bridge-cp');
?>
</div>
</div>
<div id="cc-right2" style="width:25%;float:right;position:relative;text-align:center;margin-top:40px">
	<a href="http://www.zingiri.net" target="_blank"><img src="http://www.zingiri.net/logo.png" /></a>
</div>
