<?php
ob_start();

set_time_limit(0);
include("include/input.php");
require("include/common.php");

session_write_close();
if(!isset($_REQUEST['excelflg']))require("include/head.php");

if(!isset($_REQUEST['tabel_num']) || $_REQUEST['tabel_num']=='')$_REQUEST['tabel_num']=0;
if(!isset($_REQUEST['fin_date']) || $_REQUEST['fin_date']=='')$_REQUEST['fin_date']='';

//параметры фильтра
if(!isset($_REQUEST['tab_num']) || $_REQUEST['tab_num']=='')$_REQUEST['tab_num']='';
if(!isset($_REQUEST['family']) || $_REQUEST['family']=='')$_REQUEST['family']='';
if(!isset($_REQUEST['name']) || $_REQUEST['name']=='')$_REQUEST['name']='';
if(!isset($_REQUEST['secname']) || $_REQUEST['secname']=='')$_REQUEST['secname']='';
if(!isset($_REQUEST['position']) || $_REQUEST['position']=='')$_REQUEST['position']='';

$tab_num = ($_REQUEST['tab_num']=='' || !is_numeric($_REQUEST['tab_num'])) ? 'NULL' : $_REQUEST['tab_num'];


$head=array();
$head[]='Дата';
if(isset($_REQUEST['checktab']))$head[]='Таб. номер';
$head[]='Фамилия';
$head[]='Имя';
$head[]='Отчество';
if(isset($_REQUEST['checkpos']))$head[]='Должность';
if(isset($_REQUEST['checkdep']))$head[]='Отдел';
if(isset($_REQUEST['checkgraph']))$head[]='График';
if(isset($_REQUEST['checksmena']))$head[]='Смена';
if(isset($_REQUEST['checkzasechka']) && ($_REQUEST['rtype'] == 1))$head[]='Засечки';
$col1="silver";
$col2="#f5f5dc";
$bgcolor='';
$flag=0;
$border = 'border=0';
if(isset($_REQUEST['excelflg']))
{
  $col1 ="#FFFFFF";
  $col2 ="#FFFFFF";
  $border ='border=1';
}

$BODY='';
 $BODY.='<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
$q='';
if(!isset($_REQUEST['rtype']))
{
   HEADER("Location:reportsmenu.php");
}
else
{

   $t=$_REQUEST['rtype'];
   switch($t)
   {
       //ТАБЕЛЬ
       case '1':

               if(isset($_REQUEST['t13']))
               {
                    $BODY.=PrintHead('Отчёт','Отчёт об отработанном времени.Форма Т13');
                    $BODY.='<br>';
                    $num_month=substr($_REQUEST['start_date'],3,2);
                    $month='';
                    switch($num_month)
                    {
                        case '01':$month='Январь';break;
                        case '02':$month='Февраль';break;
                        case '03':$month='Март';break;
                        case '04':$month='Апрель';break;
                        case '05':$month='Май';break;
                        case '06':$month='Июнь';break;
                        case '07':$month='Июль';break;
                        case '08':$month='Август';break;
                        case '09':$month='Сентябрь';break;
                        case '10':$month='Октябрь';break;
                        case '11':$month='Ноябрь';break;
                        case '12':$month='Декабрь';break;
                        default:break;
                    }

                    $BODY.='<table '.$border.' class="reportTable">';
                    $BODY.='<tr>';
                    $BODY.='<th>Фамилия, должность</th>';
                    $BODY.='<th>месяц &nbsp;'.$month.'</th>';
                    $BODY.='<th>Всего за месяц</th>';
                    $BODY.='</tr>';
                    $BODY.='</table>';


                    break;
               }

			   if(!isset($_REQUEST['excelflg']))
			   {
			   
				$BODY = '<!DOCTYPE HTML PUBLIC "	//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
						<html>
						<head>
						<title>Отчет</title>';
				$BODY .= '<meta http-equiv="Content-Language" content="ru">
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<link rel="stylesheet" type="text/css" href="include/style.css">';
					$BODY .= '</head><body>';
			   }

               //проверяем форму отчёта
				if(!isset($_REQUEST['excelflg']))
                {
                 if($_REQUEST['fin_date']!="")
                    $pagetitle.='Отчёт об отработанном времени за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s");
                 else
                    $pagetitle.= 'Отчёт об отработанном времени за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s");
                }
                else
                {
                   if($_REQUEST['fin_date']!="")
                    $pagetitle.='<center><b>Отчёт об отработанном времени за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                 else
                    $pagetitle.= '<center><b>Отчёт об отработанном времени за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                }
			   			   
			   
				$BODY.='<div id = "page_title" style="background-color: silver; ">'; 	// -- добавил width по ширине меню
				$BODY.='<p><font face="Verdana" size=2 color="black"><b>'.$pagetitle.'</b></font></p>';
				$BODY.='</div>';
               //Добовяем доплнительные поля
               $head[]="Время внутри смены";
               $head[]="Общее время";


                           $deptId                 = 0;
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

               $BODY.='<br><table '.$border.' class="reportTable" >';
               $BODY.='<tr>';
                for($i=0;$i<sizeof($head);$i++)
                {
                  $BODY.='<th>'.$head[$i].'</th>';
                }
                $BODY.='</tr>';

               $q='select * from BASE_W_ORDER_WORKED(
                            \''.trim(CheckString($_REQUEST['start_date'])).'\',\''.trim(CheckString($_REQUEST['fin_date'])).'\',\''
                            .trim(CheckString($_REQUEST['family'])).'\',\''
                            .trim(CheckString($_REQUEST['name'])).'\',\''
                            .trim(CheckString($_REQUEST['secname'])).'\',\''
                            .trim(CheckString($_REQUEST['position'])).'\','
                            .$deptId.','
                            .$_REQUEST['graph'].','
                            .$_REQUEST['order_by'].','
                            .$tab_num.','
                            .$_SESSION['iduser'].','
                            .'\''.$deptIdList.'\','
                            .$_REQUEST['sort_order'].')';

                $result=pg_query($q);
                while($r=pg_fetch_array($result))
                {
                 $BODY.='<tr>';
				  
		$BODY.='<td>'.$r['date'].'</td>';
                if(isset($_REQUEST['checktab']))$BODY.='<td width=4%>'.$r['tabel_num'].'</td>';
                $BODY.='<td>'.$r['family'].'</td>';
                $BODY.='<td>'.$r['name'].'</td>';
                $BODY.='<td>'.$r['secname'].'</td>';
                if(isset($_REQUEST['checkpos']))$BODY.='<td>'.$r['pos'].'</td>';
                if(isset($_REQUEST['checkdep']))$BODY.='<td>'.$r['dept_name'].'</td>';
                if(isset($_REQUEST['checkgraph']))$BODY.='<td>'.$r['graph'].'</td>';
                if(isset($_REQUEST['checksmena']))$BODY .= '<td width=16%>'.$r['ressmenastr'].'</td>';
                if(isset($_REQUEST['checkzasechka'])) $BODY.='<td>'.$r['str'].'</td>';

                $BODY.='<td>'.$r['time_sm'];
                if ($r['skiped']==1) $BODY.='<span title="Невозможно верно расчитать время из-за отсутствия последнего события на Выход O">&nbsp;+*</span>';
                if ($r['skiped']==2) $BODY.='<span title="Невозможно верно расчитать время из-за присутствия подряд идущих событий I или О">&nbsp;+**</span>';
                $BODY.='</td>';
                $BODY.='<td>'.$r['timer'];
                if ($r['skiped']==1) $BODY.='<span title="Невозможно верно расчитать время из-за отсутствия последнего события на Выход O">&nbsp;+*</span>';
                if ($r['skiped']==2) $BODY.='<span title="Невозможно верно расчитать время из-за присутствия подряд идущих событий I или О">&nbsp;+**</span>';
                $BODY.='</td>';
                $BODY.='</tr>';
                }
                 $BODY.='</table>';
  	   

       break;

//***********************************************************************************//
//                       ОТЧЁТ ПО ОПОЗДАНИЯМ
       case '2':
            $st_date = '';
            $en_date = '';
            if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
            if(isset($_REQUEST['fin_date'])) $en_date = $_REQUEST['fin_date'];
            if(!isset($_REQUEST['excelflg']))
            {
                if($st_date!=$en_date)
                   $BODY.= PrintHead('Отчёт','Отчёт по опозданиям за период с '.$st_date.'&nbsp; по '.$en_date.'. Сформирован - '.date("d.m.Y H:i:s"));
                else
                   $BODY.= PrintHead('Отчёт','Отчёт по опозданиям за &nbsp;'.$st_date.'. Сформирован - '.date("d.m.Y H:i:s"));
            }
            else
            {
                if($st_date!=$en_date)
                   $BODY.= '<center><b>Отчёт по опозданиям за период с '.$st_date.'&nbsp; по '.$en_date.'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                else
                   $BODY.= '<center><b>Отчёт по опозданиям за &nbsp;'.$st_date.'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';

            }

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


            $BODY.='<br><table '.$border.' class="reportTable">';
            $BODY.='<tr>';
            $head[] = 'Время прихода';
            $head[] = 'Время опоздания';

            for($i=0;$i<sizeof($head);$i++)
            {
                $BODY.='<th>'.$head[$i].'</th>';
            }
            $BODY.='</tr>';
							
            $q='select * from pr_rep_opazdanie(\''.trim(CheckString($st_date)).'\', \''
                    .trim(CheckString($en_date)).'\',\''
                    .CheckString($_REQUEST['family']).'\', \''
                    .CheckString($_REQUEST['name']).'\', \''
                    .CheckString($_REQUEST['secname']).'\','
                    .$deptId.', \''
                    .CheckString($_REQUEST['position']).'\', '
                    .$_REQUEST['graph']. ','
                    .$_SESSION['iduser'].', '
                    .$_REQUEST['order_by'].', '
                    .$tab_num. ', '
                    .'\''.$deptIdList.'\','.$_REQUEST['sort_order'].')';

            $result=pg_query($q);

            while($r = pg_fetch_array($result))
            {
                $BODY.='<tr>';
                $BODY.='<td>'.$r['date'].'</td>';
                if(isset($_REQUEST['checktab']))$BODY.='<td>'.$r['tabel_num'].'</td>';
                $BODY.='<td>'.$r['family'].'</td>';
                $BODY.='<td>'.$r['name'].'</td>';
                $BODY.='<td>'.$r['secname'].'</td>';
                if(isset($_REQUEST['checkpos']))$BODY.='<td>'.$r['pos'].'</td>';
                if(isset($_REQUEST['checkdep']))$BODY.='<td>'.$r['dept'].'</td>';
                if(isset($_REQUEST['checkgraph']))$BODY.='<td>'.$r['id_graph_name'].'</td>';
                if(isset($_REQUEST['checksmena']))
                {
                    $BODY.='<td><b>'.$r['smena'].'</b><br>';
                    $BODY.=$r['smena_beg'].'&#8212;'.$r['smena_end'].'<br><b>Обед</b><br>';

                    $BODY.=$r['dinner_beg'].'&#8212;'.$r['dinner_end'];
                    $BODY.='</td>';
                }
                $BODY.='<td>'.$r['time_in'].'</td>';
                $BODY.='<td>'.$r['time_left'].'</td>';

                $BODY.='</tr>';
            }


            $BODY.='</table>';


       break;

//***********************************************************************************//
//                       ОТЧЁТ ПО ПРИХОДАМ УХОДАМ
       case '3':
            $st_date = '';
            $en_date = '';
            if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
            if(isset($_REQUEST['fin_date'])) $en_date = $_REQUEST['fin_date'];
            if(!isset($_REQUEST['excelflg']))
            {
                if($st_date!=$en_date)
                   $BODY.= PrintHead('Отчёт','Отчёт по приходам/уходам за период с '.$st_date.'&nbsp; по '.$en_date.'. Сформирован - '.date("d.m.Y H:i:s"));
                else
                   $BODY.= PrintHead('Отчёт','Отчёт по приходам/уходам за &nbsp;'.$st_date.'. Сформирован - '.date("d.m.Y H:i:s"));
            }
            else
            {
                if($st_date!=$en_date)
                   $BODY.= '<center><b>Отчёт по приходам/уходам за период с '.$st_date.'&nbsp; по '.$en_date.'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                else
                   $BODY.= '<center><b>Отчёт по приходам/уходам за &nbsp;'.$st_date.'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';

            }
		$BODY.='<br><table '.$border.' class="reportTable">';
                $BODY.='<tr>';


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


                $head[] = 'Точка прохода';
                $head[] = 'Время';
                $head[] = 'Событие';

                for($i=0;$i<sizeof($head);$i++)
                {
                    $BODY.='<th>'.$head[$i].'</th>';
                }
                $BODY.='</tr>';
 
                $turnIdList  = '';

                //если есть запятая в строке, то значит выбрали несколько отделов	
		if (isset($_REQUEST['trlist']))
		{
	             if ( strstr($_REQUEST['trlist'],",") != false )
                    {
                        $turnIdList = $_REQUEST['trlist'];
                    }
                    else if (isset($_REQUEST['trlist']) && $_REQUEST['trlist'] > 0)
                    {
                        $turnIdList = $_REQUEST['trlist'];
                    }
                }
                else {			  
                              $turnIdList = '';
                }
			  
						  
		$q='select * from BASE_W_ORDER_IN_OUT_TURN(\''.trim(CheckString($st_date)).'\', \''
                            .trim(CheckString($en_date)).'\',\''
                            .CheckString($_REQUEST['family']).'\',\''
                            .CheckString($_REQUEST['name']).'\', \''
                            .CheckString($_REQUEST['secname']).'\','
                            .$deptId.', \''
                            .CheckString($_REQUEST['position']).'\','
                            .$_REQUEST['graph'].','
                            .$_SESSION['iduser'].','
                            .$_REQUEST['order_by'].','
                            .$tab_num.','
                            .$_REQUEST['sort_order'].
                            ',null, \''.$deptIdList.'\', \''.$turnIdList.'\')';			  


                $result = pg_query($q);
                 
                while($r = pg_fetch_array($result))
                {
                    if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
                    $BODY.='<tr>';
                    $BODY.='<td>'.$r['date'].'</td>';
                    if(isset($_REQUEST['checktab']))$BODY.='<td>'.$r['tabel_num'].'</td>';
                    $BODY.='<td>'.$r['family'].'</td>';
                    $BODY.='<td>'.$r['name'].'</td>';
                    $BODY.='<td>'.$r['secname'].'</td>';
                    if(isset($_REQUEST['checkpos']))$BODY.='<td>'.$r['pos'].'</td>';
                    if(isset($_REQUEST['checkdep']))$BODY.='<td>'.$r['dept'].'</td>';
                    if(isset($_REQUEST['checkgraph']))$BODY.='<td>'.$r['graph_name'].'</td>';
                    if(isset($_REQUEST['checksmena']))$BODY.='<td>'.$r['smena_name'].'</td>';
                    $BODY.= '<td>'.$r['turn'].'</td>';
                    $BODY.= '<td>'.$r['timer'].'</td>';
                    $BODY.='<td>';

                    $code_text = '';
                    switch($r['code'])
                    {
                        case 'I': $code_text = '<b>Вход</b>&nbsp;';break;
                        case 'O': $code_text = '<b>Выход</b>&nbsp;';break;
                        case 'B': $code_text = '<b>Вход по разр. док.</b>&nbsp;';break;
                        case 'D': $code_text = '<b>Выход по разр. док.</b>&nbsp;';break;
                        case 'V': $code_text = '<b>Вход по разр. охр. </b>&nbsp;';break;
                        case 'W': $code_text = '<b>Выход по разр. охр</b>. &nbsp;';break;
                        case 'P': $code_text = '<b>Посещение столовой</b>. &nbsp;';break;
                        case 'H': $code_text = '<b>Ворота открыты для въезда</b>. &nbsp;';break;
                        case 'N': $code_text = '<b>Ворота открыты для выезда</b>. &nbsp;';break;
                        case 'G': $code_text = '<b>Выход с гостевым пропуском</b>. &nbsp;';break;
                        case 'i': $code_text = '<b>незавершённый вход</b>. &nbsp;';break;
                        case 'o': $code_text = '<b>незавершённый выход</b>. &nbsp;';break;
                        case 'g': $code_text = '<b>незавершённый выход с гостевым пропуском</b>. &nbsp;';break;
                        case 'z': $code_text = '<b>двойные засечки на входе</b>. &nbsp;';break;
                        case 'q': $code_text = '<b>двойные засечки на выходе</b>. &nbsp;';break;
                        case 'p': $code_text = '<b>пропуск заблокирован</b>. &nbsp;';break;
                        case 't': $code_text = '<b>турникет заблокирован</b>. &nbsp;';break;
                        case 'd': $code_text = '<b>сюда нет допуска</b>. &nbsp;';break;
                        case 'w': $code_text = '<b>попытка прохода в неурочное время</b>. &nbsp;';break;
                        case 'y': $code_text = '<b>выход с гостевым пропуском запрещён</b>. &nbsp;';break;
                        case 'x': $code_text = '<b>незарегистрированный пропуск</b>. &nbsp;';break;
                        default:break;
                    }
                    $str = $r['code'];
                    $str = str_replace($r['code'],$code_text,$str);
                    $BODY.= $str.'</td>';

                    $BODY.='</tr>';
                }
                $BODY.='</table>';

       break;

//***********************************************************************************//
//                       ОТЧЁТ ПО НЕЯВКАМ

       case '4':
            $st_date = '';
            $en_date = '';
            if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
            if(isset($_REQUEST['fin_date'])) $en_date = $_REQUEST['fin_date'];
            if(!isset($_REQUEST['excelflg']))
            {
                if($st_date!=$en_date)
                   $BODY.= PrintHead('Отчёт','Отчёт по неявкам за период с '.$st_date.'&nbsp; по '.$en_date.'. Сформирован - '.date("d.m.Y H:i:s"));
                else
                   $BODY.= PrintHead('Отчёт','Отчёт по неявкам за &nbsp;'.$st_date.'. Сформирован - '.date("d.m.Y H:i:s"));
            }
            else
            {
                if($st_date!=$en_date)
                   $BODY.= '<center><b>Отчёт по неявкам за период с '.$st_date.'&nbsp; по '.$en_date.'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                else
                   $BODY.= '<center><b>Отчёт по неявкам за &nbsp;'.$st_date.'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';

            }
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

            $BODY.='<br><table '.$border.' class="reportTable" >';
            $BODY.='<tr>';

            for($i=0;$i<sizeof($head);$i++)
            {
                $BODY.='<th>'.$head[$i].'</th>';
            }
            $BODY.='</tr>';


            $q = 'select * from pr_rep_neyavkam(\''.$st_date.'\', \''
                    .$en_date.'\', \''
                    .CheckString($_REQUEST['family']).'\',\''
                    .CheckString($_REQUEST['name']).'\', \''
                    .CheckString($_REQUEST['secname']).'\','
                    .$deptId.', \''
                    .CheckString($_REQUEST['position']).'\','
                    .$_REQUEST['graph'].','
                    .$_SESSION['iduser'].','
                    .$_REQUEST['order_by'].','
                    .$tab_num.',\''
                    .$deptIdList.'\','
                    .$_REQUEST['sort_order'].')';

            $result = pg_query($q);

            while($r = pg_fetch_array($result))
            {
                $BODY.='<tr>';
                $BODY.='<td>'.$r['date'].'</td>';
                if(isset($_REQUEST['checktab']))$BODY.='<td>'.$r['tabel_num'].'</td>';
                $BODY.='<td>'.$r['family'].'</td>';
                $BODY.='<td>'.$r['name'].'</td>';
                $BODY.='<td>'.$r['secname'].'</td>';
                if(isset($_REQUEST['checkpos']))$BODY.='<td>'.$r['pos'].'</td>';
                if(isset($_REQUEST['checkdep']))$BODY.='<td>'.$r['dept'].'</td>';
                if(isset($_REQUEST['checkgraph']))$BODY.='<td>'.$r['graph_name'].'</td>';
                if(isset($_REQUEST['checksmena']))
                {
                    $BODY.='<td><b>'.$r['smena'].'</b><br>';
                    $BODY.=$r['smena_beg'].'&#8212;'.$r['smena_end'].'<br><b>обед</b> <br>';
                    $BODY.=$r['dinner_beg'].'&#8212;'.$r['dinner_end'];
                    $BODY.='</td>';
                }

                $BODY.='</tr>';
            }
            $BODY.='</table>';

       break;
//***********************************************************************************/
//                       ОТЧЁТ О ПРИСУТСТВИИ

        case '5':

            if(!isset($_REQUEST['excelflg']))
               $BODY.= PrintHead('Отчёт','Отчёт о присутствии. Сформирован - '.date("d.m.Y H:i:s"));
            else
               $BODY.= '<center><b>Отчёт о присутствии. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';



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

        $terr_list = '';
        if ( is_numeric( $_REQUEST['terr'] ) > 0 )
        {
            $terr_list = $_REQUEST['terr'];
        }
                           

        $head[] = 'Пришёл c';
        $head[] = 'Время';
        $head[] = 'Территория';
        $BODY.='<br><table '.$border.' class="reportTable">';
        $BODY.='<tr>';


        for($i=0;$i<sizeof($head);$i++)
        {
            $BODY.='<th>'.$head[$i].'</th>';
        }
        $BODY.='</tr>';



        $stDate   = ( isset( $_REQUEST['start_date'] ) && $_REQUEST['start_date'] != '' ) ? $_REQUEST['start_date'] : date('d.m.Y');
        $endDate  = ( isset( $_REQUEST['fin_date'] ) && $_REQUEST['fin_date'] != '' ) ? $_REQUEST['fin_date'] : $stDate;


        $q = 'select * from BASE_W_ORDER_PRISUTSTVII(\''.$stDate.'\',\''
                .$endDate.'\',\''
                .$_REQUEST['st_time'].'\','
                .$terr_list.',\''
                .CheckString($_REQUEST['family']).'\',\''
                .CheckString($_REQUEST['name']).'\', \''
                .CheckString($_REQUEST['secname']).'\','
                .$deptId.',\''
                .CheckString($_REQUEST['position']).'\','
                .$_REQUEST['graph'].','
                .$_SESSION['iduser'].','
                .$_REQUEST['order_by'].','
                .$tab_num.',\''
                .$deptIdList.'\','
                .$_REQUEST['sort_order'].')';

        $result = pg_query($q);


        while($r = pg_fetch_array($result))
        {
            $BODY.='<tr>';
            $BODY.='<td>'.$r['date'].'</td>';
            if(isset($_REQUEST['checktab']))$BODY.='<td>'.$r['tabel_num'].'</td>';
            $BODY.='<td>'.$r['family'].'</td>';
            $BODY.='<td>'.$r['name'].'</td>';
            $BODY.='<td>'.$r['secname'].'</td>';
            if(isset($_REQUEST['checkpos']))$BODY.='<td>'.$r['pos'].'</td>';
            if(isset($_REQUEST['checkdep']))$BODY.='<td>'.$r['dname'].'</td>';
            if(isset($_REQUEST['checkgraph']))$BODY.='<td>'.$r['graph_name'].'</td>';
            if(isset($_REQUEST['checksmena']))
            {
                $BODY.='<td><b>'.$r['smena'].'</b><br>';
                $BODY.=$r['start_sm'].'&#8212;'.$r['end_sm'].'<br><b>обед</b> <br>';
                $BODY.=$r['start_din'].'&#8212;'.$r['end_din'];
                $BODY.='</td>';
            }

            $BODY.='<td>'.$r['otkuda'].'</td>';
            $BODY.='<td>'.$r['time_in'].'</td>';
            $BODY.='<td>'.$r['terra'].'</td>';

            $BODY.='</tr>';
            $emplCount =   $r['nrows'];
        }

        $BODY.='<tr>';
        $BODY.='<td colspan="'.sizeof($head).'">Всего сотрудников:'.$emplCount  .'</td>';
        $BODY.='</tr>';
        $BODY.='</table>';

    break;


       ///////////////////////Ранний уход///////////////////////////
       case '6':
            if(!isset($_REQUEST['excelflg']))
                $BODY.= PrintHead('Отчёт','Ранний уход');
            else
                $BODY.= '<center><b>Ранний уход</b></center>';

            $head[] = 'Время ухода';
            $head[] = 'Ушел раньше на:';

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


            $BODY.='<br><table '.$border.' class="reportTable">';
            $BODY.='<tr>';
            for($i=0;$i<sizeof($head);$i++)
            {
                $BODY.='<th>'.$head[$i].'</th>';
            }
            $BODY.='</tr>';


            $stDate   = ( isset( $_REQUEST['start_date'] ) && $_REQUEST['start_date'] != '' ) ? $_REQUEST['start_date'] : date('d.m.Y');
            $endDate  = ( isset( $_REQUEST['fin_date'] ) && $_REQUEST['fin_date'] != '' ) ? $_REQUEST['fin_date'] : $stDate;


            $q = 'select * from pr_rep_early_left(\''.$stDate.'\',\''
                    .$endDate.'\',\''
                    .CheckString($_REQUEST['family']).'\',\''
                    .CheckString($_REQUEST['name']).'\',\''
                    .CheckString($_REQUEST['secname']).'\','
                    .$deptId.',\''
                    .CheckString($_REQUEST['position']).'\','
                    .$_REQUEST['graph'].','
                    .$_SESSION['iduser'].','
                    .$_REQUEST['order_by'].','
                    .$tab_num.',\''
                    .$deptIdList.'\','
                    .$_REQUEST['sort_order']. ')';

            $result = pg_query($q);


            while($r = pg_fetch_array($result))
            {
                $BODY.='<tr>';
                $BODY.='<td>'.$r['date'].'</td>';
                if(isset($_REQUEST['checktab']))$BODY.='<td>'.$r['tabel_num'].'</td>';
                $BODY.='<td>'.$r['family'].'</td>';
                $BODY.='<td>'.$r['name'].'</td>';
                $BODY.='<td>'.$r['secname'].'</td>';
                if(isset($_REQUEST['checkpos']))$BODY.='<td>'.$r['pos'].'</td>';
                if(isset($_REQUEST['checkdep']))$BODY.='<td>'.$r['dept_name'].'</td>';
                if(isset($_REQUEST['checkgraph']))$BODY.='<td>'.$r['graph_name'].'</td>';
                if(isset($_REQUEST['checksmena']))
                {
                    $BODY.='<td><b>'.$r['smena'].'</b><br>';
                    $BODY.=$r['smena_beg'].'&#8212;'.$r['smena_end'].'<br><b>обед</b> <br>';
                    $BODY.=$r['dinner_beg'].'&#8212;'.$r['dinner_end'];
                    $BODY.='</td>';
                }

                $BODY.='<td>'.$r['time_out'];
                if ($r['skiped']==1) $BODY.='<span title="Cобытие на Выход \'O\' отсутствует, показано последнее событие на Вход \'I\'">&nbsp;+*</span>';
                $BODY.='</td>';
                $BODY.='<td>'.$r['time_left'].'</td>';
                $BODY.='</tr>';
            }

            $BODY.='</table>';

      break;
      /////////////Отчёт по столовой////////////////////////////////
      case '7':


           if(!isset($_REQUEST['excelflg']))
           {
             if($_REQUEST['fin_date']!="")
                   $BODY.= PrintHead('Отчёт','Отчёт по столовой за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
                else
                   $BODY.= PrintHead('Отчёт','Отчёт по столовой за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
            }
                 $_REPORT = '';
                 $_R_HEAD = '';
                 $st_date = '';
                 $en_date = '';
                 if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
                 if(isset($_REQUEST['fin_date'])) $en_date = $_REQUEST['fin_date'];

               $q='DINNER_W_ORDER_ALL_GROUP "'.trim(CheckString($st_date)).'","'
                          .trim(CheckString($en_date)).'","'
                          .CheckString($_REQUEST['family']).'","'
                          .CheckString($_REQUEST['name']).'","'
                          .CheckString($_REQUEST['secname']).'",
                          '.$_REQUEST['depart'].',"'
                          .CheckString($_REQUEST['position']).'",
                          '.$_REQUEST['graph'].',
                          '.$_SESSION['iduser'].','
                          .$_REQUEST['order_by'];


               $result = mssql_query($q);

               $_REPORT .= '<br><table border="0" width="100%" cellpadding="2" cellsapcing="1" align="center">';
               $_REPORT .= '<tr class=tablehead>';
               $_REPORT .='<td align="center">Таб.Номер</td>';
               $_REPORT .='<td align="center">Фамилия</td>';
               $_REPORT .='<td align="center">Имя</td>';
               $_REPORT .='<td align="center">Отчество</td>';
               $_REPORT .='<td align="center">Завтраков</td>';
               $_REPORT .='<td align="center">Обедов</td>';
               $_REPORT .='<td align="center">Ужинов</td>';
               $_REPORT .= '</tr>';
               $sum_z = 0;
               $sum_o = 0;
               $sum_u = 0;
               while($r = mssql_fetch_array($result))
               {
                   if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
                   $_REPORT.='<tr bgcolor='.$bgcolor.'>';
                   $_REPORT.='<td class="tabcontent" align="center">'.$r['TABEL_NUM'].'</td>';
                   $_REPORT.='<td class="tabcontent" align="center">'.$r['FAMILY'].'</td>';
                   $_REPORT.='<td class="tabcontent" align="center">'.$r['NAME'].'</td>';
                   $_REPORT.='<td class="tabcontent" align="center">'.$r['SECNAME'].'</td>';
                   $_REPORT.='<td class="tabcontent" align="center">'.$r['COUNT_Z'].'</td>';
                   $_REPORT.='<td class="tabcontent" align="center">'.$r['COUNT_O'].'</td>';
                   $_REPORT.='<td class="tabcontent" align="center">'.$r['COUNT_U'].'</td>';

                   $_REPORT.='</tr>';
                   $sum_z += $r['COUNT_Z'];
                   $sum_o += $r['COUNT_O'];
                   $sum_u += $r['COUNT_U'];
               }

               $_REPORT .= '</table>';

               $_R_HEAD .= '<br><table>';
               $_R_HEAD .= '<tr>';
               $_R_HEAD .= '<td class="tabcontent" align="center"><span class="text"><b>Всего завтраков:</b>&nbsp;'.$sum_z.'&nbsp;</span></td>';
               $_R_HEAD .= '<td class="tabcontent" align="center"><span class="text"><b>Всего обедов:</b>&nbsp;'.$sum_o.'&nbsp;</span></td>';
               $_R_HEAD .= '<td class="tabcontent" align="center"><span class="text"><b>Всего ужинов:</b>&nbsp;'.$sum_u.'&nbsp;</span></td>';
               $_R_HEAD .= '</tr>';
               $_R_HEAD .= '</table>';


               $_REPORT .= '<br><table width="100%" cellpadding="2" cellsapcing="1" align="center">';
               $_REPORT .= '<tr class=tablehead>';


                if(isset($_REQUEST['checksmena']))
                {
                   array_pop($head);
                }
                   $head[] = 'Время';
                   $head[] = 'Тип';


                for($i=0;$i<sizeof($head);$i++)
                {
                  $_REPORT.='<td align="center">'.$head[$i].'</td>';
                }
                $_REPORT.='</tr>';

                $q='DINNER_W_ORDER_DINNER "'.trim(CheckString($st_date)).'","'
                          .trim(CheckString($en_date)).'","'
                          .CheckString($_REQUEST['family']).'","'
                          .CheckString($_REQUEST['name']).'","'
                          .CheckString($_REQUEST['secname']).'",
                          '.$_REQUEST['depart'].',"'
                          .CheckString($_REQUEST['position']).'",
                          '.$_REQUEST['graph'].',
                          '.$_SESSION['iduser'].','
                          .$_REQUEST['order_by'];

                 $result = mssql_query($q);
                 
                 while($r = mssql_fetch_array($result))
                 {
                   if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
                 $_REPORT.='<tr bgcolor='.$bgcolor.'>';
                 $_REPORT.='<td class="tabcontent" align="center">'.$r['DATE'].'</td>';
                  if(isset($_REQUEST['checktab']))$_REPORT.='<td class="tabcontent" align="center">'.str_replace("NULL","--",$r['TABEL_NUM']).'</td>';
                  $_REPORT.='<td class="tabcontent" align="center">'.str_replace("NULL","--",$r['FAMILY']).'</td>';
                  $_REPORT.='<td class="tabcontent" align="center">'.str_replace("NULL",'--',$r['NAME']).'</td>';
                  $_REPORT.='<td class="tabcontent" align="center">'.str_replace("NULL",'--',$r['SECNAME']).'</td>';
                  if(isset($_REQUEST['checkpos']))$_REPORT.='<td class="tabcontent" align="center">'.str_replace("NULL",'--',$r['POSITION']).'</td>';
                  if(isset($_REQUEST['checkdep']))$_REPORT.='<td class="tabcontent" align="center">'.$r['DEPT'].'</td>';
                  if(isset($_REQUEST['checkgraph']))$_REPORT.='<td class="tabcontent" align="center">'.$r['GRAPH_NAME'].'</td>';
                  $_REPORT.='<td class="tabcontent" align="center">'.$r['TIME'].'</td>';
                  $_REPORT.='<td class="tabcontent" align="center">'.$r['COMMENT'].'</td>';
                  $_REPORT.='</tr>';
                }

                $_REPORT .= '</table>';
                $BODY .= $_R_HEAD.$_REPORT;
      break;
	  
//***********************************************************************************//
//                      Сводный отчет по нарушителям режима
       case '8':
         if(!isset($_REQUEST['excelflg']))
           {
             if($_REQUEST['fin_date']!="")
                   $BODY.= PrintHead('Отчет по нарушителям режима','Сводный отчет по нарушителям режима за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
                else
                   $BODY.= PrintHead('Отчет по нарушителям режима','Сводный отчет по нарушителям режима за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
            }
            else
            {
                     if($_REQUEST['fin_date']!="")
                   $BODY.= '<center><b>Сводный отчет по нарушителям режима за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                else
                   $BODY.= '<center><b>Сводный отчет по нарушителям режима за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';

            }
               $BODY.='<br><table '.$border.' class="reportTable">';
               $BODY.='<tr>';

                 $st_date = '';
                 $en_date = '';
                 if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
                 if(isset($_REQUEST['fin_date'])) $en_date = $_REQUEST['fin_date'];

               //удаляем смену из башки

                           if(isset($_REQUEST['checksmena']))
                            array_pop($head);

                           $deptId                 = 0;
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

				 $head = array();
				 $head[0] = '№ п.п';
				 $head[1] = 'Подразделение';
				 $head[2] = 'Всего сотрудников';
				 $head[3] = 'Опозданий на смену'; 
				 $head[4] = 'Уходов раньше со смены';
				 $head[5] = 'Уходов раньше на обед';
				 $head[6] = 'Опозданий с обеда';
				
				
				$BODY .='<th>'.$head[0].'</th>';
				$BODY .='<th>'.$head[1].'</th>';
				$BODY .='<th>'.$head[2].'</th>';
				$BODY .='<th colspan="2">'.$head[3].'</th>';
				$BODY .='<th  colspan="2">'.$head[4].'</th>';				
				$BODY .='<th  colspan="2">'.$head[5].'</th>';	
				$BODY .='<th  colspan="2">'.$head[6].'</th>';				
				
				
                $BODY.='</tr>';
                $turn = (isset($_REQUEST['point_to_pass']) && $_REQUEST['point_to_pass'] > 0 ) ? $_REQUEST['point_to_pass'] : 'null';
						 					  
						  		  
                $q='exec dbo.pr_Violations_SummaryReport "'.trim(CheckString($st_date)).'","'
                          .trim(CheckString($en_date)).'","'
                          .CheckString($_REQUEST['family']).'","'
                          .CheckString($_REQUEST['name']).'","'
                          .CheckString($_REQUEST['secname']).'",
                          '.$deptId.',"'
                          .CheckString($_REQUEST['position']).'",
                          '.$_REQUEST['graph'].',
                          '.$_SESSION['iduser'].','
                          .$_REQUEST['order_by'].','
                          .$tab_num.','
                          .$turn.','
                          .'\''.$deptIdList.'\'';

                 $result = mssql_query($q);
                 
                 while($r = mssql_fetch_array($result))
				{			   
					 if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
					$BODY.='<tr>';
					$BODY.='<td>'.$r['RecNo'].'</td>';
					$BODY.='<td>'.$r['DEPT_NAME'].'</td>';
					$BODY.='<td>'.$r['PERS_CNT'].'</td>';
					$BODY.='<td>'.$r['SM_DELAY_CNT'].'</td>';
					$BODY.='<td>'.$r['SM_DELAY'].'</td>';	
					$BODY.='<td>'.$r['SM_EARLY_LEFT_CNT'].'</td>';
					$BODY.='<td>'.$r['SM_EARLY_LEFT'].'</td>';
					$BODY.='<td>'.$r['DIN_DELAY_CNT'].'</td>';
					$BODY.='<td>'.$r['DIN_DELAY'].'</td>';
					$BODY.='<td>'.$r['DIN_EARLY_LEFT_CNT'].'</td>';
					$BODY.='<td>'.$r['DIN_EARLY_LEFT'].'</td>';
					 
					$BODY.='</tr>';
                 }
                $BODY.='</table>';

       break;	  
		  //***********************************************************************************//
//                       ОТЧЁТ ПО ПРИХОДАМ УХОДАМ СОКРАЩЁННЫЙ
       case '9':
         if(!isset($_REQUEST['excelflg']))
           {
             if($_REQUEST['fin_date']!="")
                   $BODY.= PrintHead('Отчёт','Отчёт по приходам/уходам сокращённый за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
                else
                   $BODY.= PrintHead('Отчёт','Отчёт по приходам/уходам сокращённый за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
            }
            else
            {
                     if($_REQUEST['fin_date']!="")
                   $BODY.= '<center><b>Отчёт по приходам/уходам сокращённый за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                else
                   $BODY.= '<center><b>Отчёт по приходам/уходам сокращённый за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';

            }
				
               $BODY.='<br><table '.$border.' class="reportTable">';
               $BODY.='<tr>';

			   
                 $st_date = '';
                 $en_date = '';
                 if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
                 if(isset($_REQUEST['fin_date'])) $en_date = $_REQUEST['fin_date'];


                $deptId                 = 0;
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


                $head[] = 'Точка прохода';
                $head[] = 'Время';
                $head[] = 'Событие';

                for($i=0;$i<sizeof($head);$i++)
                {
                  $BODY.='<th>'.$head[$i].'</th>';
                }
                $BODY.='</tr>';

            /////// 03.04.2013 - список точек прохода 
                $turnId                 = 0;
                $turnIdList  = '';

            //если есть запятая в строке, то значит выбрали несколько отделов
	
	
		if (isset($_REQUEST['trlist']))
		{
                    if ( strstr($_REQUEST['trlist'],",") != false )
                    {
                        $turnIdList = $_REQUEST['trlist'];
                    }
                    else if (isset($_REQUEST['trlist']) && $_REQUEST['trlist'] > 0)
                    {
                        $turnIdList = $_REQUEST['trlist'];
                    }
                }
                else {			  
                    $turnIdList = '';
                }
					  
                ///////// передается отобранный список турникетов						  
                $q='select * from BASE_W_ORDER_IN_OUT_TURN_SHORT(\''.trim(CheckString($st_date)).'\', \''
                    .trim(CheckString($en_date)).'\', \''
                    .CheckString($_REQUEST['family']).'\', \''
                    .CheckString($_REQUEST['name']).'\', \''
                    .CheckString($_REQUEST['secname']).'\','
                    .$deptId.', \''
                    .CheckString($_REQUEST['position']).'\','
                    .$_REQUEST['graph'].','
                    .$_SESSION['iduser'].','
                    .$_REQUEST['order_by'].', '
                    .$tab_num.', '
                    .$_REQUEST['sort_order'].', \''
                    .$deptIdList.'\', \''
                    .$turnIdList.'\')';						  


                $result = pg_query($q);
                 
                $flag=0;
                $bgcolor="";

                while($r = pg_fetch_array($result))
                {
                    if($r['id']!=$flag)
                    {
                        if($bgcolor=="#f5f5f5")$bgcolor="#D7CFD5";
                        else $bgcolor="#f5f5f5";
                    }
                        $flag = $r['id'];
                        $BODY.='<tr>';
                        $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['date'].'</td>';
                        if(isset($_REQUEST['checktab']))$BODY.='<td bgcolor="'.$bgcolor.'">'.$r['tabel_num'].'</td>';
                        $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['family'].'</td>';
                        $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['name'].'</td>';
                        $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['secname'].'</td>';
                        if(isset($_REQUEST['checkpos']))$BODY.='<td bgcolor="'.$bgcolor.'">'.$r['pos'].'</td>';
                        if(isset($_REQUEST['checkdep']))$BODY.='<td bgcolor="'.$bgcolor.'">'.$r['dept'].'</td>';
                        if(isset($_REQUEST['checkgraph']))$BODY.='<td bgcolor="'.$bgcolor.'">'.$r['graph_name'].'</td>';
                        if(isset($_REQUEST['checksmena']))$BODY.='<td bgcolor="'.$bgcolor.'">'.$r['smena_name'].'</td>';


                        $BODY.= '<td bgcolor="'.$bgcolor.'">'.$r['turn'].'</td>';
                        $BODY.= '<td bgcolor="'.$bgcolor.'">'.$r['timer'].'</td>';
                        $BODY.='<td bgcolor="'.$bgcolor.'">';

                        $code_text = '';
                        switch($r['code'])
                        {
                            case 'I': $code_text = '<b>Вход</b>&nbsp;';break;
                            case 'O': $code_text = '<b>Выход</b>&nbsp;';break;
                            case 'B': $code_text = '<b>Вход по разр. док.</b>&nbsp;';break;
                            case 'D': $code_text = '<b>Выход по разр. док.</b>&nbsp;';break;
                            case 'V': $code_text = '<b>Вход по разр. охр. </b>&nbsp;';break;
                            case 'W': $code_text = '<b>Выход по разр. охр</b>. &nbsp;';break;
                            case 'P': $code_text = '<b>Посещение столовой</b>. &nbsp;';break;
                            case 'H': $code_text = '<b>Ворота открыты для въезда</b>. &nbsp;';break;
                            case 'N': $code_text = '<b>Ворота открыты для выезда</b>. &nbsp;';break;
                            // case 'R': $code_text = '<b>Проход от радиобрелка</b>. &nbsp;';break;
                            case 'G': $code_text = '<b>Выход с гостевым пропуском</b>. &nbsp;';break;
                            case 'i': $code_text = '<b>незавершённый вход</b>. &nbsp;';break;
                            case 'o': $code_text = '<b>незавершённый выход</b>. &nbsp;';break;
                            case 'g': $code_text = '<b>незавершённый выход с гостевым пропуском</b>. &nbsp;';break;
                            case 'z': $code_text = '<b>двойные засечки на входе</b>. &nbsp;';break;
                            case 'q': $code_text = '<b>двойные засечки на выходе</b>. &nbsp;';break;
                            case 'p': $code_text = '<b>пропуск заблокирован</b>. &nbsp;';break;
                            case 't': $code_text = '<b>турникет заблокирован</b>. &nbsp;';break;
                            case 'd': $code_text = '<b>сюда нет допуска</b>. &nbsp;';break;
                            case 'w': $code_text = '<b>попытка прохода в неурочное время</b>. &nbsp;';break;
                            case 'y': $code_text = '<b>выход с гостевым пропуском запрещён</b>. &nbsp;';break;
                            case 'x': $code_text = '<b>незарегистрированный пропуск</b>. &nbsp;';break;
                            default:break;
                        }
                        $str = $r['code'];
                        $str = str_replace($r['code'],$code_text,$str);
                        $BODY.= $str.'</td>';



                         $BODY.='</tr>';
                 }
                $BODY.='</table>';

       break;

	//                       ОТЧЁТ ПО ПРИХОДАМ УХОДАМ ДЛЯ ГОСТЕВЫХ ПРОПУСКОВ
       case '10':
         if(!isset($_REQUEST['excelflg']))
           {
                if($_REQUEST['fin_date']!="")
                   $BODY.= PrintHead('Отчёт','Отчёт по приходам/уходам посетителей за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
                else
                   $BODY.= PrintHead('Отчёт','Отчёт по приходам/уходам посетителей за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s"));
            }
            else
            {
                if($_REQUEST['fin_date']!="")
                   $BODY.= '<center><b>Отчёт по приходам/уходам посетителей за период с '.$_REQUEST['start_date'].'&nbsp; по '.$_REQUEST['fin_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';
                else
                   $BODY.= '<center><b>Отчёт по приходам/уходам посетителей за &nbsp;'.$_REQUEST['start_date'].'. Сформирован - '.date("d.m.Y H:i:s").'</b></center>';

            }
                $BODY.='<br><table '.$border.' class="reportTable">';
                $BODY.='<tr>';

			   
                $st_date = '';
                $en_date = '';
                if(isset($_REQUEST['start_date'])) $st_date = $_REQUEST['start_date'];
                if(isset($_REQUEST['fin_date'])) $en_date = $_REQUEST['fin_date'];

                $head=array();
                $head[]='Дата';
                $head[]='Посетитель';
                if(isset($_REQUEST['checkgpos']))$head[]='Должность';
                if(isset($_REQUEST['checkgcomm']))$head[]='Комментарий';
                if(isset($_REQUEST['checkpass']))$head[]='Пропуск';
                if(isset($_REQUEST['checkpasstime']))$head[]='Время действия пропуска';
                if(isset($_REQUEST['checkdopusk']))$head[]='Допуск';
                if(isset($_REQUEST['checktowho']))$head[]='К кому';
                if(isset($_REQUEST['checkpos']))$head[]='Должность';
                if(isset($_REQUEST['checkdep']))$head[]='Отдел';
                $head[] = 'Точка прохода';
                $head[] = 'Время';
                $head[] = 'Событие';

                for($i=0;$i<sizeof($head);$i++)
                {
                    $BODY.='<th>'.$head[$i].'</th>';
                }
                $BODY.='</tr>';


                !isset($_REQUEST['depart']) || $_REQUEST['depart']=='0' ? $deptIdList = '' : $deptIdList = $_REQUEST['depart'];
		!isset($_REQUEST['trlist']) || $_REQUEST['trlist']=='0' ? $turnIdList = '' : $turnIdList = $_REQUEST['trlist'];
                
			  
                $q='select * from base_w_order_in_out_turn_guest(\''
                        .trim(CheckString($st_date)).'\',\''
                        .trim(CheckString($en_date)).'\',\''
                        .CheckString($_REQUEST['guest_family']).'\', \''
                        .CheckString($_REQUEST['guest_name']).'\',\''
                        .CheckString($_REQUEST['guest_secname']).'\',\''
                        .CheckString($_REQUEST['guest_pass']).'\',\''
                        .CheckString($_REQUEST['family']).'\',\''
                        .CheckString($_REQUEST['name']).'\',\''
                        .CheckString($_REQUEST['secname']).'\', \''
                        .CheckString($_REQUEST['position']).'\','
                        .$_SESSION['iduser'].','
                        .$_REQUEST['g_order_by'].','
                        .$tab_num.','
                        .$_REQUEST['sort_order'].', \''
                        .$deptIdList.'\',\''
                        .$turnIdList.'\')';			  


                $result = pg_query($q);
                $flag=0;
                $bgcolor="";
                while($r = pg_fetch_array($result))
                {
                    if($r['id']!=$flag)
                    {
                            if($bgcolor=="#f5f5f5")$bgcolor="#D7CFD5";
                            else $bgcolor="#f5f5f5";
                    }
                 $flag = $r['id'];
                $BODY.='<tr>';
                $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['date'].'</td>';
                $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['guest_family'].' '.$r['guest_name'].' '.$r['guest_secname'].'</td>';
                if(isset($_REQUEST['checkgpos']))       $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['guest_position'].'</td>';
                if(isset($_REQUEST['checkgcomm']))      $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['guest_comment'].'</td>';
                if(isset($_REQUEST['checkpass']))       $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['px_comment'].' ('.$r['px_code'].')'.'</td>';
                if(isset($_REQUEST['checkpasstime']))   $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['date_in'].' - '.$r['date_out'].'</td>';
                if(isset($_REQUEST['checkdopusk']))     $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['dopusk'].'</td>';
                if(isset($_REQUEST['checktowho']))      $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['family'].' '.$r['name'].' '.$r['secname'].'</td>';
                if(isset($_REQUEST['checkpos']))        $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['pos'].'</td>';
                if(isset($_REQUEST['checkdep']))        $BODY.='<td bgcolor="'.$bgcolor.'">'.$r['dept'].'</td>';
                $BODY.= '<td bgcolor="'.$bgcolor.'">'.$r['turn'].'</td>';
                $BODY.= '<td bgcolor="'.$bgcolor.'">'.$r['timer'].'</td>';
                $BODY.='<td bgcolor="'.$bgcolor.'">';

                $code_text = '';
                switch($r['code'])
                {
                    case 'I': $code_text = '<b>Вход</b>&nbsp;';break;
                    case 'O': $code_text = '<b>Выход</b>&nbsp;';break;
                    case 'B': $code_text = '<b>Вход по разр. док.</b>&nbsp;';break;
                    case 'D': $code_text = '<b>Выход по разр. док.</b>&nbsp;';break;
                    case 'V': $code_text = '<b>Вход по разр. охр. </b>&nbsp;';break;
                    case 'W': $code_text = '<b>Выход по разр. охр</b>. &nbsp;';break;
                    case 'P': $code_text = '<b>Посещение столовой</b>. &nbsp;';break;
                    case 'H': $code_text = '<b>Ворота открыты для въезда</b>. &nbsp;';break;
                    case 'N': $code_text = '<b>Ворота открыты для выезда</b>. &nbsp;';break;
                    case 'G': $code_text = '<b>Выход с гостевым пропуском</b>. &nbsp;';break;
                    case 'i': $code_text = '<b>незавершённый вход</b>. &nbsp;';break;
                    case 'o': $code_text = '<b>незавершённый выход</b>. &nbsp;';break;
                    case 'g': $code_text = '<b>незавершённый выход с гостевым пропуском</b>. &nbsp;';break;
                    case 'z': $code_text = '<b>двойные засечки на входе</b>. &nbsp;';break;
                    case 'q': $code_text = '<b>двойные засечки на выходе</b>. &nbsp;';break;
                    case 'p': $code_text = '<b>пропуск заблокирован</b>. &nbsp;';break;
                    case 't': $code_text = '<b>турникет заблокирован</b>. &nbsp;';break;
                    case 'd': $code_text = '<b>сюда нет допуска</b>. &nbsp;';break;
                    case 'w': $code_text = '<b>попытка прохода в неурочное время</b>. &nbsp;';break;
                    case 'y': $code_text = '<b>выход с гостевым пропуском запрещён</b>. &nbsp;';break;
                    case 'x': $code_text = '<b>незарегистрированный пропуск</b>. &nbsp;';break;
                    default:break;
                 }
                 $str = $r['code'];
                 $str = str_replace($r['code'],$code_text,$str);
                 $BODY.= $str.'</td>';

		$BODY.='</tr>';
                 }
                $BODY.='</table>';

       break;
   
// ----------------------другие отчеты 	  
      default:break;

   }
}
if(isset($_REQUEST['excelflg']))
{
$fname = 'report.xls';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$fname");
header("Expires: 0");
//header("Content-Transfer-Encoding: binary"); 
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");
echo '<meta http-equiv=Content-Type content="text/html; charset=utf-8">';//наверн над закоментить
}

echo $BODY;
?>