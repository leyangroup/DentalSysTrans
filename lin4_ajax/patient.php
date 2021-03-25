<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	$no1=isset($_GET['no1'])?$_GET['no1']:0;
	$no2=isset($_GET['no2'])?$_GET['no2']:0;
	//設定索引

	//新增患者
	if ($no1==0){
		$conn->exec("truncate table customer");
	}

	$sql = "SELECT * FROM wcust.dbf";
	$result=$db->query($sql);
	echo "no1=$no1 , no2=$no2 ";
	foreach ($result as $key => $value) {
		if ($no1==0 || ($value['sno']>=$no1 && $value['sno']<=$no2)){
			$csn=$value['sno'];
			$cno=trim($value['sinno']);
			$name=trim(mb_convert_encoding(addslashes($value['name']),"UTF-8","BIG5"));
			$id=addslashes(trim($value['idno']));
			$csex=($value['sex']=='2')?'0':'1';
			$birth=WestDT(trim($value['birthd']));
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
	}
	echo "<h1>患者 資料轉換完成</h1>";

?>