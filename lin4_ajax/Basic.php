<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//設定索引

	//院所基本資料
	

	$sql = "SELECT * FROM sysinfo.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$cname=trim(mb_convert_encoding($value['hpname'],"UTF-8","BIG5"));
		$owner=trim(mb_convert_encoding($value['docname'],"UTF-8","BIG5"));
		$address=trim(mb_convert_encoding($value['addr'],"UTF-8","BIG5"));
		$nhicode=$value['id'];
		$tel=$value['tel'];

		$sql="update leconfig.zhi_basicset set bsname='$cname',bstel='$tel',bsaddr='$address',owner='$owner',nhicode='$nhicode',zip=''";
		echo $sql;

		$conn->exec($sql);
	}
	
	//新增醫師
	$conn->exec("truncate table leconfig.zhi_staff");
	$conn->exec("truncate table eprodb.staff");

	$sql = "SELECT * FROM wdocter.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$no=trim($value['dcno']);
		$name=trim(mb_convert_encoding($value['name'],"UTF-8","BIG5"));
		$id=$value['id'];
		$sex=substr($id,2,1);
		if ($sex=='1' ||$sex=='A' ||$sex=='C' ||$sex=='Y' ){
			$drsex='1';
		}else{
			$drsex='0';
		}
		$sql="insert into eprodb.staff(sfno,sfname,sfid,sfsex,position,drkind,is_ospro,is_endopro,is_peripro,is_pedopro,is_odpro,isowner,issyncgc,created_by,modified_by)values('$no','$name','$id','$drsex','D',0,0,0,0,0,0,0,0,0,0) ";
		echo "$sql<br>";
		$conn->exec($sql);
		$sql="insert into leconfig.zhi_staff(no,name,identity,gender,position,drkind,is_ospro,is_endopro,is_peripro,is_pedopro,is_odpro,isowner,created_at,created_by)values('$no','$name','$id','$drsex','D',0,0,0,0,0,0,0,now(),0) ";
		echo "$sql<br>";
		$conn->exec($sql);
		echo "新增醫師：".$sql."<br>";

	}


	echo "<h1>診所、醫師 資料轉換完成</h1>";

?>