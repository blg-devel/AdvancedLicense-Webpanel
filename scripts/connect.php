<?php

require __DIR__ . "/../config.php";
require_once __DIR__ . "/../util/UserManager.php";

function mysqli_result($res, $row, $field = 0)
{
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}

function breakDown($msg, $alert = '0')
{
    echo "<head>";
    echo "<link rel='stylesheet' href='css/master.css' type='text/css' charset='utf-8'>";
    echo "</head>";
    echo "<body>";
    $allCalss = "al_info";
    if ($alert) $allCalss = "al_alert";
    exit("<div class='$allCalss'>$msg</div> ");
}

$link = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
if ($link->connect_error) {
    breakDown("Failed connection to MySQL-Server please make sure that you've entered all
        MySQL-Data correctly into the config.php and that the MySQL-Server is running!", 1);
} else {

    $link->query("CREATE TABLE IF NOT EXISTS `licenses` (
      `key` VARCHAR(255) NOT NULL,
      `ips` INT DEFAULT '1',
      `expiry` BIGINT NULL,
      `plBound` INT DEFAULT 0,
      `plName` VARCHAR(255) NULL,
      `plDesc` TEXT NULL,
      `plClient` VARCHAR(255) NULL,
      `lastRef` TEXT NULL,
      `currIPs` TEXT NULL,
      PRIMARY KEY (`key`))");

    $link->query("CREATE TABLE IF NOT EXISTS `user_tokens` (
      `token` VARCHAR(31) NOT NULL,
      `ip` VARCHAR(100) NOT NULL,
      `license` VARCHAR(255) NOT NULL,
      `expiry` BIGINT,
       PRIMARY KEY (`token`))");

    $link->query("CREATE TABLE IF NOT EXISTS `auth_keys` (
      `user` VARCHAR(255) NOT NULL,
      `key` VARCHAR(10) NOT NULL,
      PRIMARY KEY (`user`))");


    $link->query("CREATE TABLE IF NOT EXISTS `users` (
      `username` VARCHAR(255) NOT NULL,
      `password` VARCHAR(255) NOT NULL,
       PRIMARY KEY (`username`))");

    if (!ADMIN_USERNAME or !ADMIN_PASSWORD) {
        breakDown("You have to enter the data for the Admin-Account in the config.php", 1);
    } else {
        $result = $link->query("SELECT * FROM `users`");
        if ($result->num_rows <= 0) {
            addUser(ADMIN_USERNAME, ADMIN_PASSWORD);
        } else {
            if (!userExists(ADMIN_USERNAME)) {
                addUser(ADMIN_USERNAME, ADMIN_PASSWORD);
            } else if (!validateUser(ADMIN_USERNAME, ADMIN_PASSWORD)) {
                changePassword(ADMIN_USERNAME, ADMIN_PASSWORD);
            }
        }
    }
}
