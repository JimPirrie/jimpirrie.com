<?php


$path = parse_url($_GET["actualRequest"])["path"];

$parts = explode("/", $path);


$tomorrow = strftime("%A", strtotime("tomorrow"));
$dayAfterTomorrow = strftime("%A", strtotime("tomorrow +1"));

$twigData["seo"]["title"] = "Free Resources";
$twigData["seo"]["description"] = $seoDescription;
$twigData["seo"]["image"] = $seoImage;
$twigData["seo"]["canonical"] = "https://www.jimpirrie.com/blog/free/{$parts["2"]}?mvsr={$_GET["mvsr"]}";

$twigData["mv_ref"] = $parts[2];
$twigData["tomorrow"] = $tomorrow;
$twigData["dayAfterTomorrow"] = $dayAfterTomorrow;


