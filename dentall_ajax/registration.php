<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table registration");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select cast(date(a.arrival_time) as text) as arrivedt, 
				A.*,disposal_id,patient_id,a11 as cardid,a14 as clinic_id,a15 as drid,a18 as ic_seqno,a23 as ic_type,
				a31 as amount,a32 as partpay,examination_code as trcode,examination_point as trpay,
				patient_identity as nhi_status,category,user_id,a17,b.id as nhidisposal_id,a54,serial_number
			from (select arrival_time, EXTRACT(hour from arrival_time)+8 as hh,
				 EXTRACT(Minute from arrival_time) as mm,r.id as rid, abnormal_code, d.id as did,chief_complaint 
					from registration r left join disposal d 
					on r.id=d.registration_id
					)A left join (select e.*,u.user_id from  nhi_extend_disposal e left join extend_user u on a15=national_id)B
			  		on a.did=b.disposal_id
				order by rid";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	$r=0;
	$seq=0;
	$DT='';
	while ($rs = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		$regsn=$rs['rid'];
		var_dump("a17=".$rs['a17']);
		if ($rs['a17']!=''){
			$ROCDT=$rs['a17'];
			$RocYY=substr($ROCDT,0,3);
			$yy=substr($ROCDT,0,3)+1911;
			$mm=substr($ROCDT,3,2);
			$dd=substr($ROCDT,5,2);
			$date=$yy.'-'.$mm.'-'.$dd;
		}else{
			$date=$rs['arrivedt'];
		}
		$reg_time=$rs['hh'].":".$rs['mm'];
		$cc=$rs['chief_complaint'];
		$cc=str_replace("\\", "＼", $cc);
		$cc=str_replace("'", "\'", $cc);
		$assistno=($rs['nhidisposal_id']==null)?0:$rs['nhidisposal_id'];
		$Gamt=($rs['disposal_id']==null)?0:$rs['disposal_id'];
		$cussn=($rs['patient_id']==null)?0:$rs['patient_id'];
		$card_id=$rs['cardid'];
		$drno1=($rs['user_id']==null)?0:$rs['user_id'];
		$ic_seqno=$RocYY.$rs['ic_seqno'];
		$ic_type=$rs['ic_type'];
		$amount=($rs['amount']==null)?0:$rs['amount'];
		$partpay=($rs['partpay']==null)?0:$rs['partpay'];
		$trcode=$rs['trcode'];
		$trpay=($rs['trpay']==null)?0:$rs['trpay'];
		$nhi_status=$rs['nhi_status'];
		$a54=$rs['a54'];  //實際看診日
		if ($a54!=''){
			$yy=substr($a54,0,3)+1911;
			$mm=substr($a54,3,2);
			$dd=substr($a54,5,2);
			$return_ic=$yy.'-'.$mm.'-'.$dd;
		}else{
			$return_ic='';
		}
		$uploadno=$rs['serial_number'];
		$category=$rs['category'];
		if ($category==''){
			if ($ic_type=='02' || $ic_type=='AB'){
				if ($amount>300){
					$category='19';
				}else{
					$category='11';
				}
			}elseif($ic_type=='AC'){
				if ($rs['ic_seqno']=='IC07'){
					$category='B7';
				}else{
					$category='A3';
				}
			}else{
				$category='';
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
	    		category,Gamt,ic_datetime,return_ic,uploadno,rx_type,reg_time,case_history,icuploadd) values
	    		('$date','$seqno','$cc',$assistno,$cussn,'$card_id',$drno1,$drno1,'$ic_seqno','$ic_type',$amount,$partpay,'$trcode',
	    		$trpay,'$nhi_status','$category',$Gamt,'$ic_datetime','$return_ic','$uploadno','2','$reg_time','4','$date')";
	    echo $r."--".$ddate.$cussn.'--'.$sql."<br>";
	    $conn->exec($sql);
	}
	$conn->exec("update registration r,customer c set r.cusno=c.cusno where r.cussn=c.cussn");

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>掛號 資料轉換完成</h1>";

?>