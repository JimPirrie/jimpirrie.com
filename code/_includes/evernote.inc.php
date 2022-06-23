<?php


function replaceResources($evernoteGuid, $body){

    global $db;

    // set up evernote and image folder

    $evernote = new Evernote\Client(evernoteDevToken(1));

    $noteStore = $evernote->getUserNotestore();

    $dir = "{$_SERVER["DOCUMENT_ROOT"]}/_blogImages/{$evernoteGuid}";

    if(!file_exists($dir)){

        mkdir($dir, 0777, true);
    }

    // parse for placeholders

    $lastStartPos = 0;

    while($lastStartPos = strpos($body, "<en-media", $lastStartPos)){

        $positions[]["start"] = $lastStartPos;

        $lastStartPos = $lastStartPos + 1;
    }

    $endTag = "</en-media>";
    $endTagLength = strlen($endTag);

    $hashLabel = "hash=\"";
    $hashLabelLength = strlen($hashLabel);

    foreach($positions AS $i => $position){

        $endPos = strpos($body, "</en-media>", $position["start"]) + $endTagLength;

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

        $resource = $noteStore->getResourceByHash(evernoteDevToken(1), $evernoteGuid, $hashBin, true, true, true);

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


    return $body;
}

function replaceMetadata($evernoteGuid, $body){

    global $db;

    $placeholders[] = ["slug", "[[SLUG"];
    $placeholders[] = ["seoTitle", "[[SEOTITLE"];
    $placeholders[] = ["seoDescription", "[[SEODESCRIPTION"];

    $comma = "";
    $updates = "";
    foreach($placeholders AS $i => $placeholder){

        $name = $placeholder[0];
        $tagStart = $placeholder[1];

        $start = strpos($body, $tagStart);

        if($start !== false){

            $end = strpos($body, "]]") +2;
        }

        if($i == 0){

            $metaDataStart = $start;
        }

        if($i == sizeof($placeholders)){

            $metaDataEnd = $end;
        }

        $placeholderTag = substr($body, $start, $end - $start);

        $esc_content = $db->real_escape_string(trim(strip_tags(str_replace([$tagStart, "]]"], "", $placeholderTag))));

        $body = str_replace($placeholderTag, "", $body);

        $updates.= "{$comma} {$name} = \"{$esc_content}\"";

        $comma = ",";
    }

    $q = "UPDATE blogPost SET {$updates} WHERE evernoteGuid = \"{$evernoteGuid}\"";
    $db->query($q);

    return $body;
}

function parseImagePlaceholders($body){

    $start = 0;
    $i = 0;
    while($start = strpos($body, "[[IMG", $start)){

        $positions[]["start"] = $start;

        $start = $start +1;
    }

    $positions = array_reverse($positions);

    foreach($positions AS $i => $position){

        $start = $position["start"];

        $end = strpos($body, "]]", $start);

        $positions[$i]["end"] = $end;

        $tag = substr($body, $start, $end - $start +2);

        $link = trim(str_replace(["[[IMG", "]]"], "", $tag));

        if($i == sizeof($positions) -1 AND strpos($body, "[[VIMEO") !== false){

            // there is a video - don't show the first image as it's the thumbnail for the video, but still use it as the seoImage

            $body = str_replace($tag, "", $body);
        }
        else{

            $body = str_replace($tag, "<img class='img-fluid' src=\"{$link}\">", $body);
        }
     }

    return $body;
}

function parseVideoPlaceholder($evernoteGuid, $body){

    $start = strpos($body, "[[VIMEO");

    if($start !== false){

        $end = strpos($body, "]]", $start) +2;

        $tag = substr($body, $start, $end - $start);

        $url = trim(str_replace(["[[VIMEO", "]]"], "", $tag));
        $url = str_replace("\xc2\xa0", '', $url);
        $url = urlencode($url);

        $oembedUrl = "https://vimeo.com/api/oembed.json?url={$url}&responsive=true";

        $data = json_decode(file_get_contents($oembedUrl));

        $player = $data->html;

        $body = str_replace($tag, $player, $body);
    }

    return $body;
}

function evernote_parseNote($noteGuid){

    global $db;

    $client = new Evernote\Client(evernoteDevToken(1));

    $note = $client->getNote($noteGuid);

    $esc_title = $db->real_escape_string($note->title);

    $body = $note->content;

    $body = replaceResources($noteGuid, $body);

    $body = replaceMetadata($noteGuid, $body);

    $resourceHashRaw = "50106a650da389560998bb8fd329eb77";

    $resourceHashBin = hex2bin($resourceHashRaw);

    $noteStore = $client->getUserNotestore();

    $resource = $noteStore->getResourceByHash(evernoteDevToken(1), $noteGuid, $resourceHashBin, true, true, true);

    $img_raw = $resource->data->body;

    $dir = "{$_SERVER["DOCUMENT_ROOT"]}/blogImages/{$noteGuid}";

    if(!file_exists($dir)){

        mkdir($dir, 0777, true);
    }

    array_map( 'unlink', array_filter((array) glob("{$dir}/*") ) );

    $path = "{$dir}/{$resource->attributes->fileName}";

    file_put_contents($path, $img_raw);

    // tidy up line breaks etc etc
    $body = str_replace(["<br clear=\"none\"/>", "<br />","<br>","<br/>"], "\r\n", $body);
    $body = nl2br(trim(strip_tags($body, "<em><strong><span>")));
    $body = str_replace(["\r", "\n"], "", $body);
    $body = str_replace(["<br /><br />", "<br /><br />"], "<br />", $body);
    $body = str_replace("<br />", "<br /><br />", $body);

    $body = parseImagePlaceholders($body);
    $body = parseVideoPlaceholder($noteGuid, $body);

    $body = str_replace("<br /><br /><br />", "<br />", $body);

    $esc_body = $db->real_escape_string($body);

    $q = "UPDATE blogPost SET title = \"$esc_title\",  body = \"{$esc_body}\" WHERE evernoteGuid = \"{$noteGuid}\"";
    $db->query($q);
}