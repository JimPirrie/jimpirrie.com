<?php

function loginManager(){

    $hash = '$2y$10$O4PAtBnUy68M0fmQ9NtUfugmSQkM.zWRO86J27Zplp/Z5a.X/iOzq';

    if($_POST["login"]){

        if($_POST["email"] == "jim@jimpirrie.com" AND password_verify($_POST["password"], $hash)) {

            $_SESSION["login"]["status"] = "logged-in";

            header("Location: /manager");
            exit;
        }
        else{

            header("Location: /manager?loginerror=1");
            exit;
        }
    }

    if($_POST["logout"]){

        unset($_SESSION["login"]);
        unset($_SESSION["evernote"]);

        header("Location: /manager");
        exit;
    }
}

function siteurl(){

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {

        // SSL connection

        return "https://www.jimpirrie.com";
    }
    else{

        return "http://jimpirrie.local";
    }
}

function isProductionSite(){

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {

        // SSL connection

        return true;
    }
    else{

        return false;
    }
}

function evernoteKeys($what){

    $sandbox = false;

    if($what == "key"){

        return "jim5598";
    }
    elseif($what == "secret"){

        return "9e5bc01283ac18d1";
    }
    elseif($what == "appname"){

        return "Blog Post Generator";
    }
    elseif($what == "sandbox"){

        return $sandbox;
    }
}

function dbKeys($var, $cronLocal = 0){

    if(strpos($_SERVER["SERVER_NAME"], ".local") OR $cronLocal){

        if($var == "dbName") {

            return "homestead";
        }
        elseif($var == "username"){

            return "homestead";
        }
        elseif($var == "password"){

            return "secret";
        }
    }
    else{

        if($var == "dbName") {

            return "jimpirrie_data";
        }
        elseif($var == "username"){

            return "jimpirriedotcom";
        }
        elseif($var == "password"){

            return "W1nst*nJIMPIRRIE";
        }
    }
}
