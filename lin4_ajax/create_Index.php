<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    
	//設定索引
	$conn->exec("truncate table registration");
	$conn->exec("truncate table treat_record");
	$conn->exec("truncate table prescription");

	$conn->exec("ALTER TABLE registration ADD INDEX(cussn,uploadd)");
	$conn->exec("ALTER TABLE treat_record ADD INDEX(cussn,uploadd)");
	$conn->exec("ALTER TABLE prescription ADD INDEX(uploadd,icuploadd)");
	
	echo "<h1>ok</h1>";
?>