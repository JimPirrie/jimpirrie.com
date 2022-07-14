<?php

$path = parse_url($_GET["actualRequest"])["path"];

$parts = explode("/", $path);

$twigData["mv_ref"] = $parts[1];

$tomorrow = strftime("%A", strtotime("tomorrow"));
$dayAfterTomorrow = strftime("%A", strtotime("tomorrow +1"));

$twigData["tomorrow"] = $tomorrow;
$twigData["dayAfterTomorrow"] = $dayAfterTomorrow
;
$twigData["message"] = $message;

