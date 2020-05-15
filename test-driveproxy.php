<?php
/* Code by #LTP Kai from Night Owl VN - https://nightowlvn.com - https://lythanhphuc.com */

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

$url = $_GET['url'];
$token = $_GET['token'];

$html = curl("http://api.nightowlvn.com/get?url=".$url."&token=".$token);
$html = str_replace('http://domainproxy.com/videoplayback?data=','http://49.12.35.6/driveproxy.php?data=', $html);
?>

<!--<!doctype html>-->
<html lang="vi" itemscope="itemscope" itemtype="http://schema.org/WebPage">
<head>
<title>Test Stream Proxy</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script src="//tools.nightowlvn.com/jwplayer/8.13.8/jwplayer.js?v=9"></script>
<script type="text/javascript">jwplayer.key="W7zSm81+mmIsg7F+fyHRKhF3ggLkTqtGMhvI92kbqf/ysE99";</script>
<style>
.jwplayer .jw-rightclick {
	display:none !important;
}
.jw-state-paused .jw-display {
	 display: table !important;
}
html, body
{
	/*background-color:#000;*/
	background-repeat: no-repeat;
	background-position: center;
	margin:0;
	padding:0;
	height:100%;
	width:100%;
	overflow:hidden;
}
*
{
	outline:0;
	border:0;
}
</style>
</head>
<body>

<center><div id="jwplayer"></div></center>
<script type="text/javascript">
		jwplayer("jwplayer").setup({
			sources: <?php echo $html;?>,
			autostart: "false",
			width: "100%",
			height: "100%",
			"playbackRateControls": true,
			"mute": false,
			"preload": "none",
			"volume": 100,
			});
		</script>
</body>
</html>
