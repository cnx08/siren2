<?php
try {
    //считаем, что веб-интерфейс и БД неразрывно связаны и версия ПО (ACS) у них одна на двоих
    //API это протокол обмена юнита и сервера
    //версия записывается как major;minor , т.е. 001;001  =  1.1
    $API_VER = '001;001';
    $ACS_VER = '001;001';
    //таблица совместимости версий
    
    //если напоролись на несовместимость версий, то все команды от этого юнита будут приводить к exit().
    //чтобы обнулить это дело - нужно удалить файл unit_version[номер].txt.
    
    
    require_once(realpath(dirname(__FILE__) . "/../include/config.php"));
    if (!pg_connect("host=".$host." dbname=".$SMDBName." user=".$SMDBUser." password=".$SMDBPass."")) {
            echo  die(pg_last_error());
            echo "Невозможно подключится к базе сервера ";
            file_put_contents("error.log",date('d.m.Y H:i:s')."\r\n",FILE_APPEND);
    }




    $st_full = microtime(true);
    $ss_time = date('d.m.Y H:i:s');

    $putdata = fopen("php://input", "r");

    while ( ($buf=fread( $putdata, 8096 )) != '' ) {
        $data .= $buf;
    }
    //file_put_contents("data".$_SERVER['REMOTE_ADDR'].".log",date('d.m.Y H:i:s'). ' '.$data."\r\n\r\n",FILE_APPEND);


    $arr = explode("\n", $data);//строку в массив
    $end_RN = '';//для \r\n
    $st_RN = '';
    //$current_command = 0;
    $unit = 0;
    $ev_string = "";

    $socket_closed = 0;
    $socket_closed2 = 0;
    //global vars
    $rows = 0;
    $day = '';
    $mon = '';
    $year = '';
    $hour = '';
    $min = '';
    $sec = '';
    function get_datetime($tid){
        global $rows, $day, $mon,$year, $hour, $min, $sec;
        $q = 'select * from ora_state where id = '.$tid;
        $res=pg_query($q);
        while($r = pg_fetch_array($res))
        {
            list($day,$mon,$year) = explode('.',$r['date']);
            list($hour,$min,$sec) = explode(':',$r['time']);
            $rows = $r['size'];
        }
    }
    $prev_command = '0';//для предотвращения повторного создания сокетов в рамках одного запроса
    foreach($arr as $key => $value)
    {
        //file_put_contents("from_unit.log","$value\r\n",FILE_APPEND);////// будем писать логи пока отлаживаем скрипт

        $mail_dog_in_row = strpos($value, '@');
        if ($mail_dog_in_row === 0) //значит строка это команда, иначе - данные идущие за последней командой
        {   
            if ($key !== 0){//чтобы небыло пустой строки в конце
                 $st_RN = chr(10).chr(13);
            }
            list($mail_dog, $command_number, $comment, $unit) = explode(";", $value);
            if(file_exists('../api/unit_version'.$unit.'.txt')){
                $unit_version = (int)preg_replace("/[^\d]+/","",file_get_contents('../api/unit_version'.$unit.'.txt'));//оставим только числа
                if ($unit_version < 100 || $unit_version >= 300) {
                    pg_query('select sys_write_log(1,\'exchange_commands.php\',\'\',\'Подключен юнит '.$unit.' с несовместимой версией ПО: '.$unit_version.'\',210,0)');
                    exit(); //ещё есть проверка при получении unit_info ниже
                }
            }
            switch ($command_number) {
                case '100': //state
                    $q = 'select * from ora_state order by id';
                    $res=pg_query($q);
                    $rows = pg_num_rows($res);
                    echo $st_RN.'@;0;state;;'.chr(10).chr(13);
                    while($r = pg_fetch_array($res))
                    {
                        list($day,$mon,$year) = explode('.',$r['date']);
                        list($hour,$min,$sec) = explode(':',$r['time']);
                        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
                        echo $r['id'].';'.$r['name'].';'.$r['size'].';'.$day.';'.$mon.';'.$year.';'.$hour.';'.$min.';'.$sec.$end_RN;
                    }
                    $prev_command = '100';
                    break;
                case '101': //tuning
                    $q = 'select * from t_tuning where unit = '.$unit.' order by id';
                    $res=pg_query($q);
                    get_datetime(1);
                    echo $st_RN.'@;1;tuning;'.$rows.';'.$day.';'.$mon.';'.$year.';'.$hour.';'.$min.';'.$sec.chr(10).chr(13);
                    while($r = pg_fetch_array($res))
                    {
                        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
                        echo $r['id'].';'.$r['name'].';'.$r['param'].';'.$end_RN;
                    }
                    $prev_command = '101';
                    break;
                case '102': //turniket
                    $q = 'select * from t_turniket';
                    $res=pg_query($q);
                    get_datetime(2);
                    echo $st_RN.'@;2;turniket;'.$rows.';'.$day.';'.$mon.';'.$year.';'.$hour.';'.$min.';'.$sec.chr(10).chr(13);
                    while($r = pg_fetch_array($res))
                    {
                        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
                        //$r['name'] не отправляю, т.к. русский текст - зло.
                        echo $r['num'].';'.$r['id_territory'].';'.$r['id_territory_out'].';'.$r['status'].';'.$r['reader_in'].';'.$r['reader_out'].$end_RN;
                    }
                    $prev_command = '102';
                    break;
                case '103': //pass
                    $q = 'select * from t_pass';
                    $res=pg_query($q);
                    get_datetime(3);
                    echo $st_RN.'@;3;pass;'.$rows.';'.$day.';'.$mon.';'.$year.';'.$hour.';'.$min.';'.$sec.chr(10).chr(13);
                    while($r = pg_fetch_array($res))
                    {
                        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
                        $urureu = $r['dopusk'].';'.$r['graph'].';'.$r['code'].';'.$r['status'].';'.$r['pin'].';'.$r['graph_offset'].$end_RN;
                        echo $urureu;
                    }
                    $prev_command = '103';
                    break;
                case '104': //dopusk
                    $q = 'select * from t_dopusk';
                    $res=pg_query($q);
                    get_datetime(4);
                    echo $st_RN.'@;4;dopusk;'.$rows.';'.$day.';'.$mon.';'.$year.';'.$hour.';'.$min.';'.$sec.chr(10).chr(13);
                    while($r = pg_fetch_array($res))
                    {
                        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
                        echo $r['id_dopusk'].';'.$r['rej_code'].';'.$r['id'].';'.$r['t_in'].';'.$r['t_out'].$end_RN;
                    }
                    $prev_command = '104';
                    break;
                case '105': //docs
                    $q = 'select * from t_docs';
                    $res=pg_query($q);
                    get_datetime(5);
                    echo $st_RN.'@;5;docs;'.$rows.';'.$day.';'.$mon.';'.$year.';'.$hour.';'.$min.';'.$sec.chr(10).chr(13);
                    while($r = pg_fetch_array($res))
                    {
                        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
                        echo $r['code'].';'.$r['terr'].';'.$r['start_h'].';'.$r['start_m'].';'.$r['end_h'].';'.$r['end_m'].';'.$r['status'].$end_RN;
                    }
                    $prev_command = '105';
                    break;
                case '106': //graph
                    $q = 'select * from t_graph';
                    $res=pg_query($q);
                    get_datetime(6);
                    echo $st_RN.'@;6;graph;'.$rows.';'.$day.';'.$mon.';'.$year.';'.$hour.';'.$min.';'.$sec.chr(10).chr(13);
                    while($r = pg_fetch_array($res))
                    {
                        list($day,$mon,$year) = explode('.',$r['date_in']);
                        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
                        echo $r['id'].';'.$r['id_dopusk'].';'.$r['num'].';'.$day.';'.$mon.';'.$year.$end_RN;
                    }
                    $prev_command = '106';
                    break;
                case '107': //server info
                    $os_inf = preg_split('/ /',php_uname(),-1);
                    $php_inf = phpversion();
                    echo $st_RN.'@;7;info;;'.chr(10).chr(13);

                    echo '1;API;'.$API_VER.';'.chr(10).chr(13);
                    echo '2;ACS;'.$ACS_VER.';'.chr(10).chr(13);
                    echo '3;OS;'.$os_inf[0].';'.$os_inf[1].';',$os_inf[3].chr(10).chr(13);
                    echo '4;APACHE;'.filter_var(substr($_SERVER['SERVER_SOFTWARE'],0,strpos($_SERVER['SERVER_SOFTWARE'],'(')), FILTER_SANITIZE_NUMBER_INT).';;'.chr(10).chr(13);
                    echo '5;PHP;'.filter_var($php_inf, FILTER_SANITIZE_NUMBER_INT).';;'.chr(10).chr(13);
                    echo '6;DATE;'.date('d;m;Y').chr(10).chr(13);
                    echo '7;TIME;'.date('H;i;s');
                    $prev_command = '107';
                    break;
                case '108': //unit info 
                    $prev_command = '108';
                    //$current_command = 108;
                    //file_put_contents("from_unit_108.log","$value\r\n",FILE_APPEND);////// будем писать логи пока отлаживаем скрипт
                    break;
                case '109': //online event
                    if($prev_command != '109')
                    {   
                        $prev_command = '109';

                        //мониторинг событий
                        if ( file_get_contents('../api/event_monitor.txt') == 'run')
                        { 
                            $is = 0;
                            $rand_id_proc = rand(0,1000);
                            $isp = 0;
                            while($is == 0)
                            {   
                                //clearstatcache();
                                if ( file_get_contents('sock.txt') == '0' )
                                {
                                    $is = 1;
                                    file_put_contents('sock.txt',1);
                                    $address = '127.0.0.1';
                                    $port = 10000;

                                    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                                    if ($socket < 0) 
                                    { 
                                        $err = socket_strerror(socket_last_error());
                                        //socket_shutdown($socket, 2);
                                        socket_close($socket);
                                        $socket_closed = 1;
                                    }

                                    $result = @socket_connect($socket, $address, $port);
                                    if ($result === false && $socket_closed == 0) 
                                    { 
                                        $no_conn = 1;
                                        $err = socket_strerror(socket_last_error());
                                        //socket_shutdown($socket, 2);
                                        socket_close($socket);
                                        $socket_closed = 1;
                                        $ttt = microtime(true) - $st_full;
                                        //file_put_contents("time".$_SERVER['REMOTE_ADDR'].".log", $rand_id_proc. ' '.$ttt." sec from begin, socket_connect() failed123: $err\r\n",FILE_APPEND);
                                    } 
                                }
                                else {
                                    //file_put_contents("sock".$_SERVER['REMOTE_ADDR'].".log",$rand_id_proc. ' '.date('d.m.Y H:i:s')." socket занят\r\n",FILE_APPEND);
                                    usleep(50000);//50ms
                                    if ($isp++ > 10) file_put_contents('sock.txt',0);
                                }
                            }
                        }

                        //фотоконтроль Nodejs
                        /*if ( file_get_contents('../api/photo_monitor.txt') == 'run')
                        { 
                            $is2 = 0;
                            $rand_id_proc2 = rand(0,1000);
                            $isp2 = 0;
                            while($is2 == 0)
                            {   
                                if ( file_get_contents('sock2.txt') == '0' )
                                {
                                    $is2 = 1;
                                    file_put_contents('sock2.txt',1);
                                    $no_conn_ph = 0;
                                    $address_ph = '127.0.0.1';
                                    $port_ph = 20000;

                                    $socket_ph = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                                    if ($socket_ph < 0) 
                                    { 
                                        $err_ph = socket_strerror(socket_last_error());
                                        $socket_closed2 = 1;
                                    }

                                    $result_ph = @socket_connect($socket_ph, $address_ph, $port_ph);
                                    if ($result_ph === false  && $socket_closed2 == 0) 
                                    { 
                                        $no_conn_ph = 1;
                                        $err_ph = socket_strerror(socket_last_error());
                                        $socket_closed2 = 1;
                                        $ttt2 = microtime(true) - $st_full;
                                        //file_put_contents("time2".$_SERVER['REMOTE_ADDR'].".log", $rand_id_proc2. ' '.$ttt2." sec from begin, socket_connect() failed: $err\r\n",FILE_APPEND);
                                    } 
                                }
                                else {
                                    //file_put_contents("sock2".$_SERVER['REMOTE_ADDR'].".log",$rand_id_proc2. ' '.date('d.m.Y H:i:s')." socket занят\r\n",FILE_APPEND);
                                    usleep(50000);//50ms
                                    if ($isp2++ > 10) file_put_contents('sock2.txt',0);
                                }
                            }

                        }*/
                    }
                    break;
                case '110': //offline event 
                        //$current_command = 110;
                        $prev_command = '0';
                        $load_events = true;
                        file_put_contents("../api/events".$unit.".txt","");
                    break;

                default: break;

            }
        }
        else{//data that going after command
            switch ($command_number) {
                case '108': //unit info
                    /*
                        1;Server;http://192.168.0.1;80;
                        2;Proxy;192.168.0.1;3128;
                        3;Unit;16123544;ODROID-C1+ Ubuntu-14
                        4;Srt;12;07;2016;17;10;04
                        5;Dm;12;07;2016;17;10;00
                        6;Arch;31;05;2016;14;21;45
                        7;eth0;192.168.0.102;192.168.0.255;255.255.255.0;B8:27:EB:0A:AF:59
                        8;eth1;not active;
                        9;lo;127.0.0.1;127.255.255.255;255.0.0.0;00:00:00:00:00:00
                        10;wifi;not active;
                        11;2G;not active;
                        12;3G;not active;
                        ...
                        32;Nc-485;12;07;2016;17;07;41
                        33;0033;12;07;2016;17;07;40
                        34;0034;12;07;2016;17;07;35
                        ...
                        99;Date/Time;12;07;2016;17;10;04
                     */

                    list($id,$info) = explode(';',$value,2);
                    if($id == 1){
                        list($param_name,$val) = explode(';',$info,2);
                        $serv_val = $val;
                    }
                    if($id == 2){
                        list($param_name,$val) = explode(';',$info,2);
                        $prox_val = $val;
                    }
                    if($id == 3){
                        list($param_name,$u_id,$u_ver,$u_descr) = explode(';',$info,4);
                        $regex_str = '/\d{0,5}/';//шаблон строки для полученной версии
                        if (preg_match($regex_str, $u_ver)){
                            file_put_contents('../api/unit_version'.$unit.'.txt',$u_ver);
                            if ($u_ver < 100 || $u_ver >= 300){
                                pg_query('select sys_write_log(1,\'exchange_commands.php\',\'\',\'Подключен юнит '.$unit.' с несовместимой версией ПО: '.$unit_version.'\',210,0)');
                                exit();
                            }
                        }
                        else {
                            pg_query('select sys_write_log(1,\'exchange_commands.php\',\'\',\'Получена некорректная информация о версии ПО юнита '.$unit.': '.$unit_version.'\',210,0)');
                            exit();
                        }
                        //смотрим не сменился ли номер у юнита?
                          $q = 'select pr_check_unit_num('.$u_id.');';
                            $res = pg_query($q); 
                            while($r = pg_fetch_array($res))
                            {
                                $unit_in_db = $r['0'];
                            }
                            if ($unit != $unit_in_db && $unit_in_db>=0){
                                echo '@;11;Control;12;'.$unit_in_db.';1;Set_unit_number';
                                file_put_contents("data".$_SERVER['REMOTE_ADDR'].".log",date('d.m.Y H:i:s'). ' '.'@;11;Control;12;'.$unit_in_db.';1;Set_unit_number'."\r\n\r\n",FILE_APPEND);
                                if(!is_dir('../ph2/f'.$unit_in_db)) mkdir('../ph2/f'.$unit_in_db);
                                exit();
                            }

                        $q = 'select base_w_u_unit_info(1,'.$unit.',\'Server\',\''.$serv_val.'\','.$u_id.');';
                        if(!pg_query($q)) break;
                        $q = 'select base_w_u_unit_info(2,'.$unit.',\'Proxy\',\''.$prox_val.'\','.$u_id.');';
                        if(!pg_query($q)) break;
                        $q = 'select base_w_u_unit_info('.$id.','.$unit.',\''.$param_name.'\',\'Ver. '.$u_ver.', '.$u_descr.'\','.$u_id.');';

                        if(!pg_query($q)) break;
                    }
                    if(($id > 3 && $id < 7) || $id > 31){
                        list($param_name,$day,$mon,$year,$hh,$mm,$ss) = explode(';',$info,8);
                        $val = $day.'.'.$mon.'.'.$year.' '.$hh.':'.$mm.':'.$ss;
                        $q = 'select base_w_u_unit_info('.$id.','.$unit.',\''.$param_name.'\',\''.$val.'\','.$u_id.')';
                        if(!pg_query($q)) break;
                    }
                    if($id > 6 && $id < 13){
                        list($param_name,$ip,$dns,$mask,$mac) = explode(';',$info,6);
                        $val = 'ip: '.$ip.'; dns: '.$dns.'; mask: '.$mask.'; mac: '.$mac;
                        $q = 'select base_w_u_unit_info('.$id.','.$unit.',\''.$param_name.'\',\''.$val.'\','.$u_id.')';
                        if(!pg_query($q)) break;
                    }

                    //file_put_contents("from_unit_108.log","$value\r\n",FILE_APPEND);////// будем писать логи пока отлаживаем скрипт
                    break;
                case '109': //online event
                    // используется для фотоконтроля и панели управления дверьми и турникетами
                    // если эти модули выключены, то онлайн события игнорируем
                    $regex_str = '/\d{0,15}[;][a-zA-Z][;]\d{0,3}[;]\d{1,2}[;]\d{1,2}[;]\d{1,2}[;][A-F0-9]{1,16}[;]\d{0,10}/';//шаблон строки для полученного события
                    if (preg_match($regex_str, $value)){
                        // Рассылка номера территории пропуска по юнитам, для контроля 2-х засечек
                        list($id_event,$code,$terr,$h1,$m1,$s1,$pass) = explode(';',$value,8);
                        if ($code == 'Z'){
                            $q = 'select pr_init_terr_change('.$terr.',\''.$pass.'\')';
                            if(!pg_query($q)) break;

                        } 

                        // фотоконтроль php
                        if ( file_get_contents('../ph2/stop.txt') == 'run' )
                        {
                            list($id_event,$event_row) = explode(';',$value,2);
                            list($code,$tur) = explode(';',$event_row,3);
                            $unitf = 'f'.$unit;

                            if ($code != 'Z' && $code != 't' && $code != 'M' && $code != 'T' && $code != 'A'  && $code != 'U'  && $code != 'E')
                            {
                                $filename = "../ph2/".$unitf.'/'.$tur.".log";
                                if(filesize($filename)>20000)//20 кБ
                                {
                                    $file_array = file($filename,FILE_IGNORE_NEW_LINES);
                                    $last_ev = end($file_array);
                                    file_put_contents($filename,"$last_ev\r\n");
                                }
                                file_put_contents($filename,"$event_row\r\n",FILE_APPEND);
                            }
                        }
                        //фотоконтроль nodejs
                        /*if ( file_get_contents('../api/photo_monitor.txt') == 'run')
                        { 
                            if ( $socket_closed2 == 0){
                                list($id_event,$code,$event_row) = explode(';',$value,3);

                                if ($code != 'Z' && $code != 't' && $code != 'M' && $code != 'T' && $code != 'A'  && $code != 'U'  && $code != 'E')
                                {
                                    global $socket_ph,$no_conn_ph;
                                    if ($no_conn_ph !== 1){
                                        socket_write($socket_ph, $value, strlen($value));
                                    }
                                    if (isset($socket_ph)) 
                                    { 
                                        socket_close($socket_ph);
                                        file_put_contents('sock2.txt',0);
                                        $socket_closed2 = 1;
                                    }
                                }
                            }
                        }
                        if ( file_get_contents('../api/event_monitor.txt') == 'stop')
                        { 
                            global $socket_ph;
                            socket_write($socket_ph, "close", 7);
                            file_put_contents('../api/event_monitor.txt','stoped');

                            if (isset($socket_ph)) 
                            { 
                                socket_close($socket_ph);
                                file_put_contents('sock2.txt',0);
                            }
                        }*/


                        // мониторинг событий php
                        if ( file_get_contents('../monitor/stop.txt') == 'run' )
                        {

                            $filename = "../monitor/events.log";
                            if(filesize($filename)>20000)//20 кБ
                            {
                                $file_array = file($filename,FILE_IGNORE_NEW_LINES);
                                $last_ev = end($file_array);
                                file_put_contents($filename,"$last_ev\r\n");
                            }
                            file_put_contents($filename,"$value\r\n",FILE_APPEND);

                            /*shared memory
                                ftok(__FILE__,'e') не пашет, поэтому берём понравившееся HEX название блока памяти и резервируем его
                                с - открытие/резервирование, а- чтение, w - чтение/запись
                                0666 - права доступа, хз чё именно это значит, но права видать есть
                                2048 - размер блока памяти в байтах
                                данные будем хранить в виде массива id : event  array('34' => '112922;E;7;11;15;10;43;0', '35' => '112923;E;7;11;15;11;43;0', ...);
                                пока мы на винде - shmop_* не работают как надо, потому как это юниксовая фича
                                на винде же эмулируется работа функций в "thread safe resource manager" (TSRM) by using Windows File Mappings internally
                                поэтому shmop_delete() не работает вовсе
                                придется незаполненное пространство выделенного блока разграничивать спец символами, которые будут означать, что после них идёт мусор



                            $content = '';
                            if($shm_id = shmop_open(0xbadaaaa, 'c', 0666, 10048)) {
                                $content = shmop_read($shm_id, 8, 0);//до 8-ки поидее длинна массива, но там все криво, и её не будем юзать
                                shmop_close($shm_id); 
                            }
                            else  file_put_contents("shmop_open_fails.log",date('d.m.Y H:i:s')."\r\n",FILE_APPEND);

                            $delimiter_pos = strpos($content,'^_^',10);
                            if( $delimiter_pos !== false ){
                                $true_str = substr($content,0,$delimiter_pos);
                                $c_arr = unserialize($true_str);

                                if ($delimiter_pos >= 9800){

                                    $max_key = max(array_keys($c_arr));
                                    $c_arr_temp = array();

                                    for ($i=$max_key-20; $i<=$max_key; $i++){//число 20 зависит от скорости поступления новых событий и служит для предотвращения запросов к индексам которых нет в массиве
                                        $c_arr_temp[$i] = $c_arr[$i];
                                    }
                                    $c_arr = array();
                                    for ($i=$max_key-20; $i<=$max_key; $i++){
                                        $c_arr[$i] = $c_arr_temp[$i];
                                    }
                                }
                            }
                            else{
                                $c_arr = array();
                            }
                            array_push($c_arr, $value);
                            if($shm_id = shmop_open(0xbadaaaa, 'c', 0666, 10048)) {
                                shmop_write($shm_id, serialize($c_arr).'^_^', 8);
                                shmop_close($shm_id); 
                            }
                            else  file_put_contents("shmop_open_fails.log",$value."\r\n",FILE_APPEND);

                            $ttt = microtime(true) - $st_full;
                            file_put_contents("time".$_SERVER['REMOTE_ADDR'].".log",$value.' time = '.$ttt." s ".date('d.m.Y H:i:s')."\r\n",FILE_APPEND);
                            */
                        }

                        //мониторинг событий nodejs
                        /*if ( file_get_contents('../api/event_monitor.txt') == 'run')
                        { 
                            if ( $socket_closed == 0){
                                global $socket,$no_conn;
                                if ($no_conn !== 1){
                                    socket_write($socket, $value, strlen($value));
                                    //$out = socket_read($socket, 1024);
                                }
                                if (isset($socket)) 
                                { 
                                    socket_close($socket);
                                    file_put_contents('sock.txt',0);
                                    $socket_closed = 1;
                                }
                            }
                       }
                       if ( file_get_contents('../api/event_monitor.txt') == 'stop')
                       { 
                            global $socket;
                            socket_write($socket, "close", strlen($value));
                            file_put_contents('../api/event_monitor.txt','stoped');

                            if (isset($socket)) 
                            { 
                                socket_close($socket);
                                file_put_contents('sock.txt',0);
                            }
                        }
*/

                        // панель управления турникетами
                        //if ( file_get_contents('../api/turn_panel_status.txt') == 'run')
                        //{ 

                        //}

                    }
                    else{
                        if (strlen($value)<12 || strlen($value)>45) {//длина строки больше или меньше чем должна быть x;1;18;02;33;6666666666666666;
                            $q = 'select sys_write_log(1,\'exchange_commands.php online\',\'\',\'не допустимая длина строки № ~'.substr($value,0,10).', длинна : '.strlen($value).'\',109,0)';
                        }
                        else $q = 'select sys_write_log(1,\'exchange_commands.php online\',\'\',\'с событием что-то не так: '.$value.'\',109,0)';
                        pg_query($q);

                    }

                    break;
                case '110': //offline event
                    $regex_str = '/\d{0,15}[;][a-zA-Z][;]\d{0,3}[;]\d{1,2}[;]\d{1,2}[;]\d{1,4}[;]\d{1,2}[;]\d{1,2}[;]\d{1,2}[;][A-F0-9]{1,16}[;]\d{0,10}/';//шаблон строки для полученного события
                    if (preg_match($regex_str, $value))
                    {
                        $ev_string .= $value."\r\n";
                    }
                    else{
                        if (strlen($value)<12 || strlen($value)>45)//длина строки больше или меньше чем должна быть x;1;18;02;33;6666666666666666;
                        {
                            $q = 'select sys_write_log(1,\'exchange_commands.php offline\',\'\',\'не допустимая длина строки № ~'.substr($value,0,10).'..., длинна : '.strlen($value).'\',110,0)';
                        }
                        else $q = 'select sys_write_log(1,\'exchange_commands.php offline\',\'\',\'с событием что-то не так: '.$value.'\',110,0)';
                        pg_query($q);
                    }

                    break;
                default: break;

            }
        }        
    }
    //global $socket;
    if (isset($socket) && $socket_closed == 0) 
    { 
        socket_close($socket);
        file_put_contents('sock.txt',0);
    }
    if (isset($socket_ph) && $socket_closed2 == 0) 
    { 
        socket_close($socket_ph);
        file_put_contents('sock2.txt',0);
    }

    //get commands from server to unit
    $q = 'select * from commands_to_unit where unit ='.$unit; //берём все команды к этому юниту
    $res=pg_query($q);
    $rows = pg_num_rows($res);
    $echo_str = '';
    while($r = pg_fetch_array($res))
    {
        $par1 = '';
        $par2 = '';
        $par_arr = array($r['param1'],$r['param2'],$r['param3'],$r['param4'],$r['param5']);
        foreach($par_arr as $key1 => $value1)
        {
            if ($par1 == '') {
                $par1 = $value1;
                continue;
            }
            if ($par2 == '') $par2 = $value1;
        }

        $end_RN = ++$i !== $rows ? chr(10).chr(13) : '';
        $echo_str .='@;11;Control;'.$r['c_code'].';'.$par1.';'.$par2.';'.$r['descr'].$end_RN;
    }
    echo $echo_str;            

    $q = 'delete from commands_to_unit where unit ='.$unit; //удаляем все команды к этому юниту 
    pg_query($q);  

    if($load_events===true){
        file_put_contents("events".$unit.".txt",$ev_string,FILE_APPEND);
        if (realpath(dirname(__FILE__) . "/../api/events".$unit.".txt") !=''){// check this too
            if (filesize(realpath(dirname(__FILE__) . "/../api/events".$unit.".txt")) == 0){
                $q = 'select sys_write_log(0,\'exchange_commands.php offline load\',\'\',\'Загрузка с юнита '.$unit.' отменена - файл с событиями пуст. Возможно небыло новых событий\',110,0)';
            }
            else{
                $q = 'select pr_sys_event_load_http(\''.realpath(dirname(__FILE__) . "/../api/events".$unit.".txt").'\','.$unit.')';
            }
            $res = pg_query($q);

        }
        //перенести на 1 строчку выше
        $en_full = microtime(true);
        $r_f = $en_full - $st_full;
        pg_query('select sys_write_log(0,\'exchange_commands.php offline load\',\'\',\'Загрузка событий за '.round($r_f,4).'\',110,0)');
    ////
    }


    fclose($putdata);
    //$ttt = microtime(true) - $st_full;
    //file_put_contents("time".$_SERVER['REMOTE_ADDR'].".log",'end '.$ttt." s ".date('d.m.Y H:i:s')."\r\n\r\n",FILE_APPEND);

} catch (Exception $e) {
    //file_put_contents("Exceptions".$_SERVER['REMOTE_ADDR'].".log",date('d.m.Y H:i:s').' '.$e->getMessage()."\r\n\r\n",FILE_APPEND);
    //echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
}
?>