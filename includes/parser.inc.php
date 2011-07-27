<?php
function cc_whmcs_bridge_parser_ajax1($buffer) {
	cc_whmcs_bridge_home($home,$pid);
	
	$buffer=str_replace('templates/orderforms/slider/js/main.js',$home.'?ccce=js&ajax=2&js='.urlencode('templates/orderforms/slider/js/main.js').$pid,$buffer);
	$buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);
	//$buffer=str_replace('src="templates','src="'.cc_whmcs_bridge_url().'/templates',$buffer);
	//$buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);
	//$buffer=str_replace('href="includes','href="'.cc_whmcs_bridge_url().'/includes',$buffer);

	return $buffer;
}

function cc_whmcs_bridge_parser_ajax2($buffer) {
	cc_whmcs_bridge_home($home,$pid);
	
	//$f[]="/jQuery.post\(\"([a-zA-Z]*?).php/";
	//$r[]="jQuery.post(\"$home?ccce=$1";
	$buffer=str_replace('"cart.php"','"'.$home.'?ccce=cart'.$pid.'"',$buffer);
	$buffer=str_replace("'cart.php?","'".$home."?ccce=cart".$pid.'&',$buffer);
	//$f[]='#\'cart.php\?#';
	//$r[]='\''.$home.'?ccce=cart&';

	//$buffer=preg_replace($f,$r,$buffer,-1,$count);

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
	}else{
		$pid='&page_id='.$pageID;
		$home=get_option('home').'/';
	}
}

function cc_whmcs_bridge_parser() {
	global $cc_whmcs_bridge_menu;
	
	cc_whmcs_bridge_home($home,$pid);

	$buffer=cc_whmcs_bridge_output();

	$tmp=explode('://',cc_whmcs_bridge_url(),2);
	$tmp2=explode('/',$tmp[1],2);
	$sub=str_replace($tmp[0].'://'.$tmp2[0],'',cc_whmcs_bridge_url()).'/';

	$ret['buffer']=$buffer;

	$f[]='/thisshouldneveroccur/';
	$r[]='';

	$f[]='/href\=\"'.preg_quote($_GET['ce_url'],'/').'\/([a-zA-Z\_]*?).php\"/';
	$r[]='href="'.$home.'?ccce=$1'.$pid.'"';

	$f[]='/href\=\"'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
	$r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

	$f[]='/href\=\"([a-zA-Z\_]*?).php\?(.*?)\"/';
	$r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

	$f[]='/href\=\"([a-zA-Z\_]*?).php\"/';
	$r[]='href="'.$home.'?ccce=$1'.$pid.'"';

	$f[]='/window.location\=\''.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\'/';
	$r[]='window.location=\''.$home.'?ccce=$1&$2'.$pid.'\'';

	$f[]='/window.location\=\''.'([a-zA-Z\_]*?).php.(.*?)\'/';
	$r[]='window.location=\''.$home.'?ccce=$1&$2'.$pid.'\'';

	$f[]='/window.location\=\''.'([a-zA-Z\_]*?).php\'/';
	$r[]='window.location=\''.$home.'?ccce=$1'.$pid.'\'';

	$f[]='/window.location \= \''.'([a-zA-Z\_]*?).php.(.*?)\'/';
	$r[]='window.location = \''.$home.'?ccce=$1&$2'.$pid.'\'';

	$f[]='/action\=\"([a-zA-Z\_]*?).php\?(.*?)\"/';
	$r[]='action="'.$home.'?ccce=$1&$2'.$pid.'"';

	$f[]='/action\=\"([a-zA-Z\_]*?).php\"/';
	$r[]='action="'.$home.'?ccce=$1'.$pid.'"';

	$f[]='/action\=\"'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
	$r[]='action="'.$home.'?ccce=$1&$2'.$pid.'"';

	//fixes the cart.php
	$f[]='#\'cart.php\?#';
	$r[]='\''.$home.'?ccce=cart&';

	//remove cart heading
	$f[]='#\<p align\=\"center\" class=\"cartheading\">(?:.*?)\<\/p\>#';
	$r[]='';

	//replace html head base
	$f[]="(\<base\s*href\=(?:\"|\')(?:.*?)(?:\"|\')\s*/\>)";
	$r[]='<base href="'.get_option('home').'">';

	//jQuery
	//jQuery.post("cart.php", 'ajax=1&a=add&pid=31&'+jQuery("#domainfrm").serialize(),

	//$f[]="/jQuery.post\(([a-zA-Z)*?.php/";
	//$r[]="jQuery.post\([hither.php";

	$f[]="/jQuery.post\(\"([a-zA-Z]*?).php/";
	$r[]="jQuery.post(\"$home?ccce=$1";

	//$f[]='/function completedomain/';
	//$r[]='function completedomain2';

	$buffer=preg_replace($f,$r,$buffer,-1,$count);

	//name is a reserved Wordpress field name
	$buffer=str_replace('name="name"','name="whmcsname"',$buffer);

	$buffer=str_replace('src="templates','src="'.cc_whmcs_bridge_url().'/templates',$buffer);
	$buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);
	$buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);
	//import local images
	$buffer=str_replace('src="images','src="'.cc_whmcs_bridge_url().'/images',$buffer);

	//verify captcha image
	$buffer=str_replace(cc_whmcs_bridge_url().'/includes/verifyimage.php',$home.'?ccce=verifyimage',$buffer);

	if ($_REQUEST['ccce']=='viewinvoice') {
		echo $buffer;
		die();
	}

	//load WHMCS style.css style sheet
	if (!get_option('cc_whmcs_bridge_style') == 'checked') {
		$buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/style.css" \/>/','',$buffer);
	}

	//replaces whmcs jquery so that it doesn't start it twice
	if(get_option('cc_whmcs_bridge_jquery')=='checked'){
		$buffer=preg_replace('/<script.*jquery.js"><\/script>/','',$buffer);
	}

	$html = new simple_html_dom();
	$html->load($buffer);
	$sidebar=trim($html->find('div[id=side_menu]', 0)->innertext);
	if ($sidebar) {
		//$ret['sidebar'][]=$sidebar->__toString();
		//$sidebarData=str_replace()
		//<div id="side_menu">
		//start sidebar change
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
		//start change
		//$ret['main']=$body->__toString();//$buffer;
		$body->__toString();
		$body=str_replace(' class="heading2"',"",$body);
		$body=str_replace("<h1>","<h4>",$body);
		$body=str_replace("</h1>","</h4>",$body);
		$body=str_replace("<h2>","<h4>",$body);
		$body=str_replace("</h2>","</h4>",$body);
		$body=str_replace("<h3>","<h5>",$body);
		$body=str_replace("</h3>","</h5>",$body);
		$ret['main']=$body;//$buffer;
		//end change

	}
	if ($head=$html->find('head',0)) $ret['head']=$head->__toString();//$buffer;

	//start new change
	if ($topMenu=$html->find('div[id=top_menu]',0)){
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