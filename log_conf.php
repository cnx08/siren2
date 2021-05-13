<?php

include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
//проверяем на доступность
echo PrintHead('СКУД - Настройка периодов хранения информации','Настройка периодов хранения информации');
if(CheckAccessToModul(49,$_SESSION['modulaccess'])==false)
{
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
require_once("include/menu.php");
$BODY.='<script>
            function save_it(f){
                
                var i = 1;
                while (i<8)
                {
                    if(!$.isNumeric($("#"+i).val()) || $("#"+i).val()<1)
                    {
                        alert("Ошибка ввода. Проверьте правильность введённых данных.");
                        return;
                    }
                    i++;
                }
                f.submit();
            };

        </script>';

if(isset($_REQUEST['action']))
{
    if($_REQUEST['action']=='save')
    {
        $time_dbl_pass = is_numeric($_POST["time_dbl_pass"]) == false ? 15 : $_POST["time_dbl_pass"];
        
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
        $q.='select base_w_u_log_conf('.$_POST["1"].','.$_POST["2"].','.$_POST["3"].','.$_POST["4"].','.$_POST["5"].','.$_POST["6"].','.$_POST["7"].')';
        pg_query($q);
    }
}


$BODY.='<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
$BODY.='<form name="log_conf" action="log_conf.php?action=save" method="POST">';
$BODY.='<table border=0 cellpadding="1" cellspacing="1" width="70%" class="dtab" align="center">';

$BODY.='<span>&nbsp;&nbsp;&nbsp;&nbsp;Хранить информацию следующее количество месяцев:</span>';

$q='select * from log_conf order by id;';

$result=pg_query($q);

$idstring='';
while($r=pg_fetch_array($result))
{
    $BODY.='<tr><td width="70px">&nbsp;&nbsp;';

    $BODY.='<input id = "'.$r['id'].'" type="text" name="'.$r['id'].'" required value="'.$r['value'].'" class="input" size="3" maxlength="3">';

    $BODY.='</td>';
    $BODY.='<td>'.$r['descr'].'</td></tr>';
}
    
    
    $BODY.='<tr class="tablehead">';
    $BODY.='<td align="right" colspan="2">
           <input type="button" name="save" onclick = \'save_it(document.log_conf)\' class="sbutton" value="сохранить">
           </td>';
    $BODY.='</tr>';
    $BODY.='</table>';
    $BODY.='</form>';


echo $BODY;


