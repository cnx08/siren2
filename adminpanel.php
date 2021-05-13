<?php

$IDMODUL=1;
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
echo PrintHead('СКУД','Административная панель');

//проверяем на доступность
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
if(!isset($_REQUEST['action']))$_REQUEST['action']='showlist';

require_once("include/menu.php");
$BODY.='
    <script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>
    <script>
    function checkAll(f,obj)
    {
       var flg=1;
       if(obj.checked==0)flg=0;
      for(var i=0;i<f.elements.length;i++)
      {
         var item = f.elements[i];
         if(item.type=="checkbox")
         {
            if(flg==1)item.checked=1;else if(flg==0)item.checked=0;
         }
      }

    }

    </script>';
 $BODY.='<script>

           function CheckForm(f)
           {

            if(f.login1.value=="")
            {alert("Не назначен логин");f.login1.focus();return;}
            if(CheckString(f.login1.value)==1)
            {alert("Недопустимый символ при вводе логина");f.login1.focus();return;}
            if(f.pass.value=="" && f.pass_h.value=="0")
            {alert("Не назначен пароль");f.pass.focus();return;}
           
            var uid = typeof f.uid=="undefined" ? 2000 : f.uid.value;
            
            var data1 = {obj:"checklogin",login1:f.login1.value};
            $.ajax({
            url: "asinc.php", 
            type: "POST",
            data: data1,
            dataType: "json",
            success: function (dota) {
                var dataObj = eval(dota);
                if (dataObj.res > 0 && uid != dataObj.res)
                {
                    alert("Такой пользователь уже существует");
                    f.login1.focus();return;

                }
                else if(dataObj.res == -5){
                    alert("Логин должен состоять только из букв и/или цифр");
                    f.login1.focus();return;
                }
                else{
                    f.modulid.value="";
                    var j=0;
                    for(var i=0;i<f.elements.length;i++)
                    {
                         var item=f.elements[i];
                         if(item.name.indexOf("mod")>-1)
                         {
                            if(item.checked==1)
                            {
                                var id=item.name.substr(3,item.name.length);
                                if(f.modulid.value=="")
                                  f.modulid.value+=id;
                                 else
                                  f.modulid.value+=","+id;
                              j++;
                            }
                         }
                    }
                   if(j!=0){
                    f.submit();
                    }
                    else {alert("Не указан ни один модуль");return;}
                }
               },
             error: function (xhr, ajaxOptions, thrownError) {
                 alert(xhr);alert(ajaxOptions);alert(thrownError);
             }
            });  

            
           }
           </script>';

if($_REQUEST['action']=='showlist')
{
$BODY.='<br><table width="100%" cellpadding="1" cellspacing="1" align=center class="dtab">';
$BODY.='<tr>';
$BODY.='<td align="left" colspan="2">';
$BODY.='<a href="adminpanel.php?action=add" class="actlink">Создать нового</a>&nbsp;&nbsp;';
$BODY.='</td>';
$BODY.='</tr>';

$BODY.='<tr class="tablehead">';
$BODY.='<td colspan="2" align="center">';
$BODY.='Список пользователей';
$BODY.='</td>';

$BODY.='</tr>';
$BODY.='</table>';

$BODY.='<table cellpadding="1" cellspacing="1" align=center class="dtab2">';
//$BODY.='<tr class="tablehead"></tr>';
$BODY.='<tr class="tablehead">';
$BODY.='<td align="center"">Модуль</td>';

$q='select * from BASE_W_S_USERS(NULL)';
$result2=pg_query($q);
while($r2=pg_fetch_array($result2))
{
    $BODY.='<td align="center">'.$r2['login'].'<br>';
    $BODY.='<img  src="buttons/guest1.gif" onclick=document.location.href="adminpanel.php?action=depacces&uid='.$r2['id'].'" class="icons" title="Доступ к персоналу">
            <img src="buttons/edit.gif" onclick=document.location.href="adminpanel.php?action=edit&uid='.$r2['id'].'" class="icons" title="Править">
            <img  src="buttons/remove.gif" onclick=document.location.href="adminpanel.php?action=del&uid='.$r2['id'].'" class="icons" title="Удалить">
         </td>';
}
$BODY.='</tr>';



$col1="silver";
$col2="#f5f5dc";
$bgcolor='';
$flag=0;
$q='select * from BASE_W_S_MODULS(NULL)';
$result=pg_query($q);
while($r=pg_fetch_array($result))
{
    if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
    $BODY.='<tr bgcolor='.$bgcolor.'>';
    $BODY.='<td align="center" >'.$r['name'].'</td>';
    
    $q1='select * from BASE_W_S_MODUL_PERM('.$r['id'].')';
    $result1=pg_query($q1);
    $check='';
    while($r1=pg_fetch_array($result1))
    {
        $BODY.='<td align="center" class="tabcontent">';
        if($r1['access']==1)$check='checked';else $check='';
        $BODY.='<input type="checkbox" '.$check.' disabled="disabled">';
        $BODY.='</td>';
    }
    $BODY.='</tr>';
}



$BODY.='</table>';
}
if($_REQUEST['action']=='depacces' &&  isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid'])>0 && $_REQUEST['uid']>=0)
{
   
   $col1="silver";
   $col2="#f5f5dc";
   $bgcolor='';
   $flag=0;

   $BODY.='<form action="adminpanel.php?action=savedepacces" name="deptacces" method="POST">';
   $BODY.='<input type="hidden" name="uid" value="'.$_REQUEST['uid'].'">';
   $BODY.='<br><table border=0 celpadding="3" cellspacing="1" class="dtab" align="center" width="50%">';
   $BODY.='<tr class="tablehead">';
   $BODY.='<td>Доступ:</td>';
   $BODY.='<td>Отдел:</td>';
   $BODY.='</tr>';
   $BODY.='<tr class="tablehead">';
   $BODY.='<td colspan="2"><input type="checkbox" class="input" onclick=checkAll(document.deptacces,this)>&nbsp;Выделить всё</td>';
   $BODY.='</tr>';
   $q = 'select * from BASE_W_S_GRAND_USER_DEPT('.$_REQUEST['uid'].')';
   $result = pg_query($q);
   $iddept = '';
   while($r = pg_fetch_array($result))
   {
       $checked = '';
       if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}

       if($r['checked']==1)$checked = 'checked';else $checked = '';
       $BODY.='<tr bgcolor='.$bgcolor.' class="tabcontent">';
       $BODY.='<td ><input type="checkbox" name="flag'.$r['id'].'" '.$checked.' class="input"></td>';
       $BODY.='<td>'.$r['name'].'</td>';
       $BODY.='</tr>';
       $iddept.=$r['id'].',';
   }
   $iddept=substr($iddept,0,strlen($iddept)-1);
   $BODY.='<tr bgcolor="gray">';
   $BODY.='<td colspan="2" align="right">
           <input type="hidden" name="iddept" value="'.$iddept.'">
           <input type="submit" value="сохранить" class="sbutton">
           <input type="button" value="отмена" class="sbutton" onclick=document.location.href="adminpanel.php?action=showlist">
           </td>';
   $BODY.='</tr>';
   $BODY.='</table>';
   $BODY.='</form>';
}
//add user
if($_REQUEST['action']=='add')
{
  
   $BODY.='<form action="adminpanel.php?action=new" name="adduser" method="POST">';
   $BODY.='<br><table border=0 celpadding="1" cellspacing="1" class="dtab" align="center">';
   $BODY.='<tr>';
   $BODY.='<td><p class="text">Логин:</td>';
   $BODY.='<td align="left"><input type="text" name="login1" autocomplete="off" class="input" value="" maxlength="50"></td>';
   $BODY.='<td><p class="text">Пароль:&nbsp;<input type="password" autocomplete="off" name="pass" class="input" value="" maxlength="50">';
   $BODY.='<input type="hidden" name="pass_h" value="0" maxlength="5"></td>';
   $BODY.='</tr>';

   $BODY.='<tr>';
   $BODY.='<td><p class="text" >Доступ:</p>
          <input type="hidden" name="modulid" value="">
          </td>';
   $BODY.='<td>';
   $BODY.='<input type="checkbox" name="auto_dept_grant" ><span class="text">Автоматически давать доступ к новым отделам</span>';
   $BODY.='</td>';
   $BODY.='<td></td>';
   $BODY.='</tr>';

   $q='select * from BASE_W_S_MODULS(NULL)';
   $result=pg_query($q);
   while($r=pg_fetch_array($result))
   {
    $BODY.='<tr>';
          $BODY.='<td><input type="checkbox" name="mod'.$r['id'].'"></td>';
          $BODY.='<td class="tabcontent">'.$r['name'].'</td>';
          $BODY.='<td class="tabcontent">'.$r['description'].'</td>';

    $BODY.='</tr>';
   }
   $BODY.='<tr class="tablehead">';
    $BODY.='<td colspan="2" align="left"><input type="checkbox" class="input" onclick=checkAll(document.adduser,this)>&nbsp;Выделить всё</td>';
   $BODY.='<td colspan="2" align="right"><input type="button" name="save" class="sbutton" value="сохранить" onclick=CheckForm(document.adduser)>&nbsp;&nbsp;';
   $BODY.='<input type="button" name="cancel" class="sbutton" value="отмена" onclick=document.location.href="adminpanel.php?action=showlist"></td>';
   $BODY.='</tr>';
   $BODY.='</table>';
   $BODY.='</form>';
}
$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
//remove user
if($_REQUEST['action']=='del' && isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid'])>0 && $_REQUEST['uid']>=0)
{
    $q .= 'select BASE_W_D_USERS('.$_REQUEST['uid'].');';
    pg_query($q);
    echo ShowConfirmWindow('Подтверждение','Пользователь удалён','adminpanel.php?action=showlist');
  
}
//edit user
if($_REQUEST['action']=='edit'&& isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid'])>0 && $_REQUEST['uid']>=0)
{
    

   $q='select * from BASE_W_S_USERS('.$_REQUEST['uid'].');';
   $result=pg_query($q);
   $r=pg_fetch_array($result);

   $BODY.='<form action="adminpanel.php?action=update" name="edituser" method="POST">';
   $BODY.='<br><table  celpadding="1" cellspacing="1" class="dtab" align="center">';
   $BODY.='<tr>';
   $BODY.='<td align="left"><p class="text">Логин:</td>';
   $BODY.='<td><input type="hidden" name="uid" value="'.$r['id'].'">
          <input type="text" name="login1" class="input" value="'.$r['login'].'" maxlength="50"></td>';
   $BODY.='<td align="left"><p class="text">Пароль:';
   $BODY.='<input type="password" name="pass" class="input" value="" maxlength="50">';
   $BODY.='<input type="hidden" name="pass_h" value="1" maxlength="5"></td>';
   $BODY.='</tr>';

   $BODY.='<tr>';
   $BODY.='<td><p class="text" >Доступ:</p>
          <input type="hidden" name="modulid" value="">

          </td>';
   $checked = '';
   if($r['auto_grand']==1)$checked = 'checked';else $checked = '';
   $BODY.='<td>';
   $BODY.='<input type="checkbox" name="auto_dept_grant" '.$checked.'><span class="text">Автоматически давать доступ к новым отделам</span>';
   $BODY.='</td>';
   $BODY.='<td></td>';
   $BODY.='</tr>';

   $q='select * from BASE_W_S_MODULS('.$_REQUEST['uid'].')';
   $result=pg_query($q);
   $check='';
   $select='';
   while($r=pg_fetch_array($result))
   {
    $BODY.='<tr>';
          if($r['access']==1){$select.=$r['id'].','; $check='checked';}else {$check='';}
          $BODY.='<td><input type="checkbox" name="mod'.$r['id'].'" '.$check.'></td>';
          $BODY.='<td class="tabcontent">'.$r['name'].'</td>';
          $BODY.='<td class="tabcontent">'.$r['description'].'</td>';

    $BODY.='</tr>';
   }

   $select=substr($select,0,strlen($select)-1);
   $BODY.='<tr class="tablehead">';
   $BODY.='<td colspan="2"><input type="checkbox" class="input" onclick=checkAll(document.edituser,this)>&nbsp;Выделить всё</td>';
   $BODY.='<td colspan="2" align="right">
           <input type="hidden" name="modulidold" value="'.$select.'">
           <input type="button" name="save" class="sbutton" value="сохранить" onclick=CheckForm(document.edituser)></td>';
   $BODY.='<td colspan="3" align="right"><input type="button" name="cancel" class="sbutton" value="отмена" onclick=document.location.href="adminpanel.php?action=showlist"></td>';
   $BODY.='</tr>';
   $BODY.='</table>';
   $BODY.='</form>';
}

if($_REQUEST['action']=='new')
{
     $auto_grand = 0;
     $error = 0;
     if(isset($_REQUEST['auto_dept_grant'])) $auto_grand = 1;else  $auto_grand = 0;
    if (!preg_match('/[^A-Za-z0-9]/', $_REQUEST['login1']))
    {
        $q.='select * from BASE_W_I_USERS(\''.CheckString($_REQUEST['login1']).'\',\''.md5($_REQUEST['pass'].'S*a^l#t_i(s)_$s!e}t').'\','.$auto_grand.');';
        $result=pg_query($q);
        $r=pg_fetch_array($result);
        $userid=$r['0'];

        $modul=explode(",",$_REQUEST['modulid']);
        if(sizeof($modul)>0)
        {
            for($i=0;$i<sizeof($modul);$i++)
            {
               $q='select BASE_W_U_PERMISSION('.$userid.','.$modul[$i].',1);';
               if(!pg_query($q))
               {
                 $error = 1; break;
               }
            }
        }
    }
    else $error = 1;
    if($error == 1) echo ShowErrorWindow('Ошибка при сохранении','adminpanel.php?action=showlist');
    else echo ShowConfirmWindow('Подтверждение','Новый пользователь добавлен','adminpanel.php?action=showlist');
}
if($_REQUEST['action']=='update' && isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid'])>0 && $_REQUEST['uid']>=0)
{
     $auto_grand = 0;
     $error = 0;
     if(isset($_REQUEST['auto_dept_grant'])) $auto_grand = 1;else  $auto_grand = 0;
    if (!preg_match('/[^A-Za-z0-9]/', $_REQUEST['login1']))
    {
        
        $pas = strlen($_REQUEST['pass'])> 0 ? md5($_REQUEST['pass'].'S*a^l#t_i(s)_$s!e}t') : '';
        $q.='select BASE_W_U_USERS('.$_REQUEST['uid'].',\''.CheckString($_REQUEST['login1']).'\',\''.$pas.'\','.$auto_grand.')';
        pg_query($q);
        $modul=explode(",",$_REQUEST['modulid']);
        $oldmodul=explode(",",$_REQUEST['modulidold']);

        $item=array_diff($modul,$oldmodul);
        sort($item,SORT_NUMERIC);
        if(sizeof($item)>0)
        {
            for($i=0;$i<sizeof($item);$i++)
            {
               $q='select BASE_W_U_PERMISSION('.$_REQUEST['uid'].','.$item[$i].',1);';
               pg_query($q);
            }
        }
        $item=array_diff($oldmodul,$modul);
        sort($item,SORT_NUMERIC);
        if(sizeof($item)>0)
        {
            for($i=0;$i<sizeof($item);$i++)
            {
               $q='select BASE_W_U_PERMISSION('.$_REQUEST['uid'].','.$item[$i].',0);';
               if(!pg_query($q))
               {
                 $error = 1; break;
               }
            }
        }
    }
    else $error = 1;
    if($error == 1) echo ShowErrorWindow('Ошибка при сохранении','adminpanel.php?action=showlist');
    else echo ShowConfirmWindow('Подтверждение','Изменения сохранены успешно','adminpanel.php?action=showlist');
   // HEADER("Location:adminpanel.php?action=showlist");
}
if($_REQUEST['action']=='savedepacces' && isset($_REQUEST['uid']) &&  is_numeric($_REQUEST['uid'])>0 && $_REQUEST['uid']>=0)
{
 $error = 0;
 $_dept = explode(',',$_REQUEST['iddept']);
 $size = sizeof($_dept);
 for($i = 0;$i < $size;$i++ )
 {
   $val = '';
   if(isset($_REQUEST['flag'.$_dept[$i]]))$val = 0;else $val=1;
   $q .= 'select BASE_W_I_GRAND_DEPT('.$_REQUEST['uid'].','.$_dept[$i].','.$val.');';

   if(!pg_query($q))
   {
     $error = 1 ; break ;
   }
 }
 if($error == 1) echo ShowErrorWindow('Ошибка при сохранении','adminpanel.php?action=showlist');
 else echo ShowConfirmWindow('Подтверждение','Изминения сохранены успешно','adminpanel.php?action=showlist');
}
echo $BODY;
echo PrintFooter();


?>