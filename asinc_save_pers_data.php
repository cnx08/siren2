<?php
include_once("include/input.php");
require_once("include/common.php");
require_once('include/hua.php');
$err = 0;
if($_REQUEST['obj'] == 'dept_check'){
    $q='select id from base_dept_s where name=\''.$_REQUEST['deptname'].'\' limit 1';
    $r=pg_fetch_assoc(pg_query($q));
    if($r['id']==null) $response = '{"id":"0"}';
    else  $response = '{"id":"'.$r['id'].'"}';

    echo $response;
}
if($_REQUEST['obj'] == 'code'){
    //Провеяем пропуска

    if(!isset($_REQUEST['pxcodenum']))$_REQUEST['pxcodenum']=NULL;
    if(!isset($_REQUEST['pxcodenum_old']))$_REQUEST['pxcodenum_old']=NULL;
    if(!isset($_REQUEST['pxcode_id']) || $_REQUEST['pxcode_id']<=0 || is_numeric($_REQUEST['pxcode_id'])<0)$_REQUEST['pxcode_id']=0;
    if(!isset($_REQUEST['pincod']))$_REQUEST['pincod']='0000';
    if(!isset($_REQUEST['status']))$_REQUEST['status']='00000000';
    if(!isset($_REQUEST['pxdatein']))$_REQUEST['pxdatein']=date("d.m.Y");
    if(!isset($_REQUEST['pxdateout']))$_REQUEST['pxdateout']='';    

    $request = '{"px_edit":';
    $q = '';
    if($_REQUEST['pxcodenum']!='')
    { 
        //проверим не привязан ли пропуск к активному сотруднику (не к тому кого редактируем)
        $q='select pr_is_pxcode_free(\''.$_REQUEST['pxcodenum'].'\','.$_REQUEST['id_pers'].')';
        $r=pg_fetch_array(pg_query($q));
        if($r['0']=='1'){//можем смело менять
            $pxdate_in = $_REQUEST['pxdatein']=='' ? NULL : $_REQUEST['pxdatein'];
            $pxdate_out = $_REQUEST['pxdateout']=='' ? NULL : $_REQUEST['pxdateout'];
            if($_REQUEST['pxcodenum']==$_REQUEST['pxcodenum_old'])
            {
                    $q='select * FROM BASE_W_U_CODES('.$_REQUEST['pxcode_id'].', \''.$pxdate_in.'\', \''.$pxdate_out.'\',\''
                    .CheckString($_REQUEST['pxcodenum']).'\',\''
                    .CheckString($_REQUEST['pincod']).'\',\''
                    .CheckString($_REQUEST['status']).'\', \''.CheckString($_REQUEST['comment']).'\')';
            }
            else
            {
                $q='select * from BASE_W_I_CODES(\''.$pxdate_in.'\', \'' .$pxdate_out.'\',\''
                    .CheckString($_REQUEST['pxcodenum']).'\',\''
                    .CheckString($_REQUEST['pincod']).'\',\''
                    .CheckString($_REQUEST['status']).'\', \''.CheckString($_REQUEST['comment']).'\')';
            }
            $r=pg_fetch_array(pg_query($q));
            if ($r['0'] > 0){
                $request .= '"'.$r['0'].'"';
            }
            else $request .= '"-1"';
        }
        else $request .= '"0"';
        
        $q='select pr_is_pers_updatable(\''.$_REQUEST['pxcodenum'].'\',\''.$_REQUEST['date_in'].'\',\''.$_REQUEST['date_out'].'\','.$_REQUEST['id_pers'].')';
        $r=pg_fetch_array(pg_query($q));
        $request .= ',"pers_edit":"'.$r['0'].'"}';//если передадим 1, то можно передавать управление в object save, если 0 то не разрешаем редактировать cотрудника.

    }
    else $request .= '"-2","pers_edit":"1"}';//пропуск в 0 при редактировании сотрудника
    echo $request;
}

$r = pg_fetch_array(pg_query('select value from base_const where name = \'base_personal_ext\''));
$ext = $r['value']; 
if ($ext != ''){
    $e_i = $_REQUEST['ext_int'];
    $e_t = $_REQUEST['ext_text'];
}
else{
    $e_i = 0;
    $e_t = '';
}

if($_REQUEST['obj'] == 'save'){
    $act = $_REQUEST['action'];
    //запрос на обновление данных  сотрудника
    if(!isset($_REQUEST['photoname'])) $_REQUEST['photoname']='';
    $zone = $_REQUEST['p_id_zone'] == '1' ? 'NULL' : $_REQUEST['id_zone'];
    $dopusk = $_REQUEST['p_id_dopusk'] == '1' ? 'NULL' : $_REQUEST['dopusk'];
    $px_code_id = $_REQUEST['pxcode_id'] < 0 || $_REQUEST['pxcode_id']=='' || $_REQUEST['pxcodenum']=='' ? 'NULL' : $_REQUEST['pxcode_id'];
    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
    $date_out = $_REQUEST['date_out']=='' ? NULL : $_REQUEST['date_out'];
    if($act=='insert')
    {
        $q.='select BASE_W_I_PERSONAL(\''.$_REQUEST['date_in'].'\',\''
                                .$date_out.'\','.CheckString($_REQUEST['tabnum']).',\''
                                .CheckString($_REQUEST['family']).'\',\''
                                .CheckString($_REQUEST['fname']).'\',\''
                                .CheckString($_REQUEST['secname']).'\','
                                .$_REQUEST['id_dept'].',\''
                                .CheckString($_REQUEST['position']).'\','
                                .$_REQUEST['graph_name'].','
                                .$px_code_id.','
                                .$zone.','.$dopusk.','
                                .$_REQUEST['id_algoritm'].',\''
                                .$_REQUEST['photoname'].'\',\''.$_REQUEST['breakfast'].'\',\''	
                                .$_REQUEST['din'].'\',\''.$_REQUEST['supper'].'\','
                                .$_REQUEST['graph_offset'].','.$e_i.',\''.$e_t.'\')';

    }
    elseif($act=='update')
    {
       $q.='select BASE_W_U_PERSONAL('.$_REQUEST['id_pers'].',\''.$_REQUEST['date_in'].'\',\''
                                .$date_out.'\','.CheckString($_REQUEST['tabnum']).',\''
                                .CheckString($_REQUEST['family']).'\',\''
                                .CheckString($_REQUEST['fname']).'\',\''
                                .CheckString($_REQUEST['secname']).'\','
                                .$_REQUEST['id_dept'].',\''
                                .CheckString($_REQUEST['position']).'\','
                                .$_REQUEST['graph_name'].','
                                .$px_code_id.','
                                .$zone.','.$dopusk.','
                                .$_REQUEST['id_algoritm'].',\''
                                .$_REQUEST['photoname'].'\',\''.$_REQUEST['breakfast'].'\',\''	
                                .$_REQUEST['din'].'\',\''.$_REQUEST['supper'].'\','
                                .$_REQUEST['graph_offset'].','.$e_i.',\''.$e_t.'\')';

    }
  
      pg_query($q);
      echo '{"res":"1"}';
}

?>
