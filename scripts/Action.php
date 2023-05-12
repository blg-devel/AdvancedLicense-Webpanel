<?php

if(file_exists('connect.php')) require 'connect.php';
elseif (file_exists('scripts/connect.php')) require 'scripts/connect.php';
else exit("Could not find file 'scripts/connect.php'");

$action = $_GET["action"];

if($action == "create"){
  try {
    $key     = $_GET["key"];
    $ips     = $_GET["ips"];
    $expDate = $_GET["expDate"];
      if($expDate == "null") $expDate = -1;
    $dName   = $_GET["dName"];
    $dDesc   = $_GET["dDesc"];
    $dClient = $_GET["dClient"];
    $dBound  = $_GET["dBound"];
      if($dBound == "true") $dBound = 1;
      else $dBound = 0;

    mysql_query("INSERT INTO `licenses` (`key`, `ips`, `expiry`, `plName`, `plDesc`, `plClient`, `plBound`) VALUES
             ('$key', '$ips', '$expDate', '$dName', '$dDesc', '$dClient', '$dBound')", $link);
    echo "SUCCESS!";
  }catch(Exception $e) {
  echo 'FAILED! Error:' .$e->getMessage();
  }
}

if($action == "delete"){
  mysql_query("DELETE FROM `licenses` WHERE `id`='".$_GET["id"]."'", $link);
}

?>
