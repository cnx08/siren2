<?php
require_once(realpath(dirname(__FILE__) . "/../include/config.php"));
        if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
			 echo  die(pg_last_error());
            echo "Невозможно подключится к базе сервера ";
         
        }

		if(@pg_query('select nightly_check_t_pass();'))
          {
            echo 'nightly_check_t_pass() завершен';
            
          }
          else
          {
           echo 'Ошибка выполнения';
          }
        if(@pg_query('select add_daily_events();')){echo 'add_daily_events завершен';}
        else{echo 'Ошибка выполнения'.pg_last_error();}

?>