<?php
	include_once "../include/db.php";

	$conn=MariaDBConnect();
	$conn->exec("truncate table drug");
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$pgconn=postgreConnect();
	$sql="select * from drug order by id";
	$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	    foreach ($line as $key => $value) {
	    	switch ($key) {
	    		case 'nhi_code':
	    			$nhicode=$value;
	    			break;
	    		case 'name':
	    			$name=$value;
	    			break;
	    		case 'quantity':
	    			$dose=substr('0'.$value,-2);
	    			break;	
	    		case 'frequency':
	    			$times=$value;
	    			break;
	    		case 'way':
	    			if($value==''){
	    				$part='PO';
	    			}else{
		    			$part=$value;
	    			}
	    			break;	
	    	}
	    }
	    $sql="insert into drug(drugno,name,nhicode,nhifee,fee,dose,part,times)values
	    		('$nhicode','$name','$nhicode',0,0,'$dose','$part','$times')";
	    echo $nhicode.'-'.$name."<br>";
	    $conn->exec($sql);
	}
	

	// 释放结果集
	pg_free_result($result);

	// 关闭连接
	pg_close($pgconn);
	
	echo "<h1>藥品 資料轉換完成</h1>";

?>