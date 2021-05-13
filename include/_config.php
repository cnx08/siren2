<?php


$_CFG = array();

//переменные для соединения с СУБД
//имя сервера СУБД
$_CFG['DB']['SERVER_NAME']	= 'localhost';
//пользователь СУБД
$_CFG['DB']['USER_NAME']	= 'postgres'; 	
//пароль СУБД
$_CFG['DB']['USER_PWD']		= 'deparol';
//имя базы в СУБД
$_CFG['DB']['NAME'] 		= 'askd51';

//переменные сервера
//директория сервера
$_CFG['SERVER']['DOCUMENT_ROOT'] 	= $_SERVER['DOCUMENT_ROOT'];
//папка со скриптами
$_CFG['SERVER']['SCRIPT_FOLDER'] 	= '/';
//полный путь к скриптам
 if ( $_CFG['SERVER']['SCRIPT_FOLDER'] != '' && $_CFG['SERVER']['SCRIPT_FOLDER'] != null )
	$_CFG['SERVER']['SCRIPT_PATH']	 	= $_CFG['SERVER']['DOCUMENT_ROOT'].'/'.$_CFG['SERVER']['SCRIPT_FOLDER'].'/';
 else
	$_CFG['SERVER']['SCRIPT_PATH'] = $_CFG['SERVER']['DOCUMENT_ROOT'];

?>