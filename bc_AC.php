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

    $staff=[];
    $sql="select sfsn,sfno from staff order by sfsn";
    $result=$mariaConn->query($sql);
    foreach ($result as $key => $value) {
        $staff[$value['sfno']]=$value['sfsn'];
    }

    $sql="SELECT m.TreatNo ,aa.RegNo RNO,aa.PatNo PNO,convert(char(10),RegDate,120)regdate,
                    RegTime,CardNo,BurdenNo,BurdenAmt,[status] as st,Sp16,ClinicFrom,isOut,ApplyDoctor,
                    Totcat as cate,LookCode,LookAmt,DrugSerNo,DrugSerAmt,
                    DrugSubAmt,DealSubAmt,DrugTotalAmt,DealTotalAmt,
                    totalAmt,ApplyAmt,convert(varchar(1000),aa.MainDesc) as cc 
              from (select r.*,t.TreatNo
                      from register r left join TreatRegister t
                      on r.RegNo=t.RegNo
                      where r.Enable='1'
                     )aa left join Treatment m
                        on aa.TreatNo=m.TreatNo
                        where len([status])=4
                        order by RegDate,RegTime";
    // register.enable=0  -->刪除的，所以只讀enable=1
    $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($result)) {
        $dt=$row['regdate'];
        $seqno=substr($row['RNO'],-3);
        echo $row['RNO'].'-'.$cusno.' ';
        $regno=$row['RNO'];
        $treatno=$row['TreatNo'];
        $ddate=$row['regdate'];
        $regtime=$row['RegTime'];
        $cusno=$row['PNO'];
        $icseq=substr($regno,0,3).$row['CardNo'];
        $nhistatus=substr($row['BurdenNo'],0,3);
        $partpay=$row['BurdenAmt'];
        $ictype=substr($row['st'],0,2);
        $barid=$row['Sp16'];
        $hospfrom=$row['ClinicFrom'];
        $isout=($row['isOut']=='')?0:$row['isOut'];
        $dr=$staff[$row['ApplyDoctor']];
        $cate=isset($row['cate'])?$row['cate']:'';
        $trcode=isset($row['LookCode'])?$row['LookCode']:'';
        $trpay=isset($row['LookAmt'])?$row['LookAmt']:0;
        // $rxtype=$row['DrugS'];
        $rxtype='2';
        $rxcode=isset($row['DrogSerNo'])?$row['DrogSerNo']:'';;
        $drugsv=isset($row['DrugSerAmt'])?$row['DrugSerAmt']:0;
        $damt=isset($row['DrugSubAmt'])?$row['DrugSubAmt']:0;
        $tamt=isset($row['DealSubAmt'])?$row['DealSubAmt']:0;
        $amount=isset($row['DealTotalAmt'])?$row['DealTotalAmt']:0;
        $giaamt=$amount-$partpay;
        $cc=isset($row['cc'])?$row['cc']:'';
        $cc=str_replace("'", "’", $cc);
        $insertSQL="INSERT into registration (ddate,seqno,cusno,reg_time,drno1,drno2,ic_seqno,ic_type,category,
                    trcode,trpay,rx_type,rx_code,drugsv,nhi_status,nhi_partpay,barid,hosp_from,is_out,
                    nhi_damt,nhi_tamt,amount,giaamt,cc,icuploadd,uploadd)
                    values('$ddate','$seqno','$cusno','$regtime',$dr,$dr,'$icseq','$ictype','$cate','$trcode',$trpay,'$rxtype','$rxcode',$drugsv,'$nhistatus',$partpay,'$barid','$hospfrom',$isout,$damt,$tamt,$amount,$giaamt,'$cc','$regno','$treatno')";
        echo "<br>".$insertSQL;
        $mariaConn->exec($insertSQL);
    }
	
	//配對患者編號
    echo "<br>配對患者編號<br>";
    $sql="update `registration` r,customer c set r.cussn=c.cussn where r.cusno=c.cusno and r.cussn is null";
    $mariaConn->exec($sql);  

    echo "<br>設定診別";
    $sql="update `registration` r,section s set r.section=s.sno where reg_time between s.start and s.end and section is null";
    $mariaConn->exec($sql);  

    echo "<br>串掛號與處置";
    $sql="update registration r, treat_record t 
            set t.regsn=r.regsn,t.seqno=r.seqno ,t.cussn=r.cussn
            where r.icuploadD=t.icuploadD
              and t.regsn=0
              and r.cussn is not null";
    $mariaConn->exec($sql);  


    echo "<h1>完成</h1>";


?>