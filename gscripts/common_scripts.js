function changeBgColor(obj,color)
{
  obj.style.backgroundColor = color;
}
function cleanNode(node)
{
	while(node.firstChild)node.removeChild(node.firstChild);
}
//создаёт список из xml документа
//containerId - id элемента в котором будет отображаться список
//xmlDoc - xml документ
//ссылки на обработчики событий, при действиях над элементами списка
//если не нужны,то указать null
//onmouseOver
//onmouseOut
//onclick
//ondblclick
/*
function createListFromXml(containerObj,xmlDoc,onmouseOver,onmouseOut,onclick,ondblclick)
{
 try
 {
   var errors = xmlDoc.getElementsByTagName('error');
   if(errors.length > 0)
   {
  	var msg = '<center><ul class="error_text">';
  	for(i = 0; i < errors.length; i++)
  	{
       var item = errors[i];
       msg += '<li>'+item.firstChild.data+'</li>';
  	}
  	msg +="</ul></center>";
  	containerObj.innerHTML = msg;
   }
   else
   {
   	 //to build headers of list
   	 var headers = xmlDoc.getElementsByTagName('head');
   	 var table = document.createElement('table');
   	     table.className = 'listTable';
   	     var cs = document.createAttribute("cellspacing");
   	         cs.value = 0;
   	     table.setAttributeNode(cs);
   	     var cp = document.createAttribute("cellpadding");
   	         cp.value = 0;
   	     table.setAttributeNode(cp);

   	 var tbody = document.createElement('tbody');
   	 var hTr   = document.createElement('tr');
   	 var fTh   = document.createElement('th');
   	     fTh.appendChild(document.createTextNode(headers[0].firstChild.data));
   	     fTh.style.borderLeft = '2px solid silver';
   	     hTr.appendChild(fTh);
   	 for (var i = 1; i < headers.length; i++)
   	 {
       var th = document.createElement('th');
           th.appendChild(document.createTextNode(headers[i].firstChild.data));

       hTr.appendChild(th);
   	 }
   	 tbody.appendChild(hTr);
   	 //to build elments of list
     var items = xmlDoc.getElementsByTagName('item');
     for(var i = 0; i < items.length; i++)
     {
        var item = items[i];
        var first = item.getAttributeNode('first').value;
        var iTr  = document.createElement('tr');
        var fTd = document.createElement('td');
            fTd.innerHTML = first;
            iTr.appendChild(fTd);
            //getting values of fields
            for(var j = 0; j < item.childNodes.length; j++)
            {
            	var value = item.childNodes[j];
            	var td = document.createElement('td');

            	if(value.getAttributeNode('type').value=='text')
            	{
            		td.style.color = value.getAttributeNode('color').value;
            		td.appendChild(document.createTextNode(value.firstChild.data));
            	}
            	if(value.getAttributeNode('type').value=='link')
            	{
            	  var ev = value.getAttributeNode('onclick').value;
            	  td.innerHTML = '<a href="#" class="actionlink" onclick='+ev+'>'+value.firstChild.data+'</a>';
            	}

            	iTr.appendChild(td);
            }
         //to appoint event listeners
         iTr.onmouseover = function()
         {
         	changeBgColor(this,"#ededed");
         }
         iTr.onmouseout = function()
         {
         	changeBgColor(this,"white");
         }

       tbody.appendChild(iTr);
     }
   	 table.appendChild(tbody);
   	 containerObj.appendChild(table);
   }
  }
  catch(e)
  {
   containerObj.innerHTML = "<center><span class=error_text>Ошибка: "+e.message+"</span></center>";
  }
}
*/
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
//конец календарных функций
//проверят существует ли элемент по его id
function elementExists(id)
{
	if(document.getElementById(id))return true; else return false;
}
/*
//добавляет обработчики событий для всех кнопок
//на странице
function initializeButtons(onOver,onOut)
{
  var inputs = document.getElementsByTagName('input');
  for (var i = 0; i < inputs.length; i++)
  {
  	if( inputs[i].type == 'button' || inputs[i].type == 'submit')
  	{
  		inputs[i].onmouseover = function ()
  		{
  			onOver(this)
  		}
  		inputs[i].onmouseout = function ()
  		{
  			onOut(this);
  		}
  	}
  }
}
*/