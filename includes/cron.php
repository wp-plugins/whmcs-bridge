<?php
//cron job for cleaning cache directory
function cc_whmcs_bridge_cron() {
	$errorLog=new zErrorLog();

	$dir = dirname(__FILE__).'/../cache/';
	echo $dir;
	$errorLog->msg('Cron job: cleaning cache');

	if ($handle = opendir($dir)) {
		while (false !== ($filename = readdir($handle))){
			if (substr($filename,-4) == '.tmp' && filemtime($dir.$filename) < strtotime ("-2 days") ) {
				unlink ($dir.$filename) ;
			}
		}
		closedir($handle);
	}
}
if ($cc_whmcs_bridge_version) {
	if (!wp_next_scheduled('cc_whmcs_bridge_cron_hook')) {
		wp_schedule_event( time(), 'hourly', 'cc_whmcs_bridge_cron_hook' );
	}
	add_action('cc_whmcs_bridge_cron_hook','cc_whmcs_bridge_cron');
}