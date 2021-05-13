<?php

$IDMODUL=37;
include("include/input.php");
require("include/common.php");
require("include/head.php");
echo PrintHead('СКУД','Резервное копирование');

//проверяем на доступность
if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}

require_once("include/menu.php");

require_once("file_for_wget.php");


$BODY.='<div style = "font-size: 14px; margin: 15px;">
            <div align="center">
               <br>Описание резервного копирования базы данных с сервера СКУД.<br>
           </div><br>
        
            <p style = "text-indent:20px;">
                На сервере хранятся бэкапы сделанные за неделю (а при нехватке места - за 1 день). По мере
                работы размер БД будет увеличиваться, а размер памяти на микро ПК ограничивается размером установленной карты памяти.
                Чтобы иметь возможность хранить длинную историю бэкапов, предусмотрена возможность скачивания с сервера актуальных
                архивов резервных копий на другие ПК в локальной сети по протоколу HTTP .
            </p><br>
            <p style = "text-indent:20px;">
                Чтобы сделать Ваш ПК местом, где будет храниться история бэкапов, нужно скачать и запустить <b>store_backups_here.exe</b>.
                При этом на диске С будет создана папка GnuWin32 с программой WGET для скачивания файлов по протоколу HTTP, а также два скрипта,
                которые будут запускаться планировщиком заданий. Эти задания будут ежедневно обновлять бэкапы <номер-дня-недели>.zip в папке C:\backup,
                и раз в месяц сохранять файл 1.zip, переименовав его в <текущая-дата>.zip.
            </p><br>
            <p style = "text-indent:20px;">
                В папке GnuWin32 находится файл <b>load.txt</b>, в котором указаны пути к файлам, которые нужно скачивать с сервера СКУД.
                Рекомендуется его обновить, скачав по ссылке ниже.
            </p><br>
            <p style = "text-indent:20px;">
                Для того чтобы выполнялись задачи в планировщике, на ПК должен быть пользователь с паролем, от имени которого будут выполняться
                задачи. Если на вашем ПК установлена ОС Windows XP, то при установке потребуется ввод пароля пользователя, от имени которого будут выполняться задачи.
                По умолчанию ежедневный бэкап выполняется в 13:00, а ежемесяный в 14:00. Это сделано в расчёте на то, что в рабочее время ПК будет
                включен.
                Пользователя и время запуска заданий можно вдальнейшем изменить.
            </p><br><br>
            <div align="center"> Ссылки для скачивания:</div><br>
            <div align="center">
                <ul>
                    <li><a href = "wget/store_backups_here.exe" type = "application/file">store_backups_here.exe</a></li>
                    <li><a href = "wget/load.txt" download="">load.txt</a></li>
                </ul>
            </div>
        </div>
';

echo $BODY;