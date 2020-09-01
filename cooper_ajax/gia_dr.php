<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=c:\cooper");
	
	//新增

	//產生指導醫師 暫存資料表
	$conn->exec("drop table if exists tmp_Dr");
	$conn->exec("Create table tmp_Dr(maindr varchar(10),giadr varchar(10))");

	$conn->exec("truncate table staff");
	// $conn->exec("ALTER TABLE staff AUTO_INCREMENT=1000");
	$sql = "SELECT * FROM doctor.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $key2=> $value2) {
			switch (mb_convert_encoding($key2,"UTF-8","BIG5")) {
				case '醫師代號':
					$drno=trim($value2);
					break;
				case '指導醫師':
					$giadr=trim($value2);
				}
		}
		$gmail=trim($value['gmail']);
		$sql="insert into tmp_Dr(maindr,giadr)values
					('$drno','$giadr') ";

		$conn->exec($sql);
	}

	
	echo "<h1>完成</h1>";

?>