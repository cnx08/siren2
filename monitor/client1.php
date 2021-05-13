<?php
$req_size = $_REQUEST['size'];
$counter = $_REQUEST['counter'];
$filename = "events.log";
$update_size = "";
function parse_event($event){
    list($id, $code, $tur, $hour, $min, $sec, $px_code, $tmp) = explode(";", $event);
        
    switch ($code) {
        case 'K':$code_descr="получен код пропуска на входе";
            break;
        case 'k':$code_descr="получен код пропуска на выходе";
            break;
        case 'I': $code_descr="вход через турникет";
            break;
        case 'O':$code_descr="выход через турникет";
            break;
        case 'B': $code_descr="вход по разрешающему документу";
            break;
        case 'D': $code_descr="выход по разрешающему документу";
            break;
        case 'V':$code_descr="вход по разрешению охранника";
            break;
        case 'S': $code_descr="проезд открыт";
            break;
        case 'C': $code_descr="проезд закрыт";
            break;
        case 'H':$code_descr="ворота открыты для въезда";
            break;
        case 'N':$code_descr="ворота открыты для выезда";
            break;
        case 'W':$code_descr="выход по разрешению охранника";
            break;
        case 'R': $code_descr="проход от радиобрелка";
            break;
        case 'G':$code_descr="выход с гостевым пропуском";
            break;
        case 'N':$code_descr="ворота открыты для выезда";
            break;
        case 'H': $code_descr="ворота открыты для въезда";
            break;
        case 'i':$code_descr="незавершённый вход";
            break;
        case 'o':$code_descr="незавершённый выход";
            break;
        case 'g': $code_descr="незавершённый выход с гостевым пропуском";
            break;
        case 'c':$code_descr="ошибка контрольной суммы кода пропуска";
            break;
        case 'z':$code_descr="двойные засечки на входе";
            break;
        case 'q': $code_descr="двойные засечки на выходе";
            break;
        case 'p': $code_descr="пропуск заблокирован";
            break;
        case 'd':$code_descr="сюда нет допуска";
            break;
        case 'w':$code_descr="попытка прохода в неурочное время";
            break;
        case 'x':$code_descr="незарегистрированный пропуск";
            break;
        case 'y':$code_descr="выход с гостевым пропуском запрещён";
            break;
        case 't': $code_descr="турникет заблокирован";
            break;
        case 'T':$code_descr="получена удачная тестовая посылка";
            break;
        case 'Z': $code_descr="Смена территории";
            break;
        default:$code_descr = $code." - неизвестное событие";
    }
    if ($code == 'M'){
        switch ($px_code) {
            case '0000000000000001': $px_code_descr="Вход с помощью кнопки на двери или турникете";
                break;
            case '0000000000000002': $px_code_descr="Выход с помощью кнопки на двери или турникете";
                break;
            case '0000000000000003': $px_code_descr="Проход с помощью кнопки на двери или турникете";
                break;
            case '0000000000000004': $px_code_descr="Турникет разблокирован на вход";
                break;
            case '0000000000000005': $px_code_descr="Турникет разблокирован на выход";
                break;
            case '0000000000000006': $px_code_descr="Турникет полностью разблокирован";
                break;
            case '0000000000000007': $px_code_descr="Турникет из разблокированного состояния переведён";
                break;
            case '0000000000000008': $px_code_descr="Сработал датчик охраны";
                break;
            case '0000000000000009': $px_code_descr="Несанкционированное открытие двери";
                break;
            case '000000000000000A': $px_code_descr="проезд открыт кнопкой";
                break;
            case '000000000000000B': $px_code_descr="проезд закрыт кнопкой";
                break;
            case '000000000000000C': $px_code_descr="USB контроллер включен";
                break;
            case '000000000000000D': $px_code_descr="USB контроллер выключен";
                break;
            case '000000000000000E': $px_code_descr="нелицензионное использование программы";
                break;
            case '000000000000000F': $px_code_descr="получен сигнал «ПОЖАР»";
                break;
            case (substr($px_code,14,2) === '10' ? true : false) : $px_code_descr="Ошибка считывателя";
                break;
            case (substr($px_code,14,2) === '11' ? true : false) : $px_code_descr="Таймаут обслуживания > 300мс";
                break;
            case (substr($px_code,14,2) === '12' ? true : false) : $px_code_descr="Информация о времени обслуживания";
                break;
            case (substr($px_code,14,2) === '14' ? true : false) : $px_code_descr="Отладка контроллера";
                break;
            default: $px_code_descr="неизвестное мультисобытие"; 
                    break;
        }
    }
    if ($code == 'E'){
        switch ($px_code) {
            case '1': $px_code_descr="Старт программы юнита"."; номер версии программы: ".$tmp;
                break;
            case '2': $px_code_descr="Остановка программы юнита";
                break;
            case '5': $px_code_descr="Включился USB контроллер";
                break;
            case '6': $px_code_descr="Отключился USB контроллер";
                break;
            case '7': $px_code_descr="Недопустимая версия драйвера СКУД";
                break;
            case '8': $px_code_descr="СКУД начинает перезагрузку ОС";
                break;
            case '20': $px_code_descr="Старт программы DM"."; номер версии программы: ".$tmp;
                break;
            case '21': $px_code_descr="Останов программы DM";
                break;
            case '22': $px_code_descr="Перечитан tuning"."; Размер: ".$tmp;
                break;
            case '23': $px_code_descr="Перечитан pass"."; Размер: ".$tmp;
                break;
            case '24': $px_code_descr="Перечитан dopusk"."; Размер: ".$tmp;
                break;
            case '25': $px_code_descr="Перечитан docs"."; Размер: ".$tmp;
                break;
            case '26': $px_code_descr="Перечитан turniket"."; Размер: ".$tmp;
                break;
            case '27': $px_code_descr="DM Рестартует SRT";
                break;
            case '28': $px_code_descr="DM перезапускает сама себя";
                break;
            case '30': $px_code_descr="Проверьте работоспособность SRT!";
                break;
            case '31': $px_code_descr="Проверьте работоспособность NC485"."; число ошибок за сутки: ".$tmp;
                break;
            case '32': $px_code_descr="Проверьте исправность контроллера"."; номер контроллера: ".$tmp;
                break;
            case '33': $px_code_descr="Таблицы управления СКУД устарели";
                break;
            case '34': $px_code_descr="Удачная корректировка системного времени";
                break;
            case '35': $px_code_descr="Ошибка корректировки системного времени";
                break;
            case '36': $px_code_descr="Перечитан graph"."; Размер: ".$tmp;
                break;
            case '37': $px_code_descr="Выполнение командного файла";
                break;
            case '38': $px_code_descr="Изменение номера юнита"."; удалять старые события: ".$tmp;
                break;
            case '39': $px_code_descr="Рестарт ПО юнита";
                break;
            case '40': $px_code_descr="Рестарт DM";
                break;
            case '41': $px_code_descr="SRT начинает перезагрузку OS";
                break;
            case '42': $px_code_descr="Приоритет SRT"."; уровень приоритета: ".$tmp;
                break;
            case '43': $px_code_descr="ошибка инициализации LIBUSB";
                break;
            case '44': $px_code_descr="СЛИШКОМ БОЛЬШОЕ ВРЕМЯ ОБСЛУЖИВАНИЯ RS-485"."; мкс: ".$tmp;
                break;
            case '45': $px_code_descr="SRT перезапускает сама себя";
                break;
            case '46': $px_code_descr="MAX время обслуживания устройства RS-485"."; мкс: ".$tmp;
                break;
            case '47': $px_code_descr="Отладка SRT; значение: ".$tmp;
                break;
            case '50': $px_code_descr="Нарушен формат пакета RS-485";
                break;
            case '51': $px_code_descr="Таблицы СКУД зафиксированы в памяти (невыгружаемы)";
                break;
            case '52': $px_code_descr="Таблицы СКУД не зафиксированы в памяти (выгружаемы)";
                break;
            default: break;
        }
    }
    if ($code == 'E' || $code == 'M'){
        return $event. " - ".$px_code_descr;
    }
    else{
        return $event. " - ".$code_descr;
    }
}


$ex_time = 120 - 1;// ставим 300 сек, почему-то (int) ini_get("max_execution_time") = 0, а в php.ini = 300
//поэтому пока так
$endTime = time() + $ex_time;
while (time() < $endTime) {
    usleep(300000);//300ms
    if (file_exists($filename)) 
    {      
        clearstatcache();
        $update_size= filesize($filename);
        if (strcmp($req_size,$update_size)!=0)//if лог файл обновился, то :
        {
            //всё в массив
            $res='true';
            $file_array = file($filename,FILE_IGNORE_NEW_LINES);
            $cnt = count($file_array);
            //распарсим последний элемент
            if($counter == 0){
                $res_row = parse_event(trim(end($file_array)));
            } 
            else{
                for ($i=$counter; $i<$cnt; $i++){
                    $res_row .= parse_event(trim($file_array[$i]))."^";  
                }
            }
            $response = '{"result":"'.$res_row.'", "update_size":"'.$update_size.'", "counter":"'.$cnt.'"}';

            echo $response;
            //break;
            exit();
        }
    }
}
//restart
echo '{"result":"restart"}';



/*
 * shared memory


$ex_time = 120 - 1;// ставим 300 сек, почему-то (int) ini_get("max_execution_time") = 0, а в php.ini = 300
//поэтому пока так
$endTime = time() + $ex_time;
while (time() < $endTime) {
    usleep(100000);//100ms
    if($shm_id = shmop_open(0xbadaaaa, 'c', 0666, 10048)) 
    {      
        $content = shmop_read($shm_id, 8, 0);
        shmop_close($shm_id);
        $delimiter_pos = strpos($content,'^_^',10);
        if( $delimiter_pos !== false ){
            $true_str = substr($content,0,$delimiter_pos);//отбрасываем мусор, который стоит за разделителем
            $ev_arr = unserialize($true_str);
            $max_cnt = 0;
            $res_row = '';
            $max_cnt = max(array_keys($ev_arr));
            if($max_cnt > $counter){
                if($counter == 0){//распарсим последний элемент
                    $res_row = parse_event(trim(end($ev_arr)));
                } 
                else{
                    for ($i=$counter+1; $i<=$max_cnt; $i++){
                        $res_row .= parse_event(trim($ev_arr[$i]))."^";  
                    }
                }
                $update_size = 111;
                $response = '{"result":"'.$res_row.'", "update_size":"'.$update_size.'", "counter":"'.$max_cnt.'"}';
                echo $response;

                exit();
            }
        }
    }
    
}
//restart
echo '{"result":"restart"}';
 
*/

?>