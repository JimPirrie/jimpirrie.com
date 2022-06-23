<?php

function evernoteDevToken($sandbox = 0){

    global $db;

    if($sandbox){

        $q = "SELECT evernoteDeveloperToken_sandbox AS devToken FROM env WHERE env_id = 1";
    }
    else{

        $q = "SELECT evernoteDeveloperKToken_production AS devToken FROM env WHERE env_id = 1";
    }

    $rs = $db->query($q);

    $data = $rs->fetch_assoc();

    return $data["devToken"];
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
