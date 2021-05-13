<?php
require_once('forms.h');

interface IList 
{
   public function render();
  // public function start();
   //public function end();
}

/**
Для создания списков.
*/
abstract class CList
{
  /** 
    Указывает тип источника данных.
	Типы источников: <br>
	query - строка запроса. <br>
	xml - xml файл.<br>
	csv - csv файл.<br>
  */
  protected $sourceType = null;
  /** Указывает имя списка данных.*/
  protected $name = null;
  /** 
    Массив для условий по которым будет формироваться список<br>
    В качестве элементов содержит так же массивы вида: value = значение,type - тип.<br> 
	Актуально только если sourceType == query;
	*/
  protected $parameters = array();
  
   /** Массив для описания выводимых полей */
  protected $fields = array();
 
  protected $data = null;
  protected $dataerror = null;
  
  public function __construct($name,$sourceType)
  {
    $Types = array('query','xml','csv');
	if(!in_array($sourceType,$Types)) die ('CList:Неизвестный тип источника данных');
	
	$this->sourceType = $sourceType;
	$this->name = $name;
	
  }
  ///////////////////////////////////////
  public function __destruct()
  {
	unset( $this->parameters );
	unset( $this->data );
  }
  //////////////////////////////////////
  public function __toString()
  {
	$t  = '';
	$t .= 'List name :'. $this->name.'<br>';
	$t .= 'Source type :'. $this->sourceType.'<br>';
	$t .= 'Data error :'. $this->dataerror.'<br>';
	$t .= 'Fields: <br> ';
	foreach ( $this->fields as $key=>$value )
		$t .= $key.'=>'.$value['head'] .'=>'.$value['type'] .'<br>';
    
	$t .= 'Parameters: <br> ';		
	foreach ( $this->parameters as $key=>$value )
		$t .= $key.'=>'.$value['value'] .'=>'.$value['type'] .'<br>';
    	
	return $t;
  }
  /////////////////////////////////////
  /** 
	 Сохраняет в массив parameters условие выборки под именем $name со значением $value
	 и типом значения $type если такого условия не существует. <br> $type - может равнятся string (строка,дата) или digit (числа, а так же null)  
  */
  public function setParameter($name,$value,$type)
  {
	if(!array_key_exists($name,$this->parameters) && ($type == 'string' || $type == 'digit'))  
	{ 
	   $this->parameters[$name]['value'] = $value;
	   $this->parameters[$name]['type'] = $type;
	}
  }
  ////////////////////////////////////
  /** Изменяет значение параметра $name на значение $value и тип значения $type если такой существует. */
  public function changeParameter($name,$value,$type)
  {
	if(array_key_exists($name,$this->parameters) && $this->parameters[$name]!=null) 
	{ 
	   $this->parameters[$name]['value'] = $value;
	   $this->parameters[$name]['type'] = $type;
	}	
  }
  /**Удаляет параметр $name из массива $parameters*/
  public function removeParameter($name)
  {
	if(array_key_exists($name,$this->parameters)) $this->parameters[$name] = null;
  }
  /** 
	 Возвращает параметры как строку разделённую запятыми, отбрасывает параметры, имена которых указаны 
	 в  аргументах метода 
	*/
  public function getStringParametersWithout()
  {
	$args = func_get_args();
	$str = '';
	foreach ($this->parameters as $key => $value)
	{
	   if($value !=null && !in_array($key,$args))
		{
		  if($value['type'] == 'string')
				$str .= '"'.$value['value'].'",' ;
			else if($value['type'] == 'digit')
				$str .= $value['value'].',' ;
		}
	}
	return $str = substr($str,0,strlen($str)-1);
  }

  public function addField($name_field_in_db,$name_showed_field,$renderType,$isSorted = false)
  {
	 if(!array_key_exists($name_field_in_db,$this->fields))
	 {
	    $this->fields[$name_field_in_db]['head'] = $name_showed_field;
		$this->fields[$name_field_in_db]['type'] = $renderType;
		$this->fields[$name_field_in_db]['sorted'] = $isSorted;
		
	 }
  }
  /**
	Выполняет запрос $query.При удачном выполнении возвращает результирующий набор иначе null и сохраняет ошибку в свойстве dataerror. 
	*/
  public function getResultSetFromQuery($query)
  {
	   if($this->sourceType != 'query') die ('CList:Для указанного типа сточника данных нужно вызвать соответсвующий обработчик');
	   
	   $this->data = @mssql_query($query);
       if(!$this->data) 
	   { 
	     $this->data = null;
		 $this->dataerror = @mssql_get_last_message();
       }
        return $this->data;	   
	}
	public function  getResultSetFromProc($proc_name)
	{
	   //создание запроса
	   $params = '';
	   $sql = '';
	   foreach ($this->parameters  as $key => $value)
       {
	      if($value !=null)
		  {
			if($value['type'] == 'string')
				$params .= '"'.$value['value'].'",' ;
			else if($value['type'] == 'digit')
				$params .= $value['value'].',' ;
		 }		
       }
	   $params = substr($params,0,strlen($params)-1);
	   $sql = $proc_name.' '.$params;
      //echo $sql.'<br>';	   
	   $this->data = @mssql_query($sql);
       if(!$this->data) 
	   { 
	     $this->data = null;
		 $this->dataerror = @mssql_get_last_message();
       }
        return $this->data;	   
	}
  /*
    Метод читает файл filePath и забивает его содержимое в data. delimiter - символ разделитель 
   */  
  public function getResultSetFromCSV($filePath,$delimiter)
  {
     
  }
  public function getLastError()
  {
    return $this->dataerror;
  }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class CPagesList extends CList implements IList
{
  
  protected $buttons = array();
  
  protected $styles = array();
  protected $elementListeners = array();
  
  
  protected $useSorting = false;
  /** Число с которого нумеруются колонки. */
  protected $colsNumbering = 'null';
  /** колличество выводимых элементов на одной странице */
  protected $length;
  
  /** Номер текущей страницы */
  protected $currentPage;  
  /** Номер колонки по которой будет выполнена сортировка */
  protected $sortCol;
  /** Направление сортировки 0 - вверх, 1 - вниз */
  protected $sortDirect;
  /** Всего найденых строк */
  protected $totalRows = null;
  /** Всего страниц */
  protected $totalPages = 0;
  /** Верхняя граница */
  protected $topBorder = 0;
  /** Нижняя граница */
  protected $bottomBorder = 0;
  //
  protected $groupOperations = array();
  //
  protected $listenerUrl;
  
  public function __construct($name,$sourceType,$listenerUrl=null)
  {
    //echo '<br>'.$name.'<br>';
	//проверяем, есть в сессии какие нибудь списки
	if(!isset($_SESSION['List']) || $_SESSION['List']['name'] != $name) 
	{
	   $_SESSION['List']['name'] = $name;
	   $_SESSION['List']['current_page'] =  1;
	   $_SESSION['List']['sort_col']     =  'null';
	   $_SESSION['List']['sort_direct']  =  'null';
	   $_SESSION['List']['page_length']  = 50;
	   $this->currentPage = $_SESSION['List']['current_page'];
	   $this->sortCol = $_SESSION['List']['sort_col'];
	   $this->sortDirect = $_SESSION['List']['sort_direct'];
	   $this->length = $_SESSION['List']['page_length'];
    }
    else
    {
	    //берём параметры из сессии если  имена списков совпадают
		if($_SESSION['List']['name'] == $name ) 
		{
			
			if(isset($_POST['page'])) $_SESSION['List']['current_page'] = $_POST['page'];
			if(isset($_POST['sort_col'])) $_SESSION['List']['sort_col'] = $_POST['sort_col']; 
			if(isset($_POST['sort_direct'])) $_SESSION['List']['sort_direct'] = $_POST['sort_direct']; 
			if(isset($_POST['page_length'])) $_SESSION['List']['page_length'] = $_POST['page_length'];
			
		   $this->currentPage = $_SESSION['List']['current_page'];
		   $this->sortCol = $_SESSION['List']['sort_col'];
		   $this->sortDirect = $_SESSION['List']['sort_direct'];
		   $this->length = $_SESSION['List']['page_length'];
		}
    }   	
	//инициализируем массив стилей
	$this->styles['select'] = '';
	$this->styles['table'] = '';
	$this->styles['button'] = '';
	$this->styles['links'] = '';
	$this->styles['footer'] = '';
	$this->styles['top'] = '';
	//инициализируем массив обработчиков
	$this->elementListeners['onmouseover'] = '';
	$this->elementListeners['onmouseout'] = '';
	$this->elementListeners['onclick'] = '';
	
	$this->listenerUrl = $listenerUrl;
	
	parent::__construct($name,$sourceType);
  }
  public function __destruct()
  {
	  unset($this->elementListeners);
	  unset($this->styles); 
	  unset($this->buttons);
	  unset($this->groupOperations);
	  unset($this->elementConditions);
	  
	  parent::__destruct();
  } 
  public function __toString()
  {
     $t = ''; 
	 //$t = parent::__toString();
	  $t .= 'List length: '. $this->length.'<br>';
	  $t .= 'Number of sort column: '. $this->sortCol.'<br>';
	  $t .= 'Sort direction: '. $this->sortDirect.'<br>';
	  $t .= 'Total rows: '. $this->totalRows.'<br>';
	  $t .= 'Total pages: '. $this->totalPages.'<br>';
	  $t .= 'Current Page: '. $this->currentPage.'<br>';
	  $t .= 'Top Border: '. $this->topBorder.'<br>';
	  $t .= 'Bottom border: '. $this->bottomBorder.'<br>';
	  return $t;
  }
  public function addButton($name,$img_src,$listener)
  {
     $args = func_get_args();
	 //echo sizeof($args);
	 $size = sizeof($args);
	 if($size >= 3 )
	 {
	   $this->buttons[$name]['src'] = $img_src;
	   $this->buttons[$name]['listener'] = $listener;
	   $this->buttons[$name]['args'] = array(); 
	   
	   for ( $i = 3; $i < $size; $i++  )
	     $this->buttons[$name]['args'][] = $args[$i];
	   
	 //  print_r($this->buttons);
	 }
	 else
	   die ('CPagesList: Слишком мало аргументов.');
  }
 
  
  public function setGroupOperation($name,$value)
  {
    if(!array_key_exists($name,$this->groupOperations))
        $this->groupOperations[$name] = $value;
  }
  
  public function setColumnsNumbering($value)
  {
     $this->useSorting = true;
	 $this->colsNumbering = $value;
  }
  public function setStyle($name,$value)
  {
     if(array_key_exists($name,$this->styles))
        $this->styles[$name] = $value;
     else
       die('CPagesList->setStyle: Не найдено указное имя стилей');	 
  }
  public function addElementListener($event_name,$listener_name)
  {
      if(array_key_exists($event_name,$this->elementListeners))
        $this->elementListeners[$event_name] = $listener_name;
     else
       die('CPagesList->addElementListener: Не найдено указное имя обработчика');	
  }
  public function removeElementListener($name)
  {
	if(array_key_exists($event_name,$this->elementListeners))
        $this->elementListeners[$event_name] = '';
  }
  //для получения колличества строк
  public function  getRowsQty($q,$useParams)
  {
	  $params  = '';
	  if($useParams)
	  {
		$params .= 'null,';
		$params .= 'null,';
		$params .= $this->sortCol.',';
		$params .= $this->sortDirect.',';
		//присоединяем парметры процедуры.
		$params .= $this->getStringParametersWithout();
	  }	
	  
	  $query = $q.' '.$params;
    //  echo 	$query.'<br>';
	 //получаем количество 
    $this->getResultSetFromQuery($query);	  
	 //если запрос не обламался то рассчитываем необходимые параметры
	 if($this->data != null) 
	 { 
	   $this->totalRows = mssql_num_rows($this->data);
	   $this->totalPages = ceil($this->totalRows / $this->length)-1;
	   //если последняя страница
	   if($this->currentPage == ($this->totalPages + 1))
	   {
			$this->topBorder = $this->totalRows - (($this->currentPage-1) * $this->length);
			$this->bottomBorder = $this->totalRows;
	   }
	   else
	   {
			$this->topBorder = $this->length;
			$this->bottomBorder = $this->currentPage * $this->length;
	   }
	   
	   //если длина страницы больше колличества найденых строк
	   if($this->length > $this->totalRows)
	   {
		 $this->topBorder = $this->totalRows;
		 $this->bottomBorder = $this->totalRows; 
	   }
	 }
     else
     {
		die ('CPagesList: Не удалось подсчитать количество строк:'.$this->dataerror);
	  } 	 
	 
	// echo $this->totalRows;
  }
  public function getAllParameters()
  {
        $params = '';
		$params .= $this->topBorder.',';
		$params .= $this->bottomBorder.',';
		$params .= $this->sortCol.',';
		$params .= $this->sortDirect.',';
		//присоединяем параметры процедуры.
		$params .= $this->getStringParametersWithout();
		return $params;
  }
  public function render()
  {
     if( $this->data != null)
     {	 
          if($this->data == null)
			{ 
				echo  '<tr><td  colspan="'.sizeof($this->fields).'">'.$this->dataerror.'</td></tr>';
			}
			//processing empty set
			else if (mssql_num_rows($this->data) == 0)
			{
				echo  '<tr><td  colspan="'.sizeof($this->fields).'">Нет данных.</td></tr>';
			}	 
			else
			{
				$size = sizeof($this->fields);
		     	while($r = mssql_fetch_array($this->data))
				{
					//формируем строку обработчиков событий
					$listeners = '';
					$extend = '';
					foreach ($this->elementListeners as $key => $value)
					{
						if($value != '') $listeners .= $key.'='.$value.' ';
					}
				   
				  $element = '<tr '.$listeners;
				  $str = '';
                  //добавляем checkbox
                  $str .= '<td><input type="checkbox" onclick=listCheckBoxClick(this)></td>';				  
				  foreach ($this->fields as $key=>$value )
				  {
  						$str .= '<td >'.$r[$key].'</td>';
		          }
				   //добовляем кнопки
				  foreach ($this->buttons as $key=>$value )
				  {
				     
					 $listener = $value['listener'];
					 $args_size = sizeof($value['args']);
					 for ( $i = 0; $i < $args_size; $i++)
					 {
						$arg = explode('=>',$value['args'][$i]);
						$listener = str_replace($arg[0],str_replace(' ','&nbsp;',$r[$arg[1]]),$listener);
						//$listener = str_replace($arg[0],$r[$arg[1]],$listener);
					 }
					 $str .= '<td><img src="'.$value['src'].'" '.$listener.'></td>';
				  }
					$element.= '>'.$str.'</tr>';
					echo $element;		  
				}//end 	
			}
	 }//end if		
  }// end method
  public function start()
  {
	//выводим скрипты
	$this->printJSScripts();
	
	$fieldsQty= 1 + sizeof($this->fields) + sizeof($this->buttons);
	$action = ($this->listenerUrl != null ) ? ' action="'.$this->listenerUrl.'"' : '';
	$head = '';
	$head	.= '<form name="'.$this->name.'" '.$action.' method="POST">';
	$head	.= '<input type="hidden" name="page" value="'.$this->currentPage.'">';
	$head	.= '<input type="hidden" name="page_length" value="'.$this->length.'">';
	$head	.= '<input type="hidden" name="sort_col" value="'.$this->sortCol.'" >';
	$head	.= '<input type="hidden" name="sort_direct" value="'.$this->sortDirect.'" >';
	$head  .= '<table border="0"  cellpadding="0" cellspacing="0" width="99%" '.$this->styles['table'].'>';
	//первая линия для отображения навигации
	
	$forwardBt = '';
	$backBt = '';
	if($this->currentPage > 1)
		$backBt .= '&nbsp;|&nbsp;<a href="#" '.$this->styles['links'].' onclick="javascript:document.'.$this->name.'.page.value = parseInt(document.'.$this->name.'.pagelist.value) - 1;document.'.$this->name.'.submit()" >предыдущая</a>&nbsp;|&nbsp;';
   
   if($this->currentPage != ($this->totalPages + 1) ) 
		$forwardBt .= '&nbsp;|&nbsp;<a href="#" '.$this->styles['links'].' onclick="javascript:document.'.$this->name.'.page.value =  1 + parseInt(document.'.$this->name.'.pagelist.value);document.'.$this->name.'.submit()">следующая </a>&nbsp;|&nbsp;';
   
	$head .= '<tr><td colspan="'.$fieldsQty.'" style="text-align:right;" align="right">';
	$head .= '<table border="0"  cellpadding="0" cellspacing="0"  width="35%" '.$this->styles['top'].'>';
	$head .= '<tr>'; 
	$head .= '<td width="40%">Страница  '.$this->currentPage.' из '.($this->totalPages+1).'&nbsp;&nbsp;&nbsp;</td><td width="30%">'.$backBt.'</td><td width="30%">'.$forwardBt.'</td>';
	$head .= '</tr>';
	$head .= '</table>';
	$head .= '</td></tr>';
	
	//вставляем чекбокс
	$head  .= '<th><input type="checkbox" onclick=listCheckAllElements(this,document.'.$this->name.')></th>';
	$numbering = $this->colsNumbering;
	foreach($this->fields as $value )
	{
	   $sortListener = '';
	   $cursor = '';
	   if($this->useSorting)
	   {
	    //обработчик сортировки
	     $sortListener = 'onclick=doSort('.$numbering.')';
		 $numbering++;
		  //курсор для сортируемых полей
	     $cursor = ($value['sorted']) ? 'style="cursor:pointer"' : 'style="cursor:default"';
	   	} 
	   $head .= '<th '.$cursor.' '.$sortListener.'>'.$value['head'].'</th>';
	      
	}
	//заголовки под кнопки
	$size = sizeof($this->buttons);
	for( $i = 0; $i < $size; $i++  )
		$head .= '<th></th>';
	echo $head;
  }
  public function end()
  {
   
   $colspan = 1 + sizeof($this->fields) + sizeof($this->buttons); 
   $footer = '';
   $footer .= '<tr><td colspan="'.$colspan.'"><table '.$this->styles['footer'].'><tr>';
   
   $footer .= '<td>';
   $footer .= 'C выделенными: ';
   $footer .= '<select name="operation" '.$this->styles['select'].' style="width:auto;" onchange="javascript:document.'.$this->name.'.submit()">';
   $footer .= '<option>--</option>';
   
   foreach ( $this->groupOperations as $key=>$value )
   {
       $footer .= '<option value="'.$key.'" >'.$value.'</option>';
   }
   $footer .= '</select>';
   
   $footer .= '</td>';
   //навигация по страницам
   $footer .= '<td style="text-align:center">';
  // $footer .= 'Страница # '.$this->currentPage.'&nbsp;&nbsp;&nbsp;';
   if($this->currentPage > 1) 
		$footer .= '&nbsp;|&nbsp;<a href="#" '.$this->styles['links'].' onclick="javascript:document.'.$this->name.'.page.value = parseInt(document.'.$this->name.'.pagelist.value) - 1;document.'.$this->name.'.submit()" >предыдущая</a>&nbsp;|&nbsp;';
   
   $footer .= '<select name="pagelist"'.$this->styles['select'].' onchange="javascript:document.'.$this->name.'.page.value = this.value;document.'.$this->name.'.submit()" >';
   
   $pageQty = $this->totalRows / $this->length;
   
   if($pageQty > 1)
   {
		for ( $i = 0; $i <= $this->totalPages; $i++ )
		{
		   $page = $i + 1;
		   if( $page != $this->currentPage)
		      $footer .= '<option value="'.$page.' ">--'.$page.'--</option>';
			else
             $footer .= '<option value="'.$page.' " selected >--'.$page.'--</option>';		
		}
   }
      
   $footer .= '</select>';
   
   if($this->currentPage != ($this->totalPages + 1) ) 
		$footer .= '&nbsp;|&nbsp;<a href="#" '.$this->styles['links'].' onclick="javascript:document.'.$this->name.'.page.value =  1 + parseInt(document.'.$this->name.'.pagelist.value);document.'.$this->name.'.submit()">следующая </a>&nbsp;|&nbsp;';
   
   $footer .= '</td>';
   
   $footer .= '<td style="text-align:right">';
   $footer .= 'Выводить по <select '.$this->styles['select'].' onchange="javascript:document.'.$this->name.'.page_length.value = this.value;document.'.$this->name.'.submit()"> ';
  
   for ( $i = 10; $i <= 200; $i+=10 )
   {
		if($i != $this->length) 
			$footer .= '<option value="'.$i.'">--'.$i.'--</option>';
		else
         	$footer .= '<option value="'.$i.'" selected="selected" >--'.$i.'--</option>';	
	} 
   $footer .= '</select>записей ';
   $footer .= '</td>';
   
   $footer .= '</tr></table>';
   $footer .= '</td></tr>';
   $footer .= '</table>';
   $footer .= '</form>';
   echo $footer;
  }
  protected function printJSScripts()
  {
    $s = '<script language="javascript">';
	 $s .= 'function listCheckBoxClick(obj) 
			{
			  obj.checked = (obj.checked) ? false : true;
			}
			function listCheckAllElements(obj,form)
			{
				for (var i = 0 ; i < form.elements.length; i++ )
				{
					var el = form.elements[i]; 
					if(el.type == \'checkbox\') 
					{  
						var list_element = el.parentNode.parentNode;
						if(obj.checked == true )
						{
							el.checked = true ;
							list_element.style.backgroundColor = \'#dddddd\';		
						}
						else
						{
							el.checked = false;
							list_element.style.backgroundColor = \'white\';	  
						}
					}   
				}
			}
			function doSort(num_field)
			{
				document.'.$this->name.'.sort_col.value = num_field;
			   if(document.'.$this->name.'.sort_direct.value == 1) document.'.$this->name.'.sort_direct.value = 0;else document.'.$this->name.'.sort_direct.value = 1;
			   document.'.$this->name.'.submit();
			}
			';
	echo $s .= '</script>';
  }
  
}



?>
