<?php

include("include/input.php");
require("include/common.php");
require("include/head.php");
//require("libraries/charset_x_win.php");

$BODY.='';
if(!isset($_REQUEST['object']))
{
  $BODY='<center><font face=Verdana size="3">Не выбран объект выборки</font></center>';
  echo $BODY;
  exit();
}


if($_REQUEST['object']=='propusk')
{
 echo PrintHead('Выдача пропуска','');
 $BODY='';
 if(!isset($_REQUEST['visitor']) || is_numeric($_REQUEST['visitor'])==0 || $_REQUEST['visitor']<=0)
 {
   $BODY.='<center><font face=Verdana size="3" color="red">Не выбран посетитель</font></center>';
   echo $BODY;
   exit();
 }
 $q='select * from VISIT_W_S_VISITORS('.$_REQUEST['visitor'].',NULL,NULL,NULL,NULL,NULL)';
 $r=pg_fetch_array(pg_query($q));

 $BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
  $BODY .= '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';
    $BODY .= '<script type="text/javascript">
        $(function () {
            $("#start_date").pickmeup({
                change : function (val) {
                    $("#start_date").val(val).pickmeup("hide")
                }
            });
            $("#end_date").pickmeup({
                change : function (val) {
                    $("#end_date").val(val).pickmeup("hide")
                }
            });
           
        });

 function GetPerson(f)
 {
   var h = f.depart.selectedIndex;
   var n = f.depart.options[h].value;
   var family=f.family.value;
   var fname=f.fname.value;
   var secname=f.secname.value;

   if(family=="" && fname=="" && secname=="" && h==0)
   {
      alert("Не указан ни один из параметров поиска");return;
   }
   if(CheckString(family)==1)
   {alert("Недопустимый символ при вводе фамилии");return;}
   if(CheckString(fname)==1)
   {alert("Недопустимый символ при вводе имени");return;}
   if(CheckString(secname)==1)
   {alert("Недопустимый символ при вводе отчества");return;}


   var url="select.php?object=personal&action=find";
   url+="&depart="+n;
   if(family!="")url+="&fa="+family;
   if(fname!="")url+="&fn="+fname;
   if(secname!="")url+="&sn="+secname;
   //alert(url);
   ShowWindow(url,"Выбор сотрудника",600,310,1);
 }
 function ToGivePass(f)
 {
    if(f.pers_id.value==-1)
    {alert("Не выбран сотрудник");return;}

    if(f.start_date.value>f.end_date.value)
    {alert("Дата окончания периода действия пропуска меньше даты начала");return;}

   if(f.start_time.value=="")
   {alert("Не укзано начальное время");return;}
   if(f.end_time.value=="")
   {alert("Не укзано время окончания");return;}

    if(isTime(f.start_time.value)==false)return;
    if(isTime(f.end_time.value)==false)return;

    if(f.start_date.value==f.end_date.value  && f.start_time.value==f.end_time.value)
    {alert("Некорректный период действия пропуска");return;}

    //alert(f.current_date.value);
    if(f.current_date.value<f.start_date.value && f.current_date.value>f.end_date.value)
    {alert("Некорректный период действия пропуска");return;}

    if(f.prop_name.selectedIndex==0)
    {alert("Не выбран пропуск");return;}

    if(f.dopusk_name.selectedIndex==0)
    {alert("Не выбран допуск");return;}

    var ell = document.getElementById("ToGive_Pass");
    ell.style.display="none";
    
    f.dopusk_id.value=f.dopusk_name.value;
    f.propusk_id.value=f.prop_name.value;

    f.action="executing.php?action=insert&dest=visitings";
    //alert(f.action);
    f.submit();
    //window.close();
 }
 </script>';
 $BODY.='<form name="propusk" action="" method="POST" >';
 $BODY.='<table border=0 cellpadding="1" cellspacing="1" class="dtab" align="center" width="100%">';
 $BODY.='<tr class="tablehead">';
 $BODY.='<td align="left" >Посетитель:
         <input type="hidden" name="visitor" value='.$r['id'].'>
         </td>';
 $BODY.='<td align="left">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
 $BODY.='</tr>';
 $BODY.='<tr>';
 $BODY.='<td align="left"><span class="text">К кому:</span>
         <input type="hidden" name="pers_id" value="-1">
         </td>';
 $BODY.='<td align="left"><span class="text">Отдел:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
         <select name="depart" class="input" style="width:200px;">
         <option value="0">Не выбран</option>';
         $res=pg_query('select * from  BASE_W_S_DEPT('.$_SESSION['iduser'].')');
         while($d=pg_fetch_array($res))
         {
            $BODY.='<option value="'.$d['id'].'">'.$d['name'].'</option>';
          }
   $BODY.='</select><br>';
   $BODY.='<span class="text">Фамилия:&nbsp;<br></span>';
   $BODY.='<input name="family" type="text" value="" class="input" size="20" ><br>';
   $BODY.='<span class="text">&nbsp;Имя:&nbsp;</span><br>';
   $BODY.='<input name="fname" type="text" value="" class="input" size="20" ><br>';
   $BODY.='<span class="text">&nbsp;Отчество:&nbsp;</span><br>';
   $BODY.='<input name="secname" type="text" value="" class="input" size="20" ><br>';

   $BODY.='<input type="button" value="найти" class="sbutton" onclick=GetPerson(document.propusk)>';
 $BODY.='</td>';
 $BODY.='</tr>';
 $BODY.='<tr>';
        $BODY.='<td align="left"><span class="text">Период:&nbsp;</span></td>';
        $BODY.='<td align="left">
                <span class="text">&nbsp;С:&nbsp;&nbsp;&nbsp;</span>
                <input type="hidden" name="current_date" value="'.date("d.m.Y").'">
                <input type="text" id ="start_date" name="start_date" value="'.date("d.m.Y").'" size="8" readonly class="input">
                
                <input type="text" name="start_time" value="'.date("H:i").'" size="3" maxlength="5" class="input"><br>
                <span class="text">&nbsp;По:&nbsp;</span>
                <input type="text" id ="end_date" name="end_date" value="'.date("d.m.Y").'" size="8" readonly class="input">
                
                <input type="text" name="end_time" value="'.date("H:i",mktime(date("H")+4,date("i"),0,0,0,0)).'" size="3" maxlength="5" class="input">
                </td>';
 $BODY.='</tr>';

 $BODY.='<tr>';
        $BODY.='<td>';
        $BODY.='<span class="text">Пропуск:</span>';
        $BODY.='</td>';
        $BODY.='<td>';
        $BODY.='<select name="prop_name" class="input"><option value="0">Не выбран</option>';
             $q='select * from VISIT_W_S_CODES()';
             $result=pg_query($q);
            //echo $q;

             while($c=pg_fetch_array($result))
             {
               $BODY.='<option value="'.$c['id'].'">'.$c['comment'].'</option>';


             }

        $BODY.='</select><input type="hidden" name="propusk_id">';
        $BODY.='</td>';
 $BODY.='</tr>';
 $BODY.='<tr>';
        $BODY.='<td>';
        $BODY.='<span class="text">Допуск:</span>';
        $BODY.='</td>';
        $BODY.='<td>';
        $BODY.='<select name="dopusk_name" class="input"><option value="0">Не выбран</option>';
             $q='select * from BASE_W_S_DOPUSK(null)';
             $result=pg_query($q);

             while($d=pg_fetch_array($result))
             {
               $BODY.='<option value="'.$d['id'].'">'.$d['name'].'</option>';
             }
        $BODY.='</select><input type="hidden" name="dopusk_id">';
        $BODY.='</td>';
 $BODY.='</tr>';

 $BODY.='<tr class="tablehead">';
 $BODY.='<td colspan="2" align="right">
         <input id = "ToGive_Pass" type="button" value="выдать" class="sbutton" onclick=ToGivePass(document.propusk)>
         <input type="button" value="отмена" class="sbutton" onclick="window.close()">
         </td>';
 $BODY.='</tr>';

 $BODY.='</table>';
 $BODY.='</form>';

 echo $BODY;
 echo PrintFooter();
}

if($_REQUEST['object']=='personal')
{
 echo PrintHead('Выбор сотрудника','');
 $BODY='';
 $BODY.='<script>

 function SelectedPers(id,fa,fn,sn)
 {
     opener.document.propusk.pers_id.value=id;
     opener.document.propusk.family.value=fa;
     opener.document.propusk.fname.value=fn;
     opener.document.propusk.secname.value=sn;
     self.close();
 }
 </script>';
 if(isset($_REQUEST['action']) && $_REQUEST['action']=='find')
 {
     $dep=0;
     $family='';
     $fname='';
     $secname='';
     if($_REQUEST['depart']>0 && is_numeric($_REQUEST['depart'])>0)
       $dep=$_REQUEST['depart'];
     if(isset($_REQUEST['fa']))$family=CheckString($_REQUEST['fa']);
     if(isset($_REQUEST['fn']))$fname=CheckString($_REQUEST['fn']);
     if(isset($_REQUEST['sn']))$secname=CheckString($_REQUEST['sn']);

    $BODY.='<table border=0 cellpadding="1" cellspacing="1"  width="100%">';
    $BODY.='<tr class="tablehead">';
          $BODY.='<td align="center">Фамилия</td>';
          $BODY.='<td align="center">Имя</td>';
          $BODY.='<td align="center">Отчество</td>';
          $BODY.='<td align="center">Отдел</td>';
          $BODY.='<td align="center"></td>';
    $BODY.='</tr>';

    $col1="silver";
    $col2="#f5f5dc";
    $bgcolor='';
    $flag=0;

    $q='select * from BASE_W_S_PERSONAL_PAGE(null,null,1,0,\''.$family.'\',
                                         \''.$fname.'\',
                                         \''.$secname.'\',\'\','.$dep.',null,\'\','.$_SESSION['iduser'].',\'0\',null,\'\')';
    $result=pg_query($q);
    while($r=pg_fetch_array($result))
    {
          if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}

          $BODY.='<tr class="tabcontent" bgcolor='.$bgcolor.'>';
          $BODY.='<td align="center">'.$r['family'].'</td>';
          $BODY.='<td align="center">'.$r['name'].'</td>';
          $BODY.='<td align="center">'.$r['secname'].'</td>';
          $BODY.='<td align="center">'.$r['dept'].'</td>';
          $BODY.='<td align="center"><a href="#" onclick=\'SelectedPers('.$r['id'].',"'.$r['family'].'","'.$r['name'].'","'.$r['secname'].'")\' class="slink">Выбрать</a></td>';
          $BODY.='</tr>';
    }

    $BODY.='</table>';
 }

  echo $BODY;
 echo PrintFooter();
}
