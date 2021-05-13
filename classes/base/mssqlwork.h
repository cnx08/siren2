<?php

/**
Класс исключений возникших при работе с СУБД MS SQL SERVER
*/
class CMSSQLException extends Exception
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
    return ('<center><span style="font-family:Tahoma;font-size:15px;font-weight:bold;color:red;">Ошибка: '. $this->code.' - '.$this->message.'</span></center>');
  }
}
/**
Класс для работы с данными, возвращенных в результате запроса.
*/
class CMSSQLResultSet 
{
  /**
  Строка sql запроса.
  */
  private $strSQL;
  /**
  Имя бызы данных.
  */
  private $databasename;
  /**
  Идентификатор соединия.
  */
  private $connection;
  /**
  Результирующий набор данных. 
  */
  private $result;
  /**
   Переменная итератора по строкам. Если текущая строка результируещего набора валидная то valid = true
   иначе false.
  */
  private $valid;
  /**
  Переменная итератора по строкам. Содержит текущую строку. 
  */
  private $currentrow;
  /**
  Значение текущего индекса.
  */
  private $key;
  
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
	if(!$this->result = @pg_query($strSQL, $connection))
	{
      throw new CMSSQLException('Ошибка исполнения запроса.<br>MS SQL Server: '.pg_last_notice(), self::QUERY_FAILED);
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
  }
/////////////////////////////////////////////////////////////////////////////////////
  /**
  Возвращает количество колонок в текущем наборе данны.
  Только для запросов на выборку.
  */
  public function getNumberColumns()
  {
    return @pg_num_fields($this->result);
     	
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
    if($num = mssql_num_rows($this->result) > 0)
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
  public function debug()
  {
	if($this->valid) echo 'Valid:true';else  echo 'Valid:false';
	echo '<br>';
	echo 'Current Row<br>';
	print_r($this->currentrow);
	echo '<br>';
	echo 'Key :'.$this->key;
  }

} 
class CMSSQLConnect
{
	/**
	Свойство определяет есть ли установленное соединение. 
	*/
	private static $instance = false;
	/**
	Идентификатор соединения.
	*/
	private $connection;
	/**
	Имя базы данных.
	*/
	private $databasename;
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
		//проверяем есть ли открытое соединение.
		if(self::$instance == false)
		{
			if(!$this->connection = pg_connect("host=".$hostname." dbname=".$dbname." user=".$username." password=".$password.""))
		    {
			  throw  new CMSSQLException('Не удаётся соединится с сервером баз данных: '.$hostname.'. Postgresql Server: '.pg_last_error(),self::CONNECTION_FAILED);
		    }
			else
			{
			  self::$instance = true;
			}
		}
		else
         {
			$msg = 'Для создания нового соединения с СУБД необходимо закрыть существующий.';
			throw new CMSSQLException($msg,self::ONLY_ONE_INSTANCE_ALLOWED);
		}		 
	}
	/**
	Деструктор. Закрывает существующее соединение.
	*/
	public function __destruct()
	{
	  $this->close();
	}
    /**
	Метод возвращает идентификатор текущего соединения.
	*/	
	public function getConnection()
	{
		return $this->connection;
	}
	/**
	Метод сравнивает имя базы данных name c текущем именем, если они не равны то выбирается новая база данных.
	*/	
    public function setDatabase($name)
	{
		if($this->databasename != $name)
		{
			$this->databasename = $name;
			//конектимся к БД.
			if(!@mssql_select_db($name))
				throw new CMSSQLException('Указанная база данных не найдена.<br>'.mssql_get_last_message(),self::DATABASE_NOT_FOUND);
		}
	}
	/**
	 Возвращает имя текущей базы данных.
	*/
	public function getDatabaseName()
	{
	  return $this->databasename;
	}
	/**
	Метод возвращает объект класса СMSSQLResultSet
	*/
	public function createResultSet($strSQL)
	{
      $rs = new CMSSQLResultSet($strSQL, $this->databasename, $this->connection );
      return $rs;
     }
  /**
  Метод закрывает текущее соединение.
  */
  public function close()
  {
    if(isset($this->connection))
	{
      mssql_close($this->connection);
      unset($this->connection);      
    }
    self::$instance = false;
  }
}

?>