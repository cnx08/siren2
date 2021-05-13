<?php
include ("../include/input.php");
require_once("include/common_t.php");
require_once('../include/hua.php');
header('Content-Type: text/xml');
header("Cache-Control: no-store, no-cache, must-revalidate");//убираем кэширование

session_write_close();    
if($_REQUEST['obj']=='')
{
  $xml = '';
  $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
  $xml .= '<response>';
       $xml .= '<object>error</object>';
       $xml .= '<text>Не распознан объект</text>';
  $xml .= '</response>';
  echo $xml;
  exit();
}
if($_REQUEST['obj']=='pers')
{
   if($_REQUEST['act']=='filter')
   {
        $deptId      = 0;
        $deptIdList  = '';

        //если есть запятая в строке, то значит выбрали несколько отделов
        if ( strstr($_REQUEST['depart'],",") != false )
        {
                     $deptIdList = $_REQUEST['depart'];

        }
        else if ( is_numeric( $_REQUEST['depart'] ) > 0 )//всего один отдел
        {
                     $deptId = $_REQUEST['depart'];
        }
       
        $tab_num='NULL';
         if(isset($_REQUEST['tab_num']) && $_REQUEST['tab_num']!='')$tab_num = $_REQUEST['tab_num'];
         $xml = '';
         $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
         $xml .= '<response>';
         $xml .= '<object>'.$_REQUEST['obj'].'</object>';
         $xml .= '<action>'.$_REQUEST['act'].'</action>';
         $xml.='<result>';
         $q = 'select * from TABL_W_S_PERSONAL(\''.CheckString($_REQUEST['family']).'\',
                        \''.CheckString($_REQUEST['name']).'\',
                        \''.CheckString($_REQUEST['secname']).'\',
                        \''.CheckString($_REQUEST['position']).'\',
                        '.$tab_num.',
                        '.$deptId.',
                        '.$_REQUEST['graph'].',
                        '.$_SESSION['iduser'].',
                        '.'\''.$deptIdList.'\')';
        //echo $q;
         $result = pg_query($q);

         while($r = pg_fetch_array($result))
         {
         $xml.='<item id="'.$r['id'].'" tabnum="'.$r['tabel_num'].'" family="'.$r['family'].'" name="'.$r['name'].'" secname="'.$r['secname'].'" />';
         }
         $xml.='</result>';
         $xml .= '</response>';
         echo $xml;
         exit();
   }
   if($_REQUEST['act']=='gettime' )
   {
         $xml = '';
         $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
         $xml .= '<response>';
         $xml .= '<object>'.$_REQUEST['obj'].'</object>';
         $xml .= '<action>'.$_REQUEST['act'].'</action>';
         $xml.='<result>';
         $_date = '01.'.$_REQUEST['month'].'.'.$_REQUEST['year'];
         $val_query = 'select * from TABL_W_S_CORRECT('.$_REQUEST['pid'].',\''.$_date.'\')';
         $values = pg_query($val_query);
        while($v =pg_fetch_array($values))
        {
		  $xml.='<item day="'.$v['day'].'" idsign="'.$v['id_sign'].'" signal="'.$v['signal'].'" time="'.$v['timer'].'" flag="'.$v['flag'].'"/>';
        }

         $q = 'select * from BASE_W_S_PERSONAL_ONCE('.$_REQUEST['pid'].')';
         $r = pg_fetch_array(pg_query($q));
         $xml.='<description id="'.$r['id'].'" tabnum="'.$r['tabel_num'].'" family="'.$r['family'].'" name="'.$r['name'].'" secname="'.$r['secname'].'" position="'.$r['pos'].'" id_dept="'.$r['id_dept'].'" depart="'.str_replace('"',"",$r['dept']).'" graph="'.$r['graph_name'].'" />';
         $xml.='</result>';
         $xml.= '</response>';
         echo $xml;
         exit();
   }
   if($_REQUEST['act']=='tocount')
   {
        $deptId      = 0;
        $deptIdList  = '';

        //если есть запятая в строке, то значит выбрали несколько отделов
        if ( strstr($_REQUEST['depart'],",") != false )
        {
             $deptIdList = $_REQUEST['depart'];
        }
        else if ( is_numeric( $_REQUEST['depart'] ) > 0 )//всего один отдел
        {
             $deptId = $_REQUEST['depart'];
        }
        //creating array of ID's employees
        $employees = array();
        if(isset($_REQUEST['empid']) && $_REQUEST['empid']!='')
        {
            $employees = @explode(',',$_REQUEST['empid']);
        }
        else
        {
            //getting "ID" from filter result
            $tab_num='NULL';
            if(isset($_REQUEST['tab_num']) && $_REQUEST['tab_num']!='')$tab_num = $_REQUEST['tab_num'];
            
            $q = 'select * from TABL_W_S_PERSONAL(\''.CheckString(@$_REQUEST['family']).'\',
                           \''.CheckString($_REQUEST['name']).'\',
                           \''.CheckString($_REQUEST['secname']).'\',
                           \''.CheckString($_REQUEST['position']).'\',
                           '.$tab_num.',
                           '.$deptId.',
                           '.$_REQUEST['graph'].',
                           '.$_SESSION['iduser'].',
                           '.'\''.$deptIdList.'\')';

            $result = pg_query($q);

            while($r = pg_fetch_array($result))
            {
                 $employees[] = $r['id'];
            }
        }
        
         $xml = '';
         $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
         $xml .= '<response>';
         $xml .= '<object>'.$_REQUEST['obj'].'</object>';
         $xml .= '<action>'.$_REQUEST['act'].'</action>';
         $xml.='<result>';
        
         $st_date = CheckString($_REQUEST['st_date']);
         $en_date = '';
         if(isset($_REQUEST['en_date'])) $en_date = CheckString($_REQUEST['en_date']);
			
         //пересчёт времени
         $error = '';
         $e_len = sizeof($employees);
         for($i = 0; $i < $e_len; $i++)
         {
          $tc_query = 'select pr_recalc_narab('.$employees[$i].',\''.$st_date.'\',\''.$en_date.'\','.$_REQUEST['graph_recalc'].','.$_REQUEST['graph_offset'].','.$_REQUEST['correct_flag'].')';
          
          $res = @pg_query($tc_query);
          if(!$res)
          {
          	$xml.='<error>Ошибка при пересчёте. EID='.$employees[$i].'</error>';
          	$xml.='</result>';
            $xml.= '</response>';
            echo $xml;
            exit();
          }
         }
         $xml.='<message>Пересчёт выполнен</message>';
         $xml.='</result>';
         $xml.= '</response>';
         echo $xml;
         exit();
   }
   if($_REQUEST['act']=='savetabel' && isset($_REQUEST['pid']) && isset($_REQUEST['date']))
   {

         $xml = '';
         $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
         $xml .= '<response>';
         $xml .= '<object>'.$_REQUEST['obj'].'</object>';
         $xml .= '<action>'.$_REQUEST['act'].'</action>';
         $xml.='<result>';
        
         if(isset($_REQUEST['time']) && isset($_REQUEST['codes']))
         {
           $arr_time = explode(';',$_REQUEST['time']);
           $arr_code = explode(';',$_REQUEST['codes']);

           $time_size = sizeof($arr_time);
           for ($i = 0; $i < $time_size; $i ++ )
           {
             // 0 - day or cod
             // 1 - id_day  or id_cod
             // 2 - value
             $val_day = explode('_',$arr_time[$i]);
             $val_cod = explode('_',$arr_code[$i]);
             $_prefix = '';

             if($val_day[1] < 10) $_prefix = '0'.$val_day[1];else $_prefix = $val_day[1];

             $_date = $_prefix.substr($_REQUEST['date'],2,strlen($_REQUEST['date']));
             $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
             $q .= 'select TABL_W_I_CORRECT('.$_REQUEST['pid'].',\''.$_date.'\','.$val_cod[2].','.$_SESSION['iduser'].','.$val_day[2].')';
             
             pg_query($q);
           }
         }
         $xml.='</result>';
         $xml.= '</response>';
         echo $xml;
  }
}

?>