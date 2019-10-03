<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=C:\cooper");

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
		// echo "藥品：".$sql."<br>";
		echo "$drugno-$drugname 。 ";
		$conn->exec($sql);		
	}

	//更新drug中的dose
	$sql="update drug d, drug_dose s set d.dose=s.doseno where d.dose=s.qty";
	$conn->exec($sql);
	echo "<h1>藥品轉換完成";

	$sql="truncate table drugcom";
	$conn->exec($sql);

	$sql="truncate table drugcom_details";
	$conn->exec($sql);

	


?>