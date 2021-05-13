<?php
	include ("include/input.php");
        require_once('include/hua.php');
        if ($_POST['act']=='init_load')
        {
            //поскольку юнит передаёт максимум по 10000 архивных событий, то для определения интервала загрузки по ид - введена доп. таблица
            //daily_events в которую раз в день заносим событие - получается своеобразная карта событий, по которой можно ориентироваться 
            // используя даты на входе. Итак - получив начало и конец интервала выраженные в ид - мы узнаем сколько раз по 10000 нам нужно 
            // загружать = parts. ДАлее мы проверяем загрузилась ли нужная нам часть раз в 2 секунды (максимум 20 секунд на часть).
            // Если да - то загружаем следующий кусок, если даже нет, то пробуем загрузить следующий.
            $date_start =$_POST['date_st'];
            $date_end = $_POST['date_end'];
            $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
            pg_query($q);
            
            $result = pg_query('select * from pr_init_event_load_period(\''.$date_start.'\',\''.$date_end.'\')');

            $arrayobj = array();
            while ($r = pg_fetch_array($result)) {
                $arrayobj[] = array($r['id'],$r['parts']);
                $parts = 0;
                while ($parts < $r['parts']){
                    $done = false;
                    $timeout = 0;
                    $id = $r['id'] + 10000*$parts;
                    $st_timer = microtime(true);
                    while ($done == false && $timeout<20){
                        sleep(2);
                        $q2 = 'select id from base_events where unit = '.$r['unit'].' and id = '.$id;
                        $result2 = pg_query($q2);
                        while ($r2 = pg_fetch_array($result2)) {
                            if($r2['id'] == $id)
                            {
                                $end_id = $r["id"]+10000*($parts+1);
                                $q3 = 'insert into commands_to_unit (c_code, unit, param1, param2, descr) values (10, '.$r["unit"].', '.$id.', '.$end_id .', \'Arch events\');';
                                pg_query($q3);
                                $done = true;
                                break;
                            }
                        }
                        $en_timer = microtime(true);
                        $timeout = $en_timer - $st_timer;
                    }
                    
                    
                    $parts++;
                }
               
            }
            echo '{res:"1"}';
        }
       