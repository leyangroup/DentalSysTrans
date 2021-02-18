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
		$no=trim($value['drugno']);
		$name=trim(mb_convert_encoding($value['dname'],"UTF-8","BIG5"));
		$unit=trim(mb_convert_encoding($value['unit'],"UTF-8","BIG5"));
		$price=$value['dprice'];
		$nhicode=$value['drugid'];
		$part=trim(mb_convert_encoding($value['po'],"UTF-8","BIG5"));
		$times=$value['dfrq'];

		$sql="insert into leconfig.zhi_drug(name,drugno,nhicode,nhifee,dose,part,times,unit,is_lockq,amt,created_at,created_by,status)
				values('$name','$no','$nhicode',$price,'01','$part','$times','$unit',0,0,now(),0,0)";
		$iok=$conn->exec($sql);
		if ($iok==0){
			echo "新增藥品失敗：".$sql."<br>";
		}
	}
	$sql="insert into leconfig.zhi_drug_times (code,created_at,created_by) select distinct times,now(),0 from eprodb.prescription");
	//要再進去填資料，找看看有沒有這個資料表
	$conn->exec($sql);
	
	echo "<h1>藥品 資料轉換完成</h1>";

?>