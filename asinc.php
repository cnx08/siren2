<?php

include('include/input.php');
include('include/common.php');
require_once('include/hua.php');
header('Content-Type: text/xml');

session_write_close();


if(!isset($_REQUEST['obj']))$_REQUEST['obj'] = '';
if(!isset($_REQUEST['act']))$_REQUEST['act'] = '';

if($_REQUEST['obj'] == ''){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
       $xml .= '<object>error</object>';
       $xml .= '<text>Не распознан объект</text>';
  $xml .= '</response>';
  echo $xml;
  exit();
}

$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
if($_REQUEST['obj'] == 'turn'){

    if($_REQUEST['act'] == 'INSERT')
    {
        $q .= 'select * from BASE_W_I_TURN('.$_REQUEST['tnum']
                            .','.$_REQUEST['turngroup']
                            .',\''.CheckString($_REQUEST['tname'])
                            .'\',\''.CheckString($_REQUEST['turndesc'])
                            .'\',\''.$_REQUEST['status']
                            .'\','.$_REQUEST['turn_type']
                            .','.$_REQUEST['reader_in']
                            .','.$_REQUEST['reader_out']
                            .','.$_REQUEST['in_terr']
                            .','.$_REQUEST['out_terr'].')';

        $res = pg_query($q);
        $r = pg_fetch_array($res);
        if($r['0'] < 0)
        {
            $response = '{"action":"INSERT"'
                    . ', "id":"-1"'
                    . ', "desc":"Турникет с номером - '.$_REQUEST['tnum'].' уже существует"}';
        }
        else
        {
            if($_REQUEST['turngroup']=='NULL')$turngroup = 0;else $turngroup = $_REQUEST['turngroup'];
            $response = '{"action":"INSERT"'
                    . ', "id":"'.$r['0'].'"'
                    . ', "num":"'.$_REQUEST['tnum'].'"'
                    . ', "name":"'.$_REQUEST['tname'].'"'
                    . ', "tg":"'.$turngroup.'"}';
        }
    }
    if($_REQUEST['act'] == 'edit')
    {
        $q = 'select * from BASE_W_S_TURN('.$_REQUEST['tid'].')';
        $res = pg_query($q);
        $r = pg_fetch_array($res);
        if($r['id_turn_group']=='')$turngroup = 0;else $turngroup = $r['id_turn_group'];
        $response = '{"action":"edit"'
                . ', "id":"'.$r['id'].'"'
                . ', "num":"'.$r['num'].'"'
                . ', "name":"'.$r['name'].'"'
                . ', "tg":"'.$turngroup.'"'
                . ', "desc":"'.$r['description'].'"'
                . ', "status":"'.$r['status'].'"'
                . ', "turn_type":"'.$r['turn_type'].'"'
                . ', "reader_in":"'.$r['reader_in'].'"'
                . ', "reader_out":"'.$r['reader_out'].'"'
                . ', "interr":"'.$r['id_territory'].'"'
                . ', "outterr":"'.$r['id_territory_out'].'"}';
    }
    if($_REQUEST['act']=='UPDATE')
    {
        $q .= 'select * from BASE_W_U_TURN('.$_REQUEST['tid'].''
                . ','.$_REQUEST['tnum']
                . ','.$_REQUEST['turngroup']
                . ',\''.CheckString($_REQUEST['tname']).'\''
                . ',\''.CheckString($_REQUEST['turndesc']).'\''
                . ',\''.$_REQUEST['status'].'\''
                . ','.$_REQUEST['turn_type']
                .','.$_REQUEST['reader_in']
                .','.$_REQUEST['reader_out']
                . ','.$_REQUEST['in_terr']
                . ','.$_REQUEST['out_terr'].')';
        $res = pg_query($q);

        $r = pg_fetch_array($res);
        if($r['id_turn_group']=='')$turngroup = 0;else $turngroup = $r['id_turn_group'];
          $response = '{"action":"edit"'
                . ', "id":"'.$r['id'].'"'
                . ', "num":"'.$r['num'].'"'
                . ', "name":"'.$r['name'].'"'
                . ', "tg":"'.$turngroup.'"'
                . ', "desc":"'.$r['description'].'"'
                . ', "status":"'.$r['status'].'"'
                . ', "turn_type":"'.$r['turn_type'].'"'
                . ', "reader_in":"'.$r['reader_in'].'"'
                . ', "reader_out":"'.$r['reader_out'].'"'
                . ', "interr":"'.$r['id_teriitory'].'"'
                . ', "outterr":"'.$r['id_teriitory_out'].'"}';
    }
    if($_REQUEST['act'] == 'REMOVE' && isset($_REQUEST['tid']) && IdValidate($_REQUEST['tid']) == true)
    {
        $q .= 'select BASE_W_D_TURN('.$_REQUEST['tid'].')';
       pg_query($q);
        $response = '{"action":"REMOVE","id":"'.$_REQUEST['tid'].'"}';
    }
    echo $response;
    exit();
}
if($_REQUEST['obj'] == 'graph' ){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
  $xml .= '<object>'.$_REQUEST['obj'].'</object>';
  $xml .= '<action>'.$_REQUEST['act'].'</action>';
  $xml .= '<view>list</view>';
  $xml.='<result>';
  $persid= $_REQUEST['persId'] != '' ? $_REQUEST['persId'] : 0;
  $q = 'select * from BASE_W_S_GRAPH_INFO('.$_REQUEST['id'].','.$persid.')';
  $result = pg_query($q);
  $col = 'white';
  $num = 1;
  while($r = pg_fetch_array($result))
  {
      if($r['flag'] == 1)$col = 'yellow'; else $col = 'white';

      $xml.='<item num="'.$num.'" name="'.$r['name_sm'].'" dopusk="'.$r['name_dopusk'].'" zone="'.$r['name_zone'].'" bg="'.$col.'" />';
      $num++;
  }
  $xml.='</result>';

  $xml .= '</response>';
  echo $xml;
  exit();
}
//В тех отчёте для иконки получения инфы о допуске, BASE_W_S_TURN_GROUP_REG не запилена
if($_REQUEST['obj'] == 'dopusk' ){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
  $xml .= '<object>'.$_REQUEST['obj'].'</object>';
  $xml .= '<action>'.$_REQUEST['act'].'</action>';
  $xml .= '<view>list</view>';
  $xml.='<result>';
  $q = 'select * from BASE_W_S_TURN_GROUP_REG('.$_REQUEST['id'].')';
  $result = pg_query($q);
  $col = 'white';
  $num = 1;
  while($r = pg_fetch_array($result))
  {
      $xml.='<item num="'.$num.'" turn_group="'.$r['turn_group_name'].'" reg_name="'.$r['reg_name'].'" reg_code="'.$r['reg_code'].'"/>';
      $num++;
  }
  $xml.='</result>';

  $xml .= '</response>';
  echo $xml;
  exit();
}

if($_REQUEST['obj'] == 'CntEvents' ){//techreport
  $q = 'SELECT COUNT(*) as cnt FROM base_events WHERE TIME between \''. $_REQUEST['st_date'] .' 00:00:00\' and \''. $_REQUEST['en_date'].' 23:59:59\'';
  $result = pg_query($q);
  while($r = pg_fetch_array($result))
  {
      $xml=$r['cnt'];//the best xml ever :D
  }
  echo $xml;
  exit();
}
if($_REQUEST['obj'] == 'adddept'){
  if(isset($_REQUEST['deptname']))$new_dept = str_replace('"',"",$_REQUEST['deptname']);
    $par_id = $_REQUEST['idparent'];
    $lesee = 0;
    $q .= 'select * from BASE_W_I_DEPT(\''.$new_dept.'\', \''.$par_id.'\', \''.$lesee.'\')';
    $res=pg_query($q);
  while($r = pg_fetch_array($res))
  { 
      $id_dept=$r['id'];
      $dept_name=str_replace('"',"",$r['name']);
  }
    $response = '{';
    $response .= '"id_dept":"'.$id_dept.'",';
    $response .= '"dept_name":"'.$dept_name.'"';
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'addPxcode'){
	$q.='select pr_is_pxcode_free(\''.$_REQUEST['pxcode'].'\','.$_REQUEST['pers_id'].')';
        $r=pg_fetch_array(pg_query($q));
        if($r['0']=='1'){//можем смело менять
	    $q = 'select * from BASE_W_I_CODES(NULL,NULL,\''.$_REQUEST['pxcode'].'\',\'zzzz\',\'00000000\',\'dont_update_if_exists\')';
	    $res = pg_fetch_array(pg_query($q));
	    $id_codes = $res['0'];
	    if ($id_codes==-1)
	    {
		$response = '{"res":"Этот пропуск кому-то принадлежит"}';
		echo $response;
	    }
	    else {
		$q = 'update base_personal set id_codes='.$id_codes.' where id='.$_REQUEST['pers_id'];
		$res=pg_query($q);
        
		$response = '{"res":"1"}';
		echo $response;
	    }
	}
	else echo '{"res":"Данный пропуск уже кому-то назначен"}';
		
}
if($_REQUEST['obj'] == 'addPhoto'){

        $q .= 'update base_personal set PHOTO=\''.str_replace('"',"",$_REQUEST['phname']).'\' where id='.$_REQUEST['pers_id'].'';
        $res=pg_query($q);
        
        $response = '{"res":"1"}';
        echo $response;
}
if($_REQUEST['obj'] == 'addSmena'){
    $q.='select * from BASE_W_I_SMENA(\''.$_REQUEST['start_sm'].'\',
                       \''.$_REQUEST['end_sm'].'\',
                       \''.$_REQUEST['start_din'].'\',
                       \''.$_REQUEST['end_din'].'\',
                       \''.str_replace('"',"",CheckString($_REQUEST['namesm'])).'\',
                       \''.str_replace('"',"",CheckString($_REQUEST['descrip'])).'\')';
    $res=pg_query($q);
    while($r = pg_fetch_array($res))
    { 
        $id_sm=$r['id'];
        $sm_name=$r['name'];
    }
    $response = '{';
    $response .= '"id_sm":"'.$id_sm.'",';
    $response .= '"sm_name":"'.str_replace('"',"",$sm_name).'"';
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'addZone'){
    
    $SS=array();
    if(isset($_REQUEST['terr_arr']) && $_REQUEST['terr_arr']!='')
    $SS=explode(",",$_REQUEST['terr_arr']);

    $q.='select * from BASE_W_I_ZONE(\''.str_replace('"',"",CheckString($_REQUEST['name'])).'\',\''.str_replace('"',"",CheckString($_REQUEST['discr'])).'\')';
    $r=pg_fetch_array(pg_query($q));
    $id=$r['id'];
    $name=$_REQUEST['name'];
    if(sizeof($SS)!=0)
    {
      for($i=0;$i<sizeof($SS);$i++)
      {
        $q='select BASE_W_I_ZONE_TERR('.$id.','.$SS[$i].',0)';
        pg_query($q) or die("Ошибка при добавлении территорий");
      }
    }
    $response = '{';
    $response .= '"id_z":"'.$id.'",';
    $response .= '"z_name":"'.$name.'"';
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'addDopusk'){
    $q.='select * from BASE_W_I_DOPUSK(\''.str_replace('"',"",CheckString($_REQUEST['name'])).'\',\''.CheckString($_REQUEST['dop_status']).'\')';
    $r=pg_fetch_array(pg_query($q));
    $id=$r['id'];
    $name=$_REQUEST['name'];
    foreach ($_REQUEST['tgreg'] as $key => $value)
    {
        $q='select BASE_W_I_DOPUSK_REG('.$id.','. $key.','.$value.',0)';
        pg_query($q) or die("Ошибка при добавлении допуска");
    }
    $response = '{';
    $response .= '"id_d":"'.$id.'",';
    $response .= '"d_name":"'.$name.'"';
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'addReg'){
    $q.='select BASE_W_I_REG_NAME(\''.str_replace('"',"",$_REQUEST['name']).'\',\''.$_REQUEST['reg_code'].'\')'; 
    $res=pg_query($q);
    $result=pg_query('select id,name from BASE_REG_NAME where name=\''.str_replace('"',"",$_REQUEST['name']).'\'');
    while($r=pg_fetch_array($result))
    {//need check for equal names
        $response = '{';
        $response .= '"id_r":"'.$r['id'].'",';
        $response .= '"r_name":"'.str_replace('"',"",$r['name']).'"';
        $response .= '}';
    }   
    echo $response;
}
if($_REQUEST['obj'] == 'addPers'){
    if($_REQUEST['act'] == 'getSmena')
    {    
        $q='select * from BASE_W_S_SMENA(NULL)';
        $res=pg_query($q);
        $response = '{"1_0":"Не выбрана"';
        while($r=pg_fetch_array($res))
        {
           $response .= ',';
           $response .= '"s_'.$r['id'].'":"'.str_replace('"',"",$r['name']).'"';//префиксы у ид для сохранения порядка при передаче json
        }
        $response .= '}';
        echo $response;
    }
    if($_REQUEST['act'] == 'getDopusk')
    {    
        $q='select * from BASE_W_S_DOPUSK(NULL)';
        $res=pg_query($q);
        $response = '{"1_0":"Не выбран"';
        while($r=pg_fetch_array($res))
        {
           $response .= ',';
           $response .= '"d_'.$r['id'].'":"'.str_replace('"',"",$r['name']).'"';
        }
        $response .= '}';
        echo $response;
    }
    if($_REQUEST['act'] == 'getZone')
    {    
        $q='select * from BASE_W_S_ZONE(NULL)';
        $res=pg_query($q);
        $response = '{"1_0":"Не выбрана"';
        while($r=pg_fetch_array($res))
        {
           $response .= ',';
           $response .= '"z_'.$r['id'].'":"'.str_replace('"',"",$r['name']).'"';
        }
        $response .= '}';
        echo $response;
    }
    if($_REQUEST['act'] == 'save')
    {    
        $q.='select * from BASE_W_I_GRAPH_NAME(\''.str_replace('"',"",$_REQUEST['gname']).'\',
                                \''.CheckString($_REQUEST['gdate']).'\',
                                \''.CheckString(str_replace('"',"",$_REQUEST['descript'])).'\')';
        $r=pg_fetch_array(pg_query($q));
        $id=$r['id'];
        if($_REQUEST['itognewval']!='')
        {
            $NV=explode(";",$_REQUEST['itognewval']);
            if(sizeof($NV)>0)
            {
                for($i=0;$i<sizeof($NV);$i++)
                {
                    $item=explode(",",$NV[$i]);
                    $q='select BASE_W_U_GRAPH('.$id.','.$item[0].','.$item[1].','.$item[2].')';
                    pg_query($q);
                }
            }
        }
        echo '{"'.$r['id'].'":"'.str_replace('"',"",$r['name']).'"}';
    }
}
if($_REQUEST['obj'] == 'getSmena' ){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
  $xml .= '<object>'.$_REQUEST['obj'].'</object>';
  $xml .= '<action>'.$_REQUEST['act'].'</action>';
  $xml .= '<view>list</view>';
  $xml.='<result>';
  $q = 'select * from BASE_W_S_SMENA('.$_REQUEST['id'].')';
  $result = pg_query($q);
  while($r = pg_fetch_array($result))
  {
      $xml.='<item start_sm="'.$r['start_sm'].'" end_sm="'.$r['end_sm'].'" start_din="'.$r['start_din'].'" end_din="'.$r['end_din'].'" />';
  }
  $xml.='</result>';

  $xml .= '</response>';
  echo $xml;
  exit();
}
if($_REQUEST['obj'] == 'getDopusk' ){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
  $xml .= '<object>'.$_REQUEST['obj'].'</object>';
  $xml .= '<action>'.$_REQUEST['act'].'</action>';
  $xml .= '<view>list</view>';
  $xml.='<result>';
  $q = 'select * from BASE_W_S_DOPUSK_TURN('.$_REQUEST['id'].')';
  $result = pg_query($q);
  while($r = pg_fetch_array($result))
  {
      $xml.='<item tg_id="'.$r['turn_id'].'" tg_name="'.$r['turn_name'].'" reg_id="'.$r['reg_id'].'" reg_name="'.$r['reg_name'].'" />';
  }
  $xml.='</result>';

  $xml .= '</response>';
  echo $xml;
  exit();
}
if($_REQUEST['obj'] == 'getZona' ){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
  $xml .= '<object>'.$_REQUEST['obj'].'</object>';
  $xml .= '<action>'.$_REQUEST['act'].'</action>';
  $xml .= '<view>list</view>';
  $xml.='<result>';
  $q = 'select * from BASE_W_S_ZONE_TERR('.$_REQUEST['id'].')';
  $result = pg_query($q);
  while($r =pg_fetch_array($result))
  {
      $xml.='<item terr="'.$r['name'].'"/>';
  }
  $xml.='</result>';

  $xml .= '</response>';
  echo $xml;
  exit();
}
if($_REQUEST['obj'] == 'getTurn' ){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
  $xml .= '<object>'.$_REQUEST['obj'].'</object>';
  $xml .= '<action>'.$_REQUEST['act'].'</action>';
  $xml .= '<view>list</view>';
  $xml.='<result>';
  $q = 'select * from BASE_W_S_TURN_IN_GROUP_SIMPLE('.$_REQUEST['id'].')';
  $result = pg_query($q);
  while($r = pg_fetch_array($result))
  {
      $xml.='<item name="'.$r['name'].'" num="'.$r['num'].'" />';
  }
  $xml.='</result>';

  $xml .= '</response>';
  echo $xml;
  exit();
}
if($_REQUEST['obj'] == 'getRegInfo' ){
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
  $xml .= '<object>'.$_REQUEST['obj'].'</object>';
  $xml .= '<action>'.$_REQUEST['act'].'</action>';
  $xml .= '<view>list</view>';
  $xml.='<result>';
  $result=pg_query('select rej_code from BASE_REG_NAME where id='.$_REQUEST['id']);
  while($r = pg_fetch_array($result))
  {
      $xml.='<item rej_code="'.$r['rej_code'].'" />';
  }
  $xml.='</result>';

  $xml .= '</response>';
  echo $xml;
  exit();
}

if($_REQUEST['obj'] == 'loadterr'){
    $response = '{"0":""';
    $result=pg_query('select * from BASE_W_S_TERRITORY(NULL)');
    while($r=pg_fetch_array($result))
    {
        $response.=',"'.$r['id'].'":"'.str_replace('"',"",$r['name']).'"';
    }   
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'getTurnGroups'){
    $response = '{"0":""';
    $result=pg_query('select id,name from base_turn_group where delete=\'0\'');
    while($r=pg_fetch_array($result))
    {
        $response.=',"'.$r['id'].'":"'.str_replace('"',"",$r['name']).'"';
    }   
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'getReg'){
    $response = '{"0":""';
    $result=pg_query('select id,name from BASE_REG_NAME where delete=\'0\'');
    while($r=pg_fetch_array($result))
    {
        $response.=',"'.$r['id'].'":"'.str_replace('"',"",$r['name']).'"';
    }   
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'checklogin' && strlen($_REQUEST['login1'])>0){
    if (!preg_match('/[^A-Za-z0-9]/', $_REQUEST['login1']))
    {
        $q='select id from BASE_USERS where login = \''.$_REQUEST['login1'].'\' and delete = \'0\'';
        $result=pg_query($q);
        $rows = pg_num_rows($result);
        if ($rows==0){
            $req = '{"res":"0"}';
        }
        if ($rows==1){
            while($r=pg_fetch_array($result))
            {
                $req = '{"res":"'.$r['id'].'"}';
            }
        }
        if ($rows>1){//a vdrug?
            $req = '{"res":"1500"}';
        }
    }
    else $req = '{"res":"-5"}';
    echo $req;

}
if($_REQUEST['obj'] == 'unit'){
    $response = '{"0":""';
    $result=pg_query('select * from base_w_s_tuning('.$_REQUEST['unit'].');');
    while($r=pg_fetch_array($result))
    {
        $response.=',"m'.$r['id'].'":"'.$r['param'].'"';
    }   
    $response .= '}';
    echo $response;
}

if($_REQUEST['obj'] == 'attempts'){
    $response = '{"0":""';
    pg_query('update left_event_ids set attempts = 1;');
       
    $response .= '}';
    echo $response;
}
if($_REQUEST['obj'] == 'comm_to_unit'){
    if(is_numeric($_REQUEST['num']) && is_numeric($_REQUEST['unit'])){
        if($_REQUEST['num']==1) $comm = 'restart-os';
        if($_REQUEST['num']==2) $comm = 'restart-unit';
        if($_REQUEST['num']==3) $comm = 'stop-os';
        $q.='insert into commands_to_unit (c_code, unit, param4, descr) values(9,'.$_REQUEST['unit'].',\''.$comm.'\',\'Выполнить коммандный файл!\')';
        
        pg_query($q);

        echo '{"0":""}';
    }
}
if($_REQUEST['obj'] == 'edit_units'){
    if($_POST["id"] > 0 && $_POST["unit"] >= 0){
        $q.='select get_units_info();';
        $q.='select pr_edit_unit('.$_POST["id"].','.$_POST["unit"].',\''.$_POST["name"].'\');';
        $result=pg_query($q);
        while($r=pg_fetch_array($result))
        {
            $response='{"result":"'.$r['0'].'"}';
        }   
    echo $response;
    }
    else{
        echo '{"result":"-1"}';
    }
}
if($_REQUEST['obj'] == 'add_unit'){
    if($_POST["id"] > 0 && $_POST["unit"] >= 0){
        $q.='select pr_add_unit('.$_POST["id"].','.$_POST["unit"].',\''.$_POST["name"].'\');';
        $result=pg_query($q);
        while($r=pg_fetch_array($result))
        {
            $response='{"result":"'.$r['0'].'"}';
        }   
    echo $response;
    }
    else{
        echo '{"result":"-4"}';
    }
}
if($_REQUEST['obj'] == 'get_unit_conf'){
    if($_POST["unit"] > 0){
        $q.='select * from base_w_s_tuning('.$_POST['unit'].');';
        if($result=pg_query($q)){
            $response='{"result":"1"';
            while($r=pg_fetch_array($result))
            {
                $response.=', "'.$r['name'].'":"'.$r['param'].'"';
            } 
            $response.='}';
        }
        else{
            echo '{"result":"-1"}';
        }
    echo $response;
    }
    else{
        echo '{"result":"-1"}';
    }
}
if($_REQUEST['obj'] == 'save_unit_conf'){
    if($_POST["unit"] > 0){
        $time_dbl_pass = is_numeric($_POST["time_dbl_pass"]) == false ? 15 : $_POST["time_dbl_pass"];
        $status = $_POST["status"] == 'on' || $_POST["status"] == 'true' ? 1 : 0;
        $q.='select BASE_W_U_TUNING('.$_POST["unit"].','.$_POST["log_level_dm"].','.$_POST["log_level_srt"].','.$_POST["prop_cnt"]
                .','.$_POST["time_cnt"].','.$_POST["out_time_cnt"].','.$time_dbl_pass.','.$_POST["hard_err_reboot"]
                .','.$_POST["sunday_reboot"].','.$_POST["timer_corr"].','.$_POST["off_line_start"].','.$status.')';
        if(pg_query($q)){
            echo '{"result":"1"}';
        }
        else{
            echo '{"result":"-1"}';
        }
    }
    else echo '{"result":"-1"}';
}
if($_REQUEST['obj'] == 'del_unit'){
    if($_POST["unit"] >= 0){
        $q.='select BASE_W_D_UNIT('.$_POST["unit_id"].','.$_POST["unit"].');';
        if(pg_query($q)){
            echo '{"result":"1"}';
        }
        else{
            echo '{"result":"-1"}';
        }
    }
    else echo '{"result":"-1"}';
}
?>