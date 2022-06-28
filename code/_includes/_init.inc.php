<?php

session_start();
error_reporting(E_ERROR);

require_once(__DIR__."/../vendor/autoload.php");
require_once("env.inc.php");
require_once("render.inc.php");
require_once("evernote.inc.php");

global $twigData;

global $db;
$db = new mysqli("localhost", dbKeys("username"), dbKeys("password"), dbKeys("dbName")) or die('Could not connect: ' . mysqli_error($db));

$twigData["isProductionSite"] = isProductionSite();
$twigData["siteUrl"] = siteurl();

