<?php
require_once ('component.class.php');
require_once ('lists.class.php');
/**
  Класс справочника.
  Автор Давыдов Денис
  Версия 1.000.001
  
*/

class CReference extends CComponent
{
	public $list = null;
	public $controlsStyle;
	private $groupOperations;
	

	public function  __construct($name)
	{
		parent::__construct($name);
		//инициализация опций
		/** колличество выводимых элементов на одной странице */
		$this->options['length'] = 15;
		/** Номер текущей страницы */
		$this->options['currentPage'] = 1;  
		/** Номер колонки по которой будет выполнена сортировка */
		$this->options['sortCol'] = 4;
		/** Направление сортировки 0 - вверх, 1 - вниз */
		$this->options['sortDirect'] = 0;
		/** Всего найденых строк */
		$this->options['totalRows'] = 0;
		/** Всего страниц */
		$this->options['totalPages'] = 0;
		/** Начальная позиция*/
		$this->options['startPos'] = 1;
		/** Конечная позиция */
		$this->options['endPos'] = $this->options['length'];
		/**адрес куда сабмитится форма*/
		$this->options['listenerUrl'] = 'null';
		/**Использование групповых операций*/
		$this->options['useGroupOperations'] = false;
		/**Обработчик групповых операций */
		$this->options['groupOperationsListener'] = null;
		
		//инициализация свойств справочника
		$this->addProperty('class');
		$this->addProperty('cellpadding','0');
		$this->addProperty('cellspacing','0');
		//создание основного листа
		$this->list = new CList('list_'.$this->name);
		//стили контролов
		$this->controlsStyle = array();
		$this->controlsStyle['select'] = '';
		$this->controlsStyle['links'] = '';
		
		
		$this->groupOperations = array();
		
		
	}
	public function __destruct()
	{
		unset($this->list);
		unset($this->controlsStyle);
		unset($this->groupOperations);
		unset($this->selectedItems);
		parent::__destruct();
	}
	
	public function  addGroupOperation( $name, $value )
	{
		if ( !array_key_exists ($name,$this->groupOperations ) )
			$this->groupOperations[$name]  = $value;
	}
	
	public function addColumn($col,$number = null)
	{
		//устанавливаем обработчики для сортируемых полей
		if ( $col->getOption('sortable') )
		{
			$sortDirect = ($this->getOption('sortDirect') == 0 ) ? 1 : 0;
			$l = 'javascript: document.'.$this->name.'.sortDirect.value='.$sortDirect.';document.'.$this->name.'.sortCol.value='.$number.';document.'.$this->name.'.submit();';
		//	echo '<br>'.$l .'<br>';
			$col->setOption('sortListener',$l );
			$col->setOption('sortDirect',$sortDirect );
		}
		$this->list->addColumn($col);
	}
	
	
	public function calculateParameters()
	{
	   $this->options['totalPages'] = ceil($this->options['totalRows'] / $this->options['length'])+1;
	   
	   if ( $this->options['totalRows'] <= $this->options['length'] )
	   {
			$this->options['currentPage'] = 1;
	   }
	   
	   if ( $this->options['currentPage'] > 1 )
	   {
			$this->options['startPos'] = ($this->options['length'] * ($this->options['currentPage']-1)) + 1;
			$this->options['endPos']   = ($this->options['startPos'] +  $this->options['length'])-1;
	   }
	   else
	   {
			$this->options['startPos'] = 1;
			$this->options['endPos']   = $this->options['length'];
	   }
	  
	}
	public function render()
	{
		
		$pageControl = '';
		$this->view  = '<form name="'.$this->name.'" method="POST">';
		$this->view	.= '<input type="hidden" name="currentPage" value="'.$this->options['currentPage'].'">';
		//$this->view	.= '<input type="hidden" name="length" value="'.$this->options['length'].'">';
		$this->view	.= '<input type="hidden" name="sortCol" value="'.$this->options['sortCol'].'" >';
		$this->view	.= '<input type="hidden" name="sortDirect" value="'.$this->options['sortDirect'].'" >';
		$this->view	.= '<input type="hidden" name="formAction" value="" >';
		$this->view	.= '<input type="hidden" name="selectAll" value="0" >';
		
		$this->view .= '<table border="0" name="ref_'.$this->name.'" '.$this->getAllProperties().' width="99%">';
		
		//reference head
		$this->view .= '<tr>';
		$this->view .= '<th width="15%">';
		
		
			
		
		$this->view .= '</th>';
		$this->view .= '<th width="85%">';
		$this->view .= 'Всего записей: '.$this->getOption('totalRows').'.&nbsp;&nbsp; Страница: '.$this->options['currentPage'].' из '.($this->options['totalPages']-1);
		$this->view .= '</th>';
		$this->view .= '<th style="text-align:right">';
		
		$pageControl = '';
		
		if($this->options['currentPage'] > 1) 
			$pageControl .= '<img src="images/leftBt16.gif" align="absmiddle" style="cursor:pointer" alt="назад" onclick="document.'.$this->name.'.currentPage.value = parseInt(document.'.$this->name.'.currentPage.value) - 1 ; document.'.$this->name.'.submit()">';
			
	   
		$pageControl .= '<select class="'.$this->controlsStyle['select'].'" onchange="document.'.$this->name.'.currentPage.value = this.options[this.selectedIndex].value;document.'.$this->name.'.submit()">';
		//if($this->options['totalPages'] > 0)
		//{
			for ( $i = 1; $i <= ($this->options['totalPages']-1) ; $i++ )
			{
				$page = $i;
				if( $page != $this->options['currentPage'])
					$pageControl .= '<option value="'.$page.' ">--'.$page.'--</option>';
				else
				$pageControl .= '<option value="'.$page.' " selected >--'.$page.'--</option>';		
			}
		//}
		$pageControl .= '</select>  ';
		
		if($this->options['currentPage'] < ($this->options['totalPages']-1)) 
			$pageControl .= '<img src="images/rightBt16.gif" align="absmiddle" style="cursor:pointer" alt="вперёд" onclick="document.'.$this->name.'.currentPage.value = parseInt(document.'.$this->name.'.currentPage.value) + 1 ; document.'.$this->name.'.submit()">';
			//$this->view .= '&nbsp;[<a href="#" class="'.$this->controlsStyle['links'].'">следующая</a>]&nbsp;';
	   
		$this->view .= $pageControl;
		$this->view .= '</th>';
		$this->view .= '</tr>';
		//reference body
		$this->view .= '<tr>';
		$this->view .= '<td valig="top" colspan="3" style="text-align:center">';
		$this->view .= $this->list->render();
		$this->view .= '</td>';
		$this->view .= '</tr>';
		//reference footer
		$this->view .= '<tr>';
		$this->view .= '<td>';
		$this->view .= '</td>';
		$this->view .= '</tr>';
		$this->view .= '<tr>';
		$this->view .= '<th width="15%">';
		
		//групповые операции
		
		if ( $this->options['useGroupOperations'] && $this->options['groupOperationsListener'] != null )
		{
			$this->view .= 'Действия ';
			
			$this->view .= '<select class="'.$this->controlsStyle['select'].'" name="groupOp" >';
			$this->view .= '<option value="0">--</option>';
			foreach ( $this->groupOperations as $key => $value )
				$this->view .= '<option value="'.$key.'" >'.$value.'</option>';
			
			$this->view .= '</select>';
			
			$this->view .= '<a href="#" onclick="'.$this->options['groupOperationsListener'].'" class="linkBt">[выполнить]</a>';
		}
		
		$this->view .= '</th>';
		$this->view .= '<th width="85%">';
		$this->view .= 'Всего записей: '.$this->getOption('totalRows').'.&nbsp;&nbsp; Страница: '.$this->options['currentPage'].' из '.($this->options['totalPages']-1);
		$this->view .= '</th>';
		$this->view .= '<th style="text-align:right">';
		$this->view .= $pageControl;
		$this->view .= '</th>';
		$this->view .= '</tr>';
		
		
		
		$this->view .= '</table>';
		$this->view .= '</form>';
		
		return $this->view;
	}
	public function __toString()
	{
		$s =  'Класс:' . __CLASS__ . '<br>';
		$s .= parent::__toString();
		
		return $s;
	}
}

?>