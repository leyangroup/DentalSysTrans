<?php
    // 日期處理

    //西元年轉換成國民年日期且沒有/
    function ROCdate($edate){
        $DT=explode('-', $edate);
        $y=$DT[0]-1911;
        $rocDT=$y.$DT[1].$DT[2];
        return $rocDT;
    }

    //民國年轉西元年
    function WestDT($rocDT){
        if (strlen(trim($rocDT))==7){
            $yy=substr($rocDT,0,3)+1911;
            $DT=$yy.'-'.substr($rocDT,3,2).'-'.substr($rocDT,-2);
        }else{
            $DT='';
        }
        return $DT;
    }

    function ROCdateWithSlash($edate){
        if (strlen($edate)==7){
            $rocDT=substr($edate,0,3).'/'.substr($edate,3,2).'/'.substr($edate,-2);
        }else{
            $DT=explode('-', $edate);
            $y=$DT[0]-1911;
            $rocDT=$y."/".$DT[1]."/".$DT[2];
        }
        return $rocDT;
    }

    
?>