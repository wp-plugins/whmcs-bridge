<?php
if ($cc_whmcs_bridge_version && get_option('cc_whmcs_bridge_debug')) {
	echo '<h2>Debug Log</h2>';
	$r=get_option('cc_whmcs_bridge_log');
	if ($r) {
		$v=$r;
		foreach ($v as $m) {
            echo '<h4 style="width:100%;background:#f7f7f7"><strong>'.date('H:i:s',$m[0]).'</strong>: <em>'.$m[1].'</em></h4>';
            echo '<div style="width:700px; word-wrap:break-word;">'.$m[2].'</div>';
		}
	}
} else {
	echo 'If you have problems with the plugin, activate the debug mode to generate a debug log for our support team.';
}
