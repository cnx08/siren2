<?php
//определяем путь к логу
$start_t = microtime(true);
$req_tur = $_REQUEST['tur'];
$req_size = $_REQUEST['size'];

$unit= 'f'.ceil($req_tur/32);

$filename = "$unit/$req_tur".".log";
$update_size = "";


    if (file_exists($filename)) 
    {      
        clearstatcache();
        $update_size= filesize($filename);
        if (strcmp($req_size,$update_size)!=0)//if лог файл обновился, то :
        {
            //всё в массив
            $res='true';
            $file_array = file($filename,FILE_IGNORE_NEW_LINES);
            //распарсим последний элемент
            list($code, $tur, $hour, $min, $sec, $px_code) = explode(";", end($file_array));
            if (strlen($px_code)== 16)
            {
                $show_small_frame=1;//Чтобы на события К не появлялись окошки
                $red_code = 0; // красная рамка для запрещающих событий
                ///$code_descr
                switch ($code) {
                    case 'K':$code_descr="получен код пропуска на входе";
                        $show_small_frame=0;
                    break;
                    case 'k':$code_descr="получен код пропуска на выходе";
                        $show_small_frame=0;
                        break;
                    case 'I':
                        $code_descr="вход через турникет";
                        break;
                    case 'O':
                        $code_descr="выход через турникет";
                        break;
                    case 'B':
                        $code_descr="вход по разрешающему документу";
                        break;
                    case 'D':
                        $code_descr="выход по разрешающему документу";
                        break;
                    case 'V':
                        $code_descr="вход по разрешению охранника";
                        break;
                    case 'S':
                        $code_descr="проезд открыт";
                        break;
                    case 'C':
                        $code_descr="проезд закрыт";
                        break;
                    case 'H':
                        $code_descr="ворота открыты для въезда";
                        break;
                    case 'N':
                        $code_descr="ворота открыты для выезда";
                        break;
                    case 'W':
                        $code_descr="выход по разрешению охранника";
                        break;
                    case 'R':
                        $code_descr="проход от радиобрелка";
                        break;
                    case 'G':
                        $code_descr="выход с гостевым пропуском";
                        break;
                    case 'i':
                        $code_descr="незавершённый вход";
                        break;
                    case 'o':
                        $code_descr="незавершённый выход";
                        break;
                    case 'g':
                        $code_descr="незавершённый выход с гостевым пропуском";
                        break;
                    case 'c':
                        $code_descr="ошибка контрольной суммы кода пропуска";
                        $red_code = 1;
                        break;
                    case 'z':
                        $code_descr="двойные засечки на входе";
                        $red_code = 1;
                        break;
                    case 'q':
                        $code_descr="двойные засечки на выходе";
                        $red_code = 1;
                        break;
                    case 'p':
                        $code_descr="пропуск заблокирован";
                        $red_code = 1;
                        break;
                    case 'd':
                        $code_descr="сюда нет допуска";
                        $red_code = 1;
                        break;
                    case 'w':
                        $code_descr="попытка прохода в неурочное время";
                        $red_code = 1;
                        break;
                    case 'x':
                        $code_descr="незарегистрированный пропуск";
                        $red_code = 1;
                        break;
                    case 'y':
                        $code_descr="выход с гостевым пропуском запрещён";
                        $red_code = 1;
                        break;
                    default:
                        $code_descr = $code." - неизвестное событие";
                        $red_code = 1;
                }

                ///time_str
                if (strlen($hour)==1) $hour = '0'.$hour;
                if (strlen($min)==1) $min = '0'.$min;
                if (strlen($sec)==1) $sec = '0'.$sec;

                $time_str=$hour.':'.$min.':'.$sec;

                include('../include/input.php');
                $q = 'select * from t_photo where code = \''.$px_code.'\' limit 1';

                $res1 = pg_query($q);
                $r = pg_fetch_array($res1);
                if(!isset($r) || $r['code']==''){
                    $res='empty'; 
                }
                else{
                    $px_code = $r['code'];
                    $FIO= $r['fio'];
                    $tab= $r['tabel_num'];
                    $dept= $r['dname'];
                    $position= $r['position'];
                    $smena_name= $r['sname'];
                    $sm_time= $r['smena'];
                    $sm_dinner= $r['obed'];
                    $photo_name= $r['photo'];
                }

                //to send as json:
                $response = '{';
                    $response .= '"result":"'.     $res.'",';
                    $response .= '"code_descr":"'. $code_descr.'",';
                    $response .= '"red_code":"'.   $red_code.'",';// 0 - есть допуск, 1 - нет допуска
                    $response .= '"time_str":"'.   $time_str.'",';
                    $response .= '"FIO":"'.        str_replace('"',"",$FIO).'",';
                    $response .= '"dept":"'.       str_replace('"',"",$dept).'",';
                    $response .= '"position":"'.   str_replace('"',"",$position).'",';
                    $response .= '"smena_name":"'. str_replace('"',"",$smena_name).'",';
                    $response .= '"sm_time":"'.    $sm_time.'",';
                    $response .= '"sm_dinner":"'.  $sm_dinner.'",';
                    $response .= '"sm_frame":"'.   $show_small_frame.'",';
                    $response .= '"photo_name":"'. str_replace('"',"",$photo_name).'",';
                    $response .= '"script_time":"'. str_replace('"',"",$start_t - microtime(true)).'",';
                    $response .= '"update_size":"'.$update_size.'"';
                $response .= '}'; 
                echo $response;
                exit();
            }
            else {
                //file_put_contents("response.log","px_code ne 16 simvolov ".end($file_array)." ".date('d.m.Y H:i:s')."\r\n",FILE_APPEND);
                echo '{"result":"false"}';
                exit();
            }
        }
        echo '{"result":"false"}';
        exit();
    }
    else {
        //file_put_contents("response.log","Передан турн ".$req_tur.". Файл не существует ".$filename." ".date('d.m.Y H:i:s')."\r\n",FILE_APPEND);
        echo '{"result":"false"}';
        exit();
    }
