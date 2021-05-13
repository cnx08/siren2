<?php
include ("../include/input.php");
require ("include/common_d.php");

$INCLUDES=array();
$INCLUDES[0] = '<link rel="stylesheet" type="text/css" href="styles/menu.css">';
$INCLUDES[1] = '<script type="text/javascript" src="scripts/expan_array.js"></script>';
$INCLUDES[2] = '<script type="text/javascript" src="scripts/doc_types.js"></script>';
$INCLUDES[7] = '<script type="text/javascript" src="scripts/documents.js"></script>';
$INCLUDES[4] = '<script type="text/javascript" src="include/controllers.js"></script>';
$INCLUDES[5] = '<script type="text/javascript" src="include/_request_functions.js"></script>';
$INCLUDES[6] = '<script type="text/javascript" src="include/_library_elements.js"></script>';
$INCLUDES[3] = '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$INCLUDES[8] = '<link rel="stylesheet" type="text/css" href="../gstyles/pickmeup.css">';
$INCLUDES[9] = '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup.js"></script>';

$BODY='';
$BODY.=PrintHead('Документы','Документы',$INCLUDES);

$BODY .= '<script>';
$BODY .= 'var _CODS = new Array();';
       $q_cod = 'select * from TABL_W_S_SING_DOC';
      $cods = pg_query($q_cod);
      $BODY .= '_CODS[0] = "";';
       while($r = pg_fetch_array($cods))
       {
         $BODY .= '_CODS['.$r['id'].'] = "'.$r['description'].'";';
       }
$BODY .='
function showCodSpravka(viewsEl,callerEl)
{
  if(!viewsEl)return; var n = callerEl.options[callerEl.selectedIndex].value;
  if(n>0){cleanNode(viewsEl); viewsEl.value = _CODS[n];}
  else
  {cleanNode(viewsEl);}
}';
$BODY .= '</script>';
$FILTER = '';
$FILTER .= '<form id="filtrfrm" name="filtrfrm" action="" method="POST" target="_blank">';
$FILTER .= '<table border="0" cellpadding="2" cellspacing="2" align="center" width="100%">';
$FILTER .= '<tr>
             <td colspan="3" align="center" bgcolor="silver"><span class="doctext">Фильтр</span></td>
            </tr>';
$FILTER .= '<tr>';
$FILTER .= '<td></td>';
$FILTER .= '<td></td>';
$FILTER .= '<td><input type="radio" name="ftype" value="document" checked onClick = \'onFilterTypeSelect(this)\'><span class="text">По документам</span>
                <input type="radio" name="ftype" value="personal" onClick = \'onFilterTypeSelect(this)\' /><span class="text">По сотрудникам</span>
           </td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Таб. номер</span></td>';
$FILTER .= '<td><input type="text" class="input" name="tab_num" value="" size="25"></td>';
$FILTER .= '<!--td><span class="text">По дате&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="text" name="f_doc_date" class="input" value="" size="10" readonly>
                <input type="button" value="..." class="sbutton" name="date_but" onClick=\'ShowCalendar(document.filtrfrm.f_doc_date,1900,2030,"dd.mm.yyyy")\' />
            </td-->';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Фамилия</span></td>';
$FILTER .= '<td><input type="text" class="input" name="family" value="" size="25"></td>';
$FILTER .= '<td rowspan = 2><span class="text">За период<br/>
                 С:
                <input type="text" id ="f_start_doc_date" name="f_start_doc_date" class="input" value="'.date("d.m.Y").'" size="10" readonly>
               
                 По:
                <input type="text" id="f_end_doc_date" name="f_end_doc_date" class="input" value="'.date("d.m.Y").'" size="10" readonly >
                
                </span>
            </td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Имя</span></td>';
$FILTER .= '<td><input type="text" class="input" name="fname" value="" size="25"></td>';
$FILTER .= '<td ><span class="text"></span></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Отчество</span></td>';
$FILTER .= '<td><input type="text" class="input" name="secname" value="" size="25"></td>';
$FILTER .= '<td><span class="text"></span></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Должность</span></td>';
$FILTER .= '<td><input type="text" class="input" name="position" value="" size="25"></td>';
$FILTER .= '<td ><span class="text">Тип:</span>'.
            InsertSelect('select * from BASE_W_S_DOC_TYPE('.$_SESSION['iduser'].')','id',0,'','f_doctype','style="width:172px" class="select"','все типы',0,'').'
            </td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">Отдел</span></td>';
$FILTER .= '<td>';
$FILTER .= InsertSelect('select * from BASE_W_S_DEPT('.$_SESSION['iduser'].')','id',0,'','depart','style="width:172px" class="select"','все отделы',0,'');
$FILTER .= '</td>';
$FILTER .= '<td><span class="text"></span></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .= '<td><span class="text">График</span></td>';
$FILTER .= '<td>';
$FILTER .= InsertSelect('select * from BASE_W_S_GRAPH_NAME(NULL)','id',0,'','graph','style="width:172px" class="select"','все графики',1,'');
$FILTER .= '</td>';
$FILTER .= '<td><span class="text"></span></td>';
$FILTER .= '</tr>';
$FILTER .= '<tr>';
$FILTER .='<td colspan="3" align="left">
           <input type="button" value="найти" class="sbutton" onClick = \'onFilter2(document.filtrfrm)\' />
           <input type="button" value="очистить фильтр" class="sbutton" onClick = \'ClearFilter(document.filtrfrm)\' />
			<input type="button" value="очистить все" class="sbutton" onClick = \'ClearAll(document)\' />
           </td>';
$FILTER .= '</tr>';
$FILTER .= '</table>';
$FILTER .= '</form>';

$PERS_LIST ='';
$PERS_LIST .= '<table border="0" cellpadding="2" cellspacing="2" align="center" width="100%">';
$PERS_LIST .= '<tr>
             <td align="center" bgcolor="silver"><span class="doctext">Список сотрудников</span></td>';
$PERS_LIST .='</tr>';
$PERS_LIST .= '<tr>';
$PERS_LIST .= '<td>';
//$PERS_LIST .= '<div id="pers_list" class="list" ></div>';
$PERS_LIST .= '</td>';
$PERS_LIST .= '</tr>';
$PERS_LIST .= '</table>';

$DOC_LIST ='';
$DOC_LIST .= '<table border="0" cellpadding="2" cellspacing="2" align="center" width="100%">';
$DOC_LIST .= '<tr>
             <td align="center" bgcolor="silver"><span class="doctext">Список документов</span></td>';
$DOC_LIST .='</tr>';
$DOC_LIST .= '<tr>';
$DOC_LIST .= '<td>';

$DOC_LIST .= '</td>';
$DOC_LIST .= '</tr>';
$DOC_LIST .= '</table>';

$ORDER_DOC = '';
$ORDER_DOC .= '<div id="order_div" style="display:none;width:100%" class="body">';
$ORDER_DOC .='<form id="order_doc" name="order_doc" action="" method="POST" >';
$ORDER_DOC .= '<table border="0" cellpadding="2" cellspacing="2" width="100%">
    <tr>
      <td colspan="3" align="center"  bgcolor="silver"><span class="doctext">Приказ</span></td>
    </tr>
    <tr>
        <td> <span class="text">Дата начала</span></td>
        <td ><input type="text" id="date_order" name="date_order" class="input" value="'.date("d.m.Y").'" readonly>
            
        </td>
         <td rowspan="1" align="right"><span class="text">Код &nbsp;</span>'.InsertSelect('select * from TABL_W_S_SING_ORD_DOC','id',0,'','code_ord','style="width:80px" class="select"','-',0,'onchange = showCodSpravka(document.opr_doc.cod_spravka,this)').'</td>
        
        </td>
    </tr>
    <tr>
	<td> <span class="text">Дата окончания</span></td>
        <td ><input type="text" id="date_order_end" name="date_order_end" class="input" value="'.date("d.m.Y").'" readonly>
           
        </td>
        <td rowspan="4" align="right"><span class="text">Обоснование</span><br>
        <textarea name="desc_order" class="input" rows="5" cols="40"></textarea>
    </tr>
	
    <tr>
      <td><span class="text">Смена</span></td>
      <td>'.InsertSelect('select * from BASE_W_S_SMENA(NULL)','id',0,'','smena','style="width:185px" class="select"','все смены',0,'').'</td>
    </tr>
    <tr>
      <td><span class="text">Допуск</span></td>
      <td>'.InsertSelect('select * from BASE_W_S_DOPUSK(NULL)','id',0,'','dopusk','style="width:185px" class="select"','все допуска',0,'').'</td>
    </tr>
    <tr>
      <td><span class="text">Зона</span></td>
      <td>'.InsertSelect('select * from BASE_W_S_ZONE(NULL)','id',0,'','zone','style="width:185px" class="select"','все зоны',0,'').'</td>
    </tr>
    <tr>
      <td colspan="3" align="left">
          <input type="button" value="создать" class="sbutton" onclick=\'CreateOrderDoc(document.order_doc)\' />
          <input type="button" value="очистить" class="sbutton" onclick=\' ClearForm(document.order_doc)\' />
      </td>
    </tr>

  </table>';
$ORDER_DOC .= '</form>';
$ORDER_DOC .= '</div>';

$SANCTION_DOC = '';
$SANCTION_DOC .= '<div id="sanction_div" style="display:none;width:100%" class="body">';
$SANCTION_DOC .='<form id="sanction_doc" name="sanction_doc" action="" method="POST" >';
$SANCTION_DOC .= '<table border="0" cellpadding="2" cellspacing="2" width="100%">
    <tr>
      <td colspan="3" align="center"  bgcolor="silver"><span class="doctext">Разрешающий</span></td>
    </tr>
    
    <tr>
        <td colspan="2" align="center"> <span class="text">Дата начала и окончания действия документа</span></td>
        <td rowspan="5" align="right"><span class="text">Обоснование</span><br>
            <textarea name="desc_sanction" class="input" rows="6" cols="40"></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">c <input type="text" id="startdate_sanction" name="startdate_sanction" class="input" size="8" value="'.date("d.m.Y").'" readonly>
           
           по <input size="8" type="text" id="enddate_sanction" name="enddate_sanction" class="input" value="'.date("d.m.Y").'" readonly>
           
        </td>
    </tr>
    <tr>
       <td colspan="2" align="center"><span class="text">Время действия документа для каждого дня</span></td>
    </tr>
    <tr>
        <td colspan="2" align="center">с <input size="3" type="text" name="starttime_sanction" class="input" value="'.date("H:i").'">
        по <input size="3" type="text" name="endtime_sanction" class="input" value="'.date("H:i").'">
        <span class="text">&nbsp;(ЧЧ:ММ)</span>    
        </td>
    </tr>
    <tr>
        <td><span class="text">Зона</span> '.InsertSelect('select * from BASE_W_S_ZONE(NULL)','id',0,'','zone','style="width:185px" class="select"','все зоны',0,'').'</td>
    </tr>
     <tr>
      <td colspan="3" align="left">
          <input type="button" value="создать" class="sbutton" onclick=\'CreateSanctionDoc(document.sanction_doc)\' />
          <input type="button" value="очистить" class="sbutton" onclick=\'ClearForm(document.sanction_doc)\' />
      </td>
    </tr>';
$SANCTION_DOC .= '</table>';
$SANCTION_DOC .= '</form>';
$SANCTION_DOC .= '</div>';
//оправдательные
$OPR_DOC = '';
$OPR_DOC .= '<div id="opr_div" style="display:none;width:100%" class="body">';
$OPR_DOC .='<form id="opr_doc" name="opr_doc" action="" method="POST" >';
$OPR_DOC .= '<table border="0" cellpadding="2" cellspacing="2" width="100%">
    <tr>
      <td colspan="3" align="center"  bgcolor="silver"><span class="doctext">Оправдательный</span></td>
    </tr>
    <tr>
      <td> <span class="text">Дата начала </span></td>
      <td ><input type="text" id="date_opr_st" name="date_opr_st" class="input" value="'.date("d.m.Y").'" readonly>
           
      </td>
      <td rowspan="4" align="right"><span class="text">Обоснование</span><br>
       <textarea name="desc_opr" class="input" rows="10" cols="40"></textarea>
      </td>
    </tr>
    <tr>
      <td> <span class="text">Дата окончания </span></td>
      <td ><input type="text" id="date_opr_end" name="date_opr_end" class="input" value="'.date("d.m.Y").'" readonly>
           
      </td>
    </tr>
    <tr>
      <td><span class="text">Код</span></td>
      <td>'.InsertSelect('select * from TABL_W_S_SING_DOC','id',0,'','code','style="width:80px" class="select"','-',0,'onchange = showCodSpravka(document.opr_doc.cod_spravka,this)').'</td>
    </tr>
    <tr>
       <td></td>
       <td><textarea  name="cod_spravka" rows="6" cols="40" class="input" readonly ></textarea></td>
    </tr>
     <tr>
      <td colspan="3" align="left">
          <input type="button" value="создать" class="sbutton" onclick=\'CreateOprDoc(document.opr_doc, document.pers_list)\' />
          <input type="button" value="очистить" class="sbutton" onclick=\'ClearForm(document.opr_doc)\' />
      </td>
    </tr>';

$OPR_DOC .= '</table>';
$OPR_DOC .= '</form>';
$OPR_DOC .= '</div>';


/*
	12.04.2013 - добавим возможность поиска по документам
*/
$SEARCH_DOC_LIST = '';
$SEARCH_DOC_LIST .= '<div id="search_div" style="display:none;width:100%" class="body">';
$SEARCH_DOC_LIST .='<form id="search_doc" name="search_doc" action="" method="POST" >';
$SEARCH_DOC_LIST .= '<table border="0" cellpadding="2" cellspacing="2" width="100%">
    <tr>
      <td colspan="2" align="center"  bgcolor="silver"><span class="doctext">Поиск документов</span></td>
    </tr>
    <tr>
      <td> <span class="text">Дата начала</span></td>
      <td ><input type="text" id="startdt_search" name="startdt_search" class="input" value="'.date("d.m.Y").'" readonly>
           
      </td>	  	  	  
    </tr>
    
    <tr>
        <td> <span class="text">Дата окончания</span></td>
        <td ><input type="text" id="enddt_search" name="enddt_search" class="input" value="'.date("d.m.Y").'" readonly>
            
        </td>
    </tr>
    <tr>
        <td><span class="text">Тип документа</span></td>
        <td>	
            <select name="search_doctype" class="select" onChange="">
               <option value=0>-- все типы --</option>
               <option value="1">Приказ</option>
               <option value="2">Разрешающий</option>
               <option value="3">Оправдательный</option>
            </select>	
        </td> 
    </tr>
    <tr>
        <td colspan="3" align="left">
            <input type="button" value="найти" class="sbutton" onclick=\'onFilter(document.search_doc)\' />
            <input type="button" value="очистить" class="sbutton" onclick=\'ClearForm(document.search_doc)\' />
        </td>
    </tr>';

$SEARCH_DOC_LIST .= '</table>';
$SEARCH_DOC_LIST .= '</form>';
$SEARCH_DOC_LIST .= '</div>';

// -------------------------------------------------------
$BODY.='<br><table border="0" cellpadding="0" cellspacing="0" width="100%" style="height:90%;" bgcolor="#f5f5f5">
    <tr>
      <td width="60%" height="30%"><div id="filter" class="body">'.$FILTER.'</div></td>
      <td width="40%" height="" valign="top" align="right">
	  <div id="pers_list" class="body" style="overflow:auto;"></div></td>
    </tr>
    <tr>
      <td width="50%" height="30"  bgcolor="silver" align="left" colspan="2">
      <span class="doctext">Создать:</span>
      <span id="div_doctype"><select name="doctype" class="select" onChange=onSelectDocType(this) id="dtyp">
               <option value=0>-</option>';
             if(CheckAccessToModul(20,$_SESSION['modulaccess'])==TRUE)
               $BODY.='<option value="1">Приказ</option>';
             if(CheckAccessToModul(21,$_SESSION['modulaccess'])==TRUE)
               $BODY.='<option value="2">Разрешающий</option>';
             if(CheckAccessToModul(22,$_SESSION['modulaccess'])==TRUE)
               $BODY.='<option value="3">Оправдательный</option>';
		/// 12.04.2013 - для поиска документов по выделенным сотрудникам
		$BODY.='<option value="4">Найти документы</option>';
$BODY.='</select></span>
      &nbsp;
      <span class="doctext">Выделено сотрудников:</span>
      <input id="pers_count" value="0" type="text" size="5" class="input" style="background:silver" readonly>
      &nbsp;
      <span class="doctext">С выделенными документами:</span>
      <select id="docaction" name="docaction" class="input">
          <option value="0">-</option>
		   <option value="1">Просмотреть</option>
          <option value="2">Удалить</option>
      </select>
      <input type="button" value="OK" class="sbutton" onClick=\'ExecuteActionDoc("docaction")\' />
      </td>

      <div id="statusbar" style="width:0%;"></div>
      </td>
    </tr>
    <tr>
      <td width="60%" height="192" valign="top">'.$ORDER_DOC.$SANCTION_DOC.$OPR_DOC.$SEARCH_DOC_LIST.'</td>
      <td width="40%" height="" valign="top" align="right"><div id="doc_list" class="body" style="overflow:auto;" ></div></td>
    </tr>

  </table>';



$BODY.=PrintFooter();


echo $BODY;


?>