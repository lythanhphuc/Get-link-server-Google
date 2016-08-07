<?php
// Code đã được edit lại bởi Kai0205 - http://lythanhphuc.com , Download code miễn phí tại ltpvn.net

error_reporting(E_ERROR | E_PARSE);

function curl($url){
		$ch = @curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		$head[] = "Connection: keep-alive";
		$head[] = "Keep-Alive: 300";
		$head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$head[] = "Accept-Language: en-us,en;q=0.5";
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
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

$url = base64_decode($_GET['link']);

if(isset($url)){
	$id_user = cut_str($url,"com/","/");
	$id_album = cut_str($url,"=","#");
	if($id_album)
	$id_album = '&authkey='.$id_album;
	$is_aut = explode('#',$url);
	$id_ep = $is_aut[1];
	$curTemp = curl('https://picasaweb.google.com/data/feed/tiny/user/'.$id_user.'/photoid/'.$id_ep.'?&alt=json'.$id_album);
	$curTemp = cut_str($curTemp,'content":[{"','media$description');
	if ($curTemp <> "") {
		$curList = explode('"',$curTemp);
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
