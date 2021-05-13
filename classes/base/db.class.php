<?php
/*
Файл содержит классы
CDBConnect - абстрактный класс соединения С БД
CDBResultSet - абстрактный класс для получения данных из БД
CMSSQLConnect - класс для работы с MSSQL
CDBFactory - класс для создания соединения с БД указзаного типа

*/


/**
Абстрактный класс соединения с БД.
Автор: 	Денис Давыдов
Дата:  	01.10.2008 
Версия: 1.000.001 
*/
abstract class  CDBConnect
{
	/**
		Имя базы данных
	*/
	protected $databaseName;
	/**
		Идентификатор соединения с БД
	*/
	protected $connection;
	/////////////////////////////////////////////////////////////////////////////
	/**
		Конструктор. Сам по себе ничего не делает. 
	*/
	public function __construct()
	{
		
	}
	/////////////////////////////////////////////////////////////////////////////
	/**
		Деструктор.
	*/
	public function __destruct()
	{
		unset ( $this->databaseName );
		unset (	$this->connection );
	}
	/////////////////////////////////////////////////////////////////////////////
	/**
		Возвращает имя базы данных
	*/
	public function getDatabaseName ( )
	{
		return $this->databaseName;
	}
	/////////////////////////////////////////////////////////////////////////////
	/**
		Возвращает идентификатор соединения
	*/
	public function getConnection()
	{
		return $this->connection;
	}
	/////////////////////////////////////////////////////////////////////////////
	/**
		Абстрактная функция для создания  набора данных. Должна быть определена в потомках 
	*/
	public abstract function createResultSet($strSQL);
	/////////////////////////////////////////////////////////////////////////////
	/**
		Абстрактная функция для закрытия соединения. Должна быть определена в потомках 
	*/
	public abstract function close();
	//////////////////////////////////////////////////////////////////////////////
	/**
		
	*/
	public function __toString()
	{
		$str = __CLASS__ .'<br>';
		$str .= 'Database Name: '. $this->databaseName.'<br>';
		$str .= 'Connection pointer: '. $this->connection.'<br>';
		
		return $str;
	}
	
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
Абстрактный класс для получения набора данных.
Автор: 	Денис Давыдов
Дата:  	01.10.2008 
Версия: 1.000.001 
*/

abstract class CDBResultSet
{
	/**
		Строка sql запроса.
	*/
	protected $strSQL;
	/**
		Идентификатор соединия.
	*/
	protected $connection;
	/**
		Результирующий набор данных. 
	*/
	protected $result;
	/**
		Переменная итератора по строкам. Если текущая строка результируещего набора валидная то valid = true
		иначе false.
	*/
	protected $valid;
	/**
		Переменная итератора по строкам. Содержит текущую строку. 
	*/
	protected $currentrow;
	/**
		Значение текущего индекса.
	*/
	protected $key;
  
	const QUERY_FAILED = 5003;
	
	/**
		Конструктор. 
	*/
	public function __construct()
	{
		
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
		Деструктор. 
	*/
	public function __destruct()
	{
		unset($this->strSql);
		unset($this->connection);
		unset($this->result);
		unset($this->valid);
		unset($this->currentrow);
		unset($this->key);
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
		Возвращает идентификатор выборки данных. 
	*/
	public function getResult()
	{
		return $this->result;
	}
	/**
		Возвращает значение свойства valid
	*/  
	public function valid()
	{
		return $this->valid;
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
		Освобождает ресурсы, занятые под результирующий набор данных. Должна быть определена в потомке
	*/
	public abstract function close();
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/**
		Возвращает текущую строку результирующего запроса. Должна быть определена в потомке
	*/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public abstract function current();
	/**
		Возвращает текущее значение индекса. Должна быть определена в потомке
	*/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public abstract function key();
	/**
		Смещается на следующую строку результирующего запроса. Должна быть определена в потомке
	*/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public abstract function next();
	/**
		Смещается на предыдущую строку результирующего запроса. Должна быть определена в потомке
	*/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public abstract function previous();
	/**
		 Устанавливает итератор на начало результирующего запроса.. Должна быть определена в потомке
	*/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public abstract function rewind();
	
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
Класс исключений возникших при работе с БД.
Автор: 	Денис Давыдов
Дата:  	01.10.2008 
Версия: 1.000.002
1.000.002: Добавлено HTML представления при выводе ошибки
*/
class CDbException extends Exception
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
Класс для соединения с СУБД MSSQL.
Автор: 	Денис Давыдов
Дата:  	01.10.2008 
Версия: 1.000.001 
*/

class CMSSQLConnect extends CDBConnect
{
	/**
	Свойство определяет есть ли установленное соединение. 
	*/
	private static $instance = false;
	/**
	Константа определяет код ошибки - 5000 - позволен только один объект соединения с СУБД.
	*/
	const ONLY_ONE_INSTANCE_ALLOWED = 5000;
	const CONNECTION_FAILED = 5001;
	const DATABASE_NOT_FOUND = 5002;
	/**
	Конструктор. Проверяет есть ли открытое соединение с БД, если да то генерируется исключение CMSSQLException.
	если нет, то делается попытка соединения с БД. Если соединение установить не удалось то енерируется исключение CMSSQLException.
	*/
	public function __construct($hostname,$dbname,$username,$password)
	{
		parent::__construct();
		//проверяем есть ли открытое соединение.
		if(self::$instance == false)
		{
			if(!$this->connection = pg_connect("host=".$hostname." dbname=".$dbname." user=".$username." password=".$password.""))
		    {
			  throw  new CDbException('Не удаётся соединится с сервером баз данных: '.$hostname.'. Postgresql Server: '.pg_last_error(),self::CONNECTION_FAILED);
		    }
			else
			{
			  self::$instance = true;
			}
		}
		else
         {
			$msg = 'Для создания нового соединения с СУБД необходимо закрыть существующий.';
			throw new CDbException($msg,self::ONLY_ONE_INSTANCE_ALLOWED);
		}		 
	}
	/**
	Деструктор. Закрывает существующее соединение.
	*/
	public function __destruct()
	{
	  $this->close();
	  parent::__destruct();
	}
    
	/**
	Метод сравнивает имя базы данных name c текущем именем, если они не равны то выбирается новая база данных.
		
    public function setDatabase($name)
	{
		if($this->databaseName != $name)
		{
			$this->databaseName = $name;
			//конектимся к БД.
			if(!@pg_select_db($name))
				throw new CDbException('Указанная база данных не найдена.'.  pg_last_notice(),self::DATABASE_NOT_FOUND);
		}
	}
    */
	/**
	Метод возвращает объект класса СMSSQLResultSet
	*/
	public function createResultSet($strSQL)
	{
      $rs = new CMSSQLResultSet($strSQL, $this->databaseName, $this->connection );
      return $rs;
     }
	/**
	Метод закрывает текущее соединение.
	*/
	public function close()
	{
		if(isset($this->connection))
		{
			pg_close($this->connection);
			unset($this->connection);      
		}
		self::$instance = false;
	}
	/////////////////////////////////////////////////////////////////////////////////////
	public function __toString()
	{
		$str = parent::__toString();
		
		return $str;
	}
}
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

class CMSSQLResultSet extends CDBResultSet
{
  
  const QUERY_FAILED = 5003;
 ///////////////////////////////////////////////////////////////////////////////////// 
  /**
  Конструктор. В качестве параметров принимает строка sql - запроса, имя базы данных, и идентификатор 
  соединения с СУБД. Даллее выполняется запрос, если запрос выполнен то результат сохраняет в свойстве 
  result.иначе генерируется исключение.
   */  
  public function __construct($strSQL, $databasename,$connection)
  {
    $this->strSQL = $strSQL;
    $this->connection = $connection;
    $this->databasename = $databasename;
	//исполняем запрос.
	if(!$this->result = pg_query($connection,$strSQL))
	{
      throw new CDBException('Ошибка исполнения запроса.PG Server: '.  pg_last_notice(), self::QUERY_FAILED);
    }
	//инициализация итератора.
	$this->rewind();
  }
////////////////////////////////////////////////////////////////////////////////////  
 /** 
  Деструктор.
  */
  public function __destruct()
  {
    $this->close();
	parent::__destruct();
  }
/////////////////////////////////////////////////////////////////////////////////////
  /**
  Возвращает количество колонок в текущем наборе данны.
  Только для запросов на выборку.
  */
  public function getNumberColumns()
  {
    return pg_num_fields($this->result);
     	
  }
/////////////////////////////////////////////////////////////////////////////////////
  /**
  Возвращает колиство строк результирующего набора.
  Только для запросов на выборку.
  */
 public function getNumberRows()
 {
    return pg_num_rows($this->result);
 }  
////////////////////////////////////////////////////////////////////////////////////  
  /**
  Возвращает индентификатор результирующего набор данных.
  */
  public function getResult()
  {
    return $this->result;
  }
////////////////////////////////////////////////////////////////////////////////////  
  /**
  Возвращает массив с названием полей результирующего набора.
  */
  public function getFieldsNames()
  {
	$size = @pg_num_fields($this->result);
	$arr = array();
	for ( $i = 0; $i < $size; $i ++ )
	{
		$arr[] = pg_field_name ( $this->result, $i );
	}
	return $arr;
  }
///////////////////////////////////////////////////////////////////////////////////  
  /**
  Возвращает массив объектов, объекты содержат описание полей. $fieldOffset - смещение индекса полей от начала
  */
  public function getFieldsProperty($fieldOffset = null)
  {
	$size = @pg_num_fields($this->result);
	$arr = array();
	if ( $fieldOffset != null && $fieldOffset <= $size )
	{
		$arr[] = pg_fetch_field( $this->result, $fieldOffset);
	}
	else
	{
		for ( $i = 0; $i < $size; $i ++ )
		{
			$arr[] = pg_fetch_field( $this->result, $i );
		}
	}
	return $arr;	
  }	
/////////////////////////////////////////////////////////////////////////////////// 
 /**
  Закрывает текущий запрос.
  */ 
 public function close()
  {
	if(isset($this->result))
	{
      @pg_free_result($this->result);
      unset($this->result);
    }
  }
//////////////////////////////////////////////////////////////////////////////////
  /**
  Возвращает текущую строку результирующего запроса.
  */
  public function current ()
  {
	 return $this->currentrow;
  }
//////////////////////////////////////////////////////////////////////////////////   
   /**
   Возвращает текущее значение индекса.
   */
   public function key()
   {
    return $this->key;
   }
/////////////////////////////////////////////////////////////////////////////////   
  /**
   Смещается на следующую строку результирующего запроса.
   */
  public function next()
  {
    if($this->currentrow = @pg_fetch_array($this->result))
	{
      $this->valid = true;
      $this->key++;
    }
	else
	{
      $this->valid = false;
    }
  }
/////////////////////////////////////////////////////////////////////////////////
  /**
   Смещается на предыдущую строку результирующего запроса.
  */
  public function previous()
  {
  	  
	  if(@pg_result_seek($this->result, --$this->key))
	  {
        $this->valid = true;
        $this->currentrow = pg_fetch_array($this->result);
      }
      else
      {
	    $this->valid = false;
	    $this->currentrow = null;
	  }	  

  }
//////////////////////////////////////////////////////////////////////////////////  
  /**
   Устанавливает итератор на начало результирующего запроса.
  */
  public function rewind ()
  {
    if($num = pg_num_rows($this->result) > 0)
	{
      if(pg_result_seek($this->result, 0))
	  {
        $this->valid = true;
        $this->key = 0;
        $this->currentrow = pg_fetch_array($this->result);
      }
    }
	else
	{
      $this->valid = false;
    } 
  }
////////////////////////////////////////////////////////////////////////////////// 
  /**
  Возвращает значение свойства valid
  */  
  public function valid()
  {
    return $this->valid;
  }
////////////////////////////////////////////////////////////////////
  public function __toString()
  {
	$str = '';
	if($this->valid) $str .=  '<b>Valid:true</b>';else  $str .= '<b>Valid:false</b>';
	$str .= '<br>';
	$str .= '<b>Current Row</b><br>';
	foreach ($this->currentrow as $key => $value)
		$str .=	'['.$key.'] = '.$value.'&nbsp;&nbsp;&nbsp;';
	$str .= '<br>';	
	$str .=  '<b>Key :<b/>'.$this->key;
	
	return $str;
  }
}

/**
Класс для создания соединения  с СУБД.
Автор: 	Денис Давыдов
Дата:  	01.10.2008 
Версия: 1.000.001 
*/

class CDBFactory
{
	public function __construct ()
	{
		
	}
	/**
		Метод создаёт экзэмпляр класса для соединения с БД указанного типа type, и возвращает его
	*/
	public function getConnection( $type, $hostName,$dbname,$userName,$password )
	{
		switch ( $type )
		{
			case 'pg':
						$conn = new CMSSQLConnect($hostName,$dbname,$userName,$password);
						return $conn;
			break;
			case 'mysql':
			break;
				
			default:
				throw new CDBException('Неудаётся создать указаный тип соединения',6000 );
			break;	
		}
	}
} 

?>