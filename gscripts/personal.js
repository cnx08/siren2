function removePerson ( fio,idPers )
{
	var url = 'personal.php?action=del&id=' + idPers + '&plink';
	
	if ( confirm( 'Вы действительно хотите удалить ' +  fio.replace(/~/g,' ') ) )
	{
		document.location.href = url;
	}
}