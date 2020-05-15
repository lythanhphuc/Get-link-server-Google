<?php
error_reporting(0);

$ref = $_SERVER['HTTP_REFERER'];
$allowed = '|lythanhphuc.com|nightowlvn.com|earlybirdvn.com|';
if (!preg_match('~('.join('|', array_map('preg_quote', explode('|', $allowed))).')~', $ref)) {
exit('<html>
<head><title>403 Forbidden</title></head>
<body bgcolor="white">
<center><h1>403 Forbidden</h1></center>
<hr><center>nginx</center>
</body>
</html>
');
}
if((isset($_SERVER['HTTP_RANGE'])) == 0) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

function decrypt($string) {
	$key = 'ahihi-baka-123';
	$result = '';
	$string = strtr($string, '-_', '+/');
	$string = base64_decode($string);
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	return $result;
}

$v = decrypt($_GET['data']);
$cookie = $_GET['key'];

function info($url, $cookie) { 
	$ch = curl_init();
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: DRIVE_STREAM=".$cookie));
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	
	$headers = array();
    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
    foreach (explode("\r\n", $header_text) as $i => $line)
        if ($i === 0)
            $headers['http_code'] = $line;
        else{
            list ($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
			}
    return $headers;
}

$headers = info($v,$cookie);

if(isset($headers['Location'])) {
	$url = $headers['Location'];
	$headers = info($url, $cookie);
	if(isset($headers['Location'])) {
		$url = $headers['Location'];
		$headers = info($url, $cookie);
	}
} else {
	$url = $v;
}

if (!strstr($headers['http_code'], "200 OK") && (!strstr($headers['http_code'], "302"))) {
	exit("Error 403 (Forbidden)!");
}

if(isset($_SERVER['HTTP_RANGE'])) {
	$rh = "\r\n" . 'Range: ' . $_SERVER['HTTP_RANGE'];
} else {
	$rh = '';
}

$base_headers['Accept'] = 'Accept: '.$_SERVER['HTTP_ACCEPT'];
$base_headers['Accept-Encoding'] = 'Accept-Encoding: '.$_SERVER['HTTP_ACCEPT_ENCODING'];
$base_headers['Accept-Language'] = 'Accept-Language: '.$_SERVER['HTTP_ACCEPT_LANGUAGE'];
$base_headers['User-Agent'] = 'User-Agent: '.$_SERVER['HTTP_USER_AGENT'];
$base_headers['Cookie'] = 'Cookie: DRIVE_STREAM='.$cookie;

$opts = array(
	'http' => array(
		'header' => implode("\r\n", $base_headers) . $rh
	)
);

$context = stream_context_create($opts);
$fp = fopen($url, 'rb', false, $context);

$size   = $headers['Content-Length']; // File size
$length = $size;           // Content length
$start  = 0;               // Start byte
$end    = $size - 1;       // End byte
#header("Accept-Ranges: 0-$length");
header('Content-Type: video/mp4');
header('Accept-Ranges: bytes');
header('Last-Modified: '.$headers['Last-Modified']);
if (isset($_SERVER['HTTP_RANGE'])) {
	$c_start = $start;
	$c_end   = $end;
    if (strpos($_SERVER['HTTP_RANGE'], '=') == false) {
		list(, $range) = explode(':', $_SERVER['HTTP_RANGE'], 2);
	} else {
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
	}
	if (strpos($range, ',') !== false) {
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		exit;
	}
	if ($range == '-') {
		$c_start = $size - substr($range, 1);
	}else {
		$range  = explode('-', $range);
		$c_start = $range[0];
		$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
	}
	$c_end = ($c_end > $end) ? $end : $c_end;
	if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		header("Content-Range: bytes $start-$end/$size");
		header('Location: '.$_SERVER['REQUEST_URI']);
		exit;
	}
	$start  = $c_start;
	$end    = $c_end;
	$length = $end - $start + 1;
	fseek($fp, $start);
	header('HTTP/1.1 206 Partial Content');
}
header("Content-Range: bytes $start-$end/$size");
header("Content-Length: $length");

$buffer = 1024 * 8;
while(!feof($fp) && ($p = ftell($fp)) <= $end) {
	if ($p + $buffer > $end) {
		$buffer = $end - $p + 1;
	}
	set_time_limit(0);
	echo fread($fp, $buffer);
	flush(); 
}
fclose($fp);
die();

?>
