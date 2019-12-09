<?php
	include_once "../include/db.php";
    $conn=MariaDBConnect();
    set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);

	//處置
	$sql = "SELECT * FROM op_opasm";
	$result=$db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		foreach ($row as $k2=> $v2) {
			switch (mb_convert_encoding($k2,"UTF-8","BIG5")) {
				case '病歷編號':
					$cusno=$v2;
					break;
				case '就診次數':
					$cnt=$v2;
					break;
				case '位置代號':
					//echo "v2=".$v2."--";
					$fdiarr=str_split($v2,3);
					//var_dump($fdiarr);
					$ans='';
					for($i=0;$i<sizeof($fdiarr);$i++){
						$rv='';
						$areaA=$i+1;
						$areaC=$i+5;
						
						//取各限象的值
						$areaVal=trim($fdiarr[$i]);
						switch ($areaVal) {
							case 'FM':
							case 'UA':
							case 'UB':
							case 'UR':
							case 'UL':
							case 'LL':
							case 'LR':
								$rv=$areaVal;
								break;
							case 'UN':
								$rv='99';
								break;
							case '':
								$rv='';
								break;	
							default:
								//折分各項限的牙位
								$len=strlen($areaVal);
								$v='';
								for ($j=0;$j<$len;$j++){
									$val=substr($areaVal,$j,1);
									switch ($val) {
										case 'A':
											$v=$areaC.'1';
											break;
										case 'B':
											$v=$areaC.'2';
											break;
										case 'C':
											$v=$areaC.'3';
											break;
										case 'D':
											$v=$areaC.'4';
											break;
										case 'E':
											$v=$areaC.'5';
											break;
										default:
											$v=$areaA.$val;
											break;
									}
									$rv=$rv.$v;
								}
								break;
						}
						$ans=$ans.$rv;
					}
					$fdi=$ans;
					//echo $fdi."<br>";
					//$fdi=$v2;
					break;	
				case '處置代號':
					$trcode=trim($v2);
					break;
				case '國際病碼':
					$sickno=trim($v2);
					break;
				case '處置病歷':
					$v2=str_replace("'", "", $v2);
					$memo='TX.'.trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '處置病歷s':
					$v2=str_replace("'", "", $v2);
					$cc=trim(mb_convert_encoding($v2,"UTF-8","BIG5"));
					break;
				case '健保數量1':
					$qty=$v2;
					break;
				case '部位':
					$part=trim($v2);
					break;
				case '健保單價1':
					$price=$v2;
					break;
				case '國際病碼a1':
					$icd10=trim($v2);
					break;
				case '手術代碼a1':
					$pcs1=trim($v2);
					break;
				case '手術代碼a2':
					$pcs2=trim($v2);
					break;
			}
		}
		$pamt=$price*$qty;
		if ($trcode=='92001C' || $trcode=='92012C'){
			$sql="insert into treat_record(regsn,uploadD,sickn,fdi,trcode,sickno,side,treat_memo,nums,punitfee,add_percent,pamt,icd10,icd10pcs1,icd10pcs2,cc) values
					(0,'$cusno','$cnt','$fdi','$trcode','$sickno','$part','$memo',$qty,$price,1,$pamt,'$icd10','$pcs1','$pcs2','$cc' )";
			echo $sql;
			// echo $cusno."-".$cnt." 。 ";
			$conn->exec($sql);
		}
	}
	//資料關連 掛號表與處置
	echo "資料關連中……";
	$sql="update registration r,treat_record t 
			 set t.regsn=r.regsn,t.ddate=r.ddate,t.seqno=r.seqno,t.cussn=r.cussn
		   where  r.cusno=t.uploadD
		      and r.stdate=t.SICKN";
	$conn->exec($sql);


	echo "<h1>補處置說明裡有引號的 資料";

?>