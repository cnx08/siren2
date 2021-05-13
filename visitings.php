<?php

$IDMODUL=19;
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
echo PrintHead('СКУД','Журнал посещений');
//проверяем на доступность
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
if(!isset($_REQUEST['action']))$_REQUEST['action']='';

require_once("include/menu.php");
$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
if($_REQUEST['action']=='getpass' && isset($_REQUEST['vid']) && is_numeric($_REQUEST['vid']) && $_REQUEST['vid']>0)
{
   $q.='select VISIT_W_U_GET_CARD('.$_REQUEST['vid'].')';
   @pg_query($q);

   $_REQUEST['action']='show';
}
if($_REQUEST['action']=='del' && isset($_REQUEST['rid']) && is_numeric($_REQUEST['rid'])>0 && $_REQUEST['rid']>0)
{
  $q.='select VISIT_W_D_VISITES('.$_REQUEST['rid'].')';
  pg_query($q);
  $_REQUEST['action']='show';
}

if($_REQUEST['action']=='show')
{

$st_date = '';
$en_date = '';
if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
if(isset($_REQUEST['end_date'])) $en_date = $_REQUEST['end_date'];
$BODY='';
$BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
   $BODY .= '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';
    $BODY .= '<script type="text/javascript">
        $(function () {
            $("#start_date").pickmeup({
                change : function (val) {
                    $("#start_date").val(val).pickmeup("hide")
                },
                position : "top"
            });
            $("#end_date").pickmeup({
                change : function (val) {
                    $("#end_date").val(val).pickmeup("hide")
                }
                ,
                position : "top"
            });

        });
function ShowRecords(f)
{
  //if(f.start_date.value>f.end_date.value)
   //{alert("Некорректный период");return;}

  f.submit();
}

</script>';

$BODY.='<div class="listcont" style="width:95%;height:80%;left:2%">';
$BODY .= '<span class="tabcontent">Последние 100 посещений</span>';
       $BODY.='<div class="listconteiner" style="height:95%">';
            $BODY.='<table border="0" cellpadding="1" cellspacing="1" width="100%">';
            
		   $BODY.='<tr class="tablehead">';
                 $BODY.='<td align="center">#</td>';
                 $BODY.='<td align="center"  width = "80px">Дата визита </td>';
                 $BODY.='<td align="center">Посетитель</td>';
                 $BODY.='<td align="center">К кому</td>';
                 $BODY.='<td align="center">Пропуск</td>';
                 $BODY.='<td align="center">Время действия пропуска</td>';
                 $BODY.='<td align="center">Допуск</td>';
                 $BODY.='<td align="center">Путь гостя</td>';
                 $BODY.='<td align="center">Выдан</td>';
                 $BODY.='<td align="center">Сдан</td>';
                 $BODY.='<td align="center">&nbsp;</td>';
            $BODY.='</tr>';
       $col1="silver";
       $col2="#E1E1E1";
       $col3="green";
       $col4="#FF5950";
       $col5="#F1F178";
       $bgcolor='';
       $flag=0;
       $i=1;

          $q='select * from VISIT_W_S_VISITES(NULL,\''.$st_date.'\',\''.$en_date.'\')';
		  //echo $q;
            $result=pg_query($q);
            while($r=pg_fetch_array($result))
            {
                 if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
                 if($r['online']==1) $bgcolor=$col3;
                 if($r['online']==0 && $r['lost']==1) $bgcolor=$col4;
                 if($r['online']==0 && $r['lost']==2) $bgcolor=$col5;
                    $str=str_replace("i","Незавершённый вход&nbsp;",$r['str']);
                    $str=str_replace("g","Незавершенный гостевой выход&nbsp;",$str);
                    $str=str_replace("G","Выход гостевой&nbsp;",$str);
                    $str=str_replace("I","Вход&nbsp;",$str);
                    $str=str_replace("O","Выход&nbsp;",$str);
                if(isset($_REQUEST['lost']) && $_REQUEST['lost']=='on'){
                     if( $r['lost']==1 && $r['online']==0){
                            $BODY.='<tr bgcolor='.$bgcolor.' class="tabcontent" onmouseover=this.style.backgroundColor="#89F384" onmouseout=this.style.backgroundColor="'.$bgcolor.'">';
                            $BODY.='<td align="center">'.$i.'</td>';
                            $BODY.='<td align="center">'.$r['date'].'</td>';
                            $BODY.='<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'<br>'.$r['pos'].'</td>';
                            $BODY.='<td align="center">'.$r['pfamily'].'&nbsp;'.$r['pname'].'&nbsp;'.$r['psecname'].'<br>'.$r['ppos'].' - '.$r['deptname'].'</td>';
                            $BODY.='<td align="center">'.$r['comment'].'</td>';
                            $BODY.='<td align="center">'.$r['date_in'].'<br>'.$r['date_out'].'</td>';
                            $BODY.='<td align="center">'.$r['zname'].'</td>';
                            $BODY.='<td >'.$str.'</td>';
                            $BODY.='<td align="center">'.$r['login'].'</td>';
                            $BODY.='<td align="center">нет</td>';
                         if($r['online']==1)
                            $BODY.='<td align="center"><nobr><img src="buttons/setpas.gif" onclick=document.location.href="visitings.php?action=getpass&vid='.$r['id'].'" class="icons" title="Изъять пропуск">
                                                       <img src="buttons/remove.gif" onclick=document.location.href="visitings.php?action=del&rid='.$r['id'].'" class="icons" title="Удалить"></nobr></td>';
                         else
                            $BODY.='<td align="center"><img src="buttons/remove.gif" onclick=document.location.href="visitings.php?action=del&rid='.$r['id'].'" class="icons" title="Удалить"></td>';
                        $BODY.='</tr>'; 
                     }
                }
                else{
                    $BODY.='<tr bgcolor='.$bgcolor.' class="tabcontent" onmouseover=this.style.backgroundColor="#89F384" onmouseout=this.style.backgroundColor="'.$bgcolor.'">';
                        $BODY.='<td align="center">'.$i.'</td>';
                        $BODY.='<td align="center">'.$r['date'].'</td>';
                        $BODY.='<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'<br>'.$r['pos'].'</td>';
                        $BODY.='<td align="center">'.$r['pfamily'].'&nbsp;'.$r['pname'].'&nbsp;'.$r['psecname'].'<br>'.$r['ppos'].' - '.$r['deptname'].'</td>';
                        $BODY.='<td align="center">'.$r['comment'].'</td>';
                        $BODY.='<td align="center">'.$r['date_in'].'<br>'.$r['date_out'].'</td>';
                        $BODY.='<td align="center">'.$r['zname'].'</td>';
                        $BODY.='<td >'.$str.'</td>';
                        $BODY.='<td align="center">'.$r['login'].'</td>';
                        $passback = $r['lost'] == 1 ? 'нет' : 'да';
                        $BODY.='<td align="center">'.$passback.'</td>';
                     if($r['online']==1)
                        $BODY.='<td align="center"><nobr><img src="buttons/setpas.gif" onclick=document.location.href="visitings.php?action=getpass&vid='.$r['id'].'" class="icons" title="Изъять пропуск">
                                                   <img src="buttons/remove.gif" onclick=document.location.href="visitings.php?action=del&rid='.$r['id'].'" class="icons" title="Удалить"></nobr></td>';
                     else
                        $BODY.='<td align="center"><img src="buttons/remove.gif" onclick=document.location.href="visitings.php?action=del&rid='.$r['id'].'" class="icons" title="Удалить"></td>';
                    $BODY.='</tr>';
                }
               $i++;
            }
            $BODY.='</table>';

       $BODY.='</div>';

       $BODY.='<div class="listhead" style="width:100%;">';
       $BODY.='
               <form name="periodfrm" action="visitings.php?action=show" method="POST">
               <table width=100% cellpadding=0 cellspacing=0 style="position:relative;top:3px;">
                    <tr class="tablehead"><td valign="bottom" colspan ="5">Вывести записи C:
                    <input type="text" id="start_date" name="start_date" value="'.$st_date.'" readonly class="input">
                    
                    &nbsp;По &nbsp;
                    <input type="text" id="end_date" name="end_date" value="'.$en_date.'" readonly class="input">
                    
                    <input type="button" value="вывести" class="sbutton" onclick=ShowRecords(document.periodfrm)>
                    <input type="button" value="очистить" class="sbutton" onclick=ClearForm(document.periodfrm)>
                    <input type="checkbox" name="lost"> Только не сданные пропуска
                    </td><tr>
                    
                    <tr class="tablehead">
                        <td width = "6%"></td>
                        <td align="left" width = "12%">
                            <div style="float:left;border:1px solid black;width:15px;height:15px;background-color:green">     </div>
                            <span class="text"> - Активен</span>
                        </td>
 
                        <td align="middle"  width = "40%">
                            <div style="float:left;border:1px solid black;width:15px;height:15px;background-color:#FF5950">     </div>
                            <span class="text"> - Утерян (пропуск не был брошен в отборник, и в дальнейшем нет событий от этого пропуска)</span>
                        </td>

                        <td align="middle" width = "40%">
                            <div style="float:left;border:1px solid black;width:15px;height:15px;background-color:#F1F178"></div>
                            <spanstyle="align:left;"> - Вернули (пропуск не был брошен в отборник, и в дальнейшем появились события от этого пропуска)</span>
                        </td>
                        <td></td>
                    </tr>
               </table>
               </form>';
      
       $BODY.='</div>';

$BODY.='</div>';
}


echo $BODY;
?>