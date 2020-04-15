<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    echo $serverName."<br>";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();

    echo "<br>轉入處置資料 treat_record<br>";
        $sql="SELECT c.RegNo as RegNo,d.DealSimCode as code,DealCodeName,CONVERT(CHAR(10),DealDate,120) as DealDate,DealAmt,TreatNo,fdi,face,nums,convert(varchar(1500),[Desc]) as descs,sp,sickno,muliter,sick10no
                from  RegisterDeal c,Deal d 
               where d.DealNo=c.DealNo
                 and d.deleteDate is null
                 and d.enable='1'
                 and d.DealSimCode in ('89014','89015')
                 and LEN(Face)=5";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            if ($row['code']!=''){
                $trcode=$row['code'];
                $trname=$row['DealCodeName'];
                $ddate=$row['DealDate'];
                $punitfee=$row['DealAmt'];
                $fdi=$row['fdi'];
                $side=$row['face'];
                $nums=$row['nums'];
                $pamt=$punitfee*$nums;
                // $desc=$row['descs'];
                $desc=str_replace('>','＞',$row['descs']);
                $desc=str_replace('<','＜',$desc);
                $desc=str_replace("'",'’',$desc);
                $sickno=$row['sickno'];
                $addp=round($row['muliter']/100,2);
                $icd10=$row['sick10no'];
                $icuploadd=$row['RegNo'];
                $uploadd=$row['TreatNo'];

                $insertSQL="INSERT into treat_record (regsn,ddate,fdi,trcode,treatname,side,add_percent,nums,punitfee,pamt,icd10,sickno,treat_memo,icuploadd,uploadd)
                    values
                    (0,'$ddate','$fdi','$trcode','$trname','$side',$addp,$nums,$punitfee,$pamt,'$icd10','$sickno','$desc','$icuploadd','$uploadd')";
                echo $insertSQL."<br>";
                //echo $ddate."--".$trcode."  ";
                $mariaConn->exec($insertSQL);
                //再用存在uploadd中的tratno去對應register 帶出開始日與號碼, t.uploadd=r.uploadd and t.ddate!=r.ddate
            }
        }

    sqlsrv_close($msConn);
    echo "<h1>轉入89014,89015 處置完成</h1>";
?>