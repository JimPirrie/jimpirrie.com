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

include(__DIR__."/../_templates/blog/_postMetadata.php"); // $posts array is here. Body content is in twig template

if($blogPostId){

    /*
    if(!$posts[$blogPostId]){

        // post not found - redirect to blog home page

        header("Location: /blog/", true, 404);
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

    $contentTemplate = "post-{$blogPostId}.html.twig";
    */
}
else{

    // blog home page

    $seoTitle = "Jim Pirrie's Blog";
    $seoDescription = "DESCRIPTION";
    $seoImage = "t-shaped-professions.png";
    $seoUrl = "/blog/";

    $contentTemplate = "blog-home.html.twig";
}

$twigData["blogPostId"] = $blogPostId;
$twigData["posts"] = $posts;
$twigData["contentTemplate"] = "blog/{$contentTemplate}";
$twigData["seo"]["title"] = $seoTitle;
$twigData["seo"]["description"] = $seoDescription;
$twigData["seo"]["image"] = "https://www.jimpirrie.com/_images/{$seoImage}";
$twigData["seo"]["canonical"] = "https://www.jimpirrie.com/{$seoUrl}";

$twigData["active"]["blog"] = "active";