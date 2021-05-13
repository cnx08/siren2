//version 1.0
var Core = new Object();

//массив для хранения инициализаторов объектов 
//которые должны быть вызваны при загрузке страницы
Core.onLoadListeners = new Array();
//очередь запросов к серверу
Core.requestQueue = new Array();

//состояние транспорта, 0 - ничего не делает 1 - отправляет данные  
Core.transportState = 0; 
//ссылка на контейнер для диалогов
Core.dialogsContainer = null;
//активная форма
Core.activeForm = null;
//переменные стилей
Core.errorMessageStyleClass = 'errorDialog';
Core.confirmMessageStyleClass = 'confirmDialog';
//
Core.dialogButtonStyleClass = 'sbutton';
//иконка ошибки
Core.errorMessageIcon =  null;
Core.confirmMessageIcon = null;
//функция добовляет инициализатор в массив
Core.addOnLoadListener = function ( listener )
{
	Core.onLoadListeners[Core.onLoadListeners.length] = listener;
}
//добовляет объект запроса в очередь запросов
Core.addRequestObject = function ( requestObject )
{
		//проверяем, существует ли запрос 
		if ( !Core.requestObjectExists( requestObject.id ) )
		{
			//document.write('<br><b>Core</b>: Request object with id = \'' + requestObject.id + '\' not found');
			
			Core.requestQueue[Core.requestQueue.length] = requestObject;
			
			//document.write('<br><b>Core</b>: Request object with id = \'' + requestObject.id + '\' was added');
		}
		// смотрим состояние транспорта
		//если не занят то запускаем его 
		if ( Core.transportState == 0 )
		{
			//document.write('<br><b>Core</b>: Launch transport');
			Core.sendRequest();
		}
}
//удаляет объект запроса
Core.removeRequestObject = function (idRequest)
{

}
//проверяет, существует ли объект запроса
Core.requestObjectExists = function (idRequest)
{
	//document.write('<br><b>Core</b>: Search the object with id = \'' + idRequest + '\'...');
	
	for ( var i = 0; i < Core.requestQueue.length; i++ )
	{
		var r = Core.requestQueue[i];
		if ( idRequest == r.id ) return true;	
	}
	//document.write('<br><b>Core</b>: search complete');
	return false;
}

Core.sendRequest = function ()
{
	Core.transportState = 1;
	//отправляем запросы в очереди 
	for ( var i = 0; i < Core.requestQueue.length; i ++ )
	{
		var item = Core.requestQueue[i];
		if ( item != null )
		{
			//document.write('<br> <b>Core request queue content</b>:' + Core.requestQueue.toString());
			
			//alert (item.data );
			//отсылаем запрос
			var req = {
						type:item.method,
						url	: item.url,
						data : item.data,
						cache:false,
						success : Core.processResponse,
						error: Core.transportError,
						requestObject: item // запоминаем ссылку на объект запроса
					  };
				
			$.ajax(req);
			
			//document.write('<br><b>Core</b>: The request object with id = \'' + item.id + '\' was sent');
			
			Core.requestQueue[i] = null;
			
		}
		
	}
	
	Core.requestQueue.length = 0;
	Core.transportState = 0;
	//document.write('<br> <b>Core sent request queue content</b>:' + Core.sentRequestQueue.toString());
}
//обработка ответов от сервера
Core.processResponse  = function (response)
{
	//alert ( this.requestObject.id);
	this.requestObject.onSuccess( response );
	this.requestObject = null;
}
//////////////////////////////
Core.transportError = function (XMLHttpRequest, textStatus, errorThrown)
{
	this.requestObject.onError( 'Ядро приложения- > Ошибка транспорта: ' + XMLHttpRequest.status + ' - '+ XMLHttpRequest.statusText );
	this.requestObject = null;
}
//создаёт объект запроса
Core.request = function(id,url,method,data,returnDataType,onSuccess,onError)
{
	this.id = id;
	this.url = url;
	this.method = method;
	this.data = data;
	this.returnDataType = returnDataType;
	this.onSuccess = onSuccess;
	this.onError = onError;
	
	//document.write('<br><b>Core Request</b>: request object with id = \'' + this.id + '\' was created');
}
//инициализируем ядро
Core.initialize = function()
{
	// инициализируем меню
	Core.initializeMenu();
	//инициализируем контейнер для диалогов
	Core.createDialogsContainer();
	
	//создаём иконки
	Core.errorMessageIcon  = new Image();
	Core.errorMessageIcon.src = 'images/error48.gif';
	Core.confirmMessageIcon  = new Image();
	Core.confirmMessageIcon.src = 'images/check48.gif';
	//инициализируем объекты
	for ( var i = 0; i < Core.onLoadListeners.length; i ++ )
	{
		var list = Core.onLoadListeners[i];
		list.call();
		//document.write('<br><b>Core:</b> initialize listener' + i );
	}	
	
}
//инициализирует меню
Core.initializeMenu = function ()
{
		if( navigator.appVersion.indexOf('MSIE')==-1 )
			return;
		var i, k, g, lg, r =/\s*p7hvr/, nn = '', c, cs ='p7hvr', bv = 'p7menubar';
// for( i = 0; i < 10; i++ )
 //{
	g = document.getElementById(bv+nn);
	if( g )
	{
		lg = g.getElementsByTagName("LI");
		if( lg )
		{
			for( k = 0; k < lg.length; k++ )
			{
				lg[k].onmouseover = function()
				{ 
					c = this.className;
					cl = ( c ) ? c + ' ' + cs:cs;
					this.className=cl;
				};
				lg[k].onmouseout = function()
				{
					c = this.className;
					this.className = ( c ) ? c.replace(r,''):'';
				};
			}
		}
	}
	//nn=i+1;
 //}
} 
Core.showDialog = function(type,headerText,bodyText)
{
	
	$(Core.dialogsContainer).empty();
	var X = Core.getTopXForCeter($(Core.dialogsContainer).width());
	var Y = 100;
	var wndStyle, icon ;
	
	if ( type == 'error')
	{
		wndStyle = Core.errorMessageStyleClass;
		icon = Core.errorMessageIcon.src;
	}
	
	if ( type == 'confirm' )
	{
		wndStyle = Core.confirmMessageStyleClass;
		icon = Core.confirmMessageIcon.src;
	}
	
	
		var c = '<table border="0" cellpadding="0" cellspacing="0" class="'+ wndStyle +'">';
			c += '<tr><th colspan="2">' + headerText + '</th></tr>';
			c += '<tr><td align="center" width="80"><img src="' + icon + '"></td>';
			c += '<td>' + bodyText + '</td></tr>';
			c += '<tr><td colspan="2" align="center"><input type="button" class="'+ Core.dialogButtonStyleClass +'" value="закрыть" onclick="Core.closeDialog()" ></tr></tr>';
			c += '</table>';
			
		$(Core.dialogsContainer).append(c);
		
		$(Core.dialogsContainer).css({left:X,top:Y});
		
		$(Core.dialogsContainer).show();
}
Core.closeDialog = function()
{
	$(Core.dialogsContainer).hide();
}
//создаёт контейнер для диалогов
Core.createDialogsContainer = function ()
{
	Core.dialogsContainer = document.createElement('div');
	Core.dialogsContainer.id = 'dialogContainer';  
	
	Core.dialogsContainer.style.position = 'absolute';
	Core.dialogsContainer.style.top  = '0px';
 	Core.dialogsContainer.style.left = '0px';
	Core.dialogsContainer.style.zIndex = '1000';
	Core.dialogsContainer.style.width = 500 + 'px'; 
	Core.dialogsContainer.style.display = 'none'; 
	Core.dialogsContainer.style.backgroundColor = 'white';
	
	document.body.appendChild(Core.dialogsContainer);
}

//возвращает координату X верхнего левого угла
//для то го что бы расположить прямоугольник шириной rW по центру экрана
Core.getTopXForCeter = function (rW)
{
	var x = parseInt(document.body.clientWidth / 2 ) - parseInt(rW / 2);
	return x;
}
//возвращает координату X верхнего левого угла
//для то го что бы расположить прямоугольник шириной rY по центру экрана
Core.getTopYForCeter = function (rH)
{
	
	var y = parseInt(document.body.clientHeight / 2 ) - parseInt(rH / 2);
	return y;
}

Core.showForm = function (obj,idForm )
{
	
	var x = $(obj).offset().left;
	var y  = $(obj).offset().top + $(obj).height();
	
	//если естm то скрываем предыдущее окно
	if ( Core.activeForm  != null )
	{
		$('#' + Core.activeForm).hide(); 
	}
	
	$('#' + idForm).css({left:x,top:y});
	$('#' + idForm).show(); 
	
	Core.activeForm = idForm;	
	
}
//////////////////////////////////////////
// скрывает форму
Core.hideForm = function ( idForm )
{
	
	$('#' + Core.activeForm).hide(); 
	
	Core.activeForm = null;
}
//открывает браузер объектов
Core.openObjectViewer = function ( obj ,url)
{
	var objViewerHeight = 300;
	var objViewerWidth  = 300;
	var objViewerTop    = $(obj).offset().top;
	var objViewerLeft   = $(obj).offset().left + $(obj).width();
	

	//alert ( document.body.clientHeight );
	var params = 'width = ' + objViewerWidth + ', height = ' + objViewerHeight + ',';
		params += 'top = ' + objViewerTop + ', left = ' + objViewerLeft + ',';
		params += 'scrollbars = 0,';
		params += 'status = 0';
	//alert ( params);	
	window.open(url,'',params);
}  

 window.onload = function ()
 {
	Core.initialize();
	
 }

Core.changeLabelButtonImage = function(obj,img)
{
	if ( obj.className.indexOf('Over') < 0 )
	{
		obj.className += 'Over';
	//	obj.firstChild.src = img;
	}
	else
	{
		var className = obj.className;
		obj.className = obj.className.substring(0,className.indexOf('Over'));
		//this.className = this.className.replace(new RegExp(" " + this.styleDescription.onMouseOverClass +"\\b"), "");
	}
	
	obj.firstChild.src = img;
	
}