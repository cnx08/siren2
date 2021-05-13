<?php
include('../include/input.php');
include('../include/common.php');
if($_REQUEST['act']==='search'){

    if (isset($_REQUEST['id_pers'])){
        $fam     = '';
        $name    = '';
        $secname = '';
        $dept    = 0;
        $code    = '';
        $id_pers = substr($_REQUEST['id_pers'],2);
        $token   = substr($_REQUEST['id_pers'],0,1);
        $stage   = 2;
    }
    else{
        $fam     = str_replace('"',"",$_REQUEST['fam']);
        $name    = str_replace('"',"",$_REQUEST['name']);
        $secname = str_replace('"',"",$_REQUEST['sec']);
        $dept    = intval($_REQUEST['dept']);
        $code    = str_replace('"',"",$_REQUEST['code']);
        $id_pers = 0;
        $token   = '';
        $stage   = 1;
    }
    $q='select * from  pr_pers_info( \''.$fam.'\',
                            \''.$name.'\',
                            \''.$secname.'\',
                            '. $dept.',
                            \''.$code.'\',
                            '. $id_pers.',
                            \''.$token.'\',
                            '. $stage.')';
    $res=pg_query($q);
    $response = '{';
    $rows=0;
    while($r=pg_fetch_array($res))
    {
        if ($r['result'] == 0)
        {
            echo '{"res":"0"}';
            return;
        }
        if ($r['result'] == 1)
        {
            $response .= '"photo":"'.CheckString2($r['photo']).'",';
            $response .= '"name":"'.CheckString2($r['name']).'",';
            $response .= '"fam":"'.CheckString2($r['family']).'",';
            $response .= '"sec":"'.CheckString2($r['secname']).'",';
            $response .= '"dept":"'.CheckString2(str_replace('"',"",$r['dept'])).'",';
            $response .= '"code":"'.CheckString2($r['code']).'",';
            $response .= '"pos":"'.CheckString2($r['pos']).'",';
            $response .= '"id_dop":"'.CheckString2($r['id_dopusk']).'",';
            $response .= '"id_graph":"'.CheckString2($r['id_graph']).'",';
            if ($r['token'] === 'p'){
                
                $response .= '"gn":"'.CheckString2($r['graph_name']).'",';
                $response .= '"graph_offset":"'.CheckString2($r['graph_offset']).'",';
                $response .= '"stsm":"'.CheckString2($r['start_sm']).'",';
                $response .= '"endsm":"'.CheckString2($r['end_sm']).'",';
                $response .= '"stdin":"'.CheckString2($r['start_din']).'",';
                $response .= '"enddin":"'.CheckString2($r['end_din']).'",';

                $response .= '"token":"p",';
            }
            if ($r['token'] === 'g'){
                $response .= '"old":"'.$r['old'].'",';
                $response .= '"vpos":"'.CheckString2($r['vpos']).'",';
                $response .= '"com":"'.CheckString2($r['comment']).'",';
                $response .= '"pasp":"'.CheckString2($r['pasport']).'",';
                $response .= '"dop":"'.CheckString2($r['dop']).'",';
                $response .= '"date_in":"'.CheckString2($r['date_in']).'",';
                $response .= '"date_out":"'.CheckString2($r['date_out']).'",';
                $response .= '"towho":"'.CheckString2($r['towho']).'",';

                $response .= '"token":"g",';
            }
            $response .= '"res":"1"';
            $response .= '}';
            echo $response;
            return;
        }
        if ($r['result'] > 1)
        {
            $rows = $r['result'];
            $response .= '"'.$r['tid'].'":{';
            $response .= '"old":"'.$r['old'].'",';
            $response .= '"id_pers":"'.$r['token'].'_'.$r['id_p'].'",';
            $response .= '"name":"'.CheckString2($r['name']).'",';
            $response .= '"fam":"'.CheckString2($r['family']).'",';
            $response .= '"sec":"'.CheckString2($r['secname']).'",';
            $response .= '"pos":"'.CheckString2($r['pos']).'",';
            $response .= '"dept":"'.CheckString2($r['dept']).'",';
            $response .= '"pasport":"'.CheckString2($r['pasport']).'",';
            $response .= '"com":"'.CheckString2($r['comment']).'"';
            
            $response .= '},'; 
        }
    }   
    $response .= '"res":"'.$rows.'"}';
    echo $response;
    return;
}
if($_REQUEST['act']==='get_dept'){
    
    $q='select id, name from base_dept where delete=\'0\'';
    $res=pg_query($q);
    $response = '{"0":"'.'Выбрать отдел'.'"';
   
    while($r=pg_fetch_array($res))
    {
        $response .= ',"'.$r['id'].'":"'.CheckString2($r['name']).'"';
    }   
    $response .= '}';
    echo $response;
}
if($_REQUEST['act']==='get_turn'){
    
    $q='select * from  pr_get_turns_by_dopusk('.$_REQUEST['id_gr'].','.$_REQUEST['id_dop'].','.$_REQUEST['graph_offset'].')';
                                
    $res=pg_query($q);
   
    $rows = 0;
    $response = '{';
    while($r=pg_fetch_array($res))
    {
        $rows++;
        $response .= '"'.$rows.'":"'.CheckString2($r[0]).'",';
    }
    $response .= '"res":"'.$rows.'"}';
    
    echo $response;
}