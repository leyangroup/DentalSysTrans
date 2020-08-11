<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//醫師資料
	
	//清除患者基本資料表
	$conn->exec("truncate table icd10");

	//患者基本資料
	$sql = "SELECT * FROM diagnose.dat";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$code=$value['A_code'];
		$ename=$value['sick_name'];
		$cname=$value['sick_name1'];
		$sql="insert into icd10 values('$code','$ename','$cname')";
		$conn->exec($sql);
		
	}
	echo "<h1>醫師資料 轉換完成</h1>";

?>