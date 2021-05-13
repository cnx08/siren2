<?php 

if(isset($_POST['upload'])){
	  
		$pathf=GetCWD()."/foto/";
		$name = $_FILES['userfile']['name'];
		
		if (stripos(php_uname(),'linux')===false)$name = iconv("UTF-8","CP1251",$name);	
                
                $uploadedFile =  $pathf.$name;
                
		if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
		
			if(move_uploaded_file($_FILES['userfile']['tmp_name'],$uploadedFile)){
		
		        $data = $_FILES['userfile'];
				//$data['errors'] = $_FILES['userfile']['name'];
			}
			else {	
				$data['errors'] = $uploadedFile;
				//$data['errors'] = "Во время загрузки файла произошла ошибка";
			}
		}
		else {	
			$data['errors'] = "Файл не  загружен";
		}

    //Формируем js-файл    
    $res = '<script type="text/javascript">';
    $res .= "var data = new Object;";
    foreach($data as $key => $value){
    	$res .= 'data.'.$key.' = "'.$value.'";';
    }
    $res .= 'window.parent.handleResponse(data);';
    $res .= "</script>";
    
    echo $res;

}
else{
	die("ERROR");
}

?>
