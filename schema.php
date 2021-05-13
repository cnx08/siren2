<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*ресурсы разбросаны немного... 
 * стили в /gstyles/common_styles.css
 * скрипты в /gscripts/schema.js
 * асинхронка в /techreport/asinc.php
 */
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
//проверяем на доступность
echo PrintHead('СКУД - Схема системы','Схема СКУД');
if(CheckAccessToModul(50,$_SESSION['modulaccess'])==false)
{
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
require_once("include/menu.php");

$BODY.='<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$BODY.='<script type="text/javascript" src="../gscripts/schema.js"></script>';

$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
$q.=' select get_units_info();';//send command to units с просьбой дать фидбэк
        
 pg_query($q);
sleep(2);

$left_div.='<div class="schema_div_main"><div class="schema_div_left">';
$right_div.='<div class="schema_div_right">';

if($result = pg_query('select value from base_const where id = 39;')){
    $r=pg_fetch_array($result);
    $firm_name = $r['value'];
}
$last_turn = '';
$last_unit = '';

$left_div.=   '<div class ="schema_div_img" ><img class ="schema_img" src = "images/green_serv.jpg"></div>';
$right_div.=  '<div id="server" class = "div_content">'
                    . '<p>'.$firm_name.'</p>'
                    . '<p>'.date("d.m.Y H:i:s").'</p>'
                    . '<p>'
                    .   '<span id="t_log" class="TBD t_log">Работа процедур БД</span>'
                    .   '<span id="left_ev" class="TBD left_ev">Загрузка событий</span>'
                    . '</p>'
              . '</div>';


$result=pg_query('select * from pr_get_scud_schema();');
$images = array(0=>'unit',1=>'turn',2=>'door',3=>'tab',4=>'barrier',5=>'gate');
$color = array(0=>'green_',1=>'red_');
$alert = 0;
$add_class = '';
while($r=pg_fetch_array($result))
{
    $num = $r['turn_num']-($r['unit']-1)*32;
    $nc_num = 32*($r['unit']-1);
    if ($nc_num < 0) $nc_num = ' не присвоен';
    if ($r['id'] == 3)
    {
        if ( $r['unit_name'] != ''){
            $u_name = $r['unit_name'];
            $add_class = '';
        }
        else {
            $u_name = 'Название не назначено';
            $add_class = 'red_alert';
        }
        $right_div.= '<div id="unit_'.$r['unit'].'" class = "div_content"><p class="'.$add_class.'">'.$u_name.' (ip:&nbsp;'.$r['time_ip'].') '.' #'.$nc_num.'</p>';
        $last_unit = $r['last_one'] == 1 ? 'lu_' : '';
        $add_class = '';
    }
    if ($r['id'] == 4)
    {
        $alert = $r['alert'];
        if ($r['alert'] == 1){ $add_class = ' red_alert';}
        $right_div.= '<p class = "'.$add_class.'">SRT: <span class = "schema_span_srt">'.$r['time_ip'].'</span></p>';
        $add_class = '';
    }
    if ($r['id'] == 5)
    {
        if ($alert<1) {$alert = $r['alert'];}
        if ($r['alert'] == 1){ $add_class = ' red_alert';}
        $right_div.= '<p class = "'.$add_class.'">DM: <span class = "schema_span_dm">'.$r['time_ip'].'</span></p>';
        $add_class = '';
    }
    if ($r['id'] == 32){
        $last_turn = $r['last_one'] == 1 ? 'ld_' : '';
        if ($alert<1) {$alert = $r['alert'];}
        if ($r['alert'] == 1){ $add_class = ' red_alert';}
        $left_div.= '<div class ="schema_div_img" ><img class ="schema_img" src = "images/'.$last_unit.$last_turn.$color[$alert].$images[$r['turn_type_id']].'.jpg"></div>';
        $right_div.= '<p class = "'.$add_class.'">NC-485: '.$r['time_ip'].'</p></div>';
        $add_class = '';
        $alert = 0;
    }
 
    if ($r['id'] > 32){
        $last_turn = $r['last_one'] == 1 ? 'ld_' : '';
        $descr = $r['unit'] == 1 ? '' : ' ('.$r['turn_num'].')';
        $left_div.= '<div class ="schema_div_img" ><img class ="schema_img" src = "images/'.$last_unit.$last_turn.$color[$r['alert']].$images[$r['turn_type_id']].'.jpg"></div>';
        $right_div.= '<div id="tur_'.$r['turn_num'].'" class = "div_content_turn"><p>'.$r['turn_name'].' #'.$num.$descr.'</p><p>'.$r['time_ip'].'</p>';
        $right_div.= '<p><span id="rs_'.$r['turn_num'].'" class="TBD rs">RS-485</span><span id="line_'.$r['turn_num'].'" class="TBD line">Потерянные события</span><span id="reader_'.$r['turn_num'].'" class="TBD reader">Считыватель</span></p>';
        $right_div.= '<p><span id="io_'.$r['turn_num'].'" class="TBD io">Датчик проходов</span><span id="test_'.$r['turn_num'].'" class="TBD test">Самотестирование</span><span id="fire_'.$r['turn_num'].'" class="TBD fire">Пожар</span></p>';
        $right_div.= '<p><span id="reg_'.$r['turn_num'].'" class="TBD reg">Режимность</span><span id="zas_'.$r['turn_num'].'" class="TBD zas">Антипассбэк</span><span id="dop_'.$r['turn_num'].'" class="TBD dop">Допуска</span><span id="graph_'.$r['turn_num'].'" class="TBD graph">Нет в базе</span></p>';
        $right_div.= '</div>';
        $last_turn = '';
    }

    $alert = 0;
}

$left_div.=         '</div>';
$right_div.=         '</div></div>';


$BODY .= $left_div.$right_div;

echo $BODY;
