<?php

/**
 Версия 1.0 Дата создания 01.02.2008. Последние изминения 01.02.2008.<br>

 Класс для валидации данных 
*/
class CDataValidator 
{
	/** Конструктор */
	public function __counstruct()
	{
	}
	/** Деструктор */
	public function __destruct()
	{
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

/** Версия 1.0. Дата создания 01.02.2008. Последние изминения 01.02.2008.<br>
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
/**
Класс для различных конвертаций данных
Версия 1.0
*/

class CDataConvert
{
	
	static $_utf8win1251 = array(
				"\xD0\x90"=>"\xC0","\xD0\x91"=>"\xC1","\xD0\x92"=>"\xC2","\xD0\x93"=>"\xC3","\xD0\x94"=>"\xC4",
				"\xD0\x95"=>"\xC5","\xD0\x81"=>"\xA8","\xD0\x96"=>"\xC6","\xD0\x97"=>"\xC7","\xD0\x98"=>"\xC8",
				"\xD0\x99"=>"\xC9","\xD0\x9A"=>"\xCA","\xD0\x9B"=>"\xCB","\xD0\x9C"=>"\xCC","\xD0\x9D"=>"\xCD",
				"\xD0\x9E"=>"\xCE","\xD0\x9F"=>"\xCF","\xD0\xA0"=>"\xD0","\xD0\xA1"=>"\xD1","\xD0\xA2"=>"\xD2",
				"\xD0\xA3"=>"\xD3","\xD0\xA4"=>"\xD4","\xD0\xA5"=>"\xD5","\xD0\xA6"=>"\xD6","\xD0\xA7"=>"\xD7",
				"\xD0\xA8"=>"\xD8","\xD0\xA9"=>"\xD9","\xD0\xAA"=>"\xDA","\xD0\xAB"=>"\xDB","\xD0\xAC"=>"\xDC",
				"\xD0\xAD"=>"\xDD","\xD0\xAE"=>"\xDE","\xD0\xAF"=>"\xDF","\xD0\x87"=>"\xAF","\xD0\x86"=>"\xB2",
				"\xD0\x84"=>"\xAA","\xD0\x8E"=>"\xA1","\xD0\xB0"=>"\xE0","\xD0\xB1"=>"\xE1","\xD0\xB2"=>"\xE2",
				"\xD0\xB3"=>"\xE3","\xD0\xB4"=>"\xE4","\xD0\xB5"=>"\xE5","\xD1\x91"=>"\xB8","\xD0\xB6"=>"\xE6",
				"\xD0\xB7"=>"\xE7","\xD0\xB8"=>"\xE8","\xD0\xB9"=>"\xE9","\xD0\xBA"=>"\xEA","\xD0\xBB"=>"\xEB",
				"\xD0\xBC"=>"\xEC","\xD0\xBD"=>"\xED","\xD0\xBE"=>"\xEE","\xD0\xBF"=>"\xEF","\xD1\x80"=>"\xF0",
				"\xD1\x81"=>"\xF1","\xD1\x82"=>"\xF2","\xD1\x83"=>"\xF3","\xD1\x84"=>"\xF4","\xD1\x85"=>"\xF5",
				"\xD1\x86"=>"\xF6","\xD1\x87"=>"\xF7","\xD1\x88"=>"\xF8","\xD1\x89"=>"\xF9","\xD1\x8A"=>"\xFA",
				"\xD1\x8B"=>"\xFB","\xD1\x8C"=>"\xFC","\xD1\x8D"=>"\xFD","\xD1\x8E"=>"\xFE","\xD1\x8F"=>"\xFF",
				"\xD1\x96"=>"\xB3","\xD1\x97"=>"\xBF","\xD1\x94"=>"\xBA","\xD1\x9E"=>"\xA2");
	static	$_win1251utf8 = array(
				"\xC0"=>"\xD0\x90","\xC1"=>"\xD0\x91","\xC2"=>"\xD0\x92","\xC3"=>"\xD0\x93","\xC4"=>"\xD0\x94",
				"\xC5"=>"\xD0\x95","\xA8"=>"\xD0\x81","\xC6"=>"\xD0\x96","\xC7"=>"\xD0\x97","\xC8"=>"\xD0\x98",
				"\xC9"=>"\xD0\x99","\xCA"=>"\xD0\x9A","\xCB"=>"\xD0\x9B","\xCC"=>"\xD0\x9C","\xCD"=>"\xD0\x9D",
				"\xCE"=>"\xD0\x9E","\xCF"=>"\xD0\x9F","\xD0"=>"\xD0\xA0","\xD1"=>"\xD0\xA1","\xD2"=>"\xD0\xA2",
				"\xD3"=>"\xD0\xA3","\xD4"=>"\xD0\xA4","\xD5"=>"\xD0\xA5","\xD6"=>"\xD0\xA6","\xD7"=>"\xD0\xA7",
				"\xD8"=>"\xD0\xA8","\xD9"=>"\xD0\xA9","\xDA"=>"\xD0\xAA","\xDB"=>"\xD0\xAB","\xDC"=>"\xD0\xAC",
				"\xDD"=>"\xD0\xAD","\xDE"=>"\xD0\xAE","\xDF"=>"\xD0\xAF","\xAF"=>"\xD0\x87","\xB2"=>"\xD0\x86",
				"\xAA"=>"\xD0\x84","\xA1"=>"\xD0\x8E","\xE0"=>"\xD0\xB0","\xE1"=>"\xD0\xB1","\xE2"=>"\xD0\xB2",
				"\xE3"=>"\xD0\xB3","\xE4"=>"\xD0\xB4","\xE5"=>"\xD0\xB5","\xB8"=>"\xD1\x91","\xE6"=>"\xD0\xB6",
				"\xE7"=>"\xD0\xB7","\xE8"=>"\xD0\xB8","\xE9"=>"\xD0\xB9","\xEA"=>"\xD0\xBA","\xEB"=>"\xD0\xBB",
				"\xEC"=>"\xD0\xBC","\xED"=>"\xD0\xBD","\xEE"=>"\xD0\xBE","\xEF"=>"\xD0\xBF","\xF0"=>"\xD1\x80",
				"\xF1"=>"\xD1\x81","\xF2"=>"\xD1\x82","\xF3"=>"\xD1\x83","\xF4"=>"\xD1\x84","\xF5"=>"\xD1\x85",
				"\xF6"=>"\xD1\x86","\xF7"=>"\xD1\x87","\xF8"=>"\xD1\x88","\xF9"=>"\xD1\x89","\xFA"=>"\xD1\x8A",
				"\xFB"=>"\xD1\x8B","\xFC"=>"\xD1\x8C","\xFD"=>"\xD1\x8D","\xFE"=>"\xD1\x8E","\xFF"=>"\xD1\x8F",
				"\xB3"=>"\xD1\x96","\xBF"=>"\xD1\x97","\xBA"=>"\xD1\x94","\xA2"=>"\xD1\x9E");

	
	public static function utf8ToWin1251($a)
	{
		if ( is_array( $a ) )
		{
			foreach ($a as $k => $v) 
			{
				if (is_array($v))
				{
					$a[$k] = utf8_win1251($v);
				} 
				else 
				{
					$a[$k] = strtr($v, self::$_utf8win1251);
				}
			}
			return $a;
		} 
		else 
		{
			return strtr($a, self::$_utf8win1251);
		}
	}
	

	public static function win1251ToUtf8($a)
	{
		if (is_array($a))
		{
			foreach ($a as $k=>$v) 
			{
				if (is_array($v)) 
				{
					$a[$k] = utf8_win1251($v);
				} 
				else 
				{
					$a[$k] = strtr($v, self::$_win1251utf8);
				}
			}
			return $a;
		} 
		else 
		{
			return strtr($a, self::$_win1251utf8);
		}
	}
	
	
	/** Вносит необходимые изминения в переданную строку и возвращает ее.<br>
	  Изминения:<br> 
	  Удаляет html теги<br>
	  Удаляет кавычки и проценты<br>
	  Удаляет из заданной строки начальные и конечные пробельные символы.	 
	*/
	public static function toSQLParameter($data)
	{
	   $data = strip_tags($data);
	   $data = str_replace("'","",$data);
	   $data = str_replace('"',"",$data);
	   $data=str_replace("%","",$data);
	   $data=str_replace("\\","",$data);
	   $data=str_replace("//","",$data);
       $data=trim($data);
       return $data;
	}
	
}

?>