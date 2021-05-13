<?php
    if (stripos(php_uname(),'linux')===false){
        //$str = 'cmd /c C:\acs\Apache2\htdocs\siren2\monitor\monitor_start.bat';
        //system($str);
        $str = 'monitor_start.bat';
        shell_exec($str);
    }
    else{
        //ставь на линукс и тестируй запуск
    }
    sleep(2);//время на сборку и старт приложения, если оно ещё стартануло, то будет 403 Service Temporarily Unavailable
    //header("Location: /node/");
        
        
       
