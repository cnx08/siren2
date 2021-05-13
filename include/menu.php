<?php

IF(!isset($ROOTPATHSTR))
 {
	$ROOTPATHSTR = '';
}

$menu_body = '<div id="main_menu" style="z-index:10000; background: none repeat scroll 0% 0% #444444;">
		<ul id="nav">
        <li><a href="#">Персонал</a>
			<ul>
				<li title="Отобразить список отделов"><a href="'.$ROOTPATHSTR.'departments.php">Отделы</a></li>
				<li title="Список сотрудников"><a href="'.$ROOTPATHSTR.'personal.php?action=new">Сотрудники</a></li>
				<li title="Операции с группами объектов"><a href="'.$ROOTPATHSTR.'groupop.php?action=newlist">Групповые операции </a></li>
				<li title="Отобразить список пропусков"><a href="'.$ROOTPATHSTR.'pxcodes.php?action=show">Пропуска</a></li>
                                <li title="Информация о сотруднике"><a href="'.$ROOTPATHSTR.'persinfo/persinfo.html">Карточка сотрудника</a></li>
			</ul>
        </li>
        <li><a href="#">Справочники</a>
			<ul>
                                <li><a href="'.$ROOTPATHSTR.'directories.php?action=show&amp;list=turn">Турникеты</a></li>
				<li title="Справочник турникетов"><a href="'.$ROOTPATHSTR.'directories.php?action=show&amp;list=terr">Территории</a></li>
				<li><a href="'.$ROOTPATHSTR.'directories.php?action=show&amp;list=workzone">Рабочие зоны</a></li>
                                <li><a href="'.$ROOTPATHSTR.'smena.php?action=showall">Рабочие смены</a></li>
                                <li><a href="'.$ROOTPATHSTR.'directories.php?action=show&amp;list=mode">Режимы</a></li>
				<li><a href="'.$ROOTPATHSTR.'directories.php?action=show&amp;list=dopusk">Допуска</a></li>
				<li><a href="'.$ROOTPATHSTR.'graph.php?action=show">Графики</a></li>
				<li><a href="'.$ROOTPATHSTR.'graph.php?action=showf">Рабочие графики сотрудников</a></li>
                <li title="Информация о сотруднике"><a href="'.$ROOTPATHSTR.'calendar/calendar.html">Календарь</a></li>
                               
			</ul>
		</li>
		<li> <a href="#">Модули</a>
			<ul>
				<li><a href="'.$ROOTPATHSTR.'reportsmenu.php">Отчёты&nbsp;&nbsp;<BR></a></li>
				<li title="Регистрация посетителей модуля Гостевого учета"><a href="'.$ROOTPATHSTR.'visitors.php?action=show">Журнал посетителей<BR></a></li> 
				<li title="Учет посещений модуля Гостевого учета: кто, когда, к кому"><a href="'.$ROOTPATHSTR.'visitings.php?action=show">Журнал посещений<BR></a></li> 
				<li title="Табельный учет"><a href="'.$ROOTPATHSTR.'tabelmanagement/tabel.php">Табельный учет<BR></a></li> 
				<li title="Документы"><a href="'.$ROOTPATHSTR.'documents/index.php"  target="_blank">Документы<BR></a></li>	
                                <li title="фотоконтроль"><a href="'.$ROOTPATHSTR.'ph2/photo.html"  target="_blank">Фотоконтроль<BR></a></li>
                                <!--li title="фотоконтроль"><a href="'.$ROOTPATHSTR.'ph"  target="_blank">Фотоконтроль nodejs<BR></a></li-->
			</ul>
		</li>
        <li><a href="#">Система&nbsp;&nbsp;<BR></a>
			<ul>
				<!--li><a href="'.$ROOTPATHSTR.'techreportsmenu.php">Тех информация&nbsp;&nbsp;<BR></a></li>
                                <li><a href="'.$ROOTPATHSTR.'techreport.php">Тех отчёт сводный&nbsp;&nbsp;<BR></a></li--!>
                                <!--li><a href="'.$ROOTPATHSTR.'node/" target="_blank">Мониторинг событий nodejs<BR></a></li-->
                                <li><a href="'.$ROOTPATHSTR.'monitor/mon.html" target="_blank">Мониторинг событий<BR></a></li>    
                                <li><a href="'.$ROOTPATHSTR.'schema.php">Схема СКУД&nbsp;&nbsp;<BR></a></li>
                                <li><a href="'.$ROOTPATHSTR.'techmenu.php">Тех отчёты&nbsp;&nbsp;<BR></a></li>
                                
				<li ><a href="'.$ROOTPATHSTR.'settings.php">Настройки&nbsp;&nbsp;</a></li>
                                <li title="Настройка и добавление юнитов"><a  href="'.$ROOTPATHSTR.'edit_units.php">Настройка юнитов</a></li>
				<li title="Учет пользователей БД: права, роли, доступ к подразделениям"><a href="'.$ROOTPATHSTR.'adminpanel.php">Управление правами пользователей&nbsp;&nbsp;</a></li>
                                <li title="Настройка периодов хранения информации"><a  href="'.$ROOTPATHSTR.'log_conf.php">Хранение информации</a></li>
                                <li title="Настроить резервное копирование бэкапов на ПК пользователя"><a href="'.$ROOTPATHSTR.'backup.php">Резервное копирование</a></li>
				<li><a  href="reboot.php" onclick=\'return confirm("Вы действительно хотите перезагрузить сервер СКУД?");\'>Перезагрузка сервера СКУД</a></li>
				<!--li title="Проведение обновлений ПО. Использовать только в случае крайней необходимости"><a href="'.$ROOTPATHSTR.'updating.php">Обновления&nbsp;&nbsp;</a></li-->
			</ul>
		</li>
        <li><a href="#">Импорт/Экспорт&nbsp;&nbsp;<BR></a>
			<ul>
				
                                <li title="Загрузка событий от турникетов в базу"><a  href="#" onclick=\'RunDTS(10,300,5)\'>Загрузка событий</a></li>
                                
				<li><a  href="#" onclick=\'RunDTS(4,250,265)\'>Загрузка событий за период</a></li>
                                <li ><a  href="#" onclick=\'RunDTS(11,300,5)\'>Выгрузка управляющей информации</a></li>
                                <li><a href="'.$ROOTPATHSTR.'sync/index.php">Синхронизация с кадровой системой<BR></a></li>
                                <li title="Не использовать! Нужен лишь для первоначальной загрузки данных в базу."><a  href="#" onclick=\'ShowWindow("importwizard/index.php","",600,400,1)\'>Мастер импорта</a></li>
			</ul>
		</li>
        <li><a href="'.$ROOTPATHSTR.'userguide/" target="_blank">Помощь&nbsp;&nbsp;</a></li>
        <li><a href="'.$ROOTPATHSTR.'exit.php">Выход&nbsp;&nbsp;</a></li>
    </ul>
';
$menu_body .= '</div>';
echo($menu_body);
?>