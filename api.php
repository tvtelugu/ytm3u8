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
    $asset_id = $c;
    $asset_type = "channel";
    $api = "https://www.youtube.com/".$c."/live";
}
if(!empty($v)) {
    $asset_id = $v;
    $asset_type = "video";
    $api = "https://www.youtube.com/watch?v=".$v;
}

$streamURL = extractHLS($api);
if(empty($streamURL)) { http_response_code(503); exit(); }

response("success", 200, "Data Fetched Successfully", array("asset_id" => $asset_id, "asset_type" => $asset_type, "hls_url" => $streamURL));

?>