<?php

require_once __DIR__ . "/connect.php";

$action = $_GET["action"];

if (isset($_COOKIE['auth_key']) and
    $link->query("SELECT * FROM `auth_keys` WHERE `key`='" . $link->real_escape_string($_COOKIE['auth_key']) . "'")->num_rows > 0) {
    if ($action == "create") {
        try {
            $key = $link->real_escape_string(strip_tags($_GET["key"]));
            $ips = $link->real_escape_string(strip_tags($_GET["ips"]));
            $expDate = $link->real_escape_string(strip_tags($_GET["expDate"]));
            if ($expDate == "null") $expDate = -1;
            $dName = $link->real_escape_string(strip_tags($_GET["dName"]));
            $dDesc = $link->real_escape_string(strip_tags($_GET["dDesc"]));
            $dClient = $link->real_escape_string(strip_tags($_GET["dClient"]));
            $dBound = $link->real_escape_string(strip_tags($_GET["dBound"]));
            if ($dBound == "true") $dBound = 1;
            else $dBound = 0;

            $link->query("INSERT INTO `licenses` (`key`, `ips`, `expiry`, `plName`, `plDesc`, `plClient`, `plBound`) VALUES
               ('$key', '$ips', '$expDate', '$dName', '$dDesc', '$dClient', '$dBound')");
            echo "SUCCESS!";
        } catch (Exception $e) {
            echo 'FAILED! Error:' . $e->getMessage();
        }
    }

    if ($action == "delete") {
        $link->query("DELETE FROM `licenses` WHERE `id`='" . $link->real_escape_string($_GET["id"]) . "'");
    }
} else echo "FAILED! You are not logged in";
