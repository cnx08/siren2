<?php
	$name = date("N");
	$prev_day = $name == 1 ? 7 : $name - 1;
        
	$str = 'C:/wamp64/www/siren2/cron_files_w/backup.bat';
   	 shell_exec($str);

	if(filesize("C:/wamp64/www/siren2/backups/".$prev_day.".zip")>300*1024*1024) // archive > 300 Mb
	{
		//clear folder
		shell_exec("del C:\wamp64\www\htdocs\siren2\backups\ /q *.*");
	}

	$zip_str = '"C:\Program Files\7-Zip\7z.exe" a -tzip -mx7 -pscud_pass_is_so_hard C:\wamp64\www\siren2\backups\\'.$name.'.zip C:\backup\file.backup';
	
	shell_exec($zip_str);
         
?>
