<?php
/**
Класс исключений возникших при работе с пользователем.
Автор: 	Денис Давыдов
Дата:  	21.10.2008 
Версия: 1.000.001
*/

class CUserException extends Exception
{
  public function __construct($message, $errorno)
  {
    //обработка ошибок, определённых программистом.
	if($errorno >= 5000)
	{
      $message = __CLASS__  .": ". $message;
    }
	else
	{
      $message = __CLASS__  . " - ". $message;
    }
    parent::__construct($message, $errorno);
  }
 
  //////////////////////////////////////////////////////////  
  public function __toString()
  {
    return ('<center><span style="color:red;font-family:Tahoma;font-weight:bold;font-size:13px">Ошибка: '. $this->code.' - '.$this->message.'</span></center>');
  }

} 
/**
Класс для представления пользователя 
Автор: 	Денис Давыдов
Дата:  	21.10.2008 
Версия: 1.000.001
*/
class CUser
{
	/**
		Поле указывает существует ли экземпляр данного класса
	*/
	private static $instance = false;
	
	////////////////////////////////////////
	/**
		Конструктор. Если при попытке создания обнаружевается что объект такого класса существует 
		то бросает исключение CUserException	
	*/
	public function __construct()
	{
		if(self::$instance == false)
		{
			self::$instance = true;
		}
		else
         {
			$msg = 'Для создания пользователя нужно уничтожить текущего.';
			throw new CUserException($msg,0001);
		}
		
	}
	////////////////////////////////////////
	/**
		Инициализирует переменные в сессиии: <br> 
		$_SESSION['USER']  - массив для хранения настроек пользователя<br> 
		$_SESSION['USER']['id'] - uid пользователя <br>
		$_SESSION['USER']['permissions']  - массив для хранения uid модулей, к которым есть доступ у пользователя<br>
		Параметр $id - uid пользователя в БД
		Если параметр $id не является целочисленным или меньше 0 тогда бросает исключение CUserException
			
	*/
	public function initialize($id)
	{
		if ( is_integer($id) && $id >= 0 )
		{
			$_SESSION ['USER']['id'] = $id;
			$_SESSION ['USER']['permissions'] = array();
		}
		else
		{
			$msg = 'Не удалось ининциализировать пользователя.';
			throw new CUserException($msg,'1000');
		}		
	}
	///////////////////////////////////////
	/**
		Добовляет к массиву permissions uid модуля $idModule (если такого не существует). Если параметр $idModule меньше нуля 
		или не является целочисленным тогда тогда бросает исключение CUserException.
		Если вызвать метод до вызова initialize, будет выброшено исключение CUserException, т.к. 
		не созданы неоходимые переменные.
	*/
	public function setModulePermission($idModule)
	{
		if ( !is_integer( $idModule ) || $idModule < 0 )
			throw new CUserException('Не удаётся добавить разрешение для пользователя','1002');
		
		if ( isset( $_SESSION['USER']['permissions'] ))
		{
			if ( !in_array($idModule,$_SESSION['USER']['permissions']) )
				$_SESSION['USER']['permissions'][] = $idModule;
		}
		else
		{
			$msg = 'Не удаётся добавить разрешение для пользователя';
			throw new CUserException($msg,'1001');
		}
		
	}
	/**
		Метод возвращает true если $idModule найден в массиве permissions иначе false.
		Если параметр $idModule меньше нуля или не является целочисленным тогда тогда бросает исключение CUserException.
		
	*/
	////////////////////////////////////////
	public function isModulePermit($idModule)
	{
		if ( is_integer($idModule) && $idModule >= 0 )
		{
			if ( !in_array($idModule,$_SESSION['USER']['permissions']) ) 
				return false;
			else
				return true;
		}
		else
		{
			$msg = 'Не удалось определить доступ к модулю.';
			throw new CUserException($msg,'1003');
		}		
	}
	////////////////////////////////////////
	public function __toString()
	{
		$str = '';
		$str .= 'UID:'. $_SESSION ['USER']['id'].'<br>';
		foreach ( $_SESSION ['USER']['permissions'] as $key => $value )
				$str .= $key . ' =>  '. $value .'<br>';
		
		return $str;
	}
	////////////////////////////////////////
	/**
		Возвращает uid пользователя
	*/
	public function getId()
	{
		if ( isset( $_SESSION['USER']['id'] ) )
		{
			return $_SESSION['USER']['id'];
		}
		else
		{
			$msg = 'Не удалось получить доступ к идентификатору.';
			throw new CUserException($msg,'1004');
		}
	}
	/////////////////////////////////////////
	/**
		Уничтожает переменные в сессии. После вызова данного метода можно создавать другого.
	*/
	public function close()
	{
		if( isset( $_SESSION['USER'] ) )
			unset( $_SESSION['USER'] );
		self::$instance = false;
	}
}
?>