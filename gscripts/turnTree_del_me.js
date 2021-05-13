var DeptViewer = new Object();

DeptViewer.tree = null;
DeptViewer.selectedNodeId = null;
DeptViewer.selectedTNodeText = null;
 
DeptViewer.initialize = function ()
{
	DeptViewer.tree = 
	$('.simpleTree').simpleTree({
		animate: false,
		animation:false,
		drag : false,
		afterClick:function(node)
		{
			DeptViewer.selectedNodeId = $(node).attr('id');
			DeptViewer.selectedNodeText = $('span:first',node).text();
		},
		afterDblClick:function(node)
		{
			
		},
		afterMove:function(destination, source, pos){
			//alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
		},
		afterAjax:function(response)
		{
			//alert(response);
		},
		animate:true
		//,docToFolderConvert:true
	})[0]; //забираем ссылку на сам объект дерева а не наобъект jQuery
}
////////////////////////////////////////
//idNodeElementPath должен содержать имя_формы.имя_элемента куда нужно вернуть id узла
//textNodeElementPath должен содержать имя_формы.имя_элемента куда нужно вернуть текст узла
DeptViewer.returnSelectTo  = function ( idNodeElementPath, textNodeElementPath )
{
	//alert ( 'opener.document.' + textNodeElementPath + '.value="' + DeptViewer.selectedNodeText+'";');
	if ( DeptViewer.selectedNodeId  != null && DeptViewer.selectedNodeText != null )
	{
		if ( opener )
		{
			//
			eval ('opener.document.' + idNodeElementPath + '.value="' + DeptViewer.selectedNodeId+'";');
			
			eval ('opener.document.' + textNodeElementPath + '.value="' + DeptViewer.selectedNodeText + '";');
		}
		DeptViewer.closeWnd();
	}
	else
	{
		alert ( 'Пожалуйста, выберите нужный отдел  или нажмите "отмена" если таковой не найден.\nДля выбора отдела - щёлкните на его название и нажмите "оk" ' );
	}
}
DeptViewer.closeWnd = function()
{
	self.close();
}
window.onload = function ()
{
	DeptViewer.initialize();
}
