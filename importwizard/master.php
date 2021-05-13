<?php
include('../include/input.php');
require_once('../classes/base/pages.h');
include('../include/common.php');
require_once('../include/hua.php');
function showError($msg,$buttons)
{
	$res = '';
	$res .= '<table class="errorTable">';
    $res .= '<tr>';
	$res .= '<th>Ошибка</th>';
	$res .= '</tr>';
	$res .= '<tr>';
	$res .= '<td style="border-bottom:1px solid red">'.$msg.'</td>';
	$res .= '</tr>';
	$res .= '<tr>';
	$res .= '<td align="right">'.$buttons.'</td>';
	$res .= '</tr>';
	$res .= '</table>';
	
	return $res;
}
function removeQuotes($str)
{
	$s = str_replace('"',' ',$str);
	$s = trim($s);
	return $s;
}
function getAllUserTables()
{
	$arr = array('сотрудников','подразделений','пропусков');
	return $arr;
}
function getFieldPositionByName($array,$fieldName)
{
	$pos = 0;
	for ( $i = 0; $i< $array; $i ++ )
	{
		if($array[$i] == $fieldName) 
		{
		  $pos = $i; 
			break;		  
		} 
	}
	return $pos;
} 

$action = '';
$BODY  = '';

if ( isset($_GET['action']) &&  $_GET['action'] != '' ) 
	$action = $_GET['action'];
else
	header("Location: index.php");

$page = new CEmptyPage('Мастер импорта');
$page -> addCSSInclude('css/styles.css');
$page -> addJSInclude('js/controllers.js');
$page->start();
	
	
//session variables
if ( !isset ($_SESSION['impVariables'] ) ) 
{
	$_SESSION['impVariables'] = array();
	$_SESSION['impVariables']['currentStep'] = 1;
	$_SESSION['impVariables']['sourceFileName'] = '';
	$_SESSION['impVariables']['catalog'] = getcwd();
	$_SESSION['impVariables']['fileCatalogName'] = 'files';
	$_SESSION['impVariables']['colSeparator'] = ';';
	$_SESSION['impVariables']['firstRowIsName'] = 0;
	$_SESSION['impVariables']['fieldsNames'] = array();
	
}	
//
//Step 1.0
//start
if ( $action == 'start' )
{
	$BODY = '';
	$BODY .= '<form enctype = "multipart/form-data" name="uploadform" action="master.php?action=upload_file" method="POST">';
	$BODY .= '<table class="someTable" border="0" cellpadding="0" cellspacing="0" align="center">';
	$BODY .= '<tr>';
	$BODY .= '<th style="border-bottom:1px solid #b7cee4">Шаг 1. Загрузка файла </th>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td>Укажите путь к файлу с имортируемыми данными</td>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td><input type="FILE" name="uploadfile" size="40" class="input"></td>';
	$BODY .= '</tr>';

	$BODY .= '<tr>';
	$BODY .= '<td style="border-bottom:1px solid #b7cee4">&nbsp;</td>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td align="right"> [ <a href="javascript:onclick=window.close()" class="bigBtLink">отмена</a> ] &nbsp; [ <a href="javascript:onclick=document.uploadform.submit()" class="bigBtLink">далее</a> ]</td>';
	$BODY .= '</tr>';

	$BODY .= '</table>';
	$BODY .= '</form>';
}
//Step 1.1
//file upload
if ($action == 'upload_file')
{ 
	
	if( isset($_FILES['uploadfile']) )
    {
      $file_tmp=$_FILES["uploadfile"]["tmp_name"];// - Имя временного файла
      $file_name=$_FILES["uploadfile"]["name"];// - Имя файла на компьютере пользователя
      $file_size=$_FILES["uploadfile"]["size"];// - Размер файла в байтах
      $file_type=$_FILES["uploadfile"]["type"]; //- MIME-тип файла
      $file_error=$_FILES["uploadfile"]["error"];// - код ошибки.
      
	  
	    $_SESSION['impVariables']['sourceFileName'] = $file_name;
      
		if($file_error==0)
		{
           
            $uploadfiledir =  $_SESSION['impVariables']['catalog'].'\\'.$_SESSION['impVariables']['fileCatalogName'].'\\';
			
		    if(!@move_uploaded_file($file_tmp,$uploadfiledir.$file_name))
            {
            	
				$msg  = 'При перемещении файла возникла ошибка. Возможно не найдена директория назначения.<br>Для выхода нажмите &quotотмена&quot';
				$buttons = '[ <a href="javascript:onclick=window.close()" class="bigBtLink">отмена</a> ]';
				$BODY .= showError($msg,$buttons);
				unset ($_SESSION['impVariables']);
				
            }
            else
            {
				$_SESSION['impVariables']['currentStep'] = 2;
				Header("Location:master.php?action=file_is_load");
            	
            }
		}
		else
		{
			    unset ($_SESSION['impVariables']);
				$msg  = 'При загрузке файла возникла ошибка. Код ошибки:'.$file_error.'<br>Для выхода нажмите &quotотмена&quot. Для возврата к предидущему шагу нажмите &quotназад&quot';
				$buttons = '[ <a href="javascript:onclick=window.close()" class="bigBtLink">отмена</a> ] &nbsp; [ <a href="master.php?action=start" class="bigBtLink">назад</a> ]';
				$BODY .= showError($msg,$buttons);
				
		}
  }
}
//Step 2.0	
if ( $action == 'file_is_load' )
{
	$BODY .= '<form name="fileSet" action="master.php?action=file_settings" method="POST">';
	$BODY .= '<table class="settingsTable" border="0" cellpadding="0" cellspacing="0" align="center">';
	$BODY .= '<tr>';
	$BODY .= '<th colspan="2" style="border-bottom:1px solid #b7cee4">Шаг 2. Настройки Файла импорта </th>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td width="40%"><b>Имя исходного файла:</b></td>';
	$BODY .= '<td >'.$_SESSION['impVariables']['sourceFileName'].' </td>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td width="40%"><b>Разделитель колонок:</b></td>';
	$BODY .= '<td><input type="text" value=";" name="col_separator" class="input"></td>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td width="40%"><b>Первая строка - название колонок:</b></td>';
	$BODY .= '<td><input type="checkbox" name="first_row_is_name"></td>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td align="center" colspan="2">  [ <a href="javascript:onclick=showCSVFileContent(\'file.php\',\''.$_SESSION['impVariables']['sourceFileName'].'\',document.fileSet.first_row_is_name.checked,document.fileSet.col_separator.value);" class="bigBtLink">просмотреть файл</a> ] </td>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td align="right" colspan="2">  [ <a href="javascript:onclick=window.close()"  class="bigBtLink">отмена</a> ] [ <a href="javascript:onclick = document.fileSet.submit() "  class="bigBtLink">далее</a> ]</td>';
	$BODY .= '</tr>';

	$BODY .= '</table>';
	$BODY .= '</form>';
}
//Step2.1
if($action == 'file_settings')
{
	$errorFlg = false;
	$errorMsg = '';
	//print_r($_POST);
	//print_r($_SESSION['impVariables']);
	if( !isset( $_POST['col_separator'] ) || $_POST['col_separator'] == '' )
	{
		$errorMsg .= 'Не указан разделитель колонок.';
		$errorFlg = true;
	}
	if ( $errorFlg )
	{
		$buttons = '[ <a href="javascript:onclick=window.close()" class="bigBtLink">отмена</a> ] &nbsp; [ <a href="master.php?action=file_is_load" class="bigBtLink">назад</a> ]';
		$BODY .= showError($errorMsg,$buttons);
	}
	else
	{
		$_SESSION['impVariables']['colSeparator'] = $_POST['col_separator'];
		$_SESSION['impVariables']['firstRowIsName'] = ( isset($_POST['first_row_is_name']) ) ? 1 : 0 ;
		
		//пытаемся открыть файл для чтения имён колонок
		$file_path = $_SESSION['impVariables']['catalog'].'\\'.$_SESSION['impVariables']['fileCatalogName'].'\\'.$_SESSION['impVariables']['sourceFileName'];
		//echo $file_path;
		$file = file($file_path);
		
		if( sizeof($file) == 0)
		{
			$buttons = '[ <a href="javascript:onclick=window.close()" class="bigBtLink">отмена</a> ] &nbsp; [ <a href="master.php?action=start" class="bigBtLink">назад</a> ]';
		    $BODY .= showError('Файл пустой.',$buttons);
		}
		else
		{
		  $fields = explode($_SESSION['impVariables']['colSeparator'],$file[0]);
			//print_r($_SESSION['impVariables']);
		  if ( $_SESSION['impVariables']['firstRowIsName'] == 1 ) 
		  {
			$_SESSION['impVariables']['fieldsNames'] = $fields;
		  }
		  else
		  {
			for ($i = 0; $i < sizeof($fields); $i++ )
				$_SESSION['impVariables']['fieldsNames'][$i] = 'Колонка_'.$i;
		  }
		  
		  header('location: master.php?action=import_settings');
		} 
	}
}
//Step 3.0

if( $action == 'import_settings' )
{
	//print_r($_SESSION['impVariables']);
	$BODY .= '<form name="importSet" action="master.php?action=exec" method="POST">';
	$BODY .= '<table class="settingsTable" border="0" cellpadding="0" cellspacing="0" align="center">';
	$BODY .= '<tr>';
	$BODY .= '<th colspan="2" style="border-bottom:1px solid #b7cee4">Шаг 3. Настройки импорта </th>';
	$BODY .= '</tr>';
	$BODY .= '<tr>';
	$BODY .= '<td width="20%">Тип</td>';
		$BODY .= '<td>';
		$BODY .= '<select name="import_type" class="input"><option value="0">Вставка</option></select>';
		$BODY .= '</td>';
	$BODY .= '</tr>';
	
	$BODY .= '<tr>';
	$BODY .= '<td width="20%">Импортировать в таблицу:</td>';
		$BODY .= '<td>';
		$BODY .= '<select name="dest_table" class="input" onchange="onImortTableChange(this)">';
		
		$tables =  getAllUserTables();
		foreach ( $tables as $key=>$value )
		{
			$BODY .= '<option value="'.$key.'">'.$value.'</option>';
		}
		
		
		$BODY .= '</select>';
		$BODY .= '</td>';
	$BODY .= '</tr>';
	
	$BODY .= '<tr>';
	$BODY .= '<td colspan="2" align="center"><b>Настройка полей</b> </td>';	
	$BODY .= '</tr>';
	
	$BODY .= '<tr>';
	$BODY .= '<td colspan="2" align="center">';
	require('set_forms.php');
	$BODY .= '</td>';
	$BODY .= '</tr>';
	
	$BODY .= '<tr>';
	$BODY .= '<td align="right" colspan="2">  [ <a href="javascript:onclick=window.close()"  class="bigBtLink">отмена</a> ] [ <a href="javascript:onclick = document.importSet.submit() "  class="bigBtLink">далее</a> ]</td>';
	$BODY .= '</tr>';
	
	$BODY .= '</table>';
	$BODY .= '</form>';
}

if( $action == 'exec' )
{
	$errorFlg = false;
	$errorMsg = '';
	$destTable = '';
	$bindPass = false;
	//print_r($_POST);

	if( !isset($_POST['dest_table']) || $_POST['dest_table'] < 0 )
	{
		$errorMsg .= 'Не указана таблица в которую импортируются данные.<br>';
		$errorFlg = true;	
	}
	else
	{
		$destTable = $_POST['dest_table'];
	}
	
		
	switch ( $destTable )
	{
		
		case '0':  
			//////////////////////НАЧАЛО ОБРАБОТКИ ИМПОРТА СОТРУДНИКОВ////////////////////////
			
			//print_r($_POST);
			if ( !isset ($_POST['pers_fields_0'])  || ($_POST['pers_fields_0'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - UID сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			if ( !isset ($_POST['pers_fields_1'])  || ($_POST['pers_fields_1'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - Таб.номер сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			if ( !isset ($_POST['pers_fields_2'])  || ($_POST['pers_fields_2'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - Фамилия сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			if ( !isset ($_POST['pers_fields_3'])  || ($_POST['pers_fields_3'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - Имя сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			if ( !isset ($_POST['pers_fields_4'])  || ($_POST['pers_fields_4'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - Отчество сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			if ( !isset ($_POST['pers_fields_5'])  || ($_POST['pers_fields_5'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - Должность сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			if ( !isset ($_POST['pers_fields_6'])  || ($_POST['pers_fields_6'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - UID подразделения сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			if ( !isset ($_POST['pers_fields_7'])  || ($_POST['pers_fields_7'] < 0) )
			{
				    $errorMsg .= 'Не указано обязательное поле - имя фотографии сотрудника.<br>';
					$errorFlg = true;
					break;
			}
			
			echo '<table class="settingsTable" cellpadding="0" cellsapcing="0">';
			echo '<tr><th>№ </th><th>Действие</th></th>';
			$file_path = $_SESSION['impVariables']['catalog'].'\\'.$_SESSION['impVariables']['fileCatalogName'].'\\'.$_SESSION['impVariables']['sourceFileName']; 
			$file = @file($file_path);
			if(!$file )
			{
				$errorMsg = 'Файл импорта не найден.<br>';
				$errorFlg = true;
				break;	
			}
                        $q = 'select sys_write_log(0,\'importwizard/master.php\',\'\',\'Начинаем импорт сотрудников'.'\',500,0)';
                        pg_query($q);
                        $clear_q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
			//очищаем таблицу
			$clear_q .= 'DELETE FROM BASE_PERSONAL';
			if ( @pg_query($clear_q) )
			{
                            echo '<tr><td colspan="2">Таблица персонала очищена.</td></tr>';
			} 
			else
			{
                            $errorMsg = 'Не удалось очистить таблицу подразделений.<br>';
                            $errorMsg .= 'Ошибка SQL: '.  pg_last_error();
                            $errorFlg = true;
                            break;	
			}
	
			$success_query_count = 0;
			$failed_query_count = 0;
				
			$start_pos = ($_SESSION['impVariables']['firstRowIsName'] == 1) ? 1: 0;
				
			$file_size = sizeof($file);
			for ( $i = $start_pos; $i < $file_size; $i++ ) 
			{
					echo '<tr><td>'.$i.'</td>';
					
					$item = explode($_SESSION['impVariables']['colSeparator'],$file[$i]);
					$q = "INSERT INTO BASE_PERSONAL (ID,TABEL_NUM,FAMILY,NAME,SECNAME,POSITION,ID_DEPT,PHOTO) ";
					$q .= " VALUES (";
					$q .= removeQuotes($item[$_POST['pers_fields_0']]).",";
					$q .= removeQuotes($item[$_POST['pers_fields_1']]).",";
					$q .= "'".removeQuotes($item[$_POST['pers_fields_2']])."',";
					$q .= "'".removeQuotes($item[$_POST['pers_fields_3']])."',";
					$q .= "'".removeQuotes($item[$_POST['pers_fields_4']])."',";
					$q .= "'".removeQuotes($item[$_POST['pers_fields_5']])."',";
					$q .= removeQuotes($item[$_POST['pers_fields_6']]).",";
					$q .= "'".removeQuotes($item[$_POST['pers_fields_7']])."'";
					$q .= ")";
					echo '<td>';
					//echo $q;
					if ( @pg_query($q) )
					{
						echo 'Сотрудник - '.$item[$_POST['pers_fields_1']].'- '.$item[$_POST['pers_fields_2']].' '.$item[$_POST['pers_fields_3']].' '.$item[$_POST['pers_fields_4']] .' добавлен.';
						$success_query_count++;
					}
					else
					{
						echo 'Не удалось добавить сотрудника - '.$item[$_POST['pers_fields_1']].'- '.$item[$_POST['pers_fields_2']].' '.$item[$_POST['pers_fields_3']].' '.$item[$_POST['pers_fields_4']].'.';
						echo '<br>Ошибка SQL: '.pg_last_error();
						$failed_query_count ++;
					}
					echo '</td></tr>';
					
			}		
			echo '<tr><th colspan="2">Строк в файле: '.$file_size.'<br>Импортировано сотрудников: '.$success_query_count.'<br>Ошибок импортирования сотрудников: '.$failed_query_count.' </th></tr>';					
			
			echo '</table>';	
			//////////////////////КОНЕЦ ОБРАБОТКИ ИМПОРТА СОТРУДНИКОВ////////////////////////
		break;
		
		case '1':
				//////////////////////НАЧАЛО ОБРАБОТКИ ИМПОРТА ПОДРАЗДЕЛЕНИЙ////////////////////////
				//print_r($_POST);
				if ( !isset ($_POST['dept_fields_1'])  || $_POST['dept_fields_1'] < 0 )
				{
					$errorMsg .= 'Не указано обязательное поле названия подразделения.<br>';
					$errorFlg = true;
					break;
				}
				
				echo '<table class="settingsTable" cellpadding="0" cellsapcing="0">';
				echo '<tr><th>№ </th><th>Действие</th></th>';
				$file_path = $_SESSION['impVariables']['catalog'].'\\'.$_SESSION['impVariables']['fileCatalogName'].'\\'.$_SESSION['impVariables']['sourceFileName']; 
				$file = @file($file_path);
				if(!$file )
				{
					$errorMsg = 'Файл импорта не найден.<br>';
					$errorFlg = true;
					break;	
				}
				
				//импортируем подразделения
				// - очищаем таблицу подразделений
                                $q = 'select sys_write_log(0,\'importwizard/master.php\',\'\',\'Начинаем импорт отделов'.'\',500,0)';
                                pg_query($q);
                                $clear_q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
				$clear_q .= 'DELETE FROM BASE_DEPT';
				if ( @pg_query($clear_q) )
				{
					echo '<tr><td colspan="2">Таблица подразделений очищена.</td></tr>';
				} 
				else
				{
					$errorMsg = 'Не удалось очистить таблицу подразделений.<br>';
					$errorMsg .= 'Ошибка SQL: '.pg_last_error();
					$errorFlg = true;
					break;	
				}
				//определяем позиции колонок
				$id_pos = (!isset ($_POST['dept_fields_0'])  || $_POST['dept_fields_0'] < 0) ? null : $_POST['dept_fields_0'];
				$name_pos = $_POST['dept_fields_1'];
				
								
				$success_query_count = 0;
				$failed_query_count = 0;
				
				$start_pos = ($_SESSION['impVariables']['firstRowIsName'] == 1) ? 1: 0;
				
				$file_size = sizeof($file);
				for ( $i = $start_pos; $i < $file_size; $i++ ) 
				{
					echo '<tr><td>'.$i.'</td>';
					
					$item = explode($_SESSION['impVariables']['colSeparator'],$file[$i]);
					
					$id = ($id_pos == null) ? $i : $item[$id_pos];
					$name = $item[$name_pos];
					
					//срезаем кавычки.
					$id = trim(str_replace('"',' ',$id));
					$name = trim(str_replace('"',' ',$name));
					
					$q = "INSERT INTO BASE_DEPT (ID,NAME) ";
					$q .= " VALUES(".$id.",'".CheckString($name)."')";
					
					echo '<td>';
					
					//echo $q.'<br>';
					if ( @pg_query($q) )
					{
                                                $qqq = "INSERT INTO BASE_ACCEESS (ID_USERS,ID_DEPT) ";
                                                $qqq .= " VALUES(".$_COOKIE['id_user'].",".$id.")";
                                                 @pg_query($qqq);
						echo 'Подразделение - '.$name.' добавлено.';
						$success_query_count++;
					}
					else
					{
						echo 'Не удалось вставить отдел.';
						echo '<br>Ошибка SQL: '.  pg_last_error();
						$failed_query_count ++;
					}
					echo '</td></tr>';
				}	
				echo '<tr><th colspan="2">Строк в файле: '.$file_size.'<br>Импортировано подразделений: '.$success_query_count.'<br>Ошибок импортирования подразделений: '.$failed_query_count.' </th></tr>';					
				echo '</table>';
		break;
		//////////////////////КОНЕЦ ОБРАБОТКИ ИМПОРТА ПОДРАЗДЕЛЕНИЙ////////////////////////
		case '2':
				//////////////////////НАЧАЛО ОБРАБОТКИ ИМПОРТА ПРОПУСКОВ/////////////////////////////////////////////
				
				if ( !isset ($_POST['file_fields_1'])  || ($_POST['file_fields_1'] < 0) )
				{
					$errorMsg .= 'Не указано обязательное поле кода пропуска.<br>';
					$errorFlg = true;
					break;
				}
				if ( (isset($_POST['bind_pass']) && $_POST['bind_pass'] == 1) 
					  &&
					 ( (!isset ($_POST['file_fields_0']) || $_POST['file_fields_0'] < 0)
					  ||
					 (!isset ($_POST['file_fields_pers_uid']) || $_POST['file_fields_pers_uid'] < 0) )	
				   )
				{
				    $errorMsg = 'Выбрана опция назначения пропусков сотрудникам, но одно из полей UID не указано.<br>';
					$errorFlg = true;
					break;	
				}
				else
				{
					$bindPass = true;
				}
				//тип идентификатора сотрудника
				$idTypeValue = (isset($_POST['pers_id_type']) && $_POST['pers_id_type'] == 2) ? '[externId]' : '[ID]';
				
				echo '<table class="settingsTable" cellpadding="0" cellsapcing="0">';
				echo '<tr><th>№ </th><th>Действие</th></th>';
				$file_path = $_SESSION['impVariables']['catalog'].'\\'.$_SESSION['impVariables']['fileCatalogName'].'\\'.$_SESSION['impVariables']['sourceFileName']; 
				$file = @file($file_path);
				if(!$file )
				{
					$errorMsg = 'Файл импорта не найден.<br>';
					$errorFlg = true;
					break;	
				}
				//импортируем пропуска
				// - очищаем таблицу пропусков
				$q = 'select sys_write_log(0,\'importwizard/master.php\',\'\',\'Начинаем импорт пропусков'.'\',500,0)';
                                pg_query($q);
                                $clear_q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
				
                                $clear_q = 'DELETE FROM BASE_CODES';
				if ( pg_query($clear_q) )
				{
					echo '<tr><td colspan="2">Таблица пропусков очищена.</td></tr>';
				} 
				else
				{
					$errorMsg = 'Не удалось очистить таблицу пропусков.<br>';
					$errorFlg = true;
					break;	
				}
				//определяем позиции полей
				$id_pos = ($_POST['file_fields_0'] < 0) ? null :  $_POST['file_fields_0'];
				$code_pos = $_POST['file_fields_1'];
				$status_code_pos = ( $_POST['file_fields_2'] < 0) ? null : $_POST['file_fields_2'];
				$uid_pers_pos = ( $_POST['file_fields_pers_uid'] < 0) ? null : $_POST['file_fields_pers_uid'];
				
				$success_query_count = 0;
				$failed_query_count = 0;
				
				$start_pos = ($_SESSION['impVariables']['firstRowIsName'] == 1) ? 1: 0;
				
				$file_size = sizeof($file);
				for ( $i = $start_pos; $i < $file_size; $i++ ) 
				{
					echo '<tr><td>'.$i.'</td>';
					
					$item = explode($_SESSION['impVariables']['colSeparator'],$file[$i]);
					
					$id = ($id_pos == null) ? $i : $item[$id_pos];
					$code = $item[$code_pos];
					$status = ($status_code_pos == null) ? '00000000' : $item[$status_code_pos]; 	
					$id_pers = ($uid_pers_pos == null) ? 'null' : $item[$uid_pers_pos]; 
					
					//убираем кавычки
					$id = trim(str_replace('"',' ',$id));
					$code = trim(str_replace('"',' ',$code));
					$status = trim(str_replace('"',' ',$status));
					$id_pers = trim(str_replace('"',' ',$id_pers));
					
					$q = "INSERT INTO BASE_CODES (ID,CODE,STATUS) ";
					$q .= "VALUES (".$id.",\'".$code."\',\'".$status."\')";
					
					echo '<td>';
					//echo $q;
					//вставляем пропуск
					if ( @pg_query($q) )
					{
						echo 'Пропуск добавлен.';
						$success_query_count ++ ;
						
						if ( $bindPass && $id_pers != null)
						{
							//определяем какой id использовать, внешний или внутренний
							
							$q = 'SELECT * FROM BASE_PERSONAL WHERE '.$idTypeValue.'='.$id_pers;
							//echo $q.'<br>'; 
							//ищем сотрудника
							if ($res = @pg_query ($q))
							{
								if( @pg_num_rows ($res) > 0)
								{
									$r =@pg_fetch_array($res);
									echo '<br>Сотрудник найден: '.$r['family'].' '.$r['name'].' '.$r['secname'].' - '.$r['position'] ;
									//назначаем пропуск сотруднику.
									$q = "UPDATE BASE_PERSONAL SET ID_CODES=".$id." WHERE ".$idTypeValue."=".$id;
									//echo $q;
									if ( @pg_query($q) )
									{
										echo '<br>Пропуск '.$code.' назначен сотруднику.';
									}
									else
									{
										echo '<br>Не удалось назначить пропуск.';
										echo '<br>Ошибка SQL: '.  pg_last_error();
									}
								}
								else
								{
									echo '<br>Сотрудник не найден. Пропуск не назначен.';
								}
							}
							else
							{
								echo 'Не удалось получить данные о сотруднике';
								echo '<br>Ошибка SQL: '.pg_last_error();
							}//данные о сотруднике
							//if ( )
							//$q = 'UPDATE BASE_PERSONAL SET []'
						}
						
					}
					else
					{
						echo 'Не удалось вставить пропуск.';
						echo '<br>Ошибка SQL: '.pg_last_error();
						$failed_query_count ++;
					}//вставка пропуска
					
					echo '</td></tr>';	
				}
				echo '<tr><th colspan="2">Строк в файле: '.$file_size.'<br>Импортировано пропусков: '.$success_query_count.'<br>Ошибок импортирования пропусков: '.$failed_query_count.' </th></tr>';
				echo '</table>';
		break;
		//////////////////////КОНЕЦ ОБРАБОТКИ ИМПОРТА ПРОПУСКОВ///////////////////////////////////////////// 
		default:break;
	}
	
	if ($errorFlg)
	{
		$buttons = '[ <a href="javascript:onclick=window.close()" class="bigBtLink">отмена</a> ] &nbsp; [ <a 	href="master.php?action=import_settings" class="bigBtLink">назад</a> ]';
		$BODY .= showError($errorMsg,$buttons);
	}
 
}


echo $BODY; 
$page->end();
?>