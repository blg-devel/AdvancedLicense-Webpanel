<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include("util/ClientCom.php");
include("util/LicenseInfo.php");
if (!isset($_GET["v1"]) or !isset($_GET["v2"])) exit("URL_ERROR");
$rand_EW_SKey = $_GET["v1"];
$key_EW_rand = $_GET["v2"];

if (isset($_GET["pl"])) $pluginName = $_GET["pl"];
else $pluginName = "UnValidPluginName!";

$clientCom = new ClientCom($rand_EW_SKey, CKAP_KEY);

$usrIP = getUserIP();

$keyBin = $clientCom->decryptToBin($key_EW_rand);

$stingKey = $clientCom->fromBinary($keyBin);

$license = getLicenseInfo($stingKey);

$passed = false;
if ($license != null) {
    if ($license->validateExpiry()) {
        if ($license->validateBound($pluginName)) {
            $passed = $license->handleIp($usrIP);
            if ($passed) {
                echo $clientCom->encryptBin($keyBin);
            } else echo "NOT_VALID_IP";
        } else echo "INVALID_PLUGIN";
    } else echo "KEY_OUTDATED";
} else echo "KEY_NOT_FOUND";

addRequestToStats($passed);
?>


<?php

function addRequestToStats($value = true)
{
    if (STATS) {
        $logFile = fopen("log.txt", "a+");
        if (!$logFile) return;
        fwrite($logFile, intval($value) . '#' . time() . "\n");
        fclose($logFile);
    }
}


function getUserIP()
{
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    return $ip;
}

?>
