<?php
/*
	10.03.2011 - скрипт почти готов
        реализовано: 
                        - печать списков сотрудников, пропусков
                        - вывод в эксель списков сотрудников, пропусков
        не реализовано:
                        - избавиться от преобразования кода пропуска в строку
                        - при дате окончания действия меньшей текущей даты - выделять цветом (т.е. сравнение дат)
                                причем цветовое выделение нормально передается в эксель

*/
include("include/input.php");
include("include/common.php");

if(!isset($_REQUEST['action']))exit();

$excel = false;

$cur_date = date("d.m.Y");

if(isset($_REQUEST['excelflg']))$excel = true;




$BODY = '';
// добавить DOCTYPE, <body>
$BODY .= '<!DOCTYPE HTML PUBLIC "	//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';	
$BODY.= '<html>';
$BODY.='<head>';
$BODY.='<title>';
$BODY.= 'Печать списка';
$BODY.='</title>';
$BODY.='<meta http-equiv="Content-Language" content="ru">';
$BODY.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';


  $BODY .= '<style type="text/css">
  table.printTable
  {
	width:100%;
	border:1px solid black;
	border-collapse:collapse;
  }
  table.printTable th
  {
	font-family:Tahoma;
	font-size:12px;
	font-weight:bold;
	border:1px solid black;
	
  }
  table.printTable td
  {
	font-family:Tahoma;
	font-size:11px;
	border:1px solid black;
	text-align:center;
  }
  </style>';


$BODY .= '</head>';
$BODY .= '<body>';


if($_REQUEST['action'] == 'printpers')
{
  //fill array of headers
  $_head = array();
  $_head[] = 'Таб.Номер';
  $_head[] = 'Фамилия';
  $_head[] = 'Имя';
  $_head[] = 'Отчество';
  if(isset($_REQUEST['depart_flag']))   $_head[] = 'Отдел';
  if(isset($_REQUEST['position_flag'])) $_head[] = 'Должность';
  if(isset($_REQUEST['graph_flag']))    $_head[] = 'График';
  if(isset($_REQUEST['pass_code_flag']))$_head[] = 'Код пропуска';
  if(isset($_REQUEST['ext_flag']))$_head[] = 'Доп. поле';

  $BODY.='<table border="1" width="100%" cellpading="0" cellspacing="0"  class="printTable">';
  $BODY.='<tr>';
  for( $i = 0; $i < sizeof($_head); $i++ )
  {
  	$BODY .='<th>'.$_head[$i].'</th>';
  }
  $BODY.='</tr>';

    $r = pg_fetch_array(pg_query('select value from base_const where name = \'base_personal_ext\''));
    $ext = $r['value']; 
  
  
 $depart = 0;
 $tab_num = 0;
 if($_POST['f_id_dept'] == '')$depart = 0; else $depart = $_POST['f_id_dept'];
 if(!is_numeric($_POST['f_tab_num']))$tab_num = 'null';else $tab_num = $_POST['f_tab_num'];
 if(!is_numeric($_POST['ext_int']))$ext_int = 'null';else $ext_int = $_POST['ext_int'];
 if($_POST['ext_int']=='')$ext_text = '';else $ext_text = $_POST['ext_text'];
 $q='';
 if(isset($_REQUEST['p_pages']) && $_REQUEST['p_pages']==1)
 {
   $q='select * from BASE_W_S_PERSONAL_PAGE(null,null,'.$_POST['cols'].','.$_POST['sort'].',\''.$_POST['f_family'].'\',
		\''.$_POST['f_fname'].'\',\''.$_POST['f_secname'].'\',\''.$_POST['f_position'].'\','.$depart.','.$tab_num.',
		\''.$_POST['pass_code'].'\','.$_POST['f_iduser'].',\''.$_POST['pxflag'].'\','.$ext_int.',\''.$ext_text.'\')';

 }
 else
 {
   $q='select * from BASE_W_S_PERSONAL_PAGE('.$_POST['top1'].','.$_POST['top2'].','.$_POST['cols'].','.$_POST['sort'].',
   \''.$_POST['f_family'].'\',\''.$_POST['f_fname'].'\',\''.$_POST['f_secname'].'\',\''.$_POST['f_position'].'\','.$depart.',
   '.$tab_num.',\''.$_POST['pass_code'].'\','.$_POST['f_iduser'].',\''.$_POST['pxflag'].'\','.$ext_int.',\''.$ext_text.'\')';

 }


 $res = pg_query($q);


 while($r = pg_fetch_array($res))
 {
   $BODY.='<tr>';
   $BODY.='<td align="center">'.$r['tabel_num'].'</td>';
   $BODY.='<td align="left">'.$r['family'].'</td>';
   $BODY.='<td align="left">'.$r['name'].'</td>';
   $BODY.='<td align="left">'.$r['secname'].'</td>';
  if(isset($_REQUEST['depart_flag']))    $BODY.='<td align="left">'.$r['dept'].'</td>';
  if(isset($_REQUEST['position_flag']))    $BODY.='<td align="left">'.$r['pos'].'</td>';
  if(isset($_REQUEST['graph_flag']))    $BODY.='<td align="left">'.$r['id_graph_name'].'</td>';
  if(isset($_REQUEST['pass_code_flag'])){
      if(isset($_REQUEST['excelflg'])) {
          $BODY.='<td align="left">\''.$r['code'].'\'</td>';
      }
      else $BODY.='<td align="left">'.$r['code'].'</td>';
  }  
  if($ext == 'int') $BODY.='<td align="left">'.$r['ext_int'].'</td>';
  if($ext == 'text') $BODY.='<td align="left">'.$r['ext_text'].'</td>';

   $BODY.='</tr>';   
  
 }
 
 $BODY .= '</table>';

 

}
elseif ($_REQUEST['action']=='printcodes')
{
// печать списка пропусков 

   $_head = array();
   $_head[] = '№ п.п.';
   $_head[] = 'Код пропуска';
  
    if(isset($_POST['px_owner']))
 {
	$_head[] = 'Владелец пропуска';
 }
 
   $_head[] = 'Статус пропуска';
   $_head[] = 'Пин-код';
   $_head[] = 'Дата введения';
   $_head[] = 'Дата окончания'; 


$BODY.='<table border="1" width="100%" cellpading="0" cellspacing="0"  class="printTable">';
  $BODY.='<tr>';
  for( $i = 0; $i < sizeof($_head); $i++ )
  {
  	$BODY .='<th>'.$_head[$i].'</th>';
  }
  
  $BODY.='</tr>';

 
 $q='';   
   
   
if(($_POST['p_CODE']=='')||($_POST['p_CODE']=='null'))
{
	$pass_code = 'null';
}   
else 
{
	$pass_code = '\''.$_POST['p_CODE'].'\'';
}



// надо распарсить статус для печати
// или готовый брать из базы
 if(isset($_REQUEST['p_pages']) && $_REQUEST['p_pages']==1)
 {
 // все страницы 
	 $q='select * from BASE_W_S_CODES_PAGE(null,'.$_POST['top2'].',11,1,'.$pass_code.','.$_POST['p_ADMIN'].','.$_POST['p_APB'].', 
				'.$_POST['p_AVTO'].','.$_POST['p_GUEST'].','.$_POST['p_BLOCK'].','.$_SESSION['iduser'].',0)';

 }
 else
 {
// текущая страница - переданы начальная позиция и количество записей
   $q='select * from BASE_W_S_CODES_PAGE('.$_POST['top1'].','.$_POST['top2'].',11,1,'.$pass_code.','.$_POST['p_ADMIN'].','.$_POST['p_APB'].', 
				'.$_POST['p_AVTO'].','.$_POST['p_GUEST'].','.$_POST['p_BLOCK'].','.$_SESSION['iduser'].',0)';
 }

   
// формируем результат 


 $res = pg_query($q);

 while($r = pg_fetch_array($res))
 {
   $BODY.='<tr>';
   $BODY.='<td align="center">'.$r['recno'].'</td>'; 

   
// при выводе в эксель возникает проблема автоформатирования текстового 
// кода пропуска в число, поэтому ВРЕМЕННО поставил доп символы для формирования именно СТРОКИ   
if(isset($_REQUEST['excelflg']))
{
   $BODY.='<td align="center">\''.$r['code'].'\'</td>';
}
else 
{   
   $BODY.='<td align="center">'.$r['code'].'</td>';
}

    if(isset($_POST['px_owner']))
 {
    $BODY.='<td align="left"><span style="text-align: left;">'.$r['owner'].'</span></td>';
  }
   
   $BODY.='<td align="left">'.$r['statusdscr'].'</td>';
   $BODY.='<td align="left">'.$r['pin'].'</td>';
   $BODY.='<td align="left">'.$r['date_in_txt'].'</td>';   
   $BODY.='<td align="left"><span';
   
   $BODY .='>'.$r['date_out_txt'].'</span></td>';   
   
  

   $BODY.='</tr>';   
 }
 // добавил отсутствующий 
$BODY .= '</table>';
}

$BODY .= '</body></html>';

// ------------ перенаправление вывода
// ------------ в эксель 
// ------------ проблемы: 1) при автом. форматировании - текстовые коды пропусков преобразуются в числа
if(isset($_REQUEST['excelflg']))
{
$fname = 'report.xls';

header("Content-type: application/vnd.ms-excel, charset=utf-8");
header("Content-Disposition: attachment; filename=$fname");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");
header ("Content-Language: ru");

    echo '<meta http-equiv=Content-Type content="text/html; charset=utf-8">';
}

// ------------ 

echo $BODY;
?>