<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table prescription");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	// $sql="select treatment_drug_id,nhi_extend_disposal_id,a73 as nhicode,a74 as part,a75 as fq,
	// 			 a76 as days,a77 as totalq,b.quantity,d.nhi_code
	// 		from nhi_extend_treatment_drug a,treatment_drug b ,drug d
	// 	   where a.treatment_drug_id=b.id
	// 		 and b.drug_id=d.id
	// 	   order by b.id"
	$sql="select a.day as days,a.frequency as fq,a.way as part,a.quantity,nhi_code,d.id
			from disposal d,prescription p ,treatment_drug a,drug 
			where d.prescription_id=p.id
			and a.prescription_id=p.id
			and drug_id=drug.id";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		$ic_sign=$line['id'];
		$drugno=$line['nhi_code'];
		$nhidrugno=$line['nhi_code'];
		$part=$line['part'];
		$times=$line['fq'];
		$q=
		$dose=substr('0'.$line['quantity'],-2);
		$days=$line['days'];
	    switch ($times) {
	    	case 'TID':
	    		$dayperQ=3*$line['quantity'];
	    		break;
	    	case 'QID':
	    		$dayperQ=4*$line['quantity'];
	    		break;
	    	default:
	    		$dayperQ=$line['quantity']; 
	    }
	    $totalq=round($dayperQ*$days,1);
	    
	    $sql="insert into prescription(regsn,drugno,nhidrugno,qty,totalq,dose,part,times,uprice,amount,day,ic_sign) values
	    		(0,'$drugno','$nhidrugno',$dayperQ,$totalq,'$dose','$part','$times',0,0,$days,$ic_sign)";
	    echo "$ic_sign-$nhi_code".$sql."<br>";
	    $conn->exec($sql);
	    $sql="update registration r, prescription t 
				 set t.ddate=r.ddate,t.seqno=r.seqno,t.regsn=r.regsn
			   WHERE r.gamt=t.ic_sign";
		$conn->exec($sql);

		$sql="update registration r,prescription p set rx_type=1,rx_day=day WHERE r.gamt=p.ic_sign";
		$conn->exec($sql);


	}
	

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>處方箋 資料轉換完成</h1>";

?>