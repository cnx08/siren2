<?php

ob_start();
set_time_limit(0);

include("include/input.php");
require("include/common.php");
require_once('include/hua.php');
//require("include/head.php");
session_write_close();
if (!isset($_REQUEST['excelflg']))
    require("include/head.php");
if (!isset($_REQUEST['fin_date']))
    $_REQUEST['fin_date'] = '';

$stDate = ( isset($_REQUEST['start_date']) && $_REQUEST['start_date'] != '' ) ? $_REQUEST['start_date'] : date('d.m.Y');
$endDate = ( isset($_REQUEST['fin_date']) && $_REQUEST['fin_date'] != '' ) ? $_REQUEST['fin_date'] : $stDate;


$BODY = '<!DOCTYPE html>	
    <html>
    <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
    <head></head>
    
    <body style="margin-top:0;margin-left:0;margin-right:0;margin-bottom:0;">';
$q = '';
if (!isset($_REQUEST['rtype'])) {
    HEADER("Location:techreportsmenu.php");
} else {
    $t = $_REQUEST['rtype'];
    switch ($t) {
        //***********************************************************************************/
        
        //ОТЧЁТ ПО ЛОГАМ
        case '1':
            
            $head = array();
            $head[] = 'Дата/время';
            $head[] = 'Функция';
            $head[] = 'Сообщение';
            
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';
            if (isset($_REQUEST['excelflg'])) {
                $col1 = "#FFFFFF";
                $col2 = "#FFFFFF";
                $border = 'border=1';
            }
            if (!isset($_REQUEST['excelflg']))
                $BODY.= PrintHead('Отчёт', 'Отчёт по логам. Сформирован - ' . date("d.m.Y H:i:s"));
            else
                $BODY.= '<center><b>Отчёт по логам. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';
            
            $BODY.= '<script type="text/javascript" src="include/window.js"></script>';
            
            $BODY.='<br><table ' . $border . ' class="techreportTable">';
            $BODY.='<tr width = "99%">';

            for ($i = 0; $i < sizeof($head); $i++) {
                $BODY.='<th>' . $head[$i] . '</th>';
            }
            $BODY.='</tr>';
            $only_err = $_REQUEST['only_err'];
            $pr_type = $_REQUEST['pr_type'];
            if (isset($_REQUEST['schema_request']) && $_REQUEST['schema_request']=='1')
            {
                $only_err = 'on';
                $startDate = time();
                $stDate = date('Y-m-d H:i:s', strtotime('-7 day', $startDate));
                $endDate = date("d.m.Y");
                $pr_type = 0;
            }
            $q = 'select * from pr_tech_order_tbllog(\''.$stDate.'\', \''
                                                  .$endDate.'\', \''
                                                  .$only_err.'\', '
                                                  .$pr_type.')';
                                                 

                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['error'] == '1') {$BODY.='<tr bgcolor="#F37D82">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}
                    
                    
                    $BODY.='<td width = "120px">'.$r['drb'].    '</td>';
                    $BODY.='<td >'.$r['object_name'].    '</td>';
                    $BODY.='<td >'.str_replace('character varying','v_ch',$r['mess_descr']).'</td>';
                    
                   
                   }
            $BODY.='</table></body></html>';

        break;
        //ОТЧЁТ ПО ТРИГГЕРАМ
        case '2':
            $head = array();
            $head[] = 'Дата/время';
            $head[] = 'Действие';
            $head[] = 'Таблица';
            $head[] = 'Добавленные / Измененные / Удаленные данные';
            $head[] = 'Пользователь';
            $head[] = 'ip';
            $head[] = 'Инфо';
            
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';
            if (isset($_REQUEST['excelflg'])) {
                $col1 = "#FFFFFF";
                $col2 = "#FFFFFF";
                $border = 'border=1';
            }
            if (!isset($_REQUEST['excelflg']))
                $BODY.= PrintHead('Отчёт', 'Отчёт по триггерам. Сформирован - ' . date("d.m.Y H:i:s"));
            else
                $BODY.= '<center><b>Отчёт по триггерам. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';
            
            $BODY.= '<script type="text/javascript" src="include/window.js"></script>';
            
            $BODY.='<br><table ' . $border . ' class="techreportTable">';
            $BODY.='<tr width = "99%">';

            for ($i = 0; $i < sizeof($head); $i++) {
                $BODY.='<th>' . $head[$i] . '</th>';
            }
            $BODY.='</tr>';

            $q = 'select * from pr_tech_order_trigger_log(\''.$stDate.'\', \'' .$endDate.'\', ' .$_REQUEST['tr_type'].', \'' .$_REQUEST['search_text'].'\', \'' .$_REQUEST['search_login'].'\', \'' .$_REQUEST['search_ip'].'\')';
                                                 

                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }                    
                    $BODY.='<tr bgcolor=' . $bgcolor . '>';
                    $BODY.='<td width = "120px">'.$r['date'].    '</td>';
                    $BODY.='<td >'.$r['action_n'].    '</td>';
                    $BODY.='<td >'.$r['table_name'].    '</td>';
                    $BODY.='<td >'.$r['mess_descr'].'</td>';
                    $BODY.='<td >'.$r['login'].'</td>';
                    $BODY.='<td >'.$r['ip'].'</td>';
                    $BODY.='<td >'.del_rubish($r['info']).'</td></tr>';
                    
                   
                   }
            $BODY.='</table></body></html>';

        break;
        //ЛОГ входа в скуд
        case '3':
            $head = array();
            $head[] = 'ID';
            $head[] = 'Дата/время';
            $head[] = 'Логин';
            $head[] = 'IP';
            $head[] = 'Информация';
             
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';
            if (isset($_REQUEST['excelflg'])) {
                $col1 = "#FFFFFF";
                $col2 = "#FFFFFF";
                $border = 'border=1';
            }
            if (!isset($_REQUEST['excelflg']))
                $BODY.= PrintHead('Отчёт', 'Лог авторизации. Сформирован - ' . date("d.m.Y H:i:s"));
            else
                $BODY.= '<center><b>Лог авторизации. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';
            
            $BODY.= '<script type="text/javascript" src="include/window.js"></script>';
            
            $BODY.='<br><table ' . $border . ' class="techreportTable">';
            $BODY.='<tr width = "99%">';

            for ($i = 0; $i < sizeof($head); $i++) {
                $BODY.='<th>' . $head[$i] . '</th>';
            }
            $BODY.='</tr>';

            $q = 'select * from pr_tech_order_login_log(\''.$stDate.'\', \'' .$endDate.'\')';
                                                 

                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['valid'] == '0') {$BODY.='<tr bgcolor="#F37D82">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}
                    
                    $BODY.='<td width = "50px">'.$r['id'].    '</td>';
                    $BODY.='<td width = "120px">'.$r['date'].    '</td>';
                    $BODY.='<td >'.$r['login'].'</td>';
                    $BODY.='<td >'.$r['ip'].'</td>';
                    $BODY.='<td >'.del_rubish($r['info']).'</td>';
                    
                   
                   }
            $BODY.='</table></body></html>';

        break;
        //ОТЧЁТ О СОБЫТИЯХ
        case '4':
            //параметры фильтра
            if (!isset($_REQUEST['tab_num']) || $_REQUEST['tab_num'] == '')
                $_REQUEST['tab_num'] = '';
            if (!isset($_REQUEST['family']) || $_REQUEST['family'] == '')
                $_REQUEST['family'] = '';
            if (!isset($_REQUEST['name']) || $_REQUEST['name'] == '')
                $_REQUEST['name'] = '';
            if (!isset($_REQUEST['secname']) || $_REQUEST['secname'] == '')
                $_REQUEST['secname'] = '';

            $tab_num = ($_REQUEST['tab_num'] == '' || !is_numeric($_REQUEST['tab_num'])) ? -1 : $_REQUEST['tab_num'];


            $head = array();
                $head[] = 'ID';
                $head[] = 'Юнит';
                $head[] = 'Тип';
                $head[] = 'Дата/время';
                $head[] = '# турникета';
                $head[] = 'id сотрудника';
                $head[] = 'Код пропуска';
		$head[] = 'Доп. поле';
            if (isset($_REQUEST['check_code_descr']))   $head[] = 'Расшифровка события';
            if (isset($_REQUEST['check_turn_name']))    $head[] = 'Название турникета';
            if (isset($_REQUEST['check_turn_group']))   $head[] = 'Группа турникетов';
            if (isset($_REQUEST['check_turn_status']))  $head[] = 'Статус';
            if (isset($_REQUEST['check_turn_terr']))    $head[] = 'Внешняя/внутренняя территорри';  
            if (isset($_REQUEST['check_fio']))          $head[] = 'ФИО';
            if (isset($_REQUEST['check_dep']))          $head[] = 'Отдел';
            if (isset($_REQUEST['check_graph']))        $head[] = 'График';
            if (isset($_REQUEST['check_dopusk']))       $head[] = 'Допуск вне графика';
            if (isset($_REQUEST['check_pers_del']))     $head[] = 'Статус удаления';
            if (isset($_REQUEST['check_code_status']))  $head[] = 'Статус';
            if (isset($_REQUEST['check_code_comm']))    $head[] = 'Комментарий';
            if (isset($_REQUEST['check_code_del']))     $head[] = 'Статус удаления';
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';
            if (isset($_REQUEST['excelflg'])) {
                $col1 = "#FFFFFF";
                $col2 = "#FFFFFF";
                $border = 'border=1';
            }
            if (!isset($_REQUEST['excelflg']))
                $BODY.= PrintHead('Отчёт', 'Отчёт о событиях. Сформирован - ' . date("d.m.Y H:i:s"));
            else
                $BODY.= '<center><b>Отчёт о событиях. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';
            
            $BODY.= '<script type="text/javascript" src="include/window.js"></script>';

            $BODY.= '<script type="text/javascript">
                         function getGraphData()
                        {//alert(this.req.responseText);
                           var xdoc = this.req.responseXML.documentElement;
                           var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
                           table+="<tr  class=client>";
                           table+="<td align=center bgcolor=silver>№</td>";
                           table+="<td align=center bgcolor=silver>Смена</td>";
                           table+="<td align=center bgcolor=silver>Допуск</td>";
                           table+="<td align=center bgcolor=silver>Зона</td>";
                           table+="</tr>";

                       var res = xdoc.getElementsByTagName("item");
                       if(res)
                         {
                          var i=0;
                          for(i=0;i<res.length;i++)
                          {
                            table+="<tr bgcolor="+res[i].getAttributeNode("bg").value+" class=clientText>";
                            table+="<td align=center >"+res[i].getAttributeNode("num").value+"</td>";
                            table+="<td align=center >"+res[i].getAttributeNode("name").value+"</td>";
                            table+="<td align=center>"+res[i].getAttributeNode("dopusk").value+"</td>";
                            table+="<td align=center>"+res[i].getAttributeNode("zone").value+"</td>";

                            table+="</tr>";
                          }
                          if(i==0) table+="<tr class=client><td>Данному графику ничего не назначено</td></tr>";

                         }
                       table+="</table>";
                           this.object.wnd.client.innerHTML+=table;
                           this.object = null;
                    }
                        function showInfo(obj,event,gname)
                        {
                        var e = event || window.event;
                        var infoWnd = new Window.poupWindow("info_wnd",e.clientY,e.clientX,window.pageXOffset-415,window.pageYOffset,400,0,"window",gname.replace("-","\ "));
                            infoWnd.Show();
                        var net = new Net.ContentLoader("asinc.php",getGraphData,Error,"POST","obj=graph&id="+obj.id);
                            net.object = infoWnd;
                       }
                       function getDopuskData()
                       {
                           var xdoc = this.req.responseXML.documentElement;
                           var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
                           table+="<tr  class=client>";
                           table+="<td nowrap align=center bgcolor=silver>Группа турникетов</td>";
                           table+="<td nowrap align=center bgcolor=silver>Режим</td>";
                           table+="<td nowrap align=center bgcolor=silver>Код режима</td>";
                           table+="</tr>";

                            var res = xdoc.getElementsByTagName("item");
                            if(res)
                                  {
                                   var i=0;
                                   for(i=0;i<res.length;i++)
                                   {
                                         table+="<tr class=clientText>";
                                                 table+="<td nowrap align=center >"+res[i].getAttributeNode("turn_group").value+"</td>";
                                                 table+="<td nowrap align=center>"+res[i].getAttributeNode("reg_name").value+"</td>";
                                                 table+="<td nowrap align=center>"+res[i].getAttributeNode("reg_code").value+"</td>";
                                         table+="</tr>";
                                   }
                                   if(i==0) table+="<tr class=client><td>Данному допуску ничего не назначено</td></tr>";

                                  }
                            table+="</table>";
                                    this.object.wnd.client.innerHTML+=table;
                                    this.object = null;
			}
                        function showInfoDop(obj,event,dname)
                        {
                        var e = event || window.event;
                        var infoDopWnd = new Window.poupWindow("infoDopWnd",e.clientY,e.clientX,window.pageXOffset-600,window.pageYOffset,400,0,"window",dname.replace("-","\ "));
                            infoDopWnd.Show();
                        var net = new Net.ContentLoader("asinc.php",getDopuskData,Error,"POST","obj=dopusk&id="+obj.id);
                            net.object = infoDopWnd;
                       }
                    </script>';
                     
            $BODY.='<br><table ' . $border . ' class="techreportTable">';
            $BODY.='<tr>';

            for ($i = 0; $i < sizeof($head); $i++) {
                $BODY.='<th>' . $head[$i] . '</th>';
            }
            $BODY.='</tr>';
            $unit = $_REQUEST['unit'] == '' ? 0 : CheckString($_REQUEST['unit']);
            $q = 'select * from pr_tech_order_events(\''.$stDate.'\', \''
                                                  .$endDate.'\', \''
                                                  .$_REQUEST['check_code'].'\', \''
                                                  .$_REQUEST['check_turn_name'].'\', \''
                                                  .$_REQUEST['check_turn_group'].'\', \''
                                                  .$_REQUEST['check_turn_status'].'\', \''
                                                  .$_REQUEST['check_turn_terr'].'\', \''
                                                  .$_REQUEST['check_fio'].'\', \''
                                                  .$_REQUEST['check_dep'].'\', \''
                                                  .$_REQUEST['check_graph'].'\', \''
                                                  .$_REQUEST['check_dopusk'].'\', \''
                                                  .$_REQUEST['check_pers_del'].'\', \''
                                                  .$_REQUEST['check_code_descr'].'\', \''
                                                  .$_REQUEST['check_code_status'].'\', \''
                                                  .$_REQUEST['check_code_comm'].'\', \''
                                                  .$_REQUEST['check_code_del'].'\', '
                                                  .$tab_num.', \''
                                                  .CheckString($_REQUEST['family']).'\', \''
                                                  .CheckString($_REQUEST['name']).'\', \''
                                                  .CheckString($_REQUEST['secname']).'\', \''
                                                    .CheckString($_REQUEST['px_code']).'\', \''
                                                  .$_REQUEST['depart'].'\', \''
                                                  .$_REQUEST['trlist'].'\', \''
                                                  .$_REQUEST['evlist'].'\', \''
                                                  .CheckString($_REQUEST['time_begin']).'\', \''
                                                  .CheckString($_REQUEST['time_end']).'\', '
                                                  .$unit.', '
                                                  .$_SESSION['iduser'].')';

                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    $BODY.='<tr bgcolor=' . $bgcolor . '>';
                    $BODY.='<td  width = "50px">'.$r['id'].    '</td>';
                    $BODY.='<td  width = "30px">'.$r['unit'].    '</td>';
                    $BODY.='<td  width = "30px">'.$r['code'].    '</td>';
                    $BODY.='<td >'.$r['timer'].    '</td>';
                    $BODY.='<td >'.$r['turn_num'].'</td>';
                    $BODY.='<td >'.$r['id_p'] .   '</td>';
                    $BODY.='<td >'.$r['px_code']. '</td>';
		    $BODY.='<td >'.$r['tmp']. '</td>';
                    if (isset($_REQUEST['check_code_descr']))   $r['id_p']<0 ?  $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['px_descr'].'</td>' : $BODY.='<td bgcolor='.$bgcolortd.'></td>';
                    //турникеты
                    if (isset($_REQUEST['check_turn_name'])){
                        //проверку, что код не Z сделаю тут, чтобы не править ещё и запрос
                        if(strpos($r['code'],'Z')===0 || strpos($r['code'],'E')===0){
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'></td>';    
                        }
                        else{
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['turn_name'];
                            if ($r['turn_name'] != ''){ 
                                $BODY.='(del='.$r['turn_del'].')';
                            }
                            $BODY.='</td>';
                        } 
                    }
                    if (isset($_REQUEST['check_turn_group'])){
                        if(strpos($r['code'],'Z')===0 || strpos($r['code'],'E')===0){
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'></td>';    
                        }
                        else{
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['turn_group_name'].'</td>';
                        } 
                    }
                    if (isset($_REQUEST['check_turn_status'])){
                        if(strpos($r['code'],'Z')===0 || strpos($r['code'],'E')===0){
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'></td>';    
                        }
                        else{
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['turn_status'].'</td>';
                        } 
                    }
                    if (isset($_REQUEST['check_turn_terr'])){
                        if(strpos($r['code'],'Z')===0 || strpos($r['code'],'E')===0){
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'></td>';    
                        }
                        else{
                            $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['turn_terr'].'</td>';
                        } 
                    }
                    //if (isset($_REQUEST['check_turn_group']))  $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['turn_group_name'] . '</td>';
                    //if (isset($_REQUEST['check_turn_status'])) $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['turn_status'] . '</td>';
                    //if (isset($_REQUEST['check_turn_terr']))   $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['turn_terr'].'</td>';
                    //сотрудники
                    if (isset($_REQUEST['check_fio']))         $BODY.='<td nowrap>'.$r['fio'].'</td>';
                    if (isset($_REQUEST['check_dep'])) $r['id_p']>0 ? $BODY.='<td nowrap>'.$r['dept'].'(del='.$r['dept_del'].')'.'</td>' : $BODY.='<td></td>';
                    if (isset($_REQUEST['check_graph'])){
                        $BODY.='<td nowrap>'.$r['graph'];
                        if ($r['id_p']>0)
                            $BODY.='<img id="'.$r['id_graph'].'" src="buttons/info3.gif" width="10" height="10" class="icons" onclick=\'showInfo(this,event,"'.str_replace(" ","-",$r['graph']).'")\'></td>';
                        else 
                            $BODY.='</td>';
                    }
                   if (isset($_REQUEST['check_dopusk'])){
                        if ($r['id_p']>0 && isset($r['dopusk']))
                           $BODY.='<td nowrap>'.$r['dopusk'].'('.$r['dopusk_st'].')'.'<img id="'.$r['id_dopusk'].'" src="buttons/info3.gif" width="10" height="10" class="icons" onclick=\'showInfoDop(this,event,"'.str_replace(" ","-",$r['dopusk']).'")\'></td>';
                        else 
                           $BODY.='<td nowrap></td>';
                    } 
                    if (isset($_REQUEST['check_pers_del']))     $BODY.='<td nowrap>'.$r['pers_del'].'</td>';
                    //пропуска
                    
                    if (isset($_REQUEST['check_code_status']))  $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['px_status'].'</td>';
                    if (isset($_REQUEST['check_code_comm']))    $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['px_comm'].'</td>';
                    if (isset($_REQUEST['check_code_del']))     $BODY.='<td nowrap bgcolor='.$bgcolortd.'>'.$r['px_del'].'</td>';
                    
                  
                   
                   }
            $BODY.='</table></body></html>';

        break;
        //ЛОГИ КОМАНД К ЮНИТАМ
        case '5':
            $head = array();
            $head[] = 'ID';
            $head[] = 'Дата/время';
            $head[] = 'Операции с таблицей команд (id, № команды, № юнита, 5 параметров , описание)';
            $head[] = 'Инициатор';
            $head[] = 'IP';
            $head[] = 'Информация';
             
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';
            if (isset($_REQUEST['excelflg'])) {
                $col1 = "#FFFFFF";
                $col2 = "#FFFFFF";
                $border = 'border=1';
            }
            if (!isset($_REQUEST['excelflg']))
                $BODY.= PrintHead('Отчёт', 'Отчёт по логу команд для юнитов. Сформирован - ' . date("d.m.Y H:i:s"));
            else
                $BODY.= '<center><b>Отчёт по логу команд для юнитов. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';
            
            $BODY.= '<script type="text/javascript" src="include/window.js"></script>';
            
            $BODY.='<br><table ' . $border . ' class="techreportTable">';
            $BODY.='<tr width = "99%">';

            for ($i = 0; $i < sizeof($head); $i++) {
                $BODY.='<th>' . $head[$i] . '</th>';
            }
            $BODY.='</tr>';

            $q = 'select * from pr_tech_order_commands_to_unit_log(\''.$stDate.'\', \'' .$endDate.'\', \'' .$_REQUEST['u_text'].'\', \'' .$_REQUEST['u_login'].'\', \'' .$_REQUEST['u_ip'].'\')';
                                                 

                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    $BODY.='<tr bgcolor=' . $bgcolor . '>';
                    $BODY.='<td width = "50px">'.$r['id'].    '</td>';
                    $BODY.='<td width = "120px">'.$r['date'].    '</td>';
                    $BODY.='<td >'.$r['descr'].    '</td>';
                    $BODY.='<td >'.$r['login'].'</td>';
                    $BODY.='<td >'.$r['ip'].'</td>';
                    $BODY.='<td >'.del_rubish($r['info']).'</td>';
                    
                   
                   }
            $BODY.='</table></body></html>';

        break;
        //НЕ ЗАГРУЖЕННЫЕ СОБЫТИЯ
        case '6':
            $BODY.= '<script type="text/javascript" src="include/window.js"></script>';
            $BODY.= '<script type="text/javascript" src="gscripts/unloaded.js"></script>';
            $BODY.= '<script type="text/javascript" src="gscripts/jquery/lib/jquery-2.1.1.js"></script>';
            $head = array();
            $head[] = 'Юнит';
            $head[] = 'С id';
            $head[] = 'По id';
            $head[] = 'Попыток загрузить <button onclick=\'set_attempts_to_1();\'>Сброс</button>';
            $head[] = 'Описание';
            $head[] = '# Архива';
            
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';
            if (isset($_REQUEST['excelflg'])) {
                $col1 = "#FFFFFF";
                $col2 = "#FFFFFF";
                $border = 'border=1';
            }
            if (!isset($_REQUEST['excelflg']))
                $BODY.= PrintHead('Отчёт', 'Отчёт по отсутствующим событиям. Сформирован - ' . date("d.m.Y H:i:s"));
            else
                $BODY.= '<center><b>Отчёт по отсутствующим событиям. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';
            
            
            
            $BODY.='<br><table ' . $border . ' class="techreportTable">';
            $BODY.='<tr width = "99%">';

            for ($i = 0; $i < sizeof($head); $i++) {
                $BODY.='<th>' . $head[$i] . '</th>';
            }
            $BODY.='</tr>';
            $full_check = $_REQUEST['full_check'] == 'on' ? 1 : 0;
            $q = 'select * from pr_tech_order_left_events('.$full_check.')';
                                                 

                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }               
                    
                    $BODY.='<tr><td width = "50px">'.$r['unit'].    '</td>';
                    $BODY.='<td width = "220px">'.$r['st_id'].    '</td>';
                    $BODY.='<td width = "220px">'.$r['end_id'].    '</td>';
                    $BODY.='<td width = "200px">'.$r['attempts'].    '</td>';
                    $BODY.='<td width = "200px">'.$r['descr'].    '</td>';
                    $BODY.='<td >'.$r['arch_num'].    '</td></tr>';
                   }
            $BODY.='</table></body></html>';

        break;
         //ОТЧЁТ ПО УПРАВЛЯЮЩИМ ДАННЫМ
        case '7':
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';
            if (isset($_REQUEST['excelflg'])) {
                $col1 = "#FFFFFF";
                $col2 = "#FFFFFF";
                $border = 'border=1';
            }
            switch ($_REQUEST['pr_type']) {
            //PASS
            case '0':
                $head = array();
                
                $head[] = 'Допуск';
                $head[] = 'График';
                $head[] = 'Код пропуска';
                $head[] = 'Статус';
                $head[] = 'pin';
                $head[] = 'Смещение в графике';

                if (!isset($_REQUEST['excelflg']))
                    $BODY.= PrintHead('Отчёт', 'Отчёт по пропускам. Сформирован - ' . date("d.m.Y H:i:s"));
                else
                    $BODY.= '<center><b>Отчёт по пропускам. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';

                $BODY.= '<script type="text/javascript" src="include/window.js"></script>';

                $BODY.='<br><table ' . $border . ' class="techreportTable">';
                $BODY.='<tr width = "99%">';

                for ($i = 0; $i < sizeof($head); $i++) {
                    $BODY.='<th>' . $head[$i] . '</th>';
                }
                $BODY.='</tr>';

                $q = 'select * from t_pass';


                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['error'] == '1') {$BODY.='<tr bgcolor="#F37D82">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}


                    $BODY.='<td >'.$r['dopusk'].'</td>';
                    $BODY.='<td >'.$r['graph'].'</td>';
                    $BODY.='<td >'.$r['code'].'</td>';
                    $BODY.='<td >'.$r['status'].'</td>';
                    $BODY.='<td >'.$r['pin'].'</td>';
                    $BODY.='<td >'.$r['graph_offset'].'</td>';
                    


                   }
                $BODY.='</table></body></html>';
            break;
            //DOPUSK
            case '1':
                $head = array();
                
                $head[] = 'Допуск';
                $head[] = 'Режим';
                $head[] = 'Турникет';
                $head[] = 'Двойные засечки на входе';
                $head[] = 'Двойные засечки на выходе';
                
                if (!isset($_REQUEST['excelflg']))
                    $BODY.= PrintHead('Отчёт', 'Отчёт по допускам. Сформирован - ' . date("d.m.Y H:i:s"));
                else
                    $BODY.= '<center><b>Отчёт по допускам. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';

                $BODY.= '<script type="text/javascript" src="include/window.js"></script>';

                $BODY.='<br><table ' . $border . ' class="techreportTable">';
                $BODY.='<tr width = "99%">';

                for ($i = 0; $i < sizeof($head); $i++) {
                    $BODY.='<th>' . $head[$i] . '</th>';
                }
                $BODY.='</tr>';

                $q = 'select * from t_dopusk';


                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['error'] == '1') {$BODY.='<tr bgcolor="#F37D82">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}


                    $BODY.='<td >'.$r['id_dopusk'].'</td>';
                    $BODY.='<td >'.$r['rej_code'].'</td>';
                    $BODY.='<td >'.$r['id'].'</td>';
                    $BODY.='<td >'.$r['t_in'].'</td>';
                    $BODY.='<td >'.$r['t_out'].'</td>';                   

                   }
                $BODY.='</table></body></html>';
            break;
            //DOCS
            case '2':
                $head = array();
                
                $head[] = 'Пропуск';
                $head[] = 'Территория';
                $head[] = 'Начало действия часы';
                $head[] = 'минуты';
                $head[] = 'Конец действия часы';
                $head[] = 'минуты';
                $head[] = 'Статус';

                if (!isset($_REQUEST['excelflg']))
                    $BODY.= PrintHead('Отчёт', 'Отчёт по документам. Сформирован - ' . date("d.m.Y H:i:s"));
                else
                    $BODY.= '<center><b>Отчёт по документам. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';

                $BODY.= '<script type="text/javascript" src="include/window.js"></script>';

                $BODY.='<br><table ' . $border . ' class="techreportTable">';
                $BODY.='<tr width = "99%">';

                for ($i = 0; $i < sizeof($head); $i++) {
                    $BODY.='<th>' . $head[$i] . '</th>';
                }
                $BODY.='</tr>';

                $q = 'select * from t_docs';
                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['error'] == '1') {$BODY.='<tr bgcolor="#F37D82">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}


                    $BODY.='<td >'.$r['code'].'</td>';
                    $BODY.='<td >'.$r['terr'].'</td>';
                    $BODY.='<td >'.$r['start_h'].'</td>';
                    $BODY.='<td >'.$r['start_m'].'</td>';
                    $BODY.='<td >'.$r['end_h'].'</td>';
                    $BODY.='<td >'.$r['end_m'].'</td>';
                    $BODY.='<td >'.$r['status'].'</td>';               

                   }
                $BODY.='</table></body></html>';
            break;
            //GRAPH
            case '3':
                $head = array();
                
                
                $head[] = 'График';
                $head[] = 'Допуск';
                $head[] = 'День';
                $head[] = 'Дата начала действия графика';

                if (!isset($_REQUEST['excelflg']))
                    $BODY.= PrintHead('Отчёт', 'Отчёт по графикам. Сформирован - ' . date("d.m.Y H:i:s"));
                else
                    $BODY.= '<center><b>Отчёт по графикам. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';

                $BODY.= '<script type="text/javascript" src="include/window.js"></script>';

                $BODY.='<br><table ' . $border . ' class="techreportTable">';
                $BODY.='<tr width = "99%">';

                for ($i = 0; $i < sizeof($head); $i++) {
                    $BODY.='<th>' . $head[$i] . '</th>';
                }
                $BODY.='</tr>';

                $q = 'select * from pr_tech_graph_report()';
                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['today'] == '1') {$BODY.='<tr bgcolor="yellow">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}

                    $BODY.='<td >'.$r['id'].'</td>';
                    $BODY.='<td >'.$r['id_dopusk'].'</td>';
                    $BODY.='<td >'.$r['num'].'</td>';
                    $BODY.='<td >'.$r['date_in'].'</td>';     
                   }
                $BODY.='</table></body></html>';
            break;
            //TURNIKET
            case '4':
                $head = array();
                
                $head[] = 'Турникет';
                $head[] = 'Территория на входе';
                $head[] = 'Территория на выходе';
                $head[] = 'Статус';
                $head[] = 'Название';
                $head[] = 'Группа турникетов';
                $head[] = 'Считка на входе';
                $head[] = 'Считка на выходе';

                $col1 = "silver";
                $col2 = "#f5f5dc";
                $bgcolor = '';
                $flag = 0;
                $border = 'border=0';
                if (isset($_REQUEST['excelflg'])) {
                    $col1 = "#FFFFFF";
                    $col2 = "#FFFFFF";
                    $border = 'border=1';
                }
                if (!isset($_REQUEST['excelflg']))
                    $BODY.= PrintHead('Отчёт', 'Отчёт по турникетам. Сформирован - ' . date("d.m.Y H:i:s"));
                else
                    $BODY.= '<center><b>Отчёт по турникетам. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';

                $BODY.= '<script type="text/javascript" src="include/window.js"></script>';

                $BODY.='<br><table ' . $border . ' class="techreportTable">';
                $BODY.='<tr width = "99%">';

                for ($i = 0; $i < sizeof($head); $i++) {
                    $BODY.='<th>' . $head[$i] . '</th>';
                }
                $BODY.='</tr>';

                $q = 'select * from t_turniket';


                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['error'] == '1') {$BODY.='<tr bgcolor="#F37D82">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}


                    $BODY.='<td >'.$r['num'].'</td>';
                    $BODY.='<td >'.$r['id_territory'].'</td>';
                    $BODY.='<td >'.$r['id_territory_out'].'</td>';
                    $BODY.='<td >'.$r['status'].'</td>';
                    $BODY.='<td >'.$r['name'].'</td>';
                    $BODY.='<td >'.$r['id_turn_group'].'</td>';
                    $BODY.='<td >'.$r['reader_in'].'</td>';
                    $BODY.='<td >'.$r['reader_out'].'</td>';
                    


                   }
                $BODY.='</table></body></html>';
            break;
            //TUNING
            case '5':
                $head = array();
                
                $head[] = 'ID';
                $head[] = 'Параметр';
                $head[] = 'Значение';
                $head[] = 'Юнит';

                $col1 = "silver";
                $col2 = "#f5f5dc";
                $bgcolor = '';
                $flag = 0;
                $border = 'border=0';
                if (isset($_REQUEST['excelflg'])) {
                    $col1 = "#FFFFFF";
                    $col2 = "#FFFFFF";
                    $border = 'border=1';
                }
                if (!isset($_REQUEST['excelflg']))
                    $BODY.= PrintHead('Отчёт', 'Отчёт по ппараметрам юнитов. Сформирован - ' . date("d.m.Y H:i:s"));
                else
                    $BODY.= '<center><b>Отчёт по параметрам юнитов. Сформирован - ' . date("d.m.Y H:i:s") . '</b></center>';

                $BODY.= '<script type="text/javascript" src="include/window.js"></script>';

                $BODY.='<br><table ' . $border . ' class="techreportTable">';
                $BODY.='<tr width = "99%">';

                for ($i = 0; $i < sizeof($head); $i++) {
                    $BODY.='<th>' . $head[$i] . '</th>';
                }
                $BODY.='</tr>';

                $q = 'select * from t_tuning where unit <= get_const_value(\'UNITS_COUNT\')::int order by unit,id';


                $result = pg_query($q);

                while ($r = pg_fetch_array($result)) {
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $bgcolortd = "#C3C1B6";
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $bgcolortd = "#DEDBCE";
                        $flag = 0;
                    }
                    if ($r['error'] == '1') {$BODY.='<tr bgcolor="#F37D82">';}
                    else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}


                    $BODY.='<td >'.$r['id'].'</td>';
                    $BODY.='<td >'.$r['name'].'</td>';
                    $BODY.='<td >'.$r['param'].'</td>';
                    $BODY.='<td >'.$r['unit'].'</td>';                  
                   }
                $BODY.='</table></body></html>';
            break;
        
	  
            default:break;
        }
        break;
        
        //Отчёты описывающие данные из схемы СКУД
        //События с ошибками RS-485
        case '101':
            $head = array();
            $head[] = 'ID';
            $head[] = 'Юнит';
            $head[] = 'Тип';
            $head[] = 'Дата/время';
            $head[] = '# турникета';
            $head[] = 'id сотрудника';
            $head[] = 'Код пропуска';
            $head[] = 'Доп. поле';
            $head[] = 'Расшифровка события';
             
            $col1 = "silver";
            $col2 = "#f5f5dc";
            $bgcolor = '';
            $flag = 0;
            $border = 'border=0';

            switch($_REQUEST['type']){
                    case 1: $REP_TYPE = 'Ошибки RS-485';
                         break;
                    case 2: $REP_TYPE = 'Незарегистрированные пропуска';
                        break;
                    case 3: $REP_TYPE = 'Нет допуска для прохода через это преграждающее устройство';
                        break;
                    case 4: $REP_TYPE = 'Попытки прохода в неурочное время';
                        break;
                    case 5: $REP_TYPE = 'Незавершенные входы/выходы, большое количество событий может означать неисправность датчика прохода';
                        break;
                    case 6: $REP_TYPE = 'Антипассбэк. Запрет двойных проходов';
                        break;
                    case 7: $REP_TYPE = 'Получен сигнал пожар';
                        break;
                    case 8: $REP_TYPE = 'Ошибка считывателя / плохо считывается код пропуска';
                        break;
                    case 9: $REP_TYPE = 'Между выделенными событиями есть потерянное событие';
                        break;
                    case 10: $REP_TYPE = 'Пропушено событие самотестирования. Нормальное наибольшее значение интервала без событий: '.$_REQUEST['interval'].' минут';
                        break;
            }         
            $BODY.= PrintHead('Отчёт', $REP_TYPE.'. Сформирован - ' . date("d.m.Y H:i:s"));
            
            //$BODY.= '<script type="text/javascript" src="include/window.js"></script>';
            
            $BODY.='<br><table ' . $border . ' class="techreportTable">';
            $BODY.='<tr width = "99%">';

            for ($i = 0; $i < sizeof($head); $i++) {
                $BODY.='<th>' . $head[$i] . '</th>';
            }
            $BODY.='</tr>';

            $q = 'select * from pr_tech_rep_for_schema('.$_REQUEST['turn'].','.$_REQUEST['type'].')';
                                                 

            $result = pg_query($q);
            $bgcolor = $col1;
            $flag = 1;
            while ($r = pg_fetch_array($result)) {
                
                if ($r['marked'] == '1') {$BODY.='<tr bgcolor="#F37D82">';}
                else {$BODY.='<tr bgcolor=' . $bgcolor . '>';}

                $BODY.='<td  width = "50px">'.$r['id'].    '</td>';
                $BODY.='<td  width = "30px">'.$r['unit'].    '</td>';
                $BODY.='<td  width = "30px">'.$r['code'].    '</td>';
                $BODY.='<td >'.$r['timer'].    '</td>';
                $BODY.='<td >'.$r['turn_num'].'</td>';
                $BODY.='<td >'.$r['id_p'] .   '</td>';
                $BODY.='<td >'.$r['px_code']. '</td>';
                $BODY.='<td >'.$r['tmp']. '</td>';
                $BODY.='<td >'.$r['descr']. '</td>';

                if ( $r['marked'] == '2' ){
                    if ($flag == 0) {//серый
                        $bgcolor = $col1;
                        $flag = 1;
                    } else {//белый
                        $bgcolor = $col2;
                        $flag = 0;
                    }
                }
            }
            $BODY.='</table></body></html>';

        break;
// ----------------------другие отчеты 	  
        default:break;
    }
}
if (isset($_REQUEST['excelflg'])) {
    $fname = 'techreport.xls';

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$fname");
    header("Expires: 0");
//header("Content-Transfer-Encoding: binary"); 
    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    header("Pragma: public");
    echo '<meta http-equiv=Content-Type content="text/html; charset=utf-8">';
}

echo $BODY;
?>