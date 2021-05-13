<?php

require_once('component.class.php');

/**
  Класс ячейки списка. Представляет собой ячейку html таблицы
  Автор Давыдов Денис
  Версия: 1.000.000
*/  
class CItem extends CComponent
{
	private $value;
	/**
		Конструктор создаёт опцию renderType которая будет доступна после создания.
		Данная опция отвечает за отображения контента ячейки. По умолчанию renderType = string
	*/
	public function __construct($name,$value='')
	{
		parent::__construct($name);
		$this->options['renderType'] = 'string'; //string or icon,
		//$this->options['iconSrc'] = null;//reserved
		$this->value = $value;
	}
	/**
		Деструктор.
	*/
	public function __destruct()
	{
		unset($this->value);
		parent::__destruct();
	}
	/**
		Отображение ячейки
	*/
	public function render()
	{
		switch ($this->options['renderType'])
		{
			case 'string': 
				$this->view = '<td id="'.$this->name.'"'.$this->getAllProperties().$this->getAllEventListeners().'>'.$this->value.'</td>'; 
			break;
			default:break;
		}		
		return $this->view;
	}
	
}

/**
 Класс  элемента списка. Представляет собой строку html таблицы.
 Является агрегатом для экземпляров класса CItem
 Автор Давыдов Денис
 Версия: 1.000.000
*/
class CListItem extends CComponent
{
	private $items;
	
	public function __construct($name)
	{
		parent::__construct($name);
		$this->items = array();
	}
	///////////////////////////////////
	public function Length()
	{
		return sizeof($this->items);
	}
	////////////////////////////////////
	public function addItem($item)
	{
		$this->items[] = $item;
	}
	///////////////////////////////
	public function __destruct()
	{
		
		unset($this->items);
		parent::__destruct();
	}
	public function render()
	{
	
		$this->view = '<tr id="'.$this->name.'"'.$this->getAllProperties().$this->getAllEventListeners().'>';
		$len = $this->Length();
			
		for ($i = 0; $i < $len ; $i ++ )
		    $this->view .= $this->items[$i]->render();
	
		$this->view .= '</tr>';
					
		return $this->view;	
	}
	
}
/**
	Класс колонки (заголовка) списка 
	Автор Давыдов Денис
	Версия: 1.000.000
*/

class CColumn extends CComponent
{
	private $value;
	
	public function __construct($name,$value='')
	{
		parent::__construct($name);
		$this->options['sortable'] = false; 
		$this->options['sortListener'] = null; 
		$this->options['sortButtonDown'] = null;
		$this->options['sortButtonUp'] = null;
		$this->options['sortDirect'] = 0;
		$this->options['icon'] = null;
		$this->value = $value;
	}
	//////////////////////////////////
	public function __destruct()
	{
		unset($this->value);
		parent::__destruct();
	}
	///////////////////////////////////

	public function render()
	{
		$sortBt = null;
		if ( $this->options['sortable'] && $this->options['sortListener'] != null )
		{
		  if ( $this->options['sortButtonDown']!=null && $this->options['sortButtonUp'] != null )
		  {
			if ( $this->options['sortDirect'] == 0 )
				$sortBt = '<img src="'.$this->options['sortButtonDown'].'" align="absmiddle" style="text-align:left">';
			else
				$sortBt = '<img src="'.$this->options['sortButtonUp'].'" align="absmiddle" style="text-align:left">';	
		  }		
		}
		$this->view = '<th id="'.$this->name.'"'.$this->getAllProperties().$this->getAllEventListeners().'>'.$sortBt.'&nbsp;&nbsp;'.$this->value.'  </th>'; 
		return $this->view;
	}
}
/**
	Класс  списка 
	Автор Давыдов Денис
	Версия: 1.000.000
*/
class CList extends CComponent
{
	private $items;
	private $columns;
	private $styles;

	
	public function __construct($name)
	{
		parent :: __construct($name);
		
		$this->items = array();
		$this->columns = array();
		$this->styles = array();
	
		$this->options['renderType'] = 'report';// report or icons
		$this->options['columnsTracking'] = false;
		$this->options['stringTracking'] = false;
		$this->options['stringTrackingListener'] = false;
		$this->options['stringSelectListener'] = null;
		
		$this->styles['colTrOnOverColor'] = null;
		$this->styles['colTrOnOutColor'] = null;
		
		$this->styles['strTrOnOverColor'] = '#ffffff';
		$this->styles['strTrOnOutColor'] = '#ffffff';
		$this->styles['strTrSkipColor'] = '#ffffff';
		
		$this->addProperty('class');
		$this->addProperty('cellpadding','0');
		$this->addProperty('cellspacing','0');
		
		$this->items = array();
		$this->columns = array();
	}
	/////////////////////////////////////
	public function __destruct()
	{
		unset($this->styles);
		unset($this->items);
		unset($this->columns);
		parent::__destruct();
	}
	/////////////////////////////////////
	public function Length()
	{
		return sizeof($this->items);
	}
	////////////////////////////////////
	public function ColumnLength()
	{
		return sizeof($this->columns);
	}
	////////////////////////////////////
	public function addColumn($column)
	{
		$this->columns[] = $column;
	}
	public function addItem($item)
	{
		$this->items[] = $item;
	}
	/////////////////////////////////////
	public function setStyle($name,$value)
	{
		if(array_key_exists($name,$this->styles))
			$this->styles[$name] = $value;
		else
			return null;
	}
	
	/////////////////////////////////////
	public function render()
	{
		switch ($this->options['renderType'])
		{
			case 'report': 
					
					
					//выводим колонки
					$this->view .= $this->getListeners();
					$this->view .= '<table id="'.$this->name.'"'.$this->getAllProperties().' width="99%"><tr>';
					$len = sizeof($this->columns);
					for ( $i = 0; $i < $len; $i ++ )
					{
						//listeners
						$col = $this->columns[$i];
						if ( $this->options['columnsTracking'] )
						{
							
							$col->setEventListener('onmouseover','javascript:this.style.backgroundColor=\''.$this->styles['colTrOnOverColor'].'\'');
							//$col->setEventListener('onmouseover',$this->name.'_columnsTarcking(this,\''.$this->styles['colTrOnOverColor'].'\')');
							//$col->setEventListener('onmouseout',$this->name.'_columnsTarcking(this,\''.$this->styles['colTrOnOutColor'].'\')');	
							$col->setEventListener('onmouseout','javascript:this.style.backgroundColor=\''.$this->styles['colTrOnOutColor'].'\'');
						}
						if ($col->options['sortable'] && $col->options['sortListener'] != null)
						{
							$col->setEventListener('onclick',$col->options['sortListener']);
							$col->addProperty('style','cursor:pointer');
						}							
						$this->view .= $this->columns[$i]->render();
					}
					$this->view .= '</tr>';
					
					$len = sizeof($this->items);
					for ( $i = 0; $i < $len; $i ++ )
					{
						$item = $this->items[$i];
							
						if ( $this->options['stringTracking']  && $this->options['stringTrackingListener'] != null )
						{
							$listener = $this->options['stringTrackingListener'].'(this,\''.$this->styles['strTrOnOverColor'].'\',\''.$this->styles['strTrSkipColor'].'\')';
							$item ->setEventListener('onmouseover',$listener);
							$listener = $this->options['stringTrackingListener'].'(this,\''.$this->styles['strTrOnOutColor'].'\',\''.$this->styles['strTrSkipColor'].'\')';
							$item ->setEventListener('onmouseout',$listener);
							if ( $this->options['stringSelectListener'] != null)
							{
								$listener = $this->options['stringSelectListener'].'(this,\''.$this->styles['strTrSkipColor'].'\',\''.$this->styles['strTrOnOutColor'].'\')';
								$item ->addEvent('ondblclick',$listener);
								
							}
							
						}
						
						$this->view .= $this->items[$i]->render();
					}
					$this->view .= '</table>';
			break;
			
			default:break;
		}
		return $this->view;
	}
	protected function getListeners()
	{
		$s = '';
		/*if ( $this->options['stringTracking'] )
		{
			$s .= 'function '.$this->name.'_stringTarcking(obj,newColor){';
			$s .= 'obj.className = className;';
			$s .= '}';
		}*/
		
		return '<script language="javascript">'.$s.'</script>';
	}
}
 
?>