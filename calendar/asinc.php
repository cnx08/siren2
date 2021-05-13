<?php
include('../include/input.php');
include('../include/common.php');


if($_REQUEST['act'] == 'ToBase'){
    
    if(sizeof($_REQUEST['ids'])!=0)
    {
        $id5 = explode("_",$_REQUEST['ids'][5]);
        $date_start = '01.01.'.$id5[2];
        $date_end = '31.12.'.$id5[2];
        $qq='delete from TABL_HOLIDAY where DATE >=\''. $date_start.'\' and DATE <=\''. $date_end.'\'';
        pg_query($qq) or die("Ошибка при удалении празников");
        for($i=0;$i<sizeof($_REQUEST['ids']);$i++)
        {
            $id = explode("_",$_REQUEST['ids'][$i]);
            $day = strlen($id[0])==1 ? '0'.$id[0]:$id[0];
            $month = strlen($id[1])==1 ? '0'.$id[1]:$id[1];
            $date = $day.'.'.$month.'.'.$id[2];

            $q='insert into TABL_HOLIDAY (DATE,ALLWAYS) VALUES( \''. $date.'\',\'0\')';
            pg_query($q) or die("Ошибка при добавлении праздников");
        }
    }

    //echo "done";
}


if($_REQUEST['act'] == 'GetHolidays')
{
    $date_start = '01.01.'.$_REQUEST['year'];
    $date_end = '31.12.'.$_REQUEST['year'];
    $q='select to_char(TABL_HOLIDAY.date,\'DD.MM.YYYY\') as date from TABL_HOLIDAY where TABL_HOLIDAY.date>=\''.$date_start.'\' and TABL_HOLIDAY.date<=\''.$date_end.'\'';
    $dates='0';
    $res=pg_query($q);
    while($r = pg_fetch_array($res))
    { 
        $dates.=','.$r['date'];
    }
    echo $dates;
}
