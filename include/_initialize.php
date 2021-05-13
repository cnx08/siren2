<?php
/*
	
	25.09.2008 Давыдов Д.Н.
	
	Скрипт создаёт объекты:
	1 - для работы С БД
	2 - валидатор данных

	v1.000.001	
*/
require_once('_config.php');
require_once($_CFG['SERVER']['SCRIPT_PATH'].'classes/base/db.class.php');
require_once($_CFG['SERVER']['SCRIPT_PATH'].'classes/base/data.class.php');
require_once($_CFG['SERVER']['SCRIPT_PATH'].'classes/base/user.class.php');

//инициализируем валидатор данных
$dataValidator = new CDataValidator();

//инициализируем соединение с БД
//set_time_limit(600);
try
{
	$dbFactory = new CDbFactory();
	
	$dbConnection = $dbFactory->getConnection('pg',$_CFG['DB']['SERVER_NAME'],$_CFG['DB']['NAME'],$_CFG['DB']['USER_NAME'],$_CFG['DB']['USER_PWD']);
	//$dbConnection->setDatabase ($_CFG['DB']['NAME']);
	
	unset ($dbFactory);
}
catch( CDbException $e )
{
	//переход на ошибку
	echo $e;
	exit();
}
//создаём пользователя
try
{
	$User  = new CUser(); 
}
catch( Exception $e )
{
	echo $e;
	exit();
}
?>