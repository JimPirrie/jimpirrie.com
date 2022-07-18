<?php

//todo: testimonials
//todo remove blog from mvault
//todo move mvault login page to home
//todo: use mv blog layout as model for minicourse ad
//todo: home page video
//todo: thankyou page video
//todo: welcome sequence emails
//todo: gift #1
//todo: gift #2
//todo: gift #3


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
        elseif(strpos(" ".$controller, "success")){

            $controller = "success";
        }
        elseif(strpos(" ".$controller, "free")){

            $controller = "free";
        }
        elseif(strpos(" ".$controller, "signup")){

            $controller = "signup";
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


