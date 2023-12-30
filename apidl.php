<?php

session_start();
header("Access-Control-Allow-Origin: *");
ini_set("allow_url_fopen", 1);

require "./videodownloder/vendor/autoload.php";


$apiurl = "http://199.192.20.82:9191/api/info";

// $url = isset($_POST["url"]) ? $_POST["url"] : null;


if (!isset($_GET['url'])) {
    header('Content-Type: application/json');
    echo json_encode(['Error ' => 'Url missing'], JSON_PRETTY_PRINT);
    die();
  }

if (isset($_GET["url"])) {
    $url = isset($_GET["url"]) ? $_GET["url"] : null;

    function get_string_between($string, $start, $end)
    {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return "";
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
    function send_json($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit();
    }

   if (!isset($url)) {
        send_json([
            "error" => "No URL provided!",
        ]);
    }

    try {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL =>
                $apiurl.
            "?url=" .
                $url .
                "&flatten=True",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        if (isset($response["error"])) {
            return send_json([
                "message" => $response["error"],
            ]);
        }
        $base = $response["videos"][0];
        $title = $base["title"] ? $base["title"] : "Title of the Request";
        $duration = isset($base["duration"]) ? $base["duration"] : 30;
        $thumb = $base["thumbnail"] ? $base["thumbnail"] : null;
        $videosLinks = $base["formats"];
        $videos = [];
        for ($i = 0; $i < count($videosLinks); $i++) {
            $size = getFileSize($videosLinks[$i]["url"]);
            $obj = [
                "url" => $videosLinks[$i]["url"],
                "filesize" => isset($videosLinks[$i]["filesize"])
                    ? $videosLinks[$i]["filesize"]
                    : $size,
                "quality" => isset($videosLinks[$i]["format_note"])
                    ? $videosLinks[$i]["format_note"]
                    : "none",
                "acodec" => isset($videosLinks[$i]["acodec"])
                    ? $videosLinks[$i]["acodec"]
                    : "none",
                "vcodec" => isset($videosLinks[$i]["vcodec"])
                    ? $videosLinks[$i]["vcodec"]
                    : "none",
                "ext" => $videosLinks[$i]["ext"],
                "protocol" => $videosLinks[$i]["protocol"],
            ];
            array_push($videos, $obj);
        }
        $object1 = [
            "title" => $title,
            "source" => $base["extractor_key"],
            "thumbnail" => $thumb,
            "duration" => $duration,
            "message" => "success",
            "formats" => $videos,
        ];
        // $object1 = getEncrptedData($object1);

        return send_json([
            "res_data" => $object1,
            "message" => "success",
        ]);
    } catch (Exception $e) {
        send_json([
            "error" => $e,
            "message" => $response,
        ]);
    }
} else {
    send_json([
        "error" => "No URL provided!",
    ]);
}

function getEncrptedData($obj)
{
    $original_string = json_encode($obj);
    $cipher_algo = "AES-128-CTR";
    $iv_length = openssl_cipher_iv_length($cipher_algo);

    $encrypt_iv = openssl_random_pseudo_bytes(
        openssl_cipher_iv_length($cipher_algo)
    );

    $encrypt_key = "mdbuj8j9w9j@#$&";

    $encrypted_string = openssl_encrypt(
        $original_string,
        $cipher_algo,
        $encrypt_key,
        0,
        $encrypt_iv
    );
    return $encrypted_string;
}

function getFileSize($url)
{
    $size = 0;
    try {
        $headers = get_headers($url, 1);
        if (isset($headers["Content-Length"])) {
            $size = intval($headers["Content-Length"]);
        }
    } catch (\Exception $e) {
        $size = 0;
    }
    return $size;
}
