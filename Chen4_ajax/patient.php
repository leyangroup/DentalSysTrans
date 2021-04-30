<?php
	include_once "../include/db.php";

    $conn=MariaDBConnect();

    $path=$_GET['path'].'\dent01_10.mdb';

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);
	$sql = "SELECT * FROM thcstfl";
	$result = $db->query($sql);
	$conn->exec("truncate table customer");
	foreach ($result as $key => $value) {
		$cusno=addslashes(mb_convert_encoding($value['sino'],"UTF-8","BIG5"));
		$cusname=addslashes(mb_convert_encoding($value['name'],"UTF-8","BIG5"));
		$cusid=$value['idno'];
		$custel=addslashes(mb_convert_encoding($value['telno'],"UTF-8","BIG5"));
		if (strlen($value['birth'])<7){
			$cusbirthdy='';
		}else{
			$yy=substr($value['birth'],0,3)+1911;
			$mm=substr($value['birth'],3,2);
			$dd=substr($value['birth'],-2);
			$cusbirthday=$yy.'-'.$mm.'-'.$dd;
		}

		if (strlen($value['date'])<7){
			$firstdate='';
		}else{
			$yy=substr($value['date'],0,3)+1911;
			$mm=substr($value['date'],3,2);
			$dd=substr($value['date'],-2);
			$firstdate=$yy.'-'.$mm.'-'.$dd;
		}

		if ($value['date2']==''){
			$lastdate='';
		}else{
			$yy=substr($value['date2'],0,3)+1911;
			$mm=substr($value['date2'],3,2);
			$dd=substr($value['date2'],-2);
			$lastdate=$yy.'-'.$mm.'-'.$dd;
		}

		$remark=addslashes(mb_convert_encoding($value['remark'],"UTF-8","BIG5"));
		$cusintro=addslashes(mb_convert_encoding($value['introduce'],"UTF-8","BIG5"));
		$insurance=addslashes(mb_convert_encoding($value['insurance'],"UTF-8","BIG5"));
		$email=addslashes(mb_convert_encoding($value['email'],"UTF-8","BIG5"));
		$disease=addslashes(mb_convert_encoding($value['disease'],"UTF-8","BIG5"));
		$other=addslashes(mb_convert_encoding($value['other'],"UTF-8","BIG5"));
		$cusmob=addslashes(mb_convert_encoding($value['mobile'],"UTF-8","BIG5"));
		$cusaddr=addslashes(mb_convert_encoding($value['address'],"UTF-8","BIG5"));
		$memo=$remark.' '.$disease.' '.$other;

		$sql="insert into customer(cusno,cusname,cusid,cusbirthday,custel,cusmob,firstdate,lastdate,cusmemo,cusintro,cusemail)
				value('$cusno','$cusname','$cusid','$cusbirthday','$custel','$cusmob','$firstdate','$lastdate','$memo','$cusintro','$email')";

		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增患者基本資料失敗 $sql";
		}
	}
	echo "<h1>新增患者基本資料結束</h1>";
	
?>





