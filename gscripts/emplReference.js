function execGroupOp(obj,form)
{
	
	var list    =  obj.previousSibling;
	if ( !list )
	{
		alert ('Ошибка: не удаётся определить действие');
		return;
	}
	if ( list.selectedIndex <= 0  )
	{
		alert ('Не указано действие');
		return;
	}
	
	var selectedId = '';
	var selCount = 0;
	for ( var i = 0; i < form.elements.length; i ++ )
	{
		var item = form.elements[i];
		//выбираем 
		if ( item.type == 'checkbox' && item.name.indexOf('select')!=-1)
		{
			if( item.checked == true )
			{
				selectedId += item.name.split('_')[1] + ';';
				selCount ++;
			}
		}
	}
	
	if ( selCount == 0 )
	{
		alert('Никого не выбрано');
		return;
	}
	//определяем действие
	switch ( list.selectedIndex )
	{
		//удаление
		case 1:
				if ( confirm( 'Вы действительно хотите удалить ' + selCount + ' сотрудников ?' ) )
				{
					var url = 'controllers/empl.controller.php?act=delete&selected';
						url += selectedId.substring(0,selectedId.length-1);
					alert (url);	
					//window.open('controllers/empl.controller.php')
				}
				
		break;
		case 2:
				if ( confirm( 'Вы действительно хотите назначить график ' + selCount + ' сотрудникам ?' ) )
				{
					alert('Пизда нах ' + selCount + ' сотрудникам' );
				}
		break;
		case 3:
				if ( confirm( 'Вы действительно хотите изменить статус пропуска у ' + selCount + ' сотрудников ?' ) )
				{
					alert('Пизда нах ' + selCount + ' сотрудникам' );
				}
		break;
		case 4:
				if ( confirm( 'Вы действительно хотите перевести ' + selCount + ' сотрудников в отдел ?' ) )
				{
					alert('Пизда нах ' + selCount + ' сотрудникам' );
				}
		break;
		case 5:
				if ( confirm( 'Вы действительно хотите назначить расчёт ' + selCount + ' сотрудникам ?' ) )
				{
					alert('Пизда нах ' + selCount + ' сотрудникам' );
				}
		break;
		default:break;
	}
	
}