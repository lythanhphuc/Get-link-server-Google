<?php
error_reporting(E_ERROR | E_PARSE);
// Code đã được edit bởi Kai0205 - http://lythanhphuc.com , Download code miễn phí tại ltpvn.net

function curl($url){
		$ch = @curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$head[] = "Connection: keep-alive";
		$head[] = "Keep-Alive: 300";
		$head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$head[] = "Accept-Language: en-us,en;q=0.5";
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		$page = curl_exec($ch);
		curl_close($ch);
		return $page;
}

function cut_str($str, $left, $right){
	$str = substr(stristr($str, $left) , strlen($left));
	$leftLen = strlen(stristr($str, $right));
	$leftLen = $leftLen ? -($leftLen) : strlen($str);
	$str = substr($str, 0, $leftLen);
	return $str;
}

$url = $_GET['link'];

if(isset($url)){
	$curTemp = curl($url);
	$curTemp = cut_str($curTemp,'{"79468658":[[','"]');
	$curTemp = str_replace('\u003d','=', $curTemp);
	$curTemp = str_replace('\u0026','&', $curTemp);
	$curTemp = urldecode($curTemp);
	if ($curTemp <> "") {
		$curList = explode("&",$curTemp);
		foreach ($curList as $curl) {
		$curl = trim(substr($curl, strpos($curl,'https')-strlen($curl)));
			if ($curl <> "" ){
				if (strpos($curl,'itag=37') || strpos($curl,'=m37') !== false) {$v1080p=$curl;}
				if (strpos($curl,'itag=22') || strpos($curl,'=m22') !== false) {$v720p=$curl;}
				if (strpos($curl,'itag=18') || strpos($curl,'=m18') !== false) {$v360p=$curl;}
			}
		}
		if($v1080p){
			echo '<font color="red">1080p:</font> '.$v1080p.'<br><font color="red">720p:</font> '.$v720p.'<br><font color="red">360p:</font> '.$v360p;
		} elseif($v720p){
			echo '<font color="red">720p:</font> '.$v720p.'<br><font color="red">360p:</font> '.$v360p;
		} else {
			echo '<font color="red">360p:</font> '.$v360p;
		}
	}
}

?>
