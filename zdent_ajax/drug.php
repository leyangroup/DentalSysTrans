<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'];

    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$path);

	$conn->exec("truncate table leconfig.zhi_drug");
    $sql = "SELECT * FROM z_drug.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$code=trim($value['code']);
		$name=trim(mb_convert_encoding(addslashes($value['name']),"UTF-8","BIG5"));
		$times=trim($value['freq']);
		$part=trim($value['baway']);
		$price=(trim($value['price'])=='')?0:$value['price'];
		$sql="insert into leconfig.zhi_drug(name,drugno,nhicode,nhifee,dose,part,times,status,created_at,created_by,is_lockQ,amt)
				values ('$name','$code','$code',$price,'01','$part','$times',0,now(),0,0,0)";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "$sql<br>";
		}
	}
	echo "<br>藥品資料轉換完畢!!";
	