<?php
//REPLACED 04.07.07
function PrintHead($title,$pagetitle,$INCLUDES)
{
    $result=' ';
	$result.= '<!DOCTYPE HTML PUBLIC "	//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';		
    $result.= '<html>';
    $result.='<head>';
    $result.='<title>';
    $result.= $title;
    $result.='</title>';
    $result.='<meta http-equiv="Content-Language" content="ru">';
    $result.='<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">';

    //обход массива с включаемыми файлами
    if(sizeof($INCLUDES)>0)
      for($i = 0; $i < sizeof($INCLUDES); $i++)
         $result .= $INCLUDES[$i];

    /*$result.='<link rel="stylesheet" type="text/css" href="../include/menu.css">';
    $result.='<link rel="stylesheet" type="text/css" href="include/style.css">';
    $result.='<script language="JavaScript" src="include/_request_functions.js"></script>';
    $result.='<script language="JavaScript" src="include/controllers.js"></script>';
    */
    $result.='</head>';
    $result.='<body style="margin-top:0;margin-left:0;margin-right:0;margin-bottom:0;">';
    $result.='<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">';
    $result.='<tr><td valign="top">';
    //заголовок страници
    $result.='<table width="100%" cellpadding="0" cellspacing="0"><tr  bgcolor="silver">';
    $result.='<td width="35%"><b>&nbsp;Сегодня:  '.date("d-m-Y").'</b></td>';
    $result.='<td align="left">';
    $result.='<b>'.$pagetitle.'</b>';
    $result.='</td>';
    $result.='</tr></table>';

return $result;
}
function PrintFooter()
{
   $result='';
   $result.='</td></tr>';
   $result.='</table>';
   $result.='</body></html>';
   return $result;
 }
function CheckString($str)
{
  //проверяет на наличие недопустимых символов в строке
  $str=strip_tags($str);
  $str=htmlspecialchars($str,ENT_QUOTES);
  $str=str_replace("%"," ",$str);
 // $str=trim($str);
  return $str;

}
//$action - javaScript обработчик
function InsertSelect($query,$field_match,$val_match,$id,$name,$style,$first_item,$e_d,$action)
{
   $res = '';
   $sel = '';
   $disabled = '';
   if($e_d==1)$disabled='disabled=1';else $disabled='';
   $res = '<select id="'.$name.'" name="'.$name.'" '.$style.' '.$disabled.' '.$action.'><option value="0">'.$first_item.'</option>';
   $result = pg_query($query);

   while($r =pg_fetch_array($result))
   {
     if($r[$field_match] == $val_match)$sel = 'selected'; else $sel = '';
     $res .= '<option value="'.$r['id'].'" '.$sel.' title="'.$r['name'].'">'.$r['name'].'</option>';
   }
   $res .= '</select>';
   return $res;
}
function InsertMonthYearSelect($f_month,$f_year,$y_start,$y_end,$sel_name,$sel_id,$sel_style)
{
   //echo $y_start+1;
   //echo $y_end+1;
    $res = '';
    $month_select = '';
    $year_select = '';
    $MONTH = array("Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
    $NUM_MONTH = array("01","02","03","04","05","06","07","08","09","10","11","12");
    if($f_month == 1)
    {
        $selected = '';
        $month_select = '<select id="'.$sel_id.'_month" name="'.$sel_name.'_month" '.$sel_style.'>';
        for($i = 0;$i < sizeof($MONTH);$i++)
        {
          if(date("m") == $NUM_MONTH[$i])$selected = 'selected'; else $selected = '';
          $month_select .= '<option value="'.$NUM_MONTH[$i].'" '.$selected.'>'.$MONTH[$i].'</option>';
        }

        $month_select .= '</select>';
      $res .= ''.$month_select;
    }
    if($f_year == 1)
    {
        $selected = '';
        $year_select = '<select id="'.$sel_id.'_year" name="'.$sel_name.'_year" '.$sel_style.'>';
        for($i = $y_start;$i < $y_end;$i++)
        {
          if(date("Y") == $i)$selected = 'selected'; else $selected = '';
          $year_select .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
        }

        $year_select .= '</select>';
       $res .= 'Год:'.$year_select;
    }
  return $res;
}
function CheckAccessToModul($id,$modullist)
  {
     $flag=0;
     for($i=0;$i<sizeof($modullist);$i++)
     {
        if($id==$modullist[$i]){$flag=1;break;}
     }
     if($flag==0)return false;else return true;
  }

?>