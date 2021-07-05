<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'].'\dent01_10.mdb';

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);
	$sql = "SELECT * FROM THMKPT";
	$result = $db->query($sql);
	$conn->exec("delete from registration where seqno='000' ");
	foreach ($result as $key => $value) {
		$cusid=$value['IDNO'];  // 暫存在uploadno中
		if (strlen(trim($value['DATE'])<7)){
			$regDT='';
		}else{
			$regDT=WestDT($value['DATE']);
		}
		$note=addslashes(mb_convert_encoding($value['TODO'],"UTF-8","BIG5"));
		$time=$value['TM'];
		$sql="insert into registration(ddate,seqno,uploadno,sch_time,schlen,sch_note,drno1,drno2)
				values('$regDT','000','$cusid','$time',15,'$note',1,1)";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增預約資料失敗 $sql";
		}
	}

	$conn->exec("update registration r,customer c 
					set r.cussn=c.cussn,r.cusno=c.cusno
				  where seqno='000'
					and r.uploadno=cusid");
	echo "<h1>新增預約資料完成</h1>";
?>




