<?php

$path = parse_url($_GET["actualRequest"])["path"];

$parts = explode("/", $path);

$blogPostId = 0;

if(sizeof($parts) > 2){

    $blogPostId = $parts[2];
}

if(sizeof($parts) > 3) {

    $blogPostSlug = $parts[3];
}

$q = "SELECT * FROM blogPost WHERE `status` = \"published\"";
$rs = $db->query($q);

$posts = [];
while($post = $rs->fetch_assoc()){

    $posts[$post["blogPost_id"]] = $post;
}

if($blogPostId){

    if(!array_key_exists($blogPostId, $posts)){

        // post not found - redirect to blog home page

        header("Location: /blog/");
        exit;
    }
    elseif($blogPostSlug != $posts[$blogPostId]["slug"]){

        // post found, but no slug - redirect to show it
        header("Location: /blog/{$blogPostId}/{$posts[$blogPostId]["slug"]}", true, 301);
        exit;
    }

    $seoTitle = $posts[$blogPostId]["seoTitle"];
    $seoDescription = $posts[$blogPostId]["seoDescription"];
    $seoImage = $posts[$blogPostId]["seoImage"];
    $seoUrl = "{$blogPostId}/{$blogPostSlug}";

    $post = $posts[$blogPostId];

    $contentTemplate = "blog-post.html.twig";
}
else{

    // blog home page

    $seoTitle = "Jim Pirrie's Blog";
    $seoDescription = "DESCRIPTION";
    $seoImage = "t-shaped-professional.png";
    $seoUrl = "/blog/";

    $contentTemplate = "blog-home.html.twig";
}

$twigData["blogPostId"] = $blogPostId;
$twigData["posts"] = $posts;
$twigData["post"] = $post;
$twigData["contentTemplate"] = "blog/{$contentTemplate}";
$twigData["seo"]["title"] = $seoTitle;
$twigData["seo"]["description"] = $seoDescription;
$twigData["seo"]["image"] = "https://www.jimpirrie.com/_images/{$seoImage}";
$twigData["seo"]["canonical"] = "https://www.jimpirrie.com/blog/{$seoUrl}";

$twigData["active"]["blog"] = "active";