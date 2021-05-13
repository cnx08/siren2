<?php
include('../include/input.php');
include('../include/common.php');
session_write_close();
if($_REQUEST['act']==='TUR'){

    $query = 'select * from BASE_SYS_ORDER_TECH(\''.$_REQUEST['date'].'\',\''.$_REQUEST['period'].'\',\''.$_REQUEST['turn'].'\',\''.$_REQUEST['offset'].'\',\''.$_REQUEST['overper'].'\')';
	$res = pg_query($query);

	while ($r = pg_fetch_array($res) )
	{
                $undersp = strrpos($r['over_interval'],'_');
                $res1 = substr($r['over_interval'], 0, $undersp);
                $per = substr($r['over_interval'], $undersp+1);
		if($r['period']!='day'){
                    $BODY .= '<tr bgcolor="#E9EFF8" id="'.$r['period'].'_'.$r['num'].'-'.$per.'xTUR" onclick=details(this,"'.$r['start'].'")>';
                }
                else $BODY .= '<tr bgcolor="#EDEDED">';
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
    
    
    echo $BODY;
}
if($_REQUEST['act']==='USB'){

    $query = 'select * from BASE_SYS_ORDER_TECH_USB(\''.$_REQUEST['date'].'\',\''.$_REQUEST['period'].'\',\''.$_REQUEST['turn'].'\',\''.$_REQUEST['offset'].'\',\''.$_REQUEST['overper'].'\')';
	$res = pg_query($query);

	while ($r = pg_fetch_array($res) )
	{
                $undersp = strrpos($r['over_interval'],'_');
                $res1 = substr($r['over_interval'], 0, $undersp);
                $per = substr($r['over_interval'], $undersp+1);
		if($r['period']!='day'){
                    $BODY .= '<tr bgcolor="#E9EFF8" id="'.$r['period'].'_'.$r['num'].'-'.$per.'xUSB" onclick=details(this,"'.$r['start'].'")>';
                }
                else $BODY .= '<tr bgcolor="#EDEDED">';
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
    
    
    echo $BODY;
}
if($_REQUEST['act']==='UNIT'){

    $query = 'select * from BASE_SYS_ORDER_TECH_UNIT(\''.$_REQUEST['date'].'\',\''.$_REQUEST['period'].'\',\''.$_REQUEST['turn'].'\',\''.$_REQUEST['offset'].'\')';
	$res = pg_query($query);

	while ($r = pg_fetch_array($res) )
	{
		if($r['period']!='day'){
                    $BODY .= '<tr bgcolor="#E9EFF8" id="'.$r['period'].'_'.$r['num'].'-0xUNIT" onclick=details(this,"'.$r['start'].'")>';
                }
                else $BODY .= '<tr bgcolor="#EDEDED">';
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
    
    
    echo $BODY;
}

if($_REQUEST['act']==='schema_turn'){

    $query = 'select * from pr_get_turn_statistics('.$_REQUEST['turn'].')';
	$res = pg_query($query);


        $rows = pg_num_rows($res);
        if ($rows==0){
            $req = '{"res":"0"}';
        }
        if ($rows>0){
            while($r=pg_fetch_array($res))
            {
                $req = '{"res":"1"';
                $req .= ',"count":"'.$r['count'].'"';
                $req .= ',"uncomplete":"'.$r['uncomplete'].'"';
                $req .= ',"uncomplete_p":"'.$r['uncomplete_p'].'"';
                $req .= ',"x":"'.$r['x'].'"';
                $req .= ',"x_p":"'.$r['x_p'].'"';
                $req .= ',"w":"'.$r['w'].'"';
                $req .= ',"w_p":"'.$r['w_p'].'"';
                $req .= ',"d":"'.$r['d'].'"';
                $req .= ',"d_p":"'.$r['d_p'].'"';
                $req .= ',"apb":"'.$r['apb'].'"';
                $req .= ',"apb_p":"'.$r['apb_p'].'"';
                $req .= ',"c":"'.$r['c'].'"';
                $req .= ',"c_p":"'.$r['c_p'].'"';
                $req .= ',"fire":"'.$r['fire'].'"';
                $req .= ',"err_reader":"'.$r['err_reader'].'"';
                $req .= ',"doublek":"'.$r['doublek'].'"';
                $req .= ',"over_interval":"'.$r['over_interval'].'"';
                $req .= '}';
            }
        }

    echo $req;
}
if($_REQUEST['act']==='schema_serv'){

    $query = 'select * from pr_get_serv_statistics()';
	$res = pg_query($query);


        $rows = pg_num_rows($res);
        if ($rows==0){
            $req = '{"res":"0"}';
        }
        if ($rows>0){
            while($r=pg_fetch_array($res))
            {
                $req = '{"res":"1"';
                $req .= ',"tbl_log":"'.$r['tbllog'].'"';
                $req .= ',"left_ev":"'.$r['left_ev'].'"';
                
                $req .= '}';
            }
        }

    echo $req;
}