<form action="" method="post">
	<input type="url" name="link" placeholder="Paste link picasa vao day">
	<input type="submit" value="Get link">
</form>

<?php
error_reporting(E_ERROR | E_PARSE);

// Code ko phải của mình làm mình copy của bạn Bảo Sora và thêm vào 1 đoạn để get đc link upload bên photos

$url = $_POST[link];

//cURL để view mã nguồn của trang
function curl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; rv:27.3) Gecko/20130101 Firefox/27.3");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	return curl_exec($ch);
}

function GetPicasa($url) {
	$is_aut = explode('#',$url);
	$id_ep=$is_aut[1];
	$url=curl($url);
	if($id_ep) {
			$url=explode('"gphoto$id":"'.$id_ep,$url);
			$url = explode('"media":{"content":[', $url[1]);
	}
	else {
			$url = explode('"media":{"content":[', $url);
	}
	$url = explode('],"', $url[1]);		
	$url =$url[0];
	return $url;
}

$url = GetPicasa($url);

if(isset($url)) {
	$gach = explode('{"url":"', ($id)?$gach[7]:$url);
	$v360p = urldecode(reset(explode('"', $gach[2])));
	$v720p = urldecode(reset(explode('"', $gach[3])));
	$v1080p = urldecode(reset(explode('"', $gach[4])));
	if(strpos($v1080p, 'itag=37') || strpos($v1080p, '=m37')){
		echo '<br><font color="red">1080p:</font> '.$v1080p.'<br><font color="red">720p:</font> '.$v720p.'<br><font color="red">360p:</font> '.$v360p;
	} elseif(strpos($v720p, 'itag=22') || strpos($v720p, '=m22')){
		echo '<br><font color="red">720p:</font> '.$v720p.'<br><font color="red">360p:</font> '.$v360p;
	} else {
		echo '<br><font color="red">360p:</font> '.$v360p;
	}
}

?>
