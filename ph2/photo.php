<?php
    include ("../include/input.php");
    require_once('../include/hua.php');
    $action=$_POST['act'];
    //$action="getturn"; 
    if($action=='getturn'){
        $q='select num,name from base_turn where delete=\'0\' ';
        $result=pg_query($q);
        $response = '{"0":"'.'Не показывать'.'"';
        while($r=pg_fetch_array($result))
        {
           $response .= ',';
           $response .= '"'.$r['num'].'":"'.str_replace('"',"",$r['name']).'"';
        }
        $response .= '}';
        echo $response;
    }
    if($action=='getconf'){
        $q='select * from photo_config where user_id='.$_SESSION['iduser'];
        $result=pg_fetch_array(pg_query($q));
        $response = '{';
        
        foreach($result as $key => $value){
            if ($key === 'user_id' || is_numeric($key)) continue;
            $str1 = 'ch_';
            strpos($key, $str1) === 0 ? $response .= '"'.$key.'":'.$value.',' : $response .= '"'.$key.'":"'.$value.'",';
        };
        
        $response .= '"0":"0"}';
              
        echo $response;
    }
    if($action=='save_config'){
        $qq = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
        $qq .= 'update PHOTO_CONFIG set';
        foreach($_POST as $key => $value){
            if ($key === 'act') continue;
            if ($value === 'false') $value = 0;
            if ($value === 'true') $value = 1;
            $qq .= ' '. $key .'=\''.$value.'\' , ';
        };
        $qq = rtrim($qq,', ');
        $qq .= ' where user_id ='.$_SESSION['iduser'];
        $result=pg_query($qq);

        foreach($_POST as $key => $value){
            if ($key === 'act') continue;
            setcookie("$key", str_replace(' ', '_', $value), time()+3600*24*30*12*5);
        };

        echo 'true';
    }
    if($action=='firstgetconf'){

       $q='select * from photo_config where user_id='.$_SESSION['iduser'];
        $result=pg_fetch_array(pg_query($q));

        foreach($result as $key => $value){
            if ($key === 'user_id' || is_numeric($key)) continue;
            setcookie("$key", str_replace(' ', '_', $value), time()+3600*24*30*12*5);
        };

        echo 'true';
    }
    if($action=='wait_ajax'){

       sleep(2);

        echo 'true';
    }


?>