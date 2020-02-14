<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table prescription");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select treatment_drug_id,nhi_extend_disposal_id,a73 as nhicode,a74 as part,a75 as fq,
				 a76 as days,a77 as totalq,b.quantity,d.nhi_code
			from nhi_extend_treatment_drug a,treatment_drug b ,drug d
		   where a.treatment_drug_id=b.id
			 and b.drug_id=d.id
		   order by b.id";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    foreach ($line as $key => $value) {
	    	switch ($key) {
	    		case 'nhi_extend_disposal_id':
	    			$ic_sign=$value;
	    			break;
	    		case 'nhi_code'://這個是treatment_drug的健保碼，當上傳檔的健保碼是空白，可能表示沒有申報不確定
	    			$drugno=$value;
	    			break;
	    		case 'nhicode'://上傳的健保碼，空白可能是沒申報
	    			$nhidrugno=$value;
	    			break;
	    		case 'part':
	    			$part=$value;
	    			break;
	    		case 'fq':
	    			$times=$value;
	    			break;
	    		case 'quantity':
	    			$q=$value;
	    			$dose=substr('0'.$value,-2);
	    			break;
	    		case 'days':
	    			$days=$value;
	    			break;
	    		case 'totalq':
	    			$totalq=$value;
	    			break;
	    	}
	    }
	    switch ($times) {
	    	case 'TID':
	    		$dayperQ=3*$q;
	    		break;
	    	case 'QID':
	    		$dayperQ=4*$q;
	    		break;
	    	default:
	    		$dayperQ=$q; 
	    }
	    
	    $sql="insert into prescription(regsn,drugno,nhidrugno,qty,totalq,dose,part,times,uprice,amount,day,ic_sign) values
	    		(0,'$drugno','$nhidrugno',$dayperQ,$totalq,'$dose','$part','$times',0,0,$days,$ic_sign)";
	    echo "$ic_sign-$nhi_code".$sql."<br>";
	    $conn->exec($sql);
	    $sql="update registration r, prescription t 
				 set t.ddate=r.ddate,t.seqno=r.seqno,t.regsn=r.regsn
			   WHERE r.assistno=t.ic_sign";
		$conn->exec($sql);

		$sql="update registration r,prescription p set rx_type=1,rx_day=day WHERE r.assistno=p.ic_sign";
		$conn->exec($sql);

	}
	

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>處方箋 資料轉換完成</h1>";

?>