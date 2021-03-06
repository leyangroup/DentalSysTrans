<?php
    include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip='192.168.10.210';
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    

    echo "轉入預約資料 order<br>";
        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }
        $StartDT=$DT." 00:00:00";
        $sql="SELECT OrderNo,PatNo,patName,DoctorNo,
                     convert(char(16),StartDate,120)StartDate,
                     convert(char(16),EndDate,120)EndDate,
                     convert(char(10),StartDate,120)sd,
                     convert(char(10),EndDate,120)ed,Notes 
                from [Order]
               where StartDate<'2021-03-08'
                 and Enable='1'
                 and IsCancel='0' 
                 and OrderStatus!='4' ";
               echo $sql;
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            if (substr($row['sd'],0,10)>=$DT){
                $cusno=$row['PatNo'];
                $dr=$staff[$row['DoctorNo']];
                $appdt=substr($row['sd'],0,10);
                $schtime=substr($row['StartDate'],-5);
                $min=(strtotime($row['EndDate'])-strtotime($row['StartDate']))/60;
                $note=str_replace("'","’",$row['Notes']);
                $insertSQL="insert into registration(ddate,seqno,cusno,sch_time,schlen,sch_note,drno1,drno2)
                            values('$appdt','000','$cusno','$schtime',$min,'$note',$dr,$dr)";
                echo $cusno,'--'.$appdt." ";
            }
            $mariaConn->exec($insertSQL);            
        } 

        //處理newpatient
        $sql="update registration set cussn=-1 where cusno like 'NP%' ";
        $mariaConn->exec($sql);
        
        //增加newpatient 
        $sql="insert into newpatient (name,regsn,drno,creator)  
              select cusno,regsn,drno1,0
                from registration 
               where cussn=-1";
        $mariaConn->exec($sql);
        
        //配對  
        $sql="update registration r,newpatient n 
                 set newpasn=n.sn
               where r.regsn=n.regsn";
        $mariaConn->exec($sql);

    sqlsrv_close($msConn);
    echo "<h1>轉入預約資料 完畢</h1>";
?>