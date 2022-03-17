<?php

require_once("init.php");

$controller = str_replace("/", "", $_SERVER["REQUEST_URI"]);

if($controller){

    $path = "{$_SERVER["DOCUMENT_ROOT"]}/../_controllers/{$controller}.php";

    if(!file_exists($path)){

        $controller = "not-found";
    }
}
else{

    $controller = "home";
}

renderPage($controller);


