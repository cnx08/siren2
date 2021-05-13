<?php
ob_start();

$IDMODUL=2;

include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
echo PrintHead('Персонал','Работа с персоналом');
//there is no action control)

if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
$ss = '
<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>
<script type="text/javascript">
function ShowDivPass(id){
    var pers_id = id.substr(5);
    $("#pers_id").val(pers_id);
    var elem = $("#"+id).offset();
    var top = elem.top;
    var left = elem.left;
    $("#pass_div").css("top",top-10);
    $("#pass_div").css("left",left-200);
    
    $("#pass_div").show();
}

$(document).keydown(function(e) {
    if( e.keyCode === 27 ) {
        $("#pass_div").hide();
        $("#pers_id").val("");
        $("#pass_code").val("");
        return false;
    }
});
function SetPass(){

    var pers_id =  $("#pers_id").val();
    var pxcode  =  $("#pass_code").val();
    
    if(pxcode.length===0){
        $("#pass_div").hide();
        $("#pers_id").val("");
        return;
    }
    if(pxcode.length!==16){
        alert("Код пропуска должен быть 16-ти значный!")
        return;
    }
    else{
        var data1 = {obj:"addPxcode", pers_id:pers_id, pxcode:pxcode};
            $.ajax({
                url: "asinc.php", 
                type: "POST",
                data: data1,
                dataType: "json",
                success: function (data) {
                    var dataObj = eval(data);
                    if (dataObj.res!="1"){
                        alert(dataObj.res);
                    }
                    else{
                        $("#pass_"+pers_id+" img").attr("src","buttons/pass_changed.gif");
                        $("#pass_"+pers_id+" img").attr("title","Пропуск сохранён");

                        $("#pass_"+pers_id).parents("tr").children("td").attr("bgcolor","#CAFFAF");
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert("error SetPass");
                
                }
             });
     
        $("#pass_div").hide();
        $("#pers_id").val("");
        $("#pass_code").val("");
    }
}
function check_number(event)
{

// массив допустимых символов 
var allowed = new Array(\'0\',\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\')

var cc = document.getElementById("findfrm");

var phone=cc.tab_num.value;
var length_phone=phone.length;

var e = event || window.event;
var ch = e.charCode || e.keyCode;


var code = e.charCode || e.keyCode; // Какая клавиша была нажата
// Если была нажата функциональная клавиша, не фильтровать ее
// if (e.charCode == 0) return true; // Функциональная клавиша (только Firefox)
//if (e.ctrlKey || e.altKey) return true; // Нажата Ctrl или Alt
 if (code < 32) return true; // Управляющий ASCII символ

// if (code = 8) return true; 

// преобразовать код символа code в сам символ - 
// это будет использоваться для сравнения
var c = String.fromCharCode(code);

// проверить, что принадлежит к числу допустимых 
if (allowed.indexOf(c) != -1) {
	// if (length_phone==0)
	// {
	// myform.phone.value="("+phone;
	// }
	// if (length_phone==4)
	// {
	// myform.phone.value=phone+")";
	// }
	// ничего не делаем  
	return true;
}
	

else 
{
	// нету в массиве допустимых символов 
	
	if (e.preventDefault) e.preventDefault();
	if (e.returnValue) e.returnValue = false;

	return false;
}
 return true;
}


</script>';

echo($ss);



IF (!isset($_SESSION['is_filtered'])) $_SESSION['is_filtered'] = false;

//подгружаем меню
require("include/menu.php");



$r = pg_fetch_array(pg_query('select value from base_const where name = \'base_personal_ext\''));
$ext = $r['value']; 

$action = ( isset( $_GET['action'] ) && $_GET['action'] != '' ) ?  $_GET['action'] : '';


//сгенерированы html код страницы
$view = '';
//$view .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$view .= '<script type="text/javascript"> function ResetSearchFormFields()
{ 
document.GetElementByName("search") = 0;
document.GetElementByName("family") = "";
document.GetElementByName("name") = "";
document.GetElementByName("secname") = "";
document.GetElementByName("position") = "";
return;} </script>';


//Переменные для отображения списка
$col1="silver";
$col2="#f5f5f5";
$bgcolor='';
$bgcolorz = "#D09388";
$flag=0;
//name of reference
$refName = 'emplReference';
//Variables for reference
//Number of current page
$refCurrentPage = 1;
// Number of sortable column
$refSortCol = 3;
//Direction of sort 0 - up, 1 - down
$refSortDirect = 0;
//Total number of found rows
$refTotalRows = 0;
//Total number of pages
$refTotalPages = 0;
// Top border
$refTopBorder = 0;
//Bottom border
$refBottomBorder = 0;
//Rows on page
$refPageLenght =100;

if ( !isset ( $_SESSION[$refName] ) )
{
        $_SESSION[$refName]['tabNum'] = 'null';
        $_SESSION[$refName]['family'] = '';
        $_SESSION[$refName]['firstName'] = '';
        $_SESSION[$refName]['surName'] = '';
        $_SESSION[$refName]['position'] = '';
        $_SESSION[$refName]['passCode'] = '';
        $_SESSION[$refName]['idDept'] = 0;
        $_SESSION[$refName]['withoutPass'] = 0;
        $_SESSION[$refName]['ext_int'] ='null';
        $_SESSION[$refName]['ext_text'] = '';

        $_SESSION[$refName]['currentPage'] = $refCurrentPage;
        $_SESSION[$refName]['sortCol'] = $refSortCol;
        $_SESSION[$refName]['sortDirect'] = $refSortDirect;
}
else
{
        if(isset($_GET['currentPage'])) $_SESSION[$refName]['currentPage'] = $_GET['currentPage'];
        if(isset($_GET['sortCol'])) $_SESSION[$refName]['sortCol'] = $_GET['sortCol'];
        if(isset($_GET['sortDirect'])) $_SESSION[$refName]['sortDirect'] = $_GET['sortDirect'];


        $refCurrentPage = $_SESSION[$refName]['currentPage'];
        $refSortDirect = $_SESSION[$refName]['sortDirect'];
        $refSortCol = $_SESSION[$refName]['sortCol'];
		
}


//если есть условия поиска из формы поиска
if ( isset ( $_POST['tab_num'] )  ) $_SESSION[$refName]['tabNum'] = ( !is_numeric($_POST['tab_num']) ) ?  'null' : $_POST['tab_num'];
if ( isset ( $_POST['family'] )  ) $_SESSION[$refName]['family'] = $_POST['family'];
if ( isset ( $_POST['fname'] )  ) $_SESSION[$refName]['firstName'] = $_POST['fname'];
if ( isset ( $_POST['secname'] )  ) $_SESSION[$refName]['surName'] = $_POST['secname'];
if ( isset ( $_POST['position'] )  ) $_SESSION[$refName]['position'] = $_POST['position'];
if ( isset ( $_POST['pass_code'] )  ) $_SESSION[$refName]['passCode'] = $_POST['pass_code'];
if ( isset ( $_POST['depart'] )  ) $_SESSION[$refName]['idDept'] = $_POST['depart'];
if ( isset ( $_POST['flagval'] )  ) $_SESSION[$refName]['withoutPass'] = $_POST['flagval'];
if ( isset ( $_POST['ext_int'] )  ) $_SESSION[$refName]['ext_int'] = ( !is_numeric($_POST['ext_int']) ) ?  'null' : $_POST['ext_int'];
if ( isset ( $_POST['ext_text'] )  ) $_SESSION[$refName]['ext_text'] = $_POST['ext_text'];
// --------------------------------
if ( isset ( $_POST['tab_num'] )  )  $_SESSION['is_filtered'] = true;
// --------------------------------


//если только открыли или нада сбросить условия поиска
if ( $action == 'new')
{
    $_SESSION[$refName]['tabNum'] = 'null';
    $_SESSION[$refName]['family'] = '';
    $_SESSION[$refName]['firstName'] = '';
    $_SESSION[$refName]['surName'] = '';
    $_SESSION[$refName]['position'] = '';
    $_SESSION[$refName]['passCode'] = '';
    $_SESSION[$refName]['idDept'] = 0;
    $_SESSION[$refName]['withoutPass'] = 0;
    $_SESSION[$refName]['currentPage'] = 1;
    $_SESSION[$refName]['sortDirect'] = 1;
    $_SESSION[$refName]['sortCol'] = 3;
    $_SESSION[$refName]['ext_int'] = 'null';
    $_SESSION[$refName]['ext_text'] = '';

    $_SESSION['is_filtered'] = false;
}
//если переход не по навигатору
if ( !isset($_GET['plink']) )
{
    //echo '<br>change direction<br>';
    $refSortDirect = ( $refSortDirect == 1 ) ? 0 : 1;
    $_SESSION[$refName]['sortDirect'] = $refSortDirect;
}


// // насильно меняем направление сортировки для только что открытого окна 
if($action == 'new')
 {
    $_SESSION[$refName]['sortDirect'] = 0;
    $refSortDirect = $_SESSION[$refName]['sortDirect'];	
 }
 elseif($_REQUEST['search']=='1') {
    $_SESSION[$refName]['sortDirect'] = 0;
    $refSortDirect = $_SESSION[$refName]['sortDirect'];
}
 


$q = 'select * from BASE_W_S_PERSONAL_COUNT_1(\''.$_SESSION[$refName]['family'].'\',\''.$_SESSION[$refName]['firstName'].'\',\''.$_SESSION[$refName]['surName'].'\',\''.$_SESSION[$refName]['position'].'\'
			,'.$_SESSION[$refName]['idDept'].','.$_SESSION[$refName]['tabNum'].',\''.$_SESSION[$refName]['passCode'].'\','.$_SESSION['iduser'].',\''.$_SESSION[$refName]['withoutPass'].'\','.$_SESSION[$refName]['ext_int'].',\''.$_SESSION[$refName]['ext_text'].'\')';


$nRowCnt = pg_query($q);

if ($nRowCnt)
{
	 $tt = pg_fetch_array($nRowCnt);
	 $refTotalRows =$tt['0'];
}
else {
	 $refTotalRows = 0;
}


$refTotalPages = ceil($refTotalRows / $refPageLenght);

if( $refCurrentPage == ( $refTotalPages + 1 ))
{
        $refTopBorder = abs($refTotalPages - (($refCurrentPage-1) * $refPageLenght));
        $refBottomBorder = $refTotalRows;
}
else
{
        $refTopBorder = $refPageLenght;
        $refBottomBorder = $refCurrentPage *  $refPageLenght;
}

if( $refPageLenght > $refTotalRows )
{
        $refTopBorder = $refTotalRows;
        $refBottomBorder = $refTotalRows;
}

$is_filtered = $_SESSION['is_filtered'];	
	
if($is_filtered)
{	
	$flt_pic_src = 'buttons/cancelSearchBt16.gif';
	$flt_btn_value = 'Снять фильтр';	
	$fnd_action = '"personal.php?action=new"';
}
else
{
	$flt_pic_src = 'buttons/findBt16.gif';
	$flt_btn_value = 'Фильтр';
	$fnd_action = '"#" onClick=\'ShowFindFrm()\'';	
}
$view.='<div id="pass_div" style="display:none;
                                  position:absolute;
                                  top:200px;
                                  right:200px;
                                  background-color:#f5f5f5;
                                  border:1px solid black;
                                  width: 195px;
                                    ">
            <input id="pass_code" type="text" maxlength="16" size="20" placeholder="Вставьте код пропуска" value="" class="input" />
            <input type="button" value="Ок" class="sbutton" onclick=\'SetPass()\' />
            <input id="pers_id" type="hidden" value=""/>
        </div>';

$view.='<table border=0 width="100%">';
$view.='<tr>';
$view.='<td valign="middle"><a href='.$fnd_action.' class="actlink"><b><img src="'.$flt_pic_src.'" class="icons" style="vertical-align: middle;">'.$flt_btn_value.'</b></a>';
$view.='<a href="addpers.php?action=add" class="actlink"><b><img src="buttons/new.gif" class="icons" style="vertical-align: middle;">Добавить сотрудника</b></a>';
$view.='<a href="#" onClick=\'ShowCloseModalWindow("infownd",0)\' class="actlink"><b><img src="buttons/info3.gif" class="icons" style="vertical-align: middle;">Информация</b></a>';
// $view.='<a href="#" class="actlink" onclick=PrintPersonalForm(event)><b><img src="buttons/print.gif" class="icons" style="vertical-align: middle;">Распечатать список</b></a></td>';
$view.='<a href="#" class="actlink" onclick=\'ShowFrm("print_wnd")\'><b><img src="buttons/print.gif" class="icons" style="vertical-align: middle;">Распечатать список</b></a></td>';
$view.='</tr>';
$view.='</table>';

/////////////////////////////////////////////////////////////////////////////////////////////////
//Show headers
$view .= '<table style="border: 0;color: black;" cellpadding="1" cellspacing="1" width="100%">';
$view .= '<tr class="tablehead">';
$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;currentPage='.$refCurrentPage.'&amp;sortCol=2&amp;sortDirect='.$refSortDirect.'" title="сортировать по табельному номеру">Таб.<br>номер</a></td>';
if ($ext == 'int')$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;currentPage='.$refCurrentPage.'&amp;sortCol=14&amp;sortDirect='.$refSortDirect.'" title="сортировать по доп. полю">Доп.<br>поле</a></td>';
if ($ext == 'text')$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;currentPage='.$refCurrentPage.'&amp;sortCol=15&amp;sortDirect='.$refSortDirect.'" title="сортировать по доп. полю">Доп.<br>поле</a></td>';
$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;currentPage='.$refCurrentPage.'&amp;sortCol=3&amp;sortDirect='.$refSortDirect.'" title="сортировать по фамилии">Фамилия</a></td>';
$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;currentPage='.$refCurrentPage.'&amp;sortCol=4&amp;sortDirect='.$refSortDirect.'" title="сортировать имени">Имя</a></td>';
$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;lcurrentPage='.$refCurrentPage.'&amp;sortCol=5&amp;sortDirect='.$refSortDirect.'" title="сортировать отчеству">Отчество</a></td>';
$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;currentPage='.$refCurrentPage.'&amp;sortCol=7&amp;sortDirect='.$refSortDirect.'" title="сортировать по отделу">Отдел</a></td>';
$view .= '<td align="center"><a class="sortlink" href="personal.php?action=showall&amp;currentPage='.$refCurrentPage.'&amp;sortCol=8&amp;sortDirect='.$refSortDirect.'" title="сортировать по должности">Должность</a></td>';
$view .= '<td align="center">График работы</td>';
$view .= '<td align="center">&nbsp;</td>';
$view .= '</tr>';


if ($refTotalRows>=$refBottomBorder)
{
	$calc_top = $refTopBorder;
}
else
{
	$calc_top = ($refTotalRows - $refTopBorder*($refCurrentPage-1));
}

$q = 'select * from BASE_W_S_PERSONAL_PAGE('.$calc_top.', '.$refBottomBorder.', '.$refSortCol.', '.$refSortDirect
			.', \''.$_SESSION[$refName]['family'].'\', \''.$_SESSION[$refName]['firstName'].'\', \''.$_SESSION[$refName]['surName']
			.'\', \''.$_SESSION[$refName]['position'].'\', '.$_SESSION[$refName]['idDept']
			.', '.$_SESSION[$refName]['tabNum'].', \''.$_SESSION[$refName]['passCode']
			.'\', '.$_SESSION['iduser'].', \''.$_SESSION[$refName]['withoutPass'].'\','.$_SESSION[$refName]['ext_int'].',\''.$_SESSION[$refName]['ext_text'].'\')';


$res = pg_query($q);
    while( $r = pg_fetch_array($res) )
    {
        if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
        if(@$r['pass'] == 0) $bgcolor = "#BE4343";
        $view .= '<tr onmouseover=\'this.style.backgroundColor="#89F384"\' onmouseout=\'this.style.backgroundColor="'.$bgcolor.'"\'>';
        $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="center">'.$r['tabel_num'].'</td>';
        if ($ext == 'int') $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="center">'.$r['ext_int'].'</td>';
        if ($ext == 'text') $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="center">'.$r['ext_text'].'</td>';
        $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="left" >'.$r['family'].'</td>';
        $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="left">'.$r['name'].'</td>';
        $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="left">'.$r['secname'].'</td>';
        $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="left">'.$r['dept'].'</td>';
        $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="left">'.$r['pos'].'</td>';
        if($r['id_graph']==0)$r['id_graph_name']='--';
        $view .= '<td class="tabcontent" bgcolor="'.$bgcolor.'" align="center">'.$r['id_graph_name'].'</td>';
        $view .= '<td nowrap bgcolor="'.$bgcolor.'" align="center">
            <a href="#" id="photo_'.$r['id'].'" onclick=\'AddEvent(this.id)\'  class="slink">';
                if(isset($r['photo'])){
                    if($r['photo']==" "){
                        $view .= '<img src="buttons/camera.gif" title="Назначить фото" class="icons">';
                    }
                    else{
                        $view .= '<img src="buttons/photo_change.gif" title="Изменить фото" class="icons">';
                    }
                }
                else{
                         $view .= '<img src="buttons/camera.gif" title="Назначить фото" class="icons">';
                    }
        $view .= '</a>
            <a  id="pass_'.$r['id'].'" onclick=\'ShowDivPass(this.id)\'  class="slink">
                <img src="buttons/pass.gif" title="Назначить пропуск" class="icons">
            </a>
            <a href="editpers.php?action=edit&amp;id='.$r['id'].'" class="slink">
                <img src="buttons/edit.gif" title="Править" class="icons">
            </a>
            <a href="#" class="slink" 
                onClick=\'javascript:DeletePerson('
		.$r['id'].',"'
		.str_replace(" ","~",$r['family']).'","'
		.str_replace(" ","~",$r['name']).'","'
		.str_replace(" ","~",$r['secname']).'")\'>
		<img src="buttons/remove.gif" title="Удалить" class="icons">
            </a>
        </td>';
		
		
       $view .= '</tr>';
     }
$view .= '</table>' ;

$view .= '<table border="1" width="100%" cellpadding="0" cellspacing="0" >';
$view .= '<tr class="tablehead"><td colspan="2">Найдено записей '.$refTotalRows.'</td></tr>';
$view .= '<tr bgcolor="gray">';
$view .= '<td valign="top" width=10%><span class="text" style="color:white;"><b>Страницы:</b>&nbsp;&nbsp;&nbsp;</span> </td>';
$view .= '<td align="left">';
if ( $refTotalPages > 1 )
{
    $pageCount = 0;
        for ( $p = 0; $p < $refTotalPages ; $p++)
        {
         $pag = $p+1;
                 if( $pag != $refCurrentPage )
                        $view .= '<a class="pagelink" href="personal.php?action=showall&amp;currentPage='.$pag.'&amp;sortCol='.$refSortCol.'&amp;sortDirect='.$refSortDirect.'&amp;plink">['.$pag.']</a>&nbsp;';
                        //$view .= '<a class="pagelink href = '$namepage?cols=".$cols."&sorts=".$sorts."&action=showall&len=$len&page=$pag".$dopnum."'>[$pag]</a> ";
         else
              $view .= '<font face="verdana" color="red" size=1 ><b>&nbsp;['.$pag.']&nbsp;</b></font>&nbsp;';

                $pageCount ++ ;
                if ( $pageCount == 20 )
                {
                        $view .= '<br>';
                        $pageCount = 0;
                }
    }
}
$view .= '</td>';
$view .= '</tr>';
$view .= '</table>';


$tabNum = ( $_SESSION[$refName]['tabNum'] == 'null') ? '' : $_SESSION[$refName]['tabNum'];
$ext_int = ( $_SESSION[$refName]['ext_int'] == 'null') ? '' : $_SESSION[$refName]['ext_int'];
$ext_text = ( $_SESSION[$refName]['ext_text'] == 'null') ? '' : $_SESSION[$refName]['ext_text'];
////////////////////////////////////////////////////////////////////////////////////////////////////

$view .= '<script type="text/javascript">

  function PrintPersonalForm(event)
  {
   var e = event || window.event;
   var printWnd = new Window.poupWindow("infowindow",e.clientY,e.clientX,-60,10,350,0,"window","Параметры печати");
        printWnd.Show();
   var fields = new Array("Таб.Номер","Фамилия","Имя","Отчество","Отдел","Должность","График","Код пропуска");
   var checks = new Array("tab_num checked disabled=1","fam checked disabled=1","name checked disabled=1","secname checked disabled=1","depart_flag","position_flag","graph_flag","pass_code_flag");

   
	var table="<form name=parintfrm action=\"print.php?action=printpers\" method=POST target=_blank>";
   table+="<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%>";
   table+="<tr class=client>";

   table+="<tr>";
   table+="<td><span class=text><b>Страницы<\/b><\/span><\/td>";
   table+="<\/tr>";
   table+="<tr>";
   table+="<td><input type=radio name=p_pages value=1 checked \/><span class=text>все<\/span><\/td>";
   table+="<\/tr>";
   table+="<td><input type=radio name=p_pages value=2 \/><span class=text>текущая<\/span><\/td>";
   table+="<\/tr>";

   table+="<tr>";
   table+="<td><hr size=1 ><\/td>";
   table+="<\/tr>";

   table+="<tr>";
   table+="<td><span class=text><b>Выводить данные<\/b><\/span><\/td>";
   table+="<\/tr>";
    table+="<tr><td>";
   for ( var i=0; i < fields.length; i++)
   {
     table+="<span class=text><input type=checkbox name="+checks[i]+">"+fields[i]+"<\/span>";
     if( i == 4) table+="<br\/>";

   }
   table+="<\/td><\/tr>";
   table+="<tr><td colspan=2><hr  size=1><\/td><\/tr>";
   table+="<tr>";
   table+="<td colspan=2><input type=checkbox name=excelflg><span class=text>Вывести в Excel<\/span><\/td>";
   table+="<\/tr>";
   table+="<tr>";
   table+="<td colspan=2><input type=submit class=sbutton value=Печать><\/td>";
   table+="<\/tr>"
   table+="<\/table>";
   table+="<input type=hidden name=cols value='.$refSortCol.'>";
   table+="<input type=hidden name=sort value='.$refSortDirect.'>";
   table+="<input type=hidden name=top1 value='.$refTopBorder.'>";
   table+="<input type=hidden name=top2 value='.$refBottomBorder.'>";
   table+="<input type=hidden name=f_tab_num value='.$tabNum.'>";';
    if ($ext == 'int') $view .= 'table+="<input type=hidden name=f_ext_int value='.$ext_int.'>";';
    if ($ext == 'text') $view .= 'table+="<input type=hidden name=f_ext_text value='.$ext_text.'>";';
   $view .= 'table+="<input type=hidden name=f_family value='.$_SESSION[$refName]['family'].'>";
   table+="<input type=hidden name=f_fname value='.$_SESSION[$refName]['firstName'].'>";
   table+="<input type=hidden name=f_secname value='.$_SESSION[$refName]['surName'].'>";
   table+="<input type=hidden name=f_id_dept value='.$_SESSION[$refName]['idDept'].'>";
   table+="<input type=hidden name=f_position value='.$_SESSION[$refName]['position'].'>";
   table+="<input type=hidden name=f_iduser value='.@$_SESSION['iduser'].'>";
   table+="<input type=hidden name=pass_code value='.$_SESSION[$refName]['passCode'].'>";
   table+="<input type=hidden name=pxflag value='.$_SESSION[$refName]['withoutPass'].'>";

   table+="<\/form>";
   printWnd.wnd.client.innerHTML+=table;

  }
  </script>';

///////////////////////////////////////////////////////////////////////////////
    //модальное окно поиска
     $view.='<div id="findwind" class="findwindow" style="padding:2px; background-color:#444; left: 100px; position:fixed;">';
     //форма поиска сотрудника
     $view.='<form id="findfrm" name="findfrm" action="personal.php?action=showall" method="POST" 
			style="width:465px; margin:0px;">';
     $view .= '<input type="hidden" name="search" value="1">';
         $view.='<table style="border:0; width:465px; height:350px; background-color:white;" cellpadding="4" cellspacing="0" class="mwtab" align="center">';
     $view.='<tr class="tablehead">
                 <td><p class="tabhead">Поиск сотрудника</p>
                 <td width="5%" style="padding-right:10px; text-align:right">
				 <div  id="closeBt"  class="imageBt"  onclick=\'CloseFindFrm()\'>
				<img title="Отмена" src="buttons/exitBt16.gif"></div>
				</td>
             </tr>';
     $view.='<tr>
             <td width="50%" ><p class="text">Фамилия</td>
             <td width="30%"><p class="text">Табельный номер</td>
             </tr>
             <tr><td><input type="text" name="family" value="'.$_SESSION[$refName]['family'] .'" size="20" class="input"></td>
                 <td><input type="text" name="tab_num" value="'.$tabNum .'" size="20" maxlength = "9" class="input" onKeyPress="check_number(event)"></td>
            </tr>
            <tr><td><p class="text">Имя</td>
                <td><p class="text">Код пропуска</td>
            </tr>
            <tr><td><input type="text" name="fname" value="'.$_SESSION[$refName]['firstName'] .'" size="20" class="input"></td>
                <td><input type="text" name="pass_code" value="'.$_SESSION[$refName]['passCode'] .'" size="20" maxlength="16" class="input"></td>
            </tr>
            <tr><td><p class="text">Отчество</td>';

        if ( $_SESSION[$refName]['withoutPass'] == 0 )
                        $view .= '<td><input type="checkbox" name="pxflag" onclick=\'ChangeFlagPass(this,document.findfrm);\' >';
                else
            $view .= '<td><input type="checkbox" name="pxflag" checked="checked" onclick=\'ChangeFlagPass(this,document.findfrm)\'; >';

      $view        .=  '<font face=verdana size=1><b>Сотрудники без пропуска</b></font>
                 <input type="hidden" name="flagval" value="'.$_SESSION[$refName]['withoutPass'].'">
                 </td></tr>
            <tr><td><input type="text" name="secname" value="" size="20" class="input"></td></tr>
            <tr><td><p class="text">Должность</td>';
             if ($ext != '') $view .= '   <td><p class="text">Доп. поле</td>';
            $view .= '</tr>
            <tr><td><input type="text" name="position" value="'.$_SESSION[$refName]['position'].'" size="20" class="input"></td>';
            if ($ext == 'int') $view .= '<td><input type="text" name="ext_int" value="'.$ext_int .'" size="20" maxlength = "50" class="input" onKeyPress="check_number(event)"></td></tr>';
            if ($ext == 'text') $view .= '<td><input type="text" name="ext_text" value="'.$ext_text .'" size="20" maxlength = "50" class="input" ></td></tr>';
           
             $view .= '<tr><td><p class="text">Отдел</td></tr>
            <tr><td colspan="2"><select name="depart" size=1 class="select"><option value="0">Все отделы</option>';
      $res=pg_query('select * from BASE_W_S_DEPT('.$_SESSION['iduser'].')');
      $sel='';
      while($r=pg_fetch_array($res))
      {
        if($_SESSION[$refName]['idDept'] > 0 && $_SESSION[$refName]['idDept'] ==$r['id'])$sel='selected';else $sel='';
        $view.='<option value="'.$r['id'].'" '.$sel.' title="'.$r['name'].'">'.substr($r['name'], 0, 50).'</option>';

      }

     $view.='</select><input type="hidden" name="dept_id" value=""></td></tr>';
     $view.='<tr class="tablehead">
             <td  align="left"><input type="button" value="Найти" onClick=\'SearchPers(window.document.findfrm)\' class="sbutton"></td>
             <td align="right"><input type="button" value="Отмена" onClick=\'CloseFindFrm()\' class="sbutton"></td></tr>';
     $view.='</table>';
     $view.='</form>';
     $view.='</div>';


  $q = 'select * from BASE_W_S_PERSONAL_INFO()';
  $r = pg_fetch_array(pg_query($q));
  //print_r($_REQUEST);
  $view.='<div id="infownd" style="display:none;position:absolute;top:11%;left:30%;z-index:2">';
         $view.='<table border="0" cellpadding="2" cellspacing="0" class="dtab">';
         $view.='<tr class="tablehead"><td>Информация</td></tr>';
         $view.='<tr><td class="reptext">';
         $view.= 'Всего сотрудников:'.$r['personal'].'<br>';
         $view.= 'Без пропусков:'.$r['nocodes'].'<br>';
         $view.= 'Без графиков:'.$r['nograph'].'<br>';
         $view.= 'Заблокированных:'.$r['lock'].'<br>';
         $view.= 'Администраторов:'.$r['admin'].'<br>';
         $view.= 'Свободных пропусков:'.$r['free_codes'].'<br>';
         $view.='</td></tr>';
         $view.='<tr><td style="text-align:left;">';
         $view.= '<input type="button" value="закрыть" onclick=\'ShowCloseModalWindow("infownd",1)\' class="sbutton"> ';
         $view.='</td></tr>';
         $view.='</table>';
  $view.='</div>';
//////////////////////////////////// -- коенц формы поиска

// ------- форма печати -------
// ------- внешний вид передрал из кода выше
$view .= '<div id="print_wnd" class="modalwindow" style="position: fixed; top:100px; left:50px; 
			background-color: gray;">';
$view .= '<form name=parintfrm action=print.php?action=printpers 
		method=POST target=_blank style="margin-top:10px; margin-bottom:5px; 
		border:none; background-color: white;">';
$view .= '<table cols=2 cellpadding=0 cellspacing=0 style="border:0; width:100%; height:100%;">';
$view .= '<tr class=client>';
// $view .= '<tr>';
$view .= '<td colspan="2"><span class=text><b>Страницы</b></span></td>';
$view .= '</tr>';
$view .= '<tr>';
$view .= '<td colspan="2"><input type=radio name=p_pages value=1 checked><span class=text>все</span></td>';
$view .= '</tr>';
$view .= '<tr>';
$view .= '<td colspan="2"><input type=radio name=p_pages value=2><span class=text>текущая</span></td>';
$view .= '</tr>';
$view .= '<tr>';
$view .= '<td colspan="2"><span class=text><b>Выводить данные</b></span></td>';
$view .= '</tr>';
$view .= '<tr><td>';

$view .= '<span class=text><input type=checkbox name="tab_num" checked disabled="1">Таб.Номер</span>';
$view .= '<br/>';
$view .= '<span class=text><input type=checkbox name="fam" checked disabled="1">Фамилия</span>';
$view .= '<br/>';
$view .= '<span class=text><input type=checkbox name="name"  checked disabled="1">Имя</span>';
$view .= '<br/>';
$view .= '<span class=text><input type=checkbox name="secname" checked disabled="1">Отчество</span>';
$view .='</td><td>';
$view .= '<span class=text><input type=checkbox name="depart_flag">Отдел</span>';
$view .= '<br/>';
$view .= '<span class=text><input type=checkbox name="position_flag">Должность</span>';
$view .= '<br/>';
$view .= '<span class=text><input type=checkbox name="graph_flag">График</span>';
$view .= '<br/>';
$view .= '<span class=text><input type=checkbox name="pass_code_flag">Код пропуска</span>';
$view .= '<br/>';
$view .= '<span class=text><input type=checkbox name="ext_flag">Доп. поле</span>';

$view .= '</td></tr>';
$view .= '<tr><td colspan=2><hr width="100%" size=1></td></tr>';
$view .= '<tr>';
$view .= '<td colspan=2><input type=checkbox name=excelflg><span class=text>Вывести в Excel</span></td>';
$view .= '</tr>';
$view .= '<tr>';
$view .= '<td ><input type=submit class=sbutton value=Печать></td>';
$view .= '<td><input type="button" value="Закрыть" onClick=\'CloseFrm("print_wnd")\' class="sbutton"/></td>';
$view .= '</tr>';
$view .= '</table>';
$view .= '<input type=hidden name=cols value='.$refSortCol.'>';
$view .= '<input type=hidden name=sort value='.$refSortDirect.'>';
$view .= '<input type=hidden name=top1 value='.$refTopBorder.'>';
$view .= '<input type=hidden name=top2 value='.$refBottomBorder.'>';
$view .= '<input type=hidden name=f_tab_num value='.$tabNum.'>';
if ($ext == 'int') $view .= '<input type=hidden name=f_ext_int value="'.$ext_int.'">';
if ($ext == 'text') $view .= '<input type=hidden name=f_ext_text value="'.$ext_text.'">';
$view .= '<input type=hidden name=f_family value='.$_SESSION[$refName]['family'].'>';
$view .= '<input type=hidden name=f_fname value='.$_SESSION[$refName]['firstName'].'>';
$view .= '<input type=hidden name=f_secname value='.$_SESSION[$refName]['surName'].'>';
$view .= '<input type=hidden name=f_id_dept value='.$_SESSION[$refName]['idDept'].'>';
$view .= '<input type=hidden name=f_position value='.$_SESSION[$refName]['position'].'>';
$view .= '<input type=hidden name=f_iduser value='.@$_SESSION['iduser'].'>';
$view .= '<input type=hidden name=pass_code value='.$_SESSION[$refName]['passCode'].'>';
$view .= '<input type=hidden name=pxflag value='.$_SESSION[$refName]['withoutPass'].'>';   
$view .= '</form>';
$view .= '</div>';
// ------- конец формы печати -------
///////////////////////
   //загрузка ФОТО
$view.='<div id="show_ph" style="display:none"><img id="ph" src="" width="100%" height="100%"></div>';
$view.='<form action="upload.php" method="post" target="hiddenframe" enctype="multipart/form-data" onsubmit="hideBtn();">
            <input type="file" id="userfile" name="userfile" accept="image/*" style="display:none" onchange="handleFiles(this.files)">
            <input type="submit" name="upload" id="upload" value="Загрузить" style="display:none">
        </form>
        
        <div id="res"></div>
        <iframe id="hiddenframe" name="hiddenframe" style="width:0px; height:0px; border:0px"></iframe>';

$view .= '<script type="text/javascript">
      window.URL = window.URL || window.webkitURL;

        function AddEvent(id){
            $("#pers_id").val(id.substr(6));
            var userfile = document.getElementById("userfile");
            userfile.click();
        }
        
           var photo = document.getElementById("ph");

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

$view .= '<script type="text/javascript">	

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
                    var pers_id =  $("#pers_id").val();
                    var data1 = {obj:"addPhoto", pers_id:pers_id, phname:mes.name};
                    $.ajax({
                        url: "asinc.php", 
                        type: "POST",
                        data: data1,
                        dataType: "json",
                        success: function (data) {
                            var dataObj = eval(data);
                            if (dataObj.res!=="1"){
                                alert("Ошибка");
                            }
                            else{
                                $("#photo_"+pers_id+" img").attr("src","buttons/photo_changed.gif");
                                $("#photo_"+pers_id+" img").attr("title","Фото сохранено");

                                $("#photo_"+pers_id).parents("tr").children("td").attr("bgcolor","#CAFFAF");
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert("error loadphoto");

                        }
                     });
                 }	
             }
     </script>';

if($action=='del' && isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])==true)
{
    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
     $q.='select BASE_W_D_PERSONAL('.$_REQUEST['id'].')';
    if(pg_query($q))
      echo ShowConfirmWindow('Подтверждение','Сотрудник удалён','');
    else
      echo ShowConfirmWindow('Ошибка при удалении сотрудника','');
     $_REQUEST['action']='showall';
	 
    HEADER("Location:personal.php?action=".$_REQUEST['action']);	 
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////


echo $view;

echo PrintFooter();


ob_flush();
?>
