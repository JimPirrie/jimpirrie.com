<?php

$twigData["seo"]["title"] = "Professional Development Coaching | Jim Pirrie Coaching";
$twigData["seo"]["description"] = "";
$twigData["seo"]["image"] = "";

$twigData["active"]["workshops"] = "active";

$twigData["template"] = "workshop.html.twig";

$twigData["active"]["foundations"] = "_active";

$twigData["title"] = "Foundations of Professional Effectiveness";
$twigData["image"] = "root.png";
$twigData["lead"] = "Professionalism is about more than technical expertise: it's equally about self-belief and confidence, and knowing how to hold your own under pressure.";
$twigData["summary"] = "In this workshop you'll explore why some people come across as authoritative and professional, and others don't. You'll experience practical ways of building your own personal and professional self-assurance, and become better prepared to make a name for yourself as a safe and professional pair of hands...";

ob_start();
?>
<p>
    COPY
</p>
<?php

$twigData["copy"] = ob_get_clean();