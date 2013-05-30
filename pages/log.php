<?php
if ($cc_whmcs_bridge_version && get_option('cc_whmcs_bridge_debug')) {
	echo '<h2 style="color: green;">Debug log</h2>';
	$r=get_option('cc_whmcs_bridge_log');
	if ($r) {
		echo '<table style="font-size:smaller">';
		$v=$r;
		foreach ($v as $m) {
			echo '<tr>';
			echo '<td style="padding-right:10px">';
			echo date('H:i:s',$m[0]);
			echo '</td>';
			echo '<td style="padding-right:10px">';
			echo $m[1];
			echo '</td>';
			echo '<td>';
			echo $m[2];
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
} else {
	echo 'Activate log first.';
}
