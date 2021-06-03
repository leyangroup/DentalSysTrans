<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    $conn->exec("ALTER TABLE `oemaster` CHANGE `cussn` `cussn` INT(10) NOT NULL DEFAULT '0' COMMENT '患者序號' ");
	echo "<h1>索引建立 完成</h1>";

?>