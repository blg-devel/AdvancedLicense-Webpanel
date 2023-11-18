<?php

global $link;
require_once __DIR__ . "/connect.php";

$action = $_GET["action"];

$key_present = false;
if (isset($_COOKIE['auth_key'])) {
    $select_key_stmt = $link->prepare("SELECT * FROM `auth_keys` WHERE `key`= ?");
    $select_key_stmt->bind_param("s", $_COOKIE['auth_key']);
    $select_key_stmt->execute();
    $res = $select_key_stmt->get_result();
    $key_present = $res->num_rows > 0;
}

if ($key_present) {
    if ($action == "create") {
        try {
            $key = strip_tags($_GET["key"]);
            $ips = strip_tags($_GET["ips"]);
            $expDate = strip_tags($_GET["expDate"]);
            if ($expDate == "null") $expDate = -1;
            $dName = strip_tags($_GET["dName"]);
            $dDesc = strip_tags($_GET["dDesc"]);
            $dClient = strip_tags($_GET["dClient"]);
            $dBound = strip_tags($_GET["dBound"]);
            if ($dBound == "true") $dBound = 1;
            else $dBound = 0;

            $stmt = $link->prepare("INSERT INTO `licenses` 
                (`key`, `ips`, `expiry`, `plName`, `plDesc`, `plClient`, `plBound` ) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siiisss", $key, $ips, $expDate, $dName, $dDesc, $dClient, $dBound);
            $stmt->execute();

            echo "SUCCESS!";
        } catch (Exception $e) {
            echo 'FAILED! Error:' . $e->getMessage();
        }
    }

    if ($action == "delete") {
        $stmt = $link->prepare("DELETE FROM `licenses` WHERE `id`= ?");
        $stmt->bind_param("s", $_GET["id"]);
        $stmt->execute();
    }
} else echo "FAILED! You are not logged in";
