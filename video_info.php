<?php

session_start();
header('Access-Control-Allow-Origin: *');
ini_set("allow_url_fopen", 1);



require('./videodownloder/vendor/autoload.php');

$url = isset($_GET['url']) ? $_GET['url'] : null;


function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
function send_json($data)
{
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}



if (!$url) {
    send_json([
        'error' => 'No URL provided!'
    ]);
}

$youtube = new \YouTube\YouTubeDownloader();

try {
    $links = $youtube->getDownloadLinks($url);
     $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_SSL_VERIFYPEER => 0,
                                CURLOPT_SSL_VERIFYHOST => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET"
                            ));
                            $response2 = curl_exec($curl);
                            curl_close($curl);

        send_json([
            'title'=>"".get_string_between($response2, '<title>', '</title>'),
            'links' => [$links->getAllFormats()]
        ]);
  

} catch (\YouTube\Exception\YouTubeException $e) {

    send_json([
        'error' => $e->getMessage()
    ]);
}