<?php

set_time_limit(0);
header( 'Content-type: text/html; charset=utf-8' );

$i = 0;


while($i++<1000){
    //ob_end_flush();
    echo date("H:i:s").' <br>';
     file_put_contents("exit.log",date("H:i:s").connection_aborted()."\r\n",FILE_APPEND);
    flush();
    ob_flush();
    sleep(1);
    if(connection_aborted()==true){
        file_put_contents("exit.log",date('d.m.Y H:i:s')."\r\n",FILE_APPEND);
        break;
        file_put_contents("exit.log","don't tell me that you are running\r\n",FILE_APPEND);
    }
}
