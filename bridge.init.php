<?php
if (!defined('WHMCS_BRIDGE')) define('WHMCS_BRIDGE','WHMCS Bridge');

define("CC_WHMCS_BRIDGE_VERSION","1.6.6");

$compatibleWHMCSBridgeProVersions=array('1.6.0','1.6.1','1.6.2','1.6.3','1.6.4');

// Pre-2.6 compatibility for wp-content folder location
if (!defined("WP_CONTENT_URL")) {
	define("WP_CONTENT_URL", get_option("siteurl") . "/wp-content");
}
if (!defined("WP_CONTENT_DIR")) {
	define("WP_CONTENT_DIR", ABSPATH . "wp-content");
}

if (!defined("CC_WHMCS_BRIDGE_PLUGIN")) {
	$cc_whmcs_bridge_plugin=str_replace(realpath(dirname(__FILE__).'/..'),"",dirname(__FILE__));
	$cc_whmcs_bridge_plugin=substr($cc_whmcs_bridge_plugin,1);
	define("CC_WHMCS_BRIDGE_PLUGIN", $cc_whmcs_bridge_plugin);
}

if (!defined("BLOGUPLOADDIR")) {
	$upload=wp_upload_dir();
	define("BLOGUPLOADDIR",$upload['path']);
}

define("CC_WHMCS_BRIDGE_URL", WP_CONTENT_URL . "/plugins/".CC_WHMCS_BRIDGE_PLUGIN."/");

$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
if ($cc_whmcs_bridge_version) {
	add_action("init","cc_whmcs_bridge_init");
	if (get_option('cc_whmcs_bridge_footer')=='Site') add_filter('wp_footer','cc_whmcs_bridge_footer');
	add_filter('the_content', 'cc_whmcs_bridge_content', 10, 3);
	add_filter('the_title', 'cc_whmcs_bridge_title');
	add_action('wp_head','cc_whmcs_bridge_header');
	add_action('admin_head','cc_whmcs_bridge_admin_header');
	add_action("plugins_loaded", "cc_whmcs_sidebar_init");
}
add_action('admin_head','cc_whmcs_bridge_admin_header');
add_action('admin_notices','cc_whmcs_admin_notices');

require_once(dirname(__FILE__) . '/includes/shared.inc.php');
require_once(dirname(__FILE__) . '/includes/http.class.php');
require_once(dirname(__FILE__) . '/includes/footer.inc.php');
require_once(dirname(__FILE__) . '/includes/integrator.inc.php');
require_once(dirname(__FILE__) . '/bridge_cp.php');
if (!class_exists('simple_html_dom_node')) require_once(dirname(__FILE__) . '/includes/simple_html_dom.php');
require(dirname(__FILE__).'/includes/sidebars.php');
require(dirname(__FILE__).'/includes/parser.inc.php');

function cc_whmcs_admin_notices() {
	global $wpdb;
	$errors=array();
	$warnings=array();
	$files=array();
	$dirs=array();

	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	if ($cc_whmcs_bridge_version && $cc_whmcs_bridge_version != CC_WHMCS_BRIDGE_VERSION) $warnings[]='You downloaded version '.CC_WHMCS_BRIDGE_VERSION.' and need to update your settings (currently at version '.$cc_whmcs_bridge_version.') from the <a href="options-general.php?page=cc-ce-bridge-cp">control panel</a>.';
	$upload=wp_upload_dir();
	if (!is_writable(session_save_path())) $warnigns[]='It looks like PHP sessions are not properly configured on your server, the sessions save path <'.session_save_path().'> is not writable. This may be a false warning, contact us if in doubt.';
	if ($upload['error']) $errors[]=$upload['error'];
	if (!get_option('cc_whmcs_bridge_url')) $warnings[]="Please update your WHMCS connection settings on the plugin control panel";
	if (get_option('cc_whmcs_bridge_debug')) $warnings[]="Debug is active, once you finished debugging, it's recommended to turn this off";
	if (phpversion() < '5') $warnings[]="You are running PHP version ".phpversion().". We recommend you upgrade to PHP 5.3 or higher.";
	if (ini_get("zend.ze1_compatibility_mode")) $warnings[]="You are running PHP in PHP 4 compatibility mode. We recommend you turn this option off.";
	if (!function_exists('curl_init')) $errors[]="You need to have cURL installed. Contact your hosting provider to do so.";

	if (count($warnings) > 0) {
		echo "<div id='zing-warning' style='background-color:greenyellow' class='updated fade'><p><strong>";
		foreach ($warnings as $message) echo WHMCS_BRIDGE.': '.$message.'<br />';
		echo "</strong> "."</p></div>";
	}
	if (count($errors) > 0) {
		echo "<div id='zing-warning' style='background-color:pink' class='updated fade'><p><strong>";
		foreach ($errors as $message) echo WHMCS_BRIDGE.':'.$message.'<br />';
		echo "</strong> "."</p></div>";
	}

	return array('errors'=> $errors, 'warnings' => $warnings);
}


/**
 * Activation: creation of database tables & set up of pages
 * @return unknown_type
 */
function cc_whmcs_bridge_activate() {
	//nothing much to do
}

function cc_whmcs_bridge_install() {
	global $wpdb,$current_user;

	ob_start();
	cc_whmcs_log();
	set_error_handler('cc_whmcs_log');
	error_reporting(E_ALL & ~E_NOTICE);

	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	if (!$cc_whmcs_bridge_version) add_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);
	else update_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);

	//create pages
	cc_whmcs_log(0,'Creating pages');
	if (!$cc_whmcs_bridge_version) {
		$pages=array();
		$pages[]=array("WHMCS","WHMCS","*",0);

		$ids="";
		foreach ($pages as $i =>$p)
		{
			$my_post = array();
			$my_post['post_title'] = $p['0'];
			$my_post['post_content'] = '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type'] = 'page';
			$my_post['menu_order'] = 100+$i;
			$my_post['comment_status'] = 'closed';
			$id=wp_insert_post( $my_post );
			if (empty($ids)) { $ids.=$id; } else { $ids.=",".$id; }
			if (!empty($p[1])) add_post_meta($id,'cc_whmcs_bridge_page',$p[1]);
		}
		if (get_option("cc_whmcs_bridge_pages")) update_option("cc_whmcs_bridge_pages",$ids);
		else add_option("cc_whmcs_bridge_pages",$ids);
	}

	restore_error_handler();

	return true;
}

/**
 * Deactivation: nothing to do
 * @return void
 */
function cc_whmcs_bridge_deactivate() {
	$ids=get_option("cc_whmcs_bridge_pages");
	$ida=explode(",",$ids);
	foreach ($ida as $id) {
		wp_delete_post($id);
	}
	$cc_whmcs_bridge_options=cc_whmcs_bridge_options();

	delete_option('cc_whmcs_bridge_log');
	foreach ($cc_whmcs_bridge_options as $value) {
		delete_option( $value['id'] );
	}

	delete_option("cc_whmcs_bridge_log");
	delete_option("cc_whmcs_bridge_ftp_user"); //legacy
	delete_option("cc_whmcs_bridge_ftp_password"); //legacy
	delete_option("cc_whmcs_bridge_version");
	delete_option("cc_whmcs_bridge_pages");
	delete_option('cc-ce-bridge-cp-support-us');
}

function cc_whmcs_bridge_output() {
	global $post;
	global $wpdb;
	global $wordpressPageName;
	global $cc_whmcs_bridge_loaded,$cc_whmcs_bridge_to_include;

	$ajax=false;

	$cf=get_post_custom($post->ID);
	if (isset($_REQUEST['ccce']) && (isset($_REQUEST['ajax']) && $_REQUEST['ajax'])) {
		$cc_whmcs_bridge_to_include=$_REQUEST['ccce'];
		$ajax=intval($_REQUEST['ajax']);
	} elseif (isset($_REQUEST['ccce'])) {
		$cc_whmcs_bridge_to_include=$_REQUEST['ccce'];
	} elseif (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]=='WHMCS') {
		$cc_whmcs_bridge_to_include="index";
	} else {
		$cc_whmcs_bridge_to_include="index";
		//return $content;
	}

	$http=cc_whmcs_bridge_http($cc_whmcs_bridge_to_include);
	cc_whmcs_log('Notification','Call: '.$http);
	//echo '<br />'.$http.'<br />';
	$news = new zHttpRequest($http,'whmcs-bridge-sso');
	if (function_exists('cc_whmcs_bridge_sso_httpHeaders')) $news->httpHeaders=cc_whmcs_bridge_sso_httpHeaders($news->httpHeaders);
	
	if (isset($news->post['whmcsname'])) {
		$news->post['name']=$news->post['whmcsname'];
		unset($news->post['whmcsname']);
	}

	if (!$news->curlInstalled()) {
		cc_whmcs_log('Error','CURL not installed');
		return "cURL not installed";
	} elseif (!$news->live()) {
		cc_whmcs_log('Error','A HTTP Error occured');
		return "A HTTP Error occured";
	} else {
		if ($cc_whmcs_bridge_to_include=='verifyimage') {
			$output=$news->DownloadToString();
			while (count(ob_get_status(true)) > 0) ob_end_clean();
			header("Content-Type: image");
			echo $news->body;
			die();
		} elseif ($cc_whmcs_bridge_to_include=='dl') {
			while (count(ob_get_status(true)) > 0) ob_end_clean();
			$output=$news->DownloadToString();
			header("Content-Disposition: ".$news->headers['content-disposition']);
			header("Content-Type: ".$news->headers['content-type']);
			echo $news->body;
			die();
		} elseif ($ajax==1) {
			while (count(ob_get_status(true)) > 0) ob_end_clean();
			$output=$news->DownloadToString();
			$body=$news->body;
			$body=cc_whmcs_bridge_parser_ajax1($body);
			echo $body;
			die();
		} elseif ($ajax==2) {
			while (count(ob_get_status(true)) > 0) ob_end_clean();
			$output=$news->DownloadToString();
			$body=$news->body;
			$body=cc_whmcs_bridge_parser_ajax2($body);
			header('HTTP/1.1 200 OK');
			echo $body;
			//echo 'it is ajax 2';
			die();
		} elseif ($news->redirect) {
			$output=$news->DownloadToString();
			//echo 'redirect1:'.$news->location.'<br />';

			if ($wordpressPageName) $p=$wordpressPageName;
			else $p='/';
			$f[]='/.*\/([a-zA-Z\_]*?).php.(.*?)/';
			$r[]=get_option('home').$p.'?ccce=$1&$2';
			$f[]='/([a-zA-Z\_]*?).php.(.*?)/';
			$r[]=get_option('home').$p.'?ccce=$1&$2';
			//echo $output.'<br />';

			$output=preg_replace($f,$r,$news->location,-1,$count);

			cc_whmcs_log('Notification','Redirect to: '.$output);
			//echo 'Location:'.$output;
			header('Location:'.$output);
			die();
		} else {
			$output=$news->DownloadToString();
			//die($output);
			return $output;
		}
	}
}

/**
 * Page content filter
 * @param $content
 * @return unknown_type
 */
function cc_whmcs_bridge_content($content) {
	global $cc_whmcs_bridge_content,$post;

	$cf=get_post_custom($post->ID);
	if (isset($_REQUEST['ccce']) || (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]=='WHMCS')) {
		if ($cc_whmcs_bridge_content) {
			$content='';
			ob_start();
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('whmcs-top-page') ) : 
			endif;
			$content.=ob_get_clean();
			$content.=$cc_whmcs_bridge_content['main'];
			ob_start();
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('whmcs-bottom-page') ) : 
			endif;
			$content.=ob_get_clean();
			if (get_option('cc_whmcs_bridge_footer')=='Page') $content.=cc_whmcs_bridge_footer(true);
		}
	}

	return $content;
}

function cc_whmcs_bridge_header() {
	global $cc_whmcs_bridge_content;

	$cc_whmcs_bridge_content=cc_whmcs_bridge_parser();

	if (isset($cc_whmcs_bridge_content['head'])) echo $cc_whmcs_bridge_content['head'];

	echo '<link rel="stylesheet" type="text/css" href="' . CC_WHMCS_BRIDGE_URL . 'cc.css" media="screen" />';
	echo '<script type="text/javascript" src="'. CC_WHMCS_BRIDGE_URL . 'cc.js"></script>';
	if (get_option('cc_whmcs_bridge_css')) {
		echo '<style type="text/css">'.get_option('cc_whmcs_bridge_css').'</style>';
	}
}

function cc_whmcs_bridge_admin_header() {
	echo '<link rel="stylesheet" type="text/css" href="' . CC_WHMCS_BRIDGE_URL . 'cc.css" media="screen" />';
}

function cc_whmcs_bridge_http($page="index") {
	global $wpdb;

	$vars="";
	if ($page=='verifyimage') $http=cc_whmcs_bridge_url().'/includes/'.$page.'.php';
	elseif (isset($_REQUEST['ccce']) && ($_REQUEST['ccce']=='js')) {
		$http=cc_whmcs_bridge_url().'/'.$_REQUEST['js'];
		return $http;
	}
	else $http=cc_whmcs_bridge_url().'/'.$page.'.php';
	$and="";
	if (count($_GET) > 0) {
		foreach ($_GET as $n => $v) {
			if ($n!="page_id" && $n!="ccce")
			{
				$vars.= $and.$n.'='.cc_urlencode($v);
				$and="&";
			}
		}
	}

	$vars.=$and.'systpl=portal';
	$and="&";
	
	if (function_exists('cc_whmcs_bridge_sso_http')) cc_whmcs_bridge_sso_http($vars,$and);

	if ($vars) $http.='?'.$vars;
	
	return $http;
}

function cc_whmcs_bridge_title($title,$id=0) {
	global $cc_whmcs_bridge_content;
	if (!in_the_loop()) return $title;
	if ($id==0) return $title;

	if (isset($cc_whmcs_bridge_content['title'])) return $cc_whmcs_bridge_content['title'];
	else return $title;
}

function cc_whmcs_bridge_default_page($pid) {
	$isPage=false;
	$ids=get_option("cc_whmcs_bridge_pages");
	$ida=explode(",",$ids);
	foreach ($ida as $id) {
		if (!empty($id) && $pid==$id) $isPage=true;
	}
	return $isPage;
}

function cc_whmcs_bridge_mainpage() {
	$ids=get_option("cc_whmcs_bridge_pages");
	$ida=explode(",",$ids);
	return $ida[0];
}

/**
 * Initialization of page, action & page_id arrays
 * @return unknown_type
 */
function cc_whmcs_bridge_init()
{
	ob_start();
	session_start();
	register_sidebars(1,array('name'=>'WHMCS Top Page Widget Area','id'=>'whmcs-top-page',));
	register_sidebars(1,array('name'=>'WHMCS Bottom Page Widget Area','id'=>'whmcs-top-page',));
	if(get_option('cc_whmcs_bridge_jquery')=='wp'){
		wp_enqueue_script('jquery');
	}
}

function cc_whmcs_log($type=0,$msg='',$filename="",$linenum=0) {
	if (get_option('cc_whmcs_bridge_debug')) {
		if (is_array($msg)) $msg=print_r($msg,true);
		$v=get_option('cc_whmcs_bridge_log');
		if (!is_array($v)) $v=array();
		array_unshift($v,array(time(),$type,$msg));
		update_option('cc_whmcs_bridge_log',$v);
	}
}

function cc_whmcs_bridge_url() {
	$url=get_option('cc_whmcs_bridge_url');
	if (substr($url,-1)=='/') $url=substr($url,0,-1);
	return $url;
}

//Kept for compatibility reasons
if (class_exists('zHttpRequest')) {
	class HTTPRequestWHMCS extends zHttpRequest {}
}
