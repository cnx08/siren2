<?php
require_once('../classes/base/pages.h');
include('../include/input.php');
$action = '';
$BODY  = '';

$fileName = $_GET['file_name'];
$firstRowIsName = ($_GET['first_row_is_name'] == 'true'  ) ? true : false;
$separator = $_GET['separator'];
$fileFolder = $_SESSION['impVariables']['catalog'].'\\'.$_SESSION['impVariables']['fileCatalogName'];
$filePath = $fileFolder.'\\'.$fileName;
$f_content = file($filePath);
$size = sizeof($f_content);
$start_pos = ( $firstRowIsName) ? 1 : 0;
$fieldsNames = array();

$BODY .= '<table class="reportTable">';
$BODY .= '<tr>';


if ( $firstRowIsName )
{
	 $fieldsNames = explode($separator,$f_content[0]);
}
else
{
	for ( $i = 0; $i < sizeof(explode($separator,$f_content[0])); $i++ )
	{
		$fieldsNames[$i] = 'Колонка_'.$i;
	}
}
//display headers
for ( $i = 0; $i < sizeof($fieldsNames); $i++ )
{
	$BODY .= '<th align="center">'.$fieldsNames[$i].'</th>';
}
$BODY .= '</tr>';
 
for ( $i = $start_pos; $i < $size; $i++ )
{
	$item = explode($separator,$f_content[$i]);
	$item_size = sizeof($item);
	$BODY .= '<tr>';
	
    for ( $j = 0; $j < $item_size; $j++ )
	{
		$sub_item = $item;
		$BODY .= '<td>'.$sub_item[$j].'</td>';
	} 	
	
	$BODY .= '</tr>';
}

$BODY .= '</table>';

$page = new CEmptyPage('Содержимое файла');

$page -> addCSSInclude('css/styles.css');
$page -> addJSInclude('js/controllers.js');
$page->start();

echo $BODY; 
$page->end();

?>