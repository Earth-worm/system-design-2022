<?php
    //時間があれば内閣hpから休日のデータを読み込む（あどみん）
    class Hierarchy{ //タグの階層構造を管理、htmlを生成
        public $tag = "";
        public $isClosed = true;
        public $isSlash = false;
        public $childs = array();
        public $attrs = NULL;
        public $innerText = NULL;
        function __construct($_tag,$_attrs=NULL,$_innerText=NULL,$_isClosed=true,$_isSlash=false){
            $this->tag = $_tag;
            $this->isClosed = $_isClosed;
            $this->isSlash = $_isSlash;
            $this->innerText = $_innerText;
            $this->attrs = $_attrs;
        }
        function appendChild($_child){
            array_push($this->childs,$_child);
        }
        function addTab($_strList){
            $rtn = array();
            foreach($_strList as $line){
                array_push($rtn,"\t".$line);
            }
            return $rtn;
        }
        function _HTML(){
            $rtn;
            if(!$this->isClosed){
                if($this->isSlash){
                    return array("</".$this->tag.">\n");
                }else{
                    return array("<".$this->tag.">\n");
                }
            }
            if($this->innerText){
                if(isset($this->attrs)){
                    return array("<".$this->tag." ".$this->attrs.">".$this->innerText."</".$this->tag.">\n");
                }else{
                    return array("<".$this->tag.">".$this->innerText."</".$this->tag.">\n");
                }
            }
            $rtn;
            if(isset($this->attrs)){
                $rtn = array("<".$this->tag." ".$this->attrs." >\n");
            }else{
                $rtn = array("<".$this->tag.">\n");
            }
            foreach($this->childs as $child){
                $rtn = array_merge($rtn,$this->addTab($child->_HTML()));
            }
            array_push($rtn,"</".$this->tag.">\n");
            return $rtn;
        }
        function HTML(){
            $rtn = "";
            foreach($this->_HTML() as $line){
                $rtn = $rtn.$line;
            }
            return $rtn;
        }
    }
    class Task{
        public $name;
        public $time;
        function __construct($_name,$_time){
            $this->name = $_name;
            $this->time = $_time;
        }
    }
    class Day{ //一日のスケジュールのデータ構造
        public $isHoliday = false;
        public $Attrs = array();
        function addAttrs($_name,$_time,$_isHoliday){
            $this->isHoliday = $this->isHoliday or $_isHoliday;
            $newTask = new Task($_name,$_time);
            array_push($this->Attrs,$newTask);
        }
        function __construct($_isHoliday=false){
            $this->isHoliday = $_isHoliday;
        }
    }
    class Schedule{ //一か月のスケジュールのデータ構造
        public $firstWeek;
        public $lastDay;
        public $month = array();
        public $tarMonth;
        function __construct($_tarMonth){
            $this->tarMonth = $_tarMonth;
            $this->firstWeek = intval(date("w",strtotime($_tarMonth." first day of this month")));
            $this->lastDay = intval(date("d",strtotime($_tarMonth." last day of this month")));
            for($i=1;$i<=$this->lastDay;$i++){
                if(($i+$this->firstWeek-1)%7==0 or ($i+$this->firstWeek-1)%7==6){
                    $newDay = new Day(true);
                    $this->month[$i] = $newDay;
                }
            }
        }
        function addTask($_day,$_name,$_isHoliday,$_time=NULL){ //予定の追加
            if(!array_key_exists($_day,$this->month)){
                $newDay = new Day();
                $this->month[$_day] = $newDay;
            }
            $this->month[$_day]->addAttrs($_name,$_time,$_isHoliday);
        }
        function genHTML(){ //一か月のタスクからスケジュールのhtml文を生成
            $before = intval(date("d",strtotime($this->tarMonth."-01 -1 day")));
            $lastWeek = intval(date("w",strtotime($this->tarMonth." last day of this month")));
            $par = new Hierarchy("div","class='container'");
            $head = new Hierarchy("div","class='row'");
            foreach(array("日","月","火","水","木","金","土") as $day){
                $child = new Hierarchy("div","class='col'",$day);
                $head->appendChild($child);
            }
            $par->appendChild($head);
            $week = new Hierarchy("div","class='row'");
            for($i=0;$i<$this->firstWeek;$i++){
                $child = new Hierarchy("div","class='col'",$before+$i-$this->firstWeek+1);
                $week->appendChild($child);
            }
            for($i=1;$i<=$this->lastDay;$i++){
                $holiday = false;
                if(array_key_exists($i,$this->month)){
                    $holiday = $this->month[$i]->isHoliday;
                    $child = new Hierarchy("div","class = 'col holiday'",$i);
                }else{
                    $child = new Hierarchy("div","class='col'",$i);
                }
                $week->appendChild($child);
                if(($i+$this->firstWeek-1)%7==6){
                    $par->appendChild($week);
                    $week = new Hierarchy("div","class='row'");
                }
            }
            for($i=0;$i<6-$lastWeek;$i++){
                $child = new Hierarchy("div","class='col'",$i+1);
                $week->appendChild($child);
            }
            $par->appendChild($week);
            return $par->HTML();
        }
    }
    
    session_start();
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);

    $month = date("Y-m");
    $isAuthenticated = isset($_SESSION["id"]);
    $schedule = new Schedule($month);
    $html =  $schedule->genHTML();
    $db = new Sqlite3("db.sqlite3");
    $rtn = $db->query("select * from task where date like '".$month."%'");
    while($row = $rtn->fetchArray()){
        var_dump($row);
        echo "<br>";
    }
    $context = array(
        "isAuthenticated"=>$isAuthenticated,
        "html"=>$html,
    );
    echo $twig->render('schedule/task_list.html',$context);
?>