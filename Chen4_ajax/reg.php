<?php
	include_once "../include/db.php";
	include_once "../include/DTFC.php";

    $conn=MariaDBConnect();

    $path=$_GET['path'].'\dent01_10.mdb';

	//新增醫師
 	set_time_limit (0); 
    ini_set("memory_limit", "1024M"); 
	$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ=".$path);

	$conn->exec("truncate table registration");
	$dr=$conn->query("select id,no from leconfig.zhi_staff");
	$drArray=[];
	foreach ($dr as $key => $doctor) {
		$no=$doctor['no'];
		$drArray[$no]=$doctor['id'];
	}
	//原系統key  idno+date+ser  轉檔程式存在 uploadno,ddate,icuploadd
	$sql = "SELECT * FROM THTHICK order by `DATE`,KEP";
	$result = $db->query($sql);
	$r=0;
	$DT='';
	foreach ($result as $key=> $value) {
		if ($DT!=$value['DATE']){
			$r=1;
			$DT=$value['DATE'];
		}else{
			$r++;
			$DT=$value['DATE'];
		}
		$cusid=$value['IDNO'];  // 暫存在uploadno中
		$seqno=substr('000'.$r,-3);
		if (strlen(trim($value['DATE'])<7)){
			$regDT='';
		}else{
			$regDT=WestDT($value['DATE']);
			$yy=substr($value['DATE'],0,3);
		}
		$ser=$value['SER'];  // 原系統key 
		if(strlen($ser)==8){
			$ic_type=substr($ser,2,2);
			$icseqno=$yy.substr($ser,4,4);
		}elseif (strlen($ser)==2){
			$ic_type='02';
			$icseqno=$yy.$ser;
		}else{
			$ic_type='';
			$icseqno=$yy;
		}
		$nhi_status=$value['PART'];
		if ($nhi_status=='H10'){
			$nhi_partpay=50;
		}else{
			$nhi_partpay=0;
		}
		$category=trim($value['SPEC1']);

		//以下六項先存在保留欄位中
		$code1=$value['CODE1'];
		$code2=$value['CODE2'];
		$code3=$value['CODE3'];
		$icd9_1=$value['ICD9_1'];
		$icd9_2=$value['ICD9_2'];
		$icd9_3=$value['ICD9_3'];

		$item=[];
		$item=$value['ITEM_1'];
		$treatno=$value['ITEM_1'].$values['ITEM_2'].$value['ITEM_3'];

		$trcode=substr($value['SERNO'],4,6);
		$dr1=$drArray[$value['DOCT']];
		$dr2=$drArray[$value['DOCT2']];
		$trpay=$value['F1'];
		$nhi_tamt=$value['F3'];
		if ($value['KEP']==''){
			$icdatetime='';
		}else{
			$icdatetime=$value['DATE'].$value['KEP'];
		}
		$regtime=substr($icdatetime,0,2).":".substr($icdatetime,2,2);
		//療程開始日 
		if ($ic_type=='AB'){
			$ABdate=WestDT($value['DATE2']);
		}else{
			$ABdate='';			
		}
		$baraddps=round($value['F1_PER']/100,2);
		if ($category=='16'){
			$barid=$value['ITEM_1'];
		}else{
			$barid='';
		}
		$amount=$trpay+$nhi_tamt;
		$giaamt=$trpay+$nhi_tamt-$nhi_partpay;
		$insertSQL="insert into registration(ddate,stdate,drno1,drno2,uploadno,treatno,category,ic_type,ic_seqno,
								nhi_status,nhi_partpay,trcode,trpay,nhi_tamt,amount,giaamt,sickn,sickn2,sickn3,
								illname,illname2,illname3,barid,baraddps,ic_datetime,icuploadd,reg_time,seqno,cussn,rx_type)
					values('$regDT','$ABdate',$dr1,$dr2,'$cusid','$treatno','$category','$ic_type','$icseqno',
							'$nhi_status',$nhi_partpay,'$trcode',$trpay,$nhi_tamt,$amount,$giaamt,'$code1','$codr2',
							'$code3','$icd9_1','$icd9_2','$icd9_3','$barid',$baraddps,'$icdatetime','$ser','$regtime',
							'$seqno',0,'2')";

		$ok=$conn->exec($insertSQL);
		if ($ok==0){
			echo "新增掛號資料失敗 $insertSQL<br>";
		}
	}
	echo "<h2>配對掛號表與患者<h2/>";

	$conn->exec("update registration r,customer c set r.cussn=c.cussn,r.cusno=c.cusno where uploadno=cusid");

	echo "產生charge<br>";
	$sql="truncate table charge";
	$conn->exec($sql);
	$FDT=$year.'-01-0100:00';
	$LDT=$DT.'23:59';
	$sql="insert into charge (ddate,chargetime,cussn,regpay,partpay,`add`,discsn,discreg,discpart,minus,balance ,is_oweic)
			SELECT ddate,reg_time,cussn,reg_pay,nhi_partpay,reg_pay+nhi_partpay,case when r.discid is null then 0 else r.discid end ,
			case when d.reg_disc is null then 0 else d.reg_disc end,
			case when d.partpay_disc is null then 0 else d.partpay_disc end,
			disc_pay,reg_pay+nhi_partpay-disc_pay,'0'
			FROM registration r left join disc_list d
			on r.discid=d.discsn
			where concat(ddate,reg_time)between '$FDT' and '$LDT'
			and seqno<>'000'
			order by ddate,seqno";
	$conn->exec($sql);
	
	$FD=$year.'-01-01';
	$LD=$DT;
	$sql="update registration r,charge c
			 set r.chargesn=c.sn
		   where r.ddate=c.ddate
			 and r.reg_time=c.chargetime
			 and r.cussn=c.cussn
			 and r.chargesn=0
			 and r.ddate between '$FD' and '$LD' ";
	$conn->exec($sql);
	echo "<h1>新增掛號-結束</h1>";
	
?>





