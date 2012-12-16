<?php
if (!defined('WHMCS_BRIDGE')) define('WHMCS_BRIDGE','WHMCS Bridge');
if (!defined('WHMCS_BRIDGE_COMPANY')) define('WHMCS_BRIDGE_COMPANY','Zingiri');
if (!defined('WHMCS_BRIDGE_PAGE')) define('WHMCS_BRIDGE_PAGE','WHMCS');

define("CC_WHMCS_BRIDGE_VERSION","2.1.3");

$compatibleWHMCSBridgeProVersions=array('2.0.1'); //kept for compatibility with older Pro versions, not used since version 2.0.0

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
	add_action('wp_head','cc_whmcs_bridge_header',10);
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
	$notices=array();
	$files=array();
	$dirs=array();

	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	if ($cc_whmcs_bridge_version && $cc_whmcs_bridge_version != CC_WHMCS_BRIDGE_VERSION) $warnings[]='You downloaded version '.CC_WHMCS_BRIDGE_VERSION.' and need to update your settings (currently at version '.$cc_whmcs_bridge_version.') from the <a href="options-general.php?page=cc-ce-bridge-cp">control panel</a>.';
	$upload=wp_upload_dir();
	if (session_save_path() && !is_writable(session_save_path())) $warnings[]='It looks like PHP sessions are not properly configured on your server, the sessions save path <'.session_save_path().'> is not writable. This may be a false warning, contact us if in doubt.';
	if ($upload['error']) $errors[]=$upload['error'];
	if (!get_option('cc_whmcs_bridge_url')) $warnings[]="Please update your WHMCS connection settings on the plugin control panel";
	if (get_option('cc_whmcs_bridge_debug')) $warnings[]="Debug is active, once you finished debugging, it's recommended to turn this off";
	if (phpversion() < '5') $warnings[]="You are running PHP version ".phpversion().". We recommend you upgrade to PHP 5.3 or higher.";
	if (ini_get("zend.ze1_compatibility_mode")) $warnings[]="You are running PHP in PHP 4 compatibility mode. We recommend you turn this option off.";
	if (!function_exists('curl_init')) $errors[]="You need to have cURL installed. Contact your hosting provider to do so.";

	if (cc_whmcs_bridge_mainpage()) {
		$link=cc_whmcs_bridge_home($home,$pid);
		$is='A WHMCS front end page has been created on your Wordpress site. This page is the main interaction page between Wordpress and WHMCS. ';
		$is.='The full url is:<code>'.$link.'</code>. You can view this page <a href="'.$link.'">here</a>. You can edit the page link by editing the page and changing the permalink. Do not delete this page!';
		$notices[]=$is;
	}

	//if (!get_option('whmcs_bridge_template') || (get_option('whmcs_bridge_template')=='portal')) $notices[]='The embedding of '.WHMCS_BRIDGE_PAGE.' style sheets has now been much improved and we recommend you experiment with turning these options on. See below for "Load '.WHMCS_BRIDGE_PAGE.' style" and "Load '.WHMCS_BRIDGE_PAGE.' invoice style".';

	if (count($warnings) > 0) {
		foreach ($warnings as $message) {
			echo "<div id='zing-warning' style='background-color:greenyellow' class='updated fade'><p><strong>";
			echo WHMCS_BRIDGE.': '.$message.'<br />';
			echo "</strong> "."</p></div>";
		}
	}
	if (count($errors) > 0) {
		foreach ($errors as $message)  {
			echo "<div id='zing-warning' style='background-color:pink' class='updated fade'><p><strong>";
			echo WHMCS_BRIDGE.':'.$message.'<br />';
			echo "</strong> "."</p></div>";
		}
	}
	if (isset($_REQUEST['page']) && ($_REQUEST['page']=='cc-ce-bridge-cp') && count($notices) > 0) {
		foreach ($notices as $message) {
			echo "<div id='zing-warning' style='background-color:lightyellow' class='updated fade'><p><strong>";
			echo $message.'<br />';
			echo "</strong> "."</p></div>";
		}
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
	global $wpdb,$current_user,$wp_rewrite;

	ob_start();
	cc_whmcs_log();
	set_error_handler('cc_whmcs_log');
	error_reporting(E_ALL & ~E_NOTICE);

	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	if (!$cc_whmcs_bridge_version) add_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);
	else update_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);

	//create pages
	if (!$cc_whmcs_bridge_version) {
		cc_whmcs_log(0,'Creating pages');
		$pages=array();
		$pages[]=array(WHMCS_BRIDGE_PAGE.'-bridge',WHMCS_BRIDGE_PAGE,"*",0);

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
		update_option("cc_whmcs_bridge_pages",$ids);
	}

	restore_error_handler();

	$wp_rewrite->flush_rules();

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

function cc_whmcs_bridge_output($page=null) {
	global $post;
	global $wpdb;
	global $wordpressPageName;
	global $cc_whmcs_bridge_loaded,$cc_whmcs_bridge_to_include;

	$ajax=false;

	$cf=get_post_custom($post->ID);
	if ($page) {
		$cc_whmcs_bridge_to_include=$page;
	} elseif (isset($_REQUEST['ccce']) && (isset($_REQUEST['ajax']) && $_REQUEST['ajax'])) {
		$cc_whmcs_bridge_to_include=$_REQUEST['ccce'];
		$ajax=intval($_REQUEST['ajax']);
	} elseif (isset($_REQUEST['ccce'])) {
		$cc_whmcs_bridge_to_include=$_REQUEST['ccce'];
	} elseif (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]==WHMCS_BRIDGE_PAGE) {
		$cc_whmcs_bridge_to_include="index";
	} else {
		$cc_whmcs_bridge_to_include="index";
	}

	$http=cc_whmcs_bridge_http($cc_whmcs_bridge_to_include);

	$news = new bridgeHttpRequest($http,'whmcs-bridge-sso');
	$news->debugFunction='cc_whmcs_log';
	if (function_exists('cc_whmcs_bridge_sso_httpHeaders')) $news->httpHeaders=cc_whmcs_bridge_sso_httpHeaders($news->httpHeaders);

	if (isset($news->post['whmcsname'])) {
		$news->post['name']=$news->post['whmcsname'];
		unset($news->post['whmcsname']);
	}

	$news=apply_filters('bridge_http',$news);

	$news->forceWithRedirect['systpl']=get_option('cc_whmcs_bridge_template') ? get_option('cc_whmcs_bridge_template') : 'portal';

	if ($cc_whmcs_bridge_to_include=='dologin') {
		$news->post['rememberme']='on';
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
			$output=$news->DownloadToString();

			if (!$news->redirect) {
				while (count(ob_get_status(true)) > 0) ob_end_clean();
				$body=$news->body;
				$body=cc_whmcs_bridge_parser_ajax1($body);
				echo $body;
				die();
			} else {
				header('Location:'.$output);
				die();
			}
		} elseif ($ajax==2) {
			while (count(ob_get_status(true)) > 0) ob_end_clean();
			$output=$news->DownloadToString();
			$body=$news->body;
			$body=cc_whmcs_bridge_parser_ajax2($body);
			header('HTTP/1.1 200 OK');
			echo $body;
			die();
		} elseif ($news->redirect) {
			$output=$news->DownloadToString();
			if ($wordpressPageName) $p=$wordpressPageName;
			else $p='/';
			$f[]='/.*\/([a-zA-Z\_]*?).php.(.*?)/';
			$r[]=get_option('home').$p.'?ccce=$1&$2';
			$f[]='/([a-zA-Z\_]*?).php.(.*?)/';
			$r[]=get_option('home').$p.'?ccce=$1&$2';
			$output=preg_replace($f,$r,$news->location,-1,$count);
			cc_whmcs_log('Notification','Redirect to: '.$output);
			header('Location:'.$output);
			die();
		} else {
			if (isset($_REQUEST['aff'])) $news->follow=false;
			$output=$news->DownloadToString();
			if ($news->redirect) {
				header('Location:'.$output);
				die();
			}
			if (isset($_REQUEST['aff']) && isset($news->headers['location'])) {
				if (strstr($news->headers['location'],get_option('home'))) header('Location:'.$news->headers['location']);
				else header('Location:'.get_option('home'));
				die();
			}
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

	if (!is_page()) return $content;


	$cf=get_post_custom($post->ID);
	if (isset($_REQUEST['ccce']) || (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]==WHMCS_BRIDGE_PAGE)) {
		if (!$cc_whmcs_bridge_content) { //support Gantry framework
			$cc_whmcs_bridge_content=cc_whmcs_bridge_parser();
		}
		if ($cc_whmcs_bridge_content) {
			$content='';
			ob_start();
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('whmcs-top-page') ) :
			endif;
			$content.=ob_get_clean();
			$content.='<div id="bridge">';
			$content.=$cc_whmcs_bridge_content['main'];
			$content.='</div><!--end bridge-->';
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
	global $cc_whmcs_bridge_content,$post;

	if (!(isset($post->ID))) return;
	$cf=get_post_custom($post->ID);
	if (isset($_REQUEST['ccce']) || (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]==WHMCS_BRIDGE_PAGE)) {
		//$p='cc_whmcs_bridge_parser_'.get_option('cc_whmcs_bridge_template');
		if (!$cc_whmcs_bridge_content) $cc_whmcs_bridge_content=cc_whmcs_bridge_parser();

		if (isset($cc_whmcs_bridge_content['head'])) echo $cc_whmcs_bridge_content['head'];

		echo '<link rel="stylesheet" type="text/css" href="' . CC_WHMCS_BRIDGE_URL . 'cc.css" media="screen" />';
		echo '<script type="text/javascript" src="'. CC_WHMCS_BRIDGE_URL . 'cc.js"></script>';
		if (get_option('cc_whmcs_bridge_css')) {
			echo '<style type="text/css">'.get_option('cc_whmcs_bridge_css').'</style>';
		}
	}
	if(get_option('cc_whmcs_bridge_jquery')=='wp') echo '<script type="text/javascript">$=jQuery;</script>';
}

function cc_whmcs_bridge_admin_header() {
	echo '<link rel="stylesheet" type="text/css" href="' . CC_WHMCS_BRIDGE_URL . 'cc.css" media="screen" />';
}

function cc_whmcs_bridge_http($page="index") {
	global $wpdb;

	$whmcs=cc_whmcs_bridge_url();
	if (substr($whmcs,-1)!='/') $whmcs.='/';
	if ((strpos($whmcs,'https://')!==0) && isset($_REQUEST['sec']) && ($_REQUEST['sec']=='1')) $whmcs=str_replace('http://','https://',$whmcs);
	$vars="";
	if ($page=='verifyimage') $http=$whmcs.'includes/'.$page.'.php';
	elseif (isset($_REQUEST['ccce']) && ($_REQUEST['ccce']=='js')) {
		$http=$whmcs.$_REQUEST['js'];
		return $http;
	} elseif (substr($page,-1)=='/') $http=$whmcs.substr($page,0,-1);
	else $http=$whmcs.$page.'.php';
	$and="";
	if (count($_GET) > 0) {
		foreach ($_GET as $n => $v) {
			if ($n!="page_id" && $n!="ccce")
			{
				if (is_array($v)) {
					foreach ($v as $n2 => $v2) {
						$vars.= $and.$n.'['.$n2.']'.'='.urlencode($v2);
					}
				}
				else $vars.= $and.$n.'='.urlencode($v);
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
	if (function_exists('cc_whmcsbridge_sso_session')) cc_whmcsbridge_sso_session();
	if (!session_id()) @session_start();
	register_sidebars(1,array('name'=>'WHMCS Top Page Widget Area','id'=>'whmcs-top-page',));
	register_sidebars(1,array('name'=>'WHMCS Bottom Page Widget Area','id'=>'whmcs-top-page',));
	if(get_option('cc_whmcs_bridge_jquery')=='wp'){
		wp_enqueue_script(array('jquery','jquery-ui','jquery-ui-slider','jquery-ui-button'));
	}
}

function cc_whmcs_log($type=0,$msg='',$filename="",$linenum=0) {
	if ($type==0) $type='Debug';
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
if (class_exists('bridgeHttpRequest')) {
	class HTTPRequestWHMCS extends bridgeHttpRequest {}
}
