<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include("util/ClientCom.php");
include("util/LicenseInfo.php");
require_once __DIR__ . "/util/TokenManager.php";
require_once __DIR__ . "/util/ConnectionUtil.php";

if (!isset($_GET["v1"]) or !isset($_GET["v2"])) exit("URL_ERROR");
$rand_EW_SKey = $_GET["v1"];
$key_EW_rand = $_GET["v2"];
$clientIp_EW_rand = isset($_GET["v3"]) ? $_GET["v3"] : null;

$pluginName = isset($_GET["pl"]) ? $_GET["pl"] : null;

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
                if ($clientIp_EW_rand != null) {
                    echo $clientCom->encrypt("TOKEN" . getNewToken($stingKey, $clientCom->decrypt($clientIp_EW_rand)));
                } else {
                    echo $clientCom->encryptBin($keyBin);
                }
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

?>
