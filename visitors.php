<?php

$IDMODUL=18;
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');


echo PrintHead('СКУД','Журнал посетителей');
//проверяем на доступность
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
if(!isset($_REQUEST['action']))$_REQUEST['action']='';

require_once("include/menu.php");

$BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
if($_REQUEST['action']=='show' || $_REQUEST['action']=='find')
{


if(!isset($_REQUEST['date']))$_REQUEST['date']='';
if(!isset($_REQUEST['family']))$_REQUEST['family']='';
if(!isset($_REQUEST['fname']))$_REQUEST['fname']='';
if(!isset($_REQUEST['secname']))$_REQUEST['secname']='';

$BODY.='<script>

function Find(f)
{
  //alert(f.family.value);
  if(CheckString(f.family.value)==1)
  {alert("Недопустимый символ при вводе фамилии");f.family.focus();return;}
  if(CheckString(f.fname.value)==1)
  {alert("Недопустимый символ при вводе имени");f.fname.focus();return;}
  if(CheckString(f.secname.value)==1)
  {alert("Недопустимый символ при вводе отчества");f.secname.focus();return;}
  if(CheckString(f.position.value)==1)
  {alert("Недопустимый символ при вводе должности");f.position.focus();return;}
  f.submit();
}
function GetPass(user_id)
{
  ShowWindow("select.php?object=propusk&visitor="+user_id,"Выбор пропуска",600,350,0);
}
</script>';

//$BODY.='<br>';
$BODY.='<div class="listcont" style="width:95%;height:85%;left:2%">';
       $BODY .= '<span class="tabcontent">Последние 100 посетителей</span>';
	   $BODY.='<div class="listconteiner" style="height:95%">';
            $BODY.='<table border="0" cellpadding="1" cellspacing="1" width="100%">';
            $BODY.='<tr class="tablehead">';
                 $BODY.='<td align="center">#</td>';
                 $BODY.='<td align="center">Дата</td>';
                 $BODY.='<td align="center">Фамилия</td>';
                 $BODY.='<td align="center">Имя</td>';
                 $BODY.='<td align="center">Отчество</td>';
                 $BODY.='<td align="center">Должность</td>';
                 $BODY.='<td align="center">Паспорт</td>';
                 $BODY.='<td align="center">Коментарий</td>';
                 $BODY.='<td ></td>';
                 $BODY.='<td></td>';
                 $BODY.='<td></td>';
            $BODY.='</tr>';

       $col1="silver";
       $col2="#f5f5f5";
       $bgcolor='';
       $flag=0;
       $i=1;
       $q='';
       if($_REQUEST['action']=='show') $q='select * from VISIT_W_S_VISITORS(NULL,NULL,NULL,NULL,NULL,NULL)';

       if($_REQUEST['action']=='find')
       {
         $q='select * from VISIT_W_S_VISITORS(NULL,\''.CheckString($_REQUEST['date']).'\',
                                      \''.CheckString($_REQUEST['family']).'\',
                                      \''.CheckString($_REQUEST['fname']).'\',
                                      \''.CheckString($_REQUEST['secname']).'\',
                                      \''.CheckString($_REQUEST['position']).'\')';
       }

      // echo $q;
       $result=pg_query($q);

       while($r=pg_fetch_array($result))
       {
          if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
          $BODY.='<tr bgcolor='.$bgcolor.' class="tabcontent" onmouseover=this.style.backgroundColor="#89F384" onmouseout=this.style.backgroundColor="'.$bgcolor.'">';
                 $BODY.='<td align="center" >'.$i.'</td>';
                 $BODY.='<td align="center">'.$r['date'].'</td>';
                 $BODY.='<td align="center">'.$r['family'].'</td>';
                 $BODY.='<td align="center">'.$r['name'].'</td>';
                 $BODY.='<td align="center">'.$r['secname'].'</td>';
                 $BODY.='<td align="center">'.$r['pos'].'</td>';
                 $BODY.='<td align="Left">'.$r['pasport'].'</td>';
                 $BODY.='<td align="Left">'.$r['comment'].'</td>';
                 $BODY.='<td align="center"><img  src="buttons/edit.gif" onclick=document.location.href="visitors.php?action=edit&uid='.$r['id'].'" class="icons" alt="править"></td>';
                 $BODY.='<td align="center"><img src="buttons/remove.gif" onclick=document.location.href="visitors.php?action=del&uid='.$r['id'].'" class="icons" alt="удалить"></td>';
                 $BODY.='<td align="center"><img src="buttons/givepas.gif" class="icons" onclick=GetPass('.$r['id'].') alt="Выдать пропуск"></td>';
          $BODY.='</tr>';
          $i++;
       }
       $BODY.='</table>';
       $BODY.='</div>';

       $BODY.='<div class="listhead" style="width:100%;">';
       $BODY.='<img align="right" valign="bottom" src="buttons/icons.gif" style="margin:3px;cursor:pointer" alt="создать посетителя" onclick=document.location.href="visitors.php?action=new">';
        $BODY.='<img align="right" valign="bottom" src="buttons/search.gif" style="margin:3px;cursor:pointer" alt="поиск посетителя" onclick=ShowCloseModalWindow("findwind",0)>';
       $BODY.='</div>';

$BODY.='</div>';


//окошко поиска
$BODY.='<div id="findwind" class="findwindow" >';
     //форма поиска сотрудника
     $BODY.='<form name="findfrm" action="visitors.php?action=find" method="POST" >';
     $BODY.='<table border=0 width="350" height="100%" class="dtab" cellpadding="4" cellspacing="0" class="mwtab">';
     $BODY.='<tr class="tablehead">
             <td>Поиск посетителя</td>
             <td></td>
             </tr>';
       $BODY.='<tr>';
             $BODY.='<td><span class="text">Дата посещения</span></td>';
             $BODY.='<td><input type="text" name="date" value="'.$_REQUEST['date'].'" class="input" readonly>
                         <input type="button" class="sbutton" value="..." onClick=ShowCalendar(document.findfrm.date,1900,2030,"dd.mm.yyyy")>
                    </td>';
       $BODY.='</tr>';
       $BODY.='<tr>';
             $BODY.='<td><span class="text">Фамилия</span></td>';
             $BODY.='<td><input type="text" name="family" value="'.$_REQUEST['family'].'" class="input" ></td>';
       $BODY.='</tr>';
             $BODY.='<td><span class="text">Имя</span></td>';
             $BODY.='<td><input type="text" name="fname" value="'.$_REQUEST['fname'].'" class="input" ></td>';
       $BODY.='<tr>';
             $BODY.='<td><span class="text">Отчество</span></td>';
             $BODY.='<td><input type="text" name="secname" value="'.$_REQUEST['secname'].'" class="input" ></td>';
       $BODY.='</tr>';
       $BODY.='<tr>';
             $BODY.='<td><span class="text">Должность</span></td>';
             $BODY.='<td><input type="text" name="position" value="'.$_REQUEST['position'].'" class="input" ></td>';
       $BODY.='</tr>';

       $BODY.='<tr bgcolor="gray">
               <td  align="left"><input type="button" value="Найти" class="sbutton" onclick=Find(document.findfrm)></td>
               <td align="right"><input type="button" value="отмена" onClick="CloseFindFrm()" class="sbutton"></td>
               </tr>';
       $BODY.='</table>';
     $BODY.='</form>';
     $BODY.='</div>';



$BODY.='</div>';
}

if($_REQUEST['action']=='new' ||($_REQUEST['action']=='edit' && isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid'])>0))
{
  $BODY.='<script>

  function Save(f)
  {

    if(f.family.value=="")
    {alert("Не указана фамилия");f.family.focus();return;}
    if(f.fname.value=="")
    {alert("Не указано имя");f.fname.focus();return;}

    if(CheckString(f.family.value)==1)
    {alert("Недопустимый символ при вводе фамилии");f.family.focus();return;}
    if(CheckString(f.fname.value)==1)
    {alert("Недопустимый символ при вводе имени");f.fname.focus();return;}
    if(CheckString(f.secname.value)==1)
    {alert("Недопустимый символ при вводе отчетсва");f.secname.focus();return;}
    if(CheckString(f.position.value)==1)
    {alert("Недопустимый символ при вводе должности");f.position.focus();return;}
    if(CheckString(f.pasport.value)==1)
    {alert("Не допустимый символ при вводе паспортных данных");f.pasport.focus();return;}
    if(CheckString(f.comment.value)==1)
    {alert("Не допустимый символ при вводе комментария");f.comment.focus();return;}

     //if(f.fhoto.value!=""){
    // if(GetFileName("phn",f.fhoto.value)==false)erflag=1;
     //if(CheckExpansion(f.photoname.value,"jpg")==false){alert("Неверное расширение файла");return;}
    // }
     if(f.filename.value!=""){
            var tto = f.filename.value;
            f.photoname.value=tto;
        }
    f.submit();

  }
  function ShowAdvanced(wind,obj)
  {
    alert(wind);alert(obj.name);
  }
  </script>';
  $action='';
  //$advancedparam='';
  if($_REQUEST['action']=='edit')
  {
      $q='select * from VISIT_W_S_VISITORS('.$_REQUEST['uid'].',NULL,NULL,NULL,NULL,NULL)';
      $r=pg_fetch_array(pg_query($q));
      $_REQUEST['visitor_id']=$r['id'];
      $_REQUEST['date']=$r['date'];
      $_REQUEST['family']=$r['family'];
      $_REQUEST['fname']=$r['name'];
      $_REQUEST['secname']=$r['secname'];
      $_REQUEST['position']=$r['pos'];
      $_REQUEST['pasport']=$r['pasport'];
      $_REQUEST['comment']=$r['comment'];
      $_REQUEST['photoname']=$r['photo'];
      $action='visitors.php?action=save';
       //print_r($_REQUEST);
  }
  else
  {
     $_REQUEST['visitor_id']=0;
     $_REQUEST['date']=date("d.m.Y");
     $_REQUEST['family']='';
     $_REQUEST['fname']='';
     $_REQUEST['secname']='';
     $_REQUEST['position']='';
     $_REQUEST['pasport']='';
     $_REQUEST['comment']='';
     $_REQUEST['photoname']='';
     $action='visitors.php?action=add';
     //$advancedparam='<input type="checkbox" name="addrecord" onclick=ShowAdvanced("advan",this)><span class="text">Добавть запись в журнал посещений</span>';
  }

  $BODY.='<form ENCTYPE="multipart/form-data" name="newvisitor" action="'.$action.'" method="POST">';
  $BODY.='<table border="0" cellpadding="2" cellspacing="0" width="50%" align="center" class="dtab">';
  $BODY.='<tr>';
         $BODY.='<td><span class="text">Дата</span>
                 </td>';
         $BODY.='<td><input type="text" name="date" value="'.$_REQUEST['date'].'" readonly class="input">

                </td>';
  $BODY.='</tr>';
  $BODY.='<tr>';
         $BODY.='<td><span class="text">Фамилия</span></td>';
         $BODY.='<td><input type="text" name="family" value="'.$_REQUEST['family'].'"  class="input">
                     <input type="hidden" name="visitor_id" value='.$_REQUEST['visitor_id'].'>
                 </td>';
  $BODY.='</tr>';
  $BODY.='<tr>';
         $BODY.='<td><span class="text">Имя</span></td>';
         $BODY.='<td><input type="text" name="fname" value="'.$_REQUEST['fname'].'"  class="input"></td>';
  $BODY.='</tr>';
  $BODY.='<tr>';
         $BODY.='<td><span class="text">Отчество</span></td>';
         $BODY.='<td><input type="text" name="secname" value="'.$_REQUEST['secname'].'"  class="input"></td>';
  $BODY.='</tr>';
  $BODY.='<tr>';
         $BODY.='<td><span class="text">Должность</span></td>';
         $BODY.='<td><input type="text" name="position" value="'.$_REQUEST['position'].'"  class="input"></td>';
  $BODY.='</tr>';
  $BODY.='<tr>';
         $BODY.='<td valign="top"><span class="text">Паспортные данные</span><br>
         <textarea  name="pasport" rows="7" cols="30"  class="input" >'.$_REQUEST['pasport'].'</textarea>
         <span class="text">Комментарий</span><br>
         <textarea  name="comment" rows="7" cols="30"  class="input">'.$_REQUEST['comment'].'</textarea>
         </td>';
         $photo = $_REQUEST['photoname'] == '' ? 'none.jpg' : $_REQUEST['photoname'];
         $BODY.='<td><img id="ph" src="foto/'.$photo.'" width="300" height="400" alt="фотография">
                    <nobr>
                    <input type="hidden" name="photo" value="">
                    <input id="phn" type="hidden" name="photoname" value="'.$_REQUEST['photoname'].'">
                    <input id="filename" type="text" style="display:none">
                    <input id="fileSelect" class="sbutton" type="button" value="выбрать">
                 </nobr>
                 </td>';

  $BODY.='</tr>';
  $BODY.='<tr>';
 // $BODY.='<td colspan="2" align="left">
 //         '.$advancedparam.'
 //         </td>';
  $BODY.='</tr>';
  $BODY.='<tr class="tablehead">';
         $BODY.='<td colspan="2" align="right">
                <input class="sbutton" type="button" name="cancel" value="сохранить" onClick=Save(document.newvisitor) >
                <input class="sbutton" type="button" name="cancel" value="отмена" onclick=document.location.href="visitors.php?action=show">
                </td>';
  $BODY.='<tr>';

  $BODY.='</table>';
  $BODY.='</form>';
  //загрузка ФОТО
   $BODY.=' <form action="upload.php" method="post" target="hiddenframe" enctype="multipart/form-data" onsubmit="hideBtn();">
                <input type="file" id="userfile" name="userfile" accept="image/*" style="display:none" onchange="handleFiles(this.files)">
                <input type="submit" name="upload" id="upload" value="Загрузить" style="display:none">
            </form>
            <div id="res"></div>
            <iframe id="hiddenframe" name="hiddenframe" style="width:0px; height:0px; border:0px"></iframe>';

   $BODY .= '<script type="text/javascript">
         window.URL = window.URL || window.webkitURL;

            var fileSelect = document.getElementById("fileSelect"),
                userfile = document.getElementById("userfile"),
                photo = document.getElementById("ph");

            fileSelect.addEventListener("click", function (e) {
              if (userfile) {
                userfile.click();
              }
              e.preventDefault(); // prevent navigation to "#"
            }, false);

            function handleFiles(file) {
              if (!file.length) {
                photo.innerHTML = "<p>No file selected!</p>";
              } else {
                   for (var i = 0; i < file.length; i++) {
			 photo.src = window.URL.createObjectURL(file[i]);
                         photo.onload = function(e) {
                         window.URL.revokeObjectURL(this.src);
			 $(document).ready(function(){
                            var wi =  $("#ph").width();
                            $("#ph").width(wi+1);
                            $("#upload").click();
                        }); 
			  }
                  }
              }
            }
            </script>';
        
   $BODY .= '<script type="text/javascript">	
	
		function hideBtn(){
			//$("#upload").hide();
			//$("#res").html("Идет загрузка файла");
		}
		
		function handleResponse(mes) {
			//$("#upload").show();
		    if (mes.errors != null) {
		    	$("#res").html("Возникли ошибки во время загрузки файла: " + mes.errors);
		    }	
		    else {
		    	//$("#res").html("Файл " + mes.name + " загружен");
                        $("#filename").val(mes.name);
                        
		    }	
		}
	</script>';

}
$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
if($_REQUEST['action']=='save' && isset($_REQUEST['visitor_id']) && is_numeric($_REQUEST['visitor_id']) && $_REQUEST['visitor_id']>0)
{
  $error = '';
  if(!isset($_REQUEST['family']))$error.='<li>Не введена фамилия</li>';
  if(!isset($_REQUEST['fname']))$error.='<li>Не введено имя</li>';
  if(!isset($_REQUEST['secname']))$error.='<li>Не введено отчество</li>';
  if(!isset($_REQUEST['position']))$_REQUEST['position']='';
  if(!isset($_REQUEST['pasport']))$_REQUEST['pasport']='';
  if(!isset($_REQUEST['comment']))$_REQUEST['comment']='';
  if(!isset($_REQUEST['photoname']))$_REQUEST['photoname']='';

  if($error!='')
  {
     $text = '<ul>'.$error.'</ul>';
     echo ShowErrorWindow($text,'visitors.php?action=show');exit();
  }
      $q.='select VISIT_W_U_VISITORS('.$_REQUEST['visitor_id'].',
                             \''.CheckString($_REQUEST['family']).'\',
                             \''.CheckString($_REQUEST['fname']).'\',
                             \''.CheckString($_REQUEST['secname']).'\',
                             \''.CheckString($_REQUEST['position']).'\',
                             \''.CheckString($_REQUEST['pasport']).'\',
                             \''.CheckString($_REQUEST['comment']).'\',
                             \''.CheckString($_REQUEST['photoname']).'\')';
   //echo $q;
   @pg_query($q);
/*
  if(isset($_FILES['fhoto']))
  {
   $fhoto_tmp=$_FILES["fhoto"]["tmp_name"];// - Имя временного файла
   $fhoto_name=$_FILES["fhoto"]["name"];// - Имя файла на компьютере пользователя
   $fhoto_size=$_FILES["fhoto"]["size"];// - Размер файла в байтах
   $fhoto_type=$_FILES["fhoto"]["type"]; //- MIME-тип файла
   $fhoto_error=$_FILES["fhoto"]["error"];// - код ошибки.

    if($fhoto_error==0)
    {
           $uploadfhotodir=$_SERVER["DOCUMENT_ROOT"].'/foto/'; //директория загрузки фотографий
            if(!move_uploaded_file($fhoto_tmp,$uploadfhotodir.$fhoto_name))
             {
              echo ShowErrorWindow('Ошибка при загрузки фотографии','visitors.php?action=show');
              exit();
            }
    }
  }*/
echo ShowConfirmWindow('Подтверждение.','Изминения сохранены успешно','visitors.php?action=show');
}


if($_REQUEST['action']=='add')
{
  $error = '';
  if(!isset($_REQUEST['family']))$error.='<li>Не введена фамилия</li>';
  if(!isset($_REQUEST['fname']))$error.='<li>Не введено имя</li>';
  if(!isset($_REQUEST['secname']))$error.='<li>Не введено отчество</li>';
  if(!isset($_REQUEST['position']))$_REQUEST['position']='';
  if(!isset($_REQUEST['pasport']))$_REQUEST['pasport']='';
  if(!isset($_REQUEST['comment']))$_REQUEST['comment']='';
  if(!isset($_REQUEST['photoname']))$_REQUEST['photoname']='';

  if($error!='')
  {
     $text = '<ul>'.$error.'</ul>';
     echo ShowErrorWindow($text,'visitors.php?action=show');exit();
  }
  $q.='select VISIT_W_I_VISITORS('.$_SESSION['iduser'].',
                             \''.CheckString($_REQUEST['family']).'\',
                             \''.CheckString($_REQUEST['fname']).'\',
                             \''.CheckString($_REQUEST['secname']).'\',
                             \''.CheckString($_REQUEST['position']).'\',
                             \''.CheckString($_REQUEST['pasport']).'\',
                             \''.CheckString($_REQUEST['comment']).'\',
                             \''.CheckString($_REQUEST['photoname']).'\')';
  // echo $q;
pg_query($q);
 /*
 if(isset($_FILES['fhoto']))
  {
   $fhoto_tmp=$_FILES["fhoto"]["tmp_name"];// - Имя временного файла
   $fhoto_name=$_FILES["fhoto"]["name"];// - Имя файла на компьютере пользователя
   $fhoto_size=$_FILES["fhoto"]["size"];// - Размер файла в байтах
   $fhoto_type=$_FILES["fhoto"]["type"]; //- MIME-тип файла
   $fhoto_error=$_FILES["fhoto"]["error"];// - код ошибки.

    if($fhoto_error==0)
    {
           $uploadfhotodir=$_SERVER["DOCUMENT_ROOT"].'/foto/'; //директория загрузки фотографий
            if(!move_uploaded_file($fhoto_tmp,$uploadfhotodir.$fhoto_name))
            {
              echo ShowErrorWindow('Ошибка при загрузки фотографии','visitors.php?action=show');
              exit();
            }
    }
  }
*/

echo ShowConfirmWindow('Подтверждение.','Новый посетитель '.$_REQUEST['family'].' '.$_REQUEST['fname'].' '.$_REQUEST['secname'].' добавлен','visitors.php?action=show');
}

if($_REQUEST['action']=='del' && isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid']) && $_REQUEST['uid']>0)
{
     $q.='select VISIT_W_D_VISITORS('.$_REQUEST['uid'].')';
    
     pg_query($q);
     echo ShowConfirmWindow('Подтверждение.','Посетитель удалён','visitors.php?action=show');
    

}



echo $BODY;
?>