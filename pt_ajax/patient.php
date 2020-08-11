<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//醫師資料
	$RS=$conn->query("select id,name from leconfig.zhi_staff ");
	$drArr=[];
	foreach ($RS as $key => $value) {
		$drArr[$value['name']]=$value['id'];
	}
	//清除患者基本資料表
	$conn->exec("truncate table customer");

	//患者基本資料
	$sql = "SELECT * FROM user.dat";
	$result=$db->query($sql);
	$r=0;
	echo "<br>";
	foreach ($result as $key => $value) {
		$keyword=addslashes( $value['keyword']);
		
		$cusno=trim(mb_convert_encoding($value['medical_no'],"UTF-8","BIG5"));
		$name=trim(mb_convert_encoding($value['name'],"UTF-8","BIG5"));
		$Doctor=trim(mb_convert_encoding($value['user_no'],"UTF-8","BIG5"));
		$maindrno=$drArr[$Doctor];
		if ($maindrno==''){
			$maindrno=0;
		}

		if (strlen(trim($value['birthday']))<=7){
			$birth='';
		}else{
			$bb=explode(".",$value['birthday']);
			$birth=($bb[0]+1911).'-'.$bb[1].'-'.$bb[2];
		}
		$id=$value['id'];
		$mobile=mb_convert_encoding($value['tel'],"UTF-8","BIG5");
		$addr=trim(mb_convert_encoding($value['address'],"UTF-8","BIG5"));
		$road=mb_convert_encoding(substr($value['address'],0,36),"UTF-8","BIG5");
		$lane=substr($value['address'],36,4)=='    '?'':(mb_convert_encoding(substr($value['address'],36,4),"UTF-8","BIG5").'巷');
		$Aly=substr($value['address'],40,4)=='    '?'':(mb_convert_encoding(substr($value['address'],40,4),"UTF-8","BIG5").'弄');
		$no=substr($value['address'],44,4)=='    '?'':(mb_convert_encoding(substr($value['address'],44,4),"UTF-8","BIG5").'號');
		$floor=substr($value['address'],48,4)=='    '?'':(mb_convert_encoding(substr($value['address'],48,4),"UTF-8","BIG5").'樓');
		$rm=substr($value['address'],52,4)=='    '?'':('之'.mb_convert_encoding(substr($value['address'],52,4),"UTF-8","BIG5"));
		$addmemo=mb_convert_encoding(substr($value['address'],-6),"UTF-8","BIG5");
		$address=$road.$lane.$Aly.$noechoec.$floor.$rm.$addmemo;
		$zip=$value['zip'];
		$address=$road.$lane.$Aly.$no.$floor.$rm.$addmemo;
		$memo=addslashes(mb_convert_encoding($value['memo'],"UTF-8","BIG5")).'\n'.addslashes(trim(mb_convert_encoding($value['address2'],"UTF-8","BIG5")));
		$sex=trim(mb_convert_encoding($value['sex'],"UTF-8","BIG5"));
		$cussex=($sex=='女')?'0':'1';

		if (strlen(trim($value['finaldate']))!=9){
			$lastDT='';
		}else{
			$fdt=explode(".",$value['finaldate']);
			$lastDT=($fdt[0]+1911).'-'.$fdt[1].'-'.$fdt[2];
		}

		//ctime 暫存keyword
		$sql="insert into customer (ctime,cusno,cusname,cusbirthday,lastdate,cussex,cusid,custel,cusmob,cusaddr,maindrno,lastdrno,cusmemo,cuszip)
		 	values
		 	('$keyword','$cusno','$name','$birth','$lastDT','$cussex','$cusid','$tel','$mobile','$address',$maindrno,$maindrno,'$memo','$zip')";
		echo "$sql<br>";


		$conn->exec($sql);

		$conn->exec("insert into custemp (cusno,cusname) values('$cusno','$cusname')");
	}
	
	echo "<h1>患者基本資料 轉換完成</h1>";

?>