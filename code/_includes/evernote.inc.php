<?php


function replaceResources($evernoteGuid, $body){

    // inserts placeholders [[IMG %src% ]]

    global $db;

    // set up evernote and image folder

    $evernote = new Evernote\Client($_SESSION["evernote"]["oauth_token"], evernoteKeys("sandbox"));

    $noteStore = $evernote->getUserNotestore();

    $dir = "{$_SERVER["DOCUMENT_ROOT"]}/_blogImages/{$evernoteGuid}";

    array_map( 'unlink', array_filter((array) glob("{$dir}/*") ) );

    if(!file_exists($dir)){

        mkdir($dir, 0777, true);
    }

    // parse for placeholders

    $lastStartPos = 0;

    while($lastStartPos = strpos($body, "<en-media", $lastStartPos)){

        $positions[]["start"] = $lastStartPos;

        $lastStartPos = $lastStartPos + 1;
    }

    if($positions){

        $endTag = "/>";
        $endTagLength = strlen($endTag);

        $hashLabel = "hash=\"";
        $hashLabelLength = strlen($hashLabel);

        foreach($positions AS $i => $position){

            $endPos = strpos($body, "/>", $position["start"]) + $endTagLength;

            $hashStartPos = strpos($body, $hashLabel, $position["start"]) + $hashLabelLength;
            $hashEndPos = strpos($body, '"', $hashStartPos);
            $hashLength = $hashEndPos - $hashStartPos;

            $hash = substr($body, $hashStartPos, $hashLength);

            $positions[$i]["end"] = $endPos;
            $positions[$i]["hash"] = $hash;
        }

        $positions = array_reverse($positions);

        // fetch the images and do the replacements

        foreach($positions AS $i=>$position){

            $hashBin = hex2bin($position["hash"]);

            $resource = $noteStore->getResourceByHash($_SESSION["evernote"]["oauth_token"], $evernoteGuid, $hashBin, true, true, true);

            $img_raw = $resource->data->body;

            $path = "{$dir}/{$resource->attributes->fileName}";

            file_put_contents($path, $img_raw);

            $link = "/_blogImages/{$evernoteGuid}/{$resource->attributes->fileName}";

            if($i == sizeof($positions) -1){

                // this is also the seoImage

                $q = "UPDATE blogPost SET seoImage = \"{$link}\" WHERE evernoteGuid = \"{$evernoteGuid}\"";
                $db->query($q);
            }

            $imgTag = "[[IMG {$link} ]]";

            $body = substr_replace($body, $imgTag, $position["start"], $position["end"] - $position["start"]);
        }
    }

    return $body;
}


function parseImagePlaceholders($body){

    $start = 0;
    while($start = strpos($body, "[[IMG", $start)){

        $positions[]["start"] = $start;

        $start = $start +1;
    }

    if($positions){

        $positions = array_reverse($positions);

        foreach($positions AS $i => $position){

            $start = $position["start"];

            $end = strpos($body, "]]", $start);

            $positions[$i]["end"] = $end;

            $tag = substr($body, $start, $end - $start +2);

            $link = trim(str_replace(["[[IMG", "]]"], "", $tag));

            $body = str_replace($tag, "<div class='blog-image-container'><img class='blog-image' src=\"{$link}\"></div>", $body);
        }
    }

    return $body;
}

function parseVideoPlaceholder(&$body){

    $start = strpos($body, "[[VIMEO");

    if($start !== false){

        $end = strpos($body, "]]", $start) +2;

        $tag = substr($body, $start, $end - $start);

        $url = trim(str_replace(["[[VIMEO", "]]"], "", $tag));
        $url = str_replace("\xc2\xa0", '', $url);
        $url = urlencode($url);

        $oembedUrl = "https://vimeo.com/api/oembed.json?url={$url}&responsive=true";

        $data = json_decode(file_get_contents($oembedUrl));

        $player = "<div>{$data->html}</div>";

        $body = str_replace($tag, $player, $body);

        $apiUrl = "http://vimeo.com/api/v2/video/{$data->video_id}.json";

        $data = json_decode(file_get_contents($apiUrl));

        return $data[0]->thumbnail_large;
    }
    else{

        return false;
    }
}

function getSeoDescription(&$body){

    $firstDivStart = strpos($body, "<div");
    $firstDivEnd = strpos($body, "</div>", $firstDivStart) +6;

    // get the text we want

    $seoDescription = str_replace(["<div class=\"blog-content\">","</div>"], "", substr($body, $firstDivStart, $firstDivEnd-$firstDivStart));

    $body = substr_replace($body, "", $firstDivStart, $firstDivEnd-$firstDivStart);

    return $seoDescription;
}

function trimBodyStart($body){

    $emptyDiv = '<div class="blog-content"></div>';
    $emptyDivLength = strlen($emptyDiv);

    $divClose = "</div>";

    // find all instances of empty div before content starts

    $i = 0;
    $start = 0;
    $positions = [];
    while($start = strpos($body, "<div", $start)){

        $end = strpos($body, $divClose, $start) + strlen($divClose);

        $thisDivLength = $end - $start;

        if($emptyDivLength != $thisDivLength){

            break;
        }
        else{

            $positions[] = $start;
        }


        $start = $start +1;

        if($i > 10){

            break;
        }

        $i++;
    }

    // reverse the array

    $positions = array_reverse($positions);

    //delete the empty divs

    foreach($positions AS $startPos){

        $body = substr_replace($body, "", $startPos, $emptyDivLength);
    }

    return $body;
}

function evernote_parseNote($noteObj){

    global $db;

    $esc_guid = $db->real_escape_string($noteObj->guid);

    $esc_title = $db->real_escape_string($noteObj->title);

    $esc_seoTitle = strip_tags($esc_title);

    $esc_slug = strtolower(trim(strip_tags($esc_title)));
    $esc_slug = str_replace(["  ", "  "], " ", $esc_slug);
    $esc_slug = str_replace(" ", "-", $esc_slug);

    $body = $noteObj->content;

    // mark up all divs as blog content

    $body = str_replace("<div>", "<div class=\"blog-content\">", $body);

    $esc_seoDescription = getSeoDescription($body);

    $body = replaceResources($esc_guid, $body);

    $body = strip_tags($body, "<div><ol><ul><li><span>");
    $body = parseImagePlaceholders($body);
    $body = str_replace(["<en-note>", "</en-note>"], "", $body);

    $body = trimBodyStart($body);
    $start = strpos($body, "<div");
    $body = substr($body, $start);

    $esc_body = $db->real_escape_string($body);

    $client = new Evernote\Client($_SESSION["evernote"]["oauth_token"], evernoteKeys("sandbox"));

    $noteStore = $client->getUserNotestore();

    $tagArr = $noteStore->getNoteTagNames($_SESSION["evernote"]["oauth_token"], $esc_guid);

    $esc_tags = implode(";", $tagArr);

    $q = "UPDATE blogPost SET slug = \"{$esc_slug}\", title = \"$esc_title\", seoTitle = \"{$esc_seoTitle}\", seoDescription = \"{$esc_seoDescription}\",  body = \"{$esc_body}\", tags = \";{$esc_tags};\" WHERE evernoteGuid = \"{$esc_guid}\"";
    $db->query($q);
}