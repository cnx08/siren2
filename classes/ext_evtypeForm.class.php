<?php

require_once ('base/form.class.php');
require_once ('base/controls.class.php');

class CTurnListForm extends CForm
{

	public function __construct($name)
	{
		parent:: __construct($name,null,'POST','multipart/form-data');
			
			$this->styles['textField']  = 'textField';
			$this->styles['table'] 		= 'formTable';
			$this->styles['button'] 	= 'sbutton';	
			
			//создаём элементы
		    //скрытые поля
			$this->elements['idTurn'] 	= new CTextField('idTurn','','text');
			$this->elements['act'] 	    = new CTextField('act','save','hidden');
			$this->elements['idParent'] = new CTextField('idParent','','text');
			
			
			$this->elements['turnName'] = new CTextField('turnName','','text');
			$this->elements['turnName']->addProperty('size','30');
			$this->elements['turnName']->addProperty('class',$this->styles['textField']);
			
			$this->elements['parentTurnName'] = new CTextField('parentTurnName','','text');
			$this->elements['parentTurnName']->addProperty('size','30');
			$this->elements['parentTurnName']->addProperty('class',$this->styles['textField']);
			$this->elements['parentTurnName']->addProperty('disabled','disabled');
			
			//сохранить
			$this->elements['submitBt'] = new CButton('SIMPLE','submitBt','сохранить');
			$this->elements['submitBt']->addProperty('class',$this->styles['button']);
			//$this->elements['submitBt']->setEventListener('onclick','document.'.$this->properties['name'].'.submit()');
			//отмена
			$this->elements['cancelBt'] = new CButton('SIMPLE','cancelBt','отмена');
			$this->elements['cancelBt']->addProperty('class',$this->styles['button']);
			
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	public function render()
	{
	   
	   $this->view = '<form ';
	   //свойства
	   $this->view .= $this->getAllProperties();
	   $this->view .= '>';
	   //скрытые поля
	   $this->view .= $this->elements['idTurn']->render();
	   $this->view .= $this->elements['idParent']->render();
	   $this->view .= $this->elements['act']->render();
	   
	   
	   if ( $this->styles['table'] != null )
			$this->view .= ' <table class="'.$this->styles['table'].'" cellpadding="0" cellspacing="0" style="border:1px solid #b7cee4;">';
		else	
	       $this->view .= ' <table  cellpadding="0" cellspacing="0" style="border:1px solid #b7cee4;">';
		   
	  $this->view .= '<tr>';
	  $this->view .= '<td>Входит в</td>';
	  $this->view .= '<td>'.$this->elements['parentTurnName']->render().'</td>';
	  $this->view .= '</tr>';
	  $this->view .= '<tr>';
	  $this->view .= '<td>Название</td>';
	  $this->view .= '<td>'.$this->elements['turnName']->render().'</td>';
	  $this->view .= '</tr>';
	  
	  $this->view .= '<tr>
			<th colspan="2" style="text-align:right;">'.$this->elements['submitBt']->render().' '.$this->elements['cancelBt']->render().'</th>
		</tr>';
	  
	  $this->view .= '</table>';
	  
	  $this->view .='';
	  $this->view .= '</form>';
	   return $this->view;
	 } 
}

class CBrowseEvForm extends CForm
{
	public $Tree;
	
	public function __construct($name)
	{
		parent:: __construct($name,null,'POST','multipart/form-data');
			
			$this->styles['textField']  	= 'textField';
			$this->styles['table'] 			= 'formTable';
			$this->styles['button'] 		= 'sbutton';
			$this->styles['treeContainer']	= 'treeScrollContainer';		
			
			//создаём элементы
		    //скрытые поля
			$this->elements['selectedTurn'] 	= new CTextField('selectedTurn','','hidden');
			$this->elements['act'] 	    		= new CTextField('act','','hidden');
			
			$this->elements['getBt'] = new CButton('SIMPLE','getBt','&nbsp;&nbsp;Ok&nbsp;&nbsp;');
			$this->elements['getBt']->addProperty('class',$this->styles['button']);
			//$this->elements['submitBt']->setEventListener('onclick','document.'.$this->properties['name'].'.submit()');
			//отмена
			$this->elements['cancelBt'] = new CButton('SIMPLE','cancelBt','отмена');
			$this->elements['cancelBt']->addProperty('class',$this->styles['button']);
	}
	public function __destruct()
	{
		unset($this->Tree);
		parent::__destruct();
	}
	
	public function render()
	{
		$this->view = '<form ';
			//свойства
			$this->view .= $this->getAllProperties();
			$this->view .= '>';
			$this->view .= $this->elements['act']->render();
			$this->elements['selectedTurn']->render(); 	
			
			 if ( $this->styles['table'] != null )
				$this->view .= ' <table class="'.$this->styles['table'].'" height="100%" cellpadding="0" cellspacing="0">';
			else	
				$this->view .= ' <table  cellpadding="0" cellspacing="0" height="100%" >';
		   
		   $this->view .= '
			<tr>
				<th  colspan="2" style="height:2%">Турникеты</th>
			</tr>';
			
			$this->view .= '<tr >';
			$this->view .= '<td style="height:98%" valign="top"><div id="treeContainer" class="'.$this->styles['treeContainer'].'" style="height:400px;width:400px;">'.$this->Tree.' </div></td>';
			
			$this->view .= '</tr>';
			$this->view .= '<tr>';
			$this->view .= '<th align="right">';
			$this->view .= $this->elements['getBt']->render().' '.$this->elements['cancelBt']->render();
			$this->view .= '</th>';
			$this->view .= '</tr>';
			
			$this->view .= '</table>';
			$this->view .= '</form>';
			
			return $this->view;
			
	}
		
}
?>