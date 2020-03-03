<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table customer");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select * from patient where delete_date is null order by id";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    foreach ($line as $key => $value) {
	    	switch ($key) {
	    		case 'id':
	    			$cussn=$value;
	    			break;
	    		case 'medical_id':
	    			$cusno=$value;
	    			break;	
	    		case 'name':
	    			$cusname=$value;
	    			break;
	    		case 'phone':
	    			$cusmob=$value;
	    			break;	
	    		case 'gender':
	    			if ($value=='MALE'){
	    				$cussex=1;
	    			}else{
	    				$cussex=0;
	    			}
	    			break;
	    		case 'birth':
	    			$cusbirthday=$value;
	    			break;
	    		case 'national_id':
	    			$cusid=$value;
	    			break;
	    		case 'address':
	    			$cusaddr=$value;
	    			break;
	    		case 'blood':
	    			$cusblood=($value=='UNKNOWN'?'':$value);
	    			break;
	    		case 'note':
	    			$memo=trim(mb_convert_encoding($value,"UTF-8","BIG5"));
					$memo=str_replace("\\", "＼", $memo);
					$memo=str_replace("'", "\'", $memo);
	    			break;
	    		case 'last_doctor_user_id':
	    			$lastdrno=$value;
	    			$lastdrno=($lastdrno==''?0:$lastdrno);
	    			break;
	    		case 'first_doctor_user_id':
	    			$maindrno=$value;
	    			$maindrno=($maindrno==''?0:$maindrno);
	    			break;
	    		case 'main_notice_channel':
	    			if ($value=='Phone'){
	    				$notifyuse=4;
	    			}elseif($value=='Line'){
	    				$notifyuse=1;
	    			}else{
	    				$notifyuse=0;
	    			}
	    			break;
	    	}
	    }
	    $sql="insert into customer(cussn,cusno,cusname,cusmob,custel,cussex,cusbirthday,cusid,cusaddr,cusblood,cusmemo,maindrno,lastdrno,notifyuse) value
	    ($cussn,'$cusno','$cusname','$cusmob','$cusmob',$cussex,'$cusbirthday','$cusid','$cusaddr','$cusblood','$cusmemo',$maindrno,$lastdrno,$notifyuse)";
	    echo $cusno.$cusname."<br>";
	    $conn->exec($sql);
	}

	//處理地址
	echo "處理地址<br>";
	$sql="update customer c,zip z set cuszip=zip,cusaddr=replace(cusaddr,concat(z.county,z.city),'') WHERE c.cusaddr like concat(z.county,z.city,'%')";
	$conn->exec($sql);

	echo "處理初診日";
	$sql="update customer c set firstdate=(select min(ddate) from registration where cussn=c.cussn and ic_type is not null and ic_type !='') ";
	$conn->exec($sql);

	echo "處理最後診日";
	$sql="update customer c set lastdate=(select max(ddate) from registration where cussn=c.cussn and ic_type is not null and ic_type !='')";
	$conn->exec($sql);

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>掛號 資料轉換完成</h1>";

?>