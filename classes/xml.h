<?php
/*
	File: xml.h v.0.1
	Author: Davydov Denis
	Create Date: 21.11.2007
	Last Modified: 21.11.2007
	Description:
	The file contains classes for creating  XML responses.
	List of classes:
	1.CXMLResponse -  for creating cover of XML data.
	  This cover contain information about response.

*/
class CXMLResponse
{

	public  $ReturnData = 0;
	public  $DataType   = 'none';
	private $Id			= '';
	private $Status     = 1;
	private $Xml;
	private $Data;

	function __construct($id)
	{
		$this->Id = $id;
		$this->Xml  = '<?xml version="1.0" encoding="windows-1251" standalone="yes"?>';
		$this->Xml .= '<response>';

	}


	public function send()
	{
		header('Content-Type: text/xml');
		$this->Xml .= '<request id="'.$this->Id.'"  status="'.$this->Status.'"  returnData="'.$this->ReturnData.'"  dataType="'.$this->DataType.'" >';
		$this->Xml .= $this->Data;
		$this->Xml .= '</request></response>';
		echo $this->Xml;
	}
	public function setError($text)
	{
      $this->Status = 0;
      $this->DataType = 'none';
      $this->ReturnData = 0;
      $this->Data = '<error>'.$text.'</error>';
	}
	public function setMessage($text)
	{
        $this->DataType = 'none';
        $this->ReturnData = 0;
		$this->Data = '<message>'.$text.'</message>';
	}
	public function addData($data,$dataType)
	{
	 $this->ReturnData = 1;
	 $this->DataType = $dataType;
	 $this->Data .= '<data>'.$data.'</data>';
	}

}


?>
