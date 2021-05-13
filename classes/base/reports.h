<?php
/**
<br>
Версия 1.0. Дата создания 30.01.2008. Последние изменения 01.02.2008.<br>
Является бызовым классом для всех классов отчётов.<br>
*/
abstract class CReport
{
	/** Массив для названия полей в БД, которые должны отображаться*/
	protected $fields = array();
	/** Массив для условий по которым будет выполнятся выборка из БД<br>
        В качестве элементов содержит так же массивы вида: value = значение,type - тип 
	*/
	protected $parameters = array();
	/** Текст sql - ошибки при выборке*/
	protected $dataerror = null;
	/** 
	  Массив для хранения информации об отчёте.<br>
	*/
	protected $reportinfo = array();
	/** Результат sql - запроса */
	protected $data = null;
	//protected $reportinfo = array();
	/** Конструктор.*/
	public function __construct($report_name='Отчёт')
	{
	  $this->reportinfo['Тип'] = $report_name;
	  $this->reportinfo['Создан'] = date("d.m.Y H:i:s");
	  
	}
	/////////////////////////////////
	/** Деструктор.*/
	public function __destruct()
	{
	   unset($this->showedfields);
	   unset($this->dbfields);
	   unset($this->parameters);
	   unset($this->dataerror);
	   unset($this->reportinfo);
	   if($this->data!=null)mssql_free_result($this->data);

	}
	//////////////////////////////
	/** Для Отображения класса при операции echo class_object.*/
	public function __toString()
	{
	   $t = '';
	   $t .= 'Отображаемые поля:<br>';
	   foreach ($this->showedfields  as $key => $value)
       {
	      $t .= '<ul>';
		  $t .= '<li>'.$key.' - '.$value.'</li>';
		  $t .= '</ul>';
       }
	  $t .= 'Поля базы данных:<br>';
	   foreach ($this->dbfields  as $key => $value)
       {
	      $t .= '<ul>';
		  $t .= '<li>'.$key.' - '.$value.'</li>';
		  $t .= '</ul>';
       }
       $t .= 'Фильтр:<br>';
	   foreach ($this->parameters  as $key => $value)
       {
	      $t .= '<ul>';
		  $t .= '<li>'.$key.' - '.$value['value'].'-'.$value['type'].'</li>';
		  $t .= '</ul>';
       }
      $t .= 'Информация об отчёте:<br>';
	   foreach ($this->reportinfo  as $key => $value)
       {
	      $t .= '<ul>';
		  $t .= '<li>'.$key.' - '.$value.'</li>';
		  $t .= '</ul>';
       }	   
       return $t;	   
	}
	/////////////////////////////
	/** 
	 Добовляет поле отчёта  name_showed_field в showedfields<br>
	 и название поля в БД  $name_field_in_db в dbfields
	*/
	
  public function addField($name_field_in_db,$name_showed_field)
  {
	 if(!array_key_exists($name_field_in_db,$this->fields))
	 {
	    $this->fields[$name_field_in_db] = $name_showed_field;
	 }
  }
	///////////////////////////////////////////////
	/** 
	 Сохраняет в массив parameters условие выборки под именем $name со значением $value
	 и типом значения $type если такого условия не существует. <br> $type - может равнятся string (строка,дата) или digit (числа, а так же null)  
	*/
	public function setParameter($name,$value,$type)
	{
	   if(!array_key_exists($name,$this->parameters) && ($type == 'string' || $type == 'digit'))  
	   { 
	     $this->parameters[$name]['value'] = $value;
		 $this->parameters[$name]['type'] = $type;
	  }
      ///print_r($this->filter);echo '<br>';	  
	}
	//////////////////////////////////////////////
	/** 
	 Изменяет значение параметра $name на значение $value и тип значения $type если такой существует.
	*/
	public function changeParameter($name,$value,$type)
	{
	  if(array_key_exists($name,$this->parameters) && $this->parameters[$name]!=null) 
	  { 
	    $this->parameters[$name]['value'] = $value;
		$this->parameters[$name]['type'] = $type;
	  }	
	}
	///////////////////////////////////////////////
	/** 
	 Удаляет параметр $name из массива $parameters
	*/
	public function removeParameter($name)
	{
	   if(array_key_exists($name,$this->parameters)) $this->parameters[$name] = null;
	}
	///////////////////////////////////////////////
	/** 
	 Возвращает параметры как строку разделённую запятыми, отбрасывает параметры, имена которых указаны 
	 в  аргументах метода 
	*/
	public function getStringParametersWithout()
	{
	   $args = func_get_args();
	   $str = '';
	   foreach ($this->parameters as $key => $value)
	   {
	     if($value !=null && !in_array($key,$args))
		  {
			if($value['type'] == 'string')
				$str .= '"'.$value['value'].'",' ;
			else if($value['type'] == 'digit')
				$str .= $value['value'].',' ;
		 }
	   }
	  return $str = substr($str,0,strlen($str)-1);
	}
	//////////////////////////////////////////////
	/**
	Выполняет запрос $query.При удачном выполнении возвращает результирующий набор иначе null и сохраняет ошибку в свойстве dataerror. 
	*/
	public function getResultSetFromQuery($query)
	{
	   $this->data = @mssql_query($query);
       if(!$this->data) 
	   { 
	     $this->data = null;
		 $this->dataerror = @mssql_get_last_message();
       }
        return $this->data;	   
	}
	/////////////////////////////////////////////////
	/** 
	 Вызывает процедуру с именем $proc_name с параметрами казаныыми в parameters.<br>
	 Метод расположит параметры в том порядке, в каком они следуют в parameters.<br>
	 Поэтому при добавлении параметров нужно учитывать порядок их следования в вызываемой
	 процедуре.
	 При удачном выполнении возвращает результирующий набор иначе null и сохраняет ошибку в свойстве dataerror.
	*/
	public function  getResultSetFromProc($proc_name)
	{
	   //создание запроса
	   $params = '';
	   $sql = '';
	   foreach ($this->parameters  as $key => $value)
       {
	      if($value !=null)
		  {
			if($value['type'] == 'string')
				$params .= '"'.$value['value'].'",' ;
			else if($value['type'] == 'digit')
				$params .= $value['value'].',' ;
		 }		
       }
	   $params = substr($params,0,strlen($params)-1);
	   $sql = $proc_name.' '.$params;
      //echo $sql.'<br>';	   
	   $this->data = @mssql_query($sql);
       if(!$this->data) 
	   { 
	     $this->data = null;
		 $this->dataerror = @mssql_get_last_message();
       }
        return $this->data;	   
	}
	////////////////////////////////////////////////////////
	/** 
	 Вызывает деструктор.Используется если нужно освободить ресурсы раньше чем это сделает "сборщик мусора"
	*/
	public function close()
	{
	   self::__destruct();
	}
	public function setReportInfo($name,$value)
	{
	  if(!array_key_exists($name,$this->reportinfo)) 
	     $this->reportinfo[$name] = $value;
	}
	/////////////////////////////////////
	/** 
	  Метод для отображения начала отчёта . Должен быть определён в производных классах
	  excel - true - если вывод идёт в Excel, className(используется только елсли вывод в броузер) - имя css класса, применяемый длявида отчёта.
	*/
	abstract function start($excel,$className);
	/** Метод для отображения окончания отчёта . Должен быть определён в производных классах*/
	abstract function end();
	/** Метод для отображения набора данных .Вызывается после методов получающие данные. Должен быть определён в производных классах*/
	abstract function renderResultSet();
	
	/////////////////////////////////////
	/** Метод отображения информации о отчёте. Должен быть определён в производных классах*/
	abstract function getReportInfo();
	/** Метод отображения ошибки. Должен быть определён в производных классах*/
	abstract function riseError();
	
}

class CSimpleReport extends CReport
{
   public function __construct($name)
   {
      parent::__construct($name);
   }
   public function __destruct()
   {
      parent :: __destruct();
   }
   public function start($excel,$className)
   {
     $style = ($excel) ? 'border=1' : 'class="'.$className.'"';
	 $str = '';
	 $str .= '<br><table '.$style.' cellpadding="0" cellspacing="0" width="100%" >';
	 //show report headers
	 $headers = '<tr>';
	 foreach($this->fields as $value )
	   $headers .= '<th >'.$value.'</th>';
	 
	 $headers .= '</tr>';
	 $str .= $headers;
	 echo $str;
   }
   public function end()
   {
     echo '</table>';
   }
   public function renderResultSet()
   {
      
	  //processing error
	  if($this->data == null)
	  { 
    	 echo  '<tr><td  colspan="'.sizeof($this->fields).'">'.$this->dataerror.'</td></tr>';
	  }
      //processing empty set
	  else if (mssql_num_rows($this->data) == 0)
     {
	     echo  '<tr><td  colspan="'.sizeof($this->fields).'">Нет данных.</td></tr>';
	  }	 
	  else
	  {
		
		$size = sizeof($this->fields);
		
		while($r = mssql_fetch_array($this->data))
       {
	      $str = '<tr>'; 
		   foreach ($this->fields as $key=>$value)
			{
  				$str .= '<td >'.$r[$key].'</td>';
		    }
		  $str .= '</tr>';
         echo $str;		  
		}	
	  }
   }
   public function getReportInfo()
   {
      $thCss = 'style="font-family:Verdana;text-align:left;font-size:12px;font-weight:bold;padding:2px;border-bottom:1px solid black;"';
	   $tdCss = 'style="font-family:Verdana;text-align:left;font-size:12px;padding:2px;padding-left:15px;border-bottom:1px solid black;"';
	  
	  $i  = '<table border="0" cellpadding="0" cellspacing="0" >';
	  foreach($this->reportinfo as $key=>$value)
	  {
		$i .= '<tr>';
		$i .= '<th '.$thCss.'>'.$key.':</th><td '.$tdCss.'>'.$value.'</td>';
		$i .= '</tr>';
	  }
	  $i .= '</table>';
	  
	  return $i;
   }
   public function riseError()
   {
      return '<center>Ошибка:'.$this->dataerror.'</center>';
   }
}

?>