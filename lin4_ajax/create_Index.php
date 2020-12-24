<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    
	//設定索引
	$conn->exec("ALTER TABLE registration ADD INDEX(cussn,uploadd)");
	$conn->exec("ALTER TABLE treat_record ADD INDEX(cussn,uploadd)");
	$conn->exec("ALTER TABLE prescription ADD INDEX(uploadd,icuploadd)");
	

?>