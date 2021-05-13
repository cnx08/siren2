<?php
include("include/input.php");
require("include/common.php");
require("include/head.php");
require_once('include/hua.php');
//проверяем на доступность
echo PrintHead('СКУД - Настройки юнитов','Настройки юнитов');
if(CheckAccessToModul(48,$_SESSION['modulaccess'])==false)
{
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
require_once("include/menu.php");
function ip_to_str($ip){
    
    $ip= str_replace('ip: ', '', $ip);
    $ip= str_replace('; mask:', ' /', $ip);
    $ip= str_replace('; dns:', ' /', $ip);
    $ip= str_replace('; mac:', ' /', $ip);
    
    return $ip;
}
$BODY .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="gscripts/edit_units.js"></script>';

$BODY.='<div id="mask" style="position: absolute;
                    background-color:white;
                    height:93%;
                    width: 100%;
                    opacity:0.7;
                    top:50px;
                    z-index:-1">
        </div>';


$q=' select get_units_info();';//send command to units с просьбой дать фидбэк    
 pg_query($q);
 sleep(1);

 $BODY.='<div class="listconteiner" style="position:absolute;top:8%;left:5%;width:90%;height:85%;border:1px solid gray;overflow:hidden;" >';

$BODY.='<div class="listconteiner" style="height:95%;">
        <table id = "u_list" border=0 cellpadding="1" cellspacing="1"  width="100%">
        <tr class="tablehead">
            <td align="center" width="2%"># Юнита</td>
            <td align="center" width="1%">Название</td>
            <td align="center" width="2%">ID</td>
            <td align="center" width="6%">Описание</td>
            <td align="center" width="25%">IP/mask/dns/mac</td>
            <td align="center" width="4%"></td>
        </tr>';
 $col1="silver";
$col2="#f5f5dc";
$bgcolor='';
$flag=0;
$q='select * from pr_units_list()';
$result=pg_query($q);
while($r=pg_fetch_array($result))
{
    $INFO ='';
    if($r['unit']==0){$bgcolor='#ff6c6d';$flag=1;}else{$bgcolor='#71e08a';$flag=0;}
    $ips = explode('^_^',$r['ip']);
    $cnt = count($ips)-1;
    $rowsp = $cnt > 0 ?'rowspan = "'.$cnt.'"' : '';
    if($r['status']==0)$bgcolor='silver';
   $BODY.='<tr class="tr_'.$r['unit_id'].'" bgcolor='.$bgcolor.'>
        <td align="center" class="tabcontent" '.$rowsp.'><input id = "unit_'.$r['unit_id'].'" type = "text" style="background-color:'.$bgcolor.'" size = "3" maxlength = "4" value = "'.$r['unit'].'"></td>
        <td align="center" class="tabcontent" '.$rowsp.'><input id = "name_'.$r['unit_id'].'" type = "text" style="background-color:'.$bgcolor.'" size = "35" maxlength = "34" value = "'.$r['u_name'].'"></td>
        <td align="center" class="tabcontent" '.$rowsp.'>'.$r['unit_id'].'</td>
        <td align="center" class="tabcontent" '.$rowsp.'>'.$r['descr'].'</td>
        <td  style="padding:5px;">'.ip_to_str($ips[0]).'</td>
        
        <td valign="top" class="tabcontent" align="center" '.$rowsp.'>';
                if ($r['unit'] != 0)$BODY.='<img src="buttons/cog.png" class="icons" onclick=\'showUnitConf("'.$r['unit'].'")\' alt="Параметры юнита" title="Параметры юнита и управление">';
                $BODY.='<img src="buttons/save.gif"  class="icons" alt="Сохранить" title="Сохранить" onclick=\'save("'.$r['unit_id'].'")\'/>
                <img src="buttons/remove.gif" onclick=\'del_unit("'.$r['unit_id'].'","'.$r['unit'].'")\' class="icons" alt="Удалить" title="Удалить"/>
       </td>
   </tr>';
    if($cnt > 0){           
        for($i=1;$i<$cnt;$i++){
            $BODY.='<tr class="tr_'.$r['unit_id'].'" bgcolor='.$bgcolor.'><td   style="padding:5px;">'.ip_to_str($ips[$i]).'</td></tr>';
        }
    }
 }

 $BODY.='</table>
</div>
<div class="listhead">
<img id = "add_button" align="right" valign="bottom" src="buttons/icons.gif" style="margin:3px;cursor:pointer" alt="Добавить юнит" title="Добавить юнит" onclick=\'add_unit()\' />
</div>
</div>';
 
 /////////////////////настройки юнита

   $BODY.='<div id="unit_conf" style="display:none;
                                        position:absolute;
                                        top:150px;
                                        left:30%;
                                        width: 530px;
                                        z-index:50;
                                        border: 1px solid gray;">
                <table border="0" cellpadding="1" cellspacing="1" class="dtab" width="100%" align="center">
                    <tr class="tablehead" >
                        <td id="thead" align="left" colspan="2"></td>
                        
                        <td align="right"><img src="buttons/crossline.gif" style="text-align: right;" class="icons" onclick=\'closeUnitConf();\' /></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span style = "margin-left:15px"><input id = "status" type="checkbox" > Активен</span></td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "off_line_start" name="off_line_start" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                            </select>
                        </td>
                        <td>Запрет (0) или разрешение (1) старта юнита без сервера</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "timer_corr" name="timer_corr" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                            </select>
                        </td>
                        <td>Разрешить корректировать дату и время</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "sunday_reboot" name="sunday_reboot" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                            </select>
                        </td>
                        <td>Разрешить профилактические перезагрузки по выходным</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "hard_err_reboot" name="hard_err_reboot" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                            </select>
                        </td>
                        <td>Разрешение перезагрузки при зависании</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <input id = "time_dbl_pass" type="text" name="time_dbl_pass"  value="" class="input" size="5">
                        </td>
                        <td>Таймаут двойных проходов (мин)</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "out_time_cnt" name="out_time_cnt" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                            </select>
                        </td>
                        <td>Признак запрета выхода в неурочное время</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "time_cnt" name="time_cnt" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                            </select>
                        </td>
                        <td>Признак проверки допустимого времени входа</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "prop_cnt" name="prop_cnt" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                            </select>
                        </td>
                        <td>Признак контроля пропусков по БД</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "log_level_srt" name="log_level_srt" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                                <option value="2" >2</option>
                            </select>
                        </td>
                        <td>Уровень подробности логфайла srt</td>
                    </tr>
                    <tr>
                        <td width="80px">&nbsp;&nbsp;
                            <select id = "log_level_dm" name="log_level_dm" style="width:60px">
                                <option value="0" >0</option>
                                <option value="1" >1</option>
                                <option value="2" >2</option>
                            </select>
                        </td>
                        <td>Уровень подробности логфайла dm</td>
                    </tr>
                    <tr>
                        <td colspan="2"><br>
                            <button id = "reboot_OS" class="sbutton" onclick="send_command(event,1)">Перезагрузить ОС юнита</button>
                            <button id = "reboot_prog" class="sbutton" onclick="send_command(event,2)">Перезагрузить ПО юнита</button>
                            <button id = "power_off" class="sbutton" onclick="send_command(event,3)">Выключить юнит</button>
                        </td>
                    </tr>
                    
                    <tr class="tablehead">
                        <td align="right" colspan="3">
                            <input type="button" value="сохранить" class="sbutton" onclick=\'save_conf()\' />
                            <input type="button" value="отмена" class="sbutton" onclick=\'closeUnitConf();\' />
                            <input id="cur_unit" type="hidden" value="-1" size="10">
                        </td>
                    </tr>
                </table>

            </div>';
 
 echo $BODY;

