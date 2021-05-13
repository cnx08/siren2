<?php
abstract class CForm 
{
	public	   $elements;
	protected $styles;
	protected $properties;
	protected $view;
	protected $name;
	
	public function __construct($name,$action,$method='POST',$type=null)
	{
		 $this->name = $name;
		 $this->properties['id']  =  $name;
		 $this->properties['name'] = $name;
		 $this->properties['action'] = $action;
		 $this->properties['method'] = $method;
		 $this->properties['enctype'] = $type;
		 $this->properties['target'] = null;
		 
		 $this->styles['select'] = null;
		 $this->styles['button'] = null;
		 $this->styles['table']	= null;
		 $this->styles['textField'] = null;
		 
		//print_r($this->properties);
	}
	public function setProperty($name,$value)
	{
		if(array_key_exists($name,$this->properties))
			$this->properties[$name] = $value;
	}
	public function getProperty($name)
	{
		if(array_key_exists($name,$this->properties))
			return $this->properties[$name];
	}
	public function setStyle($name,$value)
	{
		if(array_key_exists($name,$this->styles))
			$this->styles[$name] = $value;
	}
	///////////////////////////////////
	public function __destruct()
	{
		 unset($this->name);
		 unset($this->action);
		 unset($this->method);
		 unset($this->type);
		 unset($this->elements);
		 unset($this->styles);
	}
	protected function getAllProperties()
	{
		$prop = null;
		foreach($this->properties as $key=>$value )
		{
			if($value != null)
				$prop .= $key.'="'.$value.'" '; 
		}
		return $prop;	
	}
	abstract function render();	
}




?>