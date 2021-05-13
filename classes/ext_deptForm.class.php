<?php

require_once ('base/form.class.php');
require_once ('base/controls.class.php');

class CDepartmentForm extends CForm
{
    public function __construct($name)
    {
        parent:: __construct($name,'./controllers/dept.controller.php','POST','multipart/form-data');

        $this->styles['textField']  = 'textField';
        $this->styles['table']      = 'formTable';
        $this->styles['button']     = 'sbutton';	

        //создаём элементы
        //скрытые поля
        $this->elements['idDept']   = new CTextField('idDept','','hidden');
        $this->elements['act'] 	    = new CTextField('act','save','hidden');
        $this->elements['idParent'] = new CTextField('idParent','','hidden');

        $this->elements['deptName'] = new CTextField('deptName','','text');
        $this->elements['deptName']->addProperty('size','50');
        $this->elements['deptName']->addProperty('class',$this->styles['textField']);
        
        $this->elements['lesee'] = new CCheckBox('lesee');
        //setDisabled($this->elements['lees']);
        
        $this->elements['parentDeptName'] = new CTextField('parentDeptName','','text');
        $this->elements['parentDeptName']->addProperty('size','50');
        $this->elements['parentDeptName']->addProperty('class',$this->styles['textField']);
        $this->elements['parentDeptName']->addProperty('disabled','disabled');

        //выбрать родительский отдел
        $this->elements['parentBt'] = new CButton('SIMPLE','parentBt','выбрать');
        $this->elements['parentBt']->addProperty('class',$this->styles['button']);
        $this->elements['parentBt']->setEventListener('onclick','Departments.choose_parent()');
        
        //clear field родительский отдел
        $this->elements['clearparentBt'] = new CButton('SIMPLE','clearparentBt','очистить');
        $this->elements['clearparentBt']->addProperty('class',$this->styles['button']);
        $this->elements['clearparentBt']->setEventListener('onclick','Departments.clear_parent()');
        
        //сохранить
        $this->elements['submitBt'] = new CButton('SIMPLE','submitBt','сохранить');
        $this->elements['submitBt']->addProperty('class',$this->styles['button']);
        //$this->elements['submitBt']->setEventListener('onclick','document.'.$this->properties['name'].'.submit()');
        $this->elements['submitBt']->setEventListener('onclick','Departments.save()');  
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
       $this->view .= $this->elements['idDept']->render();
       $this->view .= $this->elements['idParent']->render();
       $this->view .= $this->elements['act']->render();


       if ( $this->styles['table'] != null )
                    $this->view .= ' <table class="'.$this->styles['table'].'" cellpadding="0" cellspacing="0" style="border:1px solid #b7cee4;">';
            else	
           $this->view .= ' <table  cellpadding="0" cellspacing="0" style="border:1px solid #b7cee4;">';

      $this->view .= '<tr>';
      $this->view .= '<td>Название</td>';
      $this->view .= '<td>'.$this->elements['deptName']->render().' &nbsp;&nbsp;'.$this->elements['lesee']->render().'&nbsp;Арендованный</td>';
      $this->view .= '</tr>';
      $this->view .= '<tr>';
      $this->view .= '<td>Входит в</td>';
      $this->view .= '<td>'.$this->elements['parentDeptName']->render().' '. $this->elements['parentBt']->render().' '. $this->elements['clearparentBt']->render().'</td>';
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

class CBrowseDeptForm extends CForm
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
			$this->elements['selectedDept'] 	= new CTextField('selectedDept','','hidden');
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
			$this->elements['selectedDept']->render(); 	
			
			 if ( $this->styles['table'] != null )
				$this->view .= ' <table class="'.$this->styles['table'].'" height="100%" cellpadding="0" cellspacing="0">';
			else	
				$this->view .= ' <table  cellpadding="0" cellspacing="0" height="100%" >';
		   
		   $this->view .= '
			<tr>
				<th  colspan="2" style="height:2%">Отделы</th>
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