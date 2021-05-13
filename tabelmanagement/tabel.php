<?php
include ("../include/input.php");

$IDMODUL=23;
$ROOTPATHSTR = '../';

function InsertCodeSelect($sel,$name)
{
    $selected = '';
    $res='';
    $res.='<select name='.$name.' class="tabinput" style="width:100%"><option value="0">-</option>';
    $q=('select * from TABL_W_S_SING');
    $result=pg_query($q);
    while($r=pg_fetch_array($result))
    {
      if($sel == $r['id']) $selected = 'selected'; else $selected = '';
       $res.='<option value="'.$r['id'].'" '.$selected.' title="'.$r['description'].'">'.trim($r['name']).'</option>';
    }
    $res.='</select>';
   return $res;
}

$INCLUDES = array();
$INCLUDES[0] = '<link rel="stylesheet" type="text/css" href="styles/edittab.css">';
$INCLUDES[1] = '<script type="text/javascript" src="include/controllers.js"></script>';
$INCLUDES[8] = '<script type="text/javascript" src="scripts/edittab.js"></script>';
$INCLUDES[3] = '<link rel="stylesheet" type="text/css" href="styles/menu.css">';
$INCLUDES[4] = '<script type="text/javascript" src="include/_library_elements.js"></script>';
$INCLUDES[5] = '<script type="text/javascript" src="include/_request_functions.js"></script>';

$INCLUDES[6] = '<link rel="stylesheet" type="text/css" href="../styles/menu.css">';

$INCLUDES[7] = '<link rel="stylesheet" type="text/css" href="../gstyles/pickmeup.css">';
$INCLUDES[2] = '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$INCLUDES[9] = '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';


require_once("include/common_t.php");


$BODY ='';
echo(PrintHead('ТУ','Редактирование табеля',$INCLUDES));

require("../include/menu.php");

if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   echo '<center><input type="button" value="на главную" class="sbutton" onclick=\'document.location.href="../main.php"\'></center>';
   exit();
}



$FILTER = '';
$FILTER .= '<form id="filtrfrm" name="filtrfrm" action="" method="POST" target="_blank">';
$FILTER .= '<table border="0" cellpadding="2" cellspacing="2" align="center" width="99%" height="98%" style="border:1px solid silver">';
$FILTER .= '<tr height="5%">
             <td colspan="2" align="center" bgcolor="silver"><span class="text"><b>Фильтр</b></span></td>
            </tr>';
$FILTER .= '<tr>';
$FILTER .= '<td width=200px><span class="text">За Месяц</span></td>';
$FILTER .= '<td><span class="text">'.InsertMonthYearSelect(1,1,2000,2020,'sel_date','sel_date','class=select').'</span></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Таб. номер</span></td>';
$FILTER .= '<td><input type="text" class="tabinput" name="tab_num" value="" size="25"></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Фамилия</span></td>';
$FILTER .= '<td><input type="text" class="tabinput" name="family" value="" size="25"></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Имя</span></td>';
$FILTER .= '<td><input type="text" class="tabinput" name="name" value="" size="25"></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Отчество</span></td>';
$FILTER .= '<td><input type="text" class="tabinput" name="secname" value="" size="25"></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Должность</span></td>';
$FILTER .= '<td><input type="text" class="tabinput" name="position" value="" size="25"></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Отдел</span></td>';
$FILTER .= '<td>';
$browseDeptUrl = '../objectviewer.php?object=departments_st&amp;elIdDept=filtrfrm.depart&amp;elDeptName=filtrfrm.deptName';

$FILTER .= '<input type="text" name="deptName" id="deptName" value="" size="25" class="input">&nbsp;
            <input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseDeptUrl.'\',\'\',\'width=420,height=480\')">&nbsp;
            <input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'deptName\');filtrfrm.depart.value=0">
            <input type="hidden" value="0" class="input" name="depart" id="depart" size="25">';
$FILTER .= '</td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">График</span></td>';
$FILTER .= '<td>';
$FILTER .= InsertSelect('select * from BASE_W_S_GRAPH_NAME(NULL)','id',0,'','graph','style="width:167px" class="select"','все графики');
$FILTER .= '</td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .='<td colspan="2" align="left">
           <input name="sbutton" type="button" value="найти" class="sbutton" onClick = \'onFilter(this,document.filtrfrm)\' />
           <input type="button" value="очистить" class="sbutton" onClick = \'ClearFilter(document.filtrfrm)\' />
           </td>';
$FILTER .= '</tr>';
//$FILTER .= '</table>';
//$FILTER .= '<table border="1" cellpadding="2" cellspacing="2" align="center" width="100%">';
$FILTER .= '<tr>';
$FILTER .= '<td colspan="2" align="center" bgcolor="silver"><span class="text"><b>Пересчёт отработанного времени сотрудников</b></span></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td colspan="2"><span class="text"><input name="tcradio" type="radio" checked value="1"/>Для выделенных.<input name="tcradio" type="radio" value="2"/>Для всех в списке.&nbsp;<input type="checkbox" name="correct_flag">Удалять корректировки табеля.</span></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Расчитать по графику: </span></td>';
$FILTER .= '<td>';
$FILTER .= InsertSelect('select * from BASE_W_S_GRAPH_NAME(NULL)','id',0,'','graph_recalc','style="width:185px" class="select"','не менять график');
$FILTER .= '</td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Смещение</span></td>';
$FILTER .= '<td><input type="text" class="tabinput" name="graph_offset" value="0" size="3"></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td colspan="2"><span class="text">';
$FILTER .= 'c:&nbsp;<input type="text" id="tc_st_date" name="tc_st_date" class="tabinput" size="10" value="" readonly>';

$FILTER .= '&nbsp;по:&nbsp;<input type="text" id ="tc_en_date" name="tc_en_date" class="tabinput" size="10" readonly>';

$FILTER .= '&nbsp;<input type="button" class="sbutton" value="пересчитать" onclick=\'toCountTime(document.filtrfrm)\' />';
$FILTER .= '</span></td>';
$FILTER .= '</tr>';
$FILTER .= '</table>';
$FILTER .= '</form>';


$PERS_LIST = '';

$PERS_LIST .= '<table border="0" cellpadding="2" cellspacing="2" align="center" width="99%" height="98%" style="border:1px solid silver">';
$PERS_LIST .= '<tr height="5%">
               <td align="center" bgcolor="silver"><span class="text"><b>Список сотрудников</b></span></td>';
$PERS_LIST .='</tr>';
$PERS_LIST .= '<tr>';
$PERS_LIST .= '<td>';
$PERS_LIST .= '<div id="list" class="body"></div>';
$PERS_LIST .= '</td>';
$PERS_LIST .= '</tr>';
$PERS_LIST .= '</table>';

$TABEL ='';

$TABEL.='<form id="person" name="person" action="" method="POST">';
$TABEL.='<table border="0" width="100%" class="tabtable" cellpadding="0" cellspacing="0">';
	$TABEL.='<tr>';
        $TABEL.='<td class="tabheadtext" valign="bottom" >Отметки о явках и неявках по числам месяца<br>';
               $TABEL.='<table border="0"  width="100%" cellpadding="0" cellspacing="0" class="tab">';
               $TABEL.='<tr>';
                      for($i=1;$i<=15;$i++)$TABEL.='<td width="6%" align="center" class="cell tabheadtext"><span class="textsmall">'.$i.'</span></td>';
                      $TABEL.='<td width="6%" align="center" class="cell tabheadtext"><span class="tabheadtext">Х</span></td>';
               $TABEL.='</tr>';
               $TABEL.='<tr>';
                      for($i=16;$i<=31;$i++)$TABEL.='<td align="center" class="cell tabheadtext"><span class="textsmall">'.$i.'</span></td>';
               $TABEL.='</tr>';
               $TABEL.='</table>';
        $TABEL.='</td>';
        $TABEL.='<td>';
               $TABEL.='<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" >';
               $TABEL.='<tr>';
                        $TABEL.='<td class="tabheadtext downborder leftborder rightborder" colspan="2" align="center">Отработано<br>за</td>';
               $TABEL.='</tr>';
               $TABEL.='<tr>';
                       $TABEL.='<td class="tabheadtext leftborder rightborder" width="50%" align="center">Половину<br>месяца</td>';
                       $TABEL.='<td class="tabheadtext rightborder" width="50%" align="center">месяц</td>';
               $TABEL.='</tr>';
               $TABEL.='<tr>';
                       $TABEL.='<td class="tabheadtext leftborder rightborder downborder topborder" colspan="2" align="center">дни</td>';
               $TABEL.='</tr>';
               $TABEL.='<tr>';
                       $TABEL.='<td class="tabheadtext leftborder rightborder downborder topborder" colspan="2" align="center">часы</td>';
               $TABEL.='</tr>';

               $TABEL.='</table>';
	$TABEL.='</tr>';



	$TABEL.='<tr>';
		$TABEL.='<td>';
			$TABEL.='<table border="0" width="100%" cellpadding="0" cellsapcing="0" class="tab" style="border:none">';
			   $TABEL.='<input name="pid" type="hidden" class="tabinput"  value="">';
               $TABEL.='<tr>';
                      for($i=1;$i<=15;$i++)$TABEL.='<td width="6%" align="center" style="border:none">'.InsertCodeSelect(0,"cod_$i").'</td>';
                      $TABEL.='<td width="6%" align="center" style="border:none"><span class="textsmall" >Х</span></td>';
               $TABEL.='</tr>';
               $TABEL.='<tr>';
                      for($i=1;$i<=15;$i++)
                      {
                         $TABEL.='<td align="center" style="border:none"><input id="day_'.$i.'" name="day_'.$i.'" type="text" value="0" size="4" class="tabinputedit" maxlength="5" onFocus=onEditing(this) onBlur=onCancelEditing(this)></td>';
                      //echo $_time[$i].'<br>';
                      }
                      $TABEL.='<td width="6%" align="center" style="border:none"><span class="textsmall">Х</span></td>';
               $TABEL.='</tr>';
               $TABEL.='<tr>';
                      for($i=16;$i<=31;$i++)$TABEL.='<td width="6%" align="center" style="border:none">'.InsertCodeSelect(0,"cod_$i").'</td>';
               $TABEL.='</tr>';
               $TABEL.='<tr>';
                      for($i=16;$i<=31;$i++)$TABEL.='<td align="center" style="border:none"><input id="day_'.$i.'" name="day_'.$i.'" type="text" value="0" size="4" class="tabinputedit" maxlength="5"  onFocus=onEditing(this) onBlur=onCancelEditing(this)></td>';
               $TABEL.='</tr>';
			$TABEL.='</table>';
		$TABEL.='</td>';
	   
		$TABEL.='<td valign="top" align="center">
				   <table  width="100%" height="100%">
						<tr>
						   <td ><input name="c_d_f_h_" type="text" value="0" size="7" class="tabinput"   readonly></td>
						   <td  rowspan="2" valign="middle" ><input name="c_d_t_" type="text" value="0" size="7" class="input" style="border:none" readonly></td>
						</tr>
						<tr>
							<td ><input name="t_d_f_h_" type="text" value="0" size="7" class="tabinput"  readonly></td>
						</tr>
						<tr>
							<td ><input name="c_d_s_h_" type="text" value="0" size="7" class="tabinput"   readonly></td>
							<td rowspan="2" valign="middle"><input name="t_t_" type="text" value="0" size="7" class="input"  style="border:none" readonly></td>
					   </tr>
					   <tr>
							<td ><input name="t_d_s_h_" type="text" value="0" size="7" class="tabinput"   readonly></td>
					   </tr>
				   </table>
               </td>';
	$TABEL.='</tr>';

	$TABEL.='<tr>';
		$TABEL.='<td colspan="8" valign="middle" style="border-top:1px solid silver"><div style="float:left;border:1px solid black;width:1%;background-color:#21f466">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><span class="text"> - была введена корректировка.</span></td>';
	$TABEL.='</tr>';
//	$TABEL.='<tr>';
//		$TABEL.='<td><input class="sbutton" type="button" value="Статус пересчёта" onclick ="loadXMLText();"></td>';
//	$TABEL.='</tr>';
$TABEL.='</table>';
$TABEL.='</form>';


$BODY .= '<div id="div_tabdata" style="margin: 0pt; padding: 0pt; left: 0pt; position: absolute; width: 100%;">';
$BODY.='<table border="0" cellpadding="0" cellspacing="0" width="100%" height="90%">

    <tr>
      <td width="50%" >'.$FILTER.'</td>
      <td width="50%"  valign="top" align="right">'.$PERS_LIST.'</td>
    </tr>
    <tr>
      <td width="100%" height="200" colspan="2" valign="top">'.$TABEL.'</td>
    </tr>
    <tr bgcolor="silver">
      <td height="25"  width="25%"><div id="status_bar" style="width:100%"></div></td>
      <td height="25">
      <div id="buttons_bar" style="width:100%;text-align:right;padding-right:10px;float:left;">
	  <img src="images/save.gif" class="panelbuttons" onmouseover=sbonmouseover(this) onmouseout=sbonmouseout(this) onclick=\'SaveTabelChange()\' /></div>
      </td>
    </tr>
  </table>';
// 22.12.2010   
$BODY .= '</div>';	// div с id = 'div_tabdata'

echo $BODY;
echo(PrintFooter());

?>