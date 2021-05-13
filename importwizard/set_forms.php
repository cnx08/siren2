<?php
if ( !isset($_SESSION['impVariables']))
{
	$BODY .= showError('Не доступны конфигурационные переменные','');
	exit();
}
//print_r($_SESSION['impVariables']['fieldsNames']);

$pass_necessary_fields = array();
$pass_necessary_fields[0] = 'UID';
$pass_necessary_fields[1] = 'Код пропуска';
$pass_necessary_fields[2] = 'Статус пропуска';


$dept_necessary_fields[0] = 'UID';
$dept_necessary_fields[1] = 'Название';

$pers_necessary_fields[0] = 'UID';
$pers_necessary_fields[1] = 'Таб.номер';
$pers_necessary_fields[2] = 'Фамилия';
$pers_necessary_fields[3] = 'Имя';
$pers_necessary_fields[4] = 'Отчество';
$pers_necessary_fields[5] = 'Должность';
$pers_necessary_fields[6] = 'UID подразделения';
$pers_necessary_fields[7] = 'Имя фотографии';


$BODY .= '<div id="pass_set_form" class="formsContainer">';

$BODY .= '<table border="0" class="forms" cellpadding="0" cellspacing="0">';
$BODY .= '<tr>';
$BODY .= '<td colspan="2">Для импорта пропусков необходимо указать следующие поля:
			<ol>
				<li>UID (Уникальный идентификатор) пропуска - если не указн то будет принято значение по умолчанию. </li>
				<li>Код пропуска - поле обязательное и не может быть пустым.</li>
				<li>Строка со статусом пропуска - если не указано то будет принято значение по умолчанию.</li>
			</ol>
			</td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td colspan="2" style="border-top:1px solid #b7cee4"><b>Укажите необходимые поля:</b></td>';
$BODY .= '</tr>';
for ( $i = 0; $i < sizeof($pass_necessary_fields); $i++ )
{
	$BODY .= '<tr>';
	$BODY .= '<td>'.$pass_necessary_fields[$i].'</td>';
	$BODY .= '<td>';
	$BODY .= '<select name="file_fields_'.$i.'" class="input"><option value="-1">нет</option>';
	
	for ( $j = 0; $j < sizeof($_SESSION['impVariables']['fieldsNames']); $j++ )
		$BODY .= '<option value="'.$j.'">'.$_SESSION['impVariables']['fieldsNames'][$j].'</option>';
	
	$BODY .= '</select>';	
	$BODY .= '</td>';
	$BODY .= '</tr>';
}
$BODY .= '<tr>';
$BODY .= '<td colspan="2" style="border-top:1px solid #b7cee4"><b>Привязка пропусков:</b></td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td colspan="2" >
			При импорте пропусков можно одновременно назначить пропуска сотрудникам. 
		    Для этого необходимо указать поле файла которое является UID пропуска, а так же поле которое содержит
			UID сотрудника.<br> Для назначения пропусков сотрудникам, установите переключатель в &quotНазначить&quot и 
			укажите поля: UID пропуска, UID сотрудника.
		  </td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td colspan="2" align="center">';
$BODY .= '<input type="radio" name="bind_pass" value="1">Назначить';
$BODY .= '&nbsp;&nbsp;&nbsp;<input type="radio" name="bind_pass" value="2" checked>Не назначать';
$BODY .= '</td>';
$BODY .= '</tr>';		  
$BODY .= '<tr>';
$BODY .= '<td>UID сотрудника файле</td>';
$BODY .= '<td>';
$BODY .= '<select name="file_fields_pers_uid" class="input"><option value="-1">нет</option>';
	
	for ( $j = 0; $j < sizeof($_SESSION['impVariables']['fieldsNames']); $j++ )
		$BODY .= '<option value="'.$j.'">'.$_SESSION['impVariables']['fieldsNames'][$j].'</option>';
	
	$BODY .= '</select>';	
$BODY .= '</td>';
$BODY .= '</tr>';		  
$BODY .= '<tr>';
$BODY .= '<td>Тип UID сотрудника. Если установлена система синхронизации 1.0 то выберите - Внешний иначе Внутренний</td>';
$BODY .= '<td align="center">';
$BODY .= '<input type="radio" name="pers_id_type" value="1" checked>Внутренний';
$BODY .= '&nbsp;&nbsp;&nbsp;<input type="radio" name="pers_id_type" value="2" >Внешний';
$BODY .= '</td>';
$BODY .= '</tr>';		  

/*
$BODY .= '<tr>';
$BODY .= '<td colspan="2" style="border-top:1px solid #b7cee4"><b>Дополнительные параметры:</b></td>';
$BODY .= '</tr>';

$BODY .= '<tr>';
$BODY .= '<td>Вести лог</td>';
$BODY .= '<td><input type="checkbox" checked name="enable_log"></td>';
$BODY .= '</tr>';*/
$BODY .= '</table>';

$BODY .= '</div>';
/////////////////////////////////////////////////////////////////////////////////////////////
$BODY .= '<div id="personal_set_form" class="formsContainer" style="display:block">';

$BODY .= '<table border="0" class="forms" cellpadding="0" cellspacing="0">';
$BODY .= '<tr>';
$BODY .= '<td colspan="2">При импорте сотрудников все поля должны быть указаны.
		  </td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td colspan="2" style="border-top:1px solid #b7cee4"><b>Укажите необходимые поля:</b></td>';
$BODY .= '</tr>';
for ( $i = 0; $i < sizeof($pers_necessary_fields); $i++ )
{
	$BODY .= '<tr>';
	$BODY .= '<td>'.$pers_necessary_fields[$i].'</td>';
	$BODY .= '<td>';
	$BODY .= '<select name="pers_fields_'.$i.'" class="input"><option value="-1">нет</option>';
	
	for ( $j = 0; $j < sizeof($_SESSION['impVariables']['fieldsNames']); $j++ )
		$BODY .= '<option value="'.$j.'">'.$_SESSION['impVariables']['fieldsNames'][$j].'</option>';
	
	$BODY .= '</select>';	
	$BODY .= '</td>';
	$BODY .= '</tr>';
}
$BODY .= '</table>';
$BODY .= '</div>';
///////////////////////////////////////////////////////////////////////////////////////////////
$BODY .= '<div id="departments_set_form" class="formsContainer">';

$BODY .= '<table border="0" class="forms" cellpadding="0" cellspacing="0">';
$BODY .= '<tr>';
$BODY .= '<td colspan="2">Для импорта подразделений необходимо указать следующие поля:
			<ol>
				<li>UID (Уникальный идентификатор) подразделения - если не указн то будет принято значение по умолчанию. </li>
				<li>Название - поле обязательное и не может быть пустым.</li>
			</ol>
			</td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td colspan="2" style="border-top:1px solid #b7cee4"><b>Укажите необходимые поля:</b></td>';
$BODY .= '</tr>';
for ( $i = 0; $i < sizeof($dept_necessary_fields); $i++ )
{
	$BODY .= '<tr>';
	$BODY .= '<td>'.$dept_necessary_fields[$i].'</td>';
	$BODY .= '<td>';
	$BODY .= '<select name="dept_fields_'.$i.'" class="input"><option value="-1">нет</option>';
	
	for ( $j = 0; $j < sizeof($_SESSION['impVariables']['fieldsNames']); $j++ )
		$BODY .= '<option value="'.$j.'">'.$_SESSION['impVariables']['fieldsNames'][$j].'</option>';
	
	$BODY .= '</select>';	
	$BODY .= '</td>';
	$BODY .= '</tr>';
}
$BODY .= '</table>';
$BODY .= '</div>';
//////////////////////////////////////////////////////////////////////////////////////////////

//echo $BODY;

?>