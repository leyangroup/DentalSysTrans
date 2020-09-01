<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//$conn->exec("truncate table treat_record");
	
	//處置明細資料
	$sql = "SELECT * FROM history.dat";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		//用treat_record.icuploadd的欄位來存keyword
		$keyword=addslashes($value['keywords']);
		$no=$value['drug_no'];
		$qty=$value['qty'];
		$price=$value['price'];
		$pamt=$qty*$price;
		$sql="insert into treat_record(regsn,trcode,nums,punitfee,pamt,add_percent,icuploadd) values(0,'$no',$qty,$price,$pamt,1,'$keyword')";
		echo "$no ；";
		$conn->exec($sql);
	}
	echo "轉換完畢";
	echo "要手動下關聯語法";

	echo "整理中…";

	$conn->exec("update registration r, treat_record t
					set t.regsn=r.regsn,t.cussn=r.cussn,t.ddate=r.ddate,t.seqno=r.seqno
				 where binary r.stdate=t.icuploadd and r.cussn!=0");

	$conn->exec("update treat_record t,treatment m set t.treatname=m.treatname where t.trcode=m.trcode");
	echo "<h1>處置明細 轉換完成</h1>";

?>