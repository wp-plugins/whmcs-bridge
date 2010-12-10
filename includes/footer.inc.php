<?php

if (!function_exists('cc_footers')) {
	function cc_footers($nodisplay='') {
		global $cc_footer,$cc_footers;

		$bail_out = ( ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) || ( strpos( $_SERVER[ 'PHP_SELF' ], 'wp-admin' ) !== false ) );
		if ( $bail_out ) return $footer;

		//Please contact us if you wish to remove the ChoppedCode logo in the footer
		if (!$cc_footer) {
			$msg.='<center style="margin-top:0px;font-size:x-small">';
			$msg.='Powered by <a href="http://www.choppedcode.com">ChoppedCode</a>';
			if (count($cc_footers) >0) {
				foreach ($cc_footers as $foot) {
					$msg.=', <a href="'.$foot[0].'">'.$foot[1].'</a>';
				}
			}
			$msg.='</center>';
			$cc_footer=true;
			if ($nodisplay===true) return $msg;
			else echo $msg;
		}

	}
}
?>