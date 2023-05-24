<?php

require_once __DIR__ . "/../scripts/connect.php";

const PASSWORD_HASH_ALGO = PASSWORD_DEFAULT; //TODO save algorithm

function addUser($name, $password)
{
    global $link;
    $hash = password_hash($password, PASSWORD_HASH_ALGO);
    $stmt = $link->prepare("INSERT INTO `users` (`username`, `password`) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $hash);
    $stmt->execute();
}

function removeUser($name)
{
    global $link;
    $stmt = $link->prepare("DELETE FROM `users` WHERE `username` = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

function changePassword($name, $password)
{
    global $link;
    $hash = password_hash($password, PASSWORD_HASH_ALGO);
    $stmt = $link->prepare("UPDATE `users` SET `password` = ? WHERE  `username` = ?");
    $stmt->bind_param("ss", $hash, $name);
    $stmt->execute();
}

function validateUser($name, $password)
{
    $hash = getPasswordHash($name);
    if ($hash == null) return false;
    return password_verify($password, $hash);
}

function getPasswordHash($name)
{
    global $link;
    $stmt = $link->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        return mysqli_result($res, 0, "password");
    }
    return null;
}

function userExists($name)
{
    global $link;
    $stmt = $link->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->num_rows > 0;
}