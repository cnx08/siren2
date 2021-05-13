var Core = new Object()
Core.modalLayer = null;	   //ссылка на модальный слой!!!!!
Core.activeForm = null;
Core.requestAllowed = true; // определяет, разрешены ли запросы к серверу или нет, либо занято
Core.responseListener = null;// обработчик данных, который   нужно вызвать после получения ответа от сервера
///////////////////////////////////
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
///////////////////////////////////

///////////////////////////////////////////
// отображает форму
Core.showForm = function (obj,idForm )
{
	//Core.showModalLayer();
	var x = $(obj).offset().left;
	var y  = $(obj).offset().top + $(obj).height();
	
	//если естm то скрываем предыдущее окно
	if ( Core.activeForm  != null )
	{
		$('#' + Core.activeForm).hide('fast'); 
	}
	//
	$('#' + idForm).css({left:x,top:y});
	$('#' + idForm).show('fast'); 
	Core.activeForm = idForm;	
	
}
//////////////////////////////////////////
// скрывает форму
Core.hideForm = function ( idForm )
{
	
	$('#' + Core.activeForm).hide('fast'); 
	
	Core.activeForm = null;
}

//////////////////////////////////////////
//  в окне свойств должены имется id контейнеров
//wndHeader и wndBody
Core.showObjProp = function (obj,url,idWnd,wndOffset)
{
	var x = null;
	var y = $(obj).offset().top;
	
	if ( wndOffset == 'left')
	{
		x = $(obj).offset().left - $('#' + idWnd).width();
	}
	//если есть то скрываем предыдущее окно
	if ( Core.activeForm  != null )
	{
		$('#' + Core.activeForm).hide('fast'); 
	}
	//
	$('#' + idWnd).css({left:x,top:y});
	$('#' + idWnd).show('fast');
	$('#' + idWnd + 'Body').empty();
	$('#' + idWnd + 'Body').append('<center>Подождите, идёт загрузка данных...<br><br><img src="images/indicator.gif" ></center>');
	
	
	Core.activeForm = idWnd;	
}
//обрабатывает данные свойств объекта
Core.objPropListener = function ()
{
	
}
//выполняет запрос к серверу
Core.sendRequest  = function (reqUrl,respType,reqParam,onload,callerObj)
{
	//определяем не занят ли транспорт
	if ( !Core.requestAllowed )
	{
		alert ('Транспорт занят');
		return;
	}
	else
	{	
	   alert ('Транспорт свободен');
	   Core.requestAllowed = false;
	   Core.responseListener = onload;
	 } 
		
	try
	{
		alert(reqUrl+' '+reqParam);
		
		var requestParam = 
			{
				type     : 'POST',
				data	 : reqParam,
				url  	 : reqUrl,
				dataType : respType,
				success  : Core.processResponse
			};
		$.ajax(requestParam);	
			
		//зануляем ссылку на вызывающий объект
		callerObj = null;
	}
	catch( e )
	{
		alert('Ошибка js - ядра приложения:' +  e.message );
		callerObj.coreErrorDetected.call();
		callerObj = null;
	}	
}
//обрабатывает 
Core.processResponse = function (response)
{
	//alert(response);
	if (Core.responseListener != null )
	{	
		Core.responseListener(response);
		Core.responseListener = null;
		Core.requestAllowed = true;
	}	
}
//////////////////////////////////////////
/*
Core.showModalLayer = function ()
{
	if ( Core.modalLayer == null )
	{
		var div = document.createElement('div');
			div.id = 'modalLayer';
			div.style.filter = 'alpha(opacity=60, finishopacity=70, style=0)';
			div.style.display = 'none';
			div.style.zIndex = 10000;
			div.style.position = 'absolute';
			div.style.top = 0 + 'px';
			div.style.left = 0 + 'px';
			div.style.width = 100 + '%';
			div.style.height = 100 + '%';
			div.style.opacity = '0.6';
			div.style.backgroundColor = '#9bc2e8';
			document.body.appendChild(div);
			Core.modalLayer = div;
			
	}	
	
	$('#'+Core.modalLayer.id).show('slow');

}*/
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
/*
Core.hideModalLayer = function()
{
	$('#'+Core.modalLayer.id).hide('slow',Core.destroyModalLayer);
	
}*/
/*
Core.destroyModalLayer = function()
{
	document.body.removeChild(Core.modalLayer);
	Core.modalLayer = null;
}*/

//обработчик меню 
$.fn.hoverClass = function(c) 
{
   return this.each(function(){
        $(this).hover( 
            function() { $(this).addClass(c);  },
            function() { $(this).removeClass(c); }
        );
    });
};    

//Начало календарных функций
// JS Календарь
var calendar = null; // глобальная переменная для календаря
// для того что бы не плодить экзэмпляры календаря

//Эта функция вызывается когда пользователь выбрал дату
function selected(cal, date) {
	cal.sel.value = date; // просто обновляем значение в поле с датой
}

// Эта функция вызывается когда пользователь выбирает дату
// или закрывает календарь. Здесь  просто скрываем календарь без его удаления
function closeHandler(cal) {
	cal.hide();			// скрыть календарь
	// Удалеяем обработчик события для документа, так как нет смысла
	//отлавливать события когда календарь скрыт
	Calendar.removeEvent(document, "mousedown", checkCalendar);
}

// Эта функция вызывается когда кнопка мыши нажата не на календаре
function checkCalendar(ev) {
	var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
	for (; el != null; el = el.parentNode)
	// FIXME: allow end-user to click some link without closing the
	// calendar.  Good to see real-time stylesheet change :)
	if (el == calendar.element || el.tagName == "A") break;
	if (el == null) {
		// calls closeHandler which should hide the calendar.
		calendar.callCloseHandler(); Calendar.stopEvent(ev);
	}
}

// This function shows the calendar under the element having the given id.
// It takes care of catching "mousedown" signals on document and hiding the
// calendar if the click was outside.
function showCalendar(id) {
	var el = document.getElementById(id);
	if (calendar != null) {
		// we already have one created, so just update it.
		calendar.hide();		// hide the existing calendar
		calendar.parseDate(el.value); // set it to a new date
	} else {
		// first-time call, create the calendar
		var cal = new Calendar(true, null, selected, closeHandler);
		calendar = cal;		// remember the calendar in the global
		cal.setRange(1930, 2070);	// min/max year allowed
		calendar.create();		// create a popup calendar
	}
	calendar.sel = el;		// inform it about the input field in use
	calendar.showAtElement(el);	// show the calendar next to the input field

	// catch mousedown on the document
	Calendar.addEvent(document, "mousedown", checkCalendar);
	return false;
}
//конец каендарных функций

//////////////////////////////////////////////

window.onload = function ()
{
	Core.initializeMenu();
	
}


