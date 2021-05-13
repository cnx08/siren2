<?php
interface IMSSQLReport
{
	public function renderReport();

}

class CMSSQLReport implements IMSSQLReport
{
  private $query;
  private $class_name;
  private $query_result = NULL;
  private $Fields =  array();
  private $Headers =  array();
  private $head_style;
  private $Properties = array();
  public  $name='';


  public function  CMSSQLReport($query,$name)
  {
  	$this->query = $query;
  	$this->name = $name;
    $this->Properties['first_col_string'] = 'silver';
    $this->Properties['second_col_string'] = '#f5f5f5';

  }
  public function setPropertyValue($name,$value)
  {
  	  if(isset($this->Properties[$name]))
  	     $this->Properties[$name] = $value;
  	  else
  	     echo 'Метод:setPropertyValue - Обращение к несуществующему свойству';

  }

  public function getData()
  {

   $this->query_result = mssql_query($this->query);
  }
  public function setStyleClassName($name)
  {
  	$this->class_name = $name;

  }
  public function setReportHeaders($headers,$spliter)
  {
  	$array = explode($spliter,$headers);
  	$size = sizeof($array);
  	for( $i = 0; $i < $size; $i++ )
  	{
  		$this->Headers[] = $array[$i];
  	}
  	unset($array);

  }

  public function setFields($fields,$spliter)
  {
    $array = explode($spliter,$fields);
  	$size = sizeof($array);
  	for( $i = 0; $i < $size; $i++ )
  	{
  		$this->Fields[] = $array[$i];
  	}
  	unset($array);
  }

  public function showQuery()
  {
    echo $this->query;
  }


  public function renderReport()
  {

    $flag = 0;

    $r = '';
    $r .= '<table border="0" class="'.$this->class_name.'" >';
    $r .= '<thead>';
    if($this->name!='') $r .= '<caption>'.$this->name.'</caption>';


    $size = sizeof($this->Headers);
    for( $i = 0; $i < $size; $i++)
    {
      $r .= '<th>'.$this->Headers[$i].'</th>';
    }
    $r .= '</thead>';
    $r .= '<tbody>';
  if($this->query_result == NULL) $this->getData();


   $flag=0;
   $field_count = sizeof($this->Fields);
   while($res = mssql_fetch_array($this->query_result))
    {


        $bgcolor = '';
        if($flag==0){$bgcolor=$this->Properties['first_col_string'];$flag=1;}else{$bgcolor=$this->Properties['second_col_string'];$flag=0;}

        $k = 0;
        $r .= '<tr bgcolor='.$bgcolor.'>';
        while($k < $field_count)
        {
           $r .= '<td>'.$res[$this->Fields[$k]].'</td>';
           $k++;
        }

        $r .= '</tr>';
    }

    $r .= '</tbody>';
    $r .= '</table>';

  return $r;
 }
}


?>