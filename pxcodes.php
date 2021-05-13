<?php
/*
	$action определяет режим показа формы pxcodes:
		- 'new' - впервые открыта, все параметры по умолчанию 
		- 'show'+ доп. параметры - показать список, в параметрах - страница, берутся из переменных $_GET
		- 'add' - добавление нового пропуска - из скрытой изначально формы заведения нового пропуска
		- как организовать сортировку списка пропусков? 

*/
//главное  не инициализировать $FltData и всё будет збс, и да, я вахуе
$IDMODUL=4;
require("include/head.php");
include("include/input.php");
require_once("include/common.php");
require_once('include/hua.php');

if (!isset($_SESSION['flt']))
{
    $_SESSION['flt'] = 0;
}
elseif($_REQUEST['is_filtered']==1)
{
    $_SESSION['flt'] = 1;
}
else 
{
    $_SESSION['flt'] = 0;
}

//проверяем на доступность
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
    echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
    exit();
}

// ------------- Заголовок страницы -------------
if(!isset($_REQUEST['action']))
{
    $_REQUEST['action']='';$title='';
}
	
if($_REQUEST['action']=='new')
{
    $title='Новый пропуск';
}
	
if($_REQUEST['action']=='choose')
{
    $title='Пропуска';
}
	
if($_REQUEST['action']=='add')
{
    $title='';
}
// ------------- Заголовок страницы  -------------------
$action = ( isset( $_GET['action'] ) && $_GET['action'] != '' ) ?  $_GET['action'] : '';


$jss = '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
   $jss .= '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';
echo $jss;

//Переменные для отображения списка
$col1     = "silver";
$col2     = "#f5f5f5";
$bgcolor  = '';
$bgcolorz = "#D09388";

$refCurrentPage  = 1;   //Number of current page
$refSortCol      = 3;   //Number of sortable column
$refSortDirect   = 0;   //Direction of sort 0 - up, 1 - down
$refTotalRows    = 0;   //Total number of found rows
$refTotalPages   = 0;   //Total number of pages
$refTopBorder    = 0;   //Top border
$refBottomBorder = 0;   //Bottom border
$refPageLenght   = 100; //Rows on page

if ( !isset ($_SESSION[$refName]) )
{
    $_SESSION[$refName]['TABEL_NUM']    = 'null';
    $_SESSION[$refName]['PXCODE']       = 'null';		
    $_SESSION[$refName]['ID_DEPT']      = 'null';		
    $_SESSION[$refName]['ID_PERS']      = 'null';		
    $_SESSION[$refName]['SORT_ORDER']   = 0;			
    $_SESSION[$refName]['currentPage']  = $refCurrentPage;
    $_SESSION[$refName]['sortCol']      = $refSortCol;
    $_SESSION[$refName]['sortDirect']   = $refSortDirect;		
}
else
{
    if(isset($_GET['currentPage']))         
        $_SESSION[$refName]['currentPage'] = $_GET['currentPage'];

    if(isset($_GET['sortCol']))                 
        $_SESSION[$refName]['sortCol'] = $_GET['sortCol'];

    if(isset($_GET['sortDirect']))
    {
        $_SESSION[$refName]['sortDirect'] = $_GET['sortDirect'];
    }
    else
    {	
        $_SESSION[$refName]['sortDirect'] = 0;
    }

    $refCurrentPage = $_SESSION[$refName]['currentPage'];
    $refSortDirect  = $_SESSION[$refName]['sortDirect'];
    $refSortCol     = $_SESSION[$refName]['sortCol'];
}

/*
	структура для хранения следующая:
	переменные формы:
		- номер текущей страницы
		- к-во записей на страницу
		- начальная запись  - расчетная (?)
		- к-во страниц	- расчетная(?)
		- всего записей

		форма поиска\фильтра - при $action = 'new' - все значения сбрасываются в состояние по умолчанию
		ID_PERS - идентификатор сотрудника-владельца пропуска
		FIO		- ФИО сотрудника-владельца пропуска
		TABEL_NUM - табельный номер сотрудника-владельца пропуска
		PXCODE	- код пропуска 
		ID_DEPT - идентификатор подразделения для фильтра по подразделению
		SORT_ORDER - порядок сортировки: 0 (по умолчанию) - по возрастанию ASC, 1 - по убыванию DESC
		
		флажки (переключатели):
		free_only - только свободные пропуска (взаимоисключающий с другими условиями фильтра)
		admin_only - только с правами администратора
		ghost_only - только гостевые
		blocked_only - блокированные
		deleted_only - удаленные (с [DELETE] = 0) - ?? неизвестно как с ними дальше работать
		double_only - с контролем двойных засечек
		
*/


if ( $action == 'new')
{
    $_SESSION[$refName]['tabNum'] = 'null';
    $_SESSION[$refName]['passCode'] = '';
    $_SESSION[$refName]['idDept'] = 0;
    $_SESSION[$refName]['withoutPass'] = 0;

    $_SESSION[$refName]['currentPage'] = 1;
    $_SESSION[$refName]['sortDirect'] = 0;
    $_SESSION[$refName]['sortCol'] = 3;
}

elseif(( !isset($_GET['plink']) ) || $_GET['is_filtered'] == 'false' || $_SESSION['is_filtered'] == false)
{
    $refSortDirect = ( $refSortDirect == 1 ) ? 0 : 1;
    $_SESSION[$refName]['sortDirect'] = $refSortDirect;

    $_SESSION['is_filtered'] = false;	

    $_SESSION['admin'] = 0;
    $_SESSION['apb'] = 0; 
    $_SESSION['avto'] = 0;
    $_SESSION['guest'] = 0;
    $_SESSION['block'] = 0;
}


// ------------ настройка переменных сессии 11.01.2011 --------------------
// для пейджера - переменная, хранящая номер отображаемой страницы с данными, по нему вычисляется какие данные отображать

if (!isset($_REQUEST['currentPage']))
{
    $_SESSION['page_number'] = 1;
}
else 
if (($_REQUEST['currentPage'])<1)
{
    $_SESSION['page_number'] = 1;
}
else
{
    $_SESSION['page_number'] = $_REQUEST['currentPage'];
}

	
	
// количество записей на страницу 
IF (!isset($_SESSION['rows_per_page']))
{
    $_SESSION['rows_per_page'] = 50;		// можно брать из интерфейса откуда-нить, типа из комбы
}

// всего записей - изначально 0
IF (!isset($_SESSION['row_count']))
{
    $_SESSION['row_count'] = 0;
}	
	
// превая запись - изначально 1
IF (!isset($_SESSION['row_start']))
{
    $_SESSION['row_start'] = 1;
}
else
{
// вычисляется как номер_страницы*к-во_записей+1
    $_SESSION['row_start'] = ($_SESSION['page_number']-1)*$_SESSION['rows_per_page']+1;
}	
	
IF(($_REQUEST['action']=='show'))
{	
    IF (isset($_REQUEST['btSearchOK']))	// была нажата кнопка ОК
    {
        IF((isset($_REQUEST['passCode']) and ($_REQUEST['passCode']<>'')) || isset($_REQUEST['chGuest']) || isset($_REQUEST['chAdmin']) || isset($_REQUEST['chApb']) || isset($_REQUEST['chAuto']) || isset($_REQUEST['chBlock']))
        {
            $_SESSION['is_filtered'] = true;	
        }	


        IF(isset($_REQUEST['chAdmin']))
        {
            $_SESSION['admin'] = 1;
        }

        IF(isset($_REQUEST['chGuest']))
        {
            $_SESSION['guest'] = 1;
        }

        IF(isset($_REQUEST['chApb']))
        {
            $_SESSION['apb'] = 1;
        }

        IF(isset($_REQUEST['chAuto']))
        {
            $_SESSION['avto'] = 1;
        }

        IF(isset($_REQUEST['chBlock']))
        {
            $_SESSION['block'] = 1;
        }	
    }
    //elseif(!isset($_REQUEST['plink']))	// если есть plink - переход осуществлен по нажатию на номер страницы
    //{
    //    $_SESSION['is_filtered'] = false;
    //}
}

if ( !isset ( $_SESSION[$FltData] ) )
{
    $_SESSION[$FltData]['IDPers']       = 'null';
    $_SESSION[$FltData]['PassCode']     = '';
    $_SESSION[$FltData]['FreePass']     = 0;
    $_SESSION[$FltData]['TabNumber']    = 'null';		

    $_SESSION[$FltData]['currentPage']  = $refCurrentPage;
    $_SESSION[$FltData]['sortCol']      = $refSortCol;
    $_SESSION[$FltData]['sortDirect']   = $refSortDirect;

    $_SESSION[$FltData]['blocked']	= 'null';
    $_SESSION[$FltData]['admin']	= 'null';
    $_SESSION[$FltData]['ghost']	= 'null'; 
    $_SESSION[$FltData]['dbl']          = 'null'; 
    $_SESSION[$FltData]['avto']         = 'null';	
    $_SESSION[$FltData]['pxcode']       = 'null';
}
else
{
    if(isset($_GET['currentPage']))
    {
        $_SESSION[$refName]['currentPage'] = $_GET['currentPage'];
    }

    if(isset($_GET['sortCol']))
    {
        $_SESSION[$FltData]['sortCol'] = $_GET['sortCol'];
    }

    if(isset($_GET['sortDirect']))
    {
        $_SESSION[$FltData]['sortDirect'] = $_GET['sortDirect'];
    }


    $refCurrentPage = $_SESSION[$FltData]['currentPage'];
    $refSortDirect  = $_SESSION[$FltData]['sortDirect'];
    $refSortCol     = $_SESSION[$FltData]['sortCol'];		

//		из формы фильтра 
    $_SESSION[$FltData]['blocked'] = $_REQUEST['chBlock'];
    $_SESSION[$FltData]['admin']   = $_REQUEST['chAdmin'];
    $_SESSION[$FltData]['ghost']   = $_REQUEST['chGhost']; 
    $_SESSION[$FltData]['dbl']     = $_REQUEST['chApb'];  
    $_SESSION[$FltData]['avto']    = $_REQUEST['chAvto']; 
    $_SESSION[$FltData]['pxcode']  = $_REQUEST['passCode']; 
}
	


// --------------- конец настройки параметров сессии -------------------------------------------------------------------------


// ------------------ код для пейджера ------------------------------------------------------
if (!isset($_SESSION['rows_per_page']))
{
    $rows_per_page = 50;	
}
else
{
    $rows_per_page = $_SESSION['rows_per_page'];
}



if (!isset($_SESSION['page_number']))
{
    $_SESSION['page_number'] = 1;
    $page_number = $_SESSION['page_number'];
}
else 
{	
    $page_number = $_SESSION['page_number'];
}

// из текущих $page_number (номер текущей страницы) и $rows_per_page(число записей на странице)
// вычисляем начальную позицию записи в отобранном наборе 
$row_start = ($page_number-1)*$rows_per_page+1;	// первая запись


// ----------- код для пейджера -------------------------------------------------------------
function ShowPxCodeList($selflag)
{
   $result='';
   $result.='
   <script type="text/javascript">
        function removePass(name,id)
        {
            if(confirm("Вы действительно хотите удалить пропуск \""+name.replace("~"," ")+"\""))
            {
                document.location.href="pxcodes.php?action=del&px="+id; 
            }
        }
    </script>';
 
    $result.='<table border="0" width="100%" cellpadding="0" cellspacing="0">
      <tr class="tablehead">
        <td align="center" width = "150px"><p class="tabhead" ><b>Код пропуска</b></p></td>
        <td align="center" width = "200px"><p class="tabhead" ><b>Дата ввода/вывода</b></p></td>
        <td align="center" width = "80px"><p class="tabhead" ><b>Pin Код</b></p></td>
        <td align="center"><p class="tabhead" ><b>Коментарий</b></p></td>';
	if($selflag == 0)	
	     $result.= '<td align="center"><p class="tabhead" ><b>Владелец</b></p></td>';
   
    $result.='<td align="center"><p class="tabhead" ><b>Статус</b></p></td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      </tr>';
      $col1="silver";
      $col2="#f5f5f5";
      $flag=0;
      $q = '';
	  

    if($selflag == 1)
    {
        $q = pg_query('select * from BASE_W_S_CODES_PAGE(50,1,11,1,null,null,null,null,null,null,'.$_SESSION['iduser'].',1)');
    }	
    else	
    {
        $qstr = 'select * from BASE_W_S_CODES_PAGE('.$_SESSION['rows_per_page'].', 
                                            '.$_SESSION['row_start'].',11,1,
                                            \''.$_SESSION[$FltData]['pxcode'].'\',
                                          '.$_SESSION['admin'].',
                                            '.$_SESSION['apb'].',
                                            '.$_SESSION['avto'].',
                                            '.$_SESSION['guest'].',
                                            '.$_SESSION['block'].',
                                            '.$_SESSION['iduser'].',0)';
					
        $q=pg_query($qstr);		
    }

    while($res=pg_fetch_array($q))
    {
        if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
        $result.= '<tr bgcolor="'.$bgcolor.'" onmouseover=\'this.style.backgroundColor="#89F384"\' onmouseout=\'this.style.backgroundColor="'.$bgcolor.'"\'>
        <td align="center"><p class="tabcontent" style="text-indent:20px">'.$res['code'].'</p></td>
        <td align="center"><p class="tabcontent" >'.$res['date_in_txt'].' / '.$res['date_out_txt'].'</p></td>
        <td align="center"><p class="tabcontent" >'.$res['pin'].'</p></td>
        <td align="center"><p class="tabcontent" >'.$res['comment'].'</p></td>';
        if($selflag == 0)	
	    $result .= '<td align="center"><p class="tabcontent" >'.$res['owner'].'</p></td>';
		 
        $result .= '<td align="center"><p class="tabcontent" >'.ParsePxCode($res['status'],$res['id']).'</p></td>';
        //формируем строку с праметрами для кнопки select
        $p='';
        $p.=$res['id'].',"'.$res['code'].'","'.$res['date_in_txt'].'","'.$res['date_out_txt'].'","'.$res['pin'].'",';
        $p.=GetCodeValue($res['status'],0).',';
        $p.=GetCodeValue($res['status'],1).',';
        $p.=GetCodeValue($res['status'],2).',';
        $p.=GetCodeValue($res['status'],3).',';
        $p.=GetCodeValue($res['status'],4);
        if($selflag==1)
            $result.='<td align="center"><img src="buttons/givepas.gif" style="cursor:pointer" onClick=\'SelectPxCode('.$p.')\' alt="выбрать пропуск"></td>';
        if($selflag==0)
        {
            $result.='<td align="center" width = "20px"><a><img src="buttons/edit.gif" onclick=\'document.location.href="pxcodes.php?action=edit&amp;px='.$res['id'].'"\' class="icons" alt="Править" /></a></td>';
            $result.='<td align="center" width = "20px"><a><img src="buttons/remove.gif" onclick=\'removePass("'.$res['code'].'",'.$res['id'].')\' class="icons" alt="Удалить" /></a></td>';
        }

        $result.='</tr>';
    }
    $result.='</table>';
    $href='';
    $close='';

    if($selflag==1)
    {
        if(isset($_REQUEST['call']))$href='pxcodes.php?action=new&amp;flag=1&amp;call='.$_REQUEST['call'];
        else
            $href='pxcodes.php?action=new&amp;flag=1';
        $close='<input type="button" name="cancel" value="Отмена" onClick="javascript:window.close()" style="font-size:11px;font-family:Verdana;border:1px solid silver;">';
    }
    else if($selflag==0)
    {
        $href='pxcodes.php?action=new';
    }

    $result.=  '<table width="100%" cellpadding="2" cellspacing="0">
                    <tr bgcolor="gray">
                        <td colspan="6" width="50%">'.$close.'</td>
                        <td colspan="6" width="50%" align="right"><input type="button" name="newpass" style="font-size:11px;font-family:Verdana;border:1px solid silver;" value="Новый" onClick=\'document.location.href="'.$href.'"\' ></td>
                    </tr>
                </table>';
 return  $result;
}

if($_REQUEST['action']=='show')
{
    $result = '';
    echo PrintHead('CКУД','Список пропусков');
    require("include/menu.php");

    $scr = '<script type="text/javascript"> 

    function SearchPXCodes(f)
    {
        var tt=window.document.getElementById("findwind");
        alert(tt.style.display);
        if (tt.style.display=="block")
        {
            tt.style.display="none";
        }
        tt.submit();
    }
    </script>';

    $is_filtered = $_SESSION['is_filtered'];
	
    if($is_filtered=='true')
    {	
        $flt_pic_src = 'buttons/cancelSearchBt16.gif';
        $flt_btn_value = 'Снять фильтр';	
        $fnd_action = '"pxcodes.php?action=show&amp;is_filtered=false"';
    }
    else
    {
        $flt_pic_src = 'buttons/findBt16.gif';
        $flt_btn_value = 'Фильтр';
        $fnd_action = '"#" onClick=\'ShowFindFrm()\'';	
    }

    $result.='<table border=0 width="100%">';
    $result.='<tr>';
    $result.='<td valign="middle"><a href='.$fnd_action.' class="actlink"><b><img src="'.$flt_pic_src.'" class="icons" style="vertical-align: middle;">'.$flt_btn_value.'</b></a>';
    $result.='<a href="pxcodes.php?action=new" class="actlink"><b><img src="buttons/new.gif" class="icons" style="vertical-align: middle;">Добавить пропуск</b></a>';
    $result.='<a href="#" onClick=\'ShowCloseModalWindow("infownd",0)\' class="actlink"><b><img src="buttons/info3.gif" class="icons" style="vertical-align: middle;">Информация</b></a>';
    $result.='<a href="#" name="print_link" class="actlink" onclick=\'ShowFrm("print_wnd")\'>';


    $result .= '<b><img src="buttons/print.gif" class="icons" style="vertical-align: middle;">Распечатать список</b>';

            $result .= '</a>';


    $result .= '</td>';
    $result.='</tr>';
    $result.='</table>';
    echo($result);	

    echo ShowPxCodeList(0);

    
    /// ------- сюда перенес расчет данных для пейджера -----
    $query = 'select * from pr_get_pxcodes_count(\''.$_SESSION[$FltData]['pxcode'].'\', 
                            '.$_SESSION['admin'].', '.$_SESSION['apb'].','.$_SESSION['avto'].','.$_SESSION['guest'].'
                            , '.$_SESSION['block'].', '.$_SESSION['iduser'].')';

    $rr = pg_query($query);
    if ($rr)
    {
        $tt = pg_fetch_array($rr);
        $row_count =$tt['0']; 
    }
    else 
    {
        $row_count = 0;
    }

    // сохраняем в сессии
    $_SESSION['row_count'] = $row_count;

    if (!isset($_SESSION['row_count']))
    {
        $row_count = 0;
    }
    else 
    {
        $row_count = $_SESSION['row_count'];
    }

    $pages = ceil($row_count/$rows_per_page);

    // ----------- отрисовываем пейджер  ------------
    // текущая страница, на которой сейчас находимся
    $refCurrentPage = $_SESSION['page_number'];


    $pg_view = '';
    $pg_view .= '<table border="1" width="100%" cellpadding="0" cellspacing="0" >';
    $pg_view .= '<tr class="tablehead"><td colspan="2">Найдено записей '.$row_count.'</td></tr>';
    $pg_view .= '<tr bgcolor="gray">';
    $pg_view .= '<td valign="top" width=10%><span class="text" style="color:white;"><b>Страницы:</b>&nbsp;&nbsp;&nbsp;</span> </td>';
    $pg_view .= '<td align="left">';

    if ( $pages > 1 )
    {
        $pageCount = 0;
        for ( $p = 0; $p < $pages ; $p++)
        {
            $pag = $p+1;
            if( $pag != $refCurrentPage )
                $pg_view .= '<a class="pagelink" href="pxcodes.php?action=show&amp;currentPage='.$pag.'&amp;sortCol='.$refSortCol.'&amp;sortDirect='.$refSortDirect.'&amp;plink">['.$pag.']</a>&nbsp;';
             else
                $pg_view .= '<font face="verdana" color="red" size=1 ><b>&nbsp;['.$pag.']&nbsp;</b></font>&nbsp;';

            $pageCount ++ ;
            if ( $pageCount == 20 )
            {
                $pg_view .= '<br>';
                $pageCount = 0;
            }
        }
    }
    $pg_view .= '</td>';
    $pg_view .= '</tr>';
    $pg_view .= '</table>';


    echo($pg_view);
    
}// ---------------------- конец пейджера ----------------

if($_REQUEST['action']=='choose')
{

    $px_body = '';
    $px_body .= '<!DOCTYPE HTML>';
    $px_body .= '<html>
                    <head>
                        <title>'.$title.'
                        </title>
                        <meta http-equiv="Content-Language" content="ru">
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                         <link rel="stylesheet" type="text/css" href="include/menu.css">
                         <script  type="text/javascript" src="include/function.js"></script>
                    </head>
                    <body style="margin-top:0;margin-left:0;margin-right:0;margin-bottom:0;">';
    echo($px_body); 
    echo ShowPxCodeList(1);
    $_REQUEST['call']='personal';
}

if($_REQUEST['action'] == 'edit' && isset($_REQUEST['px']) && IdValidate($_REQUEST['px']) == true)
{

   $q = 'select * from BASE_W_S_CODES('.$_REQUEST['px'].')';
   $result = pg_query($q);
   $r = pg_fetch_array($result);


   echo PrintHead('СКУД','Редактирование пропуска');
   
   
   echo '<form name="editpass" action="pxcodes.php?action=save" method="POST" >';
   echo '<input type="hidden" name = "pxcode_id" value="'.$r['id'].'"><table border="0" width=80% align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f5dc" class="dtab">
      <tr class="tablehead">
        <td colspan="2"><p class="tabhead" align="center"><b>Пропуск</b></p></td>
      </tr>
      <tr><td width="40%"><p class="text">Код пропуска</p></td>
          <td><input type="text" name="code" value="'.$r['code'].'" size="20" class="input"></td>
      </tr>
      <tr><td width="40%"><p class="text">Дата введения в эксплуатацию</p></td>';
    echo '<td>';
	  if($r['date_in']==NULL)
	  {
           echo ('<input type="text" id ="datein" name="datein" value="'.date("d.m.Y").'" size="20" readonly class="input" />');
		  }
		 else 
		 {
		  echo ('<input type="text" id ="datein" name="datein" value="'.$r['date_in'].'" size="20" readonly class="input" />');
		 }
      echo '</td>
      </tr>
      <tr><td width="40%"><p class="text">Дата вывода из эксплуатации</p></td>';
       echo '<td>';
           if($r['date_out']==NULL)
	  {
           echo ('<input type="text" id ="dateout" name="dateout" value="01.01.2050" size="20" readonly class="input" />');
		  }
		 else 
		 {
		  echo ('<input type="text" id ="dateout" name="dateout" value="'.$r['date_out'].'" size="20" readonly class="input" />');
		 }
      echo '</td>
      </tr>
      <tr>
         <td width="40%"><p class="text">Pin код</p></td>
          <td><input type="text" name="pin" value="'.$r['pin'].'" maxlength="4" size="20" class="input"></td>
      </tr>
      <tr>
         <td width="40%"><p class="text">Метка(краткий коментарий)</p></td>
          <td><input type="text" name="comment" value="'.$r['comment'].'" size="25" class="input"></td>
      </tr>

      <tr>
         <td width="40%"><p class="text">Статус:</p></td>
          <td><input type="hidden" name="status" value="" size="20" class="input"></td>
      </tr>
      ';
       $chk = '';
       if(GetCodeValue($r['status'],0)==1)$chk = 'checked';else $chk = '';
    echo  '<tr>
          <td ><input type="checkbox" name="block" value="" '.$chk.'><span class="text">Блокировать</span></td>';
        if(GetCodeValue($r['status'],1)==1)$chk = 'checked';else $chk = '';
    echo  '<td ><input type="checkbox" name="pxguest" value="" '.$chk.'><span class="text">Гостевой</span></td>
       </tr>';

       if(GetCodeValue($r['status'],2)==1)$chk = 'checked';else $chk = '';
    echo  '<tr>
          <td ><input type="checkbox" name="pxadmin" value="" '.$chk.'><span class="text">Администратор</span></td>';
       if(GetCodeValue($r['status'],4)==1)$chk = 'checked';else $chk = '';

    echo  '  <td ><input type="checkbox" name="pxauto" value="" '.$chk.'><span class="text">Автомобильный</span></td>
       </tr>';
        if(GetCodeValue($r['status'],3)==1)$chk = 'checked';else $chk = '';

    echo   '<tr>
         <td colspan="2"><input type="checkbox" name="pxdouble" value="" '.$chk.'><span class="text">Контроль двойных засечек</span></td>
      </tr>';
   echo '</table>';
   echo '<table border="0" width=80% align="center" cellpadding="2" cellspacing="0" class="dtab" >
         <tr class="tablehead">
         <td><input type="button" name="cancel" value="Отмена" onClick=\'document.location.href="pxcodes.php?action=show&amp;act=canceledit"\'  class="sbutton"></td>
         <td align="right"><input type="button" name="add" value="Сохранить" onclick=\'CheckPxCodeValidate(window.document.editpass)\' class="sbutton"></td>

         </tr>
         </table>';
  echo '</form>';
  $js .= '<script type="text/javascript">
        $(function () {
            $("#datein").pickmeup({
                change : function (val) {
                    $("#datein").val(val).pickmeup("hide")
                }
            });
            $("#dateout").pickmeup({
                change : function (val) {
                    $("#dateout").val(val).pickmeup("hide")
                }
            });
            
        });</script>';
echo $js;
}

//форма создания нового пропуска
if($_REQUEST['action']=='new')
{
    echo PrintHead('СКУД','Создание нового пропуска');


    $on_of  = '';
    $href   = '';
    $action = '';

    if(!isset($_REQUEST['call']))$_REQUEST['call'] = '';
    if($_REQUEST['call'] == 'pers')
    {
        $on_of = 'disabled="disabled"';
        if(!isset($_REQUEST['flag']))
        {
            require("include/menu.php");
            $href='pxcodes.php?action=show&call='.$_REQUEST['call'];
            $action='pxcodes.php?action=add&call='.$_REQUEST['call'];
        }
        else if(isset($_REQUEST['flag']))
        {
            $href='pxcodes.php?action=choose&call='.$_REQUEST['call'];
            $action='pxcodes.php?action=add&flag=1&call='.$_REQUEST['call'];
        }
    }
    else
    {
        $on_of = '';
        if(!isset($_REQUEST['flag']))
        {
            require("include/menu.php");
            $href='pxcodes.php?action=show';
            $action='pxcodes.php?action=add';
        }
    else if(isset($_REQUEST['flag']))
    {
        $href='pxcodes.php?action=choose';
        $action='pxcodes.php?action=add&flag=1';
    }

   }

   echo '<form name="addpass" action="'.$action.'" method="POST" >';
   echo '<table border="0" width=80% align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f5dc" class="dtab">
      <tr class="tablehead">
        <td colspan="2"><p class="tabhead" align="center"><b>Новый пропуск</b></p></td>
      </tr>
      <tr><td width="40%"><p class="text">Код пропуска</p></td>
          <td><input type="text" name="code" value="" size="20" maxlength="16" class="input"></td>
      </tr>
      <tr><td width="40%"><p class="text">Дата введения в эксплуатацию</p></td>
          <td><input type="text" id ="datein" name="datein" value="'.date("d.m.Y").'" size="20" readonly class="input" />
          </td>
      </tr>
      <tr><td width="40%"><p class="text">Дата вывода из эксплуатации</p></td>
          <td><input type="text" id ="dateout" name="dateout" value="01.01.2050" size="20" readonly class="input" />
          </td>
      </tr>
      <tr>
         <td width="40%"><p class="text">Pin код</p></td>
          <td><input type="text" name="pin" value="" maxlength="4" size="20" class="input"></td>
      </tr>
      <tr>
         <td width="40%"><p class="text">Метка(краткий коментарий)</p></td>
          <td><input type="text" name="comment" value="" size="25" class="input"></td>
      </tr>

      <tr>
         <td width="40%"><p class="text">Статус:</p></td>
          <td><input type="hidden" name="status" value="" size="20" class="input"></td>
      </tr>

      <tr>
          <td ><input type="checkbox" name="block" value="" ><span class="text">Блокировать</span></td>
          <td ><input type="checkbox" name="pxguest" value="" '.$on_of.'><span class="text">Гостевой</span></td>
       </tr>
       <tr>
          <td ><input type="checkbox" name="pxadmin" value=""><span class="text">Администратор</span></td>
          <td ><input type="checkbox" name="pxauto" value="" '.$on_of.'><span class="text">Автомобильный</span></td>
       </tr>
       <tr>
         <td colspan="2"><input type="checkbox" name="pxdouble" value=""><span class="text">Контроль двойных засечек</span></td>
      </tr>';
   echo '</table>';
   echo '<table border="0" width=80% align="center" cellpadding="2" cellspacing="0" class="dtab" >
         <tr class="tablehead">
         <td><input type="button" name="cancel" value="Отмена" onClick=\'document.location.href="'.$href.'"\'  class="sbutton"></td>
         <td align="right"><input type="button" name="add" value="Добавить" onclick=\'CheckPxCodeValidate(window.document.addpass)\' class="sbutton"></td>

         </tr>
         </table>';
  echo '</form>';
   $js .= '<script type="text/javascript">
        $(function () {
            $("#datein").pickmeup({
                change : function (val) {
                    $("#datein").val(val).pickmeup("hide")
                }
            });
            $("#dateout").pickmeup({
                change : function (val) {
                    $("#dateout").val(val).pickmeup("hide")
                }
            });
            
        });</script>';
echo $js;
}


//Выполняем добовление нового пропуска
$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
if($_REQUEST['action']=='add')
{
    if(!isset($_REQUEST['pin']) || $_REQUEST['pin']=='')$_REQUEST['pin']="0000";
    $dateout = $_REQUEST['dateout']=='' ? 'NULL' : CheckString($_REQUEST['dateout']);

         $q.='select * from BASE_W_I_CODES(\''.CheckString($_REQUEST['datein']).'\',\''.$dateout.'\',\''
        .CheckString($_REQUEST['code']).'\',\''.CheckString($_REQUEST['pin']).'\',\''
        .CheckString($_REQUEST['status']).'\',\''.CheckString($_REQUEST['comment']).'\')';

    pg_query($q);
    $selflag=0;
    if(isset($_REQUEST['flag'])) header("Location:pxcodes.php?action=choose");
    else header("Location:pxcodes.php?action=show");

}

if($_REQUEST['action'] == 'save' && isset($_POST['pxcode_id']) && IdValidate($_POST['pxcode_id']) == true)
{
    if(!isset($_REQUEST['pin']) || $_REQUEST['pin']=='')$_REQUEST['pin']="0000";
     $dateout = $_REQUEST['dateout']=='' ? 'NULL' : CheckString($_REQUEST['dateout']);

        $_REQUEST['dateout']='NULL';
         $q.='select * from BASE_W_U_CODES('.$_POST['pxcode_id'].',\''.CheckString($_REQUEST['datein']).'\',\''.$dateout.'\',\''
        .CheckString($_REQUEST['code']).'\',\''.CheckString($_REQUEST['pin']).'\',\''
        .CheckString($_REQUEST['status']).'\',\''.CheckString($_REQUEST['comment']).'\')';

    pg_query($q);
    $selflag=0;

    if(isset($_REQUEST['flag'])) header("Location:pxcodes.php?action=choose");
    else header("Location:pxcodes.php?action=show");
}

if($_REQUEST['action'] == 'del' && isset($_REQUEST['px']) && IdValidate($_REQUEST['px']) == true)
{
    $q .= 'select BASE_W_D_CODES('.$_REQUEST['px'].')';
    pg_query($q);

    if(isset($_REQUEST['flag'])) header("Location:pxcodes.php?action=choose");
    else  header("Location:pxcodes.php?action=show");
}

$q_info = 'select * from BASE_W_S_CODES_INFO()';
$r_info =pg_fetch_array(pg_query($q_info));

$info.='<div id="infownd" style="display:none; width: 234px; height:176px; position:absolute;top:11%;left:30%;z-index:2">';
$info .= '<div id="div_block_info" style="width: 234px; height:176px; background-color:#444;">';
$info .= '<div id="div_inner_info" style="padding:2px; position:absolute; width:230px;height:172px; background-color:#c0c0c0;">';
$info.='<table border="0" cellpadding="2" cellspacing="0" class="dtab">';

$info.= '<tr><th colspan="2"><table class="modalTitle"><tr><th width="95%">&nbsp;</th>
<th width="5%" style="text-align:center">
<div  id="closeModalBt"  class="imageBt"  onclick=\'ShowCloseModalWindow("infownd",1)\'>
<img title="Отмена" src="buttons/exitBt16.gif"></div>
</th>
</tr></table>';

$info.='<tr class="tablehead"><td style = "width:200px;">Информация</td><td>&nbsp;</td></tr>';
$info.='<tr><td class="reptext" style="width:75%; text-align:left;">';
$info.= 'Всего пропусков:</td><td class="reptext">'.$r_info['c_all'].'</td>';
$info.='</tr><tr>';
$info.= '<td class="reptext" style="width:75%; text-align:left;">Актуальных:</td><td class="reptext">'.$r_info['c_actual'].'</td>';
$info.='</tr><tr>';
$info.= '<td class="reptext" style="width:75%; text-align:left;">Неактуальных:</td><td class="reptext">'.$r_info['c_non_actual'].'</td>';
$info.='</tr><tr>';
$info.= '<td class="reptext" style="width:75%; text-align:left;">На руках:</td><td class="reptext">'.$r_info['c_pers'].'</td>';
$info.='</tr><tr>';
$info.= '<td class="reptext" style="width:75%; text-align:left;">Свободных:</td><td class="reptext">'.$r_info['c_free'].'</td>';
$info.='</tr><tr>';
$info.= '<td class="reptext" style="width:75%; text-align:left;">Администраторов:</td><td class="reptext">'.$r_info['c_admin'].'</td>';
$info.='</tr><tr>';
$info.= '<td class="reptext" style="width:75%; text-align:left;">Гостевых:</td><td class="reptext">'.$r_info['c_ghost'].'</td>';
$info.='</tr><tr>';
$info.= '<td class="reptext" style="width:75%; text-align:left;">Заблокированных:</td><td class="reptext">'.$r_info['c_blocked'].'</td>';
$info.='</tr>';
$info .= '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
$info.='<tr style="height:5px;"><td>&nbsp;</td><td style="text-align:right; padding-right:3px;">';
$info.= '<input type="button" value="закрыть" onclick=\'ShowCloseModalWindow("infownd",1)\'  class="sbutton"> ';
$info.='</td></tr>';
$info .= '<tr style="height:5px;"><td>&nbsp;</td><td>&nbsp;</td></tr>';
$info.='</table>';
$info.='</div>';
$info .='</div></div>';

echo($info);
////////////////////////////////// -- конец формы Инфо

$flt_form = '<div id="findwind" style="position:absolute; top:50px; left:0px; display:none; height:100%; width:100%; background-color: transparent; z-index: 20000;">&nbsp;';
$flt_form .='<div  id="div_block" style="top:100px; 
			left:150px; width:334px; height:234px; padding:2px; background-color:#444; position:fixed;">';
$flt_form .='<div id="innerdiv" style="position:absolute;top:2px;left:2px;z-index:30000;padding: 2px; width: 330px; height:230px; background-color: white;/*background-color: #c0c0c0;*/">';
$flt_form .= '<form id="searchForm" name="searchForm" action="pxcodes.php?action=show"  enctype="multipart/form-data" method="POST" >
<input   id="act" name="act" value="filter" type="hidden" > 
<table border="0" class="modalTable" cellpadding="0" cellspacing="0" align="center" style="width:98%">
<tr><th colspan="2"><table class="modalTitle"><tr><th width="95%">Поиск пропуска</th>
<th width="5%" style="text-align:center">
    <div  id="closeBt"  class="imageBt"  onclick=\'CloseFindFrm()\'>
    <img title="Отмена" src="buttons/exitBt16.gif"></div>
</th>
</tr>
</table></th></tr>
<tr><td>&nbsp;<td><td>&nbsp;</td></tr>
<tr><td style="width: 40em;">Код пропуска</td>
<td><input   id="passCode" name="passCode" type="text" class="textField" size="30" maxlength="16" value='.$_SESSION[$FltData]['pxcode'].'></td></tr>
<tr><td rowspan="5">Статус</td><td ><input   id="chBlock" type="checkbox" name="chBlock" value=1>Блокированый</td></tr>
<tr><td><input   id="chGuest" type="checkbox" name="chGuest" value=1>Гостевой</td></tr>
<tr><td><input   id="chAdmin" type="checkbox" name="chAdmin" value=1>Администратор</td></tr>
<tr><td><input   id="chApb" type="checkbox" name="chApb" value=1>АПБ (Антипасбэк)</td></tr>
<tr><td><input   id="chAuto" type="checkbox" name="chAuto" value=1>Автомобильный</td></tr>
<tr><td colspan="2"><div class="formBevel" style="width:95%;"></div></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><th colspan="2" style="text-align:right;">
<input type="submit" name="btSearchOK" value="Найти" onClick=\'SearchPXCodes("searchForm")\' class="sbutton"> 
<input type="submit" name="btSearchCancel" value="Отмена" onClick=\'CloseFindFrm()\' class="sbutton"></th></tr>
</table>
</form>';			
			
$flt_form .= '</div></div></div>';

echo($flt_form);
// --------------------------------- конец формы фильтра ----------------------------------------------


// ------- форма печати -------


$prn_form = '<div id="print_wnd" class="modalwindow" style="width: 342px; height:151px; position: fixed; top:100px; left:50px; display: none;">';
$prn_form .= '<div id="div_block_print" style="height:100%; width:100%; padding:2px; background-color:#444;">';
$prn_form .= '<div id="div_inner_print" style="position:absolute;top:2px;left:2px;z-index:30000;padding: 2px; width: 340px; height:150px;background-color: #c0c0c0;">';
$prn_form .= '<form name="printfrm" action=print.php?action=printcodes method=POST target=_blank style="margin-top:10px; margin-bottom:5px; border:none; background-color: white;">';
$prn_form .= '<table cols="2" cellpadding="0" cellspacing="0" style="border:0; width:100%; height:100%;">';
$prn_form .= '<tr class=client>';
$prn_form .= '<td><span class=text><b>Страницы</b></span></td>';
$prn_form .= '<td><input type=radio name=p_pages value=1 checked><span class=text>все</span></td>';
$prn_form .= '<td><input type=radio name=p_pages value=2><span class=text>текущая</span></td>';
$prn_form .= '</tr>';
$prn_form .= '<tr>';
$prn_form .= '<td colspan="2"><span class=text><b>Выводить данные</b></span></td>';
$prn_form .= '</tr>';
$prn_form .= '<tr><td  style="padding-left:10px;">';
$prn_form .= '<span class=text><input type=checkbox name="px_code" checked disabled="1">Код пропуска</span>';
$prn_form .= '<br/>';
$prn_form .= '<span class=text><input type=checkbox name="date_in" checked disabled="1">Дата введения</span>';
$prn_form .= '<br/>';
$prn_form .= '<span class=text><input type=checkbox name="px_status" checked disabled="1">Статус</span>';
$prn_form .= '</td><td style="left:10px;">';
$prn_form .= '<span class=text><input type=checkbox name="pin" checked disabled="1">Пин-код</span>';
$prn_form .= '<br/>';
$prn_form .= '<span class=text><input type=checkbox name="date_out"  checked disabled="1">Дата окончания</span>';
$prn_form .= '<br/>';
$prn_form .= '<span class=text><input type=checkbox name="px_owner">Владелец пропуска</span>';
$prn_form .='</td><td>&nbsp;';

$prn_form .= '</td></tr>';
$prn_form .= '<tr><td colspan=2><hr style="width:100%;" size=1></td></tr>';
$prn_form .= '<tr>';
$prn_form .= '<td colspan=2><input type=checkbox name=excelflg><span class=text>Вывести в Excel</span></td>';
$prn_form .= '</tr>';
$prn_form .= '<tr>';
$prn_form .= '<td ><input type=submit class=sbutton value=Печать></td>';
$prn_form .= '<td><input type="button" value="Закрыть" onClick=\'CloseFrm("print_wnd")\' class="sbutton"/></td>';
$prn_form .= '</tr>';
$prn_form .= '</table>';

$prn_form .= '<input type=hidden name=cols value=4>';
$prn_form .= '<input type=hidden name=sort value=\'aaa\'>';
$prn_form .= '<input type=hidden name=top1 value='.$_SESSION['rows_per_page'].'>';
$prn_form .= '<input type=hidden name=top2 value='.$_SESSION['row_start'].'>';


$prn_code .= '<input type=hidden name="p_CODE" value=';

if (isset($_SESSION[$FltData]['pxcode'])) 
{
    $prn_code .= '\''.$_SESSION[$FltData]['pxcode'].'\'';
}
else
{
    $prn_code .= 'null';
}

if ($_SESSION[$FltData]['pxcode'] == '') $_SESSION[$FltData]['pxcode'] = 'null';

$prn_code .= '>';
$prn_form .= $prn_code;
$prn_form .= '<input type=hidden name="p_ADMIN" value='.$_SESSION['admin'].'>';
$prn_form .= '<input type=hidden name="p_APB" value='.$_SESSION['apb'].'>';
$prn_form .= '<input type=hidden name="p_AVTO" value='.$_SESSION['avto'].'>'; 
$prn_form .= '<input type=hidden name="p_GUEST" value='.$_SESSION['guest'].'>'; 
$prn_form .= '<input type=hidden name="p_BLOCK" value='.$_SESSION['block'].'>';
$prn_form .= '</form>';
$prn_form .= '</div>';
$prn_form .= '</div></div>';


echo($prn_form);

echo PrintFooter();

?>
