<?php
list($msec,$sec)=explode(chr(32),microtime());
$Begin=$sec+$msec;
$BODY = '';
include("include/input.php");
require_once("include/common.php");
if(!isset($_REQUEST['excelflg']))
{
        require("include/head.php");
        $BODY .= PrintHead('','');
        
}
 $BODY.='<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
// print_r($_REQUEST);
$BODY.='<table border="1" cellpadding="0" cellspacing="0" width="1647" height="1" bordercolorlight="#000000">
    <tr>
      <td width="40" height="26" rowspan="6">
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1053;&#1086;&#1084;&#1077;&#1088;
        </font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1087;&#1086;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1087;&#1086;&#1088;&#1103;&#1076;&#1082;&#1091;</font></td>
      <td width="105" height="26" rowspan="6">
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1060;&#1072;&#1084;&#1080;&#1083;&#1080;&#1103;
        &#1080;&#1085;&#1080;&#1094;&#1080;&#1072;&#1083;&#1099;,</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1076;&#1086;&#1083;&#1078;&#1085;&#1086;&#1089;&#1090;&#1100;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">(&#1089;&#1087;&#1077;&#1094;&#1080;&#1072;&#1083;&#1100;&#1085;&#1086;&#1089;&#1090;&#1100;,</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1087;&#1088;&#1086;&#1092;&#1077;&#1089;&#1089;&#1080;&#1103;)</font></td>
      <td width="54" height="26" rowspan="6">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1058;&#1072;&#1073;&#1077;&#1083;&#1100;&#1085;&#1099;&#1081;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1085;&#1086;&#1084;&#1077;&#1088;</font></td>
      <td width="711" height="38" rowspan="2" colspan="16">
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1054;&#1090;&#1084;&#1077;&#1090;&#1082;&#1080;
        &#1086; &#1103;&#1074;&#1082;&#1072;&#1093; &#1080;&nbsp;
        &#1085;&#1077;&#1103;&#1074;&#1082;&#1072;&#1093; &#1085;&#1072;
        &#1088;&#1072;&#1073;&#1086;&#1090;&#1091; &#1087;&#1086;
        &#1095;&#1080;&#1089;&#1083;&#1072;&#1084;
        &#1084;&#1077;&#1089;&#1103;&#1094;&#1072; </font></td>
      <td width="161" height="1" rowspan="2" colspan="2">
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1054;&#1090;&#1088;&#1072;&#1073;&#1086;&#1090;&#1072;&#1085;&#1086;
        &#1079;&#1072;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0">&nbsp;</td>
      <td width="454" height="1" colspan="6">
        <p align="center"><font face="Times New Roman" size="1">&#1044;&#1072;&#1085;&#1085;&#1099;&#1077;
        &#1076;&#1083;&#1103;
        &#1085;&#1072;&#1095;&#1080;&#1089;&#1083;&#1077;&#1085;&#1080;&#1103;
        &#1079;&#1072;&#1088;&#1072;&#1073;&#1086;&#1090;&#1085;&#1086;&#1081;
        &#1087;&#1083;&#1072;&#1090;&#1099; &#1087;&#1086;
        &#1074;&#1080;&#1076;&#1072;&#1084; &#1080;
        &#1085;&#1072;&#1087;&#1088;&#1072;&#1074;&#1083;&#1077;&#1085;&#1080;&#1103;&#1084;
        &#1079;&#1072;&#1090;&#1088;&#1072;&#1090; </font></td>
      <td width="190" height="1" colspan="6">
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1053;&#1077;&#1103;&#1074;&#1082;&#1080;
        &#1087;&#1086; &#1087;&#1088;&#1080;&#1095;&#1080;&#1085;&#1072;&#1084; </font></td>
    </tr>
    <tr>
      <td width="454" height="1" colspan="6">
        <p align="center"><font face="Times New Roman" size="1">&#1082;&#1086;&#1076;
        &#1074;&#1080;&#1076;&#1072; &#1086;&#1087;&#1083;&#1072;&#1090;&#1099;</font></td>
      <td width="24" height="26" rowspan="5">
        <p align="center" style="line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1082;&#1086;&#1076;</font></td>
      <td width="35" height="26" rowspan="5">
        <p align="center" style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1076;&#1085;&#1080;</font></p>
        <p align="center" style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">(&#1095;&#1072;&#1089;&#1099;)</font></td>
      <td width="24" height="26" rowspan="5">
        <p align="center" style="line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1082;&#1086;&#1076;</font></td>
      <td width="35" height="26" rowspan="5">
        <p align="center" style="line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1076;&#1085;&#1080;</font></p>
        <p align="center" style="line-height: 100%; margin: 0"><font face="Times New Roman" size="1">(&#1095;&#1072;&#1089;&#1099;)</font></td>
      <td width="24" height="26" rowspan="5">
        <p align="center" style="line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1082;&#1086;&#1076;</font></td>
      <td width="35" height="26" rowspan="5">
        <p align="center" style="line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1076;&#1085;&#1080;</font></p>
        <p align="center" style="line-height: 100%; margin: 0"><font face="Times New Roman" size="1">&#1095;&#1072;&#1089;&#1099;</font></td>
    </tr>
    <tr>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">1</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">2</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">3</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">4</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">5</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">6</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">7</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">8</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">9</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">10</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">11</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">12</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">13</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">14</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">15</font></td>
      <td width="44" height="39" rowspan="2" align="center"><font face="Times New Roman" size="1">&#1061;</font></td>
      <td width="76" height="26" rowspan="2">
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1087;&#1086;&#1083;&#1086;&#1074;&#1080;&#1085;&#1091;
        </font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1084;&#1077;&#1089;&#1103;&#1094;&#1072;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">(|,||)</font></td>
      <td width="83" height="26" rowspan="2">
        <p align="center"><font face="Times New Roman" size="1">&#1084;&#1077;&#1089;&#1103;&#1094;</font></td>
      <td width="215" height="1" colspan="3">&nbsp;</td>
      <td width="237" height="1" colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td width="454" height="20" colspan="6">
        <p align="center"><font face="Times New Roman" size="1">&#1082;&#1086;&#1088;&#1088;&#1077;&#1089;&#1087;&#1086;&#1085;&#1076;&#1080;&#1088;&#1091;&#1102;&#1097;&#1080;&#1081;
        &#1089;&#1095;&#1105;&#1090;</font></td>
    </tr>
    <tr>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">16</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">17</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">18</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">19</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">20</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">21</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">22</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">23</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">24</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">25</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">26</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">27</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">28</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">29</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">30</font></td>
      <td width="44" height="18"  rowspan="2" align="center"><font face="Times New Roman" size="1">31</font></td>
      <td colspan="2" width="52"  height="18" align="center"><font face="Times New Roman" size="1">&#1044;&#1085;&#1080;</font></td>
      
      <td width="70" height="2" rowspan="2" >
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1082;&#1086;&#1076;
        &#1074;&#1080;&#1076;&#1072;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1086;&#1087;&#1083;&#1072;&#1090;&#1099;</font></td>
      <td width="118" height="2" rowspan="2" >
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1082;&#1086;&#1088;&#1088;&#1077;&#1089;&#1087;&#1086;&#1085;&#1076;&#1080;&#1088;&#1091;&#1102;&#1097;&#1080;&#1081;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1089;&#1095;&#1105;&#1090;</font></td>
      <td width="49" height="2" rowspan="2" >
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1076;&#1085;&#1080;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">(&#1095;&#1072;&#1089;&#1099;)</font></td>
      <td width="70" height="2" rowspan="2" >
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1082;&#1086;&#1076;
        &#1074;&#1080;&#1076;&#1072;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1086;&#1087;&#1083;&#1072;&#1090;&#1099;</font></td>
      <td width="118" height="2" rowspan="2" >
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1082;&#1086;&#1088;&#1088;&#1077;&#1089;&#1087;&#1086;&#1085;&#1076;&#1080;&#1088;&#1091;&#1102;&#1097;&#1080;&#1081;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1089;&#1095;&#1105;&#1090;</font></td>
      <td width="49" height="2" rowspan="2" >
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">&#1076;&#1085;&#1080;</font></p>
        <p style="word-spacing: 0; line-height: 100%; margin: 0" align="center"><font face="Times New Roman" size="1">(&#1095;&#1072;&#1089;&#1099;)</font></td>
    </tr>
    <tr>
        <td colspan="2" width="52"  height="18" align="center"><font face="Times New Roman" size="1">&#1063;&#1072;&#1089;&#1099;</font></td>
    </tr>
    
    
    <tr>
      <td width="40" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">1</font></td>
      <td width="105" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">2</font></td>
      <td width="54" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">3</font></td>
      <td width="711" height="1" align="center" colspan="16">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">4</font></td>
      <td width="76" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">5</font></td>
      <td width="83" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">6</font></td>
      <td width="66" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">7</font></td>
      <td width="96" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">8</font></td>
      <td width="49" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">9</font></td>
      <td width="79" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">7</font></td>
      <td width="118" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">8</font></td>
      <td width="49" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">9</font></td>
      <td width="24" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">10</font></td>
      <td width="35" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">11</font></td>
      <td width="24" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">12</font></td>
      <td width="35" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">13</font></td>
      <td width="24" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">14</font></td>
      <td width="35" height="1" align="center">
        <p style="word-spacing: 0; line-height: 100%; margin: 0"><font face="Times New Roman" size="1">15</font></td>
    </tr>';



	
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
						   
						   
						   
	
    $pers = 'select * from TABL_W_S_PERSONAL(\''.CheckString($_REQUEST['family']).'\',\''.CheckString($_REQUEST['name']).'\',
                \''.CheckString($_REQUEST['secname']).'\',\''.CheckString($_REQUEST['position']).'\', NULL,'.$deptId.','.$_REQUEST['graph'].',
                '.$_SESSION['iduser'].','.'\''.$deptIdList.'\')';

$personal = pg_query($pers);
$i = 1;
while($p = pg_fetch_array($personal))
{
$BODY .='<tr>
     <td width="40" height="84" align="center" rowspan="4"><span class="reptext">'.$i.'</span></td>
      <td width="105" height="84" align="center" rowspan="4"><span class="reptext">'.$p['family'].' '.mb_substr($p['name'],0,1,'utf-8').'.'.mb_substr($p['secname'],0,1,'utf-8').'.</span></td>
      <td width="54" height="84" align="center" rowspan="4"><span class="reptext">'.$p['tabel_num'].'</span></td>';

      $j = 1;
      $total_time_count = 0;
      $first_half_time = 0;
      $second_half_time = 0;
      $cod_days_string = '';
      $cod_days_f_half = '';
      $cod_days_s_half ='';
      $cod_d_f_h_count = 0;
      $d_f_h_count=0;
      $d_s_h_count=0;
              
      $_VALUES = array();


      $val_query = 'select * from TABL_W_S_CORRECT('.$p['id'].',\''.$_REQUEST['start_date'].'\')';

      $values = pg_query($val_query);
      while($v = pg_fetch_array($values))
      {
         $_VALUES[$j][] = $v['signal'];
         $_VALUES[$j][] = $v['timer'];

         $total_time_count += $v['timer'];
         $cod_days_string .= $v['signal'];
         if($j <= 15)
         {
           $first_half_time +=  $v['timer'];
           //$cod_days_f_half .= $v['SIGNAL'] ;
           
           if ($v['timer']>0){
               $d_f_h_count++;
           }
         }
         else if($j>=16)
         {
           $second_half_time +=  $v['timer'];
           //$cod_days_s_half .= $v['SIGNAL'] ;
           if ($v['timer']>0){
               $d_s_h_count++;
           }
         }

         $j++;
      }
      $d_total_count = $d_f_h_count + $d_s_h_count;

	  //из БД число приходит в виде '2,4', которое после сложения преобразуется в '2.4',
	  //а в экселе (Сервис-Параметры-Международные) стоит разделителем целой и дробной части ',',
	  //то во избежание авто преобразования в дату делаем так:
	 $y = explode('.', $total_time_count);
	 if ($y[1] != '')
	 $total_time_count = $y[0].','.$y[1];
	  
	 $y = explode('.', $first_half_time);
	 if ($y[1] != '')
	 $first_half_time = $y[0].','.$y[1];
	  
	 $y = explode('.', $second_half_time);
	 if ($y[1] != '')
	 $second_half_time = $y[0].','.$y[1];
	//print_r($res);
	
$BODY.='<td width="38" height="17" align="center"><span class="text">'.$_VALUES[1][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[2][0].'&nbsp;</span></td>
      <td width="42" height="17" align="center"><span class="text">'.$_VALUES[3][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[4][0].'&nbsp;</span></td>
      <td width="45" height="17" align="center"><span class="text">'.$_VALUES[5][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[6][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[7][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[8][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[9][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[10][0].'&nbsp;</span></td>
      <td width="49" height="17" align="center"><span class="text">'.$_VALUES[11][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[12][0].'&nbsp;</span></td>
      <td width="38" height="17" align="center"><span class="text">'.$_VALUES[13][0].'&nbsp;</span></td>
      <td width="37" height="17" align="center"><span class="text">'.$_VALUES[14][0].'&nbsp;</span></td>
      <td width="37" height="17" align="center"><span class="text">'.$_VALUES[15][0].'&nbsp;</span></td>
      <td width="44" height="17" align="center"><span class="text">Х</td>
      <td width="76" height="17" align="center"><span class="text">'.$d_f_h_count.'</span></td>
      <td width="83" height="38" align="center" rowspan="2"><span class="text">'.$d_total_count.'</span></td>
      <td width="66" height="19" align="center">&nbsp;</td>
      <td width="96" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="79" height="19" align="center">&nbsp;</td>
      <td width="118" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="16" height="19" align="center">&nbsp;</td>
      <td width="32" height="19" align="center">&nbsp;</td>
      <td width="24" height="19" align="center">&nbsp;</td>
      <td width="35" height="19" align="center">&nbsp;</td>
      <td width="43" height="19" align="center">&nbsp;</td>
      <td width="37" height="19" align="center">&nbsp;</td>
    </tr>

    <tr>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[1][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[2][1].'&nbsp;</span></td>
      <td width="42" height="19" align="center"><span class="text">'.$_VALUES[3][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[4][1].'&nbsp;</span></td>
      <td width="45" height="19" align="center"><span class="text">'.$_VALUES[5][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[6][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[7][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[8][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[9][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[10][1].'&nbsp;</span></td>
      <td width="49" height="19" align="center"><span class="text">'.$_VALUES[11][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[12][1].'&nbsp;</span></td>
      <td width="38" height="19" align="center"><span class="text">'.$_VALUES[13][1].'&nbsp;</span></td>
      <td width="37" height="19" align="center"><span class="text">'.$_VALUES[14][1].'&nbsp;</span></td>
      <td width="37" height="19" align="center"><span class="text">'.$_VALUES[15][1].'&nbsp;</span></td>
      <td width="44" height="19" align="center"><span class="text">Х</span></td>
      <td width="76" height="19" align="center"><span class="text">'.$first_half_time.'</span></td>
      <td width="66" height="19" align="center">&nbsp;</td>
      <td width="96" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="79" height="19" align="center">&nbsp;</td>
      <td width="118" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="16" height="19" align="center">&nbsp;</td>
      <td width="32" height="19" align="center">&nbsp;</td>
      <td width="24" height="19" align="center">&nbsp;</td>
      <td width="35" height="19" align="center">&nbsp;</td>
      <td width="43" height="19" align="center">&nbsp;</td>
      <td width="37" height="19" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[16][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[17][0].'&nbsp;</span></td>
      <td width="42" height="20" align="center"><span class="text">'.$_VALUES[18][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[19][0].'&nbsp;</span></td>
      <td width="45" height="20" align="center"><span class="text">'.$_VALUES[20][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[21][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[22][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[23][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[24][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[25][0].'&nbsp;</span></td>
      <td width="49" height="20" align="center"><span class="text">'.$_VALUES[26][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[27][0].'&nbsp;</span></td>
      <td width="38" height="20" align="center"><span class="text">'.$_VALUES[28][0].'&nbsp;</span></td>
      <td width="37" height="20" align="center"><span class="text">'.$_VALUES[29][0].'&nbsp;</span></td>
      <td width="37" height="20" align="center"><span class="text">'.$_VALUES[30][0].'&nbsp;</span></td>
      <td width="44" height="20" align="center"><span class="text">'.$_VALUES[31][0].'&nbsp;</span></td>
      <td width="76" height="20" align="center"><span class="text">'.$d_s_h_count.'</span></td>
      <td width="83" height="42" align="center" rowspan="2"><span class="text">'.$total_time_count.'</span></td>
      <td width="66" height="19" align="center">&nbsp;</td>
      <td width="96" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="79" height="19" align="center">&nbsp;</td>
      <td width="118" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="16" height="19" align="center">&nbsp;</td>
      <td width="32" height="19" align="center">&nbsp;</td>
      <td width="24" height="19" align="center">&nbsp;</td>
      <td width="35" height="19" align="center">&nbsp;</td>
      <td width="43" height="19" align="center">&nbsp;</td>
      <td width="37" height="19" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[16][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[17][1].'&nbsp;</span></td>
      <td width="42" height="18" align="center"><span class="text">'.$_VALUES[18][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[19][1].'&nbsp;</span></td>
      <td width="45" height="18" align="center"><span class="text">'.$_VALUES[20][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[21][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[22][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[23][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[24][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[25][1].'&nbsp;</span></td>
      <td width="49" height="18" align="center"><span class="text">'.$_VALUES[26][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[27][1].'&nbsp;</span></td>
      <td width="38" height="18" align="center"><span class="text">'.$_VALUES[28][1].'&nbsp;</span></td>
      <td width="37" height="18" align="center"><span class="text">'.$_VALUES[29][1].'&nbsp;</span></td>
      <td width="37" height="18" align="center"><span class="text">'.$_VALUES[30][1].'&nbsp;</span></td>
      <td width="44" height="18" align="center"><span class="text">'.$_VALUES[30][1].'&nbsp;</span></td>
      <td width="76" height="18" align="center"><span class="text">'.$second_half_time.'</span></td>
      <td width="66" height="19" align="center">&nbsp;</td>
      <td width="96" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="79" height="19" align="center">&nbsp;</td>
      <td width="118" height="19" align="center">&nbsp;</td>
      <td width="49" height="19" align="center">&nbsp;</td>
      <td width="16" height="19" align="center">&nbsp;</td>
      <td width="32" height="19" align="center">&nbsp;</td>
      <td width="24" height="19" align="center">&nbsp;</td>
      <td width="35" height="19" align="center">&nbsp;</td>
      <td width="43" height="19" align="center">&nbsp;</td>
      <td width="37" height="19" align="center">&nbsp;</td>
    </tr>';
   $i++;
}
$BODY.='</table>';


if(isset($_REQUEST['excelflg']))
{
  
$fname = 'report.xls';
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$fname");
header("Expires: 0");
//header("Content-Transfer-Encoding: binary"); 
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");
//echo '<meta http-equiv=Content-Type content="text/html; charset=windows-1251">';
}

if(!isset($_REQUEST['excelflg']))
   $BODY .= PrintFooter();


list($msec,$sec)=explode(chr(32),microtime());
$end=$sec+$msec;

echo $BODY;
?>