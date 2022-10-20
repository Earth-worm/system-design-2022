<?php
    use Ramsey\Uuid\Uuid;
    
    
    #アプリごとにurlを記載する関数
    #urls.phpと同じディレクトリに$_dirと同じ名前のフォルダを作成
    #->中にurls.phpを作成
    #->urls.phpに$appUrlsという名で仮想連想配列を作成(key:url,value:ファイル名)
    #static fileは#static/{$_appName}/{$fileName}に保存
    function include_app($_dir){
        global $urls;
        include "apps/".$_dir."/urls.php";
        foreach($appUrls as $url => $fileName){
            $urls["/".$_dir."/".$url] = "apps/".$_dir."/".$fileName;
        }
    }

    #htmlタグの階層構造からhtml文を生成するクラス
    #hierarchyaのappendChildメソッドを用いて下の階層にhierarchyインスタンスを配置。
    #最上位階層のhierarchyインスタンスのHTMLメソッドでhtmlを作成
    class Hierarchy{
        public $tag = "";
        public $isClosed = true;
        public $isSlash = false;
        public $childs = array();
        public $attrs = NULL;
        public $innerText = NULL;

        #$_tag:タグ(例:div),$_attrs:クラスやスタイルの追加(例:"class='blue'"),$_innerText:pタグなどの中の文字列
        #$_isColosed:imgタグなどの/で閉じないタグ(bool),$_isSlash:タグの前に/をつけるか(例</br>or<br>)
        function __construct($_tag,$_attrs=NULL,$_innerText=NULL,$_isClosed=true,$_isSlash=false){
            $this->tag = $_tag;
            $this->isClosed = $_isClosed;
            $this->isSlash = $_isSlash;
            $this->innerText = $_innerText;
            $this->attrs = $_attrs;
        }

        #自分の下にhierarchy院タンスを追加
        function appendChild($_child){
            array_push($this->childs,$_child);
        }

        #hierarchy内部で使う関数。HTMLで使用
        function _addTab($_strList){
            $rtn = array();
            foreach($_strList as $line){
                array_push($rtn,"\t".$line);
            }
            return $rtn;
        }

        #htmlを再帰的に生成
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
                $rtn = array_merge($rtn,$this->_addTab($child->_HTML()));
            }
            array_push($rtn,"</".$this->tag.">\n");
            return $rtn;
        }

        #html文を生成最上位hierarchyインスタンスで呼び出す。
        function HTML(){
            $rtn = "";
            foreach($this->_HTML() as $line){
                $rtn = $rtn.$line;
            }
            return $rtn;
        }
    }

    #一つのタスクのデータ構造
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

    #複数のタスクを持つ一日のデータ構造
    class Day{
        public $isHoliday = false;
        public $Attrs = array();

        function __construct($_isHoliday=false){
            $this->isHoliday = $_isHoliday;
        }
        
        #ある日にタスクを追加するメソッド
        function addAttrs($_name,$_time,$_isHoliday,$_id=NULL){
            $this->isHoliday = ($this->isHoliday or $_isHoliday);
            $newTask = new Task($_name,$_time,$_isHoliday,$_id);
            array_push($this->Attrs,$newTask);
        }

        #タスクを持っているか確認するメソッド
        function hasTasks(){
            return (count($this->Attrs)!=0);
        }
    }

    #複数のdayクラスを持つ一か月のデータ構造
    class Schedule{ //一か月のスケジュール
        public $firstWeek;
        public $lastDay;
        public $month = array();
        public $tarMonth;

        #インスタンス生成時に目的となる月を渡す 2022-01
        function __construct($_tarMonth){
            $this->tarMonth = $_tarMonth;
            $this->firstWeek = intval(date("w",strtotime($_tarMonth." first day of this month")));
            $this->lastDay = intval(date("d",strtotime($_tarMonth." last day of this month")));
            
            #その月の土日を追加
            for($i=1;$i<=$this->lastDay;$i++){
                if(($i+$this->firstWeek-1)%7==0 or ($i+$this->firstWeek-1)%7==6){
                    $newDay = new Day(true);
                    $this->month[$i] = $newDay;
                }
            }
        }

        #タスクを追加
        function addTask($_day,$_name,$_isHoliday,$_time=NULL,$_id=NULL){
            if(!array_key_exists($_day,$this->month)){
                $newDay = new Day();
                $this->month[$_day] = $newDay;
            }
            $this->month[$_day]->addAttrs($_name,$_time,$_isHoliday,$_id);
        }

        #カレンダーのhtml文を生成。
        function genHTML(){
            $before = intval(date("d",strtotime($this->tarMonth."-01 -1 day")));
            $lastWeek = intval(date("w",strtotime($this->tarMonth." last day of this month")));
            $par = new Hierarchy("div","class='container'");#最上位階層
            $head = new Hierarchy("div","class='row border-bottom text-center'"); #曜日が格納されるdivタグ

            #曜日divタグを生成し、$headの下に配置
            foreach(array("日","月","火","水","木","金","土") as $day){
                $child = new Hierarchy("div","class='col'",$day);
                $head->appendChild($child);
            }

            $par->appendChild($head); #$headを最上位の直下に配置
            $week = new Hierarchy("div","class='row week'");#一週間が格納されるdivタグ

            #カレンダー上の前の月を追加
            for($i=0;$i<$this->firstWeek;$i++){
                $child = new Hierarchy("div","class='col other border'");
                #$h1 = new Hierarchy("h1",NULL,$before+$i-$this->firstWeek+1);
                #$child->appendChild($h1);
                $week->appendChild($child);
            }

            #目標の月のすべての日を作成
            for($i=1;$i<=$this->lastDay;$i++){
                #対象の日にちに予定があれば、タスクの描画
                if(array_key_exists($i,$this->month)){
                    $holiday = $this->month[$i]->isHoliday;
                    $class = "day col border";

                    #表示するタスクが多ければスクロールをつける
                    if($this->month[$i]->hasTasks() and count($this->month[$i]->Attrs)>2){
                            $class = $class." scroll";
                    }
                    if($holiday){
                        $class = $class." bg-light";
                    }
                    $child = new Hierarchy("div","class='".$class."'");#一日を格納するdivタグ
                    $h = new Hierarchy("h5",NULL,$i);#曜日を格納するh5タグ
                    $child->appendChild($h);
                    #その日にタスクがあるか
                    if($this->month[$i]->hasTasks()){
                        $ul = new Hierarchy("ul","class='tasks'");
                        #タスクの数ループ
                        foreach($this->month[$i]->Attrs as $task){
                            #タスクの階層を整備
                            $a = new Hierarchy("a","href=/schedule/edittask?id=".$task->id);
                            $li = new Hierarchy("li",NULL,NULL);
                            $h = new Hierarchy("h5",NULL,$task->name);
                            $a->appendChild($h);
                            $li->appendChild($a);
                            $ul->appendChild($li);
                        }
                        #タスクをchild階層の下に配置
                        $child->appendChild($ul);
                    }
                }else{#タスクなし
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

            #カレンダー上の次の月の追加
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

    #slack apiにリクエストを送る関数 $url(apiのリクエスト先),$method(POST or GET),$data送信するデータ(辞書)
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

    #useridからuser情報を取得する関数。
    function getUserInfo($userId){
        $url = "https://slack.com/api/users.info";
        $method = "GET";
        $data = array(
            "user"=>$userId,
        );
        $url2 = "https://slack.com/api/users.info?user=".$userId;
        return json_decode(curlSlack($url,$method,$data));
    }

    #slack bot からユーザへメッセージの送信
    function sendMessage($ch,$message,$as_user=true){
        $data = array(
            "channel" => $ch,
            "text" => $message,
            "as_user" => $as_user
        );
        $url = "https://slack.com/api/chat.postMessage";
        return json_decode(curlSlack($url,"POST",$data));
    }

    #特定のチャンネルからメッセージを取得
    function getMessage($channel,$limit=1){
        $url = "https://slack.com/api/conversations.history";
        return json_decode(curlSlack($url,"GET",array("channel"=>$channel,"limit"=>$limit)));
    }
    
    #グループ内のすべてのユーザの情報を取得
    function getUserList(){
        $url = "https://slack.com/api/users.list";
        return json_decode(curlSlack($url,"GET"));
    }

    #emailからユーザの情報を取得
    function getIdByEmail($email){
        $url = "https://slack.com/api/users.lookupByEmail";
        return json_decode(curlSlack($url,"GET",array("email"=>$email)));
    }

    #ワンタイムurlのトークンを生成し、データベースに追加。
    function createToken($_db){
        $token = Uuid::uuid4();
        $rtn = $_db->query("insert into token(url) values('".$token."')");
        $delTime = time()+(15*60);
        file_put_contents("cron/tokenlist.txt",$delTime." ".$token."\n", FILE_APPEND);
        return $token;
    }
?>