<?php

class CELSelect
{
  private $name;
  private $id;
  private $query;
  private $query_result;
  private $data_field;
  private $opt_value_field;
  private $db_type;
  private $style_class;
  private $Properties = array();
  private $Options = array();
  private $Events = array();



  public function CELSelect($query,$name,$id)
  {

  	$this->query = $query;
  	$this->name  = $name;
  	$this->id   = $id;
  	$this->Properties['selectedItem'] = 0;
  	$this->Properties['disabled'] = 0;
  	$this->Events['onclick']='';
  	$this->Events['onmouseover']='';
  	$this->Events['onmouseout']='';

  }
  public function setStyleClassName($class_name)
  {
  	$this->style_class = $class_name;
  }

  public function getData($db_type) //mssql or mysql
  {
    if($this->query != '')
    {
      if($db_type = 'mssql')
      {
      	 $this->db_type = 'mssql';
      	 $this->query_result = mssql_query($this->query);
      }
      else if ($db_type = 'mysql')
      {
      	$this->db_type = 'mssql';
      	$this->query_result = mysql_query($this->query);
      }
      else
        echo 'Error: class CELSelect,method getData - undefined data type';

    }
    else
    {
    	echo 'Error: class CELSelect,method getData - string of query don\'t must be empty';
    }

  }
  public function setPropertyValue($name,$value)
  {
  	if(isset($this->Properties[$name]))
    	$this->Properties[$name]=$value;
    else
      echo 'Error: class CELSelect,method setPropertyValue - incorrect property name';
  }
  public function setOptions($opt_string,$spliter)
  {
  	$array = explode($spliter,$opt_string);
  	$size = sizeof($array);
  	for( $i = 0; $i < $size; $i++ )
  	{
  		$this->Options[] = $array[$i];
  	}
  	unset($array);
  }
  public function setDataField($name,$opt_value)
  {
  	  $this->data_field = $name;
  	  $this->opt_value_field = $opt_value;
  }
  public function renderElement()
  {
    $r = '<select name="'.$this->name.'" id="'.$this->id.'"  class="'.$this->style_class.'" ';
    $r .= 'disabled="'.$this->Properties['disabled'].'">';

    if($this->query == '')
    {
       if(sizeof($this->Options)!=0)
       {
          $size = sizeof($this->Options);
          $selected = '';

          for($i = 0; $i < $size; $i++)
          {
          	if($this->$Properties['selectedItem'] == $i)$selected = 'selected'; else $selected = '';

          	$r .= '<option value="'.$i.'" '.$selected.'>'.$this->Options[$i].'</option>';
          }
       }
    }
    else
    {
     if($this->db_type == 'mssql')
     {
       while($res = mssql_fetch_array($this->query_result))
       {
          $selected = '';
          if($this->Properties['selectedItem'] == $res[$this->opt_value_field])$selected = 'selected'; else $selected = '';
          $r .= '<option value="'.$res[$this->opt_value_field].'" '.$selected.'>'.$res[$this->data_field].'</option>';

       }
     }
     else if($this->db_type == 'mysql')
     {
       while($res = mysql_fetch_array($this->query_result))
       {
       	  $selected = '';
          if($this->Properties['selectedItem'] == $res[$this->opt_value_field])$selected = 'selected'; else $selected = '';
          $r .= '<option value="'.$res[$this->opt_value_field].'" '.$selected.'>'.$res[$this->data_field].'</option>';
       }
     }
    }
    $r.='</select>';

    return $r;
  }
}

?>