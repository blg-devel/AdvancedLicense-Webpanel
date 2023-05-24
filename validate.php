<?php
include_once __DIR__ . "/util/ClientCom.php";
include_once __DIR__ . "/util/TokenManager.php";
include_once __DIR__ . "/util/ConnectionUtil.php";

error_reporting(E_ERROR | E_WARNING | E_PARSE);
if (!isset($_GET["v1"]) or !isset($_GET["v2"])) exit("URL_ERROR");

$rand_EW_SKey = $_GET["v1"];
$token_EW_rand = $_GET["v2"];
$pluginName = isset($_GET["pl"]) ? $_GET["pl"] : null;

$clientCom = new ClientCom($rand_EW_SKey, CKAP_KEY_CLIENT);

echo(validateToken($clientCom->decrypt($token_EW_rand), getUserIP(), $pluginName));
