<?php
require_once(realpath(dirname(__FILE__) . "/../include/config.php"));
        if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
			 echo  die(pg_last_error());
            echo "Невозможно подключится к базе сервера ";
         
        }
        //тут мы будем запускать процедуру, проверяющую все ли события на месте и создающую команды на загрузку в таблице commands_to_unit 
        
		if(@pg_query('truncate table temp_user_id_ip_info; select pr_init_event_load(0);'))
          {
            echo 'pr_init_event_load завершен';
            
          }
          else
          {
           echo 'Ошибка выполнения';
          }
        

?>