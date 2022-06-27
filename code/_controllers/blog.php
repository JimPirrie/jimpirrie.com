<?php

if($_GET["tag"]){

    $_SESSION["activeTag"] = $db->real_escape_string($_GET["tag"]);
}

if($_GET["cleartags"]){

    unset($_SESSION["activeTag"]);
}

if($_SESSION["activeTag"]){

    $tagFilter = "tags LIKE \"%;{$_SESSION["activeTag"]};%\" AND ";
}

$path = parse_url($_GET["actualRequest"])["path"];

$parts = explode("/", $path);

$blogPostId = 0;

if(sizeof($parts) > 2){

    $blogPostId = $parts[2];
}

if(sizeof($parts) > 3) {

    $blogPostSlug = $parts[3];
}

if($_SESSION["login"]["status"] = "logged-in"){

    $q = "SELECT * FROM blogPost";
}
else{

    $q = "SELECT * FROM blogPost WHERE {$tagFilter} `status` = \"published\"";
}

$rs = $db->query($q);

while($post = $rs->fetch_assoc()){

    $publishedList[$post["blogPost_id"]] = $post;

    $tags.= $post["tags"];
}

$tags = array_filter(array_unique(explode(";", $tags)));


$q = "SELECT * FROM blogPost WHERE {$tagFilter} featured_main > 0 AND `status` = \"published\" ORDER BY featured_main";
$rs = $db->query($q);

while($post = $rs->fetch_assoc()){

    $mainList[$post["blogPost_id"]] = $post;
}

$q = "SELECT * FROM blogPost WHERE {$tagFilter} featured_sidebar > 0 ORDER BY featured_sidebar";
$rs = $db->query($q);

while($post = $rs->fetch_assoc()){

    $sidebarFeaturedList[] = $post;
}

$q = "SELECT * FROM blogPost WHERE {$tagFilter} featured_sidebar = 0 ORDER BY title";
$rs = $db->query($q);

while($post = $rs->fetch_assoc()){

    $sidebarOtherList[] = $post;
}

if($blogPostId){

    if(!array_key_exists($blogPostId, $publishedList)){

        // post not found - redirect to blog home page

        header("Location: /blog/");
        exit;
    }
    elseif($blogPostSlug != $publishedList[$blogPostId]["slug"]){

        // post found, but no slug - redirect to show it
        header("Location: /blog/{$blogPostId}/{$publishedList[$blogPostId]["slug"]}", true, 301);
        exit;
    }

    $post = $publishedList[$blogPostId];

    $seoTitle = $publishedList[$blogPostId]["seoTitle"];
    $seoDescription = $publishedList[$blogPostId]["seoDescription"];

    if(!$seoImage = parseVideoPlaceholder($post["body"])){

        $seoImage = siteurl()."{$publishedList[$blogPostId]["seoImage"]}";
    }

    $seoUrl = "{$blogPostId}/{$blogPostSlug}";

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

$twigData["tags"] = $tags;
$twigData["activeTag"] = $_SESSION["activeTag"];
$twigData["blogPostId"] = $blogPostId;
$twigData["mainList"] = $mainList;
$twigData["sidebarFeaturedList"] = $sidebarFeaturedList;
$twigData["sidebarOtherList"] = $posts;
$twigData["post"] = $post;
$twigData["contentTemplate"] = "blog/{$contentTemplate}";
$twigData["seo"]["title"] = $seoTitle;
$twigData["seo"]["description"] = $seoDescription;
$twigData["seo"]["image"] = $seoImage;
$twigData["seo"]["canonical"] = "https://www.jimpirrie.com/blog/{$seoUrl}";

$twigData["active"]["blog"] = "active";