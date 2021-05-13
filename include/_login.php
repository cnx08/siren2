<?php
session_start();

require_once('hua.php');
require_once('_initialize.php');

if (!check1())
{
     Header("Location:index.php");
}
# Проверка авторизованности
function check1() {
    if (isset($_SESSION['iduser'])) return true;
    else 
    { //~ проверяем наличие кук
        if (isset($_COOKIE['id_user']) and isset($_COOKIE['code_user']))
        {//~ куки есть - сверяем с таблицей сессий
            $id_user=$_COOKIE['id_user'];
            $code_user=$_COOKIE['code_user'];
            $q = 'SELECT * FROM user_session WHERE user_id='.$id_user;
            $result = pg_query($q);
            $r = pg_fetch_array($result);
            if ($r['code_sess']==$code_user && $r['user_agent_sess']==add_rubish($_SERVER['HTTP_USER_AGENT'])) 
            { //~ Данные верны
                $q = 'select * from BASE_W_S_MODULS(' . $id_user.')';
                $result = pg_query($q);
                while ($r = pg_fetch_array($result)) {
                    if ($r['access'] == 1)
                        $_SESSION['modulaccess'][] = $r['id'];
                }
                $_SESSION['iduser']=$id_user;
                //~ обновляем куки
                setcookie("id_user", $_SESSION['iduser'], time()+3600*24*34);
                setcookie("code_user", $code_user, time()+3600*24*34);
                return true;
            } else return false; //~ данные в таблице сессий не совпадают с куками
        } else return false; //~ нет куков
    }
}

?>