<?php
header("Content-Type: text/html; charset=utf-8");
require_once("config.php");
require_once("hua.php");
session_start();

if (isset($_POST['login']) && $_POST['login']!=='' && isset($_POST['passwd']) && $_POST['passwd']!=='') {
    if (!authorization($host,$SMDBName,$SMDBUser,$SMDBPass))
    {
        Header("Location:index.php");
    }
}
if (!check())
{
     Header("Location:index.php");
}

 function generateCode($length) 
{ 
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789"; 
    $code = ""; 
    $clen = strlen($chars) - 1;   
    while (strlen($code) < $length) { 
      $code .= $chars[mt_rand(0,$clen)];   
    } 
    return $code; 
  }

# Проверка авторизованности
function check() {
    if (isset($_SESSION['iduser'])){
        
        global $host,$SMDBName,$SMDBUser,$SMDBPass;
        if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
            echo  die(pg_last_error());
            echo "Невозможно подключиться к базе сервера ";
        }
        return true;
    }
    else 
    { //~ проверяем наличие кук
        if (isset($_COOKIE['id_user']) and isset($_COOKIE['code_user']))
        {//~ куки есть - сверяем с таблицей сессий
            if (is_numeric($_COOKIE['id_user']))
            {
                global $host,$SMDBName,$SMDBUser,$SMDBPass;
                if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
                    echo  die(pg_last_error());
                    echo "Невозможно подключиться к базе сервера ";
                }
                $id_user=$_COOKIE['id_user'];
                $code_user=$_COOKIE['code_user'];
                $q = 'SELECT us.*,h.value as user_agent_sess FROM user_session us join x_hua h on h.id = us.hua WHERE us.user_id='.$id_user;
                $result = pg_query($q);
                $r = pg_fetch_array($result);
                if ($r['code_sess']==$code_user && $r['user_agent_sess']==add_rubish($_SERVER['HTTP_USER_AGENT'])) 
                { //~ Данные верны
                    $q = 'select * from BASE_W_S_MODULS ('.$id_user.')';
                    $result = pg_query($q);
                    while ($r = pg_fetch_array($result)) {
                        if ($r['access'] == 1)
                            $_SESSION['modulaccess'][] = $r['id'];
                    }
                    $_SESSION['iduser']=$id_user;
                    //~ обновляем куки
                    setcookie("id_user", $_SESSION['iduser'], time()+3600*24*14,'/');
                    setcookie("code_user", $code_user, time()+3600*24*14,'/');
                    return true;
                } else return false; //~ данные в таблице сессий не совпадают с куками
            } else return false;//id_user не число
        } else return false; //~ нет куков
    }
}

 ###
  # Авторизация
function authorization() {
    //если логин содержит что-то помимо [a-zA-Z0-9] - то это в файл лога, потом поглядим)
    if (preg_match('/[^A-Za-z0-9]/', $_POST['login']))
    {
        //удалим разросшийся файл
        if(filesize("login.log")>100*1024*1024)//100 MБ
        {
            unlink("login.log");
        }
        $stroka = date("d.m.Y H:i:s")."; ".$_SERVER['REMOTE_ADDR']."; ".$_POST['login']."; ".$_POST['passwd']."; ".$_SERVER['HTTP_USER_AGENT'];
        file_put_contents("login.log","$stroka\r\n",FILE_APPEND);
        
        return false;
    }
    else{
        global $host,$SMDBName,$SMDBUser,$SMDBPass;
        if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
            echo  die(pg_last_error());
            echo "Невозможно подключиться к базе сервера ";
        }
        $login = $_POST['login'];
        $passwd = $_POST['passwd'];
        $q = 'select * from  BASE_W_LOGIN(\''.$login.'\',\''.$passwd.'\');';
        $result = pg_query($q);
        $r = pg_fetch_array($result);
        $userid = $r['out_id'];

        if ($userid >= 0) {
            //пишем в лог, что нормально залогинились
            $q = 'select sys_write_login_log(\''.$login.'\',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\',1)';
            pg_query($q);
            
            //~ добавляем/обновляем запись в таблице сессий и ставим куку
            $r_code = generateCode(15);
            $q = 'select BASE_W_SESSION_UPDATE('.$userid.', \''.$r_code.'\', \''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\', \''.$_SERVER['REMOTE_ADDR'].'\');';
            pg_query($q);
            $q = 'select * from BASE_W_S_MODULS ('.$userid.')';
            $result = pg_query($q);
            while ($r = pg_fetch_array($result)) {
                if (intval($r['access']) === 1){
                    $_SESSION['modulaccess'][] = $r['id'];
                }
            }
            $_SESSION['iduser']=$userid;

            //~ ставим куки на 2 недели
            setcookie("id_user", $_SESSION['iduser'], time()+3600*24*14,'/');
            setcookie("code_user", $r_code, time()+3600*24*14,'/');
            return true;
        } else {
          //~ пользователь не найден в бд, или пароль не соответствует введенному
             //пишем в лог, что не залогинились, если мы уже 50 раз сегодня шлём взломщика нафик, то на 51-й бан
            $q = 'select sys_write_login_log(\''.$login.'\',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\',0)';
            $result = pg_query($q);
            while ($r = pg_fetch_array($result)) {
                if ($r['0'] == -1){
                    $deny_str = 'deny from '.$_SERVER['REMOTE_ADDR'];
//                    file_put_contents(".htaccess","\r\n$deny_str\r\n",FILE_APPEND); 
                }
            }
          return false;
        }
    }
}
  


?>
