<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("delete from registration where seqno='000'");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select status,expected_arrival_time, 
				 cast(date(expected_arrival_time) as text)appDT,EXTRACT(hour from expected_arrival_time)+8 as hh,
				 EXTRACT(Minute from expected_arrival_time) as mm,
				 note,required_treatment_time,patient_id,doctor_user_id 
			from appointment
		   where registration_id is null
			 and status='CONFIRMED'
			 and expected_arrival_time >to_date('2020-02-01','yyyy-MM-dd') 
		   order by 2";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {

		var_dump($line);
		$ddate=$line['appdt'];
		$schtime=$line['hh'].':'.substr('0'.$line['mm'],0,2);
		$note=$line['note'];
		$schlen=$line['required_treatment_time'];
		$cussn=$line['patient_id'];
		$drno1=$line['doctor_user_id'];
		
	    $sql="insert into registration (ddate,seqno,cussn,sch_note,sch_time,schlen,drno1,drno2) values
	    				('$ddate','000',$cussn,'$note','$schtime',$schlen,$drno1,$drno1)";
	    echo "$ddate-$schtime".$sql."<br>";
	    $conn->exec($sql);
	}
	$conn->exec("update registration r,customer c set r.cusno=c.cusno,r.schtel=c.custel,r.schmobile=c.cusmob WHERE seqno='000' and r.cussn=c.cussn");

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>預約 資料轉換完成</h1>";

?>