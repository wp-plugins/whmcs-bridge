<?php
/**
 * Output activation messages to log
 * @param $stringData
 * @return unknown_type
 */

class zErrorLog {
	var $debug=false;
	
	function zErrorLog($clear=false,$debug=true) {
		if ($clear) $this->clear();
		$this->debug=$debug;
	}

	function log($severity, $msg, $filename="", $linenum=0) {
		if (is_array($msg)) $msg=print_r($msg,true);
		$toprint=date('Y-m-d h:i:s').' '.$msg.' ('.$filename.'-'.$linenum.')';
		$myFile = dirname(__FILE__)."/../log.txt";
		if ($fh = fopen($myFile, 'a')) {
			fwrite($fh, $toprint."\r\n");
			fclose($fh);
		}
		if ($this->debug) echo $toprint.'<br />';
	}

	function msg($msg) {
		$this->log(0,$msg);
	}
	
	function clear() {
		$myFile = dirname(__FILE__)."/../log.txt";
		if ($fh = fopen($myFile, 'w')) {
			fclose($fh);
		}
	}
}
?>