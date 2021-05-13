<?php

//скрипт выполняет необходимые действия, после чего закрывает
// окно из которого он был вызван или просто переходит на нужную страницу
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
$BODY='<script>
           window.onload=function(){
           //alert("ERROR");
          window.close();
           }</script>';

$errflag1 = 0;
$errflag2 = 0;
if(!isset($_REQUEST['action']))
{
    $errflag1 = 1;
    $_REQUEST['action'] = '';
}
if(!isset($_POST['act']))
{
    $errflag2 = 1;
    $_POST['act'] = '';
}

if($errflag1 == 1 && $errflag2 == 1)
{
     echo $BODY; exit();
     unset($errflag1);
     unset($errflag2);
}


$q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
pg_query($q);
if($_REQUEST['action']=='insert')
{
    if(isset($_REQUEST['dest']) && $_REQUEST['dest']=='visitings')
    {
        // print_r($_REQUEST);
         $user=$_SESSION['iduser'];
         $start_date=CheckString($_REQUEST['start_date']).' '.CheckString($_REQUEST['start_time']);
         $end_date=CheckString($_REQUEST['end_date']).' '.CheckString($_REQUEST['end_time']);
         $visitor=$_REQUEST['visitor'];
         $propusk=$_REQUEST['propusk_id'];
         $zona=$_REQUEST['dopusk_id'];
         $person=$_REQUEST['pers_id'];


         $q='select VISIT_W_I_VISITES(\''.$start_date.'\',
                               \''.$end_date.'\',
                               '.$visitor.',
                               '.$propusk.',
                               '.$zona.',
                               '.$person.',
                               '.$user.')';

         echo $q;
         pg_query($q);

         echo  $BODY;

    }
}

if(isset($_POST['act']) && $_POST['act'] == 'execoperation')
{

       if(!isset($_SESSION['selpers']) || sizeof($_SESSION)==0)
       {
          echo 'Не выделено ни одного сотрудника';exit();
       }
        //print_r($_POST);
        $res='';
        $msg = '<span style="font-family:Verdana;font-size:10pt;color:red;">{TEXT}</span>';
        $res='';
        if(!isset($_POST['num_operation']) || IdValidate($_POST['num_operation']) == false )
        {
          $text='Ошибка:Неопределённый тип операции.<br>Операция отменена';
          echo str_replace("{TEXT}",$text,$msg);
          unset($text);
          unset($res);
          unset($msg);
          exit();
        }
        $op = $_POST['num_operation'];
        switch ($op) {
          case 1:
             if(isset($_POST['cond']) && $_POST['cond'] == 'department' && isset($_POST['condval']) && IdValidate($_POST['condval'])==true)
              {
                   //print_R($_REQUEST);
                   $q = 'SELECT * FROM BASE_DEPT WHERE ID='.$_POST['condval'];
                   $r = pg_fetch_array(pg_query($q));
                   $department = $r['name'];
                   $id_department = $r['id'];
                 //выполняем процедуру
                //print_r($_SESSION['selpers']);
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_DEPT('.$_SESSION['selpers'][$i].','.$id_department.')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','Перевод сотрудников в отдел - '.$department.'');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='SELECT * FROM BASE_PERSONAL WHERE ID='.$_SESSION['selpers'][$i];
                       $r = pg_fetch_array(pg_query($q));
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['position'].'</td>';
                           if($r['id_dept'] == $id_department)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Переведён</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не переведён</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }

            break;
          case 2:
                 //Графики
             if(isset($_POST['cond']) && $_POST['cond'] == 'schedule' && isset($_POST['condval']) && IdValidate($_POST['condval'])==true)
              {
                   $q = 'SELECT * FROM BASE_GRAPH_NAME WHERE ID='.$_POST['condval'];
                   $r = pg_fetch_array(pg_query($q));
                   $schedule = $r['name'];
                   $id_schedule = $r['id'];
                 //выполняем процедуру

                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {

                           $q = 'select BASE_W_U_GROUP_GRAPH('.$_SESSION['selpers'][$i].','.$id_schedule.')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','Изменение графика. Новый график - '.$schedule.'');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='SELECT * FROM BASE_PERSONAL WHERE ID='.$_SESSION['selpers'][$i];
                       $r = pg_fetch_array(pg_query($q));
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['position'].'</td>';
                           if($r['id_graph'] == $id_schedule)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Назначен</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не назначен</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();
                 echo $res;
                }
              }

            break;
          case 3:
              if(isset($_POST['cond']) && $_POST['cond'] == 'zone' && isset($_POST['condval']) && IdValidate($_POST['condval'])==true)
              {

                   $q = 'SELECT * FROM BASE_ZONE WHERE ID='.$_POST['condval'];
                   $r = pg_fetch_array(pg_query($q));
                   $zone = $r['name'];
                   $id_zone = $r['id'];

                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_ZONE('.$_SESSION['selpers'][$i].','.$id_zone.')';
                           pg_query($q);

                     }


                 $res .= PrintHead('СКУД','Изменение рабочей зоны. Новая рабочая зона - '.$zone.'');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='SELECT * FROM BASE_PERSONAL WHERE ID='.$_SESSION['selpers'][$i];
                       $r = pg_fetch_array(pg_query($q));
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['position'].'</td>';
                           if($r['id_zone'] == $id_zone)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Назначена</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не назначена</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }


            break;
          case 4:

              if(isset($_POST['cond']) && $_POST['cond'] == 'algoritm' && isset($_POST['condval']) && is_numeric($_POST['condval'])>0 && $_POST['condval']>=0)
              {
                   
                   $q = 'SELECT * FROM BASE_WORK_TYPE WHERE ID='.$_POST['condval'];
                   $r = pg_fetch_array(pg_query($q));
                   $work_type = $r['name'];
                   $id_work_type = $r['id'];
                 
                
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           
                           $q = 'select BASE_W_U_GROUP_WORK_TYPE('.$_SESSION['selpers'][$i].','.$id_work_type.')';
                           pg_query($q);
                           
                     }


                 $res .= PrintHead('СКУД','Изменение типа расчёта рабочего времени. Новый тип - '.$work_type.'');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='SELECT * FROM BASE_PERSONAL WHERE ID='.$_SESSION['selpers'][$i];
                       $r = pg_fetch_array(pg_query($q));
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['position'].'</td>';
                           if($r['id_work_type'] == $id_work_type)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Назначен</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не назначен</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
          case 5:
              //Блокирование пропусков
              if(isset($_POST['cond']) && $_POST['cond'] == 'blockpass')
              {
                 //выполняем процедуру
                $pass ='';
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_LOCK('.$_SESSION['selpers'][$i].',\'1\')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','Блокирование пропусков');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));
                      // print_r($r);
                      // echo '<br><br>';
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = 'Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],0)==1)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Блокирован</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не блокирован.'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
          case 6:
                  //Разлокирование пропусков
              if(isset($_POST['cond']) && $_POST['cond'] == 'unblockpass')
              {
                 //выполняем процедуру
                $pass ='';
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_LOCK('.$_SESSION['selpers'][$i].',\'0\')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','Разблокирование пропусков');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));
                      // print_r($r);
                      // echo '<br><br>';
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = 'Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],0)==0)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Разблокирован</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не блокирован.'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
          case 7:
              //Назначить администратором
              if(isset($_POST['cond']) && $_POST['cond'] == 'doadmin')
              {
                 //выполняем процедуру
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_ADMIN('.$_SESSION['selpers'][$i].',\'1\')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','Назначить Администратором');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));
                      // print_r($r);
                      // echo '<br><br>';
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = 'Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],2)==1)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Назначен</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не назначен.'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
          case 8:
                //Снять администратором
              if(isset($_POST['cond']) && $_POST['cond'] == 'undoadmin')
              {
                 //выполняем процедуру
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_ADMIN('.$_SESSION['selpers'][$i].',\'0\')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','Назначить Администратором');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));
                      // print_r($r);
                      // echo '<br><br>';
                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = 'Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],2)==0)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Снят</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не снят .'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
            
           case 9:
               //Удаение
              if(isset($_POST['cond']) && $_POST['cond'] == 'remove')
              {
                 //выполняем процедуру
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_DELETE('.$_SESSION['selpers'][$i].')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','Удаление сотрудников');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_TRASH('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));

                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                           if($r['del']==1)
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Удалён</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не удалён</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
            case 10:
                //установить контроль двойных засечек
              if(isset($_POST['cond']) && $_POST['cond'] == 'dodouble')
              {
                 //выполняем процедуру
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_DOUBLE('.$_SESSION['selpers'][$i].',\'1\')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','установить контроль двойных засечек');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));

                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = 'Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],3)==0)
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не установлен</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Установлен.'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();


                 echo $res;
               }
              }
            break;
            case 11:
                //снять контроль двойных засечек
              if(isset($_POST['cond']) && $_POST['cond'] == 'undodouble')
              {
                 //выполняем процедуру
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_DOUBLE('.$_SESSION['selpers'][$i].',\'0\')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','снять контроль двойных засечек');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));

                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = '/Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],3)==1)
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не снят</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Снят'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
	    case 12:
                //установить гостевой статус
              if(isset($_POST['cond']) && $_POST['cond'] == 'setguest')
              {
                 //выполняем процедуру
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_GUEST('.$_SESSION['selpers'][$i].',\'1\')';
			  // echo $q;
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','установить гостевой статус');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));

                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = 'Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],1)==0)
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не установлен</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Установлен.'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
            case 13:
                //снять гостевой статус
              if(isset($_POST['cond']) && $_POST['cond'] == 'unsetguest')
              {
                 //выполняем процедуру
                if(sizeof($_SESSION['selpers']) > 0)
                {
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                           $q = 'select BASE_W_U_GROUP_GUEST('.$_SESSION['selpers'][$i].',\'0\')';
                           pg_query($q);
                     }


                 $res .= PrintHead('СКУД','снять гостевой статус');
                 $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                 $res .= '<tr  class="tablehead">';
                       $res .= '<td align="center">ФИО</td>';
                       $res .= '<td align="center">Должность</td>';
                       $res .= '<td align="center">Статус выполнения</td>';
                 $res .= '</tr>';
                     for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                     {
                       $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                       $r = pg_fetch_array(pg_query($q));

                       $res .= '<tr class="tabcontent">';
                            $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                            $res .= '<td align="center">'.$r['pos'].'</td>';

                          if($r['status']=='')$pass = 'Пропуск не назначен';else $pass ='';
                           if(GetCodeValue($r['status'],1)==1)
                                $res .= '<td align="center"><font face="Verdana" color="red" size="2">Не снят</font></td>';
                            else
                                $res .= '<td align="center"><font face="Verdana" color="green" size="2">Снят.'.$pass.'</font></td>';
                       $res .= '</tr>';

                      }
                 $res .= '</table>';
                 $res.=PrintFooter();

                 echo $res;
               }
              }
            break;
            case 14:
                //смещение графика
                if(isset($_POST['cond']) && $_POST['cond'] == 'groffset')
                {
                    if(is_int(intval($_POST['offset'])) && $_POST['offset']>=0){
                        //выполняем процедуру
                        if(sizeof($_SESSION['selpers']) > 0)
                        {
                            for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                            {
                                  $q = 'select BASE_W_U_GROUP_GROFFSET('.$_SESSION['selpers'][$i].','.$_POST['offset'].')';
                                  pg_query($q);
                            }


                        $res .= PrintHead('СКУД','смещение графика');
                        $res .= '<br><table border="0" cellpadding="1" cellspacing="1" width="0" align="center" width="100%">';
                        $res .= '<tr  class="tablehead">';
                              $res .= '<td align="center">ФИО</td>';
                              $res .= '<td align="center">Должность</td>';
                              $res .= '<td align="center">Статус выполнения</td>';
                        $res .= '</tr>';
                            for($i = 0; $i < sizeof($_SESSION['selpers']); $i++)
                            {
                              $q='select * from BASE_W_S_PERSONAL_ONCE('.$_SESSION['selpers'][$i].')';
                              $r = pg_fetch_array(pg_query($q));

                              $res .= '<tr class="tabcontent">';
                                   $res .= '<td align="center">'.$r['family'].'&nbsp;'.$r['name'].'&nbsp;'.$r['secname'].'</td>';
                                   $res .= '<td align="center">'.$r['pos'].'</td>';


                                  if($r['graph_offset']!=$_POST['offset'])
                                       $res .= '<td align="center"><font face="Verdana" color="red" size="2">Смещение не изменено</font></td>';
                                   else
                                       $res .= '<td align="center"><font face="Verdana" color="green" size="2">Смещение изменено.</font></td>';
                              $res .= '</tr>';

                             }
                        $res .= '</table>';
                        $res.=PrintFooter();

                        echo $res;
                    }
                }
              }
            break;
          default: break;
        }
}

?>