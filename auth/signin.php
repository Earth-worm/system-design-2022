<?php
    require_once '../vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader('view');
    $twig = new \Twig\Environment($loader);
    use Ramsey\Uuid\Uuid;

    $errors = array();
    $isSuccess = false;
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(empty($_POST["name"])){
            array_push($errors,"名前が入力されていません。");
        }
        if(empty($_POST["email"])){
            array_push($errors,"メールが入力されていません。");
        }
        if(empty($_POST["password"])){
            array_push($errors,"パスワードが入力されていません。");
        }
        $db = new Sqlite3("db.sqlite3");
        $rtn = $db->query("select * from user where email='".$_POST["email"]."'");
        $tmp = $rtn->fetchArray();
        if($tmp){
            array_push($errors,"このメールアドレスは既に使われています。");
        }
        if(empty($errors)){
            $uuid = Uuid::uuid4();
            $name = $_POST["name"];
            $email = $_POST["email"];
            $hash_pass = hash("sha256",$_POST["password"]);
            $stmt = $db->prepare("insert into user values(:id,:name,:email,:password)");
            $stmt->bindValue(':id',$uuid,SQLITE3_TEXT);
            $stmt->bindValue(':name',$name,SQLITE3_TEXT);
            $stmt->bindValue(':email',$email,SQLITE3_TEXT);
            $stmt->bindValue(':password',$hash_pass,SQLITE3_TEXT);
            $res = $stmt->execute();
            if(!$res){
                array_push($errors,"サインインに失敗しました。");
            }else{
                $isSuccess = $name;
            }
        }
    }
    $context = array(
        "errors"=>$errors,
        "isSuccess"=>$isSuccess,
    );
    var_dump($_POST);
    echo $twig->render('auth/signin.html', $context);
?>