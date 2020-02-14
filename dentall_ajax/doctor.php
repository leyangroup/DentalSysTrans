<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table staff");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select id,login,first_name,national_id from jhi_user a,extend_user b where a.id=b.user_id order by id";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		$sfsn=$line['id'];
		$sfno=$line['login'];
		$sfname=$line['first_name'];
		$sfid=$line['national_id'];
	    $sql="insert into staff(sfsn,sfno,sfname,sfid) value($sfsn,'$sfno','$sfname','$sfid')";
	    echo $sfno.$sfname.$sfid."<br>";
	    $conn->exec($sql);
	}
	$sql="update staff set position='D' where sfname like 'A%' ";
	$conn->exec($sql);

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>醫師 資料轉換完成</h1>";

?>