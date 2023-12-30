<?php
session_start();
header('Access-Control-Allow-Origin: *');
ini_set("allow_url_fopen", 1);

function utf8_urldecode($str)
{
    return html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)), null, 'UTF-8');
}

function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini    = strpos($string, $start);
    if ($ini == 0)
        return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

if (!isset($_GET['url'])) {
    echo json_encode("error insert a url");
    die();
    
} else {
    
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => urldecode($_GET['url']),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'User-Agent:  Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
        )
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    
    
    $videlist = array();
    
    if ($response !== false) {
        $newstr = get_string_between($response, "__NEXT_DATA__", ":[]}</") . ":[]}";
        
        $newstr1 = utf8_urldecode(get_string_between($newstr, 'json">', ":[]}") . ":[]}}}");
        
        $videlist["title"] = get_string_between($response, 'property="og:title" content="', '"');
        
        $videlist["thumb"] = get_string_between($response, 'property="og:image" content="', '"');
        
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $newstr1, $match);
        
        $videlist["links"] = array();
        if($match){
        for ($x = 0; $x <= count($match[0])-1; $x++) {
            if (strpos($match[0][$x], "sc-cdn.net") && (strpos($match[0][$x], ".400") || strpos($match[0][$x], ".80") || strpos($match[0][$x], ".27") || strpos($match[0][$x], ".256") || strpos($match[0][$x], ".111"))) {
                array_push($videlist["links"], $match[0][$x]);
            }
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($videlist);
      }  else {
        echo "error";
    }
    } else {
        echo "error";
    }
    
    
}
