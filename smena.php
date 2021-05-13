<?php
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
 //header("Content-Type: text/html; charset=utf-8");

if(!isset($_REQUEST['action']))$_REQUEST['action']='';
echo PrintHead('СКУД','Рабочие смены предприятия');
$IDMODUL=9;
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}

require("include/menu.php");

$msg='';
if(!isset($_REQUEST['namesm']))$_REQUEST['namesm']='';
if(!isset($_REQUEST['start_sm']))$_REQUEST['start_sm']='';
if(!isset($_REQUEST['end_sm']))$_REQUEST['end_sm']='';
if(!isset($_REQUEST['start_din']))$_REQUEST['end_din']='';
if(!isset($_REQUEST['end_din']))$_REQUEST['end_din']='';
if(!isset($_REQUEST['descrip']))$_REQUEST['descrip']='';


function ShowList()
{
  $col1="silver";
  $col2="#f5f5dc";
  $bgcolor='';
  $flag=0;
  $res='';

  $res.='<script type="text/javascript">
  function EditItem(id,name,desc,start,end,start_din,end_din)
  {
     var frm =document.getElementById("addsmena");
     frm.save.value = "сохранить";
     frm.id_smena.value = id;
     frm.namesm.value = name; frm.namesm.value = frm.namesm.value.replace("-"," ");
     frm.start_sm.value = start;
     frm.end_sm.value = end;
     frm.start_din.value = start_din;
     frm.end_din.value = end_din;
     frm.start_sm.value = start;
     frm.descrip.value = desc;frm.descrip.value = frm.descrip.value.replace("-"," ");
     ShowCloseModalWindow("addsm",0);

     frm.action = "smena.php?action=save";

  }

  </script>';
	$res.='<br><div class="listcont" style="width:95%;height:80%;left:2%">';
	$res.='<div class="listconteiner" style="height:95%">';
  $res.='<table border="0" width="100%"  cellspacing="1" cellpadding="1" align="center" class="dtab">';
  $res.='<tr class="tablehead">';
  $res.='<td align=center width="20%" >название смены</td>';
  $res.='<td align=center width="20%">cмена</td>';
  $res.='<td align=center width="20%">обед</td>';
  $res.='<td align=center width="5%"></td>';
  $res.='<td align=center width="5%"></td>';
  $res.='</tr>';

  $q='select * from BASE_W_S_SMENA(NULL)';
        $result=pg_query($q);
        while($r=pg_fetch_array($result))
        {
            if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
            $res.='<tr bgcolor='.$bgcolor.' onmouseover=\'this.style.backgroundColor="#89F384"\' onmouseout=\'this.style.backgroundColor="'.$bgcolor.'"\'>';
            $res.='<td align="center" ><p class="tabcontent">'.$r['name'].'</p></td>';
            $res.='<td align="center" ><p class="tabcontent">'.$r['start_sm'].'-'.$r['end_sm'].' </p></td>';
            $res.='<td align="center" ><p class="tabcontent">'.$r['start_din'].'-'.$r['end_din'].'</p></td>';
			
				$name = str_replace("&quot;","\"",$r['name']);
				$name = str_replace(" ","-",$name);
				$name = addcslashes($name,"\"");
				$desc = str_replace("&quot;","\"",$r['description']);
				$desc = str_replace(" ","-",$desc);
				$desc = addcslashes($desc,"\"");

            $res.='<td align="center" width="5%"><a href="#" class="slink"
                           onClick=\'javascript:EditItem('.$r['id'].',"'.$name.'","'.$desc.'","'.$r['start_sm'].'","'.$r['end_sm'].'","'.$r['start_din'].'","'.$r['end_din'].'")\'><img src="buttons/edit.gif" class="icons"></a></td>';
                 if($r['del']==1)
                 {
                    $res.='<td align="center" width="5%"><a class="slink" href="smena.php?action=del&amp;sid='.$r['id'].'"><img src="buttons/remove.gif" class="icons"></a></td>';
                 }
                 else
                 {
                    $res.='<td align="center" width="5%"><img src="buttons/remove1.gif" class="icons"></td>';
                  }

             $res.='</tr>';
        }

	   $res.='</table>';
	  $res.='</div>';
	  $res.='<div class="listhead" style="width:100%;">';
	  $res.='<img align="right" style="margin:3px;cursor:pointer; vertical-align: bottom;" src="buttons/icons.gif"  alt="создать смену" onclick=\'ShowCloseModalWindow("addsm",0)\' />';
	  $res.='</div>';
	$res.='</div>';
   return $res;
}

function ShowActionPanel()
{
  $res='';

  $res.='<div id="addsm" style="display:none;position:absolute;top:200px;left:400px;z-index:25;">';
  $res.='<form id="addsmena" name="addsmena" action="smena.php?action=new" method="POST">';
  $res.='<input type="hidden" name="id_smena" value="">';
  $res.='<table border="0"  width="300"class="dtab" cellspacing="0" cellpadding="0">';
  $res.='<tr class="tablehead">';
  $res.='<td align="center" colspan="2">Cмена</td>';
  $res.'</tr>';
  $res.='<tr>';
  $res.='<td ><p class="text">Название</td>';
  $res.='<td ><input type="text" name="namesm" value="" maxlength="50" class="input"></td>';
  $res.='</tr>';
  $res.='<tr>';
  $res.='<td ><p class="text" align="left">Смена &nbsp;&nbsp;с:</td>';
  $res.='<td ><input type="text" name="start_sm" value="" size="10" maxlength="5" class="input"></td>';
  $res.='</tr>';
  $res.='<tr>';
  $res.='<td ><p class="text" align="right">по:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
  $res.='<td ><input type="text" name="end_sm" value="" size="10" maxlength="5" class="input"></td>';
  $res.='</tr>';
  
  $res .= '<tr>';
  $res.='<td ><p class="text" align="left">Обед &nbsp;&nbsp;&nbsp;с:</td>';
  $res.='<td ><input type="text" name="start_din" value="" size="10" maxlength="5" class="input"></td>';
  $res.='</tr>';
  $res.='<tr>';
  $res.='<td ><p class="text" align="right">по:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
  $res.='<td ><input type="text" name="end_din" value="" size="10" maxlength="5" class="input"></td>';
  $res.='</tr>';
  $res.='<tr><td colspan="2" align="center"><p class="text">Описание</td></tr>';
  $res.='<tr>
          <td colspan="2">
          <textarea name="descrip" rows="10" cols="35" class="input">&nbsp;</textarea>
          </td>
          </tr>';
  $res.='<tr bgcolor="gray">
         <td align="left"><input type="button" name="save" onclick=\'CheckSmenaForm(document.addsmena)\' value="добавить" class="sbutton" /></td>
         <td align="right" ><input type="button" name="cancel" onclick=\'ShowCloseModalWindow("addsm",1)\' value="отмена" class="sbutton" /></td>
         </tr>';
  $res.='</table>';

  $res.='</form>';
  $res.='</div>';

  return $res;
}
/******************************************************************************/
//вывод всех смен
if($_REQUEST['action']=='showall')
{
 echo ShowActionPanel();
 echo ShowList();

}
$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
if($_REQUEST['action']=='new')
{
   $q.='select BASE_W_I_SMENA(\''.$_REQUEST['start_sm'].'\',
                       \''.$_REQUEST['end_sm'].'\',
                       \''.$_REQUEST['start_din'].'\',
                       \''.$_REQUEST['end_din'].'\',
                       \''.CheckString($_REQUEST['namesm']).'\',
                       \''.CheckString($_REQUEST['descrip']).'\')';
   //echo $q.'<br>';
   $msg='<center><p class="text">новая смена'.$_REQUEST['namesm'].' добавлена</p></center>';
   if(!pg_query($q))
     echo ShowErrorWindow('Ошибка при создании смены','');
   else
    echo ShowConfirmWindow('Подтверждение','Новая смена добавлена','');

   $_REQUEST['action']='showall';
   echo ShowActionPanel();
   echo ShowList();


}

if($_REQUEST['action'] == 'save' && isset($_POST['id_smena']) && IdValidate($_POST['id_smena'])==true)
{

      $q.='select BASE_W_U_SMENA('.$_POST['id_smena'].',\''.$_REQUEST['start_sm'].'\',
                       \''.$_REQUEST['end_sm'].'\',
                       \''.$_REQUEST['start_din'].'\',
                       \''.$_REQUEST['end_din'].'\',
                       \''.CheckString($_REQUEST['namesm']).'\',
                       \''.CheckString($_REQUEST['descrip']).'\')';

   $msg='<center><p class="text">Изминения сохранены</p></center>';
   pg_query($q) ;

   $_REQUEST['action']='showall';
   echo ShowActionPanel();
   echo ShowList();

}
if($_REQUEST['action'] == 'del' && isset($_GET['sid']) && IdValidate($_GET['sid'])==true)
{
 $q .= 'select BASE_W_D_SMENA('.$_GET['sid'].')';
  pg_query($q);
  $_REQUEST['action']='showall';
   echo ShowActionPanel();
   echo ShowList();


}
echo PrintFooter();
?>
