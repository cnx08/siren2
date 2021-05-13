<?php

 ob_start();


include_once("include/input.php");
require_once("include/head.php");
require_once("include/common.php");
require_once('include/hua.php');

$act = $_REQUEST['act'];

  $error='';
  //проверяем вылидность данных
  //табельный номер
  if(!isset($_REQUEST['tabnum'])) $error .= '<li>Не назначен табельный номер</li>';
  else
  if(is_numeric($_REQUEST['tabnum'])<0) $error .= '<li>Табельный номер должен быть чилом</li>';
  //дата увольнения
  if(isset($_REQUEST['date_out']) && $_REQUEST['date_out']!='')
  {
     if(isDate($_REQUEST['date_out'],".")==false)$error .= '<li>Некорректный формат даты увольнения</li>';
  }
  else
   {
    $_REQUEST['date_out']='';
   }
  if(!isset($_REQUEST['family']))$error.='<li>Не введена фамилия</li>';
  if(!isset($_REQUEST['fname']))$error.='<li>Не введено имя</li>';
  if(!isset($_REQUEST['secname']))$error.='<li>Не введено отчество</li>';
  if(!isset($_REQUEST['position']))$error.='<li>Не назначена должность</li>';
  if(!isset($_REQUEST['id_dept']) || $_REQUEST['id_dept']<=0 || is_numeric($_REQUEST['id_dept'])<0)
  {
      $q='select id from base_dept where name=\''.$_REQUEST['deptName'].'\'';
      $r=pg_fetch_assoc(pg_query($q));
      if($r['id']==null)
      {
         $error.='<li>Введённый отдел не существует</li>';
      }
      else  $_REQUEST['id_dept'] = $r['id'];
      
  }
  if(!isset($_REQUEST['graph_name']) || $_REQUEST['graph_name']<=0 || is_numeric($_REQUEST['graph_name'])<0)$_REQUEST['graph_name']=0;
  if(!isset($_REQUEST['graph_offset']) || $_REQUEST['graph_offset']<0 || $_REQUEST['graph_offset']>29) $error.='<li>Смещение графика должно быть от 0 до 30</li>';
  if(!isset($_REQUEST['id_algoritm']) || $_REQUEST['id_algoritm']<=0 || is_numeric($_REQUEST['id_algoritm'])<0)$_REQUEST['id_algoritm']=0;
  if(!isset($_REQUEST['id_zone']) || $_REQUEST['id_zone']<=0 || is_numeric($_REQUEST['id_zone'])<0)$_REQUEST['id_zone']=0;

  //Провеяем пропуска
 
  if(!isset($_REQUEST['pxcodenum']))$_REQUEST['pxcodenum']=NULL;
  if(!isset($_REQUEST['pxcodenum_old']))$_REQUEST['pxcodenum_old']=NULL;
  if(!isset($_REQUEST['pxcode_id']) || $_REQUEST['pxcode_id']<=0 || is_numeric($_REQUEST['pxcode_id'])<0)$_REQUEST['pxcode_id']=0;
  if(!isset($_REQUEST['pincod']))$_REQUEST['pincod']='0000';
  if(!isset($_REQUEST['status']))$_REQUEST['status']='00000000';
  if(!isset($_REQUEST['pxdatein']))$_REQUEST['pxdatein']=date("d.m.Y");
  if(!isset($_REQUEST['pxdateout']))$_REQUEST['pxdateout']='';
  if(!isset($_REQUEST['photoname'])) $_REQUEST['photoname']='';

   $break_fast = 0;
   $din        = 0;
   $supper     = 0;

   if(isset($_REQUEST['breakfast'])) $break_fast = 1;
   if(isset($_REQUEST['din']))       $din        = 1;
   if(isset($_REQUEST['supper']))    $supper     = 1;

  if($error!='')
  {
     $text = '<ul>'.$error.'</ul>';
     echo ($text);
        exit();
  }

  $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
if(isset($_REQUEST['pxcodenum'])&&($_REQUEST['pxcodenum']>''))
{  
    $pxdate_in = $_REQUEST['pxdatein']=='' ? NULL : $_REQUEST['pxdatein'];
    $pxdate_out = $_REQUEST['pxdateout']=='' ? NULL : $_REQUEST['pxdateout'];
  if($_REQUEST['pxcodenum']==$_REQUEST['pxcodenum_old'])
  {
    

    $q.='select * FROM BASE_W_U_CODES('.$_REQUEST['pxcode_id'].', \''.$pxdate_in.'\', \''.$pxdate_out.'\',\''
                        .CheckString($_REQUEST['pxcodenum']).'\',\''
                        .CheckString($_REQUEST['pincod']).'\',\''
                        .CheckString($_REQUEST['status']).'\', \'\')';

  }
  else
  {
    $q.='select * from BASE_W_I_CODES(\''.$pxdate_in.'\', \'' .$pxdate_out.'\',\''
            .CheckString($_REQUEST['pxcodenum']).'\',\''
            .CheckString($_REQUEST['pincod']).'\',\''
            .CheckString($_REQUEST['status']).'\', \'\')';

  }
}
else
{
echo('никакого кода пропуска не передано, ниче создавать\сохранять не надо');
}  
  
 if($_REQUEST['pxcodenum']!=NULL)
 {
    $r=pg_fetch_array(pg_query($q));
    $_REQUEST['pxcode_id']=$r['0'];

    if(!isset($_REQUEST['pxcode_id']) || IdValidate($_REQUEST['pxcode_id'])==false)
    {
        echo ('Данный пропуск уже кому-то принадлежит!'); 
        echo('<br/><INPUT TYPE="button" VALUE="Вернуться"   onClick="document.location.href=\'personal.php?action=showall&plink\'">');

	exit();
    }
  }
  else
  {
    $_REQUEST['pxcode_id']=0;
  }
  //запрос на обновление данных  сотрудника
  $zone = '';
  $dopusk = '';
  if(isset($_REQUEST['p_id_zone'])) $zone = 'NULL';
    else
      $zone = $_REQUEST['id_zone'];
  if(isset($_REQUEST['p_id_dopusk'])) $dopusk = 'NULL';
    else
      $dopusk = $_REQUEST['dopusk'];
$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
$date_out = $_REQUEST['date_out']=='' ? NULL : $_REQUEST['date_out'];

//дополнительное поле int or text
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
                            .$_REQUEST['pxcode_id'].','
                            .$zone.','.$dopusk.','
                            .$_REQUEST['id_algoritm'].',\''
                            .$_REQUEST['photoname'].'\',\''.$break_fast.'\',\''	
                            .$din.'\',\''.$supper.'\','
                            .$_REQUEST['graph_offset'].','.$e_i.',\''.$e_t.'\')';

}
elseif($act=='update')
{

   $q.='select BASE_W_U_PERSONAL('.$_REQUEST['id'].',\''.$_REQUEST['date_in'].'\',\''
                            .$date_out.'\','.CheckString($_REQUEST['tabnum']).',\''
                            .CheckString($_REQUEST['family']).'\',\''
                            .CheckString($_REQUEST['fname']).'\',\''
                            .CheckString($_REQUEST['secname']).'\','
                            .$_REQUEST['id_dept'].',\''
                            .CheckString($_REQUEST['position']).'\','
                            .$_REQUEST['graph_name'].','
                            .$_REQUEST['pxcode_id'].','
                            .$zone.','.$dopusk.','
                            .$_REQUEST['id_algoritm'].',\''
                            .$_REQUEST['photoname'].'\',\''.$break_fast.'\',\''	
                            .$din.'\',\''.$supper.'\','
                            .$_REQUEST['graph_offset'].','.$e_i.',\''.$e_t.'\')';

}
else 
{
	echo('Неизвестная операция!');
	echo('<a href="editpers.php?action=edit&id='.$_REQUEST['ID'].'">Вернуться</a>');
	exit();
}  
  pg_query($q);
  
 header('Location: personal.php?action=showall&plink');
 ob_flush();
?>
