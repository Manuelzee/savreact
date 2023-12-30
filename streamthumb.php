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

//echo $_POST['url'];

curl_setopt_array($curl, array(
  CURLOPT_URL => urldecode($_GET['url']),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);


if ($response !== false){
    header('Content-type: image/jpeg');
    echo $response;
//echo 'data:image/jpg;base64,'.base64_encode($response);
// echo '<img src="'.'data:image/jpg;base64,'.base64_encode($response).'"/>';

}else{
    echo "error";
}
