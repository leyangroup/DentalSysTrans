<?php
include_once "../include/db.php";
include_once "../include/DTFC.php";

set_time_limit(0);
ini_set("memory_limit", "1024M");

$oldImgFolder = trim($_GET['imagePath'], '\\') . '\\';
$customerFolder = 'D:\\xampp\\htdocs\\his\\public\\Ledocs\\customer\\';
$newImgFolder = $customerFolder. '{cussn}\\';
$newImgSubFolder = 'records\\';

// 建立customer
if (!is_dir($customerFolder)) {
    mkdir($customerFolder,0777);
}

$leqingCon = MariaDBConnect();

// dat connection
//$oldCon = new PDO('mysql:host=localhost:3306;dbname=trans', 'root', '', [PDO::ATTR_PERSISTENT => true]);
//$oldTable = 'img';

$oldCon = new PDO("odbc:Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=".$_GET['path']);
$oldTable = 'img.dat';

$oldCon->query('set names utf8;');
$oldCon->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

$sql = "SELECT `KEYWORDS`, `MEDICAL_NO`, `FILENAME` FROM {$oldTable}";
$sth = $oldCon->prepare($sql);
$sth->execute();
$result = $sth->fetchAll();

$execCount = 0;
foreach ($result as $key => $value) {
    $sql = '';
    $oldPath = '';
    $newFileName = strtolower($value['FILENAME']);
    $newFolder = '';

    // 獲取registration pk
    // 獲取customer pk
    $sql = "SELECT `regsn`, `cussn` FROM `registration` WHERE binary `stdate` = '{$value['KEYWORDS']}'";
    $ex = $leqingCon->prepare($sql);
    $ex->execute();
    $registration = $ex->fetch();

    // 獲取old圖檔路徑 確定有檔案
    $oldPath = $oldImgFolder . substr($value['FILENAME'], -8, 2) . '\\' . $value['FILENAME'];

    if ($registration && file_exists($oldPath)) {

        // 資料是否重複
        $sql = "SELECT `id` FROM `image_records` WHERE `regsn` = '{$registration['regsn']}' AND `path` = '{$newFileName}'";
        $ex = $leqingCon->prepare($sql);
        $ex->execute();
        $ir = $ex->fetch();

        if ($ir === false) {
            // 獲取新路徑
            $newFolder = str_replace("{cussn}", $registration['cussn'], $newImgFolder);

            // insert to image_record
            $now = date("Y-m-d H:i:s", time());
            $sql = "INSERT INTO image_records(regsn, path, created_at) VALUES('{$registration['regsn']}','{$newFileName}', '{$now}')";
            $leqingCon->exec($sql);

            if (!is_dir($newFolder)) {
                mkdir($newFolder,0777);
            }

            if (!is_dir($newFolder .= $newImgSubFolder)) {
                mkdir($newFolder,0777);
            }

            // copy image
            copy($oldPath, $newFolder. $value['FILENAME']);
            $execCount++;
            echo "$sql<br>";
        }
    }

    $registration = null;
}
echo "<h1>病歷圖檔 轉換完成 共{$execCount}筆</h1>";
?>

