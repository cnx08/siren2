<?php
require_once('base/pages.class.php');

class CEmptyPage extends CHTMLPage
{
  public function __construct($title)
  {
		parent :: __construct($title);
  }
  public function __destruct()
  {
	parent :: __destruct();
  }
}

class CBasePage extends CHTMLPage
{
	
	public function __construct($name)
	{
		parent::__construct($name);
	}
	
	public function __destruct()
	{
		
		parent::__destruct();
	}
	
	
	public function startHead()
	{
		$view  = '';
		$view .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" >';
		$view .= '<tr><td valign="top" height="3%" >';
		echo $view;
	}
	public function endHead()
	{
		echo '</td></tr>';
	}
	
	public function startBody()
	{
		echo '<tr  ><td valign="top">';
                //echo '<tr  ><td valign="top" style="padding-top:10px;">';
	}
	
	public function endBody()
	{
		echo '</td></tr></table>';
	}
	
	
}
?>
