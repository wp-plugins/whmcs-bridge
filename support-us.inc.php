<?php 
if (!function_exists('zing_support_us')) {
	function zing_support_us($shareName,$wpPluginName,$adminLink,$version) {
?>
		<div style="width:20%;float:right;position:relative">
			<div class="cc-support-us">
				<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
				<p>If you like this plugin, please share it with your friends</p>
				<div style="align:center;margin-bottom:15px;text-align:center">
					<a style="margin-bottom:15px" href="http://www.twitter.com/zingiri"><img align="middle" src="http://twitter-badges.s3.amazonaws.com/follow_us-a.png" alt="Follow Zingiri on Twitter"/></a>
				</div>
				<div style="margin-bottom:15px;text-align:center">
					<fb:share-button href="http://www.zingiri.net/plugins-and-addons/<?php echo $shareName;?>/" type="button" >
				</div>
				<p>Rate our plugin on Wordpress</p>
				<a href="http://wordpress.org/extend/plugins/<?php echo $wpPluginName;?>" alt="Rate our plugin">
				<?php for ($i=0;$i<5;$i++) {?>
					<img height="35px" src="http://www.zingiri.net/wordpress/wp-content/uploads/stars.png" />
				<?php }?>
				</a>
				<?php 
				//echo zing_support_us($adminLink);
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
				?>
			</div>
			<?php 	
			global $current_user;
			$url='http://www.zingiri.net/index.php?zlistpro=register&e='.urlencode($current_user->data->user_email).'&f='.urlencode($current_user->data->first_name).'&l='.urlencode($current_user->data->last_name).'&w='.urlencode(get_option('home')).'&p='.$wpPluginName.'&v='.urlencode($version);
			$news = new zHttpRequest($url);
			if ($news->live() && !$_SESSION[$wpPluginName]['news']) {
				update_option($wpPluginName.'_news',$news->DownloadToString());
				$_SESSION[$wpPluginName]['news']=true;
			}
			?>
			<?php
			$data=json_decode(get_option($wpPluginName.'_news'));
			foreach ($data as $rec) { ?>
				<div class="cc-support-us">
				<h3><?php echo $rec->title;?></h3>
				<?php echo $rec->content;?>
				</div>
			<?php }?>

			<div style="text-align:center;margin-top:40px">
				<a href="http://www.zingiri.net" target="_blank"><img src="http://www.zingiri.net/logo.png" /></a>
			</div>
		</div>
<?php 
	}
}
zing_support_us('whmcs-bridge','whmcs-bridge','cc-ce-bridge-cp',CC_WHMCS_BRIDGE_VERSION);
?>