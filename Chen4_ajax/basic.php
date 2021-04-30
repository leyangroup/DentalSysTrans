<?php
	include_once "../include/db.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'].'\dent01_10.mdb';

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);
	$sql = "SELECT * FROM thtitle";
	$result = $db->query($sql);
	$conn->exec("truncate table leconfig.zhi_basicset");
	foreach ($result as $key => $value) {
		$clinicname=mb_convert_encoding($value['clinic_name'],"UTF-8","BIG5");
		$nhicode=$value['clinic_mno'];
		$owner=mb_convert_encoding($value['doct_name'],"UTF-8","BIG5");
		$tel=$value['clinic_telno'];
		$address=mb_convert_encoding($value['clinic_address'],"UTF-8","BIG5");
		echo "$clinicname,$nhicode,$owner,$tel,$address";
		$sql="insert into leconfig.zhi_basicset(bsname,bstel,bsaddr,owner,nhicode,bsfax) 
				values('$clinicname','$tel','$address','$owner','$nhicode','')";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增診所基本資料失敗 $sql";
		}
		echo "<h1>新增診所基本資料完成</h1>";
	}
	
	$conn->exec("truncate table eprodb.staff");
	$conn->exec("truncate table leconfig.zhi_staff");

	$sql = "SELECT * FROM thdocter";
	$result = $db->query($sql);
	foreach ($result as $key => $value) {
		$no=mb_convert_encoding($value['no'],"UTF-8","BIG5");
		$name=mb_convert_encoding($value['name'],"UTF-8","BIG5");
		$id=$value['idno'];
		$sex=(substr($id,1,1)=='1')?'1':'0';

		$sql="insert into eprodb.staff(sfno,sfname,sfid,sfsex,position,drkind,is_ospro,is_endopro,is_peripro
										,is_pedopro,is_odpro,isowner,issyncgc,created_by,modified_by)
				values('$no','$name','$id','$sex','D',0,0,0,0,0,0,0,0,0,0) ";
		
		$isok=$conn->exec($sql);
		if ($isok==0){
			echo "新增醫師失敗：$sql<br>";
		}
		$sql="insert into leconfig.zhi_staff(no,name,identity,gender,position,drkind,is_ospro,is_endopro,is_peripro,
							is_pedopro,is_odpro,isowner,created_at,created_by)
				values('$no','$name','$id','$sex','D',0,0,0,0,0,0,0,now(),0) ";
		$isok=$conn->exec($sql);
		if ($isok==0){
			echo "新增zhi醫師失敗：$sql<br>";
		}
	}	

	echo "醫師新增完畢";
?>





