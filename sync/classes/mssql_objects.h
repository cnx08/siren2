<?php
class CJobInfo
{
	private $dataSet;	
	
	public function __construct($dataSet)
	{
		$this->dataSet = $dataSet;
	}
	public function __destruct()
	{
		unset($this->dataSet);
	}
	public function Name()
	{
		return $this->dataSet['name'];
	}
	public function Enabled()
	{
		if ($this->dataSet['enabled'] == 1) return 'включено';else return 'выключено';
	}
	public function LastRunDateTime()
	{
		return $this->convertDate('lastRunDate').' '.$this->convertTime('lastRunTime');
	}
	public function nextRunDateTime()
	{
		return $this->convertDate('nextRunDate').' '.$this->convertTime('nextRunTime');
	}
	public function runStatus()
	{
		$st = $this->dataSet['lastRunStatus'];
		if ($st == 0 ) return 'Сбой';
		if ($st == 1 ) return 'Выполнено';
	}
	private function convertDate($name)
	{
		$date = $this->dataSet[$name];
		//echo $date;
		$y = substr($date,0,4);
		$m = substr($date,4,2);
		$d = substr($date,6,2);
		return $d.'.'.$m.'.'.$y;
	}
	private function convertTime($name)
	{
		$time = $this->dataSet[$name];
		//echo $time;
		$size = strlen($time);
		if ($size == 1) return '00:00:0'.$time;
		if ($size == 2) return '00:00:'.$time;
		if ($size == 3) return '00:0'.substr($time,0,1).':'.substr($time,1,$size);
		if ($size == 4) return '00:'.substr($time,0,2).':'.substr($time,2,$size);
		if ($size == 5) return '0'.substr($time,0,1).':'.substr($time,1,2).':'.substr($time,3,$size);
		if ($size == 6) return substr($time,0,2).':'.substr($time,2,2).':'.substr($time,4,$size);
	}
}



?>