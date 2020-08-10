<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//醫師資料
	
	//清除患者基本資料表
	$conn->exec("truncate table leconfig.zhi_staff");
	$conn->exec("truncate table eprodb.staff");

	//患者基本資料
	$sql = "SELECT * FROM people.dat";
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$r++;
		$drno=$value['serial'];
		$drname=trim(mb_convert_encoding($value['name'],"UTF-8","BIG5"));
		$id=$value['id'];
		$sex=(substr($id,1,1)==1)?'1':'0';
		$sql="insert into leconfig.zhi_staff
					(no,name,identity,position,gender,drkind,is_ospro,is_endopro,is_peripro,is_pedopro,is_odpro,isowner,created_at,created_by) 
			values('$drno','$drname','$id','D',$sex,'0',0,0,0,0,0,0,now(),0)";
		echo "$sql<br>";
		$conn->exec($sql);


		$sql="insert into eprodb.staff
					(sfno,sfname,sfid,position,drkind,is_ospro,is_endopro,is_peripro,is_pedopro,is_odpro,isowner) 
			values('$drno','$drname','$id','D','0',0,0,0,0,0,0)";
		echo "$sql<br>";
		$conn->exec($sql);
	}
	echo "<h1>醫師資料 轉換完成</h1>";

?>