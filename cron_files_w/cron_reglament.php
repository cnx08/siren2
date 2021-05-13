<?php
require_once(realpath(dirname(__FILE__) . "/../include/config.php"));
        if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
			 echo  die(pg_last_error());
            echo "Невозможно подключится к базе сервера ";
         
        }
        $q = 'select exec_log_conf();';
        if(@pg_query($q)){echo 'exec_log_conf завершен';}
        else{echo 'Ошибка выполнения'.pg_last_error();}
        
        $q = 'vacuum;';
        if(@pg_query($q)){echo 'vacuum завершен';}
        else{echo 'Ошибка выполнения'.pg_last_error();}

        $q = 'REINDEX DATABASE askd;';
        if(@pg_query($q)){echo 'REINDEX завершен';}
        else{echo 'Ошибка выполнения'.pg_last_error();}

        $q = 'cluster;';
        if(@pg_query($q)){echo 'cluster завершен';}
        else{echo 'Ошибка выполнения'.pg_last_error();}
?>