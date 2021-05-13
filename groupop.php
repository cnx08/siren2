<?php
set_time_limit(0);
$IDMODUL=41;
include("include/input.php");
require("include/common.php");
require("include/head.php");

if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   echo PrintHead('СКУД','Групповые операции с персоналом');
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}

if(!isset($_REQUEST['action']))$_REQUEST['action']='';

echo PrintHead('СКУД','Групповые операции с персоналом');

require("include/menu.php");

$liststr='';
$destlist='';
$numstr=0;//колличество записей в отфильтрованном списке
$numrec=0;//колличество записей в  списке  отобранных
if(!isset($_REQUEST['tab_num']))$_REQUEST['tab_num']='';

if(!isset($_REQUEST['fname']))$_REQUEST['fname']='';
if(!isset($_REQUEST['family']))$_REQUEST['family']='';
if(!isset($_REQUEST['secname']))$_REQUEST['secname']='';
if(!isset($_REQUEST['position']))$_REQUEST['position']='';
if(!isset($_REQUEST['dept_id']))$_REQUEST['dept_id']=0;
if(!isset($_REQUEST['graph_id']))$_REQUEST['graph_id']=0;
if(!isset($_REQUEST['zone_id']))$_REQUEST['zone_id']=0;
if(!isset($_REQUEST['selectstring']))$_REQUEST['selectstring']='';
if(!isset($_REQUEST['dselectstring']))$_REQUEST['dselectstring']='';


function AddIdInSession($arr)
{   //Добавляет элементы к массиву id сотрудников в сессию
   if(!isset($_SESSION['selpers']))
   {
      $_SESSION['selpers'][]=array();
      for($i=0;$i<sizeof($arr);$i++)
      {
        $_SESSION['selpers'][$i]=$arr[$i];
      }
   }
   else
   {
      for($i=0;$i<sizeof($arr);$i++)
      {
        array_push($_SESSION['selpers'],$arr[$i]);
      }
   }
  $_SESSION['selpers']=array_unique($_SESSION['selpers']);
  $_SESSION['selpers']=array_values($_SESSION['selpers']);


}


function ShowList($liststr)
{
  $res='';

  $res.='<table border="0" width="100%" height="40%" cellspacing="1" cellpadding="1" align="center" class="dtab">';
  $res.='<form name="filterfrm" action="groupop.php?action=filter" method="POST">';
  $res.='<tr  height="2%"><td colspan="9" align="center"><p class="text">Исходный список сотрудников</p></td></tr>';
  $res.='<tr class="tablehead" height="5%">';
  $res.='<td align=center></td>';
  $res.='<td align=center>Таб.№</td>';
  $res.='<td align=center width="11%">Фамилия</td>';
  $res.='<td align=center width="11%">Имя</td>';
  $res.='<td align=center width="11%">Отчество</td>';
  $res.='<td align=center width="11%">Должность</td>';
  $res.='<td align=center width="26%">Отдел</td>';
  $res.='<td align=center width="13%">График</td>';
  $res.='<td align=center width="11%">Рабочая зона</td>';
  $res.='</tr>';
  $res.='<tr class="tablehead" height="5%">';
  $res.='<td align=center><input id="mainflag" type="checkbox" name="selectall" onClick=CheckAll()></td>';
  $res.='<td align=center><input type="text" size="5" name="tab_num" class="input"> </td>';
  $res.='<td align=center><input type="text" size="15" name="family" class="input"> </td>';
  $res.='<td align=center><input type="text" size="15" name="fname" class="input"></td>';
  $res.='<td align=center><input type="text" size="15" name="secname" class="input"></td>';
  $res.='<td align=center><input type="text" size="15" name="position" class="input"></td>';

   //отделы

  $res.='<td align=center>';
  
 $browseDeptUrl = 'objectviewer.php?object=departments_st&elIdDept=filterfrm.dept_id&elDeptName=filterfrm.deptName';
 
 $res.= '<input type="text" name="deptName" value="" size="15" class="input">&nbsp;';
 $res.= '<input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseDeptUrl.'\',\'\',\'width=420,height=475\')">&nbsp;'; 
 $res.= '<input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'deptName\');filterfrm.dept_id.value=0">';
 $res.= '<input type="hidden" value="0" name="dept_id" size="5"></td>';

  //графики
  $res.='<td align=center><select name="graph" class="select" style="width:180px;"><option value="0">все графики</option>';
  $sel='';
 
  $result=pg_query('select * from BASE_W_S_GRAPH_NAME(NULL)');
  while($r=pg_fetch_array($result))
  {
    if($_REQUEST['graph_id']==$r['id'])$sel='selected';else $sel='';
    $res.='<option value="'.$r['id'].'" '.$sel.'>'.$r['name'].'</option>';
  }
  $res.='</select><input type="hidden" name="graph_id" size="5"></td>';

  //рабочая зона
  $res.='<td align=center><select name="zone" class="select" style="width:180px;"><option value="0">все зоны</option>';
  $result=pg_query('select * from BASE_W_S_ZONE(NULL)');
  $sel='';
  while($r=pg_fetch_array($result))
  {
    if($_REQUEST['zone_id']==$r['id'])$sel='selected';else $sel='';
    $res.='<option value="'.$r['id'].'" '.$sel.'>'.$r['name'].'</option>';
  }
  $res.='</select><input type="hidden" name="zone_id" size="5"></td>';
  $res.='</tr>';


 //КОНТЕЙНЕР ДЛЯ СПИСКА
  $res.='<tr height="90%"><td valign="top" colspan="9" >';
  $res.='<div class="listconteiner">';
  $res.='<table width="100%" border="0" cellpadding="0" cellspacing="1" class="groupTable">';
  $res.= $liststr;
  $res.='</table>';
  $res.='</div>';
  $res.='</td></tr>';
  $res.='<tr><td colspan="9"><input id="ss" type="hidden" name="selectstring" value="'.$_REQUEST['selectstring'].'"></td></tr>';
  $res.='</form>';
  $res.='</table>';
  return $res;

}
function ShowDestinationList($destlist)
{
  $res='';

  $res.='<table border="0" width="100%" height="35%" cellspacing="1" cellpadding="2" align="center" class="dtab">';
  $res.='<form name="execfrm" action="groupop.php?action=exec" method="POST">';
  $res.='<tr><td colspan="8" align="center" height="5%"><p class="text">Формируемый список сотрудников</p></td></tr>';

  $res.='<td align=center width="10%"></td>';
  $res.='<td align=center width="10%"></td>';
  //$res.='<td align=center width="10%"></td>';
  $res.='<td align=center width="30%"></td>';
  $res.='<td align=center width="20%"></td>';
  $res.='<td align=center width="10%"></td>';
  $res.='<td align=center width="10%"></td>';
  $res.='</tr>';
  //КОНТЕЙНЕР ДЛЯ СПИСКА
  $res.='<tr height="90%"><td valign="top" colspan="8" >';
  $res.='<div class="listconteiner">';
  $res.='<table width="100%" border="0" cellpadding="0" cellspacing="1" class="groupTable">';
  $res.= $destlist;
  $res.='</table>';
  $res.='</div>';
  $res.='</td></tr>';
  $res.='<tr><td colspan="8"><input id="dss" type="hidden" name="dselectstring" value=""></td></tr>';


  $res.='</form>';

  $res.='</table>';
   return $res;
}

function ShowActionPanel($numstr,$pannum)
{
   $res='';
   $res.='<script>

   var condition = 0;

   function ExecAction()
   {
     var wnd = document.getElementById("actwind");


     var el = document.getElementById("actselect");
     if(!el)
      {
        alert("Undefined element");
        return;
      }
     var n = el.selectedIndex;
     if(n == 0)
     {
        alert("Не выбрано действие");
        return;
     }
     
     var frm = document.getElementById("actfrm");
     if(!frm)
      {
        alert("Undefined form");
        return;
      }
      if(n == 14)
     {
        var gr = document.getElementById("offset");
        frm.offset.value = gr.value;
     }
      frm.num_operation.value = n;
      frm.name_operation.value = el.options[n].text;
      frm.submit();
   }
   function DefineOperation(obj)
   {

     var frm = document.getElementById("actfrm");
     if(!frm)
      {
        alert("Undefined form");
        return;
      }
      var n = obj.selectedIndex;
      var ds = document.getElementById("departmentselect");
      var gs = document.getElementById("graphselect");
      var zs = document.getElementById("zoneselect");
      var as = document.getElementById("algoritmtselect");
      var gr = document.getElementById("offset");
      gr.style.display = "none";
      
      switch (n) {
        case 0:
              ShowCloseModalWindow("actwind",1);
        break;
        case 1:
              ShowCloseModalWindow("actwind",0);
              var t = document.getElementById("title");
                  t.value = obj.options[n].text;
              var f = document.getElementById("field");
                  f.value = "Отдел:";
                  ds.style.display = "block";
                  gs.style.display = "none";
                  zs.style.display = "none";
                  as.style.display = "none";
                  condition = 1;

          break;
        case 2:
             ShowCloseModalWindow("actwind",0);
              var t = document.getElementById("title");
                  t.value = obj.options[n].text;
              var f = document.getElementById("field");
                  f.value = "График:";
                  ds.style.display = "none";
                  gs.style.display = "block";
                  zs.style.display = "none";
                  as.style.display = "none";


                  condition = 2;
          break;
        case 3:
              ShowCloseModalWindow("actwind",0);
              var t = document.getElementById("title");
                  t.value = obj.options[n].text;
              var f = document.getElementById("field");
                  f.value = "Рабочая зона";
                  ds.style.display = "none";
                  gs.style.display = "none";
                  zs.style.display = "block";
                  as.style.display = "none";

                  condition = 3;
          break;
        case 4:
              ShowCloseModalWindow("actwind",0);
              var t = document.getElementById("title");
                  t.value = obj.options[n].text;
              var f = document.getElementById("field");
                  f.value = "Тип расчёта:";
                  ds.style.display = "none";
                  gs.style.display = "none";
                  zs.style.display = "none";
                  as.style.display = "block";

                  condition = 4;

        break;
        case 5:
              frm.cond.value = "blockpass";
              ShowCloseModalWindow("actwind",1);
        break;
        case 6:
              frm.cond.value = "unblockpass";
              ShowCloseModalWindow("actwind",1);
        break;
        case 7:
              frm.cond.value = "doadmin";
              ShowCloseModalWindow("actwind",1);
        break;
        case 8:
            frm.cond.value = "undoadmin";
            ShowCloseModalWindow("actwind",1);
        break;
        case 9:
             frm.cond.value = "remove";
             ShowCloseModalWindow("actwind",1);
        break;
        case 10:
             frm.cond.value = "dodouble";
             ShowCloseModalWindow("actwind",1);
        break;
        case 11:
             frm.cond.value = "undodouble";
             ShowCloseModalWindow("actwind",1);
        break;
	case 12:
             frm.cond.value = "setguest";
             ShowCloseModalWindow("actwind",1);
        break;
        case 13:
             frm.cond.value = "unsetguest";
             ShowCloseModalWindow("actwind",1);
        break;
        case 14:
             frm.cond.value = "groffset";
             ShowCloseModalWindow("actwind",1);
             gr.style.display = "block";
        break;

        default:break;
      }
    }
   function GetCondition()
   {
        //alert(condition);
        var frm = document.getElementById("actfrm");
         if(condition == 1)
         {
            var ds = document.getElementById("departmentselect");
            var n = ds.selectedIndex;
            var dep_id = ds.options[n].value;
             frm.cond.value = "department";
             frm.condval.value = dep_id;
            ShowCloseModalWindow("actwind",1);
         }
         if(condition == 2)
         {
            var gs = document.getElementById("graphselect");
            var n = gs.selectedIndex;
            var graph_id = gs.options[n].value;
             frm.cond.value = "schedule";
             frm.condval.value = graph_id;
            ShowCloseModalWindow("actwind",1);
         }
         if(condition == 3)
         {
            var zs = document.getElementById("zoneselect");
            var n = zs.selectedIndex;
            frm.cond.value = "zone";
            frm.condval.value = zs.options[n].value;
            ShowCloseModalWindow("actwind",1);
         }
          if(condition == 4)
         {
            var as = document.getElementById("algoritmtselect");
            var n = as.selectedIndex;
            frm.cond.value = "algoritm";
            frm.condval.value = as.options[n].value;
            ShowCloseModalWindow("actwind",1);
         }

   }

   </script>';

   $res.='<table border="0" width="100%" cellspacing="1" cellpadding="2" align="center" class="dtab">';
   $res.='<tr bgcolor="gray">';
   $res.='<td width=20%><p class="tabhead"><b>Количество записей: '.$numstr.'</b></td>';
   if($pannum==1)
   {
     $res.='<td align="center" width="50%">&nbsp;</td>';
     $res.='<td align="center"><input type="button" name="addlist" value="добавить выбраных" onclick=AddList() class="sbutton"></td>';
     $res.='<td align="center"><input type="button" name="show" value="фильтр" onclick=ValidateFilter(document.filterfrm) class="sbutton"></td>';
    }
   if($pannum==2)
   {
     $res.='<td align="right" width="50%" ><p class="tabhead">Действие:
            <select name="act" id="actselect" onChange=DefineOperation(this) class="select">
                               <option value="0">нет</option>
                               <option value="1">перевести в отдел</option>
                               <option value="2">назначить график</option>
                               <option value="3">назначить зону</option>
                               <option value="4">назначить рассчёт отработанного времени</option>
                               <option value="5">блокировать пропуска</option>
                               <option value="6">разблокировать пропуска</option>
                               <option value="7">назначить "Администратором"</option>
                               <option value="8">снять "Администратора"</option>
			       <option value="9">Удалить</option>
                               <option value="10">установить контроль двойных засечек</option>
                               <option value="11">убрать контроль двойных засечек</option>
			       <option value="12">установить пропускам гостевой статус</option>
                               <option value="13">убрать гостевой статус</option>
                               <option value="14">Установить смещение графика</option>
			       
            </select>
            </td>';
     $res.='<td align="center"><input type="text" id ="offset" style="display:none;" name="offset" value="0" size="5" class="input"></td>';
     $res.='<td align="center"><input type="button" name="clear" value="очистить" onclick=document.location.href="groupop.php?action=cdestlist" class="sbutton"></td>';
     $res.='<td align="center"><input type="button" name="execaction" value="выполнить" onclick=ExecAction() class="sbutton"></td>';
   }
   $res.='</tr>';
   $res.='</table>';


   $res.='<form id="actfrm" name="actfrm" action="executing.php" method="POST" target="_blank">';
   $res.='<input type="hidden" name="num_operation" value="">';
   $res.='<input type="hidden" name="name_operation" value="">';
   $res.='<input type="hidden" name="act" value="execoperation">';
   $res.='<input type="hidden" name="cond" value="">';
   $res.='<input type="hidden" name="condval" value="">';
   $res.='<input type="hidden" name="offset" value="">';
   $res.='</form>';

   $res .= '<div id="actwind" style="display:none;position:absolute;top:30%;left:42%;width:40%; z-index: 1000;">';
        $res .= '<table border="0" cellpadding="2" cellspacing="0" width="100%" class="dtab">';
           $res .= '<tr class="tablehead">';
               $res .= '<td><input id="title" type="text" style="border:none;background-color:gray;color:white;" value="" size="50" readonly></td>';
               $res .= '</tr>';
               $res .= '<tr>';
               $res .= '<td ><input id="field" type="text" style="border:none;background-color:#f5f5dc;" value="">&nbsp;&nbsp;

                        <select id="zoneselect" class="input" style="display:none;" class="select"><option value="0">не назначена</option>';
                          $result=pg_query("select * from BASE_W_S_ZONE(NULL)");
                          while($r=pg_fetch_array($result))
                            $res .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
                         $res.= '</select>
                         <select id="graphselect" style="display:none;" class="select"><option value="0">не назначен</option>';
                         $result=pg_query("select * from BASE_W_S_GRAPH_NAME(NULL)");
                         while($r=pg_fetch_array($result))
                            $res.='<option value="'.$r['id'].'">'.$r['name'].'</option>';
                         $res .= '</select>
                         <select id="departmentselect" style="display:none;" class="select"><option value="0"></option>';
                         $result=pg_query('select * from BASE_W_S_DEPT('.$_SESSION['iduser'].')');
                         while($r=pg_fetch_array($result))
                            $res.='<option value="'.$r['id'].'">'.$r['name'].'</option>';
                           $res.='</select>';

                        $res.='<select id="algoritmtselect" style="display:none;" class="select"><option value="0"></option>';
                         $result=pg_query("select * from BASE_W_S_WORK_TYPE(0)");
                         while($r=pg_fetch_array($result))
                            $res.='<option value="'.$r['id'].'">'.$r['name'].'</option>';
                           $res.='</select>
                        </td>';

               $res .= '</tr>';
               $res .= '<tr>';
               $res .= '<td align="right">
                        <input type="button" value="выбрать" class="sbutton" onclick=GetCondition()>
                        <input type="button" value="отмена" class="sbutton" onClick = ShowCloseModalWindow("actwind",1)>
                        </td>';
               $res .= '</tr>';
        $res .= '</table>';

   $res .= '</div>';

   return $res;
}
/*****************************************************************************/

/****************ОБРАБОТКА ДЕЙСТВИЙ********************************************/
//если пришли на страницу для создания нового списка
if($_REQUEST['action']=='newlist')
{
  if(isset($_SESSION['selpers']))unset($_SESSION['selpers']);
}

// обработка фильтра
if($_REQUEST['action']=='filter')
{

  $col1="silver";
  $col2="#f5f5dc";
  $bgcolor='';
  $flag=0;
  $tab_num = ($_REQUEST['tab_num'] == '' || !is_numeric($_REQUEST['tab_num'])) ? 'NULL' : $_REQUEST['tab_num'];
  
/////////////////////////////////////////////////////////////////////////////////////////////////////////
  $liststr='';
  $q='select * from BASE_W_S_PERSONAL_FILTER(\''.CheckString($_REQUEST['family']).'\','
          . '\''.CheckString($_REQUEST['fname']).'\','
          . '\''.CheckString($_REQUEST['secname']).'\','
          . '\''.CheckString($_REQUEST['position']).'\','
          . '\''.$_REQUEST['dept_id'].'\','
          .$_REQUEST['graph_id'].','
          .$_REQUEST['zone_id'].','
          .$tab_num.')';
						

   //echo $q.'<br>';
   $res=pg_query($q);

   $i=0;
   while($r=pg_fetch_array($res))
   {
     if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
     // echo $i;
     $liststr.='<tr  bgcolor='.$bgcolor.' >';
     $liststr.='<td ><input id="'.$r['id'].'" type="checkbox" name="check" onclick=SetSelectPers('.$r['id'].')><td>';
	  $liststr.='<td>'.$r['tabel_num'].'</td>';
     $liststr.='<td>'.$r['family'].'</td>';
     $liststr.='<td>'.$r['name'].'</td>';
     $liststr.='<td>'.$r['secname'].'</td>';
     $liststr.='<td>'.$r['pos'].'</td>';
     $liststr.='<td>'.$r['dept'].'</td>';
     $liststr.='<td>'.$r['graph_name'].'</td>';
     $liststr.='<td>'.$r['zone_name'].'</td>';
     $liststr.='</tr>';
     $i++;
   }
  if($numstr!=0)$numstr=$i+1;else $numstr=$i;
}

//Добавляем список новыми сотрудниками
if($_REQUEST['action']=='addlist' && $_REQUEST['dselectstring']!='')
{
     $col1="silver";
     $col2="#f5f5dc";
     $bgcolor='';
     $flag=0;
    //парсим строку с id сотрудников
     $SS=explode(",",$_REQUEST['dselectstring']);
    // Добавляем массив $SS в сессию
     AddIdInSession($SS);
}
//Очищаем строку
if(isset($_SESSION['selpers']) && $_REQUEST['action']=='cdestlist')
{
    unset($_SESSION['selpers']);
}
//Выводим сформированый список
if(isset($_SESSION['selpers']))
{
     $col1="silver";
     $col2="#f5f5dc";
     $bgcolor='';
     $flag=0;
     $i=0; $j=0;

    for($i=0;$i<sizeof($_SESSION['selpers']);$i++)
     {
       if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
        $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
//        echo $q;

         $res=pg_query($q);
        while($r=pg_fetch_array($res))
        {
            //print_r($r);
            $destlist.='<tr  bgcolor='.$bgcolor.'>';
            $destlist.='<td width=21 align=center><td>';
			 $destlist.='<td>'.$r['tabel_num'].'</td>';
            $destlist.='<td>'.$r['family'].'</td>';
            $destlist.='<td>'.$r['name'].'</td>';
            $destlist.='<td>'.$r['secname'].'</td>';
            $destlist.='<td>'.$r['position'].'</td>';
            $destlist.='<td>'.$r['dept'].'</td>';
            $destlist.='<td>'.$r['gr_name'].'</td>';
            $destlist.='<td>'.$r['zone_name'].'</td>';
            $destlist.='</tr>';
            $j++;
        }
     }
    if($numrec!=0)$numrec=$j+1;else $numrec=$j;
}

/*****************************************************************************/


echo ShowList($liststr);
echo ShowActionPanel($numstr,1);
echo ShowDestinationList($destlist);
echo ShowActionPanel($numrec,2);

echo PrintFooter();


?>