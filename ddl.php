<?php


session_start();
header('Access-Control-Allow-Origin: *');
ini_set("allow_url_fopen", 1);



if (!isset($_GET['url']))
{
    echo json_encode("error insert a url");
    die();

}


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.m3u8-downloader.com/api/parse?url='.$_GET['url'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'x-api-key: SdnHkadBwMXv9uAEP1wj2xS6gfHky3L4Y3kEC1Oh'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$response = json_decode($response, true);

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);


