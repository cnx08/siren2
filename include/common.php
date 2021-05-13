<?php

//сравнивает, больше ли date1 чем date_2
function compareDate($date_1,$date_2,$spliter)
{
	  $d_1 = explode($spliter,$date_1);
	  $d_2 = explode($spliter,$date_2);


	 $more_count = 0;
     if((int)$d_1[2] > (int)$d_2[2])
	   	return true;
	 if((int)$d_1[1] > (int)$d_2[1] && (int)$d_1[2] >= (int)$d_2[2])
	    return true;
	 if((int)$d_1[0] > (int)$d_2[0] && (int)$d_1[2] >= (int)$d_2[2] && (int)$d_1[1] >= (int)$d_2[1])
	    return true;

  return false;

}
function CheckString($str)
{
  //проверяет на наличие недопустимых символов в строке
  $str=strip_tags($str);
  $str=htmlspecialchars($str,ENT_QUOTES);
  $str=str_replace("%"," ",$str);
  $str=trim($str);
  return $str;

}
function CheckString2($str)
{
    $str=strip_tags($str);
    
    $str=str_replace("%"," ",$str);
    $str=str_replace("\\"," ",$str);
    $str=str_replace("/"," ",$str);
    $str=str_replace('"'," ",$str);
    //$str=str_replace('.'," ",$str);
    $str=str_replace('»'," ",$str);
    $str=str_replace('«'," ",$str);
    $str=str_replace('&quot;'," ",$str);
    $str=str_replace('>'," ",$str);
    $str=str_replace('<'," ",$str);
  
    $str=trim($str);
    return $str;

}
function IdValidate($id)
{
    if(is_numeric($id)==0)return false;
    if($id<=0)return false;

    return true;
}
function ParsePxCode($str,$id)
{

/*
	27.01.2011 - Зорков Ю.А. добавил title при выводе картинок
	
	нада переписать таким образом, чтобы отображались только такие картинки, для которых утановлен признак в 1 - пока не решено!
*/
  $check=array();
  $img_src_on=array('block.gif','guest.gif','admin.gif','zasecki1.gif','car.gif');
  $img_src_off=array('block_off.gif','guest_off.gif','admin_off.gif','zasecki1_off.gif','car_off.gif');
  $alt = array('блокированный','гостевой','администратор','двойные&nbsp;засечки','автомобильный');
  $checked='';
  $result='';
  //если все нули
  $_count=substr_count($str,"0");
  if($_count==8)
     $check1=$check2=$check3=$check4=$check5='';

  $i=0;
  for($i=0;$i<=strlen($str)-3;$i++)
    $check[$i]=substr($str,$i,1);

 $j=0;
 for($j=0;$j<sizeof($check)-1;$j++)
 {
   $img ='';
   if($check[$j]==1)
   {
   $img = $img_src_on[$j];
	// чтоб отображались только установленные в 1!!   
   $result.='<img title='.$alt[$j].' src="buttons/'.$img.'" alt='.$alt[$j].' style="margin-right:10px;">';
   }
  }
  return $result;
}
//возвращает значение бита пропуска, указанного в параметре
function GetCodeValue($str,$bitn)
{
  $b=substr($str,$bitn,1);
  return $b;
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
function ShowErrorWindow($text,$url)
{
   $res='';
   $res.='<script>
   function CloseErrorWnd(id)
   {
     var w = document.getElementById(id);
     w.style.display = "none";
   ';
   if($url!='')$res.=' document.location.href="'.$url.'";';
   $res.='}</script>';

   $style  ='display:block;position:absolute;top:40%;left:30%;width:30%;';
   $style .='border:1px solid red;background:#F5F5F5;z-index:60;';

   $butstyle = 'font-size:11px;font-family:Verdana;border:1px solid silver;';

   $res.='<div id="ewnd" style="'.$style.'">';
   $res.='<table border="0" cellpading="2" cellspacing="0" width="100%" height="100%">';
   $res.='<tr bgcolor="RED"><td align="left"><span style="font-family:verdana;font-size:11px;font-weight:bold;color:white">Ошибка</span></td><tr>';
   $res.='<tr><td align="left">';
   $res.='<span style="font-family:verdana;font-size:11px;font-weight:bold;color:red">';
   $res.=$text;
   $res.='</span>';
   $res.='</td></tr>';
   $res.='<tr><td align="center"><input type="button" value="закрыть" style="'.$butstyle.'" onclick=CloseErrorWnd("ewnd")></td></tr>';
   $res.='</table>';
   $res.='</div>';

  return $res;
}
function ShowConfirmWindow($head,$text,$url)
{
   $res='';
   $res.='<script>
   function CloseErrorWnd(id)
   {
     var w = document.getElementById(id);
     w.style.display = "none";
   ';
   if($url!='')$res.=' document.location.href="'.$url.'";';
   $res.='}</script>';

   $style  ='display:block;position:absolute;top:18%;left:30%;width:30%;';
   $style .='border:1px solid green;background:#F5F5F5;z-index:60;';
   $butstyle = 'font-size:11px;font-family:Verdana;border:1px solid silver;';
   $res.='<div id="ewnd" style="'.$style.'">';
   $res.='<table border="0" cellpading="2" cellspacing="0" width="100%" height="100%">';
   $res.='<tr bgcolor="green"><td align="left"><span style="font-family:verdana;font-size:11px;font-weight:bold;color:white">'.$head.'</span></td><tr>';
   $res.='<tr><td align="left">';
   $res.='<span style="font-family:verdana;font-size:11px;font-weight:bold;color:black">';
   $res.=$text;
   $res.='</span>';
   $res.='</td></tr>';
   $res.='<tr><td align="center"><input type="button" value="закрыть" style="'.$butstyle.'" onclick=CloseErrorWnd("ewnd")></td></tr>';
   $res.='</table>';
   $res.='</div>';

  return $res;
}
//проверяет соотвествет ли дата формату dd.mm.YYYY;
function isDate($_date,$spliter)
{
   if(substr_count($_date,$spliter)!=2)return false;
   $_DATE = explode($spliter,$_date);
   $d = $_DATE[0];
   $m = $_DATE[1];
   $y = $_DATE[2];
   if(checkdate($m,$d,$y)==true)return true;
   else return false;
}
function InsertSelect($query,$field_match,$val_match,$id,$name,$style,$first_item,$e_d)
{
   $res = '';
   $sel = '';
   $disabled = '';
   if($e_d==1)$disabled='disabled=1';else $disabled='';
   $res = '<select id=\''.$id.'\' name=\''.$name.'\' '.$style.' '.$disabled.'><option value="0">'.$first_item.'</option>';
   $result = pg_query($query);

   while($r = pg_fetch_array($result))
   {
     if($r[$field_match] == $val_match)$sel = 'selected'; else $sel = '';
     $res .= '<option value="'.$r['id'].'" '.$sel.'>'.$r['name'].'</option>';
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
       $res .= '&nbsp;Год:'.$year_select;
    }
  return $res;
}

function PrintHeadI($title,$pagetitle,$INCLUDES)
{
    $result=' ';
	$result.= '<!DOCTYPE html>';	
    $result.= '<html>';
    $result.='<head>';
    $result.='<title>';
    $result.= $title;
    $result.='</title>';
    $result.='<meta http-equiv="Content-Language" content="ru">';
    $result.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

    //обход массива с включаемыми файлами
    if(sizeof($INCLUDES)>0)
      for($i = 0; $i < sizeof($INCLUDES); $i++)
         $result .= $INCLUDES[$i];


    $result.='</head>';
    $result.='<body style="margin-top:0;margin-left:0;margin-right:0;margin-bottom:0;">';
    $result.='<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">';
    $result.='<tr><td valign="top">';
    //заголовок страници
    $result.='<table width="100%" cellpadding="0" cellspacing="0"><tr  bgcolor="silver">';
    $result.='<td width="35%"><p><b>&nbsp;Сегодня:  '.date("d-m-Y").'</b></p></td>';
    $result.='<td align="left">';
    $result.='<p><b>'.$pagetitle.'</b></p>';
    $result.='</td>';
    $result.='</tr></table>';

return $result;
}
function PrintFooterI()
{
   $result='';
   $result.='</td></tr>';
   $result.='</table>';
   $result.='</body></html>';
   return $result;
 }

?>