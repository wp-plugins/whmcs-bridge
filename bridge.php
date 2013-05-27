<?php
/*
 Plugin Name: WHMCS Bridge
 Plugin URI: http://www.zingiri.com
 Description: WHMCS Bridge is a plugin that integrates the powerfull WHMCS support and billing software with Wordpress.

 Author: Zingiri
 Version: 2.3.0
 Author URI: http://www.zingiri.com/
 */

require(dirname(__FILE__).'/bridge.init.php');
register_activation_hook(__FILE__,'cc_whmcs_bridge_activate');
register_deactivation_hook(__FILE__,'cc_whmcs_bridge_deactivate');