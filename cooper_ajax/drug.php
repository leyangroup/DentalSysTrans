<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//藥品檔
	$conn->exec("truncate table drug");
	$sql = "SELECT * FROM drug";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '藥品代號':
					$drugno=$v2;
					break;

				case '藥品名稱':
					$drugname=mb_convert_encoding(trim($v2),"UTF-8","BIG5");
					break;

				case '健保單價':
					$nhifee=$v2;
					break;

				case '每次劑量':
					$dose=$v2;
					break;

				case '使用頻率':
					$times=trim($v2);
					break;

				case '給藥途徑':
					$part=trim($v2);
					break;
				case '停用日期':
					if (trim($v2)==''){
						$isuse=0;
					}else{
						$isuse=1;
					}
					
					break;
			}
		}
		$sql="insert into drug(drugno,name,nhicode,nhifee,fee,dose,part,times,is_use)
				values('$drugno','$drugname','$drugno',$nhifee,$nhifee,$dose,'$part','$times',$isuse)";
		echo "藥品：".$sql."<br>";
		//echo "$drugno-$drugname 。 ";
		$conn->exec($sql);		
	}

	//更新times
	$sql="insert into drug_times(timesno) SELECT distinct times FROM `drug` where times not in (select timesno from drug_times) ";
	echo $sql."<br>";
	$conn->exec($sql);

	// 更新part
	$sql="insert into drug_part(useno) SELECT distinct part FROM drug where part not in (select useno from drug_part) ";
	echo $sql."<br>";
	$conn->exec($sql);

	//更新dose


	//更新drug中的dose
	$sql="update drug d, drug_dose s set d.dose=s.doseno where d.dose=s.qty";
	$conn->exec($sql);

	$sql="truncate table drugcom";
	$conn->exec($sql);

	$sql="truncate table drugcomdetails";
	$conn->exec($sql);

	//組合 PRESCR , PRESCRS

	$sql = "SELECT * FROM prescr";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '處方代號':
					$no=mb_convert_encoding(trim($v2),"UTF-8","BIG5");
					break;
				case '處方名稱':
					$name=mb_convert_encoding(trim($v2),"UTF-8","BIG5");
					break;

			}
		}
		$sql="insert into drugcom(comno,name,fee,drugfee,day) values('$no','$name',0,0,0)";
		echo $sql;
		$conn->exec($sql);
	}


	$sql = "SELECT * FROM prescrs";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '處方代號':
					$comno=mb_convert_encoding(trim($v2),"UTF-8","BIG5");
					break;
				case '藥品代號':
					$drugno=trim($v2);
					break;
				case '每次劑量':
					$dose=$v2;
					break;

				case '藥品用量':
					$qty=$v2;
					break;
				case '最高天數':
					$day=$v2;
					break;
				case '使用頻率':
					$times=trim($v2);
					break;
				case '給藥途徑':
					$part=trim($v2);
					break;
					
			}
		}
		$sql="insert into drugcomdetails(comdno,drugno,fee,drugfee,dose,times,day) values('$comno','$drugno',0,0,'$dose','$times',$day)";
		echo $sql;
		$conn->exec($sql);
	}


	$sql="update drugcomdetails set dose=substr(concat('0',dose),-2)";
	$conn->exec($sql);

	echo "<h1>藥品轉換完成";

?>