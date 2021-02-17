<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	
	$conn->exec("DROP TABLE IF EXISTS `addr`");
	$conn->exec("CREATE TABLE `addr` (
				  `id` int(11) NOT NULL,
				  `cusno` varchar(20) CHARACTER SET utf8 NOT NULL,
				  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
				  `address` varchar(50) CHARACTER SET utf8 NOT NULL,
				  `zip` varchar(10) CHARACTER SET utf8 NOT NULL,
				  `disc` int(5) NOT NULL,
				  `code` varchar(10) CHARACTER SET utf8 NOT NULL,
				  `memo` varchar(50) CHARACTER SET utf8 NOT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");

	$conn->exec("ALTER TABLE `addr` ADD PRIMARY KEY (`id`)");
	$conn->exec("ALTER TABLE `addr` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");

	//患者基本資料
	$sql = "SELECT * FROM patient.dbf";
	$result=$db->query($sql);
	$r=0;
	echo "<h1>地址資料轉入中</h1>";

	foreach ($result as $key => $value) {
		$r++;
		$cusno=trim(mb_convert_encoding($value['pat_no'],"UTF-8","BIG5"));
		$name=trim(mb_convert_encoding($value['pat_name'],"UTF-8","BIG5"));
		$addr=trim(mb_convert_encoding($value['addr'],"UTF-8","BIG5"));
		$address=addslashes($addr);
		$zip=$value['zip'];
		$disc=$value['zk_je'];
		$code=$value['zk_code'];
		$memo=trim(mb_convert_encoding($value['zk_mem0'],"UTF-8","BIG5"));

		$sql="insert into addr(cusno,name,address,zip,disc,code,memo)
		 		values('$cusno','$name','$address','$zip',$disc,'$code','$memo')";
		
		 		// echo $sql;
		// echo "<br>";
		$ok=$conn->exec($sql);
		if ($ok==0){
			echo "新增失敗：$sql <br>";
		}
	}
	
	$conn->exec("update customer c,addr a set c.cusaddr=a.address,c.cuszip=a.zip,c.disc_code=a.code where c.cusno=a.cusno");
	
	echo "<h1>患者地址 轉換完成</h1>";

?>