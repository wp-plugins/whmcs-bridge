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
        $buffer=preg_replace('/<script.*jquery.min.js"><\/script>/','',$buffer);
        $buffer=preg_replace('/<script.*jqueryui.js"><\/script>/','',$buffer);
    }

    $whmcs_path = parse_url(cc_whmcs_bridge_url(), PHP_URL_PATH);
    if (substr($whmcs_path, -1) != '/')
        $whmcs_path .= '/';

    // six
    $f[]="/src=\"".preg_quote($whmcs_path, '/')."assets\//";
    $r[]="src=\"{$home}?ccce=js&ajax=1&js=".'assets/$1'.$pid;

    $f[]="/href=\"".preg_quote($whmcs_path, '/')."assets\//";
    $r[]="href=\"{$home}?ccce=js&ajax=1&js=".'assets/$1'.$pid;


    $f[]="/templates\/orderforms\/([a-zA-Z]*?)\/js\/main.js/";
    $r[]=$home."?ccce=js&ajax=2&js=".'templates/orderforms/$1/js/main.js'.$pid;

    ## BootWHMCS
    $f[]="/templates\/orderforms\/([a-zA-Z]*?)\/static\/app.js/";
    $r[]=$home."?ccce=js&ajax=2&js=".'templates/orderforms/$1/static/app.js'.$pid;
    ## BootWHMCS

    $f[]='/href\=\"([a-zA-Z\_]*?).php\?(.*?)\"/';
    $r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

    $f[]="/jQuery.post\(\"([a-zA-Z]*?).php/";
    $r[]="jQuery.post(\"$home?ccce=$1&ajax=1";

    $f[]="/window.location\='([a-zA-Z\_]*?).php.(.*?)'/";
    $r[]="window.location='".$home."?ccce=$1&$2".$pid."'";

    // six
    $f[]="/window.location\ \=\ '([a-zA-Z\_]*?).php.(.*?)'/";
    $r[]="window.location='".$home."?ccce=$1&$2".$pid."'";

    $f[]="/'([a-zA-Z\_]*?).php'/";
    $r[]="'".$home."?ccce=$1".$pid."'";

    $f[]="/\"([a-zA-Z\_]*?).php\"/";
    $r[]="\"".$home."?ccce=$1".$pid."\"";

    $f[]="/'([a-zA-Z\_]*?).php.(.*?)'/";
    $r[]="'".$home."?ccce=$1&$2".$pid."'";

    $f[]="/\"([a-zA-Z\_]*?).php.(.*?)\"/";
    $r[]="\"".$home."?ccce=$1&$2".$pid."\"";
    // six

    $buffer=preg_replace($f,$r,$buffer,-1,$count);

    //verify captcha image
    $buffer=str_replace('"includes/verifyimage.php?', '"'.$home.'?ccce=verifyimage'.$pid.'&',$buffer);

    $buffer=str_replace('url(images', 'url('.cc_whmcs_bridge_url().'/images', $buffer);
    $buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);
    $buffer=str_replace('src="images','src="'.cc_whmcs_bridge_url().'/images',$buffer);
    $buffer=str_replace('background="images','background="'.cc_whmcs_bridge_url().'/images',$buffer);
    $buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);
    $buffer=str_replace('src="templates','src="'.cc_whmcs_bridge_url().'/templates',$buffer);
    // six
    if (stristr($_REQUEST['js'], 'assets') !== false && stristr($_REQUEST['js'], '.css') !== false)
        $buffer=str_replace('../fonts/', cc_whmcs_bridge_url().'/assets/fonts/',$buffer);

    $buffer=str_replace('"cart.php"','"'.$home.'?ccce=cart'.$pid.'"',$buffer);
    $buffer=str_replace('"cart.php?','"'.$home.'?ccce=cart'.$pid.'&"',$buffer);
    $buffer=str_replace("'cart.php?","'".$home."?ccce=cart".$pid.'&',$buffer);

    //jQuery UI
    $buffer=str_replace('href="includes/jscript/css/ui.all.css','href="'.cc_whmcs_bridge_url().'/includes/jscript/css/ui.all.css',$buffer);

    return $buffer;
}

function cc_whmcs_bridge_parser_ajax2($buffer) {
    cc_whmcs_bridge_home($home,$pid);

    $f[]="/.post\(\"([a-zA-Z]*?).php/";
    $r[]=".post(\"$home?ccce=$1&ajax=2";

    $f[]="/.post\('([a-zA-Z]*?).php/";
    $r[]=".post('$home?ccce=$1&ajax=2";

    $f[]='/document.location\=\"([a-zA-Z\_]*?).php.(.*?)\"/';
    $r[]='document.location="'.$home.'?ccce=$1&$2'.$pid.'"';

    $f[]='/window.open\(\"([a-zA-Z\_]*?).php.(.*?)\"/';
    $r[]='window.open("'.$home.'?ajax=1&ccce=$1&$2'.$pid.'"';

    $buffer=preg_replace($f,$r,$buffer,-1,$count);

    $buffer=str_replace('wbteampro.php?', $home.'?ccce=wbteampro&', $buffer);
    $buffer=str_replace('wbteampro.php', $home.'?ccce=wbteampro', $buffer);

    $buffer=str_replace('"cart.php"','"'.$home.'?ccce=cart'.$pid.'"',$buffer);
    $buffer=str_replace("'cart.php?","'".$home."?ccce=cart".$pid.'&',$buffer);

    $buffer=str_replace('url(images', 'url('.cc_whmcs_bridge_url().'/images', $buffer);
    $buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);
    $buffer=str_replace('src="images','src="'.cc_whmcs_bridge_url().'/images',$buffer);
    $buffer=str_replace('background="images','background="'.cc_whmcs_bridge_url().'/images',$buffer);
    $buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);
    $buffer=str_replace('src="templates','src="'.cc_whmcs_bridge_url().'/templates',$buffer);

    $buffer=str_replace('ajax=2?','ajax=2&',$buffer);

    return $buffer;

}

function cc_whmcs_bridge_home(&$home,&$pid,$current=false) {
    global $wordpressPageName,$post;

    if (isset($post) && $current) {
        $pageID=$post->ID;
        $permalink=get_permalink();
        preg_match('/(.*)\?page_id\=(.*)/',$permalink,$matches);
        if (count($matches)==2) {
            $pid='&page_id='.$matches[2];
            $home=$matches[1];
            $url=$permalink;
        } else {
            $pid='';
            $url=$home=$permalink;
        }
    } else {
        $pageID = cc_whmcs_bridge_mainpage();

        if (get_option('permalink_structure')){
            $homePage = get_option('home');

            $wordpressPageName = get_permalink($pageID);

            if (stristr($homePage, 'http://') !== false)
                $homePage2 = str_replace('http://', 'https://', $homePage);
            else
                $homePage2 = str_replace('https://', 'http://', $homePage);

            $wordpressPageName = str_replace(array($homePage, $homePage2),"",$wordpressPageName);

            $pid="";

            $home=$homePage.$wordpressPageName;

            if (substr($home,-1) != '/') $home.='/';
            $url=$home;
        }else{
            $pid='&page_id='.$pageID;
            $home=get_option('home');
            if (substr($home,-1)!='/') $home.='/';
            $url=$home.'?page_id='.$pageID;
        }
    }

    if (function_exists('cc_whmcsbridge_sso_get_lang')) cc_whmcsbridge_sso_get_lang($home,$pid,$url,$wordpressPageName);

    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) {
        $url=str_replace('http://','https://',$url);
        $home=str_replace('http://','https://',$home);
    }

    return $url;
}

function cc_whmcs_bridge_parser($buffer=null,$current=false) {
    global $cc_whmcs_bridge_menu;

    cc_whmcs_bridge_home($home,$pid,$current);

    if (!$buffer) $buffer=cc_whmcs_bridge_output();

    $tmp=explode('://',cc_whmcs_bridge_url(),2);
    $tmp2=explode('/',$tmp[1],2);
    $sub=str_replace($tmp[0].'://'.$tmp2[0],'',cc_whmcs_bridge_url()).'/';
    $secure='&sec=1';

    $whmcs=cc_whmcs_bridge_url();

    if (substr($whmcs,-1) != '/') $whmcs.='/';

    if (strpos($whmcs,'https://')===0) $whmcs=str_replace('https://','http://',$whmcs);
    $whmcs2=str_replace('http://','https://',$whmcs);
    $whmcs3=str_replace('http:', '', $whmcs);

    $html = new iplug_simple_html_dom();
    $html->load($buffer);
    $page_title = $html->find('title', 0);
    if (is_object($page_title) && isset($page_title->plaintext))
        $ret['page_title'] = $page_title->plaintext;

    $whmcs_path = parse_url(cc_whmcs_bridge_url(), PHP_URL_PATH);
    if (substr($whmcs_path, -1) != '/')
        $whmcs_path .= '/';

    $ret['buffer']=$buffer;

    if (get_option('cc_whmcs_bridge_permalinks') && function_exists('cc_whmcs_bridge_parser_with_permalinks') && !$pid) {
        $buffer=cc_whmcs_bridge_parser_with_permalinks($buffer,$home,$pid,$whmcs,$sub,$whmcs2,$whmcs3);
    } else {
        // index.php fixes
            $f[]="/action='index.php.(.*?)/";
            $r[]="action='$home?ccce=index&$1";

            if (stristr($home, 'index.php') === false) {
                $f[]="/index.php.(.*?)/";
                $r[]="$home?ccce=index&$1";
            }

        $loop = array();
        $loop[] = $whmcs;
        if (!is_null($whmcs2))
            $loop[] = $whmcs2;
        if (!is_null($whmcs3))
            $loop[] = $whmcs3;
		if ($whmcs_path != '/')
			$loop[] = $whmcs_path;

        // FULL URLS

        foreach ($loop as $rep_url) {
            $f[]='/value\=\"'.preg_quote($rep_url,'/').'([a-zA-Z\_]*?).php\"/';
            $r[]='value="'.$home.'?ccce=$1'.$pid.'"';

            $f[]='/value\=\"'.preg_quote($rep_url,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
            $r[]='value="'.$home.'?ccce=$1&$2'.$pid.'"';

            $f[]='/href\=\"'.preg_quote($rep_url,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
            $r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

            $f[]='/href\=\"'.preg_quote($rep_url,'/').'([a-zA-Z\_]*?).php\"/';
            $r[]='href="'.$home.'?ccce=$1'.$pid.'"';

            $f[]='/href\=\"'.preg_quote($rep_url,'/').'([a-zA-Z0-9]*?).php\"/';
            $r[]='href="'.$home.'?ccce=$1'.$pid.'"';

            $f[]='/'.preg_quote($rep_url,'/').'([a-zA-Z\_]*?).php/';
            $r[]=''.$home.'?ccce=$1'.$pid;

            // Payment Gateways
            $f[]='/'.preg_quote($rep_url,'/').'modules\/gateways\/([a-zA-Z0-9]*?)\/([a-zA-Z0-9]*?).php/';
            $r[]= $home.'?ajax=1&ccce=modules/gateways/$1/$2';
        }

        # custom paths
        $custom_paths = explode("\n", str_replace("\r\n", "\n", get_option('cc_whmcs_bridge_custom_rules')));

        if (is_array($custom_paths)) {
            foreach ($custom_paths as $pth) {
                if (trim($pth) == '') continue;

                if (substr($pth, 0, 1) == '*') {
                    $pth = substr($pth, 1);
                    $f[] = "\${$pth}(.*?).js\$";
                    $r[] = $home . "?ccce=js&ajax=1&js=" . $pth . '$1.js' . $pid;

                    $f[] = "\${$pth}(.*?).css\$";
                    $r[] = $home . "?ccce=js&ajax=1&js=" . $pth . '$1.css' . $pid;
                } else {
                    $f[] = "\${$pth}(.*?).js\$";
                    $r[] = $home . "?ccce=js&ajax=2&js=" . $pth . '$1.js' . $pid;

                    $f[] = "\${$pth}(.*?).css\$";
                    $r[] = $home . "?ccce=js&ajax=2&js=" . $pth . '$1.css' . $pid;
                }
            }
        }

        # 2factor
            $f[] = '/img src\=\"\/([a-zA-Z]*?)\/([a-zA-Z]*?).php.(.*?)\"/';
            $r[]="img src=\"$home"."?ccce=$2&$3&ajax=2\"";

        # wbteampro
            $f[]='/img src\=\"([a-zA-Z]*?).php.(.*?)\"/';
            $r[]="img src=\"$home"."?ccce=$1&$2&ajax=2\"";

        // SUB FOLDERS
            $f[]='/href\=\"'.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\"/';
            $r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

        // hyperlinks
            $f[]='/href\=\"([a-zA-Z\_]*?).php\?(.*?)\"/';
            $r[]='href="'.$home.'?ccce=$1&$2'.$pid.'"';

            $f[]='/href\=\"([a-zA-Z\_]*?).php\"/';
            $r[]='href="'.$home.'?ccce=$1'.$pid.'"';

        // images
            $f[]='/img src\=\"([a-zA-Z]*?).php.(.*?)\"/';
            $r[]="img src=\"$home"."$1/?$2&ajax=2\"";

        // form posts
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

        // url specific fixes
            $f[]='/"submitticket.php/';
            $r[]='"'.$home.'?ccce=submitticket&ajax=1'.$pid;

        // fixes the register.php
            $f[]='/action\=\"(.|\/*?)register.php\"/';
            $r[]='action="'.$home.'?ccce=register'.$pid.'"';

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

        // js single quotes
            $f[]='/window.location\=\''.'([a-zA-Z\_]*?).php\'/';
            $r[]='window.location=\''.$home.'?ccce=$1'.$pid.'\'';

            $f[]='/window.location\=\''.preg_quote($sub,'/').'([a-zA-Z\_]*?).php.(.*?)\'/';
            $r[]='window.location=\''.$home.'?ccce=$1&$2'.$pid.'\'';

            $f[]='/window.location\=\''.'([a-zA-Z\_]*?).php.(.*?)\'/';
            $r[]='window.location=\''.$home.'?ccce=$1&$2'.$pid.'\'';

            $f[]='/window.location \= \''.'([a-zA-Z\_]*?).php.(.*?)\'/';
            $r[]='window.location = \''.$home.'?ccce=$1'.$pid.'&$2\'';

            $f[]="/.post\(\'([a-zA-Z]*?).php/";
            $r[]=".post('$home?ccce=$1&ajax=1$pid";

            $f[]="/popupWindow\(\'([a-zA-Z]*?).php\?/";
            $r[]="popupWindow('$home?ccce=$1&ajax=1$pid&";

            $f[]='/window.open\(\'([a-zA-Z\_]*?).php.(.*?)\'/';
            $r[]='window.open(\''.$home.'?ajax=1&ccce=$1&$2'.$pid.'\'';

            // quotations using location.href with single quote
            $f[]='/location.href\=\''.'([a-zA-Z\_]*?).php\'/';
            $r[]='location.href=\''.$home.'?ccce=$1'.$pid.'\'';

            $f[]='/location.href\=\''.'([a-zA-Z\_]*?).php.(.*?)\'/';
            $r[]='location.href=\''.$home.'?ccce=$1&$2'.$pid.'\'';

        // js double quotes
            $f[]="/.post\(\"announcements.php/";
            $r[]=".post(\"$home?ccce=announcements&ajax=1$pid";

            $f[]="/.post\(\"submitticket.php/";
            $r[]=".post(\"$home?ccce=submitticket&ajax=1$pid";

            $f[]='/.load\(\"submitticket.php/';
            $r[]='.load("'.$home.'?ccce=submitticket&ajax=1'.$pid;

            $f[]="/.post\(\"([a-zA-Z]*?).php/";
            $r[]=".post(\"$home?ccce=$1&ajax=1$pid";

        // six
            $f[]="/src=\"".preg_quote($whmcs_path, '/')."assets\//";
            $r[]="src=\"{$home}?ccce=js&ajax=1&js=".'assets/$1'.$pid;

            $f[]="/href=\"".preg_quote($whmcs_path, '/')."assets\//";
            $r[]="href=\"{$home}?ccce=js&ajax=1&js=".'assets/$1'.$pid;

        // six modules
            $f[]="/src=\"".preg_quote($whmcs_path, '/')."modules\//";
            $r[]="src=\"{$home}?ccce=js&ajax=1&js=".'modules/$1'.$pid;

            $f[]="/href=\"".preg_quote($whmcs_path, '/')."modules\//";
            $r[]="href=\"{$home}?ccce=js&ajax=1&js=".'modules/$1'.$pid;

            $f[]="/src=\"modules\//";
            $r[]="src=\"{$home}?ccce=js&ajax=1&js=".'modules/$1'.$pid;

            $f[]="/href=\"modules\//";
            $r[]="href=\"{$home}?ccce=js&ajax=1&js=".'modules/$1'.$pid;

        // six templates css/js
            $f[]="/src=\"".preg_quote($whmcs_path, '/')."templates\/([a-zA-Z]*?)\/js\/([a-zA-Z0-9]*?).js/";
            $r[]="src=\"{$home}?ccce=js&ajax=1&js=".'templates/$1/js/$2.js'.$pid;

            $f[]="/href=\"".preg_quote($whmcs_path, '/')."templates\/([a-zA-Z]*?)\/css\/([a-zA-Z0-9]*?).css/";
            $r[]="href=\"{$home}?ccce=js&ajax=1&js=".'templates/$1/css/$2.css'.$pid;

        // orderforms
            $f[]="/src=\"templates\/orderforms\/([a-zA-Z0-9]*?)\/js\/([a-zA-Z]*?).js/";
            $r[]="src=\"{$home}?ccce=js&ajax=1&js=".'templates/orderforms/$1/js/$2.js'.$pid;

            $f[]="/href=\"templates\/orderforms\/([a-zA-Z0-9]*?)\/([a-zA-Z]*?).css/";
            $r[]="href=\"{$home}?ccce=js&ajax=1&js=".'templates/orderforms/$1/$2.css'.$pid;

        // templates css/js
            $f[]="/src=\"templates\/([a-zA-Z]*?)\/js\/([a-zA-Z0-9]*?).js/";
            $r[]="src=\"{$home}?ccce=js&ajax=1&js=".'templates/$1/js/$2.js'.$pid;

            $f[]="/href=\"templates\/([a-zA-Z]*?)\/css\/([a-zA-Z0-9]*?).css/";
            $r[]="href=\"{$home}?ccce=js&ajax=1&js=".'templates/$1/css/$2.css'.$pid;

        ## BootWHMCS
            $f[]="/templates\/orderforms\/([a-zA-Z]*?)\/static\/app.js/";
            $r[]=$home."?ccce=js&ajax=2&js=".'templates/orderforms/$1/static/app.js'.$pid;
        ## BootWHMCS

        // character fixes
            $f[]="/>>/";
            $r[]="&gt;&gt;";

        // 'page' is a Wordpress reserved variable
            $f[]='/href\=\"(.*?)&amp;page\=([0-9]?)"/';
            $r[]='href="$1'.'&whmcspage=$2"';

        // six js links
            $f[]="/'([a-zA-Z\_]*?).php'/";
            $r[]="'".$home."?ccce=$1".$pid."'";

            $f[]="/\"([a-zA-Z\_]*?).php\"/";
            $r[]="\"".$home."?ccce=$1".$pid."\"";

            $f[]="/'([a-zA-Z\_]*?).php.(.*?)'/";
            $r[]="'".$home."?ccce=$1&$2".$pid."'";

            $f[]="/\"([a-zA-Z\_]*?).php.(.*?)\"/";
            $r[]="\"".$home."?ccce=$1&$2".$pid."\"";

        // run regex
        $buffer=preg_replace($f,$r,$buffer,-1,$count);
    }

    // 2factor
        $buffer = str_replace("'clientarea.php'", "'$home?ccce=clientarea'", $buffer);

    //patch issue with &
        $buffer=str_replace('&#038;','&',$buffer);
    
    // some JS not being closed correctly
        $buffer = str_replace("&,", '&",', $buffer);
        $buffer = str_replace("&>", '&">', $buffer);
        $buffer = str_replace("&/>", '&"/>', $buffer);
        $buffer = str_replace("& />", '&" />', $buffer);

    //name is a reserved Wordpress field name
    if (isset($_REQUEST['ccce']) && ($_REQUEST['ccce']=='viewinvoice')) {
        // not in invoice
    } else {
        $buffer=str_replace('name="name"','name="whmcsname"',$buffer);
    }

    // Fix auto forward to payment gateway issue
    $buffer = str_replace('$("#submitfrm").', 'jQuery("#submitfrm").', $buffer);
    $buffer = str_replace("\$('#submitfrm').", "jQuery('#submitfrm').", $buffer);
    // end fix auto forward

    $buffer=str_replace('src="templates','src="'.cc_whmcs_bridge_url().'/templates',$buffer);
    $buffer=str_replace('href="templates','href="'.cc_whmcs_bridge_url().'/templates',$buffer);
    $buffer=str_replace('href="includes','href="'.cc_whmcs_bridge_url().'/includes',$buffer);
    $buffer=str_replace('src="includes','src="'.cc_whmcs_bridge_url().'/includes',$buffer);
    $buffer=str_replace('src="modules','src="'.cc_whmcs_bridge_url().'/modules',$buffer);
    // six
    $buffer=str_replace('src="assets','src="'.cc_whmcs_bridge_url().'/assets',$buffer);

    // proxmox
	$buffer=str_replace('src=\"modules','src=\"'.cc_whmcs_bridge_url().'/modules',$buffer);
	$buffer=str_replace('$(".so_graph', '$("div.so_graph',$buffer);
    
    //import local images
    $buffer=str_replace('src="images','src="'.cc_whmcs_bridge_url().'/images',$buffer);
    $buffer=str_replace('background="images','background="'.cc_whmcs_bridge_url().'/images',$buffer);
    $buffer=str_replace("window.open('images","window.open('".cc_whmcs_bridge_url().'/images',$buffer);

    //verify captcha image
    $buffer=str_replace(cc_whmcs_bridge_url().'/includes/verifyimage.php',$home.'?ccce=verifyimage'.$pid,$buffer);

    if (isset($_REQUEST['ccce']) &&
        (($_REQUEST['ccce']=='viewinvoice' && strstr($buffer, 'invoice.css'))
            || $_REQUEST['ccce']=='announcementsrss')

    ) {
        while (count(ob_get_status(true)) > 0) ob_end_clean();
        echo $buffer;
        die();
    }

    //load WHMCS invoicestyle.css style sheet
    if (get_option('cc_whmcs_bridge_invoicestyle') != 'checked') {
        $buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/invoicestyle.css" \/>/','',$buffer);
    }

    //load WHMCS style.css style sheet
    if (get_option('cc_whmcs_bridge_style') != 'checked') {
        $buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/style.css">/','',$buffer);
        $buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/style.css" \/>/','',$buffer);
    } else {
        $matches=array();
        if (preg_match('/<link.*href="(.*templates\/[a-zA-Z0-9_-]*\/style.css)" \/>/',$buffer,$matches)) {
            $css=$matches[1];
            $output=cc_whmcs_bridge_parser_css($css);
            $buffer=preg_replace('/<link.*templates\/[a-zA-Z0-9_-]*\/style.css" \/>/','<style type="text/css">'.$output.'</style>',$buffer);
        }
    }

    //replaces whmcs jquery so that it doesn't start it twice
    if(in_array(get_option('cc_whmcs_bridge_jquery'),array('checked','wp'))) {
        $buffer=preg_replace('/<script.*jquery.js"><\/script>/','',$buffer);
        $buffer=preg_replace('/<script.*jquery.min.js"><\/script>/','',$buffer);
        $buffer=preg_replace('/<script.*jqueryui.js"><\/script>/','',$buffer);
    }

    //jQuery ui
    $buffer=str_replace('href="includes/jscript/css/ui.all.css','href="'.cc_whmcs_bridge_url().'/includes/jscript/css/ui.all.css',$buffer);

    // Fix url issues
    $surl = str_replace(array('http:', 'https:'), '', $whmcs);
    $buffer=str_replace($surl.$home, $home, $buffer);
    $buffer=str_replace($home.'index/', $home, $buffer);
    $buffer=str_replace($home.'cart/cart.php', $home.'cart', $buffer);
    $buffer=str_replace($home.'serverstatus/serverstatus.php', $home.'serverstatus', $buffer);
    $buffer=str_replace('"cart.php"','"'.$home.'?ccce=cart'.$pid.'"',$buffer);
    $buffer=str_replace('"cart.php?','"'.$home.'?ccce=cart'.$pid.'&',$buffer);
    

    // fix double url problems
    $buffer=str_replace('http:http', 'http', $buffer);
    $buffer=str_replace('https:http', 'http', $buffer);
    $buffer=str_replace('https://http//', 'http://', $buffer);
    $buffer = str_replace($whmcs_path.'http', 'http', $buffer);
    $buffer = str_replace($whmcs_path.'://', '://', $buffer);
    $buffer = str_replace(cc_whmcs_bridge_url().'http', 'http', $buffer);
    $buffer=str_replace('http:http', 'http', $buffer);
    $buffer=str_replace('https:http', 'http', $buffer);

    $html = new iplug_simple_html_dom();
    $html->load($buffer);

    $sidebar=$html->find('div[id=side_menu]', 0) ? trim($html->find('div[id=side_menu]', 0)->innertext) : null;

    if ($sidebar) {
        $pattern = '/<form(.*?)dologin(.*?)>/';
        if (preg_match($pattern,$sidebar,$matches)) {
            $loginForm=$matches[0];
            $sidebar=preg_replace('/(<form(.*?)dologin(.*?)>)(\s*)(<p class.*>)/','$3$1',$sidebar); //swap around the <form> and <p> tags
            $ret['sidebar'][]=$sidebar;
        }
        $sidebarSearch='<p class="header">';
        $sidebarData=explode($sidebarSearch, $sidebar);

        //Remove end paragraph and text headings
        foreach($sidebarData as $count => $data){
            $title='';
            $text = explode('</p>', $data);
            if (count($text) > 0) {
                $title = $text[0];
                unset($text[0]);
                $data = implode('</p>', $text);
            }

            $sidebarData[$count]=$data;
            $sidebarData['mode'][$count-1]=$title;
        }

        $ret['sidebarNav']=@$sidebarData[1]; //QUICK NAVIGATION
        $ret['sidebarAcInf']=@$sidebarData[2]; //ACCOUNT INFORMATION
        $ret['sidebarAcSta']=@$sidebarData[3]; //ACCOUNT STATISTICS
        $ret['mode']=@$sidebarData['mode'];

        if (stristr($ret['sidebarAcInf'], 'type="password"') !== false) {
            $ret['sidebarAcInf'] = $loginForm.$ret['sidebarAcInf'].'</form>';
        }

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
    // contribution northgatewebhosting.co.uk
    if ($carttotal=$html->find('div[id=cart-total]',0)){
        //top menu here
        $carttotal=$carttotal->__toString();
        $carttotal=str_replace('<div id="cart-total">','<div id="cart-total-widget">',$carttotal);
        $ret['carttotal']=$carttotal;
    }
    // contribution northgatewebhosting.co.uk

    //end new change

    foreach ($ret as $key => $val) {
        if (stristr($key, 'sidebar') !== false || stristr($key, 'welcomebox') !== false) {
            if (!is_array($val) && stristr($val, '<form') !== false && stristr($val, '</form') === false) {
                $ret[$key] = $val.'</form>';
            }
        }
    }

    $ret['msg']=$_SESSION;

    return $ret;
}

function cc_whmcs_bridge_parse_url($redir) {
    cc_whmcs_bridge_home($home,$pid,false);
    $whmcs=cc_whmcs_bridge_url();
    if (substr($whmcs,-1) != '/') $whmcs.='/';
    $f[]='/'.preg_quote($whmcs,'/').'viewinvoice\.php\?id\=([0-9]*?)&(.*?)$/';
    if (get_option('cc_whmcs_bridge_permalinks')) $r[]=''.$home.'viewinvoice/?id=$1'.$pid.'';
    else $r[]=''.$home.'?ccce=viewinvoice&id=$1'.$pid.'';
    $newRedir=preg_replace($f,$r,$redir);
    return $newRedir;
}