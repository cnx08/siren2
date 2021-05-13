function listElementMouseOver(obj)
{
   
   /*var checkbox = obj.firstChild.firstChild;
   if(checkbox.checked == false)*/ obj.style.backgroundColor = '#cecece';
}
function listElementMouseOut(obj)
{
  var checkbox = obj.firstChild.firstChild;
   if(checkbox.checked == false) obj.style.backgroundColor = 'white'; else obj.style.backgroundColor = '#dddddd';
}
function listElementClick(obj)
{
  var checkbox = obj.firstChild.firstChild;
  if( checkbox.tagName == 'INPUT' )
  {
	if(checkbox.checked == true )
    {
    	checkbox.checked = false ;
        obj.style.backgroundColor = 'white';		
	}
    else
	{
	  checkbox.checked = true;
      obj.style.backgroundColor = '#dddddd';	  
	}  
  }  
}
