<?php
/**
Абстрактный класс CComponent является базовым
для всех классов, которые предназначены для 
создания объектов web интерфейса (кнопки,панели кнопок и т.д).  
*/
abstract class CComponent
{
  /**
  Ассоциативный массив для хранения свойств объекта. 
  Ключом элемента массива является имя html свойства элемента, например (html свойство: align="center")
  align - ключ элемента массива, center - значения.
  По умолчанию массив пустой. 
  Все свойства применяетются к html элементу-контейнеру, а если такого нет то к самомому html элементу.
  */
  protected $properties;
  /**
  Ассоциативный массив для хранения обработчиков событий объекта. 
  Ключом элемента массива является имя события (onclick ....), значением - строка с js обработчиком события.
  По умолчанию массив содержит три имени события:onclick,onmouseover,onmouseout. 
  Все события применяетются к html элементу-контейнеру, а если такого нет то к самомому html элементу.
  */
  protected $events;
  /**
  Имя используется для идентификации объекта. 
  */
  protected $name;
  /**
  Переменная хранит сгенирированый html код объекта. По умолчанию null.
  */
  protected $view = null;
  /**
	Массив для хранения специфических настроек потомков данного класса.
  */
  protected $options;
  /**
  Конструктор инициализирует массивы свойств и событий.
  */
 
  public function __construct($name)
  {
    $this->name = $name;
	//инициализация массивов
	$this->properties = array();
	$this->options = array();
	$this->events = array();
	$this->events['onclick'] = null;
	$this->events['onmouseover'] = null;
	$this->events['onmouseout'] = null;
  }
  /**
  Деструктор.
  */
  public function __destruct()
  {
     unset($this->name);
	 unset($this->view);
	 unset($this->options);
	 unset($this->properties);
	 unset($this->events);
  }
  /**
  Добовляет в массив events новое событие name (если такого не существует)  и его обработчик value.
  По умолчанию value = null. Если событие существует то метод вернёт null  
  */
  public function addEvent($name,$value=null)
  {
     if(!array_key_exists($name,$this->events))
	 {
		$this->events[$name] = $value;
	 }
	 else
		return null;
	 
  }
  /**
  Добовляет в массив properties новое свойство name (если такого не существует) и его значение value.
  По умолчанию value = null. Если свойство существует то вернёт null 
  */
  public function addProperty($name,$value=null)
  {
     if(!array_key_exists($name,$this->properties))
	 {
		$this->properties[$name] = $value;
	 }
	 else
		return null;
  }
  /**
  Возвращает значение свойства name если такое значение сущиствует и null в противном случае. 
  */
  public function getProperty($name)
  {
     if(array_key_exists($name,$this->properties))
	    return $this->properties[$name];
     else
	   return null;
  }
   /**
  Устанавливает новое значение value для свойства name если такое свойство существует, если нет то вернёт null.
  */
  public function setProperty($name,$value)
  {
     if(array_key_exists($name,$this->properties))
	    return $this->properties[$name] = $value;
     else
	   return null;
  }
   /**
  Возвращает значение name из массива options  если такое значение сущиствует и null в противном случае. 
  */
  public function getOption($name)
  {
     if(array_key_exists($name,$this->options))
	    return $this->options[$name];
     else
	   return null;
  }
  /**
  Устанавливает новое значение value для опции name если такое свойство существует, если нет то вернёт null.
  */
  public function setOption($name,$value)
  {
     if(array_key_exists($name,$this->options))
	    return $this->options[$name] = $value;
     else
	   return null;
  }
  /**
  Возвращает строку, содержащию все свойства в формате: имя0 = "значение0"...имяN = "значениеN".
  */
  protected function getAllProperties()
  {
    $prop = null;
	foreach($this->properties as $key=>$value )
	{
	   if($value != null)
			$prop .= $key.'="'.$value.'"';
	}
	return ' '.$prop;	
	
  }
  /**
  Возвращает обработчик события name если такое событие существует в противном случае null
  */
  public function getEventListener($name)
  {
     if(array_key_exists($name,$this->events))
	    return $this->events[$name];
     else
	   return null;
  }
  /**
  Устанавливает новое значение value для обработчика события name если такое событие существует, если нет то вернёт null.
  */
  public function setEventListener($name,$value)
  {
    if(array_key_exists($name,$this->events))
	     $this->events[$name] = $value;
     else
	   return null;
  }
   /**
  Возвращает строку, содержащию события в формате: имя_события0 = "обработчик0"...имя_событияN = "обработчикN".
  */
  protected function getAllEventListeners()
  {
    $listeners = null;
	foreach($this->events as $key=>$value )
	{
	   if($value != null)
			$listeners .= $key.'="'.$value.'"'; 
	}
	return ' '.$listeners; 
  }
  protected function setName($name)
  {
	$this->name = $name;
  }
  /**
  Метод предназначен для генерации html кода объекта. 
  Метод присваивает строку с сгенерированым кодом переменной view после чего возвращает её.
  Должен быть определён в каждом производном классе.
  */
  abstract function render();
}

class CBox extends CComponent
{
    private $conetent = null;
	
	
	public function __construct($name,$content='')
	{
		parent::__construct($name);
		$this->content = $content;
	}
	
	public function __destruct()
	{
		parent:: __destruct();
	}
	public function appendContent($content)
	{
		$this->content .= $content;
	}
	
	public function render()
	{
		$this->view = '<div id="'.$this->name.'"'.$this->getAllProperties().$this->getAllEventListeners().'>'.$this->content.'</div>';
		return $this->view; 
	}
}
?>
