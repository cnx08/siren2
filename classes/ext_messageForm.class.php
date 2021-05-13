<?php

require_once ('base/form.class.php');
require_once ('base/controls.class.php');

class CMessageForm extends CForm
{
	private $headText = '';
	public  $text	  = '';
	public  $header   = '';	 
	public  $iconSrc  = '';	
	private $hiddenFields = array();	
	
	public function __construct($name)
	{
		parent:: __construct($name,null,'POST','multipart/form-data');
			
				
		$this->styles['button'] 	= 'sbutton';
		//создаём элементы
		//скрытые поля
		
		
		$this->elements['cancelBt'] = new CButton('SIMPLE','cancelBt','&nbsp;&nbsp;&nbsp;&nbsp;ok&nbsp;&nbsp;&nbsp;&nbsp;');
		$this->elements['cancelBt']->addProperty('class',$this->styles['button']);
		//$this->elements['cancelBt']->setEventListener('onclick','goToAddress(\'clientFrame\',\'personal.php\',\'topPanelFrame\',\'toppanel.php?location=personal\')');
			
	}
	public function __destruct()
	{
		unset($this->text);
		unset($this->header);
		unset($this->iconSrc);
		unset($this->hiddenFields);
		parent::__destruct();
	}
	
	public function addHiddenField($name,$value)
	{
		if ( !array_key_exists($name,$this->hiddenFields) )
		{	
			$this->hiddenFields[$name] = $value;
			$this->elements[$name] = new CTextField($name,$value,'hidden');	
		}	
	}
	
	public  function render()
	{
		$this->view = '<center><form ';
	   //свойства
	   $this->view .= $this->getAllProperties();
	   $this->view .= '>';
	   //добавляем скрытые поля
	   foreach ( $this->hiddenFields as $key=>$value )
	   {
			$this->view .= $this->elements[$key]->render();
	   }
	   
	    if ( $this->styles['table'] != null )
			$this->view .= ' <table  class="'.$this->styles['table'].'" cellpadding="0" cellspacing="0">';
		else	
	       $this->view .= ' <table  cellpadding="0" cellspacing="0">';
		   
		$this->view .= '<tr><th colspan="2">'.$this->header.'</th></tr>';
		$this->view .= '<tr>';
		$this->view .= '<td  width="80" style="text-align:center"><img src="'.$this->iconSrc.'"></td>';
	    $this->view .= '<td>'.$this->text.'</td>';
		$this->view .= '</tr>';
		$this->view .= '<tr>';
		$this->view .=	'<td colspan="2" style="text-align:center">'.$this->elements['cancelBt']->render().'</td>';
		$this->view .=	'</tr>';

	   $this->view .= '</table>';	
	   $this->view .= '</form></center>';
	   
	   return $this->view;
	}	
}	
?>