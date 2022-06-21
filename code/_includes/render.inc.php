<?php

function renderPage($controller){

    global $twigData;

    include(__DIR__."/../_controllers/{$controller}.php");

    // Twig Bootstrap
    $loader = new Twig\Loader\FilesystemLoader(__DIR__."/../_templates/");

    $twig = new Twig\Environment($loader, array(
        'cache' => __DIR__."/../cache",
        'auto_reload' => true,
        'debug' => true
    ));
    $twig->addExtension(new \Twig\Extension\DebugExtension());

    // load template

    if(isset($twigData["template"])){

        $template = $twigData["template"];
    }
    else{

        $template = "{$controller}.html.twig";
    }
    $tpl = $twig->load($template);

    // render template with our data

    echo $tpl->render($twigData);
}