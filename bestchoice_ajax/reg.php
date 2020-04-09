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

    echo "<br>轉入掛號 registration<br>";
        $sql="truncate table registration ";
        $mariaConn->exec($sql);

        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }

        $result=$mariaConn->query("select * from disc_list");
        foreach ($result as $key => $value) {
            $disc[$value['discid']]=$value['discsn'];
        }

        $sql="SELECT m.TreatNo ,aa.RegNo RNO,aa.PatNo PNO,convert(char(10),RegDate,120)regdate,
                    RegTime,CardNo,BurdenNo,BurdenAmt,[status] as st,Sp16,ClinicFrom,isOut,ApplyDoctor,
                    Totcat as cate,LookCode,LookAmt,DrugSerNo,DrugSerAmt,
                    DrugSubAmt,DealSubAmt,DrugTotalAmt,DealTotalAmt,
                    totalAmt,ApplyAmt,convert(varchar(1000),aa.MainDesc) as cc ,RegAmt,RebateReason,Rebate
              from (select r.*,t.TreatNo
                      from register r left join TreatRegister t
                      on r.RegNo=t.RegNo
                      where r.Enable='1'
                     )aa left join Treatment m
                        on aa.TreatNo=m.TreatNo
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
            $trcode=isset($row['LookCode'])?trim($row['LookCode']):'';
            $trpay=isset($row['LookAmt'])?$row['LookAmt']:0;
            // $rxtype=$row['DrugS'];
            $rxtype='2';
            $rxcode=isset($row['DrogSerNo'])?$row['DrogSerNo']:'';;
            $drugsv=isset($row['DrugSerAmt'])?$row['DrugSerAmt']:0;
            $damt=isset($row['DrugSubAmt'])?$row['DrugSubAmt']:0;
            $tamt=isset($row['DealSubAmt'])?$row['DealSubAmt']:0;
            $amount=isset($row['DealTotalAmt'])?$row['DealTotalAmt']:0;
            $giaamt=$amount-$partpay;
            $reg_pay=$row['RegAmt'];
            if ($row['RebateReason']!=''){
                $discid=$disc[$row['RebateReason']];
            }else{
                $discid=0;
            }
            $disc_pay=$row['Rebate'];
            $cc=isset($row['cc'])?$row['cc']:'';
            $cc=str_replace("'", "’", $cc);
            $insertSQL="INSERT into registration (ddate,seqno,cusno,reg_time,drno1,drno2,ic_seqno,ic_type,category,
                        trcode,trpay,rx_type,rx_code,drugsv,nhi_status,nhi_partpay,barid,hosp_from,is_out,
                        nhi_damt,nhi_tamt,amount,giaamt,cc,icuploadd,uploadd,case_history,reg_pay,disc_pay,discid,roomsn)
                        values('$ddate','$seqno','$cusno','$regtime','$dr','$dr','$icseq','$ictype','$cate','$trcode',$trpay,'$rxtype','$rxcode',$drugsv,'$nhistatus',$partpay,'$barid','$hospfrom',$isout,$damt,$tamt,$amount,$giaamt,'$cc','$regno','$treatno','4',$reg_pay,$disc_pay,$discid,1)";
            echo "<br>".$insertSQL;
            echo $ddate."-".$seqno.", ";
            $mariaConn->exec($insertSQL);
        }

        $mariaConn->exec("update registration set amount=trpay+nhi_tamt+nhi_damt+drugsv,giaamt=trpay+nhi_tamt+nhi_damt+drugsv-nhi_partpay-drug_partpay");

    sqlsrv_close($msConn);
    echo "<h1>轉入掛號 完成</h1>";

?>