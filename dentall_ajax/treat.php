<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table treat_record");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	// $sql="select a.id as nhi_disposal_id,a.disposal_id,a73 as trcode,a74 as fdi,a75 as side,quantity,p.total,nhi_description,icd10,icd9
	// 		from nhi_extend_disposal as a,
	// 			nhi_extend_treatment_procedure as t,
	// 			(select * from treatment_procedure m 
 //       				left join (select i.id as icd10id, i.code as icd10,j.code as icd9 from nhi_icd_10_cm i , nhi_icd_9_cm j  where  nhi_icd9cm_id=j.id
	// 			              ) n on cast(m.nhi_icd_10_cm as int) = n.icd10id 
	// 			 )  as p  
	// 		where a.id=t.nhi_extend_disposal_id
	// 		  and t.treatment_procedure_id=p.id
	// 		order by 1";

	// $sql="select * 
	//         from (
	//         		select r.id as rid,p.disposal_id, p.id as treatment_procedure_id,p.quantity,p.total,p.nhi_description,p.nhi_procedure_id,
	// 						(select code from nhi_icd_10_cm where id=cast(p.nhi_icd_10_cm as int)) as icd10,
	// 						(select code from nhi_icd_9_cm where id=n.nhi_icd9cm_id) icd9,n.code,n.name,n.point,o.position

	// 						from registration r,disposal d,treatment_procedure p,nhi_procedure n,tooth o
	// 						where r.id=d.registration_id
	// 						and d.id=p.disposal_id
	// 						and p.nhi_procedure_id=n.id
	// 						and p.id=o.treatment_procedure_id
	// 			) aa left join nhi_extend_treatment_procedure bb 
	// 			on aa.treatment_procedure_id=bb.treatment_procedure_id
	// 			order by rid";
	$sql="select a.quantity,a.total,a.nhi_description,a.disposal_id,doctor_user_id,(select code from nhi_icd_10_cm where id=cast(a.nhi_icd_10_cm as int)) as icd10,a71,a72,a73 as trcode,a74 as fdi,a75,a76,a77,a78,nhi_extend_disposal_id,c.*
from treatment_procedure a , nhi_extend_treatment_procedure b,nhi_extend_disposal c
where a.id=b.treatment_procedure_id
and a.disposal_id=c.disposal_id
order by a71 desc";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($rs = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    $disposal_id=$rs['disposal_id'];
	    $trcode=$rs['trcode'];
	    //$name=$rs['name'];
	    //$point=$rs['point'];
	    $fdi=$rs['fdi'];
	    $side=$rs['a75'];
	    $nums=$rs['quantity'];
	    $pamt=$rs['total'];
	    $tx=$rs['nhi_description'];
	    $tx=str_replace("\\", "＼", $tx);
		$tx=str_replace("'", "\'", $tx);
		$icd10=$rs['icd10'];		
		//$icd9=$rs['icd9'];
		$fdi2=$rs['fdi'];  //牙位會超過，先存這裡

	    // $sql="insert into treat_record (regsn,fdi,trcode,treatname,side,nums,add_percent,punitfee,pamt,treat_memo,sickno,icd10,drno) value
	    // 		(0,'$fdi','$trcode','$name','$side',$nums,1,$point,$pamt,'$tx','$icd9','$icd10',$disposal_id)";
	    $sql="insert into treat_record (regsn,fdi,trcode,side,nums,add_percent,pamt,treat_memo,sickno,icd10,drno,cc) value
	    		(0,'$fdi','$trcode','$side',$nums,1,$pamt,'$tx','$icd9','$icd10',$disposal_id,'$fdi')";

	    echo "$disposal_id-$fdi-$trcode<br>";
	    $conn->exec($sql);
	}
	//掛號與處置串接
	$conn->exec("update registration r, treat_record t set t.cussn=r.cussn,t.ddate=r.ddate,t.seqno=r.seqno,t.regsn=r.regsn WHERE r.gamt=t.drno");

	//填上療程卡號
	$conn->exec("update registration r,treat_record t set t.start_icseq=substr(ic_seqno,-4)where ic_type='AB' and r.regsn=t.regsn");

	//填上療程開始日
	$conn->exec("update registration r,treat_record t set t.start_date=r.ddate where ic_type='02' and r.cussn=t.cussn and substr(r.ic_seqno,-4)=t.start_icseq");

	//處理01271
	$conn->exec("update registration r,treat_record t set r.check_finding=t.treat_memo,t.deldate='1911-01-01' where r.regsn=t.regsn and t.trcode in ('01271C','01272C','01273C')");

	$conn->exec("update registration r,prescription p set r.trcode='00121C' where r.regsn=p.regsn and r.trcode='00122C'");
	$conn->exec("update registration r,prescription p set r.trcode='00129C' where r.regsn=p.regsn and r.trcode='00130C'");

	//處理數量
	$conn->exec("update treat_record t,treatment m set t.nums=round(length(fdi)/2) where t.trcode=m.trcode and m.fee_unit=0 and nums!=round(length(fdi)/2)");

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>處置 資料轉換完成</h1>";

?>