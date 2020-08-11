<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	
	$conn->exec("ALTER TABLE `registration` ADD INDEX( `ddate`, `hospno`);");
	
	echo "<h1>索引 建立完成</h1>";

?>