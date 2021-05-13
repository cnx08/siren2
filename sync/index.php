<?php
include("../include/input.php");
require_once("../classes/base/containers.h");
require_once("../include/common.php");
require_once("classes/mssql_objects.h");
require_once('../include/hua.php');
if(CheckAccessToModul(34,$_SESSION['modulaccess'])==false)
{
    echo '<center><span class="text">Выполнение не возможно.<br> Нет прав доступа</span><br>';
    echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
    exit();
}

$_INCLUDES = array();
$_INCLUDES[0] = '<link rel="stylesheet" type="text/css" href="../include/menu.css">';
$_INCLUDES[1] = '<link rel="stylesheet" type="text/css" href="css/tabpanel.css">';
$_INCLUDES[2] = '<link rel="stylesheet" type="text/css" href="css/base.css">';
$_INCLUDES[3] = '<link rel="stylesheet" type="text/css" href="js/calendar/calendar-mos.css">';
$_INCLUDES[4] = '<script language="JavaScript" src="js/calendar/calendar.js"></script>';
$_INCLUDES[5] = '<script language="JavaScript" src="js/calendar/lang/calendar-ru.js"></script>';
$_INCLUDES[6] = '<script language="JavaScript" src="js/functions.js"></script>';
$_INCLUDES[7] = '<script language="JavaScript" src="../gscripts/core.js"></script>';


echo PrintHeadI('СКУД','Синхронизация с кадровой системой ',$_INCLUDES);

//обработка действий
$action = null;
$errorFlag = false;
$errorText = '';
$activeTab = 0;
if ( isset($_REQUEST['act']) &&  $_REQUEST['act'] != '' ) $action = $_REQUEST['act'];

if ($action=='exportWt')
{
     if ( !@pg_query('select kadr_exp_narab(\''.$_REQUEST['date'].'\')') )
	{
		$errorFlag = true;
		$errorText .= 'Ошибка при выполнении &quot;Синхронизации&quot;. '.pg_last_error().'<br>';  
	}
	$activeTab = 2; 
}
if ($action=='import')
{
    if ( !@pg_query('select kadr_imp_sp_main_load()') )
	{
		$errorFlag = true;
		$errorText .= 'Ошибка при выполнении &quot;Синхронизации&quot;. '.pg_last_error().'<br>';  
	}
	$activeTab = 2;
}


if ( $action == 'save' )
{
		//print_r($_POST);
		$validParams = array();
		if ( !isset($_POST['personalFilePath']) || $_POST['personalFilePath'] == '')
		{	
			$errorText .= 'Не указан параметр  &quot;Путь к файлу сотрудников&quot;<br>';
			$errorFlag = true;
		}
		else
		{
			$validParams['personalFilePath']['alias'] = 'Путь к файлу сотрудников';
			$validParams['personalFilePath']['value'] = $_POST['personalFilePath'];
		}
		////////////////////////////////////////////////////////////////////		
		if ( !isset($_POST['departmentsFilePath']) || $_POST['departmentsFilePath'] == '')
		{
			$errorText .= 'Не указан параметр &quot;Путь к файлу подразделений&quot;<br>';
			$errorFlag = true;
		}
		else
		{
			$validParams['departmentsFilePath']['alias'] = 'Путь к файлу подразделений';
			$validParams['departmentsFilePath']['value'] = $_POST['departmentsFilePath'];
			
		}
                ////////////////////////////////////////////////////////////////////		
		if ( !isset($_POST['departmentsFilePath']) || $_POST['departmentsFilePath'] == '')
		{
			$errorText .= 'Не указан параметр &quot;Путь к файлу подразделений&quot;<br>';
			$errorFlag = true;
		}
		else
		{
			$validParams['departmentsFilePath']['alias'] = 'Путь к файлу подразделений';
			$validParams['departmentsFilePath']['value'] = $_POST['departmentsFilePath'];
			
		}
                if ( !isset($_POST['otpuskFilePath']) || $_POST['otpuskFilePath'] == '')
		{
			$errorText .= 'Не указан параметр &quot;Путь к файлу отпусков&quot;<br>';
			$errorFlag = true;
		}
		else
		{
			$validParams['otpuskFilePath']['alias'] = 'Путь к файлу отпусков';
			$validParams['otpuskFilePath']['value'] = $_POST['otpuskFilePath'];
			
		}
		////////////////////////////////////////////////////////////////////
		if (isset($_POST['file_type'])) 
		{
			$validParams['file_type']['alias'] = 'Тип файла, csv or not';
			$validParams['file_type']['value'] = 1;
		}
		else
		{
			$validParams['file_type']['alias'] = 'Тип файла, csv or not';
			$validParams['file_type']['value'] = 0;
			
		}
		////////////////////////////////////////////////////////////////////
		if ( !isset($_POST['file_encoding']) || $_POST['file_encoding'] == '') 
		{
			$errorText .= 'Не указан параметр &quot;Кодировка файла&quot;<br>';
			$errorFlag = true;
		}
		else
		{
			$validParams['file_encoding']['alias'] = 'Кодировка файла';
			$validParams['file_encoding']['value'] = $_POST['file_encoding'];
		}
		//////////////////////////////////////////////////////////////////////////////	
		if ( !isset($_POST['colSeparator']) || $_POST['colSeparator'] == '') 
		{
			$errorText .= 'Не указан параметр &quot;Разделитель колонок&quot;<br>';
			$errorFlag = true;
		}
		else
		{
			$validParams['colSeparator']['alias'] = 'Разделитель колонок';
			$validParams['colSeparator']['value'] = $_POST['colSeparator'];
		
		}
		/////////////////////////////////////////////////////////////////////////////////
		
		/////////////////////////////////////////////////////////////////////////////////
		
		//////////////////////////////////////////////////////////////////////////////////
		if (isset($_POST['physicRemoveData']))
		{
			$validParams['physicRemoveData']['alias'] = 'Удаление данных';
			$validParams['physicRemoveData']['value'] = 1; 
		}
		else
		{
			$validParams['physicRemoveData']['alias'] = 'Удаление данных';
			$validParams['physicRemoveData']['value'] = 0; 
		}
		//////////////////////////////////////////////////////////////////////////////////
			
		if (isset($_POST['clearBeforeImport']))
		{
			$validParams['clearBeforeImport']['alias'] = 'Удаление данных';
			$validParams['clearBeforeImport']['value'] = 1; 
		}
		else
		{
			$validParams['clearBeforeImport']['alias'] = 'Удаление данных';
			$validParams['clearBeforeImport']['value'] = 0; 
		}
                /////Export///////////////////////////		
		if ( !isset($_POST['exportPath']) || $_POST['exportPath'] == '')
		{
			$errorText .= 'Не указан параметр &quot;Путь к папке экспорта&quot;<br>';
			$errorFlag = true;
		}
		else
		{
			$validParams['exportPath']['alias'] = 'Путь к папке экспорта';
			$validParams['exportPath']['value'] = $_POST['exportPath'];
			
		}
		//сохраняем настройки
		foreach ( $validParams as $key=>$value )
		{
                        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
			$q .= 'select kadr_imp_sp_update_setting(\''.$key.'\',\''.$value['value'].'\')';
			if ( !@pg_query($q) )
			{
				$errorFlag = true;
				$errorText .= 'Ошибка при сохранении &quot;'.$value['alias'].'&quot;. '.  pg_last_error().'<br>';  
			}
		}
		
		$activeTab = 3;
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////
//вывод интерфейса
$jobsNames = array();
$jobsNames[0] = 'KadrSync';


$tabHelp  = '';
$tabState = '';
$tabSettings = '';
$tabLogs = '';

//Закладка справки
$tabHelp .= '<table class="settingTable" cellpadding="0" cellspacing="0">';
$content = @file('help.txt');
$content_size = sizeof($content);
for ( $i = 0; $i < $content_size; $i++ )
{
	$tabHelp .= $content[$i];
}
$tabHelp .= '</table>';

//////////////////////////////////////////////////////////////////////////////////
//Вкладка Настройки
$tabSettings .='<form name="syncSettings" action="index.php" method="POST">';
$tabSettings .= '<table class="settingTable">';
$tabSettings .= '<tr>';
$tabSettings .= '<th width="20%">Название</th>';
$tabSettings .= '<th width="30%">Значение</th>';
$tabSettings .= '<th>Описание</th>';
$tabSettings .= '</tr>';

if ( !$res = @pg_query('SELECT * FROM kadr_imp_fn_get_all_settings()' ) )
{
	    $tabSettings .= '<tr><td colspan="7" style="color:red">Ошибка:Не удалось получить информацию об агенте.<br>';
		$tabSettings .= 'Ошибка SQL :'.pg_last_error().'</td></tr> ';
}
else
{

    while ( $r = pg_fetch_array($res))
    {	
        $tabSettings .= '<tr>';
        $tabSettings .= '<td style="text-align:left">'.$r['namealias'].'</td>';
        $tabSettings .= '<td style="text-align:left">';


        if ($r['type'] == 'string')
        {
                $tabSettings .= '<input size = "40" type="text" class="textField" name="'.$r['name'].'", value="'.$r['value'].'">'; 
        }
        /////////////////////////////////////////////////////////////////////////////
        if ($r['type'] == 'bool')
        {
                $checked = ($r['value'] == 1) ? 'checked' : '';
                $tabSettings .= '<input type="checkbox" name="'.$r['name'].'" '.$checked.'>';
        }
        $tabSettings .= '</td>';	
        $tabSettings .= '<td style="text-align:left">'.$r['comment'].'</td>';
        $tabSettings .= '</tr>';
		
    }	

	
}
//$tabSettings .= '<input type="hidden" name="activePage" value="'.$tabPanel->getOption('activeSheet').'">';
$tabSettings .= '<input type="hidden" name="act" value="save">';
$tabSettings .= '<tr><th style="text-align:right" colspan="3"><input type="submit" class="button" name="saveBt" value="сохранить"></th></tr>';
$tabSettings .= '</table>';
$tabSettings .= '</form>';

///////////////////////////////////////////////////////////////////////////////////////////////////////
//Вкладка Логи
$tabLogs .= '<form name="logsForm" action="index.php" method="GET">';
$tabLogs .= '<table class="settingTable">';
$tabLogs .= '<tr><th style="text-align:left">Просмотр логов</th></tr>';
$tabLogs .= '<tr>';
$tabLogs .= '<td style="text-align:left">';
$tabLogs .= 'с <input type="text" class="textField" value="'.date('d.m.Y').'" id="logStartDate" name="logStartDate" readonly> <input type="button" value=" ... " class="button" onclick=showCalendar("logStartDate")>';
$tabLogs .= '&nbsp;&nbsp; по &nbsp;<input type="text" class="textField" value="'.date('d.m.Y').'" id="logEndDate" readonly> <input type="button" value=" ... " class="button" onclick=showCalendar("logEndDate")>';
$tabLogs .= '</td>';
$tabLogs .= '</tr>';
$tabLogs .= '<tr><th style="text-align:right" ><input type="button" class="button" name="saveBt" value="просмотреть" onclick="showLogs()"></th></tr>';
$tabLogs .= '</table>';
$tabLogs .= '</form>';

////////////////////////////////////////////////////////////////////////////////////////////////////////
//Управление
$tabManagment .= '<form name="ManagForm" action="index.php" method="GET">';
$tabManagment .=    '<table align="center">';
$tabManagment .=           '<tr>';
$tabManagment .=               '<td >';
$tabManagment .=                    '<div id="exp" name="exp" class="LabelBt64" ';
$tabManagment .=                        ' onmouseout="Core.changeLabelButtonImage(this,\'../images/i_dataExportBt64.gif\')" ';
$tabManagment .=                        ' onmouseover="Core.changeLabelButtonImage(this,\'../images/a_dataExportBt64.gif\')" ';
$tabManagment .=                        ' onclick="ExportWt(document.ManagForm)">';
$tabManagment .=                        '<img class="LabelBtImg64" src="../images/i_dataExportBt64.gif">';
$tabManagment .=                        '<div>Выгрузка рабочего времени</div>';
$tabManagment .=                    '</div>';
$tabManagment .=                    '<div id="sdfsd"  name="sdfsd" class="LabelBt64" ';
$tabManagment .=                        ' onmouseout="Core.changeLabelButtonImage(this,\'../images/i_dataImportBt64.gif\')" ';
$tabManagment .=                        ' onmouseover="Core.changeLabelButtonImage(this,\'../images/a_dataImportBt64.gif\')" ';
$tabManagment .=                        ' onclick="document.location.href=\'index.php?act=import\'">';
$tabManagment .=                        '<img class="LabelBtImg64" src="../images/i_dataImportBt64.gif">';
$tabManagment .=                        '<div>Синхронизация кадров</div>';
$tabManagment .=                    '</div>';
$tabManagment .=                '</td>';
$tabManagment .=            '</tr>';
$tabManagment .=             '<tr>';
$tabManagment .=                 '<td>';
$tabManagment .=                        '<input type="text" size=25 class="textField" value="'.date('d.m.Y').'" id="ExpDate" name="ExpDate" readonly> ';
$tabManagment .=                        '<input type="button" value=" ... " class="button" onclick=showCalendar("ExpDate")>';
$tabManagment .=                 '</td>';
$tabManagment .=            '</tr>';
$tabManagment .= '      </table>';
$tabManagment .= '</form>';




$BODY = '<br>';

$BODY .= '<table border="0"  cellpadding="0" cellspacing="0" width="100%">';
$BODY .= '<tr>';
$BODY .= '<tr>';
$BODY .= '<td>';


///////////////////////////////////////////////////////////////////////////////////////////////////////

//вывод
$tabPanel = new СTabPanel('basePanel');
$tabPanel->setStyle('container','tabPanel');
$tabPanel->setStyle('header','tabSheetHeader');
$tabPanel->setStyle('activeHeader','activeTabSheet');
$tabPanel->setStyle('sheet','sheet');
$tabPanel->setStyle('activeSheet','activeSheet');
$tabPanel->setStyle('headerMouseOver','tabSheetMouseOver');
$tabPanel->setStyle('headerMouseOut','tabSheetMouseOut');
$tabPanel->setOption('clietHeight','500');
$tabPanel->setOption('activeSheet',$activeTab);
$tabPanel->addSheet('Справка',$tabHelp);
$tabPanel->addSheet('Управление',$tabManagment);
//$tabPanel->addSheet('Состояние',$tabState);
$tabPanel->addSheet('Логи',$tabLogs);
$tabPanel->addSheet('Настройки',$tabSettings);

$BODY .= $tabPanel->render();



$BODY .= '</td>';
$BODY .= '</tr>';
if ( $errorFlag == true )
{

	$BODY .= '<tr>';
	$BODY .= '<td style="font-weight:bold;color:red;font-family:tahoma;font-size:11px;padding:5px;" >';
	$BODY .= 'Ошибка:<br>  '. $errorText . '<br> Сохранение отменено.'; 
	$BODY .= '</td>';
	$BODY .= '</tr>';
}	

$BODY .= '</table>';

//////////////////////////////////////////////////////////////////////////////////////////////////////
//выводим ошибки


echo $BODY;

echo PrintFooterI();

?>