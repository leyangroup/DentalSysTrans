<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//設定索引

	//
	$rs=$conn->query("select id, from leconfig.zhi_staff")

	//掛號表
	$conn->exec("truncate table registration");

	$sql = "SELECT * FROM wthick.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$csn=$value['sno'];
		$cno=trim($value['sinno']);
		$DT=WestDT(trim($value['date']));
		$dr=	
			$tel=trim(mb_convert_encoding(addslashes($value['telno']),"UTF-8","BIG5"));
			$address=trim(mb_convert_encoding(addslashes($value['address']),"UTF-8","BIG5"));
			$mobile=trim(mb_convert_encoding(addslashes($value['telno1']),"UTF-8","BIG5"));
			$firstDT=WestDT(trim($value['date']));
			$lastDT=WestDT(trim($value['lastdate']));
			$zip=$value['zip'];
			$memo=addslashes(trim(mb_convert_encoding($value['remark'],"UTF-8","BIG5")));
			$sql="insert into customer(cussn,cusno,cusname,cusid,cussex,cusbirthday,custel,cusaddr,cusmob,firstdate,lastdate,cuszip,cusmemo)
					values($csn,'$cno','$name','$id',$csex,'$birth','$tel','$address','$mobile','$firstDT','$lastDT','$zip','$memo') ";
			$cnt=$conn->exec($sql);
			if ($cnt==0){
				echo "新增患者失敗：".$sql."<br>";
			}
	}
	echo "<h1>患者 資料轉換完成</h1>";

?>