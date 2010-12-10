<?php
if (!class_exists('HTTPRequest')) {
	class HTTPRequest
	{
		var $_fp;        // HTTP socket
		var $_url;        // full URL
		var $_host;        // HTTP host
		var $_protocol;    // protocol (HTTP/HTTPS)
		var $_uri;        // request URI
		var $_port;        // port
		var $error;
		var $errno=false;
		var $post=array();	//post variables, defaults to $_POST
		var $redirect=false;

		// scan url
		function _scan_url()
		{
			$req = $this->_url;

			$pos = strpos($req, '://');
			$this->_protocol = strtolower(substr($req, 0, $pos));

			$req = substr($req, $pos+3);
			$pos = strpos($req, '/');
			if($pos === false)
			$pos = strlen($req);
			$host = substr($req, 0, $pos);

			if(strpos($host, ':') !== false)
			{
				list($this->_host, $this->_port) = explode(':', $host);
			}
			else
			{
				$this->_host = $host;
				$this->_port = ($this->_protocol == 'https') ? 443 : 80;
			}

			$this->_uri = substr($req, $pos);
			if($this->_uri == '')
			$this->_uri = '/';
		}

		// constructor
		function HTTPRequest($url="",$login=false)
		{
			if (!$url) return;
			$this->login=$login;
			$this->_url = $url;
			$this->_scan_url();
			$this->post=$_POST;
		}

		//check if server is live
		function live() {
			if (ip2long($this->_host)) return true; //in case using an IP instead of a host name
			$url=($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host;
			if (gethostbyname($url) == $url) return false;
			else return true;
		}

		//check if cURL installed
		function curlInstalled() {
			if (!function_exists('curl_init')) return false;
			else return true;
		}
		
		//check destination is reachable
		function checkConnection() {
			$output=$this->DownloadToString_curl();
			if ($output=='zingiri') return true;
			else return false;
		}

		// download URL to string
		function DownloadToString($withHeaders=false,$withCookies=false)
		{
			//$withHeaders=false;
			$newfiles=array();
				
			@session_start();
			if (file_exists(dirname(__FILE__).'/../cache')) {
				if (!$_SESSION['tmpfile']) {
					$_SESSION['tmpfile']=create_sessionid(16,1);
					$ckfile=dirname(__FILE__).'/../cache/'.$_SESSION['tmpfile'].md5($_SESSION['tmpfile']).'.tmp';
					$fh = fopen($ckfile, 'w');
					fclose($fh);
				} else {
					$ckfile=dirname(__FILE__).'/../cache/'.$_SESSION['tmpfile'].md5($_SESSION['tmpfile']).'.tmp';
				}
			}
			$cainfo=dirname(__FILE__).'/../certs/'.$this->_host.'.crt';
			$ch = curl_init();    // initialize curl handle
			$url=$this->_protocol.'://'.$this->_host.$this->_uri;
			curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			if ($withHeaders) curl_setopt($ch, CURLOPT_HEADER, 1);

			if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
				//	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects - not allowed if safe mode
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60); // times out after 10s
			if ($this->_protocol == "https") {
				if (file_exists($cainfo)) {
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
					curl_setopt($ch, CURLOPT_CAINFO, $cainfo);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				} else {
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				}
			}
			if ($withCookies && isset($_COOKIE)) {
				$cookies="";
				foreach ($_COOKIE as $i => $v) {
					if ($i!='zing') {
						if ($cookies) $cookies.=';';
						$cookies.=$i.'='.$v;
					}
				}
				curl_setopt($ch, CURLOPT_COOKIE, $cookies);
			}
			curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
			curl_setopt ($ch, CURLOPT_COOKIEFILE, $ckfile);

			if (count($_FILES) > 0) {
				//print_r($_FILES);
				//$this->post['z_FILES']=$_FILES;
				foreach ($_FILES as $name => $file) {
					if ($file['tmp_name']) {
						$newfile=dirname(__FILE__).'/../cache/'.$file['name'];
						$newfiles[]=$newfile;
						//echo 'copy '.$file['tmp_name'].' to '.$newfile;
						copy($file['tmp_name'],$newfile);
						if ($file['tmp_name']) $this->post[$name]='@'.$newfile;
					}
				}
				//print_r($_FILES);
			}
			if (count($this->post) > 0) {
				curl_setopt($ch, CURLOPT_POST, 1); // set POST method
				$post="";
				$apost=array();
				foreach ($this->post as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $k2 => $v2) {
							if (is_array($v2)) {
								foreach ($v2 as $k3 => $v3) {
									if ($post) $post.='&';
									$post.=$k.'['.$k2.']'.'['.$k3.']'.'='.urlencode(stripslashes($v3));
									$apost[$k.'['.$k2.']'.'['.$k3.']']=stripslashes($v3);
								}
							} else {
								if ($post) $post.='&';
								$post.=$k.'['.$k2.']'.'='.urlencode(stripslashes($v2));
								$key='['.$k.']['.$k2.']';
								$apost[$k.'['.$k2.']']=stripslashes($v2);
							}
						}

					} else {
						if ($post) $post.='&';
						$post.=$k.'='.urlencode(stripslashes($v));
						$apost[$k]=stripslashes($v);
					}
				}
			}

			if (count($post) > 0) curl_setopt($ch, CURLOPT_POSTFIELDS, $apost); // add POST fields

			$data = curl_exec($ch); // run the whole process
			if (curl_errno($ch)) {
				$this->errno=curl_errno($ch);
				$this->error=curl_error($ch);
				die('Error:'.$this->errno.'/'.$this->error);
			}
			$info=curl_getinfo($ch);
				
			//remove temporary upload files
			if (count($newfiles) > 0) {
				foreach ($newfiles as $file) {
					unlink($file);
				}
			}

			curl_close($ch);
			
//die($data);
			if ($withHeaders) {
				list($header,$result)=explode("<", $data, 2);
				if ($result) $result='<'.$result;
				$matches = array();
				preg_match('/(Location:|URI:)(.*?)\n/', $header, $matches);
				//print_r($matches);
				if (count($matches) > 0) {
					$this->redirect=true;
					return $matches[2];
				}
			} else {
				//$result=json_decode($data,true);
				$result=$data;
			}
			//die($data);
			//var_dump($result);
			//if (empty($header)) return "";
			//echo '<br />--'.$header;
			$lookup='<meta http-equiv="refresh" content="2;URL=';
			$i=strpos($data,$lookup);
			if ($i!==false) {
				$l=strlen($lookup);
				$s=substr($data,$i+$l);
				$j=strpos($s,'" />');
				$refresh=htmlspecialchars_decode(substr($s,0,$j));
				if ($refresh != "") {
					$this->redirect=true;
					return "Location: ".$refresh;
				}
			}

			//echo '<br />--';
			return $result;
		}
	}
}
?>