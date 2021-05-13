<?php
require_once('../include/hua.php');
include ("../include/input.php");
require_once("include/common_d.php");


if($_GET['obj'] == 'docs' && isset($_GET['act']) && $_GET['act'] == 'show')
{
    $INCLUDES=array();
    $INCLUDES[0] = '<link rel="stylesheet" type="text/css" href="styles/menu.css">';
    echo  PrintHead('Документы.','Просмотр документов',$INCLUDES);
    echo '<br>';
    $docs =  explode(";",$_GET['docs']);
    $size = sizeof($docs);
	
    for ($i = 0; $i < $size; $i++)
    {
        $tab = '';
        $q = 'select * from BASE_W_S_DOC('.$docs[$i].',NULL,0,\'\',\'\',\'\',NULL,\'\',\'\',\'\',\'\',NULL,'.$_SESSION['iduser'].',1)';
        $res = pg_query($q);
        if($res)
        {
	    $r = pg_fetch_array($res);
            $tab .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="docView">';
            $tab .= '<tr><th>'.$r['doc_name'].' № '.$r['id'].' &nbsp от &nbsp; '.$r['created'].'.</th></tr>';
		   
            if($r['id_doc_type'] == 1 )
            {
                $tab .= '<tr><td>Дата действия: '.$r['date'].' </td></tr>';
                $tab .= '<tr><td>Создан для: '.$r['family'].' '.$r['name'].' '.$r['secname'].' ('.$r['tab_num'].') '.$r['pos'].' '.$r['deptname'].'</td></tr>';
                $tab .= '<tr><td>Смена: '.$r['sname'].' </td></tr>';
                $tab .= '<tr><td>Допуск: '.$r['dname'].' </td></tr>';
                $tab .= '<tr><td>Рабочая зона: '.$r['zname'].' </td></tr>';
                $tab .= '<tr><td>Обоснование: '.$r['comment'].' </td></tr>';
            }
            if($r['id_doc_type'] == 2 )
            {
                $tab .= '<tr><td>Время действия: с '.$r['date_in'].' до '.$r['date_out'].'</td></tr>';
                $tab .= '<tr><td>Создан для: '.$r['family'].' '.$r['name'].' '.$r['secname'].' ('.$r['tab_num'].') '.$r['pos'].' '.$r['deptname'].'</td></tr>';
                $tab .= '<tr><td>Рабочая зона: '.$r['zname'].' </td></tr>';
                $tab .= '<tr><td>Обоснование: '.$r['comment'].' </td></tr>';
            } 
            if($r['id_doc_type'] == 3 )
            {
                $tab .= '<tr><td>Создан для: '.$r['family'].' '.$r['name'].' '.$r['secname'].' ('.$r['tab_num'].')</td></tr>';
                $tab .= '<tr><td>'.$r['pos'].' '.$r['daptname'].' </td></tr>';
                $tab .= '<tr><td>Период: с '.$r['date_in'].' до '.$r['date_out'].'</td></tr>';
                $tab .= '<tr><td>'.$r['sign_name'].' ('.$r['sign_comment'].') </td></tr>';
                $tab .= '<tr><td>Обоснование: '.$r['comment'].' </td></tr>';
            }		 
            $tab .= '</table><br>';
            echo $tab;
        }	
	  
    }
    echo PrintFooter();
    exit();					   
}

header('Content-Type: text/xml');
header("Cache-Control: no-store, no-cache, must-revalidate");//убираем кэширование

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
   if($_REQUEST['act']=='filter2')
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
        
        $q = 'select * from TABL_W_S_PERSONAL(\''.CheckString($_REQUEST['family']).'\',
                                \''.CheckString($_REQUEST['fname']).'\',
                                \''.CheckString($_REQUEST['secname']).'\',
                                \''.CheckString($_REQUEST['position']).'\',
                                '.$tab_num.',
                                '.$deptId.',
                                '.$_REQUEST['graph'].',
                                '.$_SESSION['iduser'].',
                                '.'\''.$deptIdList.'\')';
        
        $response = '{';
        $result = pg_query($q);
        while($r = pg_fetch_array($result))
        {
            $fio = $r['family'].' '.$r['name'].' '. $r['secname'];
            $response.='"w_'.$r['id'].'":"'.$fio.'",';
        }
        $response = trim($response,',');
        $response .= '}';
        echo $response;
        exit();
   }
}
else if($_REQUEST['obj'] == 'docs')
{
    if($_REQUEST['act']=='docfilter')//поиск доков по нескольким сотрудникам
    {
        $PERS=explode(";",$_REQUEST['pid']);
        $size=sizeof($PERS);
        $response = '{';
        for($i=0;$i<$size;$i++)
        {
            $q = 'select * from BASE_W_S_DOC(NULL,
                                    '.$PERS[$i].',
                                    '.$_REQUEST['dtype'].',
                                    \'\',\''
                                    .$_REQUEST['dt_start'].'\',\'
                                    '.$_REQUEST['dt_end'].'\',
                                    NULL,
                                    \'\',
                                    \'\',
                                    \'\',
                                    \'\',
                                    0,
                                    1,
                                    1)';
            $result = pg_query($q);
            $description = '';

            while($r = pg_fetch_array($result))
            {			
                if($r['id_doc_type']==1)
                {
                    $description = 'Приказ №'.$r['id'].' от '.$r['date'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
                }
                if($r['id_doc_type']==2)
                {
                    $description = 'Разрешающий документ №'.$r['id'].' с '.$r['date_in'].' по '.$r['date_out'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
                }
                if($r['id_doc_type'] == 3)
                {
                    $description = 'Оправдательный документ №'.$r['id'].' с '.$r['date_in'].' по '.$r['date_out'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
                }
                $response.='"'.$r['id'].'":"'.$description.'",';
            }
        }
        $response = trim($response,',');
        $response .= '}';
        echo $response;

        exit();
        
    }
    if($_REQUEST['act']=='docfilter2')
    {
        $doctype='NULL';
        $tab_num='NULL';
        if($_REQUEST['dtype']!=0)$doctype=$_REQUEST['dtype'];
        if(isset($_REQUEST['tab_num']) && $_REQUEST['tab_num']!='')$tab_num = $_REQUEST['tab_num'];

        $q='select * from BASE_W_S_DOC(NULL,
                        NULL,
                        '.$doctype.',
                        \''.$_REQUEST['doc_date'].'\',
                        \''.$_REQUEST['f_start_doc_date'].'\',
                        \''.$_REQUEST['f_end_doc_date'].'\',
                        '.$tab_num.',
                        \''.CheckString($_REQUEST['family']).'\',
                        \''.CheckString($_REQUEST['fname']).'\',
                        \''.CheckString($_REQUEST['secname']).'\',
                        \''.CheckString($_REQUEST['position']).'\',
                        '.$_REQUEST['depart'].',
                        '.$_SESSION['iduser'].',
                        1)';

        $result = pg_query($q);
        $description ='';
        $response = '{';

        while($r = pg_fetch_array($result))
        {
            if($r['id_doc_type']==1)
            {
                $description = 'Приказ №'.$r['id'].' от '.$r['date'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
            }
            if($r['id_doc_type']==2)
            {
                $description = 'Разрешающий документ №'.$r['id'].' с '.$r['date_in'].' по '.$r['date_out'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
            }

            if($r['id_doc_type'] == 3)
            {
                $description = 'Оправдательный документ №'.$r['id'].' с '.$r['date_in'].' по '.$r['date_out'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
            }
            $response.='"'.$r['id'].'":"'.$description.'",';
        }
        $response = trim($response,',');
        $response .= '}';
        echo $response;

        exit();
    }
    else if($_REQUEST['act']=='save')
    {
        $description ='';
        $response = '{';

        if(isset($_REQUEST['dtype']))
        {
            if($_REQUEST['dtype']==1)
            {
                $PERS=explode(";",$_REQUEST['pid']);
                $size=sizeof($PERS);
                for($i=0;$i<$size;$i++)
                {
                    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                    $q .= 'select * from pr_create_tabl_doc('.$PERS[$i].',\''.$_REQUEST['date']
                                           .'\', \''.$_REQUEST['date_end']
                                           .'\',  '.$_REQUEST['smena']
                                           .',  '.$_REQUEST['dopusk']
                                           .', '.$_REQUEST['zone']
                                           .',  '.$_REQUEST['code']
                                           .',  \''.CheckString($_REQUEST['desc'])
                                           .'\', '.$_SESSION['iduser'].')';

                    $result=pg_query($q);
                    while($r = pg_fetch_array($result))
                    {
                        $description = 'Приказ №'.$r['id'].' от '.$r['date'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
                        $response.='"'.$r['id'].'":"'.$description.'",';
                    }
                }		
            }


            if($_REQUEST['dtype']==2)
            {
                $s_date = date("d.m.Y");
                $e_date = date("d.m.Y");
                $s_time = date("h:i:s");
                $e_time = date("h:i:s");

                $w_time = 0;
                $go     = 0;
                if(isset($_REQUEST['start_date']))$s_date = $_REQUEST['start_date'];
                if(isset($_REQUEST['end_date']))$e_date = $_REQUEST['end_date'];
                if(isset($_REQUEST['start_time'])) $s_date .=' '. $_REQUEST['start_time'];
                else $s_date .=' '.$s_time;
                if(isset($_REQUEST['end_time'])) $e_date .=' '. $_REQUEST['end_time'];
                else $e_date .=' '.$e_time;



                $PERS=explode(";",$_REQUEST['pid']);
                $size=sizeof($PERS);
                for($i=0;$i<$size;$i++)
                {
                    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                    $q .= 'select * from BASE_W_I_DOC(2,'.$PERS[$i].',\''.$s_date.'\',\''.$e_date.'\', NULL,NULL,'
                           .$_REQUEST['zone'].',\'' .CheckString($_REQUEST['desc']).'\',' .$_SESSION['iduser'].')';
                    $result=pg_query($q);
                    while($r = pg_fetch_array($result))
                    {
                        $description = 'Разрешающий документ №'.$r['id'].' с '.$r['date_in'].' по '.$r['date_out'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
                        $response.='"'.$r['id'].'":"'.$description.'",';
                    }
                }
            }
            if($_REQUEST['dtype']==3)
            {
                $w_time = 0;
                $go     = 0;
                $_date = $_REQUEST['date'];

                $PERS=explode(";",$_REQUEST['pid']);
                $size=sizeof($PERS);
                for($i=0;$i<$size;$i++)
                {
                    $q = 'select * from BASE_W_I_DOC(3,'.$PERS[$i].',\''.$_REQUEST['date_st'].'\',\''.$_REQUEST['date_en'].'\',
                        NULL,NULL,'.$_REQUEST['code'].',\'' .CheckString($_REQUEST['desc']).'\',' .$_SESSION['iduser'].')';

                    $result = pg_query($q);
                    $desc='';

                    while($r = pg_fetch_array($result))
                    {
                        $description = 'Оправдательный документ №'.$r['id'].' с '.$r['date_in'].' по '.$r['date_out'].'('.str_replace('"',"",substr($r['comment'],0,20)).'...)';
                        $response.='"'.$r['id'].'":"'.$description.'",';
                    }
                }
            }//doc_type
        }

        $response = trim($response,',');
        $response .= '}';
        echo $response;
        exit();
    }
    else if($_REQUEST['act'] == 'del')
    {
		
        $xml = '';
        $xml .= '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
        $xml .= '<response>';
        $xml .= '<object>'.$_REQUEST['obj'].'</object>';
        $xml .= '<action>'.$_REQUEST['act'].'</action>';
        $xml .= '<result>';

        $edesc = '';
        $cod = '';
        if(!isset($_REQUEST['docs']))
        {
            $edesc = 'Не указаны идентификаторы документов</erorr>';
            $cod = 'DOC_OP_REM';
            $xml.='<error cod="'.$cod.'">'.$edesc.'</error>';
        }
        else
        {
            $DOCS = explode(";",$_REQUEST['docs']);
            $size = sizeof($DOCS);
            for($i=0;$i<$size;$i++)
            {
                $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                $q .= 'select BASE_W_D_DOC('.$DOCS[$i].')';
                pg_query($q);
                $xml.='<item id="'.$DOCS[$i].'" />';
            }
        }
        $xml.='</result>';
        $xml .= '</response>';
        echo $xml;
        exit();
    }
}
