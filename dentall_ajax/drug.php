<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table drug");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select * from drug order by id";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    foreach ($line as $key => $value) {
	    	switch ($key) {
	    		case 'nhi_code':
	    			$nhicode=$value;
	    			break;
	    		case 'name':
	    			$name=$value;
	    			break;
	    		case 'quantity':
	    			$dose=substr('0'.$value,-2);
	    			break;	
	    		case 'frequency':
	    			$times=$value;
	    			break;
	    		case 'way':
	    			if($value==''){
	    				$part='PO';
	    			}else{
		    			$part=$value;
	    			}
	    			break;	
	    	}
	    }
	    $sql="insert into drug(drugno,name,nhicode,nhifee,fee,dose,part,times)values
	    		('$nhicode','$name','$nhicode',0,0,'$dose','$part','$times')";
	    echo $nhicode.'-'.$name."<br>";
	    $conn->exec($sql);
	}
	echo "處理過敏藥物與病史<br>";
	$conn->exec('truncate table allergic');
	$conn->exec('truncate table systemdisease');	
	$conn->exec('truncate table paallergic');
	$conn->exec('truncate table pasystemdi');	
	$conn->exec('truncate table sickmap');


	$sql="select * from tag ";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($rs = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		$id=$rs['id'];
		$type=$rs['jhi_type'];
		$name=$rs['name'];
		if($type=='ALLERGY'){
			$conn->exec("insert into allergic(asn,ano,aname)values($id,'$id','$name')");
		}else{
			$conn->exec("insert into systemdisease(asn,ano,aname)values($id,'$id','$name')");
		}
	}

	$sql="select jhi_type,tags_id,patients_id from tag t,patient_tag p where t.id=p.tags_id";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($rs = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		$type=$rs['jhi_type'];
		$tag=$rs['tags_id'];
		$pt=$rs['patients_id'];
		if ($type=='ALLERGY'){
			$conn->exec("insert into paallergic(cussn,asn)values($pt,$tag)");
		}else{
			$conn->exec("insert into pasystemdi(cussn,sdsn)values($pt,$tag)");
		}
	}
	echo "處理ICD10<br>";

	$sql="select a.code icd10,b.code icd9 from nhi_icd_10_cm a ,nhi_icd_9_cm b where a.nhi_icd9cm_id=b.id";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($rs = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		$icd9=$rs['icd9'];
		$icd10=$rs['icd10'];
		$conn->exec("insert into sickmap(icd9,icd10)values('$icd9','$icd10')");
	}

	$conn->exec("update treat_record t, sickmap s set t.sickno=s.icd9 WHERE t.icd10=s.icd10");


	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>藥品 資料轉換完成</h1>";

?>