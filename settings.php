<?php

$IDMODUL=26;
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
echo PrintHead('СКУД - Настройки','Настройки');
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}


//подгружаем меню
//require("include/menu.php");
require_once("include/menu.php");

if(isset($_REQUEST['action']))
{
   if($_REQUEST['action']=='save')
   {
       $item=explode(",",$_REQUEST['idstring']);
        for($i=0;$i<sizeof($item);$i++)

        {

         $val = '';
         if(isset($_REQUEST['val'.$item[$i]]))$val = $_REQUEST['val'.$item[$i]];
          else if(!isset($_REQUEST['val'.$item[$i]]))
             $val='0';
         if($val == 'on')
              $val = '1'; 

$val = str_replace('\\\\','\\',$val);
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
        $q.='select BASE_W_U_CONST('.$item[$i].',\''.$val.'\')';
        pg_query($q);
       }
   }
}

$BODY='';

$_BOOL = '';
$_TEXT = '';
$_DATE = '';
$BODY.='<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$BODY.='<!--center><b><font face="Verdana" size=2 color="red">
         ВНИМАНИЕ!!! НЕКОРРЕКТНОЕ ИЗМЕНЕНИЕ НАСТРОЕК МОЖЕТ ПРИВЕСТИ К НЕГАТИВНЫМ ПОСЛЕДСТВИЯМ.<BR>
         ЗАПОЛНЯЙТЕ ПОЛЯ В СООТВЕТСТВИИ С ПРЕДСТАВЛЕННЫМИ ШАБЛОНАМИ

       </font></b></center-->';
$BODY.='<form name="settingsfrm" action="settings.php?action=save" method="POST">';
$BODY.='<table border=0 cellpadding="1" cellspacing="1" width="70%" class="dtab" align="center">';

$q='select * from BASE_W_S_CONST(null,null)';

$result=pg_query($q);
$i=0;
$idstring='';
while($r=pg_fetch_array($result))
{
  if($r['type']=='bool')
  {
    $checked = '';
    if($r['value'] == 1)
    {
      $checked = 'checked';
    }
    else
    {
      $checked = '';
    }
    $_BOOL.='<li><input type="checkbox" name="val'.$r['id'].'" '.$checked.' ><span class="text" >'.$r['descr'].'</span></li>';
  }
  if($r['type']=='text')
  {
    $_TEXT.='<li><input type="text" name="val'.$r['id'].'" value="'.$r['value'].'" class="input" size="20"><span class="text" >&nbsp;'.$r['descr'].'</span></li>';
  }
  if($r['type']=='date')
  {
    $_DATE.='<li><input type="text" name="val'.$r['id'].'" value='.$r['value'].' class="input" size="20"><span class="text" >&nbsp;'.$r['descr'].'</span></li>';
  }
  $idstring .=$r['id'].',';
}
$BODY.='<tr>';
      $BODY.='<td><ul>'.$_BOOL.'</ul></td>';
$BODY.='</tr>';
$BODY.='<tr>';
      $BODY.='<td><ul>'.$_TEXT.'</ul></td>';
$BODY.='<tr>';
      $BODY.='<td><ul>'.$_DATE.'</ul></td>';
$BODY.='</tr>';
$BODY.='<tr>
            <td>
                <ul>
                    <li><input id = "time" type="text" value="'.date("Y-m-d H:i:s").'" class="input" size="20">
                        <span class="text" >&nbsp; <button class="sbutton" type="button" onclick=\'change_server_time();\'>Изменить</button>&nbsp;&nbsp; Формат ввода: "год-месяц-число часы:минуты:секунды"</span></li>
                </ul>
            </td>
        </tr>';


$idstring=substr($idstring,0,strlen($idstring)-1);
$BODY.='<tr class="tablehead">';
$BODY.='<td align="right" colspan="3">
       <input type="hidden" name="idstring" value="'.$idstring.'">
       <input type="submit" name="save" class="sbutton" value="сохранить">
       <input type="button" name="save" class="sbutton" value="отмена">
       </td>';
$BODY.='</tr>';
$BODY.='</table>';
$BODY.='</form>';


echo $BODY;

echo PrintFooter();
?>