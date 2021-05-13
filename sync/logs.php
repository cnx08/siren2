<?php
include("../include/input.php");
require_once("../classes/base/pages.h");

$action = '';
$BODY = '';
$totalPage = 0;
$pageLength = 100;
$currentPage = 1;
$topBorder = 100;
$bottomBorder = 100; 
$rowsQty = null;
$start_date = (!isset($_GET['start_date']) || $_GET['start_date'] == '') ? date('d.m.y') : $_GET['start_date'];
$end_date   =  (!isset($_GET['end_date']) || $_GET['end_date'] == '') ? date('d.m.y') : $_GET['end_date'];




if ( isset($_REQUEST['act']) &&  $_REQUEST['act'] != '' ) $action = $_REQUEST['act'];

$p = new CEmptyPage('Просмотр логов');
$p->addCSSInclude('css/base.css');
$p->start();

//print_r($_REQUEST);

if ( $action = 'show' )
{
	$errorFlag = false;
	
	$BODY .= '<table class="settingTable" cellpadding="0" cellspacing="0">'; 
	
	
	//подсчитываем количество строк
	$q = 'SELECT count(*) c FROM kadr_imp_tb_log t WHERE t.date between \''.$start_date.' 00:00:00\'::timestamp AND  \''.$end_date.' 23:59:59\'::timestamp';
	
	if ( $res = @pg_query($q) )
	{
		$r = pg_fetch_array($res);
		$rowsQty = $r['c'];
		$pageQty = ceil($rowsQty / $pageLength);
	   //устанавливаемя на последнюю страницу
	    $currentPage = ( isset( $_GET['page'] ) &&  $_GET['page'] != '' ) ? $_GET['page'] :  $pageQty  ;
		//echo $currentPage;
		$totalPages = ceil($rowsQty / $pageLength) -1;
		//если последняя страница
	   if($currentPage == ($totalPages + 1))
	   {
			$topBorder = $rowsQty - (($currentPage-1) * $pageLength);
			$bottomBorder = $rowsQty;
	   }
	   else
	   {
			$topBorder = $pageLength;
			$bottomBorder = $currentPage * $pageLength;
	   }
	   
	   //если длина страницы больше колличества найденых строк
	   if($pageLength > $rowsQty)
	   {
		 $topBorder = $rowsQty;
		 $bottomBorder = $rowsQty; 
	   }
	   
	   //Выводим страницы
	   $BODY .= '<tr><td colspan="5" >';
	   
	   $forward = '<a href="#" class="butlink" onclick=document.location.href=\'logs.php?act=show&start_date='.$start_date.'&end_date='.$end_date.'&page='.($currentPage+1).'\'>[вперёд]</a>';
	   $back = '<a href="#" class="butlink" onclick=document.location.href=\'logs.php?act=show&start_date='.$start_date.'&end_date='.$end_date.'&page='.($currentPage-1).'\'>[назад]</a>';
		
	   if ($currentPage > 1 ) $BODY .= $back; 
	   $BODY .= '<select id="pageNavigator1" class="select" onchange="document.location.href=\'logs.php?act=show&start_date='.$start_date.'&end_date='.$end_date.'&page=\' + this.value">';
	   
		
		
		
		if($pageQty > 1)
		{
			for ( $i = 0; $i <= $totalPages; $i++ )
			{
				$page = $i + 1;
				if( $page != $currentPage)
					$BODY .= '<option value="'.$page.' ">--'.$page.'--</option>';
				else
				$BODY .= '<option value="'.$page.' " selected >--'.$page.'--</option>';		
			}
		}	
	   $BODY .=  '</select>';
	  /* if ($currentPage >= 1 && $currentPage < $pageQty) $BODY .= $forward; */
	   $BODY .=  '</td></tr>';
	   
	   $q = 'select * from kadr_tmp_sp_get_log('.$topBorder.', '.$bottomBorder.',\''.$start_date.'\',\''.$end_date.'\')';

	   //заголовки
	   $BODY .= '<tr>';
	   $BODY .= '<th>Дата</th>';
	   $BODY .= '<th>Тип</th>';
       $BODY .= '<th>Описание</th>';
	   $BODY .= '<th>Источник</th>';
	   $BODY .= '</tr>';
	   if ($res = @pg_query($q))
	   {
			while( $r = pg_fetch_array($res) )
			{
				$BODY .= '<tr>';
				$BODY .= '<td>'.$r['date'].'</td>';
				if ( $r['type'] == 1) $BODY .= '<td>сообщение</td>';
				if ( $r['type'] == 2) $BODY .= '<td style="background-color: red">ошибка</td>';	
				
				$BODY .= '<td style="text-align:left">'.$r['messagetext'].'</td>';
				$BODY .= '<td>'.$r['source'].'</td>';
				$BODY .= '</tr>';
			}
	   }
	   else
	   {
		  $BODY .= '<tr><td style="color:red" colspan="5">Ошибка при получении данных: '.  pg_last_error().'</td></tr>';
	   }
	   if ($currentPage > 1 ) $BODY .= $back; 
	    $BODY .= '<tr><td colspan="5" >';
		$BODY .= '<select id="pageNavigator2" class="select" onchange="document.location.href=\'logs.php?act=show&start_date='.$start_date.'&end_date='.$end_date.'&page=\' + this.value">';
		if($pageQty > 1)
		{
			for ( $i = 0; $i <= $totalPages; $i++ )
			{
				$page = $i + 1;
				if( $page != $currentPage)
					$BODY .= '<option value="'.$page.' ">--'.$page.'--</option>';
				else
				$BODY .= '<option value="'.$page.' " selected >--'.$page.'--</option>';		
			}
		}	
	   $BODY .=  '</select>';
	   if ($currentPage >= 1 && $currentPage < $pageQty) $BODY .= $forward; 
	   $BODY .=  '</td></tr>';	   
	}
	else
	{
		$BODY .= '<tr><td style="color:red" colspan="5">Ошибка при подсчёте строк: '.pg_last_error().'</td></tr>';
	}
	
	
	$BODY .= '</table>';
}

echo $BODY;
$p->end();
?>