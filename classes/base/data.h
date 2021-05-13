<?php
interface IDataValidator
{
  public function toSQLParameter($data);
  public function isDigitMoreZero($data);
}
/**
Версия 1.0. Дата создания 01.02.2008. Последние изменения 01.02.2008.<br>
 Класс для валидации данных 
*/
class CDataValidator implements IDataValidator
{
	/** Конструктор */
	public function __counstruct()
	{
	}
	/** Деструктор */
	public function __destruct()
	{
	}
	/** Вносит необходимые изменения в переданную строку и возвращает ее.<br>
	  Изминения:<br> 
	  Удаляет html теги<br>
	  Удаляет кавычки и проценты<br>
	  Удаляет из заданной строки начальные и конечные пробельные символы.	 
	*/
	public function toSQLParameter($data)
	{
	   $data=strip_tags($data);
	   $data = str_replace("'","",$data);
	   $data = str_replace('"',"",$data);
	   $data=str_replace("%","",$data);
      $data=trim($data);
      return $data;
	}
   /** Возвращает true если $data яляется числом или строкоой с числовым значением. 
	и больше нуля */
    public function isDigitMoreZero($data)
	{
	  return (is_numeric($data) && $data > 0 ) ? true : false; 
	}
	/** Возвращает true если $data яляется  строкой содержащей  только символы шестнадцатиричных чисел и равна длине, переданной в параметре $len 
	 */
	public function isHexString($data,$len)
	{
	  if(strlen($data) != $len) return false;
	  if(ereg('^[a-fA-F0-9]',$data))
        return true;
      else
        return false;
	}
}

/** Версия 1.0. Дата создания 01.02.2008. Последние изменения 01.02.2008.<br>
Класс предназначен для работы с датой
*/
class CDate
{
   /**
    Дата переданная в конструкторе
   */
   private $date;
   /** Конструктор. Формат даты 'ДД.ММ.ГГГГГ'*/
	public function __construct($date)
	{
	  if(!$this->isDate($date,'.'))
		  die ("СDate: Невозможно создать объект. Некорректный параметр");
	  
	  else
	    $this->date = $date;
	}
	/** Деструктор */
	public function __destruct()
	{
	 unset($this->date);
	}
	/**
	  Метод возвращает дату указаную в date
	*/
	public function get()
	{
	  return $this->date;
	}
	/**
	Метод устанавливает дату
	*/
	public function set($date)
	{
	  return $this->date=$date;
	}
	/**
	 Метод увеличивает дату совдержащуюся в свойстве date на количество дней указанное в d_value,
	 на количество месяцев указанное в m_value и на количество лет указанное в y_value.<br>
	 По умолчанию d_value =  m_value = y_value = 0. <br>
	 Возвращает дату в формате  'ДД.ММ.ГГГГГ'<br>
	*/
	public function toIncrease($d_value = 0,$m_value=0,$y_value=0)
	{
	   @$tmp = explode(".",$this->date);
		if(!@checkdate(@$tmp[1],@$tmp[0],@$tmp[2]))	return 'failure';
			
		$this->date = date ("d.m.Y",mktime(0,0,0,$tmp[1]+$m_value,$tmp[0]+$d_value,$tmp[2]+$y_value));
		return $this->date;
	}
	/**
	 Работает так же как и метод  toIncrease, только уменьшает указанную дату 
	*/
    public function toDecrease($d_value = 0,$m_value=0,$y_value=0)
	{
		 @$tmp = @explode(".",$this->date);
		if(!@checkdate(@$tmp[1],@$tmp[0],@$tmp[2]))	return null;
			
		$this->date = date("d.m.Y",mktime(0,0,0,$tmp[1]-$m_value,$tmp[0]-$d_value,$tmp[2]-$y_value));
		return $this->date;
	}
   /**
    Сравнивает свойство date с параметром date если свойство date больше параметра date тогда вернёт true иначе  false
   */
	public function compare($date)
	{
	  //echo $date.'<br>';
	 // $tmp1 = explode(".",$this->date);
	 // $tmp2 = explode(".",$date);
	 // $s1 = $tmp1[1].'.'.$tmp1[0].'.'.$tmp1[2];
	 // $s2 = $tmp2[1].'.'.$tmp2[0].'.'.$tmp2[2];
	 // echo $s1.'<br>'.$s2.'<br>';
	  $d1 = strtotime($this->date);
	  $d2 = strtotime($date);
	 // echo $d1.'<br>'.$d2.'<br>';
	  
	  if($d1 > $d2) return true; else return false;
	}
	/**
    Проверяет корректность и существование даты
   */
	private function isDate($date,$spliter)
	{
		if(substr_count($date,$spliter)!=2)return false;
		$_DATE = explode($spliter,$date);
		$d = $_DATE[0];
		$m = $_DATE[1];
		$y = $_DATE[2];
		
		if(!is_numeric($d) || !is_numeric($m) || !is_numeric($y)) return false;
		
		if(checkdate($m,$d,$y)==true)return true;
		else return false;
	}
}
?>