<?php
	include_once "../include/db.php";

    $conn=MariaDBConnect();

    $path=$_GET['path'];

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$path);

	//掛號-處置-處方
	$conn->exec("truncate table eprodb.staff");
	$conn->exec("truncate table leconfig.zhi_staff");

	$sql = "SELECT * FROM z_conf.dbf";
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$r++;
		$ukey=trim($value['ukey']);
		$id=trim($value['id']);
		$name=trim(mb_convert_encoding(addslashes($value['name']),"UTF-8","BIG5"));
		$posi=trim(mb_convert_encoding(addslashes($value['posi']),"UTF-8","BIG5"));
		switch ($posi) {
			case '醫師':
				$position='D';
				break;
			case '護士':
				$position='A';
				break;
		}
		//申報醫師，用sfemail來存
		$giadr=$value['photo'];
		$sql="insert into staff(sfno,sfname,sfid,position,sfemail)values('$ukey','$name','$id','$position','$giadr')";
		echo $sql."<br>";
		$conn->exec($sql);

		$sql="insert into leconfig.zhi_staff(no,name,identity,position,gender,drkind,is_ospro,is_endopro,is_peripro,is_pedopro,is_odpro,isowner,created_at,created_by)
				values('$ukey','$name','$id','$position',0,0,0,0,0,0,0,0,now(),0)";
		echo $sql."<br>";
		$conn->exec($sql);
	}

	$sql = "SELECT * FROM z_confbackup.dbf";
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$r++;
		$ukey=trim($value['ukey']);
		$id=trim($value['id']);
		$name=trim(mb_convert_encoding(addslashes($value['name']),"UTF-8","BIG5"));
		$posi=trim(mb_convert_encoding(addslashes($value['posi']),"UTF-8","BIG5"));
		switch ($posi) {
			case '醫師':
				$position='D';
				break;
			case '護士':
				$position='A';
				break;
		}
		//申報醫師，用sfemail來存
		$giadr=$value['photo'];
		$sql="insert into staff(sfno,sfname,sfid,position,sfemail)values('$ukey','$name','$id','$position','$giadr')";
		echo $sql."<br>";
		$conn->exec($sql);

		$sql="insert into leconfig.zhi_staff(no,name,identity,position,gender,drkind,is_ospro,is_endopro,is_peripro,is_pedopro,is_odpro,isowner,created_at,created_by)
				values('$ukey','$name','$id','$position',0,0,0,0,0,0,0,0,now(),0)";
		echo $sql."<br>";
		$conn->exec($sql);
	}
	echo "<br>醫師資料轉換完畢!!";

?>