<?php

//todo: testimonials
//todo: ActiveCampaign
//todo: login
//todo remove blog from mvault
//todo move mvault login page to home
//todo: use mv blog layout as model for minicourse ad
//todo: fix blog post format on mobile

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


