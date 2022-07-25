<?php

$twigData["seo"]["title"] = "Professional Development Coaching | Jim Pirrie Coaching";
$twigData["seo"]["description"] = "";
$twigData["seo"]["image"] = "";

$twigData["mv"] = json_decode(file_get_contents("https://jimpirrie.mvault.net/embed/dynamicContentServer.php?ref=all"), 1);

print_r($twigData["mv"]);

$twigData["active"]["resources"] = "active";