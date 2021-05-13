<?php

include("include/input.php");
require_once("classes/base/containers2.h");
require_once("include/common.php");
require("include/head.php");


session_write_close();
if(CheckAccessToModul(43,$_SESSION['modulaccess'])==false)
{
    
    echo '<center><span class="text"><b>Доступ закрыт. Не хватает прав доступа</b></span><br>';
    exit();
}

$_INCLUDES = array();
$_INCLUDES[0] = '<link rel="stylesheet" type="text/css" href="include/menu.css">';
$_INCLUDES[1] = '<link rel="stylesheet" type="text/css" href="sync/css/tabpanel.css">';
$_INCLUDES[2] = '<link rel="stylesheet" type="text/css" href="sync/css/base.css">';
$_INCLUDES[3] = '<script language="JavaScript" src="js/calendar/lang/calendar-ru.js"></script>';

$_INCLUDES[4] = '<script type="text/javascript" src="gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$_INCLUDES[5] = '<script type="text/javascript" src="techreport/script/techreport.js"></script>';
$_INCLUDES[6] = '<script type="text/javascript" src="gscripts/jquery/plugins/jquery.pickmeup.js"></script>';
$_INCLUDES[7] = '<LINK REL=STYLESHEET TYPE="text/css" HREF="techreport/style/techreport.css">';
$_INCLUDES[8] = '<link rel="stylesheet" type="text/css" href="gstyles/pickmeup.css">';
$_INCLUDES[9] = '<link rel="stylesheet" type="text/css" href="include/style.css">';
$_INCLUDES[10] = '<script type="text/javascript" src="include/controllers.js"></script>';
echo PrintHeadNew('СКУД','Технический отчёт',$_INCLUDES);
require_once("include/menu.php");
$activeTab = 0;     
        
  
$tab_turn_unit .= '<form id = "reportData" name="reportData" action="techreport.php" method="POST" target="_blank" >
  <input id="act" type="hidden" name="act">
  <input id="per" type="hidden" name="per" value="day">
  <table align="center" class="Options">
      <tr>
          <td>&nbsp;&nbsp;Укажите дату начала периода&nbsp;&nbsp;
              <input type="text" id="date" name="date" value="'.date('d.m.Y').'" size="8" class="field" readonly>&nbsp;

              &nbsp;&nbsp;&nbsp;
          </td>
          <td>сформировать
              <select id="sel">
                  <option selected value="day">за день</option>
                  <option value="week">за неделю</option>
                  <option value="mon">за месяц</option>
                  <option value="mon3">за 3 месяца</option>
                  <option value="mon6">за 6 месяцев</option>
                  <option value="year">за год</option>
              </select>
          </td>
      </tr>
      <tr >
          <td colspan = "2" height="110px">
              <div id="exp"class="LabelBt" 
                    onclick="GoTurn()">
                   <img src="../techreport/image/turn.jpg">
                    <div>Турникеты и двери</div>
                </div>
                <div id="sdfsd" class="LabelBt" 
                     onclick="GoUnit()">
                    <img  src="../techreport/image/unit.jpg">
                    <div>Юниты и сетевые контроллеры</div>
                </div>
          </td>
      </tr>
  </table>
</form>';




   $browseTurnUrl = 'objectviewer.php?object=turnnumlist&amp;elIdTurn=reportfrm.trlist&amp;elTurnName=reportfrm.turnName';
   $browseDeptUrl = 'objectviewer.php?object=departments_st&amp;elIdDept=reportfrm.depart&amp;elDeptName=reportfrm.deptName';
   $browseEvntUrl = 'objectviewer.php?object=eventstype&amp;elIdEv=reportfrm.evlist&amp;elEvName=reportfrm.evtype';
$filter_ev_all = '<table id = "ev_rep_filter" border="0" cellpadding="3" width="100%" cellspacing="2" class="dtab" align="center" style="vertical-align: bottom;">
                <tr  class="tablehead">
                    <td colspan="3">
                        Фильтр:
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <table border="0" width="100%" cellspacing="1" cellpadding="1" align="center" >
                            <tr class="tablehead" >
                                <td align=center >Таб.№</td>
                                <td align=center >Фамилия</td>
                                <td align=center >Имя</td>
                                <td align=center >Отчество</td>
                                <td align=center >Пропуск</td>
                                <td align=center >Отдел</td>
                                <td align=center >Точка прохода</td>
                                <td align=center >Типы событий</td>
                            </tr>
                            <tr>
                                <td align=center ><input type="text" class="input" name="tab_num" value="" size="5" maxlength = "20"></td>
                                <td align=center ><input type="text" class="input" name="family" value="" size="15" maxlength = "20"></td>
                                <td align=center ><input type="text" class="input" name="name" value="" size="15" maxlength = "20"></td>
                                <td align=center ><input type="text" class="input" name="secname" value="" size="15" maxlength = "20"></td>
                                <td align=center ><input type="text" class="input" name="px_code" value="" size="15" maxlength = "16"></td>
                                <td align=center >
                                    <input type="text" name="deptName" id="deptName" value="" size="15" class="input">&nbsp;
                                    <input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseDeptUrl.'\',\'\',\'width=420,height=480\')">&nbsp;
                                    <input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'deptName\');reportfrm.depart.value=0">
                                    <input type="hidden" value="0" class="input" name="depart" id="depart" size="25">
                                </td>
                                <td align=center >
                                    <input type="text" name="turnName" id="turnName" value="" size="15" class="input">&nbsp;
                                    <input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseTurnUrl.'\',\'\',\'width=420,height=480\')">&nbsp;
                                    <input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'turnName\');reportfrm.trlist.value=0">
                                    <input type="hidden" value="0" class="input" name="trlist" id="trlist" size="25">
                                </td>
                                <td align=center >
                                    <input type="text" name="evtype" id="evtype" value="" size="15" class="input">&nbsp;
                                    <input type="button" value="..."  class="sbutton" onclick="window.open(\''.$browseEvntUrl.'\',\'\',\'width=420,height=480\')">&nbsp;
                                    <input type="button" value="очистить" class="sbutton" onclick="javascript:clearField(\'evtype\');reportfrm.evlist.value=0">
                                    <input type="hidden" value="0" class="input" name="evlist" id="evlist" size="25">
                                </td>
                             </tr>
                         </table>
                    </td>
                </tr>
                 <tr class="tablehead">
                    <td   align="left" width="20%">Время с <input type="text" class="input" name="time_begin" value="00:00:00" size="8" maxlength = "8">
                     по <input type="text" class="input" name="time_end" value="23:59:59" size="8"  maxlength = "8">
                     </td>
                     <td  align="center" width="9%">
                         Юнит <input type="text" class="input" name="unit" value="" size="5" maxlength = "3">
                    </td>
                    <td  align="right">
                        &nbsp;<input type="button" class="sbutton" value="Количество событий за период" onclick=\'toCountEvents()\' />
                        <span id="spancnt"></span>
                        &nbsp;&nbsp;&nbsp; <input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;
                     </td>
                 </tr>
                
              </table>';
    
        
$tab_proc_log = '<div id="dblog">
            <table border="0" cellpadding="3" width="100%" cellspacing="2" align="left"><tr>
                    <td width="215px" valign="top">
                        
                        <input type="checkbox" name="only_err" ><span class="text">&nbsp;Выводить только ошибки</span><br>
                    Лог по процедурам 
                            <select name="pr_type" value ="0" class="select">
                            <option value="0">Все</option>
                            <!--option value="1">Выгрузка управляющих файлов</option-->
                            <option value="2">Загрузка событий</option>
                            <option value="3">Расчёт наработки</option>
                            <option value="4">Синхронизация с КС</option>
                            <option value="5">Другие</option>
                        </select>
                    
                        <br><br><br><br><br><input class = "Excel" type="checkbox" name="excelflg"><span class="text">&nbsp;Вывести результаты в Excel</span><br>
                    </td>
                </tr>
                <tr class="tablehead">
                    <td colspan="2" align="right">
                        &nbsp;&nbsp;&nbsp; <input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;
                     </td>
                 </tr>
            </table>
            </div>';

$tab_trig = '<div id="trig_log">
            <table border="0" cellpadding="3" width="100%" cellspacing="2" align="left">
                
                <tr>
                    <td >Лог по операциям &nbsp;&nbsp;
                        <select name="tr_type" value ="0" class="select">
                            <option value="0">Все</option>
                            <option value="1">Вставка</option>
                            <option value="2">Обновление</option>
                            <option value="3">Удаление</option>
                        </select>
                    <br><br>Искать текст в логе:  
                        <input type="text" class="input" name="search_text" value="" size="20" maxlength="20">
                    <br><br>Искать по логину:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" class="input" name="search_login" value="" size="20" maxlength="20">
                    <br><br>Искать по ip:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" class="input" name="search_ip" value="" size="20" maxlength="20">
                    
                        <br><br><br><br><br><input class = "Excel" type="checkbox" name="excelflg"><span class="text">&nbsp;Вывести результаты в Excel</span><br>
                    </td>
                </tr>
                <tr class="tablehead">
                    <td colspan="2" align="right">
                        &nbsp;&nbsp;&nbsp; <input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;
                     </td>
                 </tr>
            </table>
            </div>';
        
        
$tab_all_ev = '<table border="0" cellpadding="3" width="100%" cellspacing="2" align="left">
                
                <tr>
                    <td width="215px" valign="top">
                        &nbsp;&nbsp;&nbsp;&nbsp; <b>Код события</b><br>
                        <input type="checkbox" name="check_code" ><span class="text">&nbsp;Расшифровка</span><br>
                    </td>
                    <td  width="210px" valign="top">
                       &nbsp;&nbsp;&nbsp;&nbsp; <b>Турникет</b><br>
                        <input type="checkbox" name="check_turn_name"><span class="text">&nbsp;Название</span><br>
                        <input type="checkbox" name="check_turn_group"><span class="text">&nbsp;Группа</span><br>
                        <input type="checkbox" name="check_turn_status"><span class="text">&nbsp;Статус</span><br>
                        <input type="checkbox" name="check_turn_terr"><span class="text">&nbsp;Территории внеш./внутр. </span><br>
                    </td>
                    <td width="220px" valign="top">
                        &nbsp;&nbsp;&nbsp;&nbsp; <b>Сотрудник</b><br>
                        <input type="checkbox" name="check_fio"><span class="text">&nbsp;ФИО</span><br>
                        <input type="checkbox" name="check_dep"><span class="text">&nbsp;Отдел</span><br>
                        <input type="checkbox" name="check_graph"><span class="text">&nbsp;График</span><br>
                        <input type="checkbox" name="check_dopusk"><span class="text">&nbsp;Допуск</span><br>
                        <input type="checkbox" name="check_pers_del"><span class="text">&nbsp;Статус удаления</span><br>
                    </td>
                    <td  valign="top" colspan="4">
                        &nbsp;&nbsp;&nbsp;&nbsp; <b>Код пропуска(события)</b><br>
                        <input type="checkbox" name="check_code_descr"><span class="text">&nbsp;Расшифровка события</span><br>
                        <input type="checkbox" name="check_code_status"><span class="text">&nbsp;Статус</span><br>
                        <input type="checkbox" name="check_code_comm"><span class="text">&nbsp;Комментарий </span><br>
                        <input type="checkbox" name="check_code_del"><span class="text">&nbsp;Статус удаления </span><br>
                    </td>
                </tr>
                 <tr>
                    <td colspan="7"><br><br><br>
                       <input type="checkbox" name="checkall" onclick=SelectAll(document.reportfrm)><span class="text">&nbsp;Выделить всё</span><br>
                       <input class = "Excel" type="checkbox" name="excelflg"><span class="text">&nbsp;Вывести результаты в Excel</span><br>
                       <br>
                       <span class="textnote">Note: При превышении 10000 событий в выбранном периоде не рекомендуется выбирать более 5 дополнительных полей без фильтров.</span><br>
                       <span class="textnote">      В противном случае на вывод отчета может потребоваться значительное количество времени. Не формируйте повторно отчет пока не сформировался первый.</span>
                       <br><br>'.$filter_ev_all.'
                    </td>
                </tr>
            </table>';       
//$tab_all_ev .= $filter_ev_all;     
        
$tab_com_to_unit = '
            <table border="0" cellpadding="3" width="100%" cellspacing="2" align="left">
                <tr>
                 <br>
                    &nbsp;&nbsp;Искать текст в логе:  
                        <input type="text" class="input" name="u_text" value="" size="20" maxlength="20">
                    
                 <br><br>
                    &nbsp;&nbsp;Искать по логину:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
                        <input type="text" class="input" name="u_login" value="" size="20" maxlength="20">
                    
                 <br><br>
                    &nbsp;&nbsp;Искать по ip:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" class="input" name="u_ip" value="" size="20" maxlength="20">
                   
                 <br><br>
                    
                        <br><br><br>&nbsp;&nbsp;<input class = "Excel" type="checkbox" name="excelflg"><span class="text">&nbsp;Вывести результаты в Excel</span><br>
                    </td>
                </tr>
                <tr class="tablehead">
                    <td colspan="2" align="right">
                        &nbsp;&nbsp;&nbsp; <input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;
                     </td>
                 </tr>
            </table>
           ';       
        
$tab_unloaded = '<div id="left_ev" >
            <table border="0" cellpadding="3" width="100%" cellspacing="2" align="left"><tr>
                &nbsp;&nbsp;<input id ="full_check" class = "Excel" type="checkbox" name="full_check"><span class="text">&nbsp;Выполнить полную проверку наличия событий в БД (начиная с событий позднее тех, что были планово удалены)</span><br>
                 <br><br><br><br>
                <tr class="tablehead">
                    <td colspan="2" align="right">
                        &nbsp;&nbsp;&nbsp; <input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;
                     </td>
                 </tr>
            </table>
            </div>';
$tab_login = '<div id="login_log" >
            <table border="0" cellpadding="3" width="100%" cellspacing="2" align="left">
                
                <tr>
                    <td colspan="4">
                        &nbsp;&nbsp;<span >Инфо:</span><br>
                        &nbsp;&nbsp; <span > - Неудачные попытки входа в СКУД выделены красным цветом.</span><br>
                        &nbsp;&nbsp;<span > - В отчёте не отображаются попытки входа если в поле ввода логина были введены запрещённыые символы (разрешены символы латинского алфавита и цифры).</span><br>
                        &nbsp;&nbsp;<span > - Попытки входа с вводом запрешенных символов в поле логина записаны в файле login.log и не попадут в отчёт.</span><br>
                        &nbsp;&nbsp;<span > - Если количество неудачных попыток входа с одного ip адреса превысит 50 за день, то этот ip будет помещён в чёрный список.</span>
                       
                        <br><br><br>&nbsp;&nbsp;<input class = "Excel" type="checkbox" name="excelflg"><span class="text">&nbsp;Вывести результаты в Excel</span><br>
                    </td>
                </tr> 
                
                <tr class="tablehead">
                    <td colspan="2" align="right">
                        &nbsp;&nbsp;&nbsp; <input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;
                     </td>
                 </tr>
            </table>
            </div>';
$tab_data = '<div id="data">
            <table border="0" cellpadding="3" width="100%" cellspacing="2" align="left"><tr>
                    <td width="215px" valign="top">

                    Выберите отчёт: 
                            <select name="pr_type" value ="0" class="select">
                            <option value="0">по пропускам</option>
                            <option value="1">по допускам</option>
                            <option value="2">по документам</option>
                            <option value="3">по графикам</option>
                            <option value="4">по турникетам</option>
                            <option value="5">по настройкам юнитов</option>
                        </select>
                    
                        <br><br><br><br><br><input class = "Excel" type="checkbox" name="excelflg"><span class="text">&nbsp;Вывести результаты в Excel</span><br>
                    </td>
                </tr>
                <tr class="tablehead">
                    <td colspan="2" align="right">
                        &nbsp;&nbsp;&nbsp; <input type="button" class="sbutton" value="сформировать" onClick=Go(document.reportfrm)>&nbsp;
                     </td>
                 </tr>
            </table>
            </div>';

$BODY .= '<form id="reportfrm" name="reportfrm" action="techreports.php" method="POST" target="_blank">
            <input class="rtype" type="hidden" name="rtype" id = "rtype">
            <table border="0" cellpadding="3" width="100%" cellspacing="2" class="dtab" align="center" style="vertical-align: bottom;">
                <tr  class="tablehead">
                    
                    <td id = "dates">Период: 
                        с:
                        <input type="text" id="start_date" name="start_date" value="'.date("d.m.Y").'" size="15" disabled readonly class="c_date" />&nbsp;
                        
                         по:
                        <input type="text" id="fin_date" name="fin_date" value="'.date("d.m.Y").'" size="15" disabled readonly class="c_date" />&nbsp;
                        
                     </td>
                
          </table>';
$BODY .= '<table border="0"  cellpadding="0" cellspacing="0" width="100%">';
$BODY .= '<br>';
$BODY .= '<tr>';
$BODY .= '<td>';


//вывод
$tabPanel = new СTabPanel('basePanel');
$tabPanel->setStyle('container','dtab');
$tabPanel->setStyle('header','tabSheetHeader');
$tabPanel->setStyle('activeHeader','activeTabSheet');
$tabPanel->setStyle('sheet','sheet');
$tabPanel->setStyle('activeSheet','activeSheet');
$tabPanel->setStyle('headerMouseOver','tabSheetMouseOver');
$tabPanel->setStyle('headerMouseOut','tabSheetMouseOut');
//$tabPanel->setOption('clietHeight','500');
$tabPanel->setOption('activeSheet',$activeTab);

$tabPanel->addSheet('Информация о работе туникетов и юнитов',$tab_turn_unit);
$tabPanel->addSheet('Логи процедур БД',$tab_proc_log);
$tabPanel->addSheet('Триггеры БД',$tab_trig);
$tabPanel->addSheet('Аудит входа в СКУД',$tab_login);
$tabPanel->addSheet('Все события',$tab_all_ev);
$tabPanel->addSheet('Логи команд к юнитам',$tab_com_to_unit);
$tabPanel->addSheet('Не загруженные события',$tab_unloaded);
$tabPanel->addSheet('Управляющие данные',$tab_data);



$BODY .= $tabPanel->render();



$BODY .= '</td></tr></table></form>';

//////////////////////////////////////////////////////////////////////////////////////////////////////
//выводим ошибки


echo $BODY;

echo PrintFooterI();
