<?php

$path = parse_url($_GET["actualRequest"])["path"];

$parts = explode("/", $path);

$blogPostId = 0;

if(sizeof($parts) > 2){

    $what = $parts[2];
    $twigData["contentTemplate"] = "{$what}";
}

if($what == "list"){


}

$tomorrow = strftime("%A", strtotime("tomorrow"));
$dayAfterTomorrow = strftime("%A", strtotime("tomorrow +1"));

$twigData["tomorrow"] = $tomorrow;
$twigData["dayAfterTomorrow"] = $dayAfterTomorrow
;
$twigData["message"] = $message;

