<?php


session_start();
header('Access-Control-Allow-Origin: *');
ini_set("allow_url_fopen", 1);

//https://video.infusiblecoder.com/allvideoapk/download.php?url=https://www.php.net/manual/en/function.urldecode.php&type=alll.mp4
if (!isset($_GET['url']) || !isset($_GET['type']))
{

$age = '{"message":"error feilds missing"}';
header('Content-Type: application/json; charset=utf-8');
echo json_encode($age);


return;
}

$url = $_GET['url'];
$url = base64_decode(urldecode($url));
$type_name = $_GET['type'];



    function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        //browser's user agent string (UA)
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    $output = file_get_contents_curl($url);
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$type_name");
    echo $output;


?>