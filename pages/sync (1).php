<?php
if (defined("CC_WHMCS_BRIDGE_SSO_PLUGIN") && file_exists(dirname(__FILE__).'/../../whmcs-bridge-sso/pages/sync.php')) require(dirname(__FILE__).'/../../whmcs-bridge-sso/pages/sync.php');
else echo 'Sync is a Pro feature.';
