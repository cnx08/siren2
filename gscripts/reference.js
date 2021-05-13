function itemsTracking(obj,color,skipColor)
{
	//alert ( skipColor);
	if ( obj.style.backgroundColor != skipColor )
		obj.style.backgroundColor = color;
}
function itemsSelect(obj,selColor,unSelColor)
{
	//пытаемся добраться до checkbox
	//предположительно он должен быть здесь
	try
	{
		var ch =  obj.firstChild.firstChild;
		
		if ( ch.checked == true)
		{
			
			ch.checked = false;
			obj.style.backgroundColor =  unSelColor;
		}
		else
		{
			ch.checked = true;
			obj.style.backgroundColor =  selColor;
		}
	}
	catch(e){}
	
}
function checkboxClick ( obj, selColor,unSelColor )
{
	//пытаемся добраться до tr
	//предположительно он должен быть здесь
	try
	{
		
		
		var tr =  obj.parentNode.parentNode;
		
		if ( obj.checked == true)
		{
			
			obj.checked = false;
			tr.style.backgroundColor =  unSelColor;
		}
		else
		{
			obj.checked = true;
			tr.style.backgroundColor =  selColor;
		}
	}
	catch(e){}
}
function checkAll(obj,form,selColor,unSelColor)
{
	try
	{
	
		var select = ( obj.checked) ? true : false;
		var color  = ( obj.checked) ? selColor : unSelColor;
	
		//alert(form.elements.length);
		for ( var i = 0; i < form.elements.length; i ++ )
		{
			var item = form.elements[i];
			if ( item.type == 'checkbox' )
			{
				item.checked  = select;
				var tr =  item.parentNode.parentNode;
				tr.style.backgroundColor = color;
			}
		}
	}
	catch(e){}
}

