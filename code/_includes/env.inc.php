<?php

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
