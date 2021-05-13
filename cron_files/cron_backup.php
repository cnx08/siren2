#!/bin/bash
<?php
	$name = date("N");
	$prev_day = $name == 1 ? 7 : $name - 1;
	
	$str = "pg_dump -U postgres -h localhost -Fc testutf > /backup/file.backup";
   	 shell_exec($str);

	if(filesize("/var/www/html/backups/".$prev_day.".zip")>300*1024*1024) // archive > 300 Mb
	{
		//clear folder
		shell_exec("rm /var/www/html/backups/*");
	}

	$zip_str = "zip -j  /var/www/html/backups/".$name.".zip /backup/file.backup -P scud_pass_is_so_hard";
	shell_exec($zip_str);
?>