 
<?php
include('include/input.php');
include('include/common.php');
    if(CheckAccessToModul(46,$_SESSION['modulaccess'])==false)
    {
     echo '{"res":"0"}';
     exit();
    }
    
    $regex_str = '/\d{4}[-]\d{2}[-]\d{2}\s\d{2}[:]\d{2}[:]\d{2}/';//шаблон строки для полученного события
    if ($_REQUEST['obj'] == 'time' && preg_match($regex_str, $_REQUEST['time']) && strlen($_REQUEST['time'])<=20){
        //echo '<center><span class="text">Команда изменения времени отправлена.</span><br>';
        //shell_exec('shutdown -r');//windows
        shell_exec('/home/odroid/php_root_set_time '.$_REQUEST['time']);//linux   change home dir if u need
        echo '{"res":"1"}';
    }
?>