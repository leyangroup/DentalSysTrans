<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table registration");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select A.*,disposal_id,patient_id,a11 as cardid,a14 as clinic_id,a15 as drid,a18 as ic_seqno,a23 as ic_type,a31 as amount,a32 as partpay,
				examination_code as trcode,examination_point as trpay,patient_identity as nhi_status,category,user_id,a17,b.id as nhidisposal_id,a54,
				serial_number
			from (select  r.id as rid, abnormal_code, d.id as did,chief_complaint from registration r left join disposal d on r.id=d.registration_id)A 
					left join (select e.*,u.user_id from  nhi_extend_disposal e left join extend_user u on a15=national_id)b
			  on a.did=b.disposal_id
			order by rid";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	$r=0;
	$seq=0;
	$DT='';
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    foreach ($line as $key => $value) {
	    	switch ($key) {
	    		case 'rid':
	    			$regsn=$value;
	    			break;
	    		case 'a17':
	    			$ic_datetime=$value;
	    			$RocYY=substr($value,0,3);
	    			$yy=substr($value,0,3)+1911;
	    			$mm=substr($value,3,2);
	    			$dd=substr($value,5,2);
	    			$date=$yy.'-'.$mm.'-'.$dd;
	    			break;
	    		case 'chief_complaint':
	    			$cc=$value;
					$cc=str_replace("\\", "＼", $cc);
					$cc=str_replace("'", "\'", $cc);
	    			break;
	    		case 'nhidisposal_id':  // 暫存nhi_extend_disposal_id至assistno 串接 明細與醫令的資料
	    			$assistno=$value;
	    			break;
	    		case 'disposal_id':  // 暫存disposal_id 至 GAMT 串接 treatment_procedure
	    			$Gamt=$value;
	    			break;	
	    		case 'patient_id':
	    			$cussn=$value;
	    			if ($value==null)$cussn=0;
	    			break;
	    		case 'cardid':
	    			$card_id=$value;
	    			break;
	    		case 'user_id':
	    			$drno1=$value;
	    			$drno2=$value;
	    			break;
	    		case 'ic_seqno':
	    			$ic_seqno=$RocYY.$value;
	    			break;
	    		case 'ic_type':
	    			$ic_type=$value;
	    			break;
	    		case 'amount':
	    			$amount=$value;
	    			break;
	    		case 'partpay':
	    			$partpay=$value;
	    			break;
	    		case 'trcode':
	    			$trcode=$value;
	    			break;
	    		case 'trpay':
	    			$trpay=$value;
	    			break;
	    		case 'nhi_status':
	    			$nhi_status=$value;
	    			break;
	    		case 'category':
	 				$category=$value;
	 				break;
	 			case 'a54'://當有補卡時，a54有值，這才是實際看診日期，所以要取代取卡日，目前是用取卡日來存，因為日期讀不出來@@
	 				if ($value!=''){
	 					$yy=substr($value,0,3)+1911;
		    			$mm=substr($value,3,2);
		    			$dd=substr($value,5,2);
		    			$return_ic=$yy.'-'.$mm.'-'.$dd;
	 				}else{
	 					$return_ic='';
	 				}

	 				break;
	 			case 'serial_number':
	 				$uploadno=$value;
	 				break;
	    	}
	    }
	    $r++;
	    if ($return_ic!=''){
	    	$date=$return_ic;
	    }
	    if($DT!=$date){
	    	$seq=1;
	    	$DT=$date;
	    }else{
	    	$seq++;
	    }
	    $seqno=substr('000'.$seq,-3);
	    $sql="insert into registration 
	    		(ddate,seqno,cc,assistno,cussn,card_id,drno1,drno2,ic_seqno,ic_type,amount,nhi_partpay,trcode,trpay,nhi_status,
	    		category,Gamt,ic_datetime,return_ic,uploadno,rx_type) values
	    		('$date','$seqno','$cc',$assistno,$cussn,'$card_id',$drno1,$drno2,'$ic_seqno','$ic_type',$amount,$partpay,'$trcode',
	    		$trpay,'$nhi_status','$category',$Gamt,'$ic_datetime','$return_ic','$uploadno','2')";
	    echo $r."--".$ddate.$cussn.'--'.$sql."<br>";
	    $conn->exec($sql);

	}

	$sql="update registration r,customer c 
			 set r.cusno=c.cusno
		   where r.cussn=c.cussn";
	$conn->exec($sql);
	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>掛號 資料轉換完成</h1>";

?>