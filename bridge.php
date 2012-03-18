<?php
/*
 Plugin Name: WHMCS Bridge
 Plugin URI: http://www.zingiri.net
 Description: WHMCS Bridge is a plugin that integrates the powerfull WHMCS support and billing software with Wordpress.

 Author: Zingiri
 Version: 1.8.0
 Author URI: http://www.zingiri.net/
 */

require(dirname(__FILE__).'/bridge.init.php');
register_activation_hook(__FILE__,'cc_whmcs_bridge_activate');
register_deactivation_hook(__FILE__,'cc_whmcs_bridge_deactivate');
