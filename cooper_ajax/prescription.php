<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//處方箋
	$conn->exec("truncate table prescription");
	$sql = "SELECT * FROM op_drug";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '就診次數':
					$cnt=$v2;
					break;
				case '健保藥品碼':
					$nhicode=$v2;
					break;
				case '每次劑量':
					$dose=$v2;
					break;
				case '每日用量':
					$dayuse=$v2;
					break;
				case '天數':
					$days=$v2;
					break;
				case '健保單價':
					$price=$v2;
					break;
				case '使用頻率':
					$freq=trim($v2);
					break;
				case '給藥途徑':
					$part=trim($v2);
					break;
			}
		}
		$total=$dayuse*$days;
		$amt=$total*$price;
		$sql="insert into prescription (regsn,drugno,nhidrugno,qty,totalQ,dose,part,times,uprice,amount,day,uploadd,icuploadd)
					value(0,'$nhicode','$nhicode',$dayuse,$total,$dose,'$part','$freq','$price',$amt,$days,'$cusno','$cnt')";

		echo $cusno."-".$cnt." 。 ";
		$conn->exec($sql);
	}

	//修正dose

	$sql="update prescription p,drug_dose d set p.dose=d.doseno where p.dose=d.qty";
	$conn->exec($sql);

	$sql="update registration r,prescription p 
		     set p.ddate=r.ddate,p.seqno=r.seqno,p.regsn=r.regsn
		   where r.cusno=p.uploadD
		     and r.stdate=p.icuploadd";
	$conn->exec($sql);
	echo "<h1>處方箋轉換完畢... ....";

?>