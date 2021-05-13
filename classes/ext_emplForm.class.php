<?php

/**
Contain clasess of employes forms
Date: 12.11.2008
Author: Davydov D.
version: 1.0.
*/

require_once ('base/form.class.php');
require_once ('base/controls.class.php');

class CEmployeForm extends CForm
{
	public function __construct($name)
	{
		parent:: __construct($name,null,'POST','multipart/form-data');
			
			$this->styles['textField']  = 'textField';
			$this->styles['table'] 		= 'formTable';
			$this->styles['button'] 	= 'sbutton';	
			$this->styles['select']		= 'select';
			$this->styles['link']		= 'linkBt';
				
		   //создаём элементы
		   //скрытые поля
			$this->elements['idEmpl'] 			= new CTextField('idEmpl','0','hidden');
			$this->elements['act'] 	    		= new CTextField('act','save','hidden');
			$this->elements['idPassCode'] 	    = new CTextField('idPassCode','0','hidden');
			$this->elements['oldPassCode'] 	    = new CTextField('oldPassCode','','hidden');
			
			
			//табельный номер.
			$this->elements['tabNum'] = new CTextField('tabNum','','text');
			$this->elements['tabNum']->addProperty('size','5');
			$this->elements['tabNum']->addProperty('class',$this->styles['textField']);
			//фамилия
			$this->elements['family'] = new CTextField('family','','text');
			$this->elements['family']->addProperty('size','30');
			$this->elements['family']->addProperty('class',$this->styles['textField']);
			//имя
			$this->elements['firstName'] = new CTextField('firstName','','text');
			$this->elements['firstName']->addProperty('size','30');
			$this->elements['firstName']->addProperty('class',$this->styles['textField']);
			//echo '<br>'.$this->styles['textField'];
			//отчество
			$this->elements['surName'] = new CTextField('surName','','text');
			$this->elements['surName']->addProperty('size','30');
			$this->elements['surName']->addProperty('class',$this->styles['textField']);
			//должность
			$this->elements['position'] = new CTextField('position','','text');
			$this->elements['position']->addProperty('size','30');
			$this->elements['position']->addProperty('class',$this->styles['textField']);
			//дата приёма
			$this->elements['dateIn'] = new CTextField('dateIn',date("d.m.Y"),'text');
			$this->elements['dateIn']->addProperty('size','10');
			$this->elements['dateIn']->addProperty('readonly','readonly');
			$this->elements['dateIn']->addProperty('class',$this->styles['textField']);
			//дата увольнения
			$this->elements['dateOut'] = new CTextField('dateOut','','text');
			$this->elements['dateOut']->addProperty('size','10');
			$this->elements['dateOut']->addProperty('readonly','readonly');
			$this->elements['dateOut']->addProperty('class',$this->styles['textField']);
			//код пропуска 
	        $this->elements['passCode'] = new CTextField('passCode','','text');
			$this->elements['passCode']->addProperty('size','30');
			$this->elements['passCode']->addProperty('maxlength','16');
			$this->elements['passCode']->addProperty('class',$this->styles['textField']);
			//список отделов
	    	$this->elements['departments'] = new CDropDownList('departments');
			$this->elements['departments']->addProperty('class',$this->styles['select']);
			$this->elements['departments']->addItem('','--');
			//список графиков
	    	$this->elements['shedules'] = new CDropDownList('shedules');
			$this->elements['shedules']->addProperty('class',$this->styles['select']);
			$this->elements['shedules']->addItem('','--');
			
			//список рабочих зон
	    	$this->elements['workZones'] = new CDropDownList('workZones');
			$this->elements['workZones']->addProperty('class',$this->styles['select']);
			$this->elements['workZones']->addProperty('disabled','disabled');
			$this->elements['workZones']->addItem('','--');
			//тип расчёта наработки
			$this->elements['wtType'] = new CDropDownList('wtType');
			$this->elements['wtType']->addProperty('class',$this->styles['select']);
			$this->elements['wtType']->addItem('','--');
			
			//список доступов
	    	$this->elements['access'] = new CDropDownList('access');
			$this->elements['access']->addProperty('class',$this->styles['select']);
			$this->elements['access']->addProperty('disabled','disabled');
			$this->elements['access']->addItem('','--');
			
			//статус пропуска 
			$this->elements['statusAdmin'] = new CCheckbox('statusAdmin');
			$this->elements['statusBlock'] = new CCheckbox('statusBlock');
			$this->elements['statusDouble'] = new CCheckbox('statusDouble');
			//кнопки 
			//календарь
			$this->elements['getDateInBt'] = new CButton('SIMPLE','getDateInBt','...');
			$this->elements['getDateInBt']->addProperty('class',$this->styles['button']);
			$this->elements['getDateInBt']->setEventListener('onclick','showCalendar(\'dateIn\')');
			//ещё календарь
			$this->elements['getDateOutBt'] = new CButton('SIMPLE','getDateOutBt','...');
			$this->elements['getDateOutBt']->addProperty('class',$this->styles['button']);
			$this->elements['getDateOutBt']->setEventListener('onclick','showCalendar(\'dateOut\')');
			//выбор пропуска
			$this->elements['getPassCodeBt'] = new CButton('LINK','getPassCodeBt','[выбрать пропуск]');
			$this->elements['getPassCodeBt']->addProperty('class',$this->styles['link']);
			
			//сохранить
			$this->elements['submitBt'] = new CButton('SIMPLE','submitBt','сохранить');
			$this->elements['submitBt']->addProperty('class',$this->styles['button']);
			$this->elements['submitBt']->setEventListener('onclick','document.'.$this->properties['name'].'.submit()');
			//отмена
			$this->elements['cancelBt'] = new CButton('SIMPLE','cancelBt','отмена');
			$this->elements['cancelBt']->addProperty('class',$this->styles['button']);
			$this->elements['cancelBt']->setEventListener('onclick','window.close()');
			//поле для фотки
			$this->elements['srcPhoto'] = new CTextField('srcPhoto','','file');
			$this->elements['srcPhoto']->addProperty('class',$this->styles['textField']);
			//просмотр фотки
			//$this->elements['showBt'] = new CButton('SIMPLE','showBt','просмотр');
			//$this->elements['showBt']->addProperty('class',$this->styles['button']);
			//$this->elements['showBt']->setEventListener('onclick','showPhoto(\'emplPhoto\',\'srcPhoto\')');
			//кнопки
			//очисктка даты приёма
			$this->elements['clearDateInBt'] = new CButton('LINK','clearDateInBt','[очистить]');
			$this->elements['clearDateInBt']->addProperty('class',$this->styles['link']);
			$this->elements['clearDateInBt']->setEventListener('onclick','clearInput(\'dateIn\')');
			//очисктка даты увольнения
			$this->elements['clearDateOutBt'] = new CButton('LINK','clearDateOutBt','[очистить]');
			$this->elements['clearDateOutBt']->addProperty('class',$this->styles['link']);
			$this->elements['clearDateOutBt']->setEventListener('onclick','clearInput(\'dateOut\')');
			
			//флаг для рабочей зоныchangeEnabledElements
			$this->elements['chWzFromShedule'] = new CCheckbox('chWzFromShedule');
			$this->elements['chWzFromShedule']->setChecked(true);
			$this->elements['chWzFromShedule']->setEventListener('onclick','changeEnabled(this,\'workZones\')' );
			//флаг для доступа
			$this->elements['chAccessFromShedule'] = new CCheckbox('chAccessFromShedule');
			$this->elements['chAccessFromShedule']->setChecked(true);
			$this->elements['chAccessFromShedule']->setEventListener('onclick','changeEnabled(this,\'access\')' );
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
	   $this->view .= $this->elements['idEmpl']->render();
	   $this->view .= $this->elements['act']->render();
	   $this->view .= $this->elements['idPassCode']->render();
	   $this->view .= $this->elements['oldPassCode']->render();
	   
	   if ( $this->styles['table'] != null )
			$this->view .= ' <table class="'.$this->styles['table'].'" cellpadding="0" cellspacing="0">';
		else	
	       $this->view .= ' <table  cellpadding="0" cellspacing="0">';
    $this->view .='
	<tr>
      <th  colspan="3">Форма сотрудника</th>
    </tr>
    <tr>
      <td>Tab. номер </td>
      <td width="50%" >'.$this->elements['tabNum']->render().'</td>
      <td rowspan="10" width="50%" style="text-align:center;"><img id="emplPhoto"src="../images/logo.gif" width="300" height="200"></td>
    </tr>
    <tr>
      <td>Фамилия</td>
      <td>'.$this->elements['family']->render().'</td>
    </tr>
    <tr>
      <td>Имя</td>
      <td>'.$this->elements['firstName']->render().'</td>
    </tr>
    <tr>
      <td >Отчество</td>
      <td >'.$this->elements['surName']->render().'</td>
    </tr>
    <tr>
      <td>Должность</td>
      <td>'.$this->elements['position']->render().'</td>
    </tr>
	<tr>
      <td >Отдел</td>
      <td >'.$this->elements['departments']->render() .'</td>
    </tr>
	<tr>
      <td>Дата приёма</td>
      <td>'.$this->elements['dateIn']->render().' '.$this->elements['getDateInBt']->render().' '.$this->elements['clearDateInBt']->render().'</td>
    </tr>
    <tr>
      <td>Дата увольнения</td>
      <td>'.$this->elements['dateOut']->render().' '.$this->elements['getDateOutBt']->render().' '.$this->elements['clearDateOutBt']->render().'</td>
    </tr>
	<tr>
      <td >График</td>
      <td >'.$this->elements['shedules']->render() .'</td>
    </tr>
	<tr>
      <td >Рабочая зона</td>
      <td >'.$this->elements['workZones']->render() .'&nbsp;'.$this->elements['chWzFromShedule']->render().'брать из графика</td>
    </tr>
	
    <tr>
      <td>Доступ</td>
      <td>'.$this->elements['access']->render().'&nbsp;'.$this->elements['chAccessFromShedule']->render().'брать из графика</td>
  	  <th style="text-align:right;">'.$this->elements['srcPhoto']->render().'</th>

    </tr>
    <tr>
	<tr>
      <td >Тип расчёта наработки</td>
      <td >'.$this->elements['wtType']->render() .'</td>
    </tr>
      <td>Код пропуска</td>
      <td>'.$this->elements['passCode']->render().' '.$this->elements['getPassCodeBt']->render().'</td>
    </tr>
    <tr>
      <td>Статус пропуска</td>
      <td>'.$this->elements['statusAdmin']->render().'администратор <br>'.$this->elements['statusBlock']->render().'блокированный <br>'.$this->elements['statusDouble']->render().'контроль двойного прохода</td>
	  <td></td>	
	</tr>
    
    <tr>
      <th colspan="3" style="text-align:right;">'.$this->elements['submitBt']->render().' '.$this->elements['cancelBt']->render().'</th>
    </tr>
  </table>';
       
	   
	   $this->view .= '</form>';
	   return $this->view;
	}
	
 
}


class CEmployeSearchForm extends CForm
{
		
		public function __construct($name)
		{
			parent:: __construct($name,null,'POST');
			
			$this->styles['textField']  = 'textField';
			$this->styles['table'] 		= 'formTable';
			$this->styles['button'] 	= 'sbutton';	
			$this->styles['select']		= 'select';
			$this->styles['link']		= 'linkBt';
			
			
			$this->elements['act'] = new CTextField('act','search','hidden');
			$this->elements['idDept'] = new CTextField('idDept','','hidden');
			
			//табельный номер.
			$this->elements['tabNum'] = new CTextField('tabNum','','text');
			$this->elements['tabNum']->addProperty('size','5');
			$this->elements['tabNum']->addProperty('class',$this->styles['textField']);
			//фамилия
			$this->elements['family'] = new CTextField('family','','text');
			$this->elements['family']->addProperty('size','30');
			$this->elements['family']->addProperty('class',$this->styles['textField']);
			//имя
			$this->elements['firstName'] = new CTextField('firstName','','text');
			$this->elements['firstName']->addProperty('size','30');
			$this->elements['firstName']->addProperty('class',$this->styles['textField']);
			//отчество
			$this->elements['surName'] = new CTextField('surName','','text');
			$this->elements['surName']->addProperty('size','30');
			$this->elements['surName']->addProperty('class',$this->styles['textField']);
			//должность
			$this->elements['position'] = new CTextField('position','','text');
			$this->elements['position']->addProperty('size','30');
			$this->elements['position']->addProperty('class',$this->styles['textField']);
			
			//список отделов
	    	$this->elements['deptName'] = new CTextField('deptName','','text');
			$this->elements['deptName']->addProperty('class',$this->styles['textField']);
			$this->elements['deptName']->addProperty('readonly','readonly');
			
			//код пропуска 
	        $this->elements['passCode'] = new CTextField('passCode','','text');
			$this->elements['passCode']->addProperty('size','30');
			$this->elements['passCode']->addProperty('class',$this->styles['textField']);
			
			
			//кнопки
			$this->elements['searchBt'] = new CButton('SUBMIT','submitBt','    поиск    ');
			$this->elements['searchBt'] -> addProperty('class',$this->styles['button']);
			$this->elements['searchBt'] -> setEventListener('onclick','document.'.$name.'.submit()');

			$this->elements['cancelBt'] = new CButton('SIMPLE','cancelBt','отмена');
			$this->elements['cancelBt'] -> addProperty('class',$this->styles['button']);
			//$this->elements['cancelBt']->setEventListener('onclick','goToAddress(\'clientFrame\',\'personal.php\',\'topPanelFrame\',\'toppanel.php?location=personal\')');
			$this->elements['viewDeptBt'] = new CButton('SIMPLE','viewDeptBt','...');
			$this->elements['viewDeptBt']->addProperty('class',$this->styles['button']);
			

			$this->elements['withoutPass'] = new CCheckBox('withoutPass');
			
			$this->elements['clearDeptBt'] = new CButton('LINK','clearDeptBt','[очистить]');
			$this->elements['clearDeptBt']->addProperty('class',$this->styles['link']);
			$this->elements['clearDeptBt']->setEventListener('onclick','javascript:document.'.$this->name.'.deptName.value=\'\';document.'.$this->name.'.idDept.value=\'\';');
			
			//$this->elements['flWithoutShedule'] = new CCheckBox('flWithoutShedule');
			//статус пропуска 
			//$this->elements['status_admin'] = new CCheckbox('status_admin');
			//$this->elements['status_block'] = new CCheckbox('status_block');
			//$this->elements['status_double'] = new CCheckbox('status_double');
		}
		/////////////////////////////////////
		public function __destruct()
		{
			parent::__destruct();
		}
		////////////////////////////////////
		public function render()
		{
			
			
			$this->view = '<form ';
			//свойства
			$this->view .= $this->getAllProperties();
			$this->view .= '>';
			$this->view .= $this->elements['act']->render();
			$this->view .= $this->elements['idDept']->render();
			
			 if ( $this->styles['table'] != null )
				$this->view .= ' <table class="'.$this->styles['table'].'" cellpadding="0" cellspacing="0">';
			else	
				$this->view .= ' <table  cellpadding="0" cellspacing="0">';
		   
		   $this->view .= '
			<tr>
				<th  colspan="2">Параметры поиска</th>
			</tr>
			<tr>
				<td width="20%">Tab. номер </td>
				<td  >'.$this->elements['tabNum']->render().'</td>
			</tr>
			<tr>
				<td>Фамилия</td>
				<td>'.$this->elements['family']->render().'</td>
			</tr>
			<tr>
				<td>Имя</td>
				<td>'.$this->elements['firstName']->render().'</td>
			</tr>
			<tr>
				<td >Отчество</td>
				<td >'.$this->elements['surName']->render().'</td>
			</tr>
			<tr>
				<td >Должность</td>
				<td >'.$this->elements['position']->render() .'</td>
			</tr>
			<tr>
				<td >Код пропуска</td>
				<td >'.$this->elements['passCode']->render() .'</td>
			</tr>
			<tr>
				<td >Отдел</td>
				<td >'.$this->elements['deptName']->render() .' '.$this->elements['viewDeptBt']->render().' '.$this->elements['clearDeptBt']->render().'</td>
			</tr>
			<tr>
				<td >Пропуск</td>
				<td >'.$this->elements['withoutPass']->render() .' без пропуска
				</td>
			</tr>
			
			<tr>
				<th colspan="2" style="text-align:center">'.$this->elements['searchBt']->render().' '.$this->elements['cancelBt']->render().' </th>
			</tr>';
			$this->view .= '</table>';
			$this->view .= '</form>';
	
			return $this->view;
	   }
		
}


class CEmployePrintForm extends CForm
{
	public function __construct($name)
	{
			parent:: __construct($name,null,'POST');
			$this->styles['textField']  = 'textField';
			$this->styles['table'] 		= 'formTable';
			$this->styles['button'] 	= 'sbutton';	
			$this->styles['select']		= 'select';
			$this->styles['link']		= 'linkBt';
			
			$this->elements['refName']  =  new CTextField('refName','','hidden');

			$this->elements['chTabNum'] 	= new CCheckBox('chTabNum');
			$this->elements['chTabNum']		->setChecked(true);
			$this->elements['chTabNum']		->setDisabled(true);
			
			$this->elements['chFio'] 		= new CCheckBox('chFio');
			$this->elements['chFio']		->setChecked(true);
			$this->elements['chFio']		->setDisabled(true);
			
			$this->elements['chDepartment'] 	= new CCheckBox('chDepartment');
			$this->elements['chPosition'] 		= new CCheckBox('chPosition');
			$this->elements['chShedule'] 		= new CCheckBox('chShedule');
			$this->elements['chPassCode'] 		= new CCheckBox('chPassCode');
			//$this->elements['chStatus']			= new CCheckBox('chStatus');
			
			$this->elements['chToExcel'] 		= new CCheckBox('chToExcel');
			
			$this->elements['printBt'] 			= new CButton('SUBMIT','printBt','печать');
			$this->elements['printBt'] 			->addProperty('class',$this->styles['button']);
			
			$this->elements['cancelBt'] 		= new CButton('SIMPLE','cancelBt','отмена');
			$this->elements['cancelBt'] 		->addProperty('class',$this->styles['button']);
			
			
	}
	//////////////////////////////////
	public function __destruct()
	{
			parent::__destruct();
	}
	//////////////////////////////
	public function render()
	{
	    	$this->view = '<form  ';
						//свойства
			$this->view .= $this->getAllProperties();
			$this->view .= '>';
			
			$this->view .= $this->elements['refName']->render();
			 if ( $this->styles['table'] != null )
				$this->view .= ' <table class="'.$this->styles['table'].'" cellpadding="0" cellspacing="0">';
			else	
				$this->view .= ' <table  cellpadding="0" cellspacing="0">';
		   
		   $this->view .= '
		   <tr>
				<th >Печать</th>
			</tr>
			<tr>
				<td>
				<input type="radio" name="printPage" value="1" checked>Текущая
				<input type="radio" name="printPage" value="2">Все
				</td>
			</tr>
			<tr>
				<td ><b>Поля<b></td>
			</tr>
			<tr>
				<td>';
			$this->view .= 	$this->elements['chTabNum']->render() .'Таб.номер&nbsp;&nbsp;';
			$this->view .= 	$this->elements['chFio']->render().'ФИО&nbsp;&nbsp;';
			
			$this->view .='</td>';
			$this->view .= '</tr>';
			
			$this->view .= '<tr><td>';
			$this->view .= 	$this->elements['chDepartment']->render() .'Отделы&nbsp;&nbsp;';
			$this->view .= 	$this->elements['chPosition']->render() .'Должность&nbsp;&nbsp;';
			$this->view .= 	$this->elements['chShedule']->render() .'График&nbsp;&nbsp;';
			$this->view .= 	$this->elements['chPassCode']->render() .'Код пропуска&nbsp;&nbsp;';
			//$this->view .= 	$this->elements['chStatus']->render() .'Статус&nbsp;&nbsp;';
			
			$this->view .= '<tr>';
			$this->view .= '<td><b>Вывод<b></td>';
			$this->view .= '</tr>';
			
			$this->view .= '<tr><td>';
			$this->view .= $this->elements['chToExcel']->render().' в Excel';
			$this->view .= '</td></tr>';
						
			$this->view .= '<tr>';
			$this->view .= '<td style="text-align:right">';
			$this->view .= $this->elements['printBt']->render();
			$this->view .= $this->elements['cancelBt']->render();
			$this->view .= '</td>';
			$this->view .= '</tr>';
			
			$this->view .= '</table></form>';
		   
		   return $this->view;
	}	
}
?>