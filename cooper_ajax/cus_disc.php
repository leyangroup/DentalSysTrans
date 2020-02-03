<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);


	$conn->exec("drop table if exists cus_tmp");
	$conn->exec("create table cus_tmp (cusno varchar(10),disc varchar(4))");

	//新增患者
	$sql = "SELECT * FROM patient.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=trim($v2);
					break;
				case '優免身份':
			 		$disc=trim($v2);
					break;
			}
		}
		if ($disc!=''){
			$sql="insert into cus_tmp (cusno,disc)values('$cusno','$disc')";
			echo $sql."<br>";
			$conn->exec($sql);
		}
	}
	echo "<h1>優待身份完成";


?>