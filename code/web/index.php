<?php

//todo: testimonials
//todo remove blog from mvault
//todo move mvault login page to home
//todo: use mv blog layout as model for minicourse ad

require_once("init.php");

if(strpos(" ".$_SERVER["HTTP_HOST"], "professionaldevelopmentcoach")){

    $controller = "coaching";
}
elseif(strpos(" ".$_SERVER["HTTP_HOST"], "marketingcoachingforcoaches")){

    $controller = "marketing";
}
else{

    $controller = str_replace("/", "", $_SERVER["REQUEST_URI"]);

    $parts = explode("?", $controller);

    $controller = $parts[0];

    if($controller){

        if(strpos(" ".$controller, "blog")){

            $controller = "blog";
        }
        elseif(strpos(" ".$controller, "thank-you")){

            $controller = "thank-you";
        }

        $path = "{$_SERVER["DOCUMENT_ROOT"]}/../_controllers/{$controller}.php";

        if(!file_exists($path)){

            $controller = "not-found";
        }
    }
    else{

        $controller = "home";
    }
}

$twigData["seo"]["canonical"] = "";
$twigData["seo"]["fbAppId"] = "";
$twigData["POST"] = $_POST;

renderPage($controller);


