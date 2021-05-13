<?php
	include ("include/input.php");
        $date_start =$_GET['action1']; 
        $date_end = $_GET['action2'];
        $regex = '/\d{2}[_]\d{2}[_]\d{2}\b.txt\b/';//шаблон имени файла
        $regex_str = '/[a-zA-Z][;]\d{0,3}[;]\d{1,2}[;]\d{1,2}[;]\d{1,2}[;][A-F0-9]{1,16}[;]\d{0,10}/';//шаблон строки в файле событий
        $date_end_unix =strtotime($date_end);//будем сравнивать время в юниксовом формате

        $cntr=0;//считаем сколько юнитов

        $q = 'select * from get_active_units_and_load_info(\''.$date_start.'\')';
        $res=pg_query($q);
        while($r = pg_fetch_array($res))
        { 
            $files_string = '';
            $files_size_string = '';
            $files_upd_string = '';

            $dir=$r['path_unit'];
            $date_st=$r['date_st'];
            $date_start_unix = strtotime($date_st);
            $files = scandir($dir);
            for($i = 0, $cnt = count($files); $i < $cnt; $i++){
                if (preg_match($regex, $files[$i])) {//формат названия файла должен быть like 00.00.00.txt
                    $date_i = substr_replace(str_replace('_','.',substr($files[$i], 0, 8)),'20',6,0);//получили строку с датой ~ 12.05.2015
                    $date_i_unix = strtotime($date_i); 
                    if(strtotime($date_i)>=$date_start_unix && strtotime($date_i)<=$date_end_unix)
                    {
			$newfile = $dir.'/'.$files[$i].'tmp';

			if (!copy($dir.'/'.$files[$i], $newfile)) {
			    $q = 'select sys_write_log(1,\'evload.php\',\'\',\'не удалось copy файл '.$dir.'/'.$files[$i].' to tmp'.'\',8,0)';
                            pg_query($q);
			    continue;
			}
                        $file_arr = file($newfile);
                        for($j = 0, $cnt_str = count($file_arr); $j < $cnt_str; $j++){
				if (strlen($file_arr[$j])<12 || strlen($file_arr[$j])>45) {//длина строки больше или меньше чем должна быть x;1;18;02;33;6666666666666666;
					$str_num = $j+1;
					$q = 'select sys_write_log(1,\'php_check_rows\',\'\',\''.$dir.'/'.$files[$i].' не допустимая длина строки №'.$str_num.': '.strlen($file_arr[$j]).'\',8,0)';
					pg_query($q);
					//delete bad row
					$fp=fopen($newfile,"w");
					unset($file_arr[$j]);
					fputs($fp,implode("",$file_arr));
					fclose($fp);
					continue;
					//
				}
                            if (!preg_match($regex_str, $file_arr[$j])) {//формат of string должен быть like x;1;18;02;33;6666666666666666;
                                $str_num = $j+1;
                                $q = 'select sys_write_log(1,\'php_check_rows\',\'\',\''.$dir.'/'.$files[$i].' строка №'.$str_num.': '.$file_arr[$j].'\',8,0)';
                                pg_query($q);
                                //delete bad row
                                $fp=fopen($newfile,"w");
                                unset($file_arr[$j]);
                                fputs($fp,implode("",$file_arr));
                                fclose($fp);
                                //
                            }
			    
                        }
                        $files_string .= $files[$i].'tmp;';
                        $files_size_string .= filesize($dir.'/'.$files[$i].'tmp').';';
                        $files_upd_string .= date("d.m.Y H:i:s", filemtime($dir.'/'.$files[$i])).';';
                    }
                }

            }
            $cntr++;
            $q_load = 'select pr_sys_event_load(\''.$dir.'\',\''.$files_string.'\',\''.$files_size_string.'\',\''.$files_upd_string.'\')';
            $wqeqe = pg_fetch_array(@pg_query($q_load));
	    if($files_string !=''){
		$arr_of_files = explode(';',rtrim($files_string,';'));

		foreach($arr_of_files as $val) {
			
			unlink($dir.'/'.$val);//del tmp files
		}
	    }
            if($wqeqe['pr_sys_event_load']==0)   
            {
                echo '<center><span class="text">Загрузка событий c юнита №'.$cntr.' завершена</span><br>';
            }
            else
            {
             echo '<center>Во время выполнения произошла ошибка. См логи.</center>';
            }
        }
        echo '<input type="button" value="закрыть" class="sbutton" onclick=window.close()></center>';
