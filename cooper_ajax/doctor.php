<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=C:\cooper");

	//院所基本資料
	$sql = "SELECT * FROM clinic.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $key2=> $value2) {
			switch (mb_convert_encoding($key2,"UTF-8","BIG5")) {
				case '診所名稱':
					$cname=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '負責人':
					$owner=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '診所地址':
					$addr=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '醫事機構號':
					$nhicode=trim($value2);
					break;
				case '診所電話':
					$tel=trim($value2);
					break;
				}
		}
		$sql="update basicset set bsname='$cname',bstel='$tel',bsaddr='$addr',owner='$owner',nhicode='$nhicode'";
		echo $sql;
		$conn->exec($sql);
	}
	
	//新增醫師
	$conn->exec("truncate table staff");
	$sql = "SELECT * FROM doctor.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $key2=> $value2) {
			switch (mb_convert_encoding($key2,"UTF-8","BIG5")) {
				case '醫師代號':
					$drno=trim($value2);
					break;
				case '醫師姓名':
					$drname=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				case '到職日期':
					if (trim($value2)==''){
						$fdt='';
					}else{
						$y=substr(trim($value2),0,3)+1911;
						$fdt=$y.'-'.substr(trim($value2),3,2).'-'.substr(trim($value2),-2);
					}
					break;
				case '離職日期':
					if (trim($value2)==''){
						$ldt='';
					}else{
						$y=substr($value2,0,3)+1911;
						$ldt=$y.'-'.substr($value2,3,2).'-'.substr($value2,-2);
					}
					break;
				case '身份證號':
					$id=trim($value2);
					switch (substr($id,1,1)) {
						case '1':
						case 'A':
						case 'C':
						case 'Y':
							$sex='1';
							break;
						default:
							$sex='0';
							break;
					}
					break;
				case '出生日期':
					if (trim($value2)==''){
						$birth='';
					}else{
						$y=substr(trim($value2),0,3)+1911;
						$birth=$y.'-'.substr(trim($value2),3,2).'-'.substr(trim($value2),-2);
					}
					break;
				case '聯絡電話':
					$tel=trim($value2);
					break;
				case '聯絡地址':
					$addr=trim(mb_convert_encoding($value2,"UTF-8","BIG5"));
					break;
				// case 'gmail':
				// 	$gmail=trim($v2);
				}
		}
		$gmail=trim($value['gmail']);
		$sql="insert into staff(sfno,sfname,sfid,sfbirthday,sfsex,sftel,sfstartjob,sfendjob,sfaddr,position,gmailno)values
					('$drno','$drname','$id','$birth','$sex','$tel','$fdt','$ldt','$addr','D','$gmail') ";
		echo "新增醫師：".$sql."<br>";

		$conn->exec($sql);
	}

	//優待身份
	$conn->exec("truncate table disc_list");
	$sql = "SELECT * FROM pattype.dbf";
	$result=$db->query($sql);
	$rs=$result->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rs as $key => $value) {
		foreach ($value as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '優免代號':
					$discno=$v2;
					break;
				case '優免名稱':	
					$discname=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '複診優免':
					$discreg=$v2;
					break;
				case '免部份負擔':
					if ($v2=='T'){
						$discpt=50;
					}else{
						$discpt=0;
					}
					break;
			}
		}
		$sql="insert into disc_list(discid,disc_name,reg_disc,partpay_disc)values
				('$discno','$discname',$discreg,$discpt)";
		echo "新增優待身份.".$sql."<br>";
		$conn->exec($sql);
	}

	echo "<h1>診所、醫師、優待身份 資料轉換完成</h1>";

?>