<?php
require_once(realpath(dirname(__FILE__) . "/../include/config.php"));
        if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
			 echo  die(pg_last_error());
            echo "Невозможно подключится к базе сервера ";
         
        }
        $q = 'select pr_trully_t_pass()';
          if(@pg_query($q))
          {
            echo 'Проверка завершена';
            
          }
          else
          {
           echo 'Ошибка выполнения pr_trully_t_pass';
          }

?>