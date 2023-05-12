<?php

function breakDown($msg, $alert='0'){
  echo "<head>";
  echo "<link rel='stylesheet' href='css/master.css' type='text/css' charset='utf-8'>";
  echo "</head>";
  echo "<body>";
  $allCalss = "al_info";
  if($alert) $allCalss = "al_alert";
  exit("<div class='$allCalss'>$msg</div> ");
}

if(file_exists('../config.php')) require '../config.php';
elseif (file_exists('config.php')) require 'config.php';
else breakDown("Could not find file 'config.php'");

if(!($link = mysql_connect(HOST, USERNAME, PASSWORD))){
  breakDown("Failed connection to MySQL-Server please make sure that you've entered all
        MySQL-Data correctly into the config.php and that the MySQL-Server is running!", 1);
}else{
  if (!mysql_select_db(DB_NAME, $link)) {
    breakDown("Failed to select db 'Licenses' please make sure that you have already created this data base", 1);
  } else {
    if(!mysql_query("DESCRIBE `licenses`", $link)) {
      mysql_unbuffered_query("CREATE TABLE `licenses` (
        `id` INT NULL AUTO_INCREMENT,
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
      DEFAULT CHARACTER SET = utf8", $link);

      breakDown("The table 'licenses' got createt successfully! Please refresh the page | Step [1/4]");
    }

    if(!mysql_query("DESCRIBE `auth_keys`", $link)) {
      mysql_unbuffered_query("CREATE TABLE `auth_keys` (
        `id` INT NULL AUTO_INCREMENT,
        `user` TEXT NULL,
        `key` TEXT NULL,
        PRIMARY KEY (`id`))
        ENGINE = InnoDB
      DEFAULT CHARACTER SET = utf8", $link);

      breakDown("The table 'auth_keys' got createt successfully! Please refresh the page | Step [2/4]<");
    }

    if(!mysql_query("DESCRIBE `users`", $link)) {
      mysql_unbuffered_query("CREATE TABLE `users` (
        `username` TEXT NULL,
        `password` TEXT NULL )
        ENGINE = InnoDB
      DEFAULT CHARACTER SET = utf8", $link);

      breakDown("The table 'users' got createt successfully! Please refresh the page | Step [3/4]");
    }

    if(!ADMIN_USERNAME OR !ADMIN_PASSWORD) breakDown("You have to enter the data for the Admin-Account in the config.php" ,1);
    else{
      $result = mysql_query("SELECT * FROM `users`", $link);
      if (mysql_num_rows($result) > 0) {
        mysql_unbuffered_query("UPDATE `users` SET
            `username` = '".ADMIN_USERNAME."',
            `password` = '".ADMIN_PASSWORD."'
          WHERE
            `username` = '".mysql_result($result, 0, 'username')."' AND
            `password` = '".mysql_result($result, 0, 'password')."' ", $link);
      } else {
        mysql_unbuffered_query("INSERT INTO `users` (`username`, `password`) VALUES ('".ADMIN_USERNAME."','".ADMIN_PASSWORD."')", $link);
      }
    }
  }
}
?>
