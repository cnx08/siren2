<?php
include("../include/input.php");

$action = null;

if ( !isset($_POST['act']) || $_POST['act'] == '' ) 
{
	header("loaction: index.php"); 
	exit();
}
else
	$action = $_POST['act'];


if ( $action == 'save' )
{
		print_r($_POST);
		
		$errorFlag = false;
		$errorText = '';
		
		//if ( !isset($_POST['personalFilePath']) )
}
	
?>