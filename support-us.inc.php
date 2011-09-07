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
?>
<div style="width:20%;float:right;position:relative;">
	<div class="cc-support-us">
		<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
		<h2>Support Us</h2>
		<p>If you like this plugin, please share it with your friends</p>
		<div style="align:center;margin-bottom:15px;text-align:center">
			<a style="margin-bottom:15px" href="http://www.twitter.com/zingiri"><img align="middle" src="http://twitter-badges.s3.amazonaws.com/follow_us-a.png" alt="Follow Zingiri on Twitter"/></a>
		</div>
		<div style="margin-bottom:15px;text-align:center">
			<fb:share-button href="http://www.zingiri.net/plugins-and-addons/whmcs-bridge/" type="button" >
		</div>
		<p>And rate our plugin on Wordpress</p>
		<a href="http://wordpress.org/extend/plugins/whmcs-bridge" alt="Rate our plugin"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"><img height="35px" src="<?php echo CC_WHMCS_BRIDGE_URL;?>stars.png"></img></a>
		<?php echo zing_support_us('cc-ce-bridge-cp');?>
	</div>

	<div class="cc-support-us">
	<h2>Documentation & Support</h2>
	Check out our <a href="http://www.zingiri.net/plugins-and-addons/whmcs-bridge/" target="_blank">documentation</a> to get you starting. If you encounter any issues, you will most likely find the answer by searching or posting on our <a href="http://forums.zingiri.net/forumdisplay.php?fid=56" target="_blank">forums</a>. And if you're really stuck, you can always ask your question via our <a href="http://www.clientcentral.info/submitticket.php" target="_blank">support desk</a>. Finally, if you need professional support or custom development, you can contact us <a href="http://www.clientcentral.info/submitticket.php" target="_blank">here</a>. 
	</div>

	<?php if (!get_option('cc_whmcs_bridge_sso_active')) {?>
	<div class="cc-support-us">
	<h2>Discover WHMCS Bridge Pro</h2>
	<p>Thanks to the single sign-on feature, your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.</p>
	<p>Additional features: multi-lingual WHMCS support</p>
	<a href="http://www.clientcentral.info/cart.php?a=add&pid=20" target="_blank"><img src="<?php echo CC_WHMCS_BRIDGE_URL;?>images/buy_now.png" /></a>
	</div>
	<?php }?>

	<div style="text-align:center;margin-top:40px">
		<a href="http://www.zingiri.net" target="_blank"><img src="http://www.zingiri.net/logo.png" /></a>
	</div>
</div>
