<?php
    $week = array('日','月','火','水','木','金','土');
    $now_month = date("Y年n月"); //表示する年月
    $start_date = date('Y-m-01'); //開始の年月日
    $end_date = date("Y-m-t"); //終了の年月日
    $start_week = date("w",strtotime($start_date)); //開始の曜日の数字
    $end_week = 6 - date("w",strtotime($end_date)); //終了の曜日の数字
     
    echo '<table class="cal">';
    //該当月の年月表示
    echo '<tr>';
    echo '<td colspan="7" class="center">'.$now_month.'</td>';
    echo '</tr>';
     
    //曜日の表示 日～土
    echo '<tr>';
    foreach($week as $key => $youbi){
        if($key == 0){ //日曜日
            echo '<th class="sun">'.$youbi.'</th>';
        }else if($key == 6){ //土曜日
            echo '<th class="sat">'.$youbi.'</th>';
        }else{ //平日
            echo '<th>'.$youbi.'</th>';
        }	
    }
    echo '</tr>';
     
    //日付表示部分ここから
    echo '<tr>';
    //開始曜日まで日付を進める
    for($i=0; $i<$start_week; $i++){
        echo '<td></td>';
    }
     
    //1日～月末までの日付繰り返し
    for($i=1; $i<=date("t"); $i++){
        $set_date = date("Y-m",strtotime($start_date)).'-'.sprintf("%02d",$i);
        $week_date = date("w", strtotime($set_date));
        //土日で色を変える
        if($week_date == 0){
            //日曜日
            echo '<td class="sun">'.$i.'</td>';
        }else if($week_date == 6){
            //土曜日
            echo '<td class="sat">'.$i.'</td>';
        }else{
            //平日
            echo '<td>'.$i.'</td>';
        }	
        if($week_date == 6){
            echo '</tr>';
            echo '<tr>';
        }
    }
    //末日の余りを空白で埋める
    for($i=0; $i<$end_week; $i++){
        echo '<td></td>';
    }
     
    echo '</tr>';
    echo '</table>';
?>
class Schedule{
    $firstaWeek;
    $month = array();
    class Attr{
        $name; //str
        $time; //str 00:00
        $isHoliday; //bool
        function __constract($_name,$_time,$_isHoliday){
            $this->name = $_name;
            $this->time = $_time;
            $this->isHoliday = $_isHoliday;
        }
    }
    function __constract($_tarMonth){ //$_tarMonth:str y-m
        $firstWeek = intval(date("w",strtotime($_tarMonth." first day of this month")));
        $lastDay = intval(date("d"))
    }
    function addAttrs($_day,$_name,$_time,$_isHoliday){
        $attr = new Attr($_name,$_time,$_isHoliday);
        if(!array_key_exitst($_day,$this->month)){
            $this->month[$_day] = array();
        }
        array_push($this->month,$attr);
    }
}