<?php
/*
 Plugin Name: WHMCS Bridge
 Plugin URI: http://www.choppedcode.com/Forum
 Description: WHMCS Bridge is a plugin that integrates the powerfull WHMCS support and billing software with Wordpress.

 Author: EBO
 Version: 1.0.7
 Author URI: http://www.choppedcode.com/
 */

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', '1');

define("CC_WHMCS_BRIDGE_VERSION","1.0.7");
define("CC_WHMCS_VERSION","4.0");

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

define("CC_WHMCS_BRIDGE_URL", WP_CONTENT_URL . "/plugins/".CC_WHMCS_BRIDGE_PLUGIN."/");

$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
if ($cc_whmcs_bridge_version) {
	add_action("init","cc_whmcs_bridge_init");
	if (get_option('cc_whmcs_bridge_footer')=='Site') add_filter('wp_footer','cc_footers');
	add_filter('the_content', 'cc_whmcs_bridge_content', 10, 3);
	add_filter('the_title', 'cc_whmcs_bridge_title');
	add_action('wp_head','cc_whmcs_bridge_header');
	add_action("plugins_loaded", "cc_whmcs_sidebar_init");
}
register_activation_hook(__FILE__,'cc_whmcs_bridge_activate');
register_deactivation_hook(__FILE__,'cc_whmcs_bridge_deactivate');

require_once(dirname(__FILE__) . '/includes/errorlog.class.php');
require_once(dirname(__FILE__) . '/includes/shared.inc.php');
require_once(dirname(__FILE__) . '/includes/http.class.php');
require_once(dirname(__FILE__) . '/includes/footer.inc.php');
require_once(dirname(__FILE__) . '/includes/integrator.inc.php');
require_once(dirname(__FILE__) . '/bridge_cp.php');
require_once(dirname(__FILE__) . '/includes/simple_html_dom.php');

$zErrorLog=new zErrorLog();

function cc_whmcs_bridge_check() {
	global $wpdb;
	$errors=array();
	$warnings=array();
	$files=array();
	$dirs=array();

	foreach ($files as $file) {
		if (!is_writable($file)) $warnings[]='File '.$file.' is not writable, please chmod to 666';
	}

	$dirs[]=dirname(__FILE__).'/cache';
	foreach ($dirs as $file) {
		if (!is_writable($file)) $errors[]='Directory '.$file.' is not writable, please chmod to 777';
	}

	if (!get_option('cc_whmcs_bridge_url')) $warnings[]="Please update your WHMCS connection settings on the plugin control panel";
	if (get_option('cc_whmcs_bridge_debug')) $warnings[]="Debug is active, once you finished debugging, it's recommended to turn this off";
	if (phpversion() < '5')	$warnings[]="You are running PHP version ".phpversion().". We recommend you upgrade to PHP 5.3 or higher.";
	if (ini_get("zend.ze1_compatibility_mode")) $warnings[]="You are running PHP in PHP 4 compatibility mode. We recommend you turn this option off.";
	if (!function_exists('curl_init')) $errors[]="You need to have cURL installed. Contact your hosting provider to do so.";
	@session_start();
	if (!session_id()) $errors[]='Sessions are not working on your installation, make sure they are turned on.';
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
	global $wpdb,$zErrorLog,$current_user;

	$eaw=cc_whmcs_bridge_check();
	if (count($eaw['errors']) > 0) return false;

	ob_start();
	$zErrorLog->clear();
	set_error_handler(array($zErrorLog,'log'));
	error_reporting(E_ALL & ~E_NOTICE);

	$prefix = $wpdb->prefix."cc_mybb_";

	$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
	if (!$cc_whmcs_bridge_version) add_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);
	else update_option("cc_whmcs_bridge_version",CC_WHMCS_BRIDGE_VERSION);

	//create pages
	$zErrorLog->msg('Creating pages');
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
}

/**
 * Uninstallation: removal of database tables
 * @return void
 */
function cc_whmcs_bridge_uninstall() {
	$ids=get_option("cc_whmcs_bridge_pages");
	$ida=explode(",",$ids);
	foreach ($ida as $id) {
		wp_delete_post($id);
	}
	delete_option("cc_whmcs_bridge_log");
	delete_option("cc_whmcs_bridge_version");
	delete_option("cc_whmcs_bridge_pages");
	delete_option("cc_mybb_version");
}

function cc_whmcs_bridge_output() {
	global $post;
	global $wpdb;

	global $cc_whmcs_bridge_loaded,$cc_whmcs_bridge_to_include;

	$cf=get_post_custom($post->ID);
	if (isset($_REQUEST['ccce']))
	{
		$cc_whmcs_bridge_to_include=$_REQUEST['ccce'];
	}
	elseif (isset($cf['cc_whmcs_bridge_page']) && $cf['cc_whmcs_bridge_page'][0]=='WHMCS')
	{
		$cc_whmcs_bridge_to_include="index";
	}
	else
	{
		$cc_whmcs_bridge_to_include="index";
		//return $content;
	}

	$http=cc_whmcs_bridge_http($cc_whmcs_bridge_to_include);
	cc_whmcs_log('Notification','Call: '.$http);
	//echo '<br />'.$http.'<br />';
	$news = new HTTPRequestWHMCS($http);
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
		$output=$news->DownloadToString(true,false);
		//echo $output;
		if ($news->redirect) {
			//echo 'redirect1';
				
			$f[]='/.*\/([a-zA-Z\_]*?).php.(.*?)/';
			$r[]=get_option('home').'/index.php?ccce=$1&$2';
			$f[]='/([a-zA-Z\_]*?).php.(.*?)/';
			$r[]=get_option('home').'/index.php?ccce=$1&$2';
			//echo $output.'<br />';
			
			$output=preg_replace($f,$r,$output,-1,$count);
			cc_whmcs_log('Notification','Redirect to: '.$output);
			
			header('Location:'.$output);
			//echo $output;
			die();
		}
		return $output;
	}
}

function cc_whmcs_bridge_http($page="index") {
	global $wpdb;

	$vars="";
	$http=cc_whmcs_bridge_url().'/'.$page.'.php';
	$and="";
	if (count($_GET) > 0) {
		foreach ($_GET as $n => $v) {
			if ($n!="page_id" && $n!="ccceadmin")
			{
				$vars.= $and.$n.'='.cc_urlencode($v);
				$and="&";
			}
		}
	}
	$vars.=$and.'cc_url='.cc_urlencode(get_option('home'));
	$vars.='&ce_url='.cc_urlencode(cc_whmcs_bridge_url());
	$vars.='&cc_site_url='.cc_urlencode(CC_WHMCS_BRIDGE_URL);
	if ($get && $vars) $vars.='&';
	if ($get) $vars.=$get;
	if ($vars) $http.='?'.$vars;

	return $http;
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
			$content=$cc_whmcs_bridge_content['main'];
			if (get_option('cc_whmcs_bridge_footer')=='Page') $content.=cc_footers(true);
		}
	}

	return $content;
}


function cc_whmcs_bridge_title($title,$id=0) {
	global $cc_whmcs_bridge_content;
	if (!in_the_loop()) return $title;
	if (!cc_whmcs_bridge_default_page($post->ID) || $id==0 || ($id != $post->ID)) return $title;
	
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

/**
 * Header hook: loads FWS addons and css files
 * @return unknown_type
 */
function cc_whmcs_bridge_header()
{
	global $cc_whmcs_bridge_content;
	global $cc_whmcs_bridge_menu;
	$pid='&page_id='.cc_whmcs_bridge_mainpage();
	$home=get_option('home').'/';
	$buffer=cc_whmcs_bridge_output();

	$tmp=explode('://',cc_whmcs_bridge_url(),2);
	$tmp2=explode('/',$tmp[1],2);
	$sub=str_replace($tmp[0].'://'.$tmp2[0],'',cc_whmcs_bridge_url()).'/';

	$ret['buffer']=$buffer;

	$f[]='/thisshouldneveroccur/';
	$r[]='';
	$f[]='/href\="'.preg_quote($_GET['ce_url'],'/').'\/([a-zA-Z\_]*?).php"/';
	$r[]='href="'.$home.'index.php?ccce=$1'.$pid.'"';

	$f[]='/href\="'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)"/';
	$r[]='href="'.$home.'index.php?ccce=$1&$2'.$pid.'"';

	$f[]='/href\="([a-zA-Z\_]*?).php\?(.*?)"/';
	$r[]='href="'.$home.'index.php?ccce=$1&$2'.$pid.'"';

	$f[]='/href\="([a-zA-Z\_]*?).php"/';
	$r[]='href="'.$home.'index.php?ccce=$1'.$pid.'"';

	$f[]='/window.location\=\''.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\'/';
	$r[]='window.location=\''.$home.'index.php?ccce=$1&$2'.$pid.'\'';

	$f[]='/window.location\=\''.'([a-zA-Z\_]*?).php.(.*?)\'/';
	$r[]='window.location=\''.$home.'index.php?ccce=$1&$2'.$pid.'\'';

	$f[]='/window.location\=\''.'([a-zA-Z\_]*?).php\'/';
	$r[]='window.location=\''.$home.'index.php?ccce=$1'.$pid.'\'';

	$f[]='/window.location \= \''.'([a-zA-Z\_]*?).php.(.*?)\'/';
	$r[]='window.location = \''.$home.'index.php?ccce=$1&$2'.$pid.'\'';

	$f[]='/action\="([a-zA-Z\_]*?).php\?(.*?)"/';
	$r[]='action="'.$home.'index.php?ccce=$1&$2'.$pid.'"';

	$f[]='/action\="([a-zA-Z\_]*?).php"/';
	$r[]='action="'.$home.'index.php?ccce=$1'.$pid.'"';

	$f[]='/action\="'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)"/';
	$r[]='action="'.$home.'index.php?ccce=$1&$2'.$pid.'"';

	$buffer=preg_replace($f,$r,$buffer,-1,$count);

	//name is a reserved Wordpress field name
	$buffer=str_replace('name="name"','name="whmcsname"',$buffer);
	
	$buffer=str_replace('src="templates','src="'.cc_whmcs_bridge_url().'/templates',$buffer);
	$buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);
	$buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);
	
	if ($_REQUEST['ccce']=='viewinvoice') {
		echo $buffer;
		die();
	}
	
	$buffer=str_replace('style.css','unknown.css',$buffer);
	
	$html = new simple_html_dom();
	$html->load($buffer);
	$sidebar=$html->find('div[id=side_menu]', 0);
	if ($sidebar) $ret['sidebar'][]=$sidebar->__toString();
	if ($body=$html->find('div[id=content_left]',0)) {
		$title=$body->find('h1',0);
		$ret['title']=$title->innertext;
		$title->outertext='';
		$ret['main']=$body->__toString();//$buffer;
	}
	if ($head=$html->find('head',0)) $ret['head']=$head->__toString();//$buffer;
	
	$ret['msg']=$_SESSION;

	$cc_whmcs_bridge_content=$ret;
	echo $cc_whmcs_bridge_content['head'];
	echo '<link rel="stylesheet" type="text/css" href="' . CC_WHMCS_BRIDGE_URL . 'cc.css" media="screen" />';
}

function cc_whmcs_bridge_mainpage() {
	$ids=get_option("cc_whmcs_bridge_pages");
	$ida=explode(",",$ids);
	return $ida[0];
}

/**
 * Register sidebar widgets
 * @return unknown_type
 */
function cc_whmcs_sidebar_init()
{
	wp_register_sidebar_widget('WHMCS Main','WHMCS Main', 'cc_whmcs_sidebar_main');
}

function cc_whmcs_sidebar_main($args) {
	global $cc_whmcs_bridge_content;
	if (is_array($args)) extract($args);
	echo $before_widget;
	echo $before_title;
	echo 'WHMCS Main';
	echo $after_title;
	echo $cc_whmcs_bridge_content['sidebar'][0];
	echo $after_widget;
}

/**
 * Initialization of page, action & page_id arrays
 * @return unknown_type
 */
function cc_whmcs_bridge_init()
{
	ob_start();
	session_start();
}

function cc_whmcs_log($type,$msg) {
	if (get_option('cc_whmcs_bridge_debug')) {
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
?>