<?php
function MariaDBConnect(){
    try {
        $conn=new PDO('mysql:host=localhost:3306;dbname=eprodb','leyan','2016leyan0429',array(PDO::ATTR_PERSISTENT => true));
        $conn->query('set names utf8;');
        return $conn;
    } catch (PDOException $e) {
        print "Couldn't connect to the database:".$e->getMessage();
    }
}

function MsSQLConnect(){
    try{
        header("content-Type:text/html;charset=utf-8");
        $serverName="192.168.1.20\bestchoice";
        $connectionInfo=array("Database"=>"Doctor","UID"=>'bestchoice',"PWD"=>"0937093374","CharacterSet"=>"UTF-8");
        $conn=sqlsrv_connect($serverName,$connectionInfo);
        return $conn;    
    }catch(PDOException $e){
        print "Couldn't connect to the database:".$e->getMessage();
    }
}

function postgreConnect(){

	try {
		$conn_string  =  "host=localhost port=54321 dbname=test user=postgres password=1234" ; 
		$dbconn = pg_connect($conn_string);
		if (!$dbconn) 
		    echo "连接失败！！！！！/r/n";
		else 
		    echo "连接成功！！！！！/r/n";
		    

		// $host='127.0.0.1';
		// $db='test';
		// $conn=new PDO("pgsql:host=$host;port=54321;dbname=$db;",array(PDO::ATTR_PERSISTENT => true));
  //       $conn->query('set names utf8;');
        return $dbconn;
    } catch (PDOException $e) {
        print "Couldn't connect to the postgreSQL tabase:".$e->getMessage();
    }
}

// function DBFConnect(){
//     try{
//         set_time_limit (0); 
//         $dsn = "Driver={Microsoft Visual FoxPro Driver};SourceType=dbf;sourcedb=c:\visd;BackgroundFetch=yes"; 
//         $login = ""; 
//         $pw = ""; 
//         $conn = odbc_connect($dsn,$login,$pw); 
//         return $conn;
//     }catch(PDOException $e){
//         print "Couldn't connect to the database:".$e->getMessage();
//     }
// }
?>

