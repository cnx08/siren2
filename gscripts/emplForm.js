function  showPhoto(idImgObj,idInputObj)
{
    //var image  = document.getElementById(idImgObj);
	var source = document.getElementById(idInputObj);
	alert(source.value);
	var image = new Image();
		image.src = source.value;
	document.body.appendChild(image);
	
}
function changeEnabled (obj,idSelectObj)
{
	try
	{
		var el = document.getElementById(idSelectObj);
		if ( obj.checked)
			el.disabled = true;
		else
			el.disabled = false;
	}
	catch ( e )
	{
		alert ('Ошибка:' + e.message );
	}
	
	
}

function clearInput( idInputObj )
{
	var el = document.getElementById( idInputObj );
	
	try
	{
		el.value = '';
	}
	catch ( e )
	{
		alert ('Ошибка:' + e.message );
	}
		
}