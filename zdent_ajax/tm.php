<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'];

    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$path);

	$conn->exec("truncate table treatment");
    $sql = "SELECT * FROM z_tc.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$trcode=trim($value['code']);
		$name=trim(mb_convert_encoding(addslashes($value['name']),"UTF-8","BIG5"));
		$price=trim($value['price']);
		$treatno=trim($value['ppcode']);
		$icd9=trim($value['illcode']);
		$icd10=trim($value['illcode10']);
		$pcs=trim($value['pcs1']);
		$sql="insert into treatment(trcode,nhicode,treatname,nhi_fee,fee,treatno,sickno,icd10cm,icd10pcs,memo)
				values ('$trcode','$trcode','$name',$price,$price,'$treatno','$icd9','$icd10','$pcs','Tx.')";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "$sql<br>";
		}
	}

	$sql="update treatment t,nhicode n 
			set t.category=n.category,t.is_oper=n.is_oper,t.is_endo=n.is_endo,t.is_peri=n.is_peri,t.is_oral=n.is_oral,
			t.is_xray=n.is_xray,t.is_pedo=n.is_pedo,
			t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,t.tr_os=n.tr_os,t.tr_ospath=n.tr_ospath,
			t.tr_pedo=n.tr_pedo,t.fee_unit=n.feeunit,t.nhi_fee=n.fee
			where t.nhicode=n.nhicode";
	$conn->exec($sql);

	$conn->exec("update `treatment` set category='19' where category is null");
	echo "<br>處置資料轉換完畢!!";
	