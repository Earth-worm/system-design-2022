<?php
    require_once("tools.php");
    #sendMessage("@mycatiscute01082","test dayo");
    $rtn = getIdByEmail("mimizu0108@outlook.jp");
    var_dump($rtn);
    echo $rtn->user->id;
    if($rtn->ok){
        echo "yes";
    }else{
        echo "no";
    }
    ?>