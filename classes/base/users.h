<?php

class CUserException extends Exception
{
  public function __construct($message,$errono)
  {
	parent::__construct($message, $errorno);
  }
  public function __destruct()
  {
    parent::__destruct();
  }
  public function __toString()
  {
  }
}

class CUser
{
	//private $partslPermissions = array();
	//private $settings = array();
	//private $sessionAvailable = false;
	public function __construct($username,$password)
	{
		
	}
	public function __destruct()
	{
		//unset($this->partsPermissions);
	}
	
	public function updateSetting($name,$value)
	{
		
	}
	public function partIsAvailable($name)
	{
	  return true;
	}
	public function actionIsAvailable($name)
	{
	  return true;
	}
	
}

?>