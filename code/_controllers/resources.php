<?php

$twigData["seo"]["title"] = "Professional Development Coaching | Jim Pirrie Coaching";
$twigData["seo"]["description"] = "";
$twigData["seo"]["image"] = "";

$twigData["mv"] = file_get_contents("https://jimpirrie.local/embed/dynamicContentServer.php?ref=all");

print_r($twigData["mv"]);

$twigData["active"]["resources"] = "active";