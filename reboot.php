 
<?php
include('include/input.php');
include('include/common.php');
    if(CheckAccessToModul(46,$_SESSION['modulaccess'])==false)
    {
     echo '<center><span class="text">Выполнение не возможно.<br> Нет прав доступа</span><br>';
     exit();
    }
    echo '<center><span class="text">Выполняется перезагрузка сервера СКУД.</span><br>';
    shell_exec('shutdown -r');//windows
    //shell_exec('/home/odroid/php_root_reboot');//linux   change home dir if u need
?>