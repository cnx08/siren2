<?php
include("include/input.php");
require("include/head.php");
require("include/common.php");
require_once('include/hua.php');
session_write_close();
if(!isset($_REQUEST['action']) || $_REQUEST['action']=='' || is_numeric($_REQUEST['action'])<0 || $_REQUEST['action']<=0)
{
  echo '<script>window.onload=function(){window.close();}</script>';
  exit();
}
echo PrintHead('Выполнение DTS пакета','');

$dts_num = $_REQUEST['action'];
switch ($dts_num)
{
   
   case 4;
        if(CheckAccessToModul(33,$_SESSION['modulaccess'])==false)
        {
            echo '<center><span class="text">Выполнение не возможно.<br> Нет прав доступа</span><br>';
            echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
            exit();
        }
        $FILTER .= '<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
        $FILTER .= '<script type="text/javascript" src="../gscripts/jquery/plugins/jquery.pickmeup_1.js"></script>';
        $FILTER .= '<script type="text/javascript">
            $(function () {
                $("#st_date").pickmeup({
                    change : function (val) {
                        $("#st_date").val(val).pickmeup("hide");
                    }
                 });
                 $("#en_date").pickmeup({
                    change : function (val) {
                        $("#en_date").val(val).pickmeup("hide")
                    }
                });

               
            });
            
        </script>';
            $FILTER.=  '<form id="evload" name="evload" method="POST">
                            <table>
                                <tr>
                                    <td colspan="2" align="center" bgcolor="silver"><span class="text">Загрузка событий за период</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><span class="text">
                                    c:&nbsp;<input type="text" id="st_date" name="st_date" class="tabinput" size="10" value="" readonly>
                                    
                                    &nbsp;по:&nbsp;<input type="text" id="en_date" name="en_date" class="tabinput" size="10" readonly>
                                   
                                    </span></td>
                                </tr>
                                <tr>
                                    <td align="center" ><input type="button" class="sbutton" value="Загрузить" onclick=\'EvLoad2(document.evload)\' /></td>
                                </tr>
                            </table>
                    </form>';
            echo $FILTER;
        break;
        
   case 7;
        if(CheckAccessToModul(36,$_SESSION['modulaccess'])==false)
        {
         echo '<center><span class="text">Выполнение не возможно.<br> Нет прав доступа</span><br>';
         echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
         exit();
        }
        $name = date("N");
	$str = "pg_dump -U postgres -h localhost -Fc askd > /var/www/html/backups/".$name.".backup";
        echo shell_exec($str);
   break;
  
   case 9;
	if(CheckAccessToModul(46,$_SESSION['modulaccess'])==false)
        {
         echo '<center><span class="text">Выполнение не возможно.<br> Нет прав доступа</span><br>';
         echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
         exit();
        }
	echo '<center><span class="text">Выполняется перезагрузка сервера СКУД.</span><br>';
	include('Net/SSH2.php');

	$ssh = new Net_SSH2('localhost');
	$ssh->login('odroid', 'odroid');

	$ssh->read('[prompt]');
	$ssh->write("sudo shutdown -r now\n");
	$ssh->read('Password:');
	$ssh->write("odroid\n");
	echo $ssh->read('[prompt]');

           echo '<center><span class="text">Выполняется перезагрузка сервера СКУД.</span><br>';
           echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';

   break;
   case 10;
        if(CheckAccessToModul(33,$_SESSION['modulaccess'])==false)
        {
            echo '<center><span class="text">Выполнение не возможно.<br> Нет прав доступа</span><br>';
            echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
            exit();
        }
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
        $q .= 'select pr_init_event_load(0);';
       
        if(pg_query($q))   
        {
            echo '<center><span class="text">Загрузка событий инициирована</span><br>';
        }
        else
        {
            echo '<center>Во время выполнения произошла ошибка. См логи.</center>';
        }
        echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
   break;
   case 11;
        if(CheckAccessToModul(28,$_SESSION['modulaccess'])==false)
        {
            echo '<center><span class="text">Выполнение не возможно.<br> Нет прав доступа</span><br>';
            echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
            exit();
        }
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
        $q .= 'select pr_init_upload_to_unit();';
       
        if(pg_query($q))   
        {
            echo '<center><span class="text">Выгрузка информации инициирована</span><br>';
        }
        else
        {
            echo '<center>Во время выполнения произошла ошибка. См логи.</center>';
        }
        echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
   break;
   default:
   echo '<script>window.onload=function(){window.close();}</script>';
   exit();
   break;
}
echo PrintFooter();
?>