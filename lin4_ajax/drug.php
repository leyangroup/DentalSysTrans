<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	
	//藥品
	$conn->exec("truncate table leconfig.zhi_drug");
	$conn->exec("truncate table leconfig.zhi_drug_times");

	$sql = "SELECT * FROM wdname.dbf";
	$result=$db->query($sql);
	//用uploadd, icuploadd來存資料，uploadd存cussn,icuploadd存第幾次來 這樣才能對應
	foreach ($result as $key => $value) {
		$no=$value['drugno'];
		$name=$value['dname'];
		$unit=$value['unit'];
		$price=$value['nhifee'];
		$nhicode=$value['drugid'];
		$part=$value['po'];
		$times=$value['dfrq'];


		$sql="insert into leconfig.zhi_drug(name,drugno,nhicode,nhifee,dose,part,times,unit";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增藥品失敗：".$sql."<br>";
		}
	}
	echo "<h1>藥品 資料轉換完成</h1>";

?>