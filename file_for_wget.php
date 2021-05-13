<?php
	$ip = $_SERVER['SERVER_ADDR'];
	$handle = fopen("wget/load.txt","w");
	for($i=1;$i<8;$i++){
		fwrite($handle,"http://".$ip."/backups/".$i.".zip\r\n");
	}
	fclose($handle);
?> 
