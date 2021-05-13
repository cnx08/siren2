#!/bin/bash
<?php
require_once(realpath(dirname(__FILE__) . "/../include/config.php"));
        if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
			 echo  die(pg_last_error());
            echo "Невозможно подключится к базе сервера ";
         
        }
        $q = 'select pr_calc_narab(null,null,0,0,0,1)';
          if(@pg_query($q))
          {
            echo 'Расчёт наработки завершен';
            
          }
          else
          {
           echo 'Ошибка выполнения';
          }

?>