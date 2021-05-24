<?php
	include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    set_time_limit (0);
    ini_set("memory_limit", "1024M");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];

    $result=$mariaConn->query("select bsname from basicset limit 1")->fetch();
    $bsname=$result['bsname'];

    echo "<br>轉入患者  customer <br>";
        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }
        $sql="truncate table customer ";
        $mariaConn->exec($sql);

        $sql="SELECT *,convert(varchar(1000),notes)as Note,convert(varchar(1000),notesEX)as NEX,convert(varchar(1000),othernote)as ONote,
                convert(varchar,Birth,120) birthday,convert(varchar,FirstDate,120) FD,convert(varchar,LastDate,120) LD,Matter,VIP
            from Patients 
            where Enable='1'
            order by PatNo";
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    //display
        while ($row=sqlsrv_fetch_array($result)) {
            $cusno=$row['PatNo'];
            // if ($cusno=='1001402' && $bsname='森源牙醫診所'){
            //     $cusname='江?賱';
            // }else{
                $cusname=$row['PatName'];
            // }
            $id=$row['ID'];
            if ($row['Sex']=='女'){
                $sex=0;
            }else{
                $sex=1;
            }
            $birth=$row['birthday'];
            $tel=$row['TelH'];
            $mobile=$row['Moble1'];
            $email=$row['Email'];
            $zip=$row['Zip1'];
            $addr=str_replace("'","’",$row['Addr1']);
            $job=$row['Job'];
            $intro=str_replace("'","’",$row['Introducer']);
            $lv=$row['Vip'];
            if ($row['FD']==null or $row['FD']=''){
                $firstdate='';
            }else{
                $firstdate=$row['FD'];
            }

            if ($row['LD']==null or $row['LD']=''){
                $lastdate='';
            }else{
                $lastdate=$row['LD'];
            }
            $fdr=0;
            $ldr=0;
            if ($row['FirstDoc']==null || $row['FirstDoc']==''){
                if ($row['Doctor']==NULL || $row['Doctor']==''){
                    $fdr=0;
                }else{
                    $fdr=(empty($staff[$row['Doctor']]))?0:$staff[$row['Doctor']];
                }
            }else{
                $fdr=(empty($staff[$row['FirstDoc']]))?0:$staff[$row['FirstDoc']];
            }
            if ($fdr==null) $fdr=0;

            if ($row['LastDoc']==null || $row['LastDoc']==''){
                if ($row['Doctor']==NULL || $row['Doctor']==''){
                    $ldr=0;
                }else{
                    $ldr=(empty($staff[$row['Doctor']]))?0:$staff[$row['Doctor']];
                }
            }else{
                $ldr=(empty($staff[$row['LastDoc']]))?0:$staff[$row['LastDoc']];
            }

            $sp=$row['sp'];
        
            echo "患者：".$row['PatNo']."--".$row['PatName'].' ';

            $memo='';
            if ($row['DrugAllergy']!=''){
                $memo='過敏藥物：'.$row['DrugAllergy']." ";
            }
            if ($row['Question']!=''){
                $memo=$memo.'系統疾病：'.$row['Question']." ";
            }
            if ($row['Note']!=''){
                $memo=$memo.str_replace("'", "’", $row['Note'])." 。 ";
            }
        
            if ($row['NEX']!=''){
                $memo=$memo.str_replace("'", "’", $row['NEX'])." 。 ";
            }
        
            if ($row['ONote']!=''){
                $memo=$memo.str_replace("'", "’", $row['ONote'])." 。 ";
            }
            if ($row['Matter']!=''){
                $memo=$memo.str_replace("'", "’", $row['Matter'])." 。 ";
            }
            $disc_code=$row['VIP'];
            if ($row['VIP']==null){
                $isdisc=0;
            }else{
                $isdisc=1;
            }
            $insertSQL="INSERT into customer (cusno,cusname,cusid,cussex,cusbirthday,custel,cusmob,cusemail,cuszip,cusaddr,cusjob,cusintro,cuslv,firstdate,lastdate,maindrno,lastdrno,barrier,cusmemo,is_disc,disc_code)
            values('$cusno','$cusname','$id','$sex','$birth','$tel','$mobile','$email','$zip','$addr','$job',
            '$intro','$lv','$firstdate','$lastdate',$fdr,$ldr,'$sp','$memo',$isdisc,'$disc_code')";
            echo $insertSQL;
            //echo "$cusno-$cusname";
            $mariaConn->exec($insertSQL);

        }
    sqlsrv_close($msConn);
    echo "<h3>患者資料轉完 </h3>";

?>
