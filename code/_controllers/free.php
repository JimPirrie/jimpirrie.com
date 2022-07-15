<?php


$path = parse_url($_GET["actualRequest"])["path"];

$parts = explode("/", $path);


$tomorrow = strftime("%A", strtotime("tomorrow"));
$dayAfterTomorrow = strftime("%A", strtotime("tomorrow +1"));

$twigData["mv_ref"] = $parts[2];
$twigData["tomorrow"] = $tomorrow;
$twigData["dayAfterTomorrow"] = $dayAfterTomorrow;


