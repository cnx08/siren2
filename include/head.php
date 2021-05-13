<?php
$BODY=''; // строка с кодом тела html страницы

function PrintHead($title,$pagetitle)
{
    $result=' ';
	$result.= '<!DOCTYPE html>';	
    $result.= '<html>';
    $result.='<head>';
		$result.='<title>';
			$result.= $title;
		$result.='</title>';
		$result.='<meta http-equiv="Content-Language" content="ru">';
		$result.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		//скрипты	 
		$result.='<script type="text/javascript" src="include/function.js"></script>';
		$result.='<script type="text/javascript" src="include/controllers.js"></script>';
		$result.='<script type="text/javascript" src="include/_library_elements.js"></script>';
		$result.='<script type="text/javascript" src="include/_request_functions.js"></script>';
		$result.='<script type="text/javascript" src="include/Net.js"></script>';
		$result.='<script type="text/javascript" src="include/window.js"></script>';
		
		//стили
		$result.='<link rel="stylesheet" type="text/css" href="styles/menu.css">';
		$result.='<link rel="stylesheet" type="text/css" href="include/style.css">';
		$result.='<link rel="stylesheet" type="text/css" href="styles/calendar.css">';
		$result.='<link rel="stylesheet" type="text/css" href="styles/cleanup.css">';
		$result.='<link rel="stylesheet" type="text/css" href="gstyles/common_styles.css">';
		$result.='<link rel="stylesheet" type="text/css" href="styles/datatable.css">';
		$result.='<link rel="stylesheet" type="text/css" href="styles/link.css">';	 
		$result.='<link rel="stylesheet" type="text/css" href="styles/window.css">';	 
		$result.='<link rel="stylesheet" type="text/css" href="../gstyles/wnd_styles.css">';
                $result.='<link rel="stylesheet" type="text/css" href="../gstyles/pickmeup.css">';
                
		$result .= '<link rel="icon" href="favicon.ico" type="image/x-icon">';
    $result.='</head>';
    $result.='<body style="margin-top:0;margin-left:0;margin-right:0;margin-bottom:0;">';
		$result.='<div id = "page_title" style="background-color: silver; ">'; 	// -- добавил width по ширине меню
			$result.='<p><font face="Verdana" size=2 color="black"><b>'.$pagetitle.'</b></font></p>';
		$result .='</div>';

return $result;
}
function PrintFooter()
{
   $result='';
   $result.='</body></html>';
   return $result;
 }
 
 function PrintHeadNew($title,$pagetitle,$INCLUDES)
{
    $result=' ';
    $result.= '<!DOCTYPE html>';	
    $result.= '<html>';
    $result.='<head>';
    $result.='<title>';
    $result.= $title;
    $result.='</title>';
    $result.='<meta http-equiv="Content-Language" content="ru">';
    $result.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    
    //styles
    //$result.='<link rel="stylesheet" type="text/css" href="styles/menu.css">';
    //$result.='<link rel="stylesheet" type="text/css" href="include/style.css">';
    //$result.='<link rel="stylesheet" type="text/css" href="styles/calendar.css">';
    $result.='<link rel="stylesheet" type="text/css" href="styles/cleanup.css">';
    //$result.='<link rel="stylesheet" type="text/css" href="gstyles/common_styles.css">';
    //$result.='<link rel="stylesheet" type="text/css" href="styles/datatable.css">';
    //$result.='<link rel="stylesheet" type="text/css" href="styles/link.css">';	 
    //$result.='<link rel="stylesheet" type="text/css" href="styles/window.css">';	 
    //$result.='<link rel="stylesheet" type="text/css" href="../gstyles/wnd_styles.css">';
    //$result.='<link rel="stylesheet" type="text/css" href="../gstyles/pickmeup.css">';
    //скрипты	
    //$result.='<script type="text/javascript" src="include/function.js"></script>';
    //$result.='<script type="text/javascript" src="include/controllers.js"></script>';
    //$result.='<script type="text/javascript" src="include/_library_elements.js"></script>';
    ////$result.='<script type="text/javascript" src="include/_request_functions.js"></script>';
    //$result.='<script type="text/javascript" src="include/Net.js"></script>';
    //$result.='<script type="text/javascript" src="include/window.js"></script>';
    //$result.='<script type="text/javascript" src="gscripts/core.js"></script>';
    
    if(sizeof($INCLUDES)>0)
    for($i = 0; $i < sizeof($INCLUDES); $i++) $result .= $INCLUDES[$i];
    $result.='</head>';
    $result.='<body style="margin-top:0;margin-left:0;margin-right:0;margin-bottom:0;">';
    $result.='<div id = "page_title" style="background-color: silver; ">'; 	// -- добавил width по ширине меню
    $result.='<p><font face="Verdana" size=2 color="black"><b>'.$pagetitle.'</b></font></p>';
    $result .='</div>';


return $result;
}
?>