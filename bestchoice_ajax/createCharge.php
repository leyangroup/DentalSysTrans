<?php
	include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $DT=$_GET['dt'];

    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();

	echo "<br>產生charge<br>";
    $year=substr($DT,0,4);
    $Start=$year.'-01-0100:00';
    $End=$DT.'23:59';

    $SDT=$year.'-01-01';

    $mariaConn->exec("truncate table charge"); 

    $sql="insert into charge (ddate,chargetime,cussn,regpay,partpay,`add`,discsn,discreg,discpart,minus,balance ,is_oweic)
            SELECT ddate,reg_time,cussn,reg_pay,nhi_partpay,reg_pay+nhi_partpay,
            case when r.discid is null then 0 else r.discid end ,
            case when d.reg_disc is null then 0 else d.reg_disc end,
            case when d.partpay_disc is null then 0 else d.partpay_disc end,
            disc_pay,reg_pay+nhi_partpay-disc_pay,'0'
            FROM registration r left join disc_list d
            on r.discid=d.discsn
            where concat(ddate,reg_time)between '$Start' and '$End'
            and seqno<>'000'
            order by ddate,seqno";
    echo $sql;
    $mariaConn->exec($sql); 

    $sql="update registration r,charge c
            set r.chargesn=c.sn
            where r.ddate=c.ddate
            and r.reg_time=c.chargetime
            and r.cussn=c.cussn
            and r.chargesn=0
            and r.ddate between '$SDT' and '$DT'";
    echo "<br>$sql";

    $mariaConn->exec($sql); 

     
    echo "<h1>產生charge完成</h1>";
?>