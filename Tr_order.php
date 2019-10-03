<?php 

include_once "include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $serverName="192.168.1.20\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);

    $mariaConn=MariaDBConnect();
    //$DT=$_GET['DT']

    
    echo "轉入預約資料 order<br>";
        
        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }

        // $sql="delete from registration where seqno='000'";
        // $mariaConn->exec($sql);

        $sql="SELECT OrderNo,PatNo,patName,DoctorNo,
                     convert(char(16),StartDate,120)StartDate,
                     convert(char(16),EndDate,120)EndDate,
                     convert(char(10),StartDate,120)sd,
                     convert(char(10),EndDate,120)ed,Notes 
                from [Order]
               where StartDate>='2019-04-10' 
               and Enable='1'";
               // echo $sql;
        $result=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            if (strpos($row['Notes'],"'")){
                $cusno=$row['PatNo'];
                $dr=$staff[$row['DoctorNo']];
                $dt=substr($row['sd'],0,10);
                $schtime=substr($row['StartDate'],-5);
                $min=(strtotime($row['EndDate'])-strtotime($row['StartDate']))/60;
                $note=$row['Notes'];
                $insertSQL="insert into registration(ddate,seqno,cusno,sch_time,schlen,sch_note,drno1,drno2)
                            values('$dt','000','$cusno','$schtime',$min,'$note',$dr,$dr)";
                echo "<br>".$insertSQL;
            }
            
            // $mariaConn->exec($insertSQL);            
        } 
        echo "end";

// echo "<br>預約患者的電話轉入";
//     $sql="update registration r,customer c
//              set r.schtel=c.custel,r.schmobile=c.cusmbo
//            where r.cussn=c.cussn
//              and r.seqno='000'";
//     $mariaConn->exec($sql); 

// $sql="update registration r, newpatient n
// set r.newpasn=n.sn
// where r.regsn=n.regsn";

// echo "<br>整理np的資料" ;   
//     $sql="update `registration` set cussn=-1,cusno='' where seqno='000' and cusno='NP'";
//     $mariaConn->exec($sql); 

// echo "<br>產生新患者資料表";
//     $sql="insert into newpatient(regsn,name,drno)
//         SELECT regsn,left(sch_note,3),drno1 FROM registration
//         where cussn=-1";
//     $mariaConn->exec($sql); 

// echo "<br>若電話為null 則填入0";
//     $sql="update registration set schtel='0' where seqno='000' and schtel is null";
//     $mariaConn->exec($sql);

//     $sql="update registration set schmobile='0' where seqno='000' and schmobile is null";
//     $mariaConn->exec($sql);     

// echo "<br>。。。。。。預約資料完成。。。。。。";

        ?>