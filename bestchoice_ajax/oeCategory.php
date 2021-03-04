<?php
	include_once "../include/db.php";

    header("content-Type:text/html;charset=utf-8");
    $ip=$_GET['IP'];
    $serverName=$ip."\bestchoice";
    $connectionInfo=array("Database"=>"DoctorProject","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
    $msConn=sqlsrv_connect($serverName,$connectionInfo);
    if ($msConn===false){
        die(print_r(sqlsrv_errors(),true));
    }
    $mariaConn=MariaDBConnect();
    $DT=$_GET['DT'];

    
	//自費項目
    //由於他們的設計模式，轉入的資料會是
    //優勢的第一層=樂晴的第一層
    //優勢的第二層=樂晴的第一層
    //優勢的第二層=樂晴的第三層 且一對一
    //要新的再加
    

    $mariaConn->exec("truncate table leconfig.zhi_category");

    echo "<br>轉入自費治療項目 第一層<br>";
    $sql="select * from FirstType";        
    $Data=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($Data)){
        if ($row['Enable']=='1'){
            $sn=$row['FtID'];
            $name=$row['FtName'];
            $sort=$row['FtSort'];
            $path='0,'.$sn;
            $insertSQL="insert into leconfig.zhi_category(id,parent_id,depth,id_path,name,price,sort,created_at,created_by)
                        values($sn,0,0,'$path','$name',0,$sort,now(),0) ";
            echo "$insertSQL<br>";
            $mariaConn->exec($insertSQL);
        }
    }

    // //跑二次，要給舊資料對應 但是是在第二層
    // $sql="select * from FirstType";        
    // $Data=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    // while ($row=sqlsrv_fetch_array($Data)){
    //     if ($row['Enable']=='1'){
    //         $sn=$row['FtID'];
    //         $name=$row['FtName'];
    //         $sort=$row['FtSort'];
    //         $path='0,'.$sn;
    //         $insertSQL="insert into leconfig.zhi_category(parent_id,depth,id_path,name,price,sort,created_at,created_by)
    //                     values($sn,1,'$path','$name',0,$sort,now(),0) ";
    //         echo "$insertSQL<br>";
    //         $mariaConn->exec($insertSQL);
    //     }
    // }
    echo "<br>轉入自費治療項目 第二層<br>";
    $sql="select * from SecondType";        
    $Data=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());
    while ($row=sqlsrv_fetch_array($Data)){
            $sn++;  //記錄第二層的sn 由本系統自訂，與舊系統無關 只是對應時要再以舊資料的名稱來對應
            $parent_id=$row['StSort'];
            $name=$row['StName'];
            $sort=$row['StSort'];
            $path='0,'.$parent_id.','.$sn;
        // if ($row['Enable']=='1'){
            $insertSQL="insert into leconfig.zhi_category(id,parent_id,depth,id_path,name,price,sort,created_at,created_by)
                        values($sn,$parent_id,1,'$path','$name',0,$sort,now(),0) ";
        // }else{
        //     $insertSQL="insert into leconfig.zhi_category(id,parent_id,depth,id_path,name,price,sort,created_at,created_by,deleted_at,deleted_by)
        //                 values($sn,$parent_id,1,'$path','$name',0,$sort,now(),0,now(),0) ";
        // }            
        echo "$insertSQL<br>";
        $mariaConn->exec($insertSQL);

            //第三層 和第二層一樣內容
            $parentsn=$sn;
            $sn++;
            $path='0,'.$parent_id.','.$parentsn.','.$sn;
        // if ($row['Enable']=='1'){
        //     $insertSQL="insert into leconfig.zhi_category(id,parent_id,depth,id_path,name,price,sort,created_at,created_by,deleted_at,deleted_by)
        //                 values($sn,$parentsn,2,'$path','$name',0,$sort,now(),0,now(),0) ";
        // }else{
            $insertSQL="insert into leconfig.zhi_category(id,parent_id,depth,id_path,name,price,sort,created_at,created_by)
                        values($sn,$parentsn,2,'$path','$name',0,$sort,now(),0) ";
        // }                
        echo "$insertSQL<br>";
        $mariaConn->exec($insertSQL);
    }

    // $cate2Array=[];
    // $sql="select id,parent_id,id_path from leconfig.zhi_category where depth=1";
    // $result=$mariaConn->query($sql);
    // foreach ($result as $key => $value) {
    //     $cate2_id[$value['parent_id']]=$value['id'];
    //     $cate2_path[$value['parent_id']]=$value['id_path'];
    // }

    // // 先建立一個tmp_cate3 將資料轉入，再丟入category ，要對應至oedetail時再與tmp_cate3一起對應
    // $mariaConn->exec("drop table if exists tmp_cate3");
    // $sql="CREATE TABLE `tmp_cate3` (
    //                                   `id` int(5) NOT NULL DEFAULT 0,
    //                                   `dealno` varchar(10) DEFAULT NULL,
    //                                   `cate1` int(5) NOT NULL DEFAULT 0,
    //                                   `name` varchar(50) DEFAULT NULL,
    //                                   `price` int(8) NOT NULL DEFAULT 0
    //                                 ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='優勢自費第三層'";
    // $mariaConn->exec($sql);

    // $mariaConn->Exec("ALTER TABLE `tmp_cate3` ADD UNIQUE KEY `dealno` (`dealno`)");

    // echo "<br>轉入自費治療項目 第三層<br>";
    // $sql="select * from DealData";        
    // $Data=sqlsrv_query($msConn,$sql) or die("sql error:".sqlsrv_errors());

    // while ($row=sqlsrv_fetch_array($Data)){
    //     $sn++;
    //     $dealno=$row['DealNo'];
    //     $cate1=$row['FtID'];
    //     $name=$row['DealName'];
    //     $price=$row['DealAmt'];
    //     $cate2=$cate2_id[$cate1];
    //     $idpath=$cate2_path[$cate1].','.$sn;
    //     $insertSQL="insert into tmp_cate3(id,dealno,cate1,name,price) 
    //                         value($sn,'$dealno','$cate1','$name',$price)";
    //         echo "$insertSQL<br>";
    //     $mariaConn->exec($insertSQL);

    //     $insertSQL="insert into leconfig.zhi_category(id,parent_id,depth,id_path,name,price,sort,created_at,created_by)
    //                     values($sn,$cate2,2,'$idpath','$name',$price,$sort,now(),0) ";
    //         echo "$insertSQL<br>";
    //     $mariaConn->exec($insertSQL);
    // }

    echo "<h1>自費 資料 轉入完畢</h1>";
    sqlsrv_close($msConn);
?>