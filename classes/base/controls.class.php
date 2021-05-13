<?php
/*
Файл содержит классы элементов управления
*/

require_once('component.class.php');
/**
Класс кнопки.
*/
class CButton extends CComponent 
{
  /**
  Тип создаваемой кнопки. Может иметь одно из трёх значений:
  SIMPLE - стандартная кнопка.
  IMAGE - картинка или иконка выполняющая роль кнопки.
  LINK - ссылка выполняющая роль кнопки.
  */
  private $type = null;
  /**
  Значение кнопки. Для разных типов кнопок value используется по-разному.
  Для type = SIMPLE - текст написанный на кнопке.
  Для type = IMAGE - путь к файлу с картинкой.
  Для type = LINK - текст ссылки.
  */
  private $value = null;
  
  /**
  Конструктор инициализирует значения type и name а также вызывает конструктор базового класса.
  */
  public function  __construct($type,$name,$value='null')
  {
    $this->type = $type;
	$this->value = $value; 
     parent::__construct($name);
	 
  }
  /**
  Деструктор.
  */
  public function __destruct()
  {
    parent::__destruct();
  }
  /**
  Генерирует и возвращает html  код создаваемой кнопки в зависимости от типа кнопки.
  Для каждого типа кнопок устанавливается два обязательных свойства: id и name значения которых
  равны значению переданному в параметре name в конструкторе.
  Для генерации кнопки вызывает один из частных методов класса. 
  */
  public function render()
  {
     switch($this->type)
	 {
	   case 'SIMPLE':
	        $this->addProperty('id',$this->name);
			$this->addProperty('name',$this->name);
			$this->addProperty('type','button');
			$this->addProperty('value',$this->value);
			$this->createSimpleBt(); 
			return $this->view;
	   break;
	   case 'SUBMIT':
	        $this->addProperty('id',$this->name);
			$this->addProperty('name',$this->name);
			$this->addProperty('type','submit');
			$this->addProperty('value',$this->value);
			$this->createSimpleBt(); 
			return $this->view;
	   break;	
	   
	   case 'IMAGE' :
			$this->addProperty('id',$this->name);
			$this->addProperty('name',$this->name);
			$this->addProperty('src',$this->value);
			$this->createImageBt(); return $this->view;
	   break;	
	   case 'LINK' :
			$this->addProperty('id',$this->name);
			$this->addProperty('name',$this->name);
			$this->addProperty('href','#');
			$this->createLinkBt(); return $this->view;
		break;	
	   default:
	   die ('CButton:Неизвестный тип кнопки'); 
	   break;
	   
	 }
  }
  /**
  Генерирует html код для кнопки типа SIMPLE.
  */
  private function createSimpleBt()
  {
    $this->view = '<input ';
	//свойства
	$this->view .= $this->getAllProperties();
	//обработчики событий
	$this->view .= $this->getAllEventListeners();
	$this->view .= '>';
  }
  /**
  Генерирует html код для кнопки типа IMAGE.
  */
  private function createImageBt()
  {
	$this->view = '<div ';
	//свойства
	$p = $this->getProperty('id');
    if ( $p != null )
		$this->view .= ' id="'.$p.'" ';
		
	$p = $this->getProperty('name');
    if ( $p != null )
		$this->view .= ' name="'.$p.'" ';	
	
	$p = $this->getProperty('class');
    if ( $p != null )
		$this->view .= ' class="'.$p.'" ';	
		
	//обработчики событий
	$this->view .= $this->getAllEventListeners();
	
	$this->view .= '>';
	$this->view .= '<img src="'.$this->getProperty('src').'" style="margin:2px">';
	
	$this->view .= '</div>';
  }
   /**
  Генерирует html код для кнопки типа LINK.
  */
  private function createLinkBt()
  {
	$this->view = '<a ';
	//свойства
	$this->view .= $this->getAllProperties();
	//обработчики событий
	$this->view .= $this->getAllEventListeners();
	$this->view .= '>';
	$this->view .= $this->value;
	$this->view .= '</a>';
	
  }
}
/**
Класс для кнопок, состоящих из картинки, слева от которой кнопка в виде ссылки.
*/
class CImageLinkBt extends CComponent
{
	/**
	 Путь к изображению.
     */
	private $imageSrc;
	/**
	 Текст для кнопки ссылки
     */
	private $value;
	/**
	Объект кнопки типа LINK
	*/
	public $linkButton;
	/** 
	Конструктор. Инициализирует переменные. Создаёт объект кнопки тип LINK. 
	*/
	public function __construct($imageSrc,$name,$value)
	{
		$this->imageSrc = $imageSrc;
		$this->name = $name;
		$this->value = $value;
		$this->linkButton = new CButton('LINK',$name,$value);
		
		parent::__construct($name);
	}
	public function __destruct()
	{
	  parent::__destruct();
	}
	/**
	Генерирует html  код. Для контейнера картинки и кнопки используется div. 
	*/
	public function render()
	{
		$this->view = '<div ';
		//свойства
		$this->view .= $this->getAllProperties();
		//обработчики событий
		$this->view .= $this->getAllEventListeners();
		$this->view .= '>';
		$this->view .= '<img src='.$this->imageSrc.' style="float:left;margin:2px 5px 2px 2px" align="absmiddle">';
		$this->view .= '&nbsp;'. $this->linkButton->render().'&nbsp;';
		$this->view .= '</div>';
		
		return $this->view;
	}
}
/**
Представляет собой агрегатный класс для представления
панели, содержащей кнопки различного типа. Панелью является таблица с одной строкой, 
в ячейках которой находятся кнопки.
*/
class CButtonPanel extends CComponent 
{
   /**
   Массив для хранения кнопок.
   */
   private $buttons;
   /**
   Конструктор. Создаёт два свойства для контейнера(table): id и name. 
   */
   public function __construct($name)
   {
 	  parent::__construct($name);
	  $this->addProperty('id',$this->name);
	  $this->addProperty('name',$this->name);
   }
   /**
   Деструктор.
   */
   public function __destruct()
   {
	 parent::__destruct();
   }
   /**
   Геннерирует html код.
   */
   public function render()
   {
      $this->view = '<table style="margin:0px;"';
	 $this->view .= $this->getAllProperties();
	  $this->view .= '>';
	 $this->view .= '<tr>';
	 $size = sizeof($this->buttons);
	 
	 for ( $i = 0; $i < $size; $i++ )
	 {
	   $button = $this->buttons[$i];
	   $this->view .= '<td>'.$button->render().'</td>';
	 }
	 
	 $this->view .= '</tr>';
	 $this->view .= '</table>';
	 return $this->view;
   }
   /**
   Добовляет кнопку buttonObj в массив buttons. buttonObj - объект класа CButton или его производных.
   */
   public function addButton($buttonObj)
   {
     if(is_object($buttonObj))
	   $this->buttons[] = $buttonObj;
   }
   
}

class CTextField extends CComponent
{
   private $value;
   private $type;
   public function __construct($name,$value,$type)
   {
		if($type == 'text' || $type == 'hidden' || $type == 'file')
		{
			$this->name = $name;
			$this->value = $value;
			$this->type = $type;
			parent::__construct($name);
			$this->addProperty('id',$name);
			$this->addProperty('name',$name);
			$this->addProperty('value',$value);
			$this->addProperty('type',$type);
		}
		else
		{
		  parent::__destruct();
		  die('CTextField:'.$name.' - невозможно создать объект. Неверный тип.');
		}	
   }
   public function __destruct()
   {
     parent::__destruct();
   }
   public function render()
   {
     $this->view = '<input ';
	 //свойства
	 $this->view .= $this->getAllProperties();
	//обработчики событий
	 $this->view .= $this->getAllEventListeners();
	 $this->view .= '>';
	 return $this->view;
   }
   
}

class CDropDownList extends CComponent
{
   private $selected = -10000;
   private $items = array();
   public function __construct($name)
   {
			$this->name = $name;
			parent::__construct($name);
			$this->addProperty('id',$name);
			$this->addProperty('name',$name);
   }
   public function __destruct()
   {
      parent::__destruct();
   }
   public function render()
   {
      $this->view = '<select ';
	  //свойства
	 // print_r($this->properties);
	 $this->view .= $this->getAllProperties();
	//обработчики событий
	 $this->view .= $this->getAllEventListeners();
	 $this->view .= '>';
	 //выводим элементы из массива.
	 foreach( $this->items as $key => $value)
	 {
		$selected = '';
		if($this->selected == $key) $selected = 'selected'; else $selected = '';
		
		$this->view .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
	 }
	 
	 //выводим поля из запроса.
	 $this->view .= '</select>';
	 return $this->view;
   }
   public function addItem($option,$value)
   {
	if(!array_key_exists($option,$this->items))
	   $this->items[$option] = $value;
   }
   public function setSelected($value)
   {
	$this->selected = $value;
   }
   public function getSelected()
   {
	return $this->selected;
   }
   public function setDisabled($disabled)
   {
		if($disabled) 
		   $this->addProperty('disabled','disabled');
		else
		   $this->removeProperty('disabled');	
   }
}

class CCheckBox extends CComponent
{
   public function __construct($name)
   {
			parent::__construct($name);
			$this->addProperty('id',$name);
			$this->addProperty('type','checkbox');
			$this->addProperty('name',$name);
   }
   public function __destruct()
   {
     parent::__destruct();
   }
   public function render()
   {
     $this->view = '<input ';
	 //свойства
	 $this->view .= $this->getAllProperties();
	//обработчики событий
	 $this->view .= $this->getAllEventListeners();
	 $this->view .= '>';
	 return $this->view;
   }
   public function setDisabled($disabled)
   {
		if($disabled) $this->addProperty('disabled','disabled');
   }
   public function setChecked($checked)
   {
		
		if($checked) 
		   $this->addProperty('checked','checked');
		else
		   $this->removeProperty('checked');	
   }

}	
/////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////
class CBox extends CComponent
{
    private $content = null;
	
	
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
