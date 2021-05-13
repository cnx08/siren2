<?php
//IN DEVELOPMENT
require_once('topbase.h');
/**
Класс для отображение панели с закладками. Содержит все себе объекты класса CBox
*/

class СTabPanel extends CComponent
{
	/**
		Массив для хранения имён классов css.
	*/
	private $styles;
	/**
		массив для хранения объектов класса CBox, которые представляют собой заголовки страницы
	*/
	private $headers;
	/**
		массив для хранения объектов класса CBox, которые представляют собой листы страницы
	*/
	private $sheets;
	
	
	///////////////////////////////////////////////////////////////
	public function __construct($name)
	{
		parent::__construct($name);
		
		//initialize styles 
		$this->styles = array();
		$this->styles['header'] = ''; //css класс заголовков
		$this->styles['activeHeader'] = '';//css класс активного заголовка
		$this->styles['sheet'] = ''; //css класс листа
		$this->styles['activeSheet'] = '';//css класс активного листа
		$this->styles['headerMouseOver'] = ''; //css класс при наведении мыши
		$this->styles['headerMouseOut'] = ''; //css класс после наведения мыши
		$this->styles['container'] = ''; //css класс контейнера, в котором содержится элементы ( тэг table)
		//initialize options
		$this->options['activeSheet'] = 0; //номер активнго листа
		$this->options['sheetCount'] = 0; //счётчик листов
		$this->options['clietHeight'] = 200;// высота панели, единица измерения - px
		
		//initialize propperties
		$this->addProperty('cellpadding','0');
		$this->addProperty('cellspacing','0');
		
		
	}
	/**
	Добовляет лист. Заголовок листа передаётся в $header, а содержимое в text
	*/
	public function addSheet($header = '', $text = '')
	{
		$h = new CBox('h_'.$this->name.'_'.$this->options['sheetCount'],$header);
		$h->addProperty('class');
		$s = new CBox('s_'.$this->name.'_'.$this->options['sheetCount'],$text);
		$s->addProperty('class');
		
		$this->headers[] = $h;
		$this->sheets[] = $s;
		
		$this->options['sheetCount']++; 
	}
	/**
		
	*/
	public function setStyle($name,$value)
	{
		if(array_key_exists($name,$this->styles))
			$this->styles[$name] = $value;
		else
			return null;
	}
	///////////////////////////////////////////////////////////////
	public function __destruct()
	{
		unset($this->headers);
		unset($this->sheets);
		unset($this->styles);
		parent::__destruct();
	}
	///////////////////////////////////////////////////////////////
	public function render()
	{
		
		//setting styles
		$this->addProperty('class',$this->styles['container']);
		//setting listeners
		$this->view .= $this->getListeners();
		
		$this->view .= '<table id="'.$this->name.'" '.$this->getAllProperties().' >';
		$this->view .= '<tr>';
		$this->view .= '<th>';
		
		$size = sizeof($this->headers);
		//headers's rendering
		for ( $i = 0; $i < $size; $i ++  )
		{
			
			$this->headers[$i]->setEventListener('onmouseover',$this->name.'_onHeaderMouseRollHandler(this,\''.$this->styles['headerMouseOver'].'\')');
			$this->headers[$i]->setEventListener('onmouseout',$this->name.'_onHeaderMouseRollHandler(this,\''.$this->styles['headerMouseOut'].'\')');
			$this->headers[$i]->setEventListener('onclick',$this->name.'_onHeaderClick(this)');
			
			if ( $this->options['activeSheet'] == $i )
				 $this->headers[$i]->setProperty('class',$this->styles['activeHeader']); 
			else
				 $this->headers[$i]->setProperty('class',$this->styles['header']); 
				 
			$this->view .= 	$this->headers[$i]->render(); 
		}
		
		$this->view .= '</th>';
		$this->view .= '</tr>';
		$this->view .= '<tr>';
		//$this->view .= '<td>';
		 $this->view .= '<td>';
		//sheets's rendering
		for ( $i = 0; $i < $size; $i ++  )
		{
			
			if ( $this->options['activeSheet'] == $i )
				 $this->sheets[$i]->setProperty('class',$this->styles['activeSheet']); 
			else
				 $this->sheets[$i]->setProperty('class',$this->styles['sheet']); 
				 
			$this->view .= 	$this->sheets[$i]->render(); 
		}
		$this->view .= '</td>';
		$this->view .= '</tr>';
		$this->view .= '</table>';
		
		return $this->view;
	}
	public function __toString()
	{
		$s = __CLASS__ .'<br>';
		$s .= 'Styles:<br>';
		foreach($this->styles as $key => $value)
		{
			$s .= $key .' = '.$value . '<br>';  
		}
		
		return $s;
	}
	private function getListeners()
	{
		$current_id = 'h_'.$this->name.'_'.$this->options['activeSheet'];
		$s = '<script language="javascript">';
		$s .= '/* '.$this->name.' listeners*/';
		$s .= 'var '.$this->name.'_current_active = \''.$current_id .'\';';
               
		$s .= 'function '.$this->name.'_onHeaderMouseRollHandler(obj,className){';
		$s .= 'obj.className=(obj.className != \''.$this->styles['activeHeader'].'\') ? className : \''.$this->styles['activeHeader'].'\';';
		$s .= '}';
		$s .= 'function '.$this->name.'_onHeaderClick(obj){';
		$s  .= ' var current = null;
                        $(".Excel").prop("checked",false);
                        $(".rtype").val(obj.id.substring(12));
                        if(obj.id.substring(12)>0){
                            $("#start_date").removeAttr("disabled");
                            $("#fin_date").removeAttr("disabled");
                        }
                        else{
                            $("#start_date").attr("disabled","disabled");
                            $("#fin_date").attr("disabled","disabled");
                        }
		         if (obj.id == '.$this->name.'_current_active ) return; else current = document.getElementById('.$this->name.'_current_active);
			     if ( !current ) return;
				 var currentNumber = current.id.split(\'_\')[2];
				 var currentSheet = document.getElementById(\'s_'.$this->name.'_\'+ currentNumber);
				 if ( !currentSheet ) return;
				 var newSheet = document.getElementById(\'s_'.$this->name.'_\' + obj.id.split(\'_\')[2]);
				 if	( !newSheet ) return;
				 current.className = \''.$this->styles['header'].'\';
				 currentSheet.className = \''.$this->styles['sheet'].'\';
				 obj.className = \''.$this->styles['activeHeader'].'\';
				 newSheet.className = \''.$this->styles['activeSheet'].'\';
				 '.$this->name.'_current_active = obj.id;
			   ';
		
		$s .= '}';
		$s .= '</script>';
		
		return $s;
	}
	
}	
?>