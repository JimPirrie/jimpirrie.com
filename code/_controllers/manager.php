<?php

if($_POST["featured_sidebar"]){

    $esc_id = $db->real_escape_string($_POST["id"]);
    $esc_featured_sidebar = $db->real_escape_string($_POST["featured_sidebar"]);

    $q = "SELECT * FROM blogPosts WHERE featured_sidebar >= $esc_featured_sidebar ORDER BY featured_sidebar";
    $rs = $db->query($q);

    while($post = $rs->fetch_assoc()){

        $q = "UPDATE blogPost SET featured_sidebar = featured_sidebar +1 WHERE blogPost_id = {$post["blogPost_id"]}";
        $db->query($q);
    }

    $q = "UPDATE blogPost SET featured_sidebar = featured_sidebar +1 WHERE blogPost_id = {$esc_id}";
    $db->query($q);

    header("Location: /manager");
    exit;
}



if($_POST["createPost"]){

    $esc_guid = $db->real_escape_string($_POST["guid"]);

    $q = "INSERT INTO blogPost(evernoteGuid, status) VALUES(\"{$esc_guid}\", \"draft\")";
    $db->query($q);

    evernote_parseNote($esc_guid);

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

if($_POST["updateStatus"]){

    $esc_status = $db->real_escape_string($_POST["status"]);
    $esc_id = $db->real_escape_string($_POST["id"]);

    $q = "UPDATE blogPost SET status = \"{$esc_status}\" WHERE blogPost_id = {$esc_id}";
    $db->query($q);

    header("Location: /manager");
    exit;
}

if($_POST["post"]){

    $esc_guid = $db->real_escape_string($_POST["guid"]);

    evernote_parseNote($esc_guid);

    header("Location: /manager");
    exit;
}

$q = "SELECT * FROM blogPost";
$rs = $db->query($q);

while($post = $rs->fetch_assoc()){

    $posts[] = $post;
}

$twigData["posts"] = $posts;

$contentTemplate = "manage-blog.html.twig";
