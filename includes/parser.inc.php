<?php
function cc_whmcs_bridge_parser_css($css) {
	$input=file_get_contents($css);
	$s='';
	$output='';
	$comments=false;
	for ($i=0; $i < strlen($input); $i++) {
		if ($input[$i]=='/' && $input[$i+1]=='*') {
			$comments=true;
			$output.=$s;
			$s='';
		}
		if ($input[$i]=='*' && $input[$i+1]=='/') {
			$comments=false;
			$output.=$s.'*/';
			$i+=2;
			$s='';
		}
		if (!$comments) {
			if ($input[$i]==',') {
				$output.='#bridge '.$s.',';
				$s='';
			} elseif ($input[$i]=='{') {
				$output.='#bridge '.$s.'{';
				$s='';
			} elseif ($input[$i]=='}') {
				$output.=$s.'}';
				$s='';
			} else {
				$s.=$input[$i];

			}
		} else {
			$s.=$input[$i];

		}
	}
	return $output;
}

function cc_whmcs_bridge_parser_ajax1($buffer) {
	cc_whmcs_bridge_home($home,$pid);

	//replaces whmcs jquery so that it doesn't start it twice
	if(in_array(get_option('cc_whmcs_bridge_jquery'),array('checked','wp'))) {
		$buffer=preg_replace('/<script.*jquery.js"><\/script>/','',$buffer);
		$buffer=preg_replace('/<script.*jqueryui.js"><\/script>/','',$buffer);
		//	$buffer=preg_replace('/<link.*ui.all.cs.*\/>/','',$buffer);
	}

	$f[]="/templates\/orderforms\/([a-zA-Z]*?)\/js\/main.js/";
	$r[]=$home."?ccce=js&ajax=2&js=".'templates/orderforms/$1/js/main.js'.$pid;

	$f[]='/href\=\"([a-zA-Z\_]*?).php\?(.*?)\"/';
	$r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

	$f[]="/jQuery.post\(\"([a-zA-Z]*?).php/";
	$r[]="jQuery.post(\"$home?ccce=$1&ajax=1";


	$buffer=preg_replace($f,$r,$buffer,-1,$count);

	$buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);
	$buffer=str_replace('src="images','src="'.cc_whmcs_bridge_url().'/images',$buffer);
	$buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);

	//jQuery UI
	$buffer=str_replace('href="includes/jscript/css/ui.all.css','href="'.cc_whmcs_bridge_url().'/includes/jscript/css/ui.all.css',$buffer);

	return $buffer;
}

function cc_whmcs_bridge_parser_ajax2($buffer) {
	cc_whmcs_bridge_home($home,$pid);

	$buffer=str_replace('"cart.php"','"'.$home.'?ccce=cart'.$pid.'"',$buffer);
	$buffer=str_replace("'cart.php?","'".$home."?ccce=cart".$pid.'&',$buffer);

	return $buffer;

}

function cc_whmcs_bridge_home(&$home,&$pid) {
	global $wordpressPageName;

	$pageID = cc_whmcs_bridge_mainpage();

	if (get_option('permalink_structure')){
		$homePage = get_option('home');
		$wordpressPageName = get_permalink($pageID);
		$wordpressPageName = str_replace($homePage,"",$wordpressPageName);
		$pid="";
		$home=$homePage.$wordpressPageName;
		if (substr($home,-1) != '/') $home.='/';
		$url=$home;
	}else{
		$pid='&page_id='.$pageID;
		$home=get_option('home').'/';
		$url=$home.'?page_id='.$pageID;
	}

	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) {
		$url=str_replace('http://','https://',$url);
	}
	return $url;
}

function cc_whmcs_bridge_parser() {
	global $cc_whmcs_bridge_menu;

	cc_whmcs_bridge_home($home,$pid);

	$buffer=cc_whmcs_bridge_output();

	$tmp=explode('://',cc_whmcs_bridge_url(),2);
	$tmp2=explode('/',$tmp[1],2);
	$sub=str_replace($tmp[0].'://'.$tmp2[0],'',cc_whmcs_bridge_url()).'/';
	$secure='&sec=1';
	
	$whmcs=cc_whmcs_bridge_url();

	if (substr($whmcs,-1) != '/') $whmcs.='/';

	if (strpos($whmcs,'https://')===0) $whmcs=str_replace('https://','http://',$whmcs);
	$whmcs2=str_replace('http://','https://',$whmcs);

	$ret['buffer']=$buffer;
	if (get_option('cc_whmcs_bridge_permalinks') && function_exists('cc_whmcs_bridge_parser_with_permalinks') && !$pid) {
		$buffer=cc_whmcs_bridge_parser_with_permalinks($buffer,$home,$pid,$whmcs,$sub,$whmcs2);
	} else {
		$f[]='/value\=\"'.preg_quote($whmcs,'/').'([a-zA-Z\_]*?).php\"/';
		$r[]='value="'.$home.'?ccce=$1'.$pid.'"';

		$f[]='/value\=\"'.preg_quote($whmcs,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
		$r[]='value="'.$home.'?ccce=$1&$2'.$pid.'"';

		$f[]='/href\=\"'.preg_quote($whmcs,'/').'([a-zA-Z\_]*?).php\"/';
		$r[]='href="'.$home.'?ccce=$1'.$pid.'"';

		$f[]='/href\=\"'.preg_quote($whmcs,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
		$r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

		$f[]='/'.preg_quote($whmcs,'/').'([a-zA-Z\_]*?).php/';
		$r[]=''.$home.'?ccce=$1'.$pid;

		$f[]='/value\=\"'.preg_quote($whmcs2,'/').'([a-zA-Z\_]*?).php\"/';
		$r[]='value="'.$home.'?ccce=$1'.$pid.$secure.'"';

		$f[]='/value\=\"'.preg_quote($whmcs2,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
		$r[]='value="'.$home.'?ccce=$1&$2'.$pid.$secure.'"';

		$f[]='/href\=\"'.preg_quote($whmcs2,'/').'([a-zA-Z\_]*?).php\"/';
		$r[]='href="'.$home.'?ccce=$1'.$pid.$secure.'"';

		$f[]='/href\=\"'.preg_quote($whmcs2,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
		$r[]='href="'.$home.'?ccce=$1&$2'.$pid.$secure.'"';

		$f[]='/'.preg_quote($whmcs2,'/').'([a-zA-Z\_]*?).php/';
		$r[]=''.$home.'?ccce=$1'.$pid.$secure;

		$f[]='/href\=\"'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
		$r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

		$f[]='/href\=\"([a-zA-Z\_]*?).php\?(.*?)\"/';
		$r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

		$f[]='/href\=\"([a-zA-Z\_]*?).php\"/';
		$r[]='href="'.$home.'?ccce=$1'.$pid.'"';

		$f[]='/window.open\(\'([a-zA-Z\_]*?).php.(.*?)\'/';
		$r[]='window.open(\''.$home.'?ajax=1&ccce=$1&$2'.$pid.'\'';

		$f[]='/window.location\=\''.'([a-zA-Z\_]*?).php\'/';
		$r[]='window.location=\''.$home.'?ccce=$1'.$pid.'\'';

		$f[]='/window.location\=\''.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\'/';
		$r[]='window.location=\''.$home.'?ccce=$1&$2'.$pid.'\'';

		$f[]='/window.location\=\''.'([a-zA-Z\_]*?).php.(.*?)\'/';
		$r[]='window.location=\''.$home.'?ccce=$1&$2'.$pid.'\'';

		$f[]='/window.location \= \''.'([a-zA-Z\_]*?).php.(.*?)\'/';
		$r[]='window.location = \''.$home.'?ccce=$1'.$pid.'&$2\'';

		$f[]='/<form(.*?)method\=\"get\"(.*?)action\=\"([a-zA-Z\_]*?).php\"(.*?)>/';
		if (!$pid) $r[]='<form$1method="get"$2action="'.$home.'"$4><input type="hidden" name="ccce" value="$3" />';
		else $r[]='<form$1method="get"$2action="'.$home.'"$4><input type="hidden" name="ccce" value="$3" /><input type="hidden" name="page_id" value="'.cc_whmcs_bridge_mainpage().'"/>';

		$f[]='/action\=\"([a-zA-Z\_]*?).php\?(.*?)\"/';
		$r[]='action="'.$home.'?ccce=$1&$2'.$pid.'"';

		$f[]='/action\=\"([a-zA-Z\_]*?).php\"/';
		$r[]='action="'.$home.'?ccce=$1'.$pid.'"';

		$f[]='/<form(.*?)method\=\"get\"(.*?)action\=\"'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php\"(.*?)>/';
		if (!$pid) $r[]='<form$1method="get"$2action="'.$home.'"$4><input type="hidden" name="ccce" value="$3" />';
		else $r[]='<form$1method="get"$2action="'.$home.'"$4><input type="hidden" name="ccce" value="$3" /><input type="hidden" name="page_id" value="'.cc_whmcs_bridge_mainpage().'"/>';

		$f[]='/action\=\"'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php\"/';
		$r[]='action="'.$home.'?ccce=$1'.$pid.'"';

		$f[]='/action\=\"'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
		$r[]='action="'.$home.'?ccce=$1&$2'.$pid.'"';

		//fixes the register.php
		$f[]='/action\=\"(.|\/*?)register.php\"/';
		$r[]='action="'.$home.'?ccce=register"';

		//remove cart heading
		$f[]='#\<p align\=\"center\" class=\"cartheading\">(?:.*?)\<\/p\>#';
		$r[]='';

		//remove base tag
		$f[]="(\<base\s*href\=(?:\"|\')(?:.*?)(?:\"|\')\s*/\>)";
		$r[]='';

		//remove title tag
		$f[]="/<title>.*<\/title>/";
		$r[]='';

		//remove meta tag
		$f[]="/<meta.*>/";
		$r[]='';

		$f[]="/.post\(\"announcements.php/";
		$r[]=".post(\"$home?ccce=announcements&ajax=1";

		$f[]="/.post\(\"submitticket.php/";
		$r[]=".post(\"$home?ccce=submitticket&ajax=1";

		$f[]="/jQuery.post\(\"([a-zA-Z]*?).php/";
		$r[]="jQuery.post(\"$home?ccce=$1&ajax=1";

		$f[]="/popupWindow\(\'([a-zA-Z]*?).php\?/";
		$r[]="popupWindow('$home?ccce=$1&ajax=1&";

		$f[]="/templates\/orderforms\/([a-zA-Z]*?)\/js\/main.js/";
		$r[]=$home."?ccce=js&ajax=2&js=".'templates/orderforms/$1/js/main.js'.$pid;

		$f[]="/>>/";
		$r[]="&gt;&gt;";

		$f[]='/action\=\".*(\/modules\/gateways\/[a-zA-Z\_]*?).php\?(.*?)\"/';
		$r[]='action="'.$home.'?ccce=$1&$2'.$pid.'"';

		$buffer=preg_replace($f,$r,$buffer,-1,$count);
	}
	//name is a reserved Wordpress field name
	$buffer=str_replace('name="name"','name="whmcsname"',$buffer);

	$buffer=str_replace('src="templates','src="'.cc_whmcs_bridge_url().'/templates',$buffer);
	$buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);
	$buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);

	//import local images
	$buffer=str_replace('src="images','src="'.cc_whmcs_bridge_url().'/images',$buffer);
	$buffer=str_replace("window.open('images","window.open('".cc_whmcs_bridge_url().'/images',$buffer);

	//verify captcha image
	$buffer=str_replace(cc_whmcs_bridge_url().'/includes/verifyimage.php',$home.'?ccce=verifyimage',$buffer);

	//if (isset($_REQUEST['ccce']) && ($_REQUEST['ccce']=='viewinvoice') && !strstr($buffer,'frmlogin')) {
	if (isset($_REQUEST['ccce']) && ($_REQUEST['ccce']=='viewinvoice')) {
		while (count(ob_get_status(true)) > 0) ob_end_clean();
		echo $buffer;
		die();
	}

	//load WHMCS invoicestyle.css style sheet
	if (!get_option('cc_whmcs_bridge_invoicestyle') == 'checked') {
		$buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/style.css" \/>/','',$buffer);
	}

	//load WHMCS style.css style sheet
	if (!get_option('cc_whmcs_bridge_style') == 'checked') {
		$buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/invoicestyle.css">/','',$buffer);
		//$buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/css\/bootstrap.css".*>/','',$buffer);
		//$buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/css\/whmcs.css".*>/','',$buffer);
	} else {
		$matches=array();
		if (preg_match('/<link.*href="(.*templates\/[a-zA-Z0-9_-]*\/style.css)" \/>/',$buffer,$matches)) {
			$css=$matches[1];
			$output=cc_whmcs_bridge_parser_css($css);
			$buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/style.css" \/>/','<style type="text/css">'.$output.'</style>',$buffer);
		}
		/*
		 $s='/<link.*href="(.*templates\/[a-zA-Z0-9_-]*\/css\/bootstrap.css)".*>/';
		 if (preg_match($s,$buffer,$matches)) {
			$css=$matches[1];
			$output=cc_whmcs_bridge_parser_css($css);
			$buffer=preg_replace($s,'<style type="text/css">'.$output.'</style>',$buffer);
			}
			*/
	}

	//replaces whmcs jquery so that it doesn't start it twice
	if(in_array(get_option('cc_whmcs_bridge_jquery'),array('checked','wp'))) {
		$buffer=preg_replace('/<script.*jquery.js"><\/script>/','',$buffer);
		$buffer=preg_replace('/<script.*jqueryui.js"><\/script>/','',$buffer);
		//		$buffer=preg_replace('/<link.*ui.all.cs.*\/>/','',$buffer);
	}

	//jQuery ui
	$buffer=str_replace('href="includes/jscript/css/ui.all.css','href="'.cc_whmcs_bridge_url().'/includes/jscript/css/ui.all.css',$buffer);


	$html = new simple_html_dom();
	$html->load($buffer);
	$sidebar=$html->find('div[id=side_menu]', 0) ? trim($html->find('div[id=side_menu]', 0)->innertext) : null;
	if ($sidebar) {
		$pattern = '/<form.*?dologin.>/';
		if (preg_match($pattern,$sidebar,$matches)) {
			$loginForm=$matches[0];
			$sidebar=preg_replace('/(<form.*?dologin.>)(\s*)(<p class.*>)/','$3$1',$sidebar); //swap around the <form> and <p> tags
			$ret['sidebar'][]=$sidebar;
		}
		$sidebarSearch='<p class="header">';
		$sidebarData=explode($sidebarSearch, $sidebar);

		//Remove end paragraph and text headings
		foreach($sidebarData as $count => $data){
			$title='';
			if (preg_match('/.*<\/p>/',$data,$matches)) {
				$title=substr($matches[0],0,-4);
				$data=str_replace($title.'</p>','',$data);
			}
			$sidebarData[$count]=$data;
			$sidebarData['mode'][$count-1]=$title;

		}
		$ret['sidebarNav']=$sidebarData[1]; //QUICK NAVIGATION
		$ret['sidebarAcInf']=$sidebarData[2]; //ACCOUNT INFORMATION
		$ret['sidebarAcSta']=$sidebarData[3]; //ACCOUNT STATISTICS
		$ret['mode']=$sidebarData['mode'];
	};
	if ($body=$html->find('div[id=content_left]',0)) {
		$title=$body->find('h1',0);
		$ret['title']=$title->innertext;
		$title->outertext='';
		$ret['main']=$body->innertext;
		$ret['main']=str_replace(' class="heading2"',"",$ret['main']);
		$ret['main']=str_replace("<h1>","<h4>",$ret['main']);
		$ret['main']=str_replace("</h1>","</h4>",$ret['main']);
		$ret['main']=str_replace("<h2>","<h4>",$ret['main']);
		$ret['main']=str_replace("</h2>","</h4>",$ret['main']);
		$ret['main']=str_replace("<h3>","<h5>",$ret['main']);
		$ret['main']=str_replace("</h3>","</h5>",$ret['main']);
	} elseif ($body=$html->find('body',0)) {
		$ret['main']=$body->innertext;
	} elseif ($body=$html->find('div',0)) {
		$ret['main']=$body->innertext;
	}
	if ($head=$html->find('head',0)) $ret['head']=$head->innertext;//$buffer;



	//start new change
	if ($topMenu=$html->find('div[id=top_menu] ul',0)){

		//		foreach ($html->find('div[id=top_menu] li') as $div) {
		//		echo 'hi';
		//}
		//die($topMenu);


		//top menu here
		$topMenu=$topMenu->__toString();
		$ret['topNav']=$topMenu;
	}else{
		$ret['topNav']="";
	}
	if ($welcomebox=$html->find('div[id=welcome_box]',0)){
		//top menu here
		$welcomebox=$welcomebox->__toString();
		$welcomebox=str_replace("&nbsp;","",$welcomebox);
		$welcomebox=str_replace("</div>","",$welcomebox);
		$welcomebox=str_replace('<div id="welcome_box">',"",$welcomebox);
		$welcomebox=preg_replace("/<img[^>]+\>/i", " | ", $welcomebox);
		$welcomebox='<div class="search_engine">'.$welcomebox;
		$welcomebox=$welcomebox."</div>";
		$ret['welcomebox']=$welcomebox;
	}
	//end new change
	$ret['msg']=$_SESSION;

	return $ret;
}