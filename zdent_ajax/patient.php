<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $path=$_GET['path'];

 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$path);

	//新增患者
	$conn->exec("truncate table customer");
	$dr=$conn->query("select sfsn,sfname from staff where position='D' ");
	$drArray=[];
	foreach ($dr as $key => $drV) {
		$drname=$drV['sfname'];
		$drArray[$drname]=$drV['sfsn'];
	}
	//患者基本資料  用apdate來儲存患者主鍵，才來關連掛號
	$sql = "SELECT * FROM z_ZPAT.dbf";
	$result=$db->query($sql);
	foreach ($result as $key => $value) {
		$ukey=$value['ukey'];
		$patno=trim(mb_convert_encoding($value['patno'],"UTF-8","BIG5"));
		$name=trim(mb_convert_encoding($value['name'],"UTF-8","BIG5"));
		$birth=westDT(trim($value['birth']));
		$id=trim(mb_convert_encoding($value['id'],"UTF-8","BIG5"));
		$sex=trim($value['sex']);
		if ($sex==''){$sex=0;}
		$firstdate=westDT($value['inidate']);
		$tel1=trim(str_replace('-', '',trim(mb_convert_encoding(addslashes($value['tel1']),"UTF-8","BIG5"))));
		$tel2=trim(str_replace('-', '',trim(mb_convert_encoding(addslashes($value['tel2']),"UTF-8","BIG5"))));
		$fax=str_replace('-', '',trim(mb_convert_encoding(addslashes($value['fax']),"UTF-8","BIG5")));
		$address=trim(mb_convert_encoding(addslashes($value['addr']),"UTF-8","BIG5"));
		$dr=trim(mb_convert_encoding(addslashes($value['lastdoc']),"UTF-8","BIG5"));
		$lastdoc=$drArray[$dr];
		if (empty($lastdoc)) {$lastdoc=0;}
		$lastdate=westDT($value['lastdate']);
		$note=trim(mb_convert_encoding(addslashes($value['note']),"UTF-8","BIG5"));
		if ($tel2=='' && $tel1!=''){
			$tel2=$tel1;
		}
		if ($tel2=='' && $tel1==''){
			$tel2='09';
		}

		$sql="insert into customer(familyno,cusno,cusname,cusbirthday,cusid,cussex,firstdate,custel,cusmob,cusfax,cusaddr,maindrno,lastdrno,lastdate,cusmemo) 
			  values('$ukey','$patno','$name','$birth','$id','$sex','$firstdate','$tel1','$tel2','$fax','$address',$lastdoc,$lastdoc,'$lastdate','$note')";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "$sql<br>";
		}
	}
	echo "<br>患者資料轉換完畢!!";

?>