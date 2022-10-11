<?php
    require_once("tools.php");
    $errors = array();
    $name = NULL;
    $date = NULL;
    $time = NULL;
    $holiday = NULL;
    if(strcmp($_POST["text"],"")!=0){
        $text = $_POST["text"];
        $text = str_replace(array("\r\n","\r"),"\n",$text);
        $arr = explode("\n",$text);
        $count = count($arr);
        var_dump($arr);
        if($count < 2){
            array_push($errors,"日付が設定されていません。");
        }else{
            $name = $arr[0];
            $date = $arr[1];
            $dateArr = explode("-",$date);
            if(count($dateArr)!=3 or intval($dateArr[1])>12 or intval($dateArr[2])>31){
                var_dump($dateArr);
                array_push($errors,"日付入力に誤りがあります。");
            }

        }
        if($count > 2 and $count < 5){
            $holiday = (strcmp($arr[2],"T") == 0);
            if(!$holiday){
                $holiday=(strcmp($arr[2],"F")==0);
                if($holiday){
                    $holiday = false;
                }else{
                    array_push($errors,"休日入力に誤りがあります。");
                }
            }
        }
        if($count == 4){
            $time = $arr[3];
            $timeArr = explode(":",$time);
            if(count($timeArr)!=2 or intval($timeArr[0])>23 or intval($timeArr[1])>59){
                array_push($errors,"時間入力に誤りがあります。");
                $time = NULL;
            }
        }
    }else{
        array_push($errors,"タスクが設定されていません。");
    }
    if(count($errors)==0){
        sendMessage($_POST["user_id"],$name."を作成しました。");
        $db = new Sqlite3("db.sqlite3");
        $values="'".$name."','".$date."'";
        if($holiday){
            $values = $values.",1";
        }else{
            $values = $values.",0";
        }
        if($time){
            $values = $values.",'".$time."'";
        }else{
            $values = $values.",NULL";
        }
        $values = $values.",'".$_POST["user_id"]."'";
        echo $values;
        $db->query("insert into task(name,date,holiday,time,user_id) values(".$values.")");
    }else{
        $rtn="";
        foreach($errors as $error){
            $rtn = $rtn.$error."\n";
        }
        sendMessage($_POST["user_id"],$rtn);
    }
?>