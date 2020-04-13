<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	$today=ROCdate($_GET['dt']);
	//醫師資料
	$sql="select sfsn,sfno from staff  ";
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$Dr[$value['sfno']]=$value['sfsn'];
	}
	echo $today."<br>";
	var_dump($Dr);

	echo "0002=".$Dr[0002];

	$conn->exec("delete from registration where seqno='000'");
	//預約資料
	$sql = "SELECT * FROM schk.dbf where p_date>='$today'";
	echo $sql;
	$result=$db->query($sql);
	$r=0;
	foreach ($result as $key => $value) {
		$patno=trim($value['pat_no']);
		$dt=westdt($value['p_date']);
		$time=trim($value['p_time']);
		$len=$value['p_len'];
		$drno=trim($value['dr_no']);
		$adr=$Dr[$drno];
		$memo=trim(mb_convert_encoding($value['comms'],"UTF-8","BIG5"));
		$memo=str_replace("\\", "＼", $memo);
		$memo=str_replace("'", "\'", $memo);
		$sql="insert into registration(ddate,seqno,cusno,sch_time,schlen,drno1,drno2,sch_note,schtel,schmobile) 
				values('$dt','000','$patno','$time',$len,$adr,$adr,'$memo','0','0')";
		$r++;
		echo "$r.$sql<br>";

		$conn->exec($sql);
	}

	//將預約的電話與手機填入患者基本資料的電話與手機
	$sql="update registration r,customer c 
			 set r.cussn=c.cussn
			where r.cusno=c.cusno
			  and r.seqno='000' ";
	$conn->exec($sql);

	$sql="update registration r,customer c 
			 set r.schtel=c.custel
			where r.cusno=c.cusno
			  and r.seqno='000' 
			  and c.custel!='' ";
	$conn->exec($sql);
	
	$sql="update registration r,customer c 
			 set r.schmobile=c.cusmob
			where r.cusno=c.cusno
			  and r.seqno='000' 
			  and c.cusmob!='' ";
	$conn->exec($sql);

	$conn->exec("truncate table delappom");
	$sql="insert into delappom (regsn,ddate,cussn,cusno,drno1,userid,sch_time,schlen,schqty,sch_note,muid,schtel,chgtype)
			SELECT regsn,ddate,cussn,cusno,drno1,1,sch_time,schlen,1,sch_note,1,schtel,'a' FROM `registration` where seqno='000'";
	$conn->exec($sql);

		
	echo "資料轉換完成";

?>