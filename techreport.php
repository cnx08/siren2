<?php

include("include/input.php");
require("include/common.php");
require("include/head.php");

session_write_close();
//проверяем на доступность
if(CheckAccessToModul(43,$_SESSION['modulaccess'])==false)
{
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}


echo PrintHead('СКУД','Технический отчёт');
$action = 'new';
$BODY = '';
$BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$BODY .= '<script type="text/javascript" src="techreport/script/techreport.js"></script>';
$BODY .= '<script type="text/javascript" src="gscripts/jquery/plugins/jquery.pickmeup.js"></script>';

$BODY .= '<LINK REL=STYLESHEET TYPE="text/css" HREF="techreport/style/techreport.css">';
if(isset($_REQUEST['act'])) $action = $_REQUEST['act'];  

if( $action == 'turn' )
{
$date = $_REQUEST['date'];
$period = $_REQUEST['per'];
$descr_per = array("day" => "день", "week" => "неделю", "mon" => "месяц",  "mon3" => "3 месяца", "mon6" => "6 месяцев", "year" => "год");
$BODY.='<table border="0" width="100%" class="repTable">
    <caption>Отчёт за '.$descr_per[$period].' с    '.$date.'</caption>	
    <tr>
      <th colspan="2">Точка доступа</th>
      <th rowspan="2">Период</th>
      <th colspan="2">Последнее событие</th>
      <th rowspan="2">Общее число событий</th>
      <th colspan="2">Число незавершённых проходов</th>
      <th colspan="2">Число незарегистрированных пропусков</td>
      <th colspan="2">Число запретов доступа</th>
      <th colspan="3">Число мультисобытий</th>
      <th colspan="2" >Число ошибок RS-485 <img src="../techreport/image/info.gif" title = "ошибка контрольной суммы при передаче пакета"></img></th>
      <th colspan="2">Проблемы с линией связи/питанием</th>
     </tr>
    <tr>
      <th>#</th>
      <th>Название</th>
      <th>Время</th>
      <th>Код</th>
      <th>Всего</th>
      <th>%</th>
      <th>Всего</th>
      <th>%</th>
      <th>Всего</th>
      <th>%</th>
      <th>Включен контроллер</th>
      <th>Сигнал пожар</th>
      <th>Ошибка считывателя <img src="../techreport/image/info.gif" title = "считан неверный код пропуска, контрольные биты не совпадают"></img></th>
      <th>Всего</th>
      <th>%</th>
      <th>Число пропущенных событий Т (период)<img src="../techreport/image/info.gif" title = "Количество случаев, когда отсутствует событие самотестирования дольше периода, указанного в скобках. Самотестирование происходит при простое системы (при отстутствии событий определённое время)"></img></th>
      <th>Число двойных событий К <img src="../techreport/image/info.gif" title = "События К идущие друг за другом"></img></th>
    </tr>';
	
	
	$query = 'select * from BASE_SYS_ORDER_TECH(\''.$date.'\',\''.$period.'\',null,null,0)';
	$res = pg_query($query);

	while ($r = pg_fetch_array($res) )
	{
            $undersp = strrpos($r['over_interval'],'_');
            $res1 = substr($r['over_interval'], 0, $undersp);
            $per = substr($r['over_interval'], $undersp+1);
            if($period!='day'){
		$BODY .= '<tr id="'.$period.'_'.$r['num'].'-'.$per.'xTUR" onclick=details(this,"'.$date.'")>';
            }
            else $BODY .= '<tr>';
		$BODY .= '<td >'.$r['num'].'</td>';
		$BODY .= '<td >'.$r['tname'].'</td>';
                $BODY .= '<td >'.$r['per'].'</td>';
		$BODY .= '<td>'.$r['time_max'].'</td>';
		$BODY .= '<td>'.$r['code'].'</td>';
		$BODY .= '<td>'.$r['count'].'</td>';
		$BODY .= '<td>'.$r['count_n'].'</td>';
		$BODY .= '<td>'.$r['count_n_p'].'</td>';
		$BODY .= '<td>'.$r['x'].'</td>';
		$BODY .= '<td>'.$r['x_p'].'</td>';
		$BODY .= '<td>'.$r['count_d'].'</td>';
		$BODY .= '<td>'.$r['count_d_p'].'</td>';
                $BODY .= '<td>'.$r['contr_on'].'</td>';
                $BODY .= '<td>'.$r['fire'].'</td>';
                $BODY .= '<td>'.$r['err_reader'].'</td>';
                $BODY .= '<td>'.$r['c'].'</td>';
		$BODY .= '<td>'.$r['c_p'].'</td>';
                $BODY .= '<td>'.$res1;
                if ($per!=1000){
                     $BODY .= ' ('.$per.')';
                }
                $BODY .= '</td>';
                $BODY .= '<td>'.$r['doublek'].'</td>';
		$BODY .= '</tr>';
	}
 
    
  $BODY .= '</table>';
	
}

if( $action == 'unit' )
{
$date = $_REQUEST['date'];
$period = $_REQUEST['per'];
$descr_per = array("day" => "день", "week" => "неделю", "mon" => "месяц",  "mon3" => "3 месяца", "mon6" => "6 месяцев", "year" => "год");
$BODY.='<table border="0" width="100%" class="repTable">
    <caption>Отчёт по работе сетевых контроллеров за '.$descr_per[$period].' с    '.$date.'</caption>	
    <tr>
      <th colspan="2">Сетевой контроллер</th>
      <th rowspan="2">Период</th>
      <th colspan="2">Последнее событие</th>
      <th rowspan="2">Общее число событий</th>
      <th colspan="2">USB контроллер</th>
      <th rowspan="2">Сигнал ПОЖАР</td>
      <th rowspan="2">Число перерывов в работе <img src="../techreport/image/info.gif" title = "Количество случаев, когда отсутствует событие самотестирования дольше периода, указанного в скобках."></img></th>
      <th rowspan="2">Число посторонних событий (с)<img src="../techreport/image/info.gif" title = "При штатной работе с сетевого контроллера должны приходить только события Т и М. Сюда может попасть также событие с - это не значит, что есть проблемы с сетевым контроллером. "></img></th>
     
     </tr>
    <tr>
      <th>#</th>
      <th>Юнит</th>
      <th>Время</th>
      <th>Код</th>
      <th>Вкл</th>
      <th>Выкл</th>
      
    </tr>';
	
	
	$query = 'select * from BASE_SYS_ORDER_TECH_USB(\''.$date.'\',\''.$period.'\',null,null,0)';
	$res = pg_query($query);

	while ($r = pg_fetch_array($res) )
	{
            $undersp = strrpos($r['over_interval'],'_');
            $res1 = substr($r['over_interval'], 0, $undersp);
            $per = substr($r['over_interval'], $undersp+1);
            if($period!='day'){
		$BODY .= '<tr id="'.$period.'_'.$r['num'].'-'.$per.'xUSB" onclick=details(this,"'.$date.'")>';
            }
            else $BODY .= '<tr>';
		$BODY .= '<td >'.$r['num'].'</td>';
		$BODY .= '<td >'.$r['unit'].'</td>';
                $BODY .= '<td >'.$r['per'].'</td>';
		$BODY .= '<td>'.$r['time_max'].'</td>';
		$BODY .= '<td>'.$r['code'].'</td>';
		$BODY .= '<td>'.$r['count'].'</td>';
		$BODY .= '<td>'.$r['usb_on'].'</td>';
		$BODY .= '<td>'.$r['usb_off'].'</td>';
		$BODY .= '<td>'.$r['fire'].'</td>';
		$BODY .= '<td>'.$res1;
                if ($per!=1000){
                     $BODY .= ' ('.$per.')';
                }
                $BODY .= '</td>';
		$BODY .= '<td>'.$r['other'].'</td>';              
		$BODY .= '</tr>';
	}
 

  $BODY .= '</table>';
  $BODY .= '<div class = "separator"></div>';
  $BODY.='<table border="0" width="100%" class="repTable">
    <caption>Отчёт по работе юнитов за '.$descr_per[$period].' с    '.$date.'</caption>
    <tr>
        <th rowspan="2">Юнит</th>
        <th rowspan="2">Период</th>
        <th colspan="2">Последнее событие</th>
        <th rowspan="2">Всего</th>
       
        <th colspan="2">USB контроллер</th>
        <th colspan="8">SRT</th>';
  
 $BODY.='<th colspan="3">DM</th>
        <th colspan="6">DM Перечитана таблица</th>
        
        <th colspan="3">Проверьте работоспособность</th>';
  $BODY.='<th colspan="2">DM перезагрузит</th>
        <th rowspan="2">Таблицы управления СКУД устарели</th>
        <th rowspan="2">Корректировка времени (неудачно)</th>
        <th rowspan="2">Нарушен формат пакета RS-485</th>
        <th rowspan="2">Неизвестные события</th>
       
    </tr>
    <tr>
        <th>Время</th>
        <th>Код</th>
        <th>Вкл</th>
        <th>Выкл</th>
        <th>Старт</th>
        <th>Стоп</th>
        <th>Рестарт</th>
        <th>Рестарт DM</th>
        <th>Рестарт ОС</th>
        <th>Недопустимая версия драйвера</th>
        <th>ошибка инициализации LIBUSB</th>
        <th>Превышено время обслуживания RS-485</th>
        <th>Старт</th>
        <th>Стоп</th>
        <th>Рестарт</th>
	<th>tuning</th>
        <th>pass</th>
        <th>dopusk</th>
        <th>docs</th>
        <th>turniket</th>
        <th>graph</th>
	
        <th>SRT</th>
        <th>NC485</th>
        <th>контроллера</th>
        <th>SRT</th>
        <th>ОС</th>';
        
        
   $BODY.=' </tr>';
	
	
	$query = 'select * from BASE_SYS_ORDER_TECH_UNIT(\''.$date.'\',\''.$period.'\',null,null)';
	$res = pg_query($query);

	while ($r = pg_fetch_array($res) )
	{
            if($period!='day'){
		$BODY .= '<tr id="'.$period.'_'.$r['num'].'-0xUNIT" onclick=details(this,"'.$date.'")>';
            }
            else $BODY .= '<tr>';
		$BODY .= '<td >'.$r['num'].'</td>';
                $BODY .= '<td >'.$r['per'].'</td>';
		$BODY .= '<td>'.$r['time_max'].'</td>';
		$BODY .= '<td>'.$r['code'].'</td>';
		$BODY .= '<td>'.$r['count'].'</td>';
		$BODY .= '<td>'.$r['usb_on'].'</td>';
		$BODY .= '<td>'.$r['usb_off'].'</td>';
		$BODY .= '<td>'.$r['srt_start'].'</td>';
		$BODY .= '<td>'.$r['srt_stop'].'</td>';
                $BODY .= '<td>'.$r['srt_reboot_srt'].'</td>';
		$BODY .= '<td>'.$r['srt_redm'].'</td>';
                $BODY .= '<td>'.$r['srt_reboot_pc'].'</td>';
                $BODY .= '<td>'.$r['srt_bad_driver'].'</td>';
                $BODY .= '<td>'.$r['srt_libusb_err'].'</td>';
                $BODY .= '<td>'.$r['srt_large_handling'].'</td>';
                $BODY .= '<td>'.$r['dm_start'].'</td>';
                $BODY .= '<td>'.$r['dm_stop'].'</td>';
                $BODY .= '<td>'.$r['dm_self_restart'].'</td>';
                $BODY .= '<td>'.$r['dm_tuning'].'</td>';
		$BODY .= '<td>'.$r['dm_pass'].'</td>';  
                $BODY .= '<td>'.$r['dm_dopusk'].'</td>';
		$BODY .= '<td>'.$r['dm_docs'].'</td>';
		$BODY .= '<td>'.$r['dm_turn'].'</td>';
                $BODY .= '<td>'.$r['dm_graph'].'</td>';
                $BODY .= '<td>'.$r['dm_check_srt'].'</td>';
		$BODY .= '<td>'.$r['dm_check_nc485'].'</td>';
		$BODY .= '<td>'.trim($r['dm_check_cntr'],",").'</td>';
                $BODY .= '<td>'.$r['dm_resrt'].'</td>';
                $BODY .= '<td>'.$r['dm_reboot_pc'].'</td>';
		$BODY .= '<td>'.$r['dm_old_table'].'</td>';
		$BODY .= '<td>'.$r['dm_time_corr'].' ('.$r['dm_time_corr_err'].')</td>';
                $BODY .= '<td>'.$r['bad_package'].'</td>';
		$BODY .= '<td>'.$r['unknown_event'].'</td>'; 
		$BODY .= '</tr>';
	}

    
  $BODY .= '</table>';
    

}

echo $BODY;
echo PrintFooterI();
