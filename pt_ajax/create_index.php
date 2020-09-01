<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	
	$conn->exec("ALTER TABLE `customer` ADD INDEX(`cusno`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX(`cusno`)");
	$conn->exec("ALTER TABLE `registration` ADD INDEX(`stdate`)");
	$conn->exec("ALTER TABLE `treat_record` ADD INDEX(`icuploadD`);");

	echo "<h1>索引 建立完成</h1>";

?>