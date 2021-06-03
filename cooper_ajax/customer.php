<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	$sql="select sfsn,sfno from staff ";
	$RS=$conn->query($sql);
	foreach ($RS as $key => $col) {
		$drArr[$col['sfno']]=$col['sfsn'];
	}

	//新增患者
	$conn->exec("truncate table customer");
	// $conn->exec("ALTER TABLE customer AUTO_INCREMENT=1000");
	$sql = "SELECT * FROM patient.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=trim(addslashes($v2));
					break;
				case '病患姓名':
					$v2=str_replace("'", "", trim($v2));
					$cusname=mb_convert_encoding($v2,"UTF-8","BIG5");
					break;
				// case '性別':
				// 	if (trim($v2)=='女'){
				// 		$cussex=0;
				// 	}else{
				// 		$cussex=1;
				// 	}
				// 	break;
				case '出生日期':
					if (trim($v2)==''){
						$cusbirthday='';
					}else{	
						$y=substr(trim($v2),0,3)+1911;
						$cusbirthday=$y.'-'.substr(trim($v2),3,2).'-'.substr(trim($v2),-2);
					}
					break;
				case '身份證號':
			 		$cusid=trim(mb_convert_encoding(addslashes($v2),"UTF-8","BIG5"));
			 		$sex=substr($cusid,1,1);
			 		switch ($sex) {
			 			case '1':
			 			case 'A':
			 			case 'C':
			 			case 'Y':
			 				$cussex=1;
			 				break;
			 			default:
			 				$cussex=0;
			 				break;
			 		}
					break;
				case '住宅電話':
					$v2=str_replace('-', '', $v2);
					$custel=trim(mb_convert_encoding(addslashes($v2),"UTF-8","BIG5"));
					break;
				case '地址':
					$v2=str_replace("'", "", $v2);
					$cusaddr=trim(mb_convert_encoding(addslashes($v2),"UTF-8","BIG5"));
					break;
				case '行動電話':
					$v2=str_replace('-','',$v2);
					$cusmob=trim(mb_convert_encoding(addslashes($v2),"UTF-8","BIG5"));
					break;
				case '初診日期':
					if (trim($v2)==''){
						$fdt='';
					}else{
						$y=substr(trim($v2),0,3)+1911;
						$fdt=$y.'-'.substr(trim($v2),3,2).'-'.substr(trim($v2),-2);
					}
					break;
				case '主治醫師':
					$maindrsn=$drArr[trim($v2)];
					if (empty($maindrsn)){
						$maindrsn=0;
					}
					break;
				case '病患memo':
					$v2=str_replace("'", "", $v2);
					$cusmemo=trim(mb_convert_encoding(addslashes($v2),"UTF-8","BIG5"));
					break;
			}
		}
		$sql="insert into customer (cusno,cusname,cussex,cusbirthday,cusid,custel,cusaddr,cusmob,firstdate,maindrno,lastdrno,cusmemo)
		values('$cusno','$cusname','$cussex','$cusbirthday','$cusid','$custel','$cusaddr','$cusmob','$fdt',$maindrsn,$maindrsn,'$cusmemo')";
		// echo $sql."<br>";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增失敗..$sql<br>";
		}
  
  }

	$sql="update customer set custel=cusmob where (custel='' or custel is null) and cusmob!='' and cusmob is not null";
	$conn->exec($sql);

	$sql="update customer set cusmob=custel where (cusmob='' or cusmob is null) and custel!='' and custel is not null";
	$conn->exec($sql);

	// 捉追蹤事項 在patret
	echo "<br>更新追蹤事項……";
	$sql = "SELECT * FROM patret.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '追蹤內容':
					$v2=str_replace("'", "", $v2);
					$tret=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
			}
		}
		echo 'tret='.$tret."<br>";
		if ($tret!=''){
			$sql="update customer set cusmemo=concat('$tret','。 ',cusmemo) where cusno='$cusno'";
			//echo "追蹤內容：".$sql."<br>";
			$conn->exec($sql);
		}
	}

	//填地址與郵遞區號
	echo '填地址與郵遞區號<br>';
	$sql="update customer set cusaddr=replace(cusaddr,'台','臺')";
	$conn->exec($sql);

	$sql="update customer c,zip z set cuszip=z.zip where left(cusaddr,6)=concat(county,city)";
	$conn->exec($sql);

	$sql="update customer c,zip z set cuszip=z.zip where left(cusaddr,5)=concat(county,city)";
	$conn->exec($sql);

	//修正地址
	$sql="update customer c,zip z set c.cusaddr=replace(cusaddr,concat(county,city),'') where c.cuszip=z.zip";
	$conn->exec($sql);
	echo "<h1>患者資料轉換完成";


?>
