<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    $path='c:\dent01\dent01_10.mdb';

	//新增處置
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);

	
	$sql = " select a.no as ano,a.name,a.spec,a.cat,a.disease,a.treat,a.icd9,b.no as bno,no1,per,tot,c.no as cno,name1,name2,name3,name4,name5,name6,name7,name8,name9,name10,d.no as dno,d.name as dname,sale1
			   from thfile as a,thfile2 as b,thstatus as c,thprice d
			  where a.no=b.no
			    and a.no=c.no
			    and b.no1=d.no
			  order by a.no";
	$result = $db->query($sql);
	$conn->exec("truncate table test.tr_set_memo");
	foreach ($result as $key => $value) {
		$v0=$value[0];
		$v1=addslashes(mb_convert_encoding($value[1],"UTF-8","BIG5"));
		$v2=$value[2];
		$v3=$value[3];
		$v4=$value[4];
		$v5=$value[5];
		$v6=$value[6];
		$v7=$value[7];
		$v8=$value[8];
		$v9=$value[9];
		$v10=$value[10];
		$v11=$value[11];
		$v12=addslashes(mb_convert_encoding($value[12],"UTF-8","BIG5"));
		$v13=addslashes(mb_convert_encoding($value[13],"UTF-8","BIG5"));
		$v14=addslashes(mb_convert_encoding($value[14],"UTF-8","BIG5"));
		$v15=addslashes(mb_convert_encoding($value[15],"UTF-8","BIG5"));
		$v16=addslashes(mb_convert_encoding($value[16],"UTF-8","BIG5"));
		$v17=addslashes(mb_convert_encoding($value[17],"UTF-8","BIG5"));
		$v18=addslashes(mb_convert_encoding($value[18],"UTF-8","BIG5"));
		$v19=addslashes(mb_convert_encoding($value[19],"UTF-8","BIG5"));
		$v20=addslashes(mb_convert_encoding($value[20],"UTF-8","BIG5"));
		$v21=addslashes(mb_convert_encoding($value[21],"UTF-8","BIG5"));
		$v22=addslashes(mb_convert_encoding($value[21],"UTF-8","BIG5"));
		$v23=addslashes(mb_convert_encoding($value[23],"UTF-8","BIG5"));
		$v24=$value[24];

		$sql="insert into tr_set_memo
				values('$v0','$v1','$v2','$v3','$v4','$v5','$v6','$v7','$v8','$v9','$v10',
					   '$v11','$v12','$v13','$v14','$v15','$v16','$v17','$v18','$v19','$v20',
					   '$v21','$v22','$v23',$v24)";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "$sql;<br>";
		}
	}
	
	echo "<h1>新增處置資料完成</h1>";
?>





