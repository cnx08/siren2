function showLogWindow(url)
{
  var wp="";//свойства окна
  wp+="fullscreen=0,";
  wp+="scrollbars=1,";
  wp+="resizable=1,";
  wp+="status=1";
  //alert(wp);
  window.open(url,'Логи',wp);
}

function showLogs()
{
	var f = document.logsForm;
	var url = 'logs.php?act=show';
	url  += '&start_date=' + f.logStartDate.value;
	url  += '&end_date=' + f.logEndDate.value;
	
	showLogWindow(url);
}
function ExportWt(f)
{
    var f = document.ManagForm;
	var url = 'index.php?act=exportWt';
	url  += '&date=' + f.ExpDate.value;
        document.location.href=url;
}