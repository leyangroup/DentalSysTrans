<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];
    
    echo "建立索引";
    $sql="ALTER TABLE `treat_record` ADD INDEX( `icuploadD`, `uploadD`)";
    $mariaConn->exec($sql);  
    $mariaConn->exec("ALTER TABLE `treat_record` ADD INDEX(`uploadD`)");

    $sql="ALTER TABLE `registration` ADD INDEX( `icuploadD`, `uploadD`)";
    $mariaConn->exec($sql);  
    $mariaConn->exec("ALTER TABLE `registration` ADD INDEX(`uploadD`)");

    $sql="ALTER TABLE `prescription` ADD INDEX( `uploadD`, `icuploadd`)";
    $mariaConn->exec($sql);  

    $mariaConn->exec("ALTER TABLE `customer` ADD INDEX(`cusno`)"); 

    $mariaConn->exec("ALTER TABLE `treatment` ADD INDEX(`nhicode`)");

    $mariaConn->exec("ALTER TABLE `prescription` ADD INDEX(`times`)");

    $mariaConn->exec("ALTER TABLE `nhicode` ADD INDEX(`nhicode`)");

    echo "<h1>索引建立完畢</h1>";
?>