<?php

require_once('../include/hua.php');
require_once('../classes/base/data.class.php');
include("../include/input.php");

//----Script global variables------------------------------------------------------------//

$errorFlag = false;
$response = '';
$msgText  = '';
//----Action definition------------------------------------------------------------------//

$action  = null;
if ( isset( $_REQUEST['act'] ) && !empty( $_REQUEST['act'] ) )
    $action = $_REQUEST['act'];

//----Processing actions-----------------------------------------------------------------//
$qw = array('\r\n','\r','\n','(',')','%','\'','\\','/','"','*');
if ( $action == 'save' )
{	
    echo "save";
    $query = '';

    // проверяем параметры 
    if ( !isset( $_REQUEST['depName'] ) || empty($_REQUEST['depName'] ) )
    {
        $msgText   = '<li> Не указано название отдела </li>';
        $errorFlag  = true;
    }

    if (isset( $_REQUEST['idDept'] )  )
    {
        $lesee = $_REQUEST['lesee']=='' ? 0 : 1;
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
        $q .= 'update base_dept set name=\''.str_replace($qw,'',$_REQUEST['deptName']).'\', lesee =\''.$lesee.'\' where id ='.$_REQUEST['idDept'];
        pg_query($q);
        header("Location: ../departments.php"); 
        exit(); 
    }

    //если не было ошибок
    if ( !$errorFlag )
    {

    }
    else
    {
            $msgText =  '';
    }	
} 
if ( $action == 'checkData' )
{   
    $idParent=$_REQUEST['idParent'];
    if ($idParent == '')$idParent = 0;
     $response = '';
    if ( !isset ( $_REQUEST['idRequest']) )
    {
        $response = 'package={ result:false,reason:"неопределённый идентификатор запроса"}';
        echo $response;
        exit();
    }	
    
    $q = 'select pr_dept_check(\''.$_REQUEST['obj'].'\', \''.str_replace($qw,'',$_REQUEST['deptName']).'\', \''.$_REQUEST['parentDeptName'].'\','.$idParent.', \''.$_REQUEST['lesee'].'\')';
    $check_result =  pg_query($q);
    $data = pg_fetch_array($check_result);
    	
    $response = 'package={';
    $response .= 'result:true,';
    $response .= 'check_result:'.$data[0];		
    $response .= '}';

    echo $response;
    exit();
} 

//add добавим отдел
if ( $action == 'add' )
{   
    $idParent=$_REQUEST['idParent'];
    if ($idParent == '')$idParent = 0;
    $lesee = $_REQUEST['lesee']=='' ? 0 : 1;
    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
    $q .= 'select  BASE_W_I_DEPT(\''.str_replace($qw,'',$_REQUEST['deptName']).'\', '.$idParent.', \''.$lesee.'\')';
    pg_query($q);
    
    header("Location: ../departments.php"); 
    exit();
}
//replace Переносим отдел
if ( $action == 'replace' )
{   
    $lesee = $_REQUEST['lesee']=='' ? 0 : 1;
    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
    $q .= 'select pr_dept_replace('.$_REQUEST['idDept'].', '.$_REQUEST['idParent'].',\''.$lesee.'\')';
    pg_query($q);
    
    header("Location: ../departments.php"); 
    exit();
}
//getDeptName
if ( $action == 'getDeptName' && isset( $_REQUEST['idParent'] ) && is_numeric( $_REQUEST['idParent'] ) )
{
    $response = '';
    if ( !isset ( $_REQUEST['idRequest']) )
    {
        $response = 'package={ result:false,reason:"неопределённый идентификатор запроса"}';
        echo $response;
        exit();
    }	
    $response = 'package={';
    $q = 'select id,name from base_dept where id= '. $_REQUEST['idParent'];  
    
    $dataSet =  pg_query($q);
    $data = pg_fetch_array($dataSet);
    $response .= 'result:true,';

    $response .= 'deptName:\''.$data['name'].'\',';	
    $response .= 'idParent:\''.$data['id'].'\'';	

    $response .= '}';

    echo $response;
    exit();
}
//getNode
if ( $action == 'getNode')
{
    if ( isset( $_REQUEST['idNode'] ) && is_numeric( $_REQUEST['idNode'] )  )
    {
        $idNode = $_REQUEST['idNode'];

        $q = 'select * from pr_get_departments('.$idNode.', '.$_SESSION['iduser'].')';
        $dataSet =  pg_query($q);

        while ( $r = pg_fetch_array($dataSet) )
        {	
            $response .= '<li  id="'.$r['id'].'">';
            $response .=  '<span>'.$r['name'].'</span>';

            //проверяем, существует ли потомки
            $q1 = 'select * from pr_department_child_exists('.$r['id'].')';

            $chExist = pg_query($q1);
            $childExists =  pg_fetch_array($chExist);

            if ( $childExists['bool'] == 'true')
            {
                $response  .=   '<ul class="ajax">';
                $response  .=   '<li >{url:controllers/dept.controller.php?act=getNode&idNode='.$r['id'].'}</li>';
                $response  .= 	'</ul>';
            }
            $response .= '</li>';
        }
        echo $response;
    }		
}
//getData получаем данные об отделе
if ( $action == 'getData' && isset( $_REQUEST['idDept'] ) && is_numeric( $_REQUEST['idDept'] ) )
{
    $response = '';
    if ( !isset ( $_REQUEST['idRequest']) )
    {
        $response = 'package={ result:false,reason:"неопределённый идентификатор запроса"}';
        echo $response;
        exit();
    }	
    $response = 'package={';
    $q = 'select * from pr_get_dept_data('. $_REQUEST['idDept'].')';  
    
    $dataSet =  pg_query($q);
    $data = pg_fetch_array($dataSet);
    $response .= 'result:true,';

    $deptName    = $data['dept_name'];
    $deptName    = $deptName; 
    $parentName  = $data['parent_name'];
    $parentName  = $parentName; 

    $response .= 'idDept:'.$data['id_dept'] .',';

    if ( $data['id_parent'] == null )
            $response .= 'idParent:null,';
    else
            $response .= 'idParent:'.$data['id_parent'].',';
    $response .= 'lesee:'.$data['lesee'] .',';
    $response .= 'deptName:\''.$deptName.'\',';
    $response .= 'parentDeptName:\''.$parentName.'\'';
    	
    $response .= '}';

    echo $response;
    exit();
}
//remove удаляем отдел
if ( $action == 'remove' && isset( $_REQUEST['idDept'] ) && is_numeric( $_REQUEST['idDept'] ) )
{
    $response = '';
    if ( !isset ( $_REQUEST['idRequest']) )
    {
        $response = 'package={ result:false,reason:"неопределённый идентификатор запроса"}';
        echo $response;
        exit();
    }

    $response = 'package={';

    //проверяем есть ли у отдела подчинённые
    try
    {
        $q = 'select * from pr_department_child_exists('.$_REQUEST['idDept'].')';	
        $dataSet =  pg_query($q);		
        $childExists = pg_fetch_array($dataSet);

        if ( $childExists['bool'] == 'true')
        {
            $response = 'package={ result:false,reason:"Невозможно удалить отдел так как он имеет подчинённые отделы. Сначала удалить все подчинённые отделы"}';
            echo $response;
            exit();
        }
        else
        {
            $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
            $q .= 'select BASE_W_D_DEPT('.$_REQUEST['idDept'].')';
            if ( pg_query ( $q ) )
            {
                $response .= 'result:true,reason:"Отдел удалён",';
                $response .= 'idObject:'.$_REQUEST['idDept'].'}';	
            }
            else
            {
                $response = 'package={ result:false,reason:"Не удалось выполнить запрос к БД"}';
            }			
        }
    }
    catch ( CDBException $e )
    {
            $response = 'package={ result:false,reason:"Не удалось выполнить запрос к БД"}';
    }	

    echo $response;
    exit();			 
}
