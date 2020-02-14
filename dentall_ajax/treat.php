<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table treat_record");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select a.id as nhi_disposal_id,a.disposal_id,a73 as trcode,a74 as fdi,a75 as side,quantity,p.total,nhi_description,icd10,icd9
from nhi_extend_disposal as a,nhi_extend_treatment_procedure as t,
(select * from treatment_procedure m 
       left join (select i.id as icd10id, i.code as icd10,j.code as icd9 from nhi_icd_10_cm i , nhi_icd_9_cm j  where  nhi_icd9cm_id=j.id
				 ) n 
	  on cast(m.nhi_icd_10_cm as int) = n.icd10id )  as p  
where a.id=t.nhi_extend_disposal_id
and t.treatment_procedure_id=p.id
order by 1";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    foreach ($line as $key => $value) {
	    	switch ($key) {
	    		case 'nhi_disposal_id':
	    			$nhi_disposal_id=$value;
	    			break;
	    		case 'trcode':
	    			$trcode=$value;
	    			break;
	    		case 'fdi':
	    			$fdi=$value;
	    			break;
	    		case 'side':
	    			$side=$value;
	    			break;
	    		case 'quantity':
	    			$nums=$value;
	    			break;
	    		case 'total':
	    			$pamt=$value;
	    			break;
	    		case 'nhi_description':
	    			$tx=$value;
					$tx=str_replace("\\", "＼", $tx);
					$tx=str_replace("'", "\'", $tx);
	    			break;
	    		case 'icd10':
	    			$icd10=$value;
	    			break;
	    		case 'icd9':
	    			$icd9=$value;
	    			break;
	    	}
	    }
	    $sql="insert into treat_record (regsn,fdi,trcode,side,nums,add_percent,pamt,treat_memo,sickno,icd10,drno) value
	    		(0,'$fdi','$trcode','$side',$nums,1,$pamt,'$tx','$icd9','$icd10',$nhi_disposal_id)";
	    echo "$nhi_disposal_id-$fdi-$trcode".$sql."<br>";
	    $conn->exec($sql);
	}
	
	$sql="update registration r, treat_record t 
			 set t.cussn=r.cussn,t.ddate=r.ddate,t.seqno=r.seqno,t.regsn=r.regsn
		   WHERE r.assistno=t.drno";
	$conn->exec($sql);


	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>處置 資料轉換完成</h1>";

?>