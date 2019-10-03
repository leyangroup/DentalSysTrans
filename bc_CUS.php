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

    $sql="SELECT *,convert(varchar(1000),notes)as Note,convert(varchar(1000),notesEX)as NEX,convert(varchar(1000),othernote)as ONote,
                convert(varchar,Birth,120) birthday,convert(varchar,FirstDate,120) FD,convert(varchar,LastDate,120) LD
            from Patients 
            where patno in ('0990802','0830802')
            order by PatNo";
    $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    //display
    while ($row=sqlsrv_fetch_array($result)) {
        $cusno=$row['PatNo'];
        $cusname=$row['PatName'];
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
        
        if ($row['FirstDoc']==null || $row['FirstDoc']==''){
            if ($row['Doctor']==NULL){
                $fdr=0;
            }else{
                $fdr=$staff[$row['Doctor']];;
            }
        }else{
            $fdr=$staff[$row['FirstDoc']];
        }
        if ($row['LastDoc']==null || $row['LastDoc']==''){
            if ($row['Doctor']==NULL){
                $ldr=0;
            }else{
                $ldr=$staff[$row['Doctor']];;
            }
        }else{
            $ldr=$staff[$row['LastDoc']];
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
            $memo=$memo.str_replace("'", "’", $row['Note'])."char(13)";
        }
    
        if ($row['NEX']!=''){
            $memo=$memo.str_replace("'", "’", $row['NEX'])."char(13)";
        }
    
        if ($row['ONote']!=''){
            $memo=$memo.str_replace("'", "’", $row['ONote'])."char(13)";
        }
        $insertSQL="INSERT into customer (cusno,cusname,cusid,cussex,cusbirthday,custel,cusmob,cusemail,cuszip,cusaddr,cusjob,cusintro,cuslv,firstdate,lastdate,maindrno,lastdrno,barrier,cusmemo)
        values('$cusno','$cusname','$id','$sex','$birth','$tel','$mobile','$email','$zip','$addr','$job',
        '$intro','$lv','$firstdate','$lastdate','$fdr','$ldr','$sp','$memo')";
        echo $insertSQL;
        // $mariaConn->exec($insertSQL); 
    }

    echo "<h1>完成</h1>";

?>