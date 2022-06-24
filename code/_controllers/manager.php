<?php

loginManager();

// evernote oauth

$key = evernoteKeys("key");
$secret = evernoteKeys("secret");
$sandbox = evernoteKeys("sandbox");
$callback = siteurl()."/manager";

$oauth_handler = new Evernote\Auth\OauthHandler($sandbox);

if($_POST["authorize"]){

    unset($_SESSION["evernote"]);

    $oauth_handler = new Evernote\Auth\OauthHandler($sandbox);

    $oauth_handler->authorize($key, $secret, $callback); // creates temp keys & calls callback
}
elseif($_GET["oauth_verifier"]){

    $oauth_handler = new Evernote\Auth\OauthHandler($sandbox);

    $oath_data = $oauth_handler->authorize($key, $secret, $callback); // returns validated token

    $_SESSION["evernote"] = $oath_data;
    $_SESSION["evernote"]["status"] = "authorized";

    header("Location: /manager");
    exit;
}

if($_POST["sort"]){

    $_SESSION["sort"] = $_POST["sort"];

    header("Location: /manager");
    exit;
}

if($_POST["update_featured_sidebar"]){

    $esc_id = $db->real_escape_string($_POST["id"]);
    $esc_featured_sidebar = $db->real_escape_string($_POST["featured_sidebar"]);

    $q = "SELECT * FROM blogPost WHERE featured_sidebar >= {$esc_featured_sidebar} ORDER BY featured_sidebar";
    $rs = $db->query($q);

    while($post = $rs->fetch_assoc()){

        $q = "UPDATE blogPost SET featured_sidebar = featured_sidebar +1 WHERE blogPost_id = {$post["blogPost_id"]}";
        $db->query($q);
    }

    $q = "UPDATE blogPost SET featured_sidebar = {$esc_featured_sidebar} WHERE blogPost_id = {$esc_id}";
    $db->query($q);

    $q = "SELECT * FROM blogPost WHERE featured_sidebar >0 ORDER BY featured_sidebar";
    $rs = $db->query($q);

    $i = 1;
    while($post = $rs->fetch_assoc()){

        $q = "UPDATE blogPost SET featured_sidebar = {$i} WHERE blogPost_id = {$post["blogPost_id"]}";
        $db->query($q);

        $i++;
    }

    header("Location: /manager");
    exit;
}

if($_POST["update_featured_main"]){

    $esc_id = $db->real_escape_string($_POST["id"]);
    $esc_featured_main = $db->real_escape_string($_POST["featured_main"]);

    $q = "SELECT * FROM blogPost WHERE featured_main >= $esc_featured_main ORDER BY featured_main";
    $rs = $db->query($q);

    while($post = $rs->fetch_assoc()){

        $q = "UPDATE blogPost SET featured_main = featured_main +1 WHERE blogPost_id = {$post["blogPost_id"]}";
        $db->query($q);
    }

    $q = "UPDATE blogPost SET featured_main = {$esc_featured_main} WHERE blogPost_id = {$esc_id}";
    $db->query($q);

    $q = "SELECT * FROM blogPost WHERE featured_main >0 ORDER BY featured_main";
    $rs = $db->query($q);

    $i = 1;
    while($post = $rs->fetch_assoc()){

        $q = "UPDATE blogPost SET featured_main = {$i} WHERE blogPost_id = {$post["blogPost_id"]}";
        $db->query($q);

        $i++;
    }

    header("Location: /manager");
    exit;
}

if($_POST["createPost"]){

    $esc_url = $db->real_escape_string(trim($_POST["shareUrl"]));

    $parts = parse_url($esc_url);

    if(!$parts["host"]){

        $esc_guid = $esc_url;
    }
    if($parts["host"] == "www.evernote.com"){

        $parts = explode("/", $parts["path"]);

        $esc_guid = $parts[4];
    }
    elseif($parts["host"] == "sandbox.evernote.com"){

        $get = [];
        parse_str($parts["fragment"], $get);

        $esc_guid = $get["n"];
    }

    $client = new Evernote\Client($_SESSION["evernote"]["oauth_token"]);

    try{

        $note = $client->getNote($esc_guid);

        if($note){

            $q = "SElECT * FROM blogPost WHERE evernoteGuid = \"$esc_guid\"";
            $rs = $db->query($q);

            if($rs->num_rows == 0){

                $q = "INSERT INTO blogPost(evernoteGuid, status) VALUES(\"{$esc_guid}\", \"draft\")";
                $db->query($q);
            }

            evernote_parseNote($note);
        }
    }
    catch(Exception $e){

        print_r($e);
    }

    header("Location: /manager");
    exit;
}

if($_POST["confirmDeletePost"]){

    $esc_guid = $db->real_escape_string($_POST["guid"]);

    $dir = "{$_SERVER["DOCUMENT_ROOT"]}/_blogImages/{$esc_guid}";

    array_map( 'unlink', array_filter((array) glob("{$dir}/*") ) );

    rmdir($dir);

    $q = "DELETE FROM blogPost WHERE evernoteGuid = \"{$esc_guid}\" LIMIT 1";
    $db->query($q);

    header("Location: /manager");
    exit;
}

if($_POST["status"]){

    $esc_status = $db->real_escape_string($_POST["status"]);
    $esc_id = $db->real_escape_string($_POST["id"]);

    $q = "UPDATE blogPost SET status = \"{$esc_status}\" WHERE blogPost_id = {$esc_id}";
    $db->query($q);

    header("Location: /manager");
    exit;
}

if($_POST["updatePost"]){

    $client = new Evernote\Client($_SESSION["evernote"]["oauth_token"]);

    $esc_guid = $db->real_escape_string($_POST["guid"]);

    try{

        $note = $client->getNote($esc_guid);

        if($note){

            $q = "SElECT * FROM blogPost WHERE evernoteGuid = \"$esc_guid\"";
            $rs = $db->query($q);

            if($rs->num_rows == 0){

                $q = "INSERT INTO blogPost(evernoteGuid, status) VALUES(\"{$esc_guid}\", \"draft\")";
                $db->query($q);
            }

            evernote_parseNote($note);
        }
    }
    catch(Exception $e){

        print_r($e);
    }

    header("Location: /manager");
    exit;
}

if($_SESSION["login"]["status"] == "logged-in"){

    if(!$_SESSION["sort"] OR $_SESSION["sort"] == "all"){

        $q = "SELECT * FROM blogPost ORDER BY featured_main, title";
    }
    elseif($_SESSION["sort"] == "draft"){

        $q = "SELECT * FROM blogPost WHERE status = \"draft\" ORDER BY featured_main, title";
    }
    elseif($_SESSION["sort"] == "published"){

        $q = "SELECT * FROM blogPost WHERE status = \"published\" ORDER BY featured_main, title";
    }
    elseif($_SESSION["sort"] == "featured_main"){

        $q = "SELECT * FROM blogPost WHERE featured_main > 0 ORDER BY featured_main";
    }
    elseif($_SESSION["sort"] == "featured_sidebar"){

        $q = "SELECT * FROM blogPost WHERE featured_sidebar > 0 ORDER BY featured_sidebar";
    }

    $rs = $db->query($q);

    while($post = $rs->fetch_assoc()){

        $mainList[] = $post;
    }

    $q = "SELECT * FROM blogPost WHERE featured_sidebar > 0 ORDER BY featured_sidebar";
    $rs = $db->query($q);

    while($post = $rs->fetch_assoc()){

        $sidebarFeaturedList[] = $post;
    }

    $q = "SELECT * FROM blogPost WHERE featured_sidebar = 0 ORDER BY title";
    $rs = $db->query($q);

    while($post = $rs->fetch_assoc()){

        $sidebarOtherList[] = $post;
    }
}

$twigData["evernote"] = $_SESSION["evernote"];
$twigData["login"] = $_SESSION["login"];
$twigData["sortSelected"]["{$_SESSION["sort"]}"] = "selected";
$twigData["mainList"] = $mainList;
$twigData["sidebarFeaturedList"] = $sidebarFeaturedList;
$twigData["sidebarOtherList"] = $sidebarOtherList;

$contentTemplate = "manage-blog.html.twig";
