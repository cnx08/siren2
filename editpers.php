<?php
//добавляем новую загрузку фото

 ob_start();

$IDMODUL=2;
include("include/input.php");

require("include/head.php");
require("include/common.php");

if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   echo PrintHead('Персонал','');
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
//проверяем на недопустимые действия

//смотрим не было ли ошибок
if(isset($_REQUEST['error']))
{
 echo ShowErrorWindow($text);
}

if($_REQUEST['action']=='edit')
{
  echo PrintHead('Персонал','Редактирование Сотрудника');
}


/*
	создадим окошко подтверждения - до ZoneFlag 
*/ 
$BODY .= '<div id="confirm_window" class="confirmwnd" style="background-color:gray; top: 200px; left:100px; height: 100px; width: 200px; display: none;">
<form method="GET" action="">	
<input type="button" onclick="personal.php?action=plink">
</form>
</div>';



//ПУть к папке с фотографиями
$PATHPHOTO='/foto/';
$fhoto='';
$foto_isx = ''; 


require("include/menu.php");

$r = pg_fetch_array(pg_query('select value from base_const where name = \'base_personal_ext\''));
$ext = $r['value']; 

// пришол запрос на редактирование
if($_REQUEST['action']=='edit')
{
  if(!isset($_REQUEST['id']) || IdValidate($_REQUEST['id'])==false)
  {
    echo ShowErrorWindow('Не удалось распознать сотрудника','personal.php?action=showall');
    exit();
  }

  $q='select * from BASE_W_S_PERSONAL_ONCE('.$_REQUEST['id'].')';
  $res=pg_fetch_array(pg_query($q));

  if ($ext == 'int')$_REQUEST['ext_int']=$res['ext_int'];
  if ($ext == 'text')$_REQUEST['ext_text']=$res['ext_text'];
  $_REQUEST['tabnum']=$res['tabel_num'];
  $_REQUEST['date_in']=$res['date_in'];
  $_REQUEST['date_out']=$res['date_out'];
  $_REQUEST['family']=$res['family'];
  $_REQUEST['fname']=$res['name'];
  $_REQUEST['secname']=$res['secname'];
  $_REQUEST['position']=$res['pos'];
  $_REQUEST['id_dept']=$res['id_dept'];
  $_REQUEST['graph_name']=$res['id_graph'];
  $_REQUEST['graph_offset']=$res['graph_offset'];
  $_REQUEST['id_algoritm']=$res['id_work_type'];
  $_REQUEST['id_zone']=$res['id_zone'];
  $_REQUEST['pxcode_id']=$res['pxcode_num'];
  
  if(isset($_REQUEST['foto_prev']))
  {
	$_REQUEST['photoname']=$_REQUEST['foto_prev'];
  }
  else 
  {
	$_REQUEST['photoname']=$res['photo'];			/////  это при открытии формы 
  }
  
  $_REQUEST['p_id_zone']= $res['p_id_zone'];
  $fhoto=$PATHPHOTO.$_REQUEST['photoname'];
  $fhoto=str_replace("\\","/",$PATHPHOTO.$_REQUEST['photoname']);	
    	
  $foto_isx = $res['photo']; 
  $_REQUEST['pxcodenum']=$res['code'];
  $_REQUEST['pxcodenum_old']=$res['code'];
  $_REQUEST['pxdatein']=$res['c_date_in'];
  $_REQUEST['pxdateout']=$res['c_date_out'];
  $_REQUEST['pincod']=$res['pin'];
  $_REQUEST['status']=$res['status'];
  $_REQUEST['comment']=$res['comment'];
  $_REQUEST['id_dopusk']=$res['id_dopusk'];
  $_REQUEST['p_id_dopusk']=$res['p_id_dopusk'];
  $_REQUEST['breakfast'] = $res['breakfast'];
  $_REQUEST['din'] = $res['din'];
  $_REQUEST['supper'] = $res['supper'];
}

/******************************************************************************/

$BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
   $BODY .= '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';
    $BODY .= '<script type="text/javascript">
        $(function () {
            $("#date_in").pickmeup({
                change : function (val) {
                    $("#date_in").val(val).pickmeup("hide")
                }
            });
            $("#date_out").pickmeup({
                change : function (val) {
                    $("#date_out").val(val).pickmeup("hide")
                }
            });
            $("#pxdatein").pickmeup({
                change : function (val) {
                    $("#pxdatein").val(val).pickmeup("hide")
                }
            });
            $("#pxdateout").pickmeup({
                change : function (val) {
                    $("#pxdateout").val(val).pickmeup("hide")
                }
            });
            $("#gdate").pickmeup({
                change : function (val) {
                    $("#gdate").val(val).pickmeup("hide")
                },
                before_show : function (val) {
                    $(".pickmeup").css("z-index","55");
                }
            });
        });
   function ZoneFlag(id,sid)
   {
     var f = document.getElementById(id);
     var s = document.getElementById(sid);
     if( f.checked == true)
         s.disabled = true;
     else
        s.disabled = false;
   }
  function getGraphData()
 {
            //alert(this.req.responseText);
            var xdoc = this.req.responseXML.documentElement;
            var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
            table+="<tr  class=client>";
            table+="<td align=center bgcolor=silver>№<\/td>";
            table+="<td align=center bgcolor=silver>Смена<\/td>";
            table+="<td align=center bgcolor=silver>Допуск<\/td>";
            table+="<td align=center bgcolor=silver>Зона<\/td>";
            table+="<\/tr>";

            var res = xdoc.getElementsByTagName("item");
            if(res)
              {
               var i=0;
               for(i=0;i<res.length;i++)
               {
                 var bgcolor = res[i].getAttributeNode("bg").value;
                    // if(bgcolor == "silver")
                                table+="<tr bgcolor="+bgcolor+" class=clientText>";
                        // else
                          //  table+="<tr class=clientText>";

                 table+="<td align=center>"+res[i].getAttributeNode("num").value+"<\/td>";
                 table+="<td align=center>"+res[i].getAttributeNode("name").value+"<\/td>";
                 table+="<td align=center>"+res[i].getAttributeNode("dopusk").value+"<\/td>";
                 table+="<td align=center>"+res[i].getAttributeNode("zone").value+"<\/td>";

                 table+="<\/tr>";
               }
               if(i==0) table+="<tr class=client><td>Данному графику ничего не назначено<\/td><\/tr>";

              }
            table+="<\/table>";
                 this.object.wnd.client.innerHTML+=table;
                 this.object = null;
         }

         function showGraphInfo(obj,event)
         {
          var id = obj.options[obj.selectedIndex].value;
          var gname = obj.options[obj.selectedIndex].text;
          //alert(gname);
          if(id > 0)
          {
           var e = event || window.event;
           var infoWnd = new Window.poupWindow("info_wnd",e.clientY,e.clientX,50,0,400,0,"window",gname.replace("-","\ "));

             infoWnd.Show();
            var net = new Net.ContentLoader("asinc.php",getGraphData,Error,"POST","obj=graph&id="+id+"&persId='.$_REQUEST['id'].'");
            net.object = infoWnd;
          }

         }
         
   </script>';

   
// --------------   
   
$BODY .='<script type="text/javascript">
	function WndReload(url_str)
	{ 
		document.location = url_str; 
	}
</script>
';
$BODY.='<div id="mask" style="position: absolute;
                    background-color:white;
                    height:93%;
                    width: 100%;
                    opacity:0.7;
                    top:50px;
                    z-index:-1">
        </div>';

	$BODY.='<form name="aepers" action="save_pers_data.php?act=update" method="POST">';
	$BODY.='<input  type="hidden" name="id_pers" value="'.$_REQUEST['id'].'">';
	$BODY.='<input type="hidden" name="photo" value="">';
	$BODY.='<input id="phn" type="hidden" name="photoname" value="'.$_REQUEST['photoname'].'">';
   
// -----    ПОЛЯ ФОРМЫ - ЗДЕСЬ 
   $BODY.='<table border="0" cellpadding="1" cellspacing="1" bgcolor="#f5f5dc" align="center" class="dtab">';
   $BODY.='<tr>';
   $BODY.='<td>
   <span class="text">Табельный номер</span></td>
   <td><input type="text" name="tabnum" value="'.$_REQUEST['tabnum'].'" size=20 class="input"></td>';
   
   $BODY.='<td rowspan="16" width="40%" align="center" style="border: 1px;"><img id="ph" src="'.$fhoto.'" 
			alt="фотография сотрудника" width="100%" height="100%"></td>';
   
   $BODY.='</tr>';
   if ($ext !=''){
    $BODY.='<tr>';
    $BODY.='<td><span class="text">Доп. поле</span></td>';
     if ($ext == 'int') $BODY.='<td><input type="text" name="ext_int" value="'.$_REQUEST['ext_int'].'" size="20" class="input" ></td>';
     if ($ext == 'text') $BODY.='<td><input type="text" name="ext_text" value="'.$_REQUEST['ext_text'].'" size="20" class="input" ></td>';
    $BODY.='</tr>';
   }
   
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Фамилия</span></td>
   <td><input type="text" name="family" value="'.$_REQUEST['family'].'" size="20" class="input" ></td>';
   $BODY.='</tr>';
   
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Имя</span></td>
   <td><input type="text" name="fname" value="'.$_REQUEST['fname'].'" size="20" class="input"></td>';
   $BODY.='</tr>';
   
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Отчество</span></td>
   <td><input type="text" name="secname" value="'.$_REQUEST['secname'].'" size="20" class="input"></td>';
   $BODY.='</tr>';
   
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Фотография</span></td>';

   $BODY.='<td><input type="text" id="filename" style="display:none">'; 
   $BODY.='<input type="button" id="fileSelect" value="выбрать" class="sbutton">';
   $BODY.='</td>';
   $BODY.='</tr>';
   
   
   
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Должность</span></td>
   <td><input type="text" name="position" value="'.$_REQUEST['position'].'"size="20" class="input"></td>';
   $BODY.='</tr>';

   $BODY.='<tr><td><span class="text">Отдел</span></td>';
   $BODY.='<td>';
   $browseDeptUrl = 'objectviewer.php?object=departments&elIdDept=aepers.id_dept&elDeptName=aepers.deptName';
   
   $res=pg_query('select id,name from base_dept where id ='.$_REQUEST['id_dept']);
   $r=pg_fetch_array($res);
     
   $BODY.= '<input type="text" id ="deptName" name="deptName" value="'.str_replace('"',"",$r['name']).'" size="20" class="input">&nbsp;';
   $BODY.= '<input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseDeptUrl.'\',\'\',\'width=420,height=475\')">&nbsp;'; 
   $BODY.= '<input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'deptName\');aepers.id_dept.value=0">';
   $BODY.= '<input type="button" value="создать" class="sbutton" onclick=\'$("#adddept").show();\'>';
   $BODY.= '<input type="hidden" id ="id_dept" name="id_dept" value="'.$r['id'].'"></td>';
   $BODY.='</td>';
   
   $BODY.='</tr>';
   
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Дата приёма на работу</span></td><td>
             <input type="text" id ="date_in" name="date_in" value="'.$_REQUEST['date_in'].'" size="20" readonly class="input" />
             
             </td>';
   $BODY.='</tr>';
   
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Дата увольнения</span></td>
           <td>
             <input id="date_out" type="text" name="date_out" value="'.$_REQUEST['date_out'].'" size="20" readonly class="input" />
             
             <input type="button" value="очистить" class="sbutton" onclick=\'clearField("date_out")\' />
             </td>';
   $BODY.='</tr>';
   $BODY.='<tr>
           <td><span class="text">График работы</span></td>
           <td><select id="graph" name="graph" style="Z-INDEX:0; width:163px;" class="select"><option value="0">не назначен</option>';

    $res=pg_query("select * from BASE_W_S_GRAPH_NAME(NULL)");

     while($r=pg_fetch_array($res))
     {
       if($_REQUEST['action']=='edit' && $_REQUEST['graph_name']==$r['id'] )$selected=" selected";
       else $selected="";
       $BODY.='<option value="'.$r['id'].'"'.$selected.'>'.$r['name'].'</option>';
     }		   
   $BODY.= '</select>
                <input type="button" value="просмотреть" name="showgraph" class="sbutton" onclick=\'showGraphInfo(document.aepers.graph,event,'.$_REQUEST['graph_name'].')\' />
                <input type="button" value="создать" class="sbutton" onclick=\'showAddGraphWind();\'>
                <input type="hidden" name="graph_name" value="'.$_REQUEST['graph_name'].'" />&nbsp;
           </td>';
   $BODY.='</tr>';
  
   $BODY.='<tr><td><p class="text">Смещение в графике:</p></td><td><input type="text" name="graph_offset" value="'.$_REQUEST['graph_offset'].'" size="2" class="input"></td></tr>';

   $disable = '';
   $checked = '';
   if($_REQUEST['p_id_zone'] == -1)
   {
      $disable = 'disabled="disabled"';
      $checked = 'checked';
   }
   else
   {
      $disable = 'enabled';
      $checked = '';
   }
   if($_REQUEST['action'] == 'add')
   {
           $checked = 'checked';
           $disable = 'disabled="disabled"';
   }
   $BODY.='<tr>
           <td>
           <span class="text">Рабочая зона</span></td>
           <td><select id="zoneselect" name="zone" class="input" style="width:163px;"'.$disable.'><option value="0">не назначена</option>';

     $res=pg_query("select * from pr_get_zone(NULL)");
     $selected = '';
     while($r=pg_fetch_array($res))
     {
       if($_REQUEST['action']=='edit' && $_REQUEST['p_id_zone']==$r['id'] )$selected=" selected";
       else $selected="";
       $BODY.='<option value="'.$r['id'].'"'.$selected.'>'.$r['name'].'</option>';
     }

   $BODY.= '</select>';
   $BODY.='<input type="hidden" name="id_zone" value="'.$_REQUEST['id_zone'].'">
   <input id="p_id_zone" type="checkbox" name="p_id_zone" '.$checked.' onclick = \'ZoneFlag("p_id_zone","zoneselect")\' /><span class="text">Брать из графика</span>

        </td>
    </tr>
    <tr>';

   $checked = '';
   $disable = '';
   if($_REQUEST['p_id_dopusk'] == -1)
   {
      $disable = 'disabled="disabled"';
      $checked = 'checked';
   }
   else
   {
      $disable = 'enabled';
      $checked = '';
   }
   if($_REQUEST['action'] == 'add')
   {
           $checked = 'checked';
           $disable = 'disabled="disabled"';
   }

  $BODY .= '<td><span class="text">Допуск</span></td>';
   $BODY .= '<td>';
   $BODY .='<select id="dopuskselect" name="dopusk" class="input" style="width:163px;"'.$disable.'><option value="0">не назначен</option>';

     $res=pg_query("select * from BASE_W_S_DOPUSK(NULL)");
     $selected = '';
     while($r=pg_fetch_array($res))
     {
       if($_REQUEST['action']=='edit' && $_REQUEST['id_dopusk']==$r['id'] && $checked=='' )$selected=" selected";
       else $selected="";
       $BODY.='<option value="'.$r['id'].'"'.$selected.'>'.$r['name'].'</option>';
     }

   $BODY.= '</select>';
   $BODY.= '<input type="hidden" name="id_dopusk" value="'.$_REQUEST['id_dopusk'].'">
   <input id="p_id_dopusk" type="checkbox" name="p_id_dopusk" '.$checked.' onclick=\'ZoneFlag("p_id_dopusk","dopuskselect")\' /><span class="text">Брать из графика</span>';
   
   $BODY .= '</td>';
   $BODY .= '</tr>';

   
   ////// тип расчета наработки 
   $BODY.='<tr>';
          $BODY.='<td><span class="text">Тип расчёта наработки</span></td>';
          $BODY.='<td>
                  <select name="algoritm" style="Z-INDEX:0;" class="select">';
                  $q = 'select * from BASE_W_S_WORK_TYPE(NULL)';
                  $res = pg_query($q);
                  $selected = '';
                  while($r = pg_fetch_array($res))
                  {
                    if($_REQUEST['action']=='edit' && $_REQUEST['id_algoritm']==$r['id'] )$selected=" selected";
                    else $selected="";
                    $BODY.='<option value="'.$r['id'].'"'.$selected.'>'.$r['name'].'</option>';
                  }
          $BODY.='</select><input type="hidden" name="id_algoritm" value="'.$_REQUEST['id_algoritm'].'">
                 </td>';
   $BODY.='</tr>';
   
   

   //Питание
   $break_fast = '';
   $din        = '';
   $supper     = '';

   if($_REQUEST['breakfast'] == 1)$break_fast = 'checked';
   if($_REQUEST['din'] == 1)$din = 'checked';
   if($_REQUEST['supper'] == 1)$supper = 'checked';
   $BODY .= '<tr>';
        $BODY .= '<td>';
              $BODY.= '<span class="text">Питание:</span><br>';
              $BODY .= '<input type="checkbox" name="breakfast" '.$break_fast.'><span class="text">Завтрак</span><br>';
              $BODY .= '<input type="checkbox" name="din" '.$din.'><span class="text">Обед</span><br>';
              $BODY .= '<input type="checkbox" name="supper" '.$supper.'><span class="text">Ужин</span>';
        $BODY .= '</td><td>&nbsp;</td>';
   $BODY .= '</tr>';

//// ВНИМАНИЕ!! Длина кода пропуска железно забита в 16 символов!!
   $BODY.='<tr>';
   $BODY.='<td><span class="text">Код пропуска</span></td>
   <td><input type="text" name="pxcodenum" value="'.$_REQUEST['pxcodenum'].'" size=20 class="input" maxlength="16">
       <input type="button" name="showcodepr" value="+" onClick=\'ShowClosePxCode(this)\' style="width:18px;" class="sbutton" />
       <input type="button" name="showpxcode" value="выбрать" onClick=\'ShowPxCodeWindow("pers")\' class="sbutton" />
       <input type="hidden" name="pxcode_id"  id="pxcode_id" value="'.$_REQUEST['pxcode_id'].'" />
       <input type="hidden" name="pxcodenum_old" value="'.$_REQUEST['pxcodenum_old'].'" />
   </td>';
   $BODY.='</tr>';
   //Реквизиты пропуска
   $BODY.='<tr><td colspan="2">';
     $BODY.='<div id="codewind" class="modalwindow" style="border:none">';

     $BODY.='<table border="0" style="width:100%; height:100%;"  cellpadding="2" cellspacing="0" class="dtab">';
     $BODY.='<tr><td width="38%"><span class="text">Дата ввода<br> в эксплуатацию</span></td>
                     <td><input type="text" id="pxdatein" name="pxdatein" value="'.$_REQUEST['pxdatein'].'" size="20"  class="input" readonly>
                     
                     </td>';
     $BODY.='</tr>';
     $BODY.='<tr><td><span class="text">Дата вывода<br>из эксплуатации</span></td>
                 <td><input type="text" id="pxdateout" name="pxdateout" value="'.$_REQUEST['pxdateout'].'"  class="input" readonly>
                 
                 </td>';
     $BODY.='</tr>';
     $BODY.='<tr><td><span class="text">Pin код</span></td>
                 <td><input type="text" name="pincod" value="'.$_REQUEST['pincod'].'" size="20"  class="input" ></td>
            </tr>
            <tr><td><span class="text">Коментарий</span></td>
                <td><input type="text" name="comment" size="20" value="'.$_REQUEST['comment'].'"  class="input"></td>
            </tr>';

     $BODY.='<tr><td colspan="2"><span class="text">Статус пропуска</span>
                 <input type="hidden" name="status" size="20" value="'.$_REQUEST['status'].'" ></td></tr>
             ';

     $chk='';
     if(GetCodeValue($_REQUEST['status'],0)==1)$chk='checked';else $chk='';
        $BODY.='<tr><td ><input type="checkbox" name="pxblock" '.$chk.'><font face="Verdana" size="1"><b>Блокировать</b></font></td>';
     if(GetCodeValue($_REQUEST['status'],1)==1)$chk='checked';else $chk='';
        $BODY.='<td ><input type="checkbox" name="pxguest" '.$chk.'><font face="Verdana" size="1"><b>Гостевой</b></font></td>';

     if(GetCodeValue($_REQUEST['status'],2)==1)$chk='checked';else $chk='';
     $BODY.='<tr><td ><input type="checkbox" name="pxadmin" '.$chk.'><font face="Verdana" size="1"><b>Администратор</b></font></td>';
     //if(GetCodeValue($_REQUEST['status'],4)==1)$chk='checked';else $chk='';
     $BODY.='<td colspan="2"><input type="checkbox" name="pxauto"  style="display:none"><font face="Verdana" size="1" style="display:none"><b>Автомобильный</b></font></td></tr>';

     if(GetCodeValue($_REQUEST['status'],3)==1)$chk='checked';else $chk='';
     $BODY.='<tr><td colspan="2"><input type="checkbox" name="pxdouble" '.$chk.'><font face="Verdana" size="1"><b>Контроль двойных засечек</b></font></td>';

     $BODY.='</tr>';
     $BODY.='</table>';
     $BODY.='</div>';

   $BODY.='</td>';
   $BODY.='</tr>';

  
   $BODY.='<tr bgcolor="gray">';
   $BODY.='<td><input type="button" value="сохранить" name="savebt" onClick=\'AddEdPers(document.aepers,"update")\' class="sbutton" /></td>';

   $BODY.='<td align="center"><input type="button" value="назад" onclick=\'document.location.href="personal.php?action=showall&amp;plink"\' class="sbutton" /></td>';
 
	$BODY .= '<td>&nbsp;</td>';
   $BODY.'</tr>';

   $BODY.='</table>';
   $BODY.='</form>';
   
      /////////////////////добавление нового отдела
   $BODY .= '<script type="text/javascript" src="../gscripts/addedpers.js"></script>';
   
   $BODY.='<div id="adddept"  style="display:none; position:absolute; top:50px; left:65px;z-index:50; border: 1px solid gray;">
            <form id="adddeptfrm" name="adddeptfrm" action="" method="POST">
                <table border="0" width="450"class="dtabturn" cellspacing="0" cellpadding="0">
                    <tr class="tablehead">
                        <td align="left" >Новый отдел</td>
                        <td align="right"><img src="buttons/crossline.gif" style="text-align: right;" class="icons" onclick=\'$("#adddept").hide();\' /></td>
                    </tr>
                    <tr>
                        <td><span class="text" >Название</span></td>
                        <td><span class="text"><input name="namedept" type="text" value="" size="35" maxlength="32" class="input"></span></td>
                    </tr>
                    <tr>
                        <td><span class="text">Входит в</span></td>
                        <td>
                        <select name="in_dept"><option value="0">Нет</option>';
                            $result=pg_query('select * from BASE_W_S_DEPT_SIMPLE()');
                            while($r = pg_fetch_array($result))
                            {
                               $BODY .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
                            }
   $BODY .=             '</select></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="statusbar" style="display:none;margin:2px;border-top:1px solid midnightblue;width:100%;padding:2px;font-family:Verdana;font-size:8pt; color:black;" >
                                 <img src="buttons/indicator.gif" width="20" height="20" style="margin-right:10px;">
                                 <span class="text">&nbsp;&nbsp;Обработка...</span>
                            </div>
                        </td>
                    </tr>
                    <tr class="tablehead">
                        <td colspan="1" align="left"><input name="addturnbut" type="button" class="sbutton" value="сохранить"  onclick=\'AddDept(document.adddeptfrm)\' /></td>
                        <td align="right"><input type="button" class="sbutton" value="отмена" onclick=\'$("#adddept").hide();\' /></td>
                    </tr>
            </table>
            </form>
         </div>';
   /////////////////////
   
   //
   
    /////////////////////добавление нового графика

   $BODY.='<div id="addgraph"  style="display:none; position:absolute; top:50px; left:65px;z-index:50; border: 1px solid gray;">
            <form id="addgraphfrm" name="addgraphfrm" action="asinc.php?obj=addPers&act=save" method="POST">
                <table border="0" cellpadding="1" cellspacing="0" class="dtab" width="100%" align="center">
                    <tr class="tablehead" >
                        <td align="left" colspan="2">Новый график</td>
                        <td align="right"><img src="buttons/crossline.gif" style="text-align: right;" class="icons" onclick=\'closeAddGraphWind();\' /></td>
                    </tr>
                    <tr>
                        <td align=left colspan="3"><span class="text">Название</span>
                             <input id="gname" type="text" name="gname" value="" class="input" />
                             <span class="text">&nbsp;Дата введения</span>
                             <input id="gdate" type="text" name="gdate" value="'.date("d.m.Y").'" class="input" readonly />&nbsp;
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div id="main" style="height:100%;">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="left"><span class="text">Добавить</span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text">Смена:&nbsp;</span>
                            <select id="smadd" name="smadd" class="select"></select>
                        </td>
                        <td>
                            <span class="text">&nbsp;Доступ:&nbsp;</span>
                            <select id="dopadd" name="dopadd" class="select" ></select>
                       </td>
                       <td>
                            <span class="text">&nbsp;Зона:&nbsp;</span>
                            <select id="zoadd" name="zoadd" class="select"></select>

                           <input type="button" value="+" class="delbut" style="height:20px;width:20px;margin-bottom:2px;" onClick=\'AddItem(document.addgraphfrm)\' />
                       </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <input type="button" value="создать" class="sbutton" onclick=\'showAddSmenaWind();\'>
                       </td>
                       <td align="center">
                            <input type="button" value="создать" class="sbutton" onclick=\'showAddDopuskWind();\'>
                       </td>
                       <td align="center">
                            <input type="button" value="создать" class="sbutton" onclick=\'showAddZoneWind();\'>
                       </td>
                    </tr>
                    <tr>
                        <td  valign=top>
                            <span class=text>Описание</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" valign=top>
                            <textarea id="descript" name="descript" rows="5" cols="40" class="input"></textarea>
                        </td>
                    </tr>
                    <tr class="tablehead">
                        <td align="right" colspan="3">
                            <input type="button" value="сохранить" class="sbutton" onclick=\'Save(document.addgraphfrm)\' />
                            <input type="button" value="отмена" class="sbutton" onclick=\'closeAddGraphWind();\' />
                            <input id="count" name="count" type="hidden" value="0" size="10">
                        </td>
                    </tr>
                 </table>

             </form>
        </div>';
   

   /////////////////////
   //
   /////////////////////добавление новой смены   
  $BODY.='<div id="addsm" style="display:none;position:absolute;top:200px;left:400px;z-index:65;">
            <form id="addsmena" name="addsmena" action="asinc.php?obj=addSmena" method="POST">
                <input type="hidden" name="id_smena" value="">
                <table border="0"  width="300"class="dtab" cellspacing="0" cellpadding="0">
                    <tr class="tablehead">
                        <td align="center" colspan="2">Cмена</td>
                    </tr>
                    <tr>
                        <td><p class="text">Название</td>
                        <td><input type="text" id="namesm" value="" maxlength="50" class="input"></td>
                    </tr>
                    <tr>
                        <td><p class="text" align="left">Смена &nbsp;&nbsp;с:</td>
                        <td><input type="text" id="start_sm" value="00:00" size="10" maxlength="5" class="input"></td>
                    </tr>
                    <tr>
                        <td><p class="text" align="right">по:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><input type="text" id="end_sm" value="00:00" size="10" maxlength="5" class="input"></td>
                    </tr>
                    <tr>
                        <td><p class="text" align="left">Обед &nbsp;&nbsp;&nbsp;с:</td>
                        <td><input type="text" id="start_din" value="00:00" size="10" maxlength="5" class="input"></td>
                    </tr>
                    <tr>
                        <td><p class="text" align="right">по:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><input type="text" id="end_din" value="00:00" size="10" maxlength="5" class="input"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><p class="text">Описание</td></tr>
                    <tr>
                        <td colspan="2">
                            <textarea id="descrip" rows="10" cols="35" class="input">&nbsp;</textarea>
                        </td>
                    </tr>
                    <tr bgcolor="gray">
                        <td align="left"><input type="button" name="save" onclick=\'AddSmena(document.addsmena)\' value="добавить" class="sbutton" /></td>
                        <td align="right" ><input type="button" name="cancel" onclick=\'closeAddSmenaWind()\' value="отмена" class="sbutton" /></td>
                    </tr>
                </table>
            </form>
         </div>';
  /////////////////////
   //
  /////////////////////добавление новой зоны
   $BODY.='<div id="addzonediv" style="display:none;position:absolute;top:150px;left:300px;z-index:65">
                <form name="addzone" action="" method="POST">
                    <table border="0" width="310"class="dtab" cellspacing="0" cellpadding="0">
                        <tr class="tablehead">
                            <td align="center" colspan="2">Создание новой рабочей зоны</td>
                        </tr>
                        <tr>
                            <td><p class="text" >Название</p></td>
                            <td><p class="text"><input id = "namezone" name="namezone" type="text" value="" size="20" maxlength="32" class="input"></p></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><p class="text" >Описание</p></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><textarea id = "descrzone" name="descrip" rows="5" cols="35" class="input" style="width:90%"></textarea>
                        </tr>
                        <tr>
                            <td><p class="text" >Территории</p></td>
                            <td>
                                <select id="terr" name="ter" class="select"></select>
                                <input  type="button" value="+" onclick=\'AddTerr(document.addzone)\' class="sbutton" />
                                <input type="hidden" name="terr_id" value="">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><div id="tlist" style="width:100%;" ></div>                      
                                <tr bgcolor="gray">
                                    <td align="left"><input type="button" name="add"  value="сохранить" onclick=\'AddZone(document.addzone)\' class="sbutton" /></td>
                                    <td align="right" ><input type="button" name="cancel" onclick=\'closeAddZoneWind()\' value="отмена" class="sbutton" /></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input id = "selstr" type="hidden" name="selstr"></td>
                                </tr>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>';
   /////////////////////
  //
  //
///////////////////////добавление нового допуска
$BODY.='<div id="adddopusk" style="display:none;position:absolute;top:150px;left:50px;z-index:65">
            <form name="frm_adddopusk" action="" method="POST">
                <table border="0" width=370 cellpadding="1" cellspacing="0" class="dtab">
                    <tr class="tablehead">
                        <td align="center" colspan="2">Создание нового допуска</td>
                    </tr>
                    <tr>
                        <td><p class="text">Название</p></td>
                        <td><input id="namedop" type="text" value="" class="input" size="25"></td>
                    </tr>
                    <tr>
                        <td><p class="text">Контроль двойных засечек</p></td>
                        <td>
                            <span class="text">вход</span><input type="checkbox" id="incheck">
                            <span class="text">выход</span><input type="checkbox" id="outcheck"><br>
                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="tg_reg_list" style="height:100%;">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><p class="text">Группа турникетов</p></td>
                        <td><p class="text">Режим доступа</p></td>
                    </tr>
                    <tr>
                        <td>
                            <select id="tg_select" name="tg_select" class="select"></select>
                        </td>
                        <td nowrap>
                            <select id="reg_select" name="reg_select" class="select"></select>
                            <input  type="button" value="+" onclick=\'AddTgroupReg()\' class="sbutton" />
                        </td>
                    </tr>
                     <tr>
                        <td></td>
                        <td>
                            <input type="button" value="создать" class="sbutton" onclick=\'showAddRegWind();\'>
                         </td>
                    </tr>
                    <tr class="tablehead">
                        <td align="left"><input type="button" class="sbutton" value="cохранить" onclick=\'AddDopusk()\' /></td>
                        <td align="right"><input type="button" class="sbutton" value="отмена" onclick=\'closeAddDopuskWind()\' /></td>
                    </tr>
                </table>
            </form>
        </div>';
///////////////////////
//
//
//////добавление нового режима
$BODY.='<div id="addreg" style="display:none;position:absolute;top:150px;left:50px;z-index:70">
            <form name="frm_addreg" action="" method="POST">
                <table border="0" width=402 cellpadding="1" cellspacing="0" class="dtab">
                    <tr class="tablehead">
                        <td align="center" colspan="2">Создание нового режима</td>
                    </tr>
                    <tr>
                        <td><p class="text">Название</p></td>
                        <td><input id="namereg" type="text" value="" class="input" size="25"></td>
                    </tr>
                    <tr>
                        <td><p class="text">Промежуток времени:</p></td>
                        <td>
                            c
                            <input id="stime" type="text" size="4" maxlength="5" class="input" value="00:00">
                            по
                            <input id="ftime" type="text" size="4" maxlength="5" class="input" value="00:00">
                            <input type="button" value="ок"  class="sbutton" onClick=\'SetRegInterval()\' />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div id="reg_time"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="button" value="очистить"  class="sbutton" onClick=\'ClearRegPanel()\' />
                        </td>
                    </tr>
                    <tr class="tablehead">
                        <td align="left"><input type="button" class="sbutton" value="cохранить" onclick=\'AddReg()\' /></td>
                        <td align="right"><input type="button" class="sbutton" value="отмена" onclick=\'closeAddRegWind()\' /></td>
                    </tr>
                </table>
            </form>
        </div>';
   
   
   ///////////////////////
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
  
   
   echo $BODY;   
   
   
echo PrintFooter();

 ob_flush();
?>