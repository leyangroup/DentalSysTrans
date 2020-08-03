<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//設定索引
	// $conn->exec("ALTER TABLE customer ADD INDEX(`cusno`)");
	// $conn->exec("ALTER TABLE `treat_record` ADD INDEX( `ddate`, `seqno`)");
	// $conn->exec("ALTER TABLE `registration` ADD INDEX( `ddate`, `seqno`)");

	// $conn->exec("ALTER TABLE `pasystemdi` ADD UNIQUE( `sn`)");
	// $conn->exec("ALTER TABLE `pasystemdi` CHANGE `sn` `sn` INT(8) NOT NULL AUTO_INCREMENT COMMENT '  流水號  '");
	//院所基本資料
	$sql = "SELECT * FROM com_name.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$cname=trim(mb_convert_encoding($value['coname'],"UTF-8","BIG5"));
		$owner=trim(mb_convert_encoding($value['cmaster'],"UTF-8","BIG5"));
		$address=trim(mb_convert_encoding($value['ad1'],"UTF-8","BIG5"));
		$nhicode=$value['cod'];
		$tel=$value['tel'];
		$zip=$value['zip1'];

		$sql="update basicset set bsname='$cname',bstel='$tel',bsaddr='$addr',zip='$zip',owner='$owner',nhicode='$nhicode',bsfax='',volume='',opendate=''";
		echo $sql;

		$conn->exec($sql);
	}
	
	//新增醫師
	$conn->exec("truncate table staff");

	//sftitle 暫存申報醫師身份證
	$sql = "SELECT * FROM user.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$no=trim($value['ucode']);
		$name=trim(mb_convert_encoding($value['username'],"UTF-8","BIG5"));
		$id=$value['cid'];
		if (trim($value['aid'])==''){
			$aid=$id;
		}else{
			$aid=$value['aid'];
		}

		$birth=$value['cbirthday'];
		$BD='';
		if (strlen(trim($birth))==7){
			$yy=substr($birth,0,3)+1911;
			echo 'yy='.$yy;
			$BD=$yy.'-'.substr($birth,3,2).'-'.substr($birth,-2); 
		}
		$sex='';
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
		$position=(substr($no,0,1)=='D')?'D':'A';
		$endjob='';
		if (strlen(trim($ldate))==7){
			$yy=1911+substr($ldate,0,3);
			$endjob=$yy.'-'.substr($ldate,3,2).'-'.substr($ldate,-2);
		}
		$sql="insert into staff(sfno,sfname,sfid,sfbirthday,sfsex,sfendjob,position,sfemail,drkind)values
					('$no','$name','$id','$BD','$sex','$endjob','$position','$aid','0') ";
		echo "新增醫師：".$sql."<br>";

		$conn->exec($sql);
	}

	//優待身份
	$conn->exec("truncate table disc_list");
	$sql = "SELECT * FROM discmemo.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$discno=$value['discno'];
		$discname=trim(mb_convert_encoding($value['discmemo'],"UTF-8","BIG5"));
		$discreg=$value['dis_path'];		
		$discpt=$value['dis_pro'];

		$sql="insert into disc_list(discid,disc_name,reg_disc,partpay_disc)values
				('$discno','$discname',$discreg,$discpt)";
		echo "新增優待身份.".$sql."<br>";
		//$conn->exec($sql);
	}

	echo "<h1>診所、醫師、優待身份 資料轉換完成</h1>";

?>