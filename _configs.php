<?php

//error_reporting(0);

//=====================================================================================//

if(!isset($KAYA_APP['DATA_FOLDER']) || empty($KAYA_APP['DATA_FOLDER'])){ $KAYA_APP['DATA_FOLDER'] = "_AppData_"; }
if(!is_dir($KAYA_APP['DATA_FOLDER'])){ mkdir($KAYA_APP['DATA_FOLDER']); }
if(!file_exists($KAYA_APP['DATA_FOLDER']."/index.html")){ @file_put_contents($KAYA_APP['DATA_FOLDER']."/index.html", ""); }
if(!file_exists($KAYA_APP['DATA_FOLDER']."/.htaccess")){ @file_put_contents($KAYA_APP['DATA_FOLDER']."/.htaccess", "deny from all"); }

//=====================================================================================//

$_SERVER['HTTP_HOST'] = strtok($_SERVER['HTTP_HOST'], ':'); $_SERVER['HTTP_HOST'] = str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']);
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { $streamenvproto = "https"; } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") { $streamenvproto = "https"; } else { $streamenvproto = "http"; } $plhoth = ($_SERVER['SERVER_ADDR'] !== "127.0.0.1") ? $_SERVER['HTTP_HOST'] : getHostByName(php_uname('n'));
if(isset($_SERVER['HTTP_CF_VISITOR']) && !empty($_SERVER['HTTP_CF_VISITOR'])){ $htcfs = @json_decode($_SERVER['HTTP_CF_VISITOR'], true); if(isset($htcfs['scheme']) && !empty($htcfs['scheme'])){ $streamenvproto = $htcfs['scheme']; }}

//=====================================================================================//

function response($status, $code, $message, $data)
{
    header("Content-Type: application/json");
    $respo = array("status" => $status, "code" => $code, "message" => $message, "data" => $data);
    exit(json_encode($respo));
}

function encdec($action, $data)
{
    $output = ""; $ky = $iv = "fQKrWv9RdjETyv6x";
    if($action == "encrypt")
    {
        $encrypted = openssl_encrypt($data, "AES-128-CBC", $ky, OPENSSL_RAW_DATA, $iv);
        if(!empty($encrypted)) { $output = bin2hex($encrypted); }
    }
    if($action == "decrypt")
    {
        if(strlen($data) % 2 == 0) {
            $dexBinary = hex2bin($data);
            $decrypted = openssl_decrypt($dexBinary, "AES-128-CBC", $ky, OPENSSL_RAW_DATA, $iv);
            if(!empty($decrypted)) { $output = $decrypted; }
        }
    }
    return $output;
}

function getRootBase($url)
{
    $output = "";
    $purl = parse_url($url);
    if(isset($purl['host'])) {
        $output = $purl['scheme']."://".$purl['host'];
    }
    return $output;
}

function getRelBase($url)
{
    $output = "";
    if(stripos($url, "?") !== false) {
        $drl = explode("?", $url);
        if(isset($drl[0]) && !empty($drl[0])) {
            $url = trim($drl[0]);
        }
    }
    $output = str_replace(basename($url), "", $url);
    return $output;
}

function getRelBasedot($url)
{
    $output = "";
    if(stripos($url, "?") !== false) {
        $drl = explode("?", $url);
        if(isset($drl[0]) && !empty($drl[0])) {
            $url = trim($drl[0]);
        }
    }
    $output = str_replace(basename($url), "", $url);
    $output = str_replace(basename($output)."/", "", $output);
    return $output;
}

function getXPURI($string)
{
    $output = '';
    $pattern = '/URI="(.*?)"/';
    preg_match($pattern, $string, $matches);
    if (isset($matches[1])) {
        $output = $matches[1];
    }
    return $output;
}

function getRequest($url, $headers)
{
	$process = curl_init($url);
	curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($process, CURLOPT_HEADER, 0);
	curl_setopt($process, CURLOPT_TIMEOUT, 5);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($process);
	$effURL = curl_getinfo($process, CURLINFO_EFFECTIVE_URL);
	$httpcode = curl_getinfo($process, CURLINFO_HTTP_CODE);
	$contentType = curl_getinfo($process, CURLINFO_CONTENT_TYPE);
	curl_close($process);
	return array("url" => $effURL, "code" => $httpcode, "content_type" => $contentType, "data" => $response);
}

function BrowsergetRequest($url)
{
    global $APP_CONFIG;
    $vrhead = array("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36");
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, $vrhead);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_TIMEOUT, 15);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($process);
    $effURL = curl_getinfo($process, CURLINFO_EFFECTIVE_URL);
    $httpcode = curl_getinfo($process, CURLINFO_HTTP_CODE);
    $phpRError = curl_error($process);
    curl_close($process);
    return array("url" => $effURL, "error" => $phpRError, "code" => $httpcode, "data" => $return);
}


//=====================================================================================//

function extractHLS($api)
{
    $output = "";
    $fetch = getRequest($api, array("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36"));
    $return = $fetch['data'];
    if(stripos($return, ".m3u8") !== false)
    {
        $xbai = explode(".m3u8", $return);
        if(isset($xbai[0]))
        {
            $nsxa = explode('"', $xbai[0]);
            $countd = count($nsxa) - 1;
            if(isset($nsxa[$countd]))
            {
                $rrdje = $nsxa[$countd];
                if(!empty($rrdje))
                {
                    $rawURL = trim($rrdje);
                    if(filter_var($rawURL, FILTER_VALIDATE_URL))
                    {
                        $output = $rawURL.".m3u8";
                    }
                }
            }
            
        }
    }
    return $output;
}

?>