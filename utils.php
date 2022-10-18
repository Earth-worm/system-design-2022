<?php
    use Ramsey\Uuid\Uuid;
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
        public $date;
        public $isHoliday;
        public $id;
        function __construct($_name,$_time,$_isHoliday=NULL,$_id=NULL,$_date=NULL){
            $this->name = $_name;
            $this->time = $_time;
            $this->isHoliday = $_isHoliday;
            $this->id = $_id;
            $this->date = $_date;
        }
    }

    class Day{
        public $isHoliday = false;
        public $Attrs = array();

        function __construct($_isHoliday=false){
            $this->isHoliday = $_isHoliday;
        }
        
        function addAttrs($_name,$_time,$_isHoliday,$_id=NULL){
            $this->isHoliday = ($this->isHoliday or $_isHoliday);
            $newTask = new Task($_name,$_time,$_isHoliday,$_id);
            array_push($this->Attrs,$newTask);
        }

        function hasTasks(){
            return (count($this->Attrs)!=0);
        }
    }

    class Schedule{ //一か月のスケジュール
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

        function addTask($_day,$_name,$_isHoliday,$_time=NULL,$_id=NULL){ //予定の追加
            if(!array_key_exists($_day,$this->month)){
                $newDay = new Day();
                $this->month[$_day] = $newDay;
            }
            $this->month[$_day]->addAttrs($_name,$_time,$_isHoliday,$_id);
        }

        function genHTML(){ //一か月のタスクからスケジュールのhtml文を生成
            $before = intval(date("d",strtotime($this->tarMonth."-01 -1 day")));
            $lastWeek = intval(date("w",strtotime($this->tarMonth." last day of this month")));
            $par = new Hierarchy("div","class='container'");
            $head = new Hierarchy("div","class='row border-bottom text-center'");
            foreach(array("日","月","火","水","木","金","土") as $day){
                $child = new Hierarchy("div","class='col'",$day);
                $head->appendChild($child);
            }
            $par->appendChild($head);
            $week = new Hierarchy("div","class='row week'");
            for($i=0;$i<$this->firstWeek;$i++){
                $child = new Hierarchy("div","class='col other border'");
                #$h1 = new Hierarchy("h1",NULL,$before+$i-$this->firstWeek+1);
                #$child->appendChild($h1);
                $week->appendChild($child);
            }
            for($i=1;$i<=$this->lastDay;$i++){
                if(array_key_exists($i,$this->month)){
                    $holiday = $this->month[$i]->isHoliday;
                    $class = "day col border";
                    if($this->month[$i]->hasTasks() and count($this->month[$i]->Attrs)>2){
                            $class = $class." scroll";
                    }
                    if($holiday){
                        $class = $class." bg-light";
                    }
                    $child = new Hierarchy("div","class='".$class."'");
                    $h = new Hierarchy("h5",NULL,$i);
                    $child->appendChild($h);
                    if($this->month[$i]->hasTasks()){
                        $ul = new Hierarchy("ul","class='tasks'");
                        foreach($this->month[$i]->Attrs as $task){
                            $a = new Hierarchy("a","href=/schedule/edittask?id=".$task->id);
                            $li = new Hierarchy("li",NULL,NULL);
                            $h = new Hierarchy("h5",NULL,$task->name);
                            $a->appendChild($h);
                            $li->appendChild($a);
                            $ul->appendChild($li);
                        }
                        $child->appendChild($ul);
                    }
                }else{
                    $child = new Hierarchy("div","class='day col border'");
                    $h = new Hierarchy("h5",NULL,$i);
                    $child->appendChild($h);
                }
                $week->appendChild($child);
                if(($i+$this->firstWeek-1)%7==6){
                    $par->appendChild($week);
                    $week = new Hierarchy("div","class='week row'");
                }
            }
            for($i=0;$i<6-$lastWeek;$i++){
                $child = new Hierarchy("div","class='day col other'");
                #$h1 = new Hierarchy("h1",NULL,$i+1);
                #$child->appendCHild($h1);
                $week->appendChild($child);
            }
            $par->appendChild($week);
            return $par->HTML();
        }
    }

    function curlSlack($url,$method,$data=NULL){
        $headers = [
            'Authorization: Bearer '.$_ENV["SlackToken"],
            'Content-Type: application/json;charset=utf-8'
        ];
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
        ];
        if($method=="GET"){
            if(isset($data)){
                $url = $url."?".http_build_query($data);
            }
        }else if($mthod="POST"){
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data) ;
        }
        $options[CURLOPT_URL]=$url;
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
        
    }

    function getUserInfo($userId){
        $url = "https://slack.com/api/users.info";
        $method = "GET";
        $data = array(
            "user"=>$userId,
        );
        $url2 = "https://slack.com/api/users.info?user=".$userId;
        return json_decode(curlSlack($url,$method,$data));
    }

    function sendMessage($ch,$message,$as_user=true){
        $data = array(
            "channel" => $ch,
            "text" => $message,
            "as_user" => $as_user
        );
        $url = "https://slack.com/api/chat.postMessage";
        return json_decode(curlSlack($url,"POST",$data));
    }

    function getMessage($channel,$limit=1){
        $url = "https://slack.com/api/conversations.history";
        return json_decode(curlSlack($url,"GET",array("channel"=>$channel,"limit"=>$limit)));
    }
    
    function getUserList(){
        $url = "https://slack.com/api/users.list";
        return json_decode(curlSlack($url,"GET"));
    }

    function getIdByEmail($email){
        $url = "https://slack.com/api/users.lookupByEmail";
        return json_decode(curlSlack($url,"GET",array("email"=>$email)));
    }
    function createToken($_db){
        $token = Uuid::uuid4();
        $rtn = $_db->query("insert into token(url) values('".$token."')");
        $delTime = time()+(15*60);
        file_put_contents("cron/tokenlist.txt",$delTime." ".$token."\n", FILE_APPEND);
        return $token;
    }
?>