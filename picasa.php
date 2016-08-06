<?php
error_reporting(E_ERROR | E_PARSE);

$url = 'https://picasaweb.google.com/106923264651606961732/1?authkey=Gv1sRgCOe50vDWpfKuPw#5828547078991257090';

function curl($url) {
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
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
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

if(isset($url)) {
	$id_user = cut_str($url,"com/","/");
	$id_album = cut_str($url,"=","#");
	$is_aut = explode('#',$url);
	$id_ep = $is_aut[1];
	$curTemp = curl('https://picasaweb.google.com/data/feed/tiny/user/'.$id_user.'/photoid/'.$id_ep.'?&alt=json&authkey='.$id_album.'');
	$curTemp = cut_str($curTemp,'content":[{"','media$description');
	if ($curTemp <> "") {
			$curList = explode('"',$curTemp);
			foreach ($curList as $curl) {
			$curl = trim(substr($curl, strpos($curl,'https://')-strlen($curl)));
			$curl = urldecode($curl);
				 if ($curl <> "" ){
					if (strpos($curl,'itag=37') || strpos($curl,'=m37') !== false) {$v1080p=$curl;}
					if (strpos($curl,'itag=22') || strpos($curl,'=m22') !== false) {$v720p=$curl;}
					if (strpos($curl,'itag=18') || strpos($curl,'=m18') !== false) {$v360p=$curl;}
				}
			}
			echo '<div class="container-fluid"><font color="red">360p:</font> <a href="'.$v360p.'" target="_blank">'.$v360p.'</a><br><br><font color="red">720p:</font> <a href="'.$v720p.'" target="_blank">'.$v720p.'</a><br><br><font color="red">1080p:</font> <a href="'.$v1080p.'" target="_blank">'.$v1080p.'</a></div>';
		}
}

?>
