<?php
include("include/input.php");
require("include/common.php");
require("include/head.php");


echo PrintHead('СКУД','Формирование отчёта');
require("include/menu.php");
$BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$BODY .= '<script type="text/javascript" src="../gscripts/reportsmenu.js"></script>';
$BODY .= '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';

$BODY .= '<div style="margin: 2px; padding: 0pt; left: 0pt; position: absolute; width:99%;" id="div_dtab">';
$BODY.='<form id="reportfrm" name="reportfrm" action="reports.php" method="POST" target="_blank">';
$BODY.='<table border="0" cellpadding="3" width="100%" cellspacing="2" class="dtab" align="center" style="vertical-align: bottom;">';
$BODY.='<tr  class="tablehead">';
$BODY.='<td colspan="1">Вид отчёта:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$BODY.='   <select id="select_order" name="rtype" class="select" onChange=SelectReport(this)>';
$BODY.=      '<option value="0" >Не выбран</option>';
        if(CheckAccessToModul(13,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="1">Отчёт об отработаном времени</option>';
        if(CheckAccessToModul(14,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="2">Отчёт по опозданиям</option>';
        if(CheckAccessToModul(15,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="3">Отчёт по приходам/уходам</option>';
        if(CheckAccessToModul(16,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="4">Отчёт по неявкам</option>';
        if(CheckAccessToModul(17,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="5">Отчёт о присутствии</option>';
        if(CheckAccessToModul(12,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="6">Ранний уход</option>';
        //if(CheckAccessToModul(39,$_SESSION['modulaccess'])==TRUE)  // $BODY.='<option value="7">Столовая</option>';
        if(CheckAccessToModul(15,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="9">Отчёт по приходам/уходам сокр.</option>';
        if(CheckAccessToModul(19,$_SESSION['modulaccess'])==TRUE) $BODY.='<option value="10">Отчёт по приходам/уходам посетителей</option>';
		
$BODY.= '</select>';

$BODY.='</td>';
$BODY.='<td>';
  $BODY.='C:';
  $BODY.='<input type="text" id = "start_date" name="start_date" value="'.date("d.m.Y").'" size="15" readonly class="input" />&nbsp;';
  //$BODY.='<input type="button" value="..." onClick=\'ShowCalendar(document.reportfrm.start_date,2000,2030,"dd.mm.yyyy")\'  class="sbutton" />&nbsp;';
  $BODY.='По:';
  $BODY.='<input type="text" name="fin_date" id = "fin_date" value="'.date("d.m.Y").'" size="15" readonly class="input" />&nbsp;';
  //$BODY.='<input type="button" value="..." onClick=\'ShowCalendar(document.reportfrm.fin_date,2000,2030,"dd.mm.yyyy")\' class="sbutton" />&nbsp;';
   
$BODY.='</td>';
$BODY.='</tr>';

$BODY.='<tr  class="tablehead">';
$BODY.='<td colspan="2">';
$BODY.= 'Реквизиты отчёта:';
$BODY.='</td>';
$BODY.='</tr>';

$BODY.='<tr>';
$BODY.='<td colspan="2">';
$BODY.='<input type="checkbox" name="excelflg"><span class="text">Вывести результаты в Excel</span><br>';
$BODY.= '<input type="checkbox" name="checkall" onclick=SelectAll(document.reportfrm)><span class="text">&nbsp;Выделить всё</span>';
$BODY.='</td>';
$BODY.='</tr>';

$BODY.='<tr>';
$BODY.='<td width="50%" valign="top">';
$BODY.= '<div id="checktab"><input id="checktab" type="checkbox" name="checktab" ><span class="text">&nbsp;Табельный номер</span><br></div>';
$BODY.= '<div id="checktowho"><input id="checktowho" type="checkbox" name="checktowho"><span class="text">&nbsp;К кому</span><br></div>';
$BODY.= '<div id="checkpos"><input id="checkpos" type="checkbox" name="checkpos"><span class="text">&nbsp;Должность</span><br></div>';
$BODY.= '<div id="checkdep"><input id="checkdep" type="checkbox" name="checkdep"><span class="text">&nbsp;Отдел</span><br></div>';
$BODY.= '<div id="checkgraph"><input id="checkgraph" type="checkbox" name="checkgraph"><span class="text">&nbsp;График</span><br></div>';
$BODY.= '<div id="checksmena"><input id="checksmena" type="checkbox" name="checksmena"><span class="text">&nbsp;Смена</span><br></div>';
$BODY.= '<div id="checkzasechka"><input id="checkzasechka" type="checkbox" name="checkzasechka"><span class="text">&nbsp;Засечки</span><br></div>';
$BODY.='</td>';
//гостевые
$BODY.='<td valign="top">';
$BODY.= '<div id="checkgpos"><input id="checkgpos" type="checkbox" name="checkgpos"><span class="text">&nbsp;Должность посетителя</span><br></div>';
$BODY.= '<div id="checkgcomm"><input id="checkgcomm" type="checkbox" name="checkgcomm"><span class="text">&nbsp;Комментарий</span><br></div>';
$BODY.= '<div id="checkpasstime"><input id="checkpasstime" type="checkbox" name="checkpasstime"><span class="text">&nbsp;Разрешённое время посещения</span><br></div>';
$BODY.= '<div id="checkdopusk"><input id="checkdopusk" type="checkbox" name="checkdopusk"><span class="text">&nbsp;Допуск</span><br></div>';
$BODY.= '<div id="checkpass"><input id="checkpass" type="checkbox" name="checkpass"><span class="text">&nbsp;Пропуск</span><br></div>';
$BODY.='</td>';
$BODY.='</tr>';

$BODY.='<tr>';
$BODY.='<td colspan="2">';
if(CheckAccessToModul(25,$_SESSION['modulaccess'])==TRUE) $BODY.= '<div id="t13"><input id="t13" type="checkbox" name="t13" onclick=SelectT13(document.reportfrm)><span id="t13text" class="text">&nbsp;Форма T13</span></div>';
$BODY.= '<div id="time_text"><span id = "time_text" class="text">Время:&nbsp;<input id="st_time" type="text" name="st_time" value="'.date("H:i:s").'" size="7" class="input" maxlength="8"></span>'
        . '<br><span class="text">Территория&nbsp;
         <select name="terr">';
         //<select name="terr"><option value="2">Внутренняя территория</option>';   
         $q = 'select * from BASE_W_S_TERRITORY(NULL)';
         $result = pg_query($q);
         while($r = pg_fetch_array($result))
         {
            if ($r['id']>2)
            {
             $BODY.='<option value="'.$r['id'].'">'.$r['name'].'</option>';
            } 
         }
$BODY.= '</select></span>';
$BODY.=  '</div>';

$BODY.='<div id="point_to_pass"">';
 $BODY.= '<span class="text">Точка прохода: </span>';
/// для множественного выбора
   $browseTurnUrl = 'objectviewer.php?object=turnlist&amp;elIdTurn=reportfrm.trlist&amp;elTurnName=reportfrm.turnName';

   $BODY .= '<input type="text" name="turnName" id="turnName" value="" size="15" class="input">&nbsp;';
   $BODY .= '<input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseTurnUrl.'\',\'\',\'width=420,height=480\')">&nbsp;';
   $BODY .= '<input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'turnName\');reportfrm.trlist.value=0">';
   $BODY .='<input type="hidden" value="0" class="input" name="trlist" id="trlist" size="25">';
$BODY.='</div>';

$BODY.='</td>';
$BODY.='</tr>';


$BODY.='<tr  class="tablehead">';
$BODY.='<td colspan="2">';
$BODY.= 'Фильтр:';
$BODY.='</td>';
$BODY.='</tr>';

$BODY.='<tr>';
$BODY.='<td colspan="2">';
$BODY.='<table id="guestfilter" border="0" width="100%" cellspacing="1" cellpadding="1" align="center" style="display:none;">';
  $BODY.='<tr class="tablehead" >';
  $BODY.='<td align=center >Фамилия посетителя</td>';
  $BODY.='<td align=center >Имя посетителя</td>';
  $BODY.='<td align=center >Отчество посетителя</td>';
  $BODY.='<td align=center >Номер пропуска</td>';
  $BODY.='</tr>';
  $BODY.='<tr  >';
  $BODY.='<td align=center ><input type="text" class="input" name="guest_family" value="" size="15"></td>';
  $BODY.='<td align=center ><input type="text" class="input" name="guest_name" value="" size="15"></td>';
  $BODY.='<td align=center ><input type="text" class="input" name="guest_secname" value="" size="15"></td>';
  $BODY.='<td align=center ><input type="text" class="input" name="guest_pass" value="" size="15"></td>';
  $BODY.='</tr>';
  $BODY.='</table>';
$BODY.='<table border="0" width="100%" cellspacing="1" cellpadding="1" align="center" >';
  $BODY.='<tr class="tablehead" >';
  $BODY.='<td align=center >Таб.№</td>';
  $BODY.='<td align=center >Фамилия</td>';
  $BODY.='<td align=center >Имя</td>';
  $BODY.='<td align=center >Отчество</td>';
  $BODY.='<td align=center >Должность</td>';
  $BODY.='<td align=center >Отдел</td>';
  $BODY.='<td align=center >График</td>';
  $BODY.='</tr>';
  $BODY.='<tr >';
  $BODY.='<td align=center ><input type="text" class="input" name="tab_num" value="" size="5"></td>';
  $BODY.='<td align=center ><input type="text" class="input" name="family" value="" size="15"></td>';
  $BODY.='<td align=center ><input type="text" class="input" name="name" value="" size="15"></td>';
  $BODY.='<td align=center ><input type="text" class="input" name="secname" value="" size="15"></td>';
  $BODY.='<td align=center ><input type="text" class="input" name="position" value="" size="15"></td>';

  
  $BODY.='<td align=center >';
   $browseDeptUrl = 'objectviewer.php?object=departments_st&amp;elIdDept=reportfrm.depart&amp;elDeptName=reportfrm.deptName';
 
   $BODY .= '<input type="text" name="deptName" id="deptName" value="" size="15" class="input">&nbsp;';
   $BODY .= '<input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseDeptUrl.'\',\'\',\'width=420,height=480\')">&nbsp;';
   
   $BODY .= '<input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'deptName\');reportfrm.depart.value=0">';
  $BODY.='<input type="hidden" value="0" class="input" name="depart" id="depart" size="25">';
  $BODY.='</td>';
  $BODY.='<td align=center ><select name="graph" class="select" style="width:185px"><option value="0">все графики</option>';
           $result=pg_query('select * from BASE_W_S_GRAPH_NAME(NULL)');
  
           while($r=pg_fetch_array($result))
           {
             $BODY.='<option value="'.$r['id'].'" title="'.$r['name'].'">'.substr($r['name'], 0, 50).'</option>';
           }
          $BODY.='</select>';
  $BODY.='</td>';  
  $BODY.='</tr>';
  $BODY.='</table>';

$BODY.='</td>';
$BODY.='</tr>';

$BODY.='<tr class="tablehead">';
$BODY.='<td colspan="2" align="right">';
$BODY.='Сортировать по:&nbsp;';

$BODY.='<select id="p_order_by" name="order_by" class="select"> ';
$BODY.='<option value="1">Дате</option>';
$BODY.='<option value="2">Табельному номеру</option>';
$BODY.='<option value="3">ФИО</option>';
$BODY.='<option value="6">Должности</option>';
$BODY.='<option value="7">Отдел</option>';
$BODY.='<option value="8">Графику</option>';
$BODY.='</select>';

$BODY.='<select id="g_order_by" name="g_order_by" class="select"> ';
$BODY.='<option value="1">Дате</option>';
$BODY.='<option value="2">Табельному номеру</option>';
$BODY.='<option value="3">ФИО</option>';
$BODY.='<option value="4">Должности</option>';
$BODY.='<option value="5">Отдел</option>';
$BODY.='<option value="6">ФИО посетителя</option>';
$BODY.='<option value="7">Время посещения</option>';
$BODY.='<option value="8">Номер пропуска</option>';
$BODY.='</select>';

$BODY.=' Порядок:&nbsp;';
$BODY.='<select name="sort_order" class="select"> ';
$BODY.='<option value="0">Прямой</option>';
$BODY.='<option value="1">Обратный</option>';

$BODY.='</select>';

$BODY.='&nbsp;';

$BODY.='<input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;';
$BODY.='<input type="button" class="sbutton" value="отмена">';
$BODY.='</td>';
$BODY.='</tr>';

$BODY.='</table>';

$BODY.='</form>';

$BODY .= '</div>';
echo $BODY;
echo PrintFooter();
?>