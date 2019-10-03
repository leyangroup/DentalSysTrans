ALTER TABLE `customer` 
CHANGE `times` `times` SMALLINT(3) NOT NULL DEFAULT '0' COMMENT '就醫次數', 
CHANGE `tthamt` `tthamt` FLOAT NOT NULL DEFAULT '0' COMMENT '保留欄-累積完成金額', 
CHANGE `tthpre` `tthpre` FLOAT NOT NULL DEFAULT '0' COMMENT '保留欄-假牙預付金額',
CHANGE `linamt` `linamt` FLOAT NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `linpre` `linpre` INT(5) NOT NULL DEFAULT '0' COMMENT '健保欠章押金', 
CHANGE `tthok` `tthok` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `cming` `cming` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `printok` `printok` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `printok1` `printok1` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `isthumb` `isthumb` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `redpp` `redpp` INT(4) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `hjisyn` `hjisyn` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `redpc` `redpc` INT(4) NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `depdebt` `depdebt` FLOAT NOT NULL DEFAULT '0' COMMENT '保留欄', 
CHANGE `btrace` `btrace` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '追蹤碼', 
CHANGE `soproid` `soproid` INT(4) NOT NULL DEFAULT '0' COMMENT '保留欄-X光系統SOPRO用', 
CHANGE `isuse` `isuse` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄-CT 電腦斷層用', 
CHANGE `binlang` `binlang` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄-嚼檳榔', 
CHANGE `cigarette` `cigarette` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄-抽菸', 
CHANGE `tran_to` `tran_to` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄-需轉介', 
CHANGE `is_disc` `is_disc` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否優待', 
CHANGE `cusowing` `cusowing` INT(8) NOT NULL DEFAULT '0' COMMENT '欠款';


ALTER TABLE `customer` 
CHANGE `wine` `wine` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '保留欄-喝酒', 
CHANGE `chainCsn` `chainCsn` INT(8) NOT NULL DEFAULT '0' COMMENT '連鎖院所流水號', 
CHANGE `apm_s1` `apm_s1` INT(2) NOT NULL DEFAULT '0' COMMENT '約診爽約次數', 
CHANGE `apm_s2` `apm_s2` INT(2) NOT NULL DEFAULT '0' COMMENT '看診遲到次數', 
CHANGE `apm_s3` `apm_s3` INT(2) NOT NULL DEFAULT '0' COMMENT '約診取消次數', 
CHANGE `is_receiveMsg` `is_receiveMsg` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否願意接受簡訊通知', 
CHANGE `sopro_id` `sopro_id` INT(10) NOT NULL DEFAULT '0' COMMENT 'SOPRO ID', 
CHANGE `nativetwn` `nativetwn` VARCHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '原住民', 
CHANGE `icstatus` `icstatus` VARCHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT 'IC卡身份註記';

ALTER TABLE `disc_list` CHANGE `discid` `discid` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '優免身分代號';


ALTER TABLE `treatment` 
CHANGE `fee` `fee` FLOAT NOT NULL DEFAULT '0' COMMENT '自費金額', 
CHANGE `is_oper` `is_oper` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 復形 ', 
CHANGE `is_endo` `is_endo` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 根管 ', 
CHANGE `is_oral` `is_oral` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 外科 ', 
CHANGE `is_pros` `is_pros` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 補綴 ', 
CHANGE `is_peri` `is_peri` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 牙周 ', 
CHANGE `is_orth` `is_orth` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 矯正 ', 
CHANGE `is_xray` `is_xray` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 X光 ', 
CHANGE `is_pedo` `is_pedo` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '牙科分類 兒童 ', 
CHANGE `fee_unit` `fee_unit` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '計費單位 ', 
CHANGE `is_tr` `is_tr` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '療程 ', 
CHANGE `period` `period` INT(5) NOT NULL DEFAULT '0' COMMENT '處置間隔天數 ', 
CHANGE `times` `times` INT(8) NOT NULL DEFAULT '0' COMMENT '計算使用次數', 
CHANGE `is_chknum` `is_chknum` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '檢查醫令顆數 ', 
CHANGE `is_material` `is_material` VARCHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0', 
CHANGE `is_rcpP` `is_rcpP` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '自費收據列印項 ';

ALTER TABLE `treatment` 
CHANGE `padd_percent` `padd_percent` FLOAT(5,2) NOT NULL DEFAULT '1.00' COMMENT '支付成數', 
CHANGE `radd_percent` `radd_percent` FLOAT(5,2) NOT NULL DEFAULT '1.30' COMMENT '轉診加乘 ';