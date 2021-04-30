<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];

    echo "<br>轉入處置資料 treat_record<br>";
    $sql="truncate table treat_record ";
    $mariaConn->exec($sql);

    $sql="SELECT c.RegNo as RegNo,d.DealSimCode as code,DealCodeName,CONVERT(CHAR(10),DealDate,120) as DealDate,
                DealAmt,TreatNo,fdi,face,nums,convert(varchar(1500),[Desc]) as descs,sp,sickno,muliter,sick10no
            from  RegisterDeal c,Deal d 
           where d.DealNo=c.DealNo
             and d.deleteDate is null
             and d.enable='1'";
    $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($result)) {
        if ($row['code']!=''){
            $trcode=trim($row['code']);
            $trname=trim($row['DealCodeName']);
            $ddate=$row['DealDate'];
            $punitfee=($row['DealAmt']==null)?0:$row['DealAmt'];
            $side=$row['face'];
            $nums=$row['nums'];
            $desc=addslashes($row['descs']);
            $sickno=trim($row['sickno']);
            $addp=round($row['muliter']/100,2);
            $pamt=round($punitfee*$nums*$addp,0);
            $icd10=trim($row['sick10no']);
            $icuploadd=$row['RegNo'];
            $uploadd=$row['TreatNo'];
            if (strlen($row['fdi'])>=13){
                for ($i=0; $i < $time; $i++) { 
                    $fdi=addslashes(substr($row['fdi'],$i*12,12));
                    if ($i!=0){
                        $nums=0;
                        $pamt=0;
                    }
                    $insertSQL="INSERT into treat_record (regsn,ddate,fdi,trcode,treatname,side,add_percent,nums,punitfee,pamt,icd10,sickno,treat_memo,icuploadd,uploadd)
                        values
                        (0,'$ddate','$fdi','$trcode','$trname','$side',$addp,$nums,$punitfee,$pamt,'$icd10','$sickno','$desc','$icuploadd','$uploadd')";
                    $isok=$mariaConn->exec($insertSQL);
                    if ($isok==0){
                         echo "insert 失敗：$insertSQL<br>";
                    }
                }
            }else{
                $fdi=addslashes($row['fdi']);
                $insertSQL="INSERT into treat_record (regsn,ddate,fdi,trcode,treatname,side,add_percent,nums,punitfee,pamt,icd10,sickno,treat_memo,icuploadd,uploadd)
                    values
                    (0,'$ddate','$fdi','$trcode','$trname','$side',$addp,$nums,$punitfee,$pamt,'$icd10','$sickno','$desc','$icuploadd','$uploadd')";
                $isok=$mariaConn->exec($insertSQL);
                if ($isok==0){
                     echo "insert 失敗：$insertSQL<br>";
                }
            }
        }
    }

    sqlsrv_close($msConn);
    echo "<h1>轉入處置完成</h1>";
?>