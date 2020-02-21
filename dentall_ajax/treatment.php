<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table treatment");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select code,substring(name,0,40) tname,point,
				(select code from nhi_icd_10_cm where id=t.default_icd_10_cm_id)ICD10,description,
				(select code from nhi_icd_9_cm where id=t.nhi_icd9cm_id)icd9 ,specific_code
			from nhi_procedure t 
			where nhi_procedure_type_id!=200 order by code";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	$r=0;
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		$r++;
		$nhicode=$line['code'];
		$name=$line['tname'];
		$point=$line['point'];
		$icd10=$line['icd10'];
		$icd9=$line['icd9'];
		$memo=$line['description'];
					$memo=str_replace("\\", "＼", $memo);
					$memo=str_replace("'", "\'", $memo);
		$treatno=$line['specific_code'];
		
	    $sql="insert into treatment (trcode,nhicode,treatname,nhi_fee,treatno,sickno,icd10cm,padd_percent,radd_percent,memo) values
	    				('$nhicode','$nhicode','$name',$point,'$treatno','$icd9','$icd10',1,1.3,'$memo')";
	    echo "$r. $nhicode--<br>";
	    $conn->exec($sql);
	}
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

	$conn->exec("update registration set nhi_status='H10' where nhi_partpay=50");
	$conn->exec("update registration set nhi_tamt=amount-trpay,giaamt=amount-nhi_partpay");
	$conn->exec("update registration set nhi_status='009',nhi_partpay=0 where ic_type='AC'");
	$conn->exec("update registration set nhi_partpay=50,giaamt=amount-50 where nhi_status='H10' and nhi_partpay=0");
	$conn->exec("update registration set nhi_status='H10',nhi_partpay=50,giaamt=amount-50 where ic_type='02'");
	$conn->exec("update registration set nhi_status='009',nhi_partpay=0,giaamt=amount where ic_type='AB' and nhi_status='H10'");
	$conn->exec("update registration set ic_seqno=left(ic_seqno,3) where ic_type='AB'");
	$conn->exec("update treat_record t, treatment m set t.treatname=m.treatname,punitfee=nhi_fee WHERE t.trcode=m.trcode and punitfee=0");
	$conn->exec("update treat_record t set pamt=nums*add_percent*punitfee where deldate is null and pamt=0");

	//處理處置加成的
	$conn->exec("update treat_record t,customer c set add_percent=1,pamt=nums*punitfee 
				WHERE t.cussn=c.cussn and c.cusbirthday is not null and period_diff(replace(left(ddate,7),'-',''),replace(left(cusbirthday,7),'-',''))>48");

	$conn->exec("update treat_record t set pamt=round(nums*punitfee*add_percent) where deldate is null and pamt!=roundnums*punitfee*add_percent)");

	// $conn->exec("update treat_record set add_percent=round(pamt/nums/punitfee,1) where pamt!=0 and nums!=0 and punitfee!=0 and deldate is null and round(pamt/nums/punitfee,1)>=1");
	//處理合計

	$conn->exec("update registration r set nhi_tamt=(select sum(pamt) from treat_record where regsn=r.regsn and deldate is null) where ic_type is not null and ic_type!='' and nhi_tamt!=(select sum(pamt) from treat_record where regsn=r.regsn and deldate is null)  ");
	$conn->exec("update registration set amount=trpay+nhi_tamt,giaamt=trpay+nhi_tamt-nhi_partpay");
	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>預約 資料轉換完成</h1>";

?>