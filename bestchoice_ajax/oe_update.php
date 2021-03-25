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

 //    $mariaConn->exec("DROP TABLE IF EXISTS test.tmpbc_oe");
 //    $mariaConn->exec("CREATE TABLE test.tmpbc_oe (
 //                      `pdid` varchar(10) NOT NULL,
 //                      `PatNo` varchar(10) NOT NULL,
 //                      `cussn` int(5) not null DEFAULT 0,
 //                      `payDT` varchar(10) NOT NULL,
 //                      `pay` int(11) NOT NULL DEFAULT 0,
 //                      `ReallyTC` int(11) NOT NULL DEFAULT 0
 //                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

 //    $mariaConn->exec("ALTER TABLE test.tmpbc_oe ADD KEY `Proid` (`Proid`,`payDT`)");

	// //自費資料
 //    echo "<br>轉入自費明細<br>";
 //    $sql="SELECT a.ProID,a.PatNo, convert(char(10),b.ActDate,120) ActDate,b.TheCharges,ReallyTC,b.PdID
 //            from Project a,Project_Detail b 
 //           where a.ProID=b.ProID
 //             and a.Enable=1
 //             and b.Enable=1
 //             and TheCharges!=reallytc
 //             order by a.ProID,b.PdID";

 //    $result=sqlsrv_query($msConn,$sql) or die("1.sql error:".sqlsrv_errors());
 //    while ($row=sqlsrv_fetch_array($result)){
 //        $pdid=$row['PdID']; //優勢代碼
 //        $patno=$row['PatNo']; //病編 先存在sign 到時候再對應
 //        $ActDate=$row['ActDate'];
 //        $payamt=$row['TheCharges'];
 //        $ReallyTC=$row['ReallyTC'];
 //        $insertSQL="INSERT into test.tmpbc_oe(pdid,PatNo,payDT,pay,ReallyTC)
 //                    values('$pdid','$patno','$ActDate',$payamt,$ReallyTC)";
 //        $isok=$mariaConn->exec($insertSQL);
 //        if ($isok==0){
 //            echo "新增 tmpbc_oe 失敗：$insertSQL"."<br>";
 //        }
 //    }
        
 //    //對應患者代碼
 //    $mariaConn->exec("UPDATE test.tmpbc_oe o,eprodb.customer c 
 //                        set o.cussn=c.cussn 
 //                        where c.cusno=o.patno and o.cussn=0");
 //    sqlsrv_close($msConn);


 //    $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
 //    $msConn=sqlsrv_connect($serverName,$connectionInfo);
 //    if ($msConn===false){
 //        die(print_r(sqlsrv_errors(),true));
 //    }

    $mariaConn->exec("drop table if exists test.tmpapp");
    $sql="CREATE TABLE test.tmpapp (
          `ddate` varchar(10) DEFAULT NULL,
          `cusno` varchar(10) DEFAULT NULL,
          `schtime` varchar(5) DEFAULT NULL,
          `schnote` text DEFAULT NULL,
          `enable` int(5) NOT NULL DEFAULT 0,
          `iscancel` int(5) NOT NULL DEFAULT 0,
          `drno1` int(5) NOT NULL DEFAULT 0,
          `cussn` int(5) NOT NULL DEFAULT 0
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8"; 
    $mariaConn->exec($sql);

        $staff=[];
        $sql="select sfsn,sfno from staff order by sfsn";
        $result=$mariaConn->query($sql);
        foreach ($result as $key => $value) {
            $staff[$value['sfno']]=$value['sfsn'];
        }

        $sql="SELECT OrderNo,PatNo,patName,DoctorNo,
                     convert(char(16),StartDate,120)StartDate,
                     convert(char(16),EndDate,120)EndDate,
                     convert(char(10),StartDate,120)sd,
                     convert(char(10),EndDate,120)ed,Notes,IsCancel,enable
                from [Order]
               where StartDate>='2021-03-08'
                 and OrderStatus='4' ";

        $result=sqlsrv_query($msConn,$sql) or die("2.sql error:".sqlsrv_errors());
        while ($row=sqlsrv_fetch_array($result)) {
            if (substr($row['sd'],0,10)>=$DT){
                $cusno=$row['PatNo'];
                $dr=$staff[$row['DoctorNo']];
                $appdt=substr($row['sd'],0,10);
                $schtime=substr($row['StartDate'],-5);
                $note=str_replace("'","’",$row['Notes']);
                $IsCancel=$row['IsCancel'];
                $enable=$row['enable'];
                $insertSQL="INSERT into test.tmpapp(ddate,cusno,schtime,schnote,enable,iscancel,drno1,cussn)
                            values('$appdt','$cusno','$schtime','$note',$enable,$IsCancel,$dr,0)";
                $isok=$mariaConn->exec($insertSQL);
                if ($isok==0){
                    echo "新增 tmpapp 失敗：$insertSQL"."<br>";
                }
            }
        } 

        //處理patient
        $mariaConn->exec("update test.tmpapp set cussn=-1 where cusno like 'NP%' ");
        $mariaConn->exec("update test.tmpapp t,eprodb.customer c 
                             set t.cussn=c.cussn
                           where t.cusno=c.cusno 
                             and t.cussn=0");
    echo "<h1>轉入 完畢 請處理預約刪除問題</h1>";
    sqlsrv_close($msConn);

?>