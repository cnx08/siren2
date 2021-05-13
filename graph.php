<?php
//replaced 15.06.07
//print_r($_REQUEST);
/*
	10.11.2010 - Зорков Ю.А,
	1) добавил кэширование ob_start(), ob_flush()
	2) lвнешний вид кнопок -> изменил JS-ф-ию AddItem - а) элемент input вместо button, б) добавил тип = "button"
*/
ob_start();

$IDMODUL=10;
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
$col1="silver";
$col2="#f5f5dc";
$bgcolor='';
$flag=0;
$border = 'border="0"';
$caption = 'Графики сотрудиков';

if(isset($_REQUEST['excelflg']) && $_REQUEST['action'] == 'showgraph')
{
$border = 'border="1"';
//$caption = '<caption>Отчёт по автотранспорту за '.$_date.'</caption>';
$col1 ="#FFFFFF";
$col2 ="#FFFFFF";

}
else
{
 echo PrintHead('СКУД','Графики');
 require("include/menu.php");
}
//проверяем на доступность
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}

if(!isset($_REQUEST['action']))$_REQUEST['action']='';




$BODY='';
$BODY.='<div id="statusbar" class="infowindow text"></div>';

if($_REQUEST['action']=='show')
{
 $BODY.='<script type="text/javascript">
 function getGraphData()
 {
 	//alert(this.req.responseText);
 	var xdoc = this.req.responseXML.documentElement;
 	var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
        table+="<tr  class=client>";
        table+="<td align=center bgcolor=silver>№</td>";
        table+="<td align=center bgcolor=silver>Смена</td>";
        table+="<td align=center bgcolor=silver>Допуск</td>";
        table+="<td align=center bgcolor=silver>Зона</td>";
        table+="</tr>";

    var res = xdoc.getElementsByTagName("item");
    if(res)
      {
       var i=0;
       for(i=0;i<res.length;i++)
       {
         table+="<tr bgcolor="+res[i].getAttributeNode("bg").value+" class=clientText>";
         table+="<td align=center >"+res[i].getAttributeNode("num").value+"</td>";
         table+="<td align=center >"+res[i].getAttributeNode("name").value+"</td>";
         table+="<td align=center>"+res[i].getAttributeNode("dopusk").value+"</td>";
         table+="<td align=center>"+res[i].getAttributeNode("zone").value+"</td>";

         table+="</tr>";
       }
       if(i==0) table+="<tr class=client><td>Данному графику ничего не назначено</td></tr>";

      }
    table+="</table>";
 	this.object.wnd.client.innerHTML+=table;
 	this.object = null;
 }

 function showInfo(obj,event,gname)
 {
  var e = event || window.event;
  var infoWnd = new Window.poupWindow("info_wnd",e.clientY,e.clientX,-415,0,400,0,"window",gname.replace("-","\ "));
      infoWnd.Show();
  var net = new Net.ContentLoader("asinc.php",getGraphData,Error,"POST","obj=graph&id="+obj.id);
      net.object = infoWnd;
 }
 </script>';
 
 //$BODY.='<p stype="text-decoration: none; "><h3>Рабочие графики</h3></p>';
 $BODY.='<div class="listconteiner" style="position:absolute;top:10%;left:5%;width:90%;height:85%;border:1px solid gray;overflow:hidden;" >';

 $BODY.='<div class="listconteiner" style="height:95%;">';
 $BODY.='<table border=0 cellpadding="1" cellspacing="1"  width="100%">';
 $BODY.='<tr class="tablehead">';
        $BODY.='<td align="center" width="25%">Название</td>';
        $BODY.='<td align="center">Описание</td>';
        $BODY.='<td align="center" width="2%">Смены</td>';
        $BODY.='<td align="center" width="5%"></td>';

 $BODY.='</tr>';
 $q='select * from BASE_W_S_GRAPH_NAME(NULL)';
 $result=pg_query($q);
 while($r=pg_fetch_array($result))
 {
   $INFO ='';
   if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
   $BODY.='<tr bgcolor='.$bgcolor.' onmouseover=this.style.backgroundColor="#89F384" onmouseout=this.style.backgroundColor="'.$bgcolor.'">';
       $BODY.='<td align="center" class="tabcontent">'.$r['name'].'</td>';
       $BODY.='<td align="center" class="tabcontent">'.$r['description'].'</td>';
       $BODY.='<td valign="top" class="tabcontent" align="center">
               <img id="'.$r['id'].'" src="buttons/info3.gif" class="icons" onclick=\'showInfo(this,event,"'.str_replace(" ","-",$r['name']).'")\' alt="Показать смены" >';
       $BODY.='</td>';
       //onclick=showInfo(this,event,"'.str_replace(" ","-",$r['NAME']).'")
       $BODY.='<td align="center">';
       
       
       if($r['edit']==1 && $r['id']>2)
       {
          $BODY.='<img src="buttons/edit.gif" onclick=document.location.href="graph.php?action=edit&gid='.$r['id'].'" class="icons" alt="Править" />';
       }
       if($r['del']==1 && $r['id']>2)
       {
          $BODY.='<img src="buttons/remove.gif" onclick=document.location.href="graph.php?action=del&gid='.$r['id'].'" class="icons" alt="Удалить" />';
       }

       $BODY.='</td>';
   $BODY.='</tr>';

 }

 $BODY.='</table>';
 $BODY.='</div>';
 $BODY.='<div class="listhead" style="width:100%">';
 $BODY.='<img align="right" valign="bottom" src="buttons/icons.gif" style="margin:3px;cursor:pointer" alt="создать график" onclick=\'document.location.href="graph.php?action=new"\' />';
 $BODY.='</div>';
 $BODY.='</div>';

 $BODY.='<div id="addgraph" style="display:none;position:absolute;top:150px;left:300px;z-index:2">';
 $BODY.='<table border=0 cellpadding="1" cellspacing="1"  width="100%">';
 $BODY.='<tr>';
 $BODY.='<td></td>';
 $BODY.='</tr>';
 $BODY.='</table>';
 $BODY.='</div>';
}
//редактирование
if(($_REQUEST['action']=='edit'  && isset($_REQUEST['gid']) && is_numeric($_REQUEST['gid'])>0 && $_REQUEST['gid']>0) || $_REQUEST['action']=='new')
{
  //сначала заполняем массивы
    $BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
  $BODY .= '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';
    $BODY .= '<script type="text/javascript">
        $(function () {
            $("#gdate").pickmeup({
                change : function (val) {
                    $("#gdate").val(val).pickmeup("hide")
                }
            });
           
        });
  var LAST=0;
  var SMENA = new Array();
      SMENA[0]="0~не назначена";
  var DOPUSK = new Array();
      DOPUSK[0]="0~не назначен";
  var ZONA = new Array();
      ZONA[0]="0~не назначена";
  ';

  $q='select * from BASE_W_S_SMENA(NULL)';
  $result=pg_query($q);
  $j=1;
  while($r=pg_fetch_array($result))
  {$BODY.='SMENA['.$j.']="'.$r['id'].'~'.$r['name'].'";';$j++;}

  $q='select * from BASE_W_S_DOPUSK(NULL)';
  $result=pg_query($q);
  $j=1;
  while($r=pg_fetch_array($result))
     {$BODY.='DOPUSK['.$j.']="'.$r['id'].'~'.$r['name'].'";';$j++;}

  $q='select * from BASE_W_S_ZONE(NULL)';
  $result=pg_query($q);
  $j=1;
  while($r=pg_fetch_array($result))
     {$BODY.='ZONA['.$j.']="'.$r['id'].'~'.$r['name'].'";';$j++;}


  $BODY.='
  function Save(f)
  {
    var newval="";
    for(i=0;i<f.elements.length;i++)
    {
       var item=f.elements[i];
       if(item.name.indexOf("smena")>-1)
       {
             //alert(item.name);
             newval+=item.options[item.selectedIndex].value+",";
       }
       if(item.name.indexOf("dopusk")>-1)
       {
             //alert(item.name);
             newval+=item.options[item.selectedIndex].value+",";
       }
       if(item.name.indexOf("zona")>-1)
       {
             //alert(item.name);
             newval+=item.options[item.selectedIndex].value+";";

       }
    }
    newval=newval.substr(0,newval.length-1);
    f.itognewval.value=newval;
    if(CheckString(f.gname.value)==1)
    {alert("Недопустимый символ при вводе имени");return;}
    if(CheckString(f.descript.value)==1)
    {alert("Недопустимый символ при вводе описания");return;}
    if(f.gname.value=="")
    {alert("График должен иметь название");return;}

    f.submit();
  }

  function RemoveItem(itemid,f)
  {
     var el=document.getElementById(itemid);
     var parent=document.getElementById("main");
     parent.removeChild(el);

  }

  function AddItem(f)
  {
    if(f.smadd.selectedIndex==0){alert("не выбрана смена");return;}
    if(f.dopadd.selectedIndex==0){alert("не выбран допуск");return;}
    if(f.zoadd.selectedIndex==0){alert("не выбрана рабочая зона");return;}

    LAST=parseInt(f.count.value);
    LAST+=1;
    f.count.value=LAST;

    var parent=document.getElementById("main");
    var newel=document.createElement("div");
        newel.id="item"+LAST;
        newel.className="listitem";
    parent.appendChild(newel);

    var button=document.createElement("input");
        button.className="delbut";
		button.type="button";
        button.style.marginRight = 2+"px";
        button.style.height=20+"px";
        button.value="-";
        button.onclick=function(){
         RemoveItem(newel.id,f);
        }
    newel.appendChild(button);
    CreateSelectExt(SMENA,newel.id,"select",1,"smena"+LAST,f.smadd.selectedIndex);
    CreateSelectExt(DOPUSK,newel.id,"select",1,"dopusk"+LAST,f.dopadd.selectedIndex);
    CreateSelectExt(ZONA,newel.id,"select",1,"zona"+LAST,f.zoadd.selectedIndex);

  }
  </script>';

  echo $q='';
  $GRAPH=array();
  if($_REQUEST['action']=='edit')
  {
    $q='select * from BASE_W_S_GRAPH_NAME('.$_REQUEST['gid'].')';
    $GRAPH=pg_fetch_array(pg_query($q));
  }
  else
  {
    $GRAPH['name']='';
    $GRAPH['dete_in']=date("d.m.Y");
    $GRAPH['description']='';
    $_REQUEST['gid']=-1;

  }
  $BODY.='<form name="addgraph" action="graph.php?action=save&amp;gid='.$_REQUEST['gid'].'" method="POST">';
  $BODY.='<br><table border="0" cellpadding="1" cellspacing="1" class="dtab" width="60%" align="center">';
  $BODY.='<tr>';
      $BODY.='<td align=left ><span class="text">Название</span>
             <input type="text" name="gname" value="'.$GRAPH['name'].'" class="input" />
             <span class="text">&nbsp;Дата введения</span>
             <input type="text" id="gdate" name="gdate" value="'.$GRAPH['date_in'].'" class="input" readonly />&nbsp;';
      
  $BODY.='</tr>';
 /* $BODY.='<tr>';
       $BODY.='<td align="center"><span class="text">Смены</span></td>';
       $BODY.='<td align="center"><span class="text">Допуска</span></td>';
       $BODY.='<td align="center"><span class="text">Рабочие зоны</span></td>';
  $BODY.='</tr>';
 */
  $BODY.='<tr><td >
          <div id="main" style="height:100%;">';
  $q='select * from BASE_W_S_GRAPH('.$_REQUEST['gid'].')';
  $result=pg_query($q);
  $oldval='';
  $i=1;
   
  while($r=pg_fetch_array($result))
  {
     $BODY.='<div class="listitem" id="item'.$i.'">';
            $BODY.='<input type="button" value="-" class="delbut" style="height:20px;" onclick=\'RemoveItem("item'.$i.'",document.addgraph)\' />';

            $BODY.='<select name="smena'.$i.'" class="select"><option value="0">не назначена</option>';
            $q1='select * from BASE_W_S_SMENA(NULL)';
            $res1=pg_query($q1);
            $sel='';
            while($r1=pg_fetch_array($res1))
            {
              if($r1['id']==$r['id_sm'])$sel='selected';else $sel='';
              $BODY.='<option value='.$r1['id'].' '.$sel.'>'.$r1['name'].'</option>';
            }
            $BODY.='</select>';
            $BODY.='<select name="dopusk'.$i.'" class="select" >
            <option value="0">не назначен</option>';
            $q1='select * from BASE_W_S_DOPUSK(NULL)';
            $res1=pg_query($q1);
            $sel='';
            while($r1=pg_fetch_array($res1))
            {
              if($r1['id']==$r['id_dopusk'])$sel='selected';else $sel='';
              $BODY.='<option value='.$r1['id'].' '.$sel.'>'.$r1['name'].'</option>';

            }
            $BODY.='</select>';

            $BODY.='<select name="zona'.$i.'" class="select"><option value="0">не назначен</option>';
            $q1='select * from BASE_W_S_ZONE(NULL)';
            $res1=pg_query($q1);
            $sel='';
            while($r1=pg_fetch_array($res1))
            {
              if($r1['id']==$r['id_zone'])$sel='selected';else $sel='';
              $BODY.='<option value='.$r1['id'].' '.$sel.'>'.$r1['name'].'</option>';
            }
            $BODY.='</select>';

     $BODY.='</div>';

    $oldval.=$r['id_sm'].','.$r['id_dopusk'].','.$r['id_zone'].';';
	//echo $oldval;
    $i++;
  }
  
  $BODY.='</div></td></tr>';


  $BODY.='<tr>';
  $BODY.='<td align="left"><span class="text">Добавить</span></td>';
  $BODY.='</tr>';
  $BODY.='<tr>';
  $BODY.='<td>';
      $BODY.='<span class="text">Смена:&nbsp;</span>';
      $BODY.='<select name="smadd" class="select"><option value="0">не назначена</option>';
      $q1='select * from BASE_W_S_SMENA(NULL)';
            $res1=pg_query($q1);
            while($r1=pg_fetch_array($res1))
              $BODY.='<option value='.$r1['id'].'>'.$r1['name'].'</option>';
            $BODY.='</select>';
   $BODY.='<span class="text">&nbsp;Доступ:&nbsp;</span>';
            $BODY.='<select name="dopadd" class="select" >
            <option value="0">не назначен</option>';
            $q1='select * from BASE_W_S_DOPUSK(NULL)';
            $res1=pg_query($q1);
            while($r1=pg_fetch_array($res1))
              $BODY.='<option value='.$r1['id'].' >'.$r1['name'].'</option>';
            $BODY.='</select>';
  $BODY.='<span class="text">&nbsp;Зона:&nbsp;</span>';
  $BODY.='<select name="zoadd" class="select"><option value="0">не назначена</option>';
            $q1='select * from BASE_W_S_ZONE(NULL)';
            $res1=pg_query($q1);
            while($r1=pg_fetch_array($res1))
              $BODY.='<option value='.$r1['id'].' >'.$r1['name'].'</option>';
            $BODY.='</select>';
  $BODY.='<input type="button" value="+" class="delbut" style="height:20px;width:20px;margin-bottom:2px;" onClick=\'AddItem(document.addgraph)\' />';
  $BODY.='</td>';
  $BODY.='</tr>';



   $BODY.='<tr>';
         $BODY.='<td  valign=top>';
         $BODY.='<span class=text>Описание</span>';
         $BODY.='</td>';
  $BODY.='</tr>';
   $BODY.='<tr>';
         $BODY.='<td colspan="3" valign=top>';
          $BODY.='<textarea name="descript" rows="5" cols="40" class="input">'.$GRAPH['description'].'</textarea>';
         $BODY.='</td>';
  $BODY.='</tr>';

  $oldval=substr($oldval,0,strlen($oldval)-1);
  $BODY.='<tr class="tablehead">';
  $BODY.='<td align="right" colspan="3">
          <input type="button" value="сохранить" class="sbutton" onclick=\'Save(document.addgraph)\' />
          <input type="button" value="отмена" class="sbutton" onclick=\'document.location.href="graph.php?action=show"\' />
          <input id="itognewval" name="itognewval" type="hidden" value="" >
          <input id="itogoldval" name="itogoldval" type="hidden" value="'.$oldval.'" >
          <input id="count" name="count" type="hidden" value="'.$i.'" size="10">
          </td>';
  $BODY.='</tr>';
  $BODY.='</table>';
  $BODY.='</form>';
}

if($_REQUEST['action']=='save' && isset($_REQUEST['gid']) && is_numeric($_REQUEST['gid'])>0)
{
  //сохраняем имя и дату и описание
 if($_REQUEST['gid']>0)
 {
     $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
  $q.='select BASE_W_U_GRAPH_NAME('.$_REQUEST['gid'].',
                         \''.CheckString($_REQUEST['gname']).'\',
                         \''.CheckString($_REQUEST['gdate']).'\',
                         \''.CheckString($_REQUEST['descript']).'\')';
   pg_query($q);
  if($_REQUEST['itognewval']!='')
  {
     $NV=explode(";",$_REQUEST['itognewval']);
      if(sizeof($NV)>0)
      {
	  
// здесь надо добавить проверку на возможность удаления графика 
// путем анализа возвращаемого результата
          $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
          $q.='select BASE_W_D_GRAPH('.$_REQUEST['gid'].')';
           pg_query($q);
           for($i=0;$i<sizeof($NV);$i++)
          {
             $item=explode(",",$NV[$i]);
             $q='select BASE_W_U_GRAPH('.$_REQUEST['gid'].','.$item[0].','.$item[1].','.$item[2].')';
             pg_query($q);
          }
      }
  }
 }


 if($_REQUEST['gid']<0)
 {
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
       $q.='select * from BASE_W_I_GRAPH_NAME(\''.CheckString($_REQUEST['gname']).'\',
                                \''.CheckString($_REQUEST['gdate']).'\',
                                \''.CheckString($_REQUEST['descript']).'\')';
      $r=pg_fetch_array(pg_query($q));
      $id=$r['id'];
    if($_REQUEST['itognewval']!='')
    {
     $NV=explode(";",$_REQUEST['itognewval']);
      if(sizeof($NV)>0)
      {
           for($i=0;$i<sizeof($NV);$i++)
          {
             $item=explode(",",$NV[$i]);
             $q='select BASE_W_U_GRAPH('.$id.','.$item[0].','.$item[1].','.$item[2].')';
             pg_query($q);
          }
      }
     }

 }

  HEADER("Location:graph.php?action=show");

}
if($_REQUEST['action']=='del' && isset($_REQUEST['gid']) && is_numeric($_REQUEST['gid'])>0)
{
    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
  $q.='select BASE_W_D_GRAPH('.$_REQUEST['gid'].')';
   pg_query($q);

  $q='select BASE_W_D_GRAPH_NAME('.$_REQUEST['gid'].')';
   pg_query($q);
  HEADER("Location:graph.php?action=show");
}
if($_REQUEST['action'] == 'showf')
{
  $BODY .= '<script type="text/javascript">
  function submitFilter(f)
  {
           f.action="graph.php?action=showgraph";
           if(f.towndflg.checked == 1)
                   f.target="_blank";
           else
              f.target="_parent";
          f.submit();
  }
  </script>';
  $BODY .= '<form name="filterfrm" action="" method="POST">';
  $BODY .= '<table border="0" cellpading="0" cellspacing="0" width="40%" align="center" style="border:1px solid gray">';
  $BODY .= '<caption>Фильтр по сотрудникам</caption>';
  $BODY .= '<tr class="tablehead">';
  $BODY .= '<td colspan="2">Месяц&nbsp;'.InsertMonthYearSelect(1,1,2000,2020,'sel_date','sel_date','class=select').'</tr>';
  $BODY .= '</tr>';
  $BODY .= '<tbody>';
  $BODY .= '<tr>';
  $BODY .= '<td><span class="text">Фамилия</span></td>';
  $BODY .= '<td><input type="text" name="family" class="input"></td>';
  $BODY .= '</tr>';
  $BODY .= '<tr>';
  $BODY .= '<td><span class="text">Имя</span></td>';
  $BODY .= '<td><input type="text" name="name" class="input"></td>';
  $BODY .= '</tr>';
  $BODY .= '<tr>';
  $BODY .= '<td><span class="text">Отчество</span></td>';
  $BODY .= '<td><input type="text" name="secname" class="input"></td>';
  $BODY .= '</tr>';
  $BODY .= '<tr>';
  $BODY .= '<td><span class="text">Должность</span></td>';
  $BODY .= '<td><input type="text" name="position" class="input"></td>';
  $BODY .= '</tr>';
  $BODY .= '<tr>';
  $BODY .= '<td><span class="text">Отдел</span></td>';
  $BODY .= '<td>'.InsertSelect('select * from BASE_W_S_DEPT('.$_SESSION['iduser'].')','id',0,'','depart','style="width:185px" class="select"','все отделы',0).'</td>';
  $BODY .= '</tr>';
  $BODY .= '<tr>';
  $BODY .= '<td ><input type="checkbox" name="towndflg"><span class="text">Вывести в новом окне</span></td>';
  $BODY .= '<td ><input type="checkbox" name="excelflg"><span class="text">Вывести в Excel</span></td>';
  $BODY .= '</tr>';
  $BODY .= '<tr>';
  $BODY .= '<td><input type="button" name="show" value="показать" class="sbutton" onclick=submitFilter(document.filterfrm)></td>';
  $BODY .= '<td></td>';
  $BODY .= '</tr>';
  $BODY .= '</tbody>';

  $BODY .= '</table>';
  $BODY .= '</form>';
}
if($_REQUEST['action'] == 'showgraph')
{
    if(!isset($_REQUEST['sel_date_month']))$_REQUEST['sel_date_month']=date("m");
    if(!isset($_REQUEST['sel_date_year']))$_REQUEST['sel_date_year']=date("Y");
    if(!isset($_REQUEST['family']))$_REQUEST['family']='';
    if(!isset($_REQUEST['name']))$_REQUEST['name']='';
    if(!isset($_REQUEST['secname']))$_REQUEST['secname']='';
    if(!isset($_REQUEST['position']))$_REQUEST['position']='';
    if(!isset($_REQUEST['depart']))$_REQUEST['depart']=0;




        $_date = date("d").'.'.$_REQUEST['sel_date_month'].'.'.$_REQUEST['sel_date_year'];
        $BODY .= '<table '.$border.' cellpapding="2" cellspacing="2" width="8000px">';
        $BODY .= '<caption >'.$caption.'</caption>';
        $BODY .= '<tr class="tablehead">';
        $BODY .= '<td  width="120px" align="center">Сотрудник</td>';
    $d = 'select to_char(days,\'DD.MM.YYYY\') as days from BASE_SYS_MONTH_PERIOD(\''.$_date.'\')';
    $res = pg_query($d);
    while($r = pg_fetch_array($res))
    {
             $BODY .= '<td width="200px" align="center">';
             $BODY .= $r['days'];
             $BODY .= '</td>';
    }

    $BODY .= '</tr>';


         $pers_query = 'select * from BASE_W_S_PERSONAL_FILTER(\''.CheckString($_REQUEST['family']).'\',\''.CheckString($_REQUEST['name']).'\',\''.CheckString($_REQUEST['secname']).'\',\''.$_REQUEST['position'].'\',\''.$_REQUEST['depart'].'\',0,0,null)';

         $res = pg_query($pers_query);
         while($p = pg_fetch_array($res))
         {
                  if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}

                  $q = 'select * from BASE_W_S_GRAPH_MONTH('.$p['id'].',\''.$_date.'\')';
                  //echo $q;
    $BODY .= '<tr bgcolor="'.$bgcolor.'">';
        $BODY .= '<td><span class="text">'.$p['fio'].'</span></td>';
                  $result = pg_query($q);
                  $color = '';
                  $doc = '';
                   while($g = pg_fetch_array($result))
                  {
                           if($g['doc'] >-1)
                           {
                             $color = 'bgcolor="green"';

                             $doc = 'Документ №'.$g['doc'];
                           }
                           else
                           {
                       $color = '';
                             $doc = '';
                           }
                           $BODY .= '<td '.$color.'><span class="text">';
                           $BODY .= '<b>Смена:&nbsp;</b>'.$g['sname'].'<br>';
                           $BODY .= '<b>Допуск:&nbsp;</b>'.$g['dname'].'<br>';
                           $BODY .= '<b>Зона:&nbsp;</b>'.$g['zname'].'<br>';
                           $BODY .= $doc;
                           $BODY.=  '</span></td>';

                  }
                  $BODY .= '</tr>';

         }
    $BODY .= '</table>';
    if(isset($_REQUEST['excelflg']))
        {
           $fname = 'report.xls';
             header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=$fname");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
                header("Pragma: public");
                echo $BODY;
                exit();
        }
}
echo $BODY;

ob_flush();
?>