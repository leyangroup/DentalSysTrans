<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//清除藥品
	$conn->exec("truncate table drug");

	$sql = "SELECT * FROM drugk.dbf";
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$drugno=trim($value['drugno']);
		$piece=trim($value['piece']);
		$dname=trim(mb_convert_encoding($value['drugn'],"UTF-8","BIG5"));
		$nhicode=trim($value['tcode']);
		$price=$value['tfee'];
		$dose=($value['tdrug1']==' ')?'01':$value['tdrug1'];
		$part=trim($value['tdrug2']);
		$times=trim($value['tdrug3']);
		$sql="insert into drug(drugno,name,dosage,nhicode,nhifee,dose,part,times)
				values('$drugno','$dname','$piece','$nhicode',$price,'$dose','$part','$times')";
		$r++;
		echo "$r.$sql";
		echo "<br>";
		$conn->exec($sql);
	}
	echo "填入健保碼處理<br>";
	$sql="update prescription p,drug d 
			 set p.nhidrugno=d.nhicode
		   where p.drugno=d.drugno";

	$conn->exec($sql);

	//用藥每次量
	echo "轉入用藥每次量<br>";
	$conn->exec("truncate table drug_dose");

	$sql="select * from tabdrug1.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$no=trim($value['tdrug1']);
		$name=trim(mb_convert_encoding($value['tname1'],"UTF-8","BIG5"));
		$qty=($value['tamt1']==' ')?1:$value['tamt1'];
		$sql="insert into drug_dose(doseno,name,qty) values('$no','$name',$qty)";
		echo $sql."<br>";
		$conn->exec($sql);

	}
	
	//用藥部位
	echo "轉入用藥部位<br>";
	$conn->exec("truncate table drug_part");
	
	$sql="select * from tabdrug2.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$no=trim($value['tdrug2']);
		$name=trim(mb_convert_encoding($value['tname2'],"UTF-8","BIG5"));
		$sql="insert into drug_part(useno,name) values('$no','$name')";		
		echo $sql."<br>";

		$conn->exec($sql);
	}

	//用藥頻率	
	echo "轉入用藥頻率<br>";
	$conn->exec("truncate table drug_times");
	
	$sql="select * from tabdrug3.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$no=trim($value['tdrug3']);
		$name=trim(mb_convert_encoding($value['tname3'],"UTF-8","BIG5"));
		$qty=($value['tamt3']=='')?1:$value['tamt3'];
		$sql="insert into drug_times(timesno,name,qty) values('$no','$name',$qty)";		
		echo $sql."<br>";

		$conn->exec($sql);
	}

	$conn->exec("truncate table drugcom");
	$conn->exec("truncate table drugcomdetails");

	echo "<br><br>藥品 資料轉換完成!!";

?>