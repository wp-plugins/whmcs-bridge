<?php 
//v2.02.06
if (!function_exists('zing_support_us')) {
	function zing_support_us($shareName,$wpPluginName,$adminLink,$version,$donations=true,$pluginUrl=false) {
		if (!$pluginUrl) $pluginUrl=plugins_url().'/'.$wpPluginName.'/';
		if (get_option('cc_whmcs_bridge_sso_license_key')) $donations=false;
?>
		<div style="width:20%;float:right;position:relative">
				<div style="margin:5px 15px;">
					<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
					<div style="float:left;">
						<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.zingiri.com" data-text="Zingiri">Tweet</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>				
					</div>
					<div style="float:left;">
						<fb:share-button href="http://www.zingiri.com/bookings/<?php echo $shareName;?>/" type="button" >
					</div>
				</div>
				<div style="clear:both"></div>
			<div class="cc-support-us">
			<h3>The bridge page</h3>
			<p>A WHMCS front end page has been created on your Wordpress site. This page is the main interaction page between Wordpress and WHMCS.</p>
		<p>The full url is:<a href="<?php echo cc_whmcs_bridge_home($home,$pid);?>"><code><?php echo cc_whmcs_bridge_home($home,$pid);?></code></a>. You can edit the page link by editing the page and changing the permalink.</p>
		<p style="color:red">Do not delete this page!</p>
			
			</div>
			<div class="cc-support-us">
			<h3>Not sure where to start?</h3>
			<p>Download our <a href="http://go.zingiri.com/downloads.php?action=displaycat&catid=6">documentation</a></p><br />
			<p>Check out our <a href="http://forums.zingiri.com/forumdisplay.php?fid=74">forums</a></p><br />
			<p>Pro users can open a <a href="https://go.zingiri.com/submitticket.php">support ticket</a></p>
			</div>
			<div class="cc-support-us">
			<h3>Discover our other plugins & addons!</h3>
			<a href="http://www.zingiri.com" target="_blank"><h4 style="color:blue;">WHMCS Membership</h4></a>&nbsp;<p>Create a membership website using WHMCS for billing and WordPress to manage the content.</p><br />
			<a href="http://www.zingiri.com/plugins-and-addons/remote-provisioning/" target="_blank"><h4 style="color:blue;">Remote Provisioning</h4></a>&nbsp;<p>Automatically provision Wordpress websites from WHMCS.</p><br />
			<a href="http://www.zingiri.com/plugins-and-addons/product-images-for-whmcs/" target="_blank"><h4 style="color:blue;">Product Images</h4></a>&nbsp;<p>Add images to your WHMCS products.</p><br />
			<a href="http://www.zingiri.com/plugins-and-addons/whmcs-backup-restore/" target="_blank"><h4 style="color:blue;">Backup &amp; Restore</h4></a>&nbsp;<p>Make it easy to backup, restore &amp; transer sites with WHMCS.</p><br />
			<a href="http://www.zingiri.com" target="_blank"><h4 style="color:blue;">Facebook Promo</h4></a>&nbsp;<p>Automatically issue promotions to your Facebook fans.</p><br />
			</div>
			<?php if (!get_option('cc_whmcs_bridge_sso_license_key')) {?>
			<div class="cc-support-us">
					<h3>Discover WHMCS Bridge Pro</h3>
					<h4>Single sign on: </h4><p>thanks to the single sign-on feature, your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.</p><br /><br />
					<h4>Multi-lingual WHMCS support: </h4><p>fully integrated with qtranslate.</p><br /><br />
					<h4>IP address resolution 'patch': </h4><p>shows your customer's IP address instead of your server's IP address during sign up.</p><br /><br />
					<h4>Choose your WHMCS portal: </h4><p>fully compatible with the WHMCS v5 'default' template</p><br /><br />
					<h4>Pretty permalinks: </h4><p>display links like http://www.mysite.com/clientarea/ rather than http://www.mysite.com/?ccce=clientarea. Also supports knowledgebase, announcement and download links.</p><br /><br />
					<a href="http://www.zingiri.com/whmcs-bridge/" target="_blank"><img src="<?php echo plugins_url().'/whmcs-bridge/images/buy_now.png'?>" /></a>
				</div>
				<?php }?>
			<div class="cc-support-us">
				<h3>Support us by rating our plugin on Wordpress</h3>
				<a href="http://wordpress.org/extend/plugins/<?php echo $wpPluginName;?>" alt="Rate our plugin">
				<img src="<?php echo $pluginUrl?>images/5-stars-125pxw.png" />
				</a>
				<?php 
				$option=$wpPluginName.'-support-us';
				if (get_option($option) == '') {
					update_option($option,time());
				} elseif (isset($_REQUEST['support-us']) && ($_REQUEST['support-us'] == 'hide')) {
					update_option($option,time()+7776000);
				} else {
					if ((time() - get_option($option)) > 1209600) { //14 days 
						if ($donations) echo "<div id='zing-warning' style='background-color:red;color:white;font-size:large;margin:20px;padding:10px;'>Looks like you've been using this plugin for quite a while now. Have you thought about showing your appreciation through a small donation?<br /><br /><a href='http://www.zingiri.com/donations'><img src='https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif' /></a><br /><br />If you already made a donation, you can <a href='?page=".$adminLink."&support-us=hide'>hide</a> this message.</div>";
					}
				}
				?>
			</div>
			<div style="text-align:center;margin-top:15px">
				<a href="http://www.zingiri.com" target="_blank"><img width="150px" src="<?php echo $pluginUrl?>images/logo.png" /></a>
			</div>
		</div>
<?php 
	}
}
?>