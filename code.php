<?php


session_start();
header('Access-Control-Allow-Origin: *');
ini_set("allow_url_fopen", 1);

if (!isset($_GET['code']))
{
    header('Content-Type: application/json');
    echo json_encode(['code' => '500'], JSON_PRETTY_PRINT);
    die();

}


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.envato.com/v3/market/author/sale?code='.$_GET['code'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer HgqAAPZjDcePQb9CAMSpqQmEue8ljDt3 ',
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$response = json_decode($response, true);

header('Content-Type: application/json');


if (isset($_GET['t']))
{
  echo json_encode($response, JSON_PRETTY_PRINT);
  die();
}


if (isset($response['license']))
{
  echo json_encode(['code' => '200'], JSON_PRETTY_PRINT);
  die();
}
echo json_encode(['code' => '404'], JSON_PRETTY_PRINT);
die();


