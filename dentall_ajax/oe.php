<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("delete from registration where seqno='000'");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select (select name from patient where id=a.patient_id)ptname,project_code,jhi_type,display_name,
				(select first_name from jhi_user where id= cast(a.doctor as int)) drname,amount,charge,arrears,note,created_date
			from ledger a 
			order by patient_id,project_code";

	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	echo "<h1>自費明細</h1>";
	echo "<table border='1'>";
	echo "<tr>";
		echo "<td>患者</td>";
		echo "<td>單號</td>";
		echo "<td>類別</td>";
		echo "<td>事項</td>";
		echo "<td>醫師</td>";
		echo "<td>應收</td>";
		echo "<td>本次收費</td>";
		echo "<td>備註</td>";		
		echo "<td>建立日</td>";
	echo "</tr>";
	while ($rs = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		echo "<tr>";
		echo "<td>".$rs['ptname']."</td>";
		echo "<td>".$rs['project_code']."</td>";
		echo "<td>".$rs['jhi_type']."</td>";
		echo "<td>".$rs['display_name']."</td>";
		echo "<td>".$rs['drname']."</td>";
		echo "<td>".$rs['amount']."</td>";
		echo "<td>".$rs['charge']."</td>";
		echo "<td>".$rs['note']."</td>";		
		echo "<td>".$rs['created_date']."</td>";
		echo "</tr>";
	}
	echo "<table>";

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	

?>