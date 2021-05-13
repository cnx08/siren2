<?php
include('../include/input.php');
include('../include/common.php');
require_once('../classes/base/pages.h');

$page = new CEmptyPage('Мастер импорта');
$page -> addCSSInclude('css/styles.css');
$page->start();
if(CheckAccessToModul(45,$_SESSION['modulaccess'])==false)
 {
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
 }
$BODY = '';
$BODY .= '<table class="someTable" border="0" cellpadding="0" cellspacing="0" align="center">';
$BODY .= '<tr>';
$BODY .= '<th style="border-bottom:1px solid #b7cee4">Мастер импорта 1.1</th>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td>С помощью мастера вы можете вставить данные из текстовых файлов в базу данных</td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td>Импорт данных из файла происходит в несколько шагов</td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td>ВНИМАНИЕ!!! при импорте все старые данные которые содержаться в таблице - Удаляются </td>';
$BODY .= '</tr>';

$BODY .= '<tr>';
$BODY .= '<td style="border-bottom:1px solid #b7cee4">Для продолжения нажмите &quotдалее&quot или &quotотмена&quot для выхода </td>';
$BODY .= '</tr>';
$BODY .= '<tr>';
$BODY .= '<td align="right"> [ <a href="javascript:onclick=window.close()" class="bigBtLink">отмена</a> ] &nbsp; [ <a href="master.php?action=start" class="bigBtLink">далее</a> ]</td>';
$BODY .= '</tr>';

$BODY .= '</table>';


echo $BODY;



$page->end();

?>