
var CURRENT_ACTIVE_FORM_ID = 'personal_set_form';

function showCSVFileContent(scriptName,fileName,firstRowIsName,separator)
{
  var u = scriptName + '?file_name=' + fileName + '&first_row_is_name='+firstRowIsName + '&separator='+separator;
	
   //alert(wp);
  window.open(u,"","Width=800,Height=500,Scrollbars=1,top=150,left=0,resizeble=1");
}
function onImortTableChange(obj)
{
	var choose = obj.value;
	
	if ( CURRENT_ACTIVE_FORM_ID != null )
	{
		var form = document.getElementById(CURRENT_ACTIVE_FORM_ID);
		if ( form )
		{
			form.style.display = 'none';
		}
	}
	
	switch (choose)
	{
		case '0':
			var form = document.getElementById('personal_set_form');
			if ( form )
				form.style.display = 'block';
			
			CURRENT_ACTIVE_FORM_ID = 'personal_set_form';
		break;
		case '1':
			var form = document.getElementById('departments_set_form');
			if ( form )
				form.style.display = 'block';
			
			CURRENT_ACTIVE_FORM_ID = 'departments_set_form';
		break;
		case '2':
		case '1':
			var form = document.getElementById('pass_set_form');
			if ( form )
				form.style.display = 'block';
			
			CURRENT_ACTIVE_FORM_ID = 'pass_set_form';
		break;
		default:break;
	}
}

