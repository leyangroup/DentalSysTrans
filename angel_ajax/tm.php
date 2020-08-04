<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
	
	//支付標準表 院內碼
	$conn->exec("truncate table treatment");

	$sql = "SELECT * FROM dencodek.dbf";
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$trcode=trim($value['pcode']);
		$treatname=trim(mb_convert_encoding($value['pname'],"UTF-8","BIG5"));
		$icd10=trim($value['illcode']);
		$treatno=trim($value['tsub']);
		$nhicode=trim($value['tcode']);
		$fee=$value['tfee_new'];
		$memo=trim(mb_convert_encoding($value['descs'],"UTF-8","BIG5"));
		$memo=str_replace("\\", "＼", $memo);
		$memo=str_replace("'", "\'", $memo);

		$sql="insert into treatment(trcode,nhicode,treatname,nhi_fee,treatno,padd_percent,radd_percent,memo,icd10cm,category) 
				values('$trcode','$nhicode','$treatname',$fee,'$treatno',1,1.3,'$memo','$icd10','$totact')";
		$r++;
		$conn->exec($sql);
		echo "$r.$sql<br>";
	}

	//處理計價單位 轉診加成註記
	$conn->exec("update nhicode set tr_ospath=1 where nhicode in ('92049B','92065B','92073C','92090C','92091C','92095C')");
	$sql="update treatment set fee_unit=0";  //先將計價單位改成計顆
	$conn->exec($sql);
	
	 $sql="update treatment t,nhicode n 
			set t.nhi_fee=n.fee,t.category=n.category,fee_unit=feeunit,
			    t.tr_od=n.tr_od,t.tr_endo=n.tr_endo,t.tr_peri=n.tr_peri,
			    t.tr_os=n.tr_os,t.tr_ospath=n.tr_ospath,t.tr_pedo=n.tr_pedo
	 where t.nhicode=n.nhicode";

	$conn->exec($sql);

	//處理 科別
	$conn->exec("update treatment set is_oper=0,is_endo=0,is_oral=0,is_peri=0,is_xray=0,is_pedo=0;");
	$conn->exec("update treatment set is_oper=1 where nhicode like '89%' and nhicode!='89' ");
	$conn->exec("update treatment set is_endo=1 where nhicode like '90%' ");
	$conn->exec("update treatment set is_oral=1 where nhicode like '92%' ");
	$conn->exec("update treatment set is_peri=1 where nhicode like '91%' ");
	$conn->exec("update treatment set is_xray=1 where nhicode like '34%' ");
	$conn->exec("update treatment set is_pedo=1 where nhicode between '81' and '92229B' ");
	$conn->exec("update treatment set is_pedo=0 where nhicode in ('89006C','89007C','89013C','89113C','90001C','90002C','90003C','90019C','90020C','90017C','92096C','91021C','91022C','91023C') ");

	$conn->exec("truncate table sundryitem");
	$sql = "SELECT * FROM inpart.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$no=$value['inpart'];
		$memo=trim(mb_convert_encoding($value['descs'],"UTF-8","BIG5"));
		$memo=str_replace("\\", "＼", $memo);
		$memo=str_replace("'", "\'", $memo);
		$sql="insert into sundryitem (no,name,invdate) values('$no','$memo','') ";
		echo $sql;
		$conn->exec($sql);
	}
	//補上處置名稱
	$sql="update treatment m,treat_record t
			 set t.treatname=m.treatname
		   where t.trcode=m.trcode";
	$conn->exec($sql);

	//填上傷病

	$sql="update nhicode n,treatment m 
			set m.sickno=n.icd9 
			where n.nhicode=m.nhicode
			and m.sickno is null";
	$conn->exec($sql);

	$sql="update treat_record t,treatment m 
			 set t.sickno=m.sickno
		   where t.trcode=m.trcode
		     and (t.sickno is null or t.sickno='')";
	$conn->exec($sql);

	$sql="update treat_record t,treatment m 
			 set t.icd10=m.icd10cm
		   where t.trcode=m.trcode
		     and (t.icd10 is null or t.icd10='')";
	$conn->exec($sql);

	$sql="update treat_record t,treatment m 
			set t.punitfee=m.nhi_fee,pamt=m.nhi_fee*nums
			where t.trcode=m.trcode
			
			and t.ddate >='2017-01-01'";
	$conn->exec($sql);

	$sql="delete from treatment where trcode like '0127%' ";
	$conn->exec($sql);

	$sql="update registration r
		set nhi_tamt=(select sum(pamt) from treat_record where regsn=r.regsn and deldate is null)
		where nhi_tamt !=(select sum(pamt) from treat_record where regsn=r.regsn and deldate is null)";
	$conn->exec($sql);

	$sqi="update registration set amount=trpay+nhi_tamt+nhi_damt,giaamt=trpay+nhi_tamt+nhi_damt-nhi_partpay";
	$conn->exec($sql);

	echo "處置 資料轉換完成";

?>