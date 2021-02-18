<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//醫師資料
	$drA=[];
	$sql="select sfno,sfsn from staff ";
	$result=$conn->query($sql);
	foreach ($result as $key => $value) {
		$drA[$value['sfno']]=$value['sfsn'];
	}
	print_r($drA);
	$sql="truncate table customer";
	$conn->exec($sql);

	//清除患者基本資料表
	$conn->exec("truncate table customer");

	//患者基本資料
	$sql = "SELECT * FROM patient.dbf";
	$result=$db->query($sql);
	$r=0;
	echo "<h1>資料轉入中</h1>";

	foreach ($result as $key => $value) {
		$r++;
		$cusno=trim(mb_convert_encoding($value['pat_no'],"UTF-8","BIG5"));
		$name=trim(mb_convert_encoding($value['pat_name'],"UTF-8","BIG5"));
		$shortCode=trim(mb_convert_encoding($value['shcode'],"UTF-8","BIG5"));
		$cusname=str_replace("'", "\'", $name);
		$cusbirth=WestDT($value['birth']);
		$firstDT=WestDT($value['fdate']);
		$lastDT=WestDT($value['ldate']);
		$sex=($value['sex'])?'1':'0';
		$cardid=$value['cardid'];
		$cusid=$value['id'];
		$tel=addslashes(trim(mb_convert_encoding($value['tel1'],"UTF-8","BIG5")));
		$mobile=addslashes(trim(mb_convert_encoding($value['cmt'],"UTF-8","BIG5")));
		$addr=trim(mb_convert_encoding($value['addr'],"UTF-8","BIG5"));
		$address=addslashes($addr);
		$maindrno=($drA[trim($value['dr_no'])]=='')?0:$drA[trim($value['dr_no'])];
		$areacode=$value['areacode'];
		$memo=trim(mb_convert_encoding($value['patmemo'],"UTF-8","BIG5"));
		$memo=addslashes($memo);		
		$soproid=$value['soproid'];
		$zip=$value['zip'];
		$sql="insert into customer (cusno,cusname,cussname,cusbirthday,firstdate,lastdate,cussex,iccardid,cusid,custel,cusmob,cusaddr,maindrno,lastdrno,areacode,cusmemo,sopro_id,zip)
		 		values('$cusno','$cusname','$shortCode','$cusbirth','$firstDT','$lastDT','$sex','$cardid','$cusid','$tel','$mobile',
		 		'$address',$maindrno,$maindrno,'$areacode','$cusmemo',$soproid,'$zip')";
		
		 		// echo $sql;
		// echo "<br>";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增失敗：$sql <br>";
		}
	}
	
	$sql="update customer c,zip z  
			 set cuszip=z.zip,cusaddr=replace(cusaddr,concat(county,city),'')
		   where c.cusaddr like concat(county,city,'%')	 ";
	$conn->exec($sql);
	
	echo "<h1>患者基本資料 轉換完成</h1>";

?>