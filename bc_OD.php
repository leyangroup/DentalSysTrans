<?php
    include_once "include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $serverName="192.168.1.20\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();

   //  $sql="select a.*,c.regno,b.* 
   //  	    from registerdeal a,deal b,register c  
		 //   where len(b.face)=5
			// and b.dealno=a.dealno
			// and c.regno=a.regno";
    $sql="select c.RegNo as RegNo,d.DealSimCode as code,DealCodeName,CONVERT(CHAR(10),DealDate,120) as DealDate,
                    DealAmt,TreatNo,fdi,face,nums,convert(varchar(1500),[Desc]) as descs,sp,sickno,muliter,sick10no
    	    from registerdeal c,deal d,register f  
		   where len(d.face)=5
			and d.dealno=c.dealno
			and f.regno=c.regno";
	
	$result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($result)) {
        if ($row['code']!=''){
            $trcode=$row['code'];
            $trname=$row['DealCodeName'];
            $ddate=$row['DealDate'];
            $punitfee=$row['DealAmt'];
            $fdi=$row['fdi'];
            $side=str_replace("+", "", $row['face']);
            $nums=$row['nums'];
            $pamt=$punitfee*$nums;
            $desc=str_replace('>','＞',$row['descs']);
            $desc=str_replace('<','＜',$desc);
            $desc=str_replace("'",'’',$desc);
            $sickno=$row['sickno'];
            $addp=round($row['muliter']/100,2);
            $icd10=$row['sick10no'];
            $icuploadd=$row['RegNo'];
            $uploadd=$row['TreatNo'];

            $insertSQL="INSERT into 
            			treat_record (ddate,fdi,trcode,treatname,side,add_percent,nums,punitfee,pamt,icd10,sickno,treat_memo,icuploadd,uploadd)
                		values
                		('$ddate','$fdi','$trcode','$trname','$side',$addp,$nums,$punitfee,$pamt,'$icd10','$sickno','$desc','$icuploadd','$uploadd')";
            echo $insertSQL."<br>";
            echo $ddate."--".$trcode."  ";
            $mariaConn->exec($insertSQL);
            //再用存在uploadd中的tratno去對應register 帶出開始日與號碼, t.uploadd=r.uploadd and t.ddate!=r.ddate
        }
    }

    $sql="update registration r, treat_record t 
            set t.regsn=r.regsn,t.seqno=r.seqno ,t.cussn=r.cussn
            where r.icuploadD=t.icuploadD
            and t.regsn=0";
    $mariaConn->exec($insertSQL);

    echo "<h1>完成</h1>";


?>