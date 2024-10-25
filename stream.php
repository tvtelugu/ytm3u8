<?php

include("_configs.php");

header("Access-Control-Allow-Origin: *");

$c = ""; $v = "";
if(isset($_REQUEST['c'])){ $c = trim(strip_tags($_REQUEST['c'])); }
if(isset($_REQUEST['v'])){ $v = trim(strip_tags($_REQUEST['v'])); }

if(empty($c) && empty($v)) {
    http_response_code(400);
    exit();
}

if(!empty($c)) {
    $api = "https://www.youtube.com/".$c."/live";
}
if(!empty($v)) {
    $api = "https://www.youtube.com/watch?v=".$v;
}

$streamURL = extractHLS($api);
if(empty($streamURL)) { http_response_code(503); exit(); }

header("Location: ".$streamURL);
exit();

?>