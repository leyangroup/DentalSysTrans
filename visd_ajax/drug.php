<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
	$path=$_GET['path'];

	$dbDrug=dbase_open($path.'/CO09D.dbf',2);
	if (dbase_pack($dbDrug)){
		echo "pack ok ";
	}else{
		echo "pack error ";
	}
	if ($dbDrug){
		$conn->exec('truncate table drug');
		$record_numbers = dbase_numrecords($dbDrug);
		for ($i = 1; $i <= $record_numbers; $i++) {
			$row = dbase_get_record_with_names($dbDrug, $i);
			if (trim($row['DTYPE'])=='02'){
				if (strlen(trim($row['DNO']))==10){
					$drugno=trim(mb_convert_encoding($row['KDRUG'], "UTF-8", "BIG5"));
					$nhicode=$row['DNO'];
					$drugname=trim(mb_convert_encoding($row['DDESC'], "UTF-8", "BIG5"));
					$times=$row['DFREQ'];
					$sql="insert into drug (drugno,name,nhicode,dose,part,times)values
						('$drugno','$drugname','$nhicode','01','PO','$times')";
					echo $sql."<br>";
					$conn->exec($sql);
				}
			}
		}
	}

	// 填入藥品健保碼
	$sql="update prescription p,drug d set p.nhidrugno=d.nhicode where p.drugno=d.drugno";
	$conn->exec($sql);

	$sql="update prescription p,drug_dose d set p.dose=d.doseno where p.dose=d.qty";
	$conn->exec($sql);

	//計算藥品數量問題
	$sql="update prescription set qty=4,totalQ=4*day,amount=4*day*uprice where totalq!=4*day and times='QID'";
	$conn->exec($sql);

	//處理處置的中文, 與傷病
	$sql="update treat_record t,treatment m set t.treatname=m.treatname,t.sickno=m.sickno,t.icd10=m.icd10cm where t.trcode=m.trcode";
	$conn->exec($sql);

	//處理01271的問題 將01271的做刪除記錄
	$sql="update registration r, treat_record t 
			 set r.trcode=concat(t.trcode,'C')
			where r.regsn=t.regsn
			  and t.trcode like '0127%'";
	$conn->exec($sql);

	$sql="update treat_record set deldate='1911-01-01' where trcode like '0127%'";
	$conn->exec($sql);

	//處理療程開始日與卡號
	$sql="update registration r,treat_record t 
			 set t.start_date=r.uploadno,t.start_icseq=substr(r.ic_seqno,-4)
		   where r.regsn=t.regsn
		     and r.ic_type='AB'";
	$conn->exec($sql);

	$sql="update registration set ic_seqno=substr(ic_seqno,0,3) where ic_type='AB'";
	$conn->exec($sql);

	$conn->exec('truncate table drugcom');
	$conn->exec('truncate table drugcomdetails');

	echo "<br>藥品相關資料轉換完成!!";

?>