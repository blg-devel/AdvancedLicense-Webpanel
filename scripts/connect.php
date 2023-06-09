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
      `id` INT AUTO_INCREMENT,
      `key` TEXT NULL,
      `ips` INT NULL DEFAULT '1',
      `expiry` BIGINT NULL,
      `plBound` INT NULL DEFAULT 0,
      `plName` TEXT NULL,
      `plDesc` TEXT NULL,
      `plClient` TEXT NULL,
      `lastRef` TEXT NULL,
      `currIPs` TEXT NULL,
      PRIMARY KEY (`id`))
      ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8");

    $link->query("CREATE TABLE IF NOT EXISTS `user_tokens` (
      `token` TEXT NULL,
      `ip` TEXT NULL,
      `license` TEXT NULL,
      `expiry` BIGINT NULL,
       UNIQUE INDEX `idx_token_unique` (`token`))
      ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8");

    $link->query("CREATE TABLE IF NOT EXISTS `auth_keys` (
      `id` INT AUTO_INCREMENT,
      `user` TEXT NULL,
      `key` TEXT NULL,
      PRIMARY KEY (`id`))
      ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8");


    $link->query("CREATE TABLE IF NOT EXISTS `users` (
      `username` TEXT NULL,
      `password` TEXT NULL,
       UNIQUE INDEX `idx_username_unique` (`username`))
      ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8");

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
