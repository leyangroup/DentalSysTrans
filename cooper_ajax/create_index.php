<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//先建立資料表的索引
	$conn->exec("ALTER TABLE `disc_list` CHANGE `discid` `discid` VARCHAR(4) CHARACTER 
		SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '優免身分代號' ");

	$conn->exec("ALTER TABLE `customer` ADD INDEX(`cusno`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX( `cusno`, `stdate`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX(`ic_datetime`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX( `cussn`, `ic_seqno`)");

	$conn->exec("ALTER TABLE `treat_record` ADD INDEX( `uploadD`, `SICKN`)");
	$conn->exec("ALTER TABLE `treat_record` ADD INDEX( `cussn`, `start_icseq`)");

	$conn->exec("ALTER TABLE `prescription` ADD INDEX( `uploadD`, `icuploadd`)");

	$conn->exec("ALTER TABLE `treatment` ADD INDEX(`nhicode`)");
	
	$conn->exec("ALTER TABLE `nhicode` ADD INDEX(`nhicode`)");
	
	$conn->exec("ALTER TABLE `zip` ADD INDEX(`zip`)");

	$conn->exec("ALTER TABLE `zip` ADD INDEX( `county`, `city`)");
	echo "<h1>索引建立 完成</h1>";

?>