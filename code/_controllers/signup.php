<?php

$path = parse_url($_GET["actualRequest"])["path"];

$parts = explode("/", $path);


$tomorrow = strftime("%A", strtotime("tomorrow"));
$dayAfterTomorrow = strftime("%A", strtotime("tomorrow +1"));

$twigData["mv"] = json_decode(file_get_contents("https://jimpirrie.mvault.net/embed/dynamicContentServer.php?ref={$parts[2]}"), 1);
print_r($twigData["mv"]);

$twigData["mv_ref"] = $parts[2];
$twigData["tomorrow"] = $tomorrow;
$twigData["dayAfterTomorrow"] = $dayAfterTomorrow;

