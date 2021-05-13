<?php
/**
<br>
Версия 1.0. Дата создания 31.01.2008. Последние изменения 31.01.2008.<br>
Является бызовым классом для всех видов форм <br>
Определяет набор общих для всех форм свойства,
а также методы для создания параметров форм.
*/
abstract class CForm
{
  /** Имя формы, будет назначено атрибутам id и name тега form. <br>Если null то атрибуты не будут указаны.
  <br> По умолчанию null.
  */
  protected $name = null;
  /** Url куда будут передаваться параметры. <br>Если null то атрибут action  не будет указан.
  <br> По умолчанию null.
  */
  protected $actionUrl = null;
  /** Занчение скрытого поля act, для передачи значения действия. <br>Если null то поле  не будет указано.
  <br> По умолчанию null.
  */
  protected $method = 'POST';
  /** Если данное свойство true тогда форма является самостоятельной иначе форма является частью другой.
  <br> По умолчанию true.
  */
  protected $standAlone = true;
  /** Массив с характеристиками полей формы. */
  protected $fields = array();
  /** Тип формы. */
  protected $type = null;
  /** Дополнительные параметры. */
  protected $extend = null;
  /** Текст ошибки внутренней ошибки класса. */
  protected $error = null;
  
  
   /** Конструктор формы. Для понимания параметров см. описание свойств*/
  public function __construct($stand_alone = true,$name='',$action_url='',$type='',$method='POST',$extend='')
  {
      $this->standAlone 	=  $stand_alone;
	   $this->name  		=  $name;
	   $this->actionUrl     =  $action_url;
	   if($method != 'POST' && $method != 'GET')
			$this->method		=  'POST';
		else
		   $this->method = $method;
	  $this->extend = $extend;   
	  $this->type = $type;	   
  }
  /////////////////////////////////////////////////
   /** Деструктор формы.*/
  public function __destruct()
  {
     unset($this->Fields);
  }
  /////////////////////////////////////////////////
  public function __toString()
  {
    $msg = '<ol>';
	$msg .= '<li>name = '.$this->name.'</li>';
	$msg .= '<li>actionUrl = '.$this->actionUrl.'</li>';
	$msg .= '<li>standAlone ='.($this->standAlone) ? 'yes':'no'.' </li>';
	$msg .= '<li>type = '.$this->type.'</li>';
	$msg .= '<li>error = '.$this->error.'</li>';
	$msg .= '<li>method = '.$this->method.'</li>';
	$msg .= '</ol>';
	$msg.= '--------Поля------- <br>';
	foreach ($this->fields as $key => $value)
	{
	    $msg .= '<br>'.$key.'<br>';
		 $msg .= 'Тип: '. $value['type'].'<br>';
		 $msg .= 'Значение: '. $value['value'].'<br>';
		 $msg .= 'Дополнит.: '. $value['extend'].'<br>';
	}
	
	return $msg;
  }
  /////////////////////////////////////////////////
  /**
   Добовляет в массив fields поле input c именени и id равными name типа type.<br>
   Параметр extend является дополнительным, в нем можно указать дополнительные атрибуты.
  */
  public function addInputField($name,$type,$value,$extend = '')
  {
     if(!array_key_exists($name,$this->fields))
	 {
	    $this->fields[$name]['type']  	= $type;
		$this->fields[$name]['value'] 	= $value;
		$this->fields[$name]['extend'] 	= $extend;
	 }
  }
  /////////////////////////////////////////////////
  /*
   Добовляет в массив fields поле select c имененем и id равными name типа select.<br>
   Параметр source_type указывает какой тип источника элементов списка использовать, может быть 
   два типа: query - используется строка запроса (параметр source) для получения данных ,static -
   в параметре source нужно передать строку с тегами option.<br> 
   Только если source_type = query:<br>
   field_match - имя поля в БД по которому будет искаться совпадение со значением  value_match. Если совпадение
   найдено то элемет списка будет выделен.
   <br>
   Параметр extend является дополнительным, в нем можно указать дополнительные атрибуты.
  */
  public function addListField($source_type,$source,$name,$field_match,$value_match,$extend = '')
  {
      if($source_type == 'query')
	  {
	     //$res = @mssql_query($source);
	  }
	  else if($source_type == 'static')
	  {
	     $options = explode(';',$source);
		 $size = sizeof($options);
		 $opt = ''; 
		 for ( $i = 0; $i < $size; $i ++)
		   $opt .= $oprions[$i];
		 if($opt != '')
         {
		    $this->fields[$name]['type']  = 'list';
			$this->fields[$name]['value'] = $opt;
			$this->fields[$name]['extend'] 	= $extend;
		 }		
		 
	  }
	  else
	  {
	     $this->$error = 'CForm :: ListField: Неопределённый тип источника';
	  }
  }
  
  public function addTextArea($name,$row,$cols,$extend = '')
  {
  }
  
  protected function start()
  {
     if($this->standAlone)
	 {
	   $html = '<form ';
	   if($this->type != '')
	      $html .= ' type="'.$this->type.'" ';
	   if($this->name != '')
	    	$html .= ' id="'.$this->name.'" name="'.$this->name.'" '; 
		if($this->actionUrl != '')
	    	$html .= ' action="'.$this->actionUrl.'" ';
		$html .= ' method="'.$this->method.'" ';
	   	if($this->extend != '')
	    	$html .= ' '.$this->extend.'" ';
		 	
	   $html .= '>';
	   
	   return $html;
	 }  
	 
	 return null;
  }
  protected function end()
  {
    return ($this->standAlone) ? '</form>' : null;
  }
  public function  getHtmlOfField($fieldname)
  {
     if(array_key_exists($fieldname,$this->fields))
	 {
	   $field = $this->fields[$fieldname];
	   $html = '';
	   if($field['type'] == 'list')
	   {
	   }
	   else if ($field['type'] == 'textarea')
	   {
		
	   }
	   else  
	   {
		 $html .= '<input id="'.$fieldname.'" name="'.$fieldname.'" type="'.$field['type'].'" value="'.$field['value'].'" '.$field['extend'].'>';
	   }
	   return $html;
	 }
	 else
	 {
	   $this->error = 'CForm :: getHtmlOfField: Поле не найдено';
	 }
  }
  
  abstract function renderForm();
  
} 
/**
Версия 1.0. Дата создания 31.01.2008. Последние изменения 31.01.2008.<br>
Класс формы ошибок. Отображает окно с текстом ошибки
*/
class CErrorForm extends CForm
{
    /** Текст выводимой ошибки */
	private  $errorText = '';
	/** error_text - текст ошибки <br>
		action_value - значение скрытого поля, для определения действия при переходе на другую страницу
       onkeypress - действие при отправки формы. (js обработчик или код)		
	*/
	public function __construct($error_text,$name='errorForm',$action_url,$action_value,$onkeypress='')
	{
	  parent::__construct(true,$name,$action_url,null,'POST');
	  $this->errorText = $error_text;
      //создаём поля
	  $this->addInputField('act','hidden',$action_value);
	  $this->addInputField('errortext','hidden',$error_text);
	  $extend = ' style="border:1px solid silver;font-family:Verdana;font-size:11px;color:black;width:100px" ';
	  if($onkeypress=='')
	    $extend .= ' onclick="document.'.$name.'.submit()"';
	  else
        $extend .= $onkeypress;	  
	  
	  $this->addInputField('subbt','button','OK',$extend);	  
	}
	public function __destruct()
	{
	  parent::__destruct();
	}
	/////////////////////////////////////////////////////////////////
	/**
	метод для отображения формы
	*/
	public function renderForm()
	{
	  
	   $r =  $this->start();
	   $r .= $this->getHtmlOfField('act');
	   $r .= $this->getHtmlOfField('errortext');
	   $r .= '<table  cellpadding="3" cellspacing="0" width="40%" align="center" style="border:1px solid #f95d5d">';
	   $r .= '<tr>';
	   $r .= '<td width="2%" bgcolor="#f95d5d"><img src="images/fatal_error.gif'.'"></td><td bgcolor="#f95d5d" ><span style="font-family:Verdana;font-size:12px;font-weight:bold;color:white">Ошибка</span></td>';
	   $r .= '</tr>';
	   $r .= '<tr>';
	   $r .= '<td colspan="2"><span style="font-family:Verdana;font-size:11px;color:black">'.$this->errorText.'</span></td>';
	   $r .= '</tr>';
	   $r .= '<tr>';
	   $r .= '<td colspan="2" align="center">'.$this->getHtmlOfField('subbt').'</td>';
	   $r .= '</tr>';
	   $r .= '</table>';
	   $r .= $this->end(); 	   
	  return $r;
	}
}

class CEmptyForm extends CForm
{
   public function __construct($stand_alone = true,$name='',$action_url='',$type='',$method='POST',$extend='')
   {
      parent::__construct($stand_alone,$name,$action_url,$type,$method,$extend);
   }
   public function __destruct()
   {
     parent::_destruct();
   }
   public function  renderForm()
   {
   }
}


?>