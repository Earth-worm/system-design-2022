<?php
    class Task{
        public $name;
        public $time;
        function __construct($_name,$_time){
            $this->name = $_name;
            $this->time = $_time;
        }
    }
    class Day{
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
    class Schedule{
        public $firstWeek; //first week of day
        public $lastDay; //last day of month
        public $month = array(); //key(int) value(day)
        function __construct($_tarMonth){ //$_tarMonth:str y-m
            $this->firstWeek = intval(date("w",strtotime($_tarMonth." first day of this month")));
            $this->lastDay = intval(date("d",strtotime($_tarMonth." last day of this month")));
            for($i=1;$i<=$this->lastDay;$i++){
                if(($i+$this->firstWeek-1)%7==0 or ($i+$this->firstWeek-1)%7==6){
                    $newDay = new Day(true);
                    $this->month[$i] = $newDay;
                }
            }
        }
        function addTask($_day,$_name,$_isHoliday,$_time=NULL){
            if(!array_key_exists($_day,$this->month)){
                $newDay = new Day();
                $this->month[$_day] = $newDay;
            }
            $this->month[$_day]->addAttrs($_name,$_time,$_isHoliday);
        }
        function genHTML(){
            $rtn = "<table class='cal'>\n";
            foreach(array("日","月","火","水","木","金","土") as $week){
            }
            for($i=1;$i<=$this->lastDay;$i++){
                echo $i."日 ";
                if(array_key_exists($i,$this->month)){
                    var_dump($this->month[$i]);
                    echo "</br>";
                }else{
                    echo "</br>";
                }
            }
        }
    }

    class Hierarchy{
        public $tag = "";
        public $isClosed = false;
        public $isSlash = false;
        public $childs = array();
        public $attrs = NULL;
        public $innerText = NULL;
        function __construct($_tag,$_attrs=NULL,$_isClosed=true,$_innerText=NULL,$_isSlash=false){
            $this->tag = $_tag;
            $this->isClosed = $_isClosed;
            $this->isSlash = $_isSlash;
            $this->innerText = $_innerText;
            $this->attrs = $_attrs;
        }
        function appendChild($_child){
            array_push($this->childs,$_child);
        }
        function addTab($_strList){ //全部の行に水平tabを追加
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
                    return array("<".$this->tag." ".$this->attrs." >".$this->innerText."<".$this->tag.">\n");
                }else{
                    return array("<".$this->tag.">".$this->innerText."<".$this->tag.">\n");
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
    
    $before = date("d",strtotime($_tarMonth."-01 before this day"));
    echo $before."<br>";
    $schedule = new Schedule("2022-10");
?>