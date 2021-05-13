//replaced 15.06.07
//Basic object for asynchronous requests. File version 1.0.
var Net = new Object();
//State of the http request
Net.READY_STATE_UNINITIALIZED = 0;
Net.READY_STATE_LOADING = 1;
Net.READY_STATE_LOADED = 2;
Net.READY_STATE_INTERACTIVE = 3;
Net.READY_STATE_COMPLETE = 4;

//Constructor

Net.ContentLoader = function(url,onload,onerror,method,params,contentType)
{
    this.url = url;
    this.req = null;

    this.onload = onload;
    this.onerror = (onerror) ? onerror : this.defaultError;
    this.loadXMLDoc(url,method,params,contentType);
    this.object = null;
}

Net.ContentLoader.prototype = {

Dispatcher : null, // ссылка на диспетчер запросов
Parser : null, // ссылка на парсер

onReadyState : function()
{
 var req = this.req;
 var ready = req.readyState;
 if( ready == Net.READY_STATE_COMPLETE )
 {
   var httpStatus = req.status;
   if ( httpStatus == 200 || httpStatus == 0 )
     this.onload.call(this);
   else
     this.onerror.call(this);
 }
},//onReadyState

loadXMLDoc : function(url,method,params,contentType)
{
  if(!method)method = 'GET';
  if(!contentType && method=='POST')contentType = 'application/x-www-form-urlencoded';

   // alert(contentType);
  if (window.XMLHttpRequest)
    this.req = new XMLHttpRequest();
  else if(window.ActiveXObject)
    this.req = new ActiveXObject("Microsoft.XMLHTTP");

 if ( this.req )
 {
 try
  {
   var loader = this;
   this.req.onreadystatechange = function (){ loader.onReadyState.call(loader);}
   this.req.open(method,url,true);

   if(contentType)this.req.setRequestHeader('Content-Type',contentType);

   this.req.send(params);
 }
  catch(err)
  {
  	 this.onerror.call(this,err);
  }
 }

 }
}//prototype
//Диспетчер запросов
Net.RequestDispatcher = function()
{
	this.Properties =
	{
		Id  : null,    // идентификатор
		//MaxLength : 20 //максимальная длина очереди запросов
		StatusPanel: null //ссылка на строку состояния, для отображения хода выполнения запросов
    }
	this.Queue = new Array(); //очередь запросов
	this.Sent = new Array();//массив для отосланных команд
	this.State = 0; // 0 - свободен, 1 - в состоянии отсылки

}
Net.RequestDispatcher.prototype =
{
	 // добавить запрос если такой не существует
	push : function (request)
	{
        if (this.getRequestById(request.Properties.Id,this.Sent) == null)
        {
          this.Queue.append(request);
          this.send();
        }
	},
	//посылка запросов
	send : function ()
	{
	  this.State = 1;
	  var len = this.Queue.length;
	  while ( len != 0 )
	  {
	   	var request = this.Queue[0];
	   	if (request.Properties.Id != null && request.Properties.Url != null && request.Properties.Obj != null)
	   	{
	   		//Нужно ли отображать индикатор в объекте
	   		var reqInd = null;
	   		if(request.Properties.IndicateInObj)
	   		{

	   			// от дурака, что бы небыло одновременно индикатора на панели и в объекте
	   			request.Properties.IndicateInPanel = false;
	   			cleanNode(request.Properties.Obj);
	   			//очищаем объект.
	   			reqInd = new Net.SimpleIndicator("loadProtocolIndicator","images/indicators/32.gif",request.Properties.Obj);
                reqInd.create();
			 	reqInd.setTextString(request.Properties.Description);
 				reqInd.setPosition(40,40,"%");
	   		}
	   		//в строке состояния
            if(request.Properties.IndicateInPanel && this.Properties.StatusPanel !=null)
            {
            	request.Properties.IndicateInObj = false;
            	cleanNode(request.Properties.Obj);
	   			reqInd = new Net.SimpleIndicator("ind_"+request.Properties.Id,"images/indicators/indicator.gif",this.Properties.StatusPanel);
                reqInd.OnPanel = true;
                reqInd.OnPanelCssClass = 'statusBarItems';
                reqInd.create();
			 	reqInd.setTextString(request.Properties.Description);

 		     }
 		    //Присваиваем объекту запроса ссылку на индикатор
 		    request.Indicator = reqInd;
 		    reqInd = null;
 		   //определяем какой обработчик вызывать при ответе
 		    var listener = this.onResponse;
 		    if(request.UserListener != null) listener = request.UserListener
	   		var req = new Net.ContentLoader(request.Properties.Url,listener,this.onError,'POST',request.Properties.Parameters);
	   		//добавляем запрос в массив отосланных
	   		this.Sent.append(request);
	   		//Удаляем его из очереди
	   		this.Queue.remove(request);
	   		len = this.Queue.length;
	   	}
	   	else
	   	{
           //так ка нет полных данных в запросе, то отбрасываем его
           this.Queue.remove(request);
	   	   len = this.Queue.length;
	   	}

	  }//while
      this.State = 0;
	},
	//получение объекта запроса по id из указанного массива
	getRequestById : function (id,arr)
	{
	  var len = arr.length;
	  var i = 0;
	  for (i; i < len; i++ )
	  {
	  	var item = arr[i];
	  	if ( item.Properties.Id == id )
	  	  return item;
	  }
	  return null;
	},
	//встроенный обработчик ответов, вызывается если не указан пользовательский.
	onResponse : function ()
	{
		alert(this.req.responseText);

		//1 пытаемся получить заголовок запроса
		var respHead = null;
		try
		{
			var xDoc = this.req.responseXML;
			respHead = xDoc.getElementsByTagName('request')[0];
			//alert(respHead.length);
		}
		catch (e)
		{
			alert("Ошибка: Некорректный ответ сервера\n"+e.message);
		}
		if(respHead != null)
		{
            //получаем данные из заголовка
            var rId = respHead.getAttributeNode('id').value;
            var rStatus = respHead.getAttributeNode('status').value;
            var returnData = respHead.getAttributeNode('returnData').value;
            var dataType = respHead.getAttributeNode('dataType').value;
            //забираем запрос из массива отосланых
            var r = this.Dispatcher.getRequestById(rId,this.Dispatcher.Sent);
                r.Indicator.setTextString('Обработка ответа...');
            this.Dispatcher.Sent.remove(r);
            //получаем ссылку на
            //определяем как выполнился запрос
            if(rStatus == 0)
            {
               //Произошла ошибка в запросе
               var xError = null;
               try
               {
               	xError = xDoc.getElementsByTagName('error')[0];
               }
               catch(e)
               { }
               if(xError!=null)
               {
               	 this.Parser.parseError(r,xError);
               }
            }//if щбработки ошибок
            else if (rStatus == 1)
            {
              if(returnData == 1) //вызываем парсер данных
              {
                var xData = xDoc.getElementsByTagName('data')[0];
                this.Parser.parseData(r,dataType,xData);
              }
            }
		}
		else
		{
			alert("Ошибка: Некорректный ответ сервера.\nЗаголовок ответа не найден");
		}
    },
	onError : function()
	{
		alert(this.req.statusText);
	}
}

//запрос
Net.Request = function(id,url,params,obj)
{
  this.Properties =
  {
  	Id : id,
  	Url : url,
  	Parameters : params,
  	Obj : obj,  //объект, в котором нужно отобразить результаты. либо сообщение об ошибке.
  	Description : '',
   	IndicateInObj : false,
    IndicateInPanel : false,
    UserListener : null  //для пользовательских вариантов обработки ответа
  };
  this.Indicator = null;// ссылка на индикатор

}
//парсер ответов, для представления ответов
Net.ResponseParser = function()
{   /*//если нужно переопределить обработчики данных
	this.Parsers =
	{
		Error   : null;
		Message : null;
	}    */
}
Net.ResponseParser.prototype =
{
	parseData : function (req,dataType,xData)
	{
		req.Indicator.setTextString('Обработка данных...');
		req.Indicator.removeView();
		req.Indicator = null;
        switch (dataType)
        {
        	case 'tablelist' :
        		if(xData.firstChild.tagName == 'tablelist')
        	    	this.createTableList(req.Properties.Obj,xData.firstChild);
        	break;
        	default: req.Properties.Obj.appendChild(document.createTextNode('Undefined data type'));break;
        }
        delete req;
	},
	parseError : function (req,xError)
	{
	  req.Indicator.setTextString('Ошибка...');
	},
	parseMessage : function (req,xMessage)
	{
	  req.Indicator.setTextString('Сообщение...');
	},
	//обработчики данных
	createTableList : function (parent,xData)
	{
      var list_name = xData.getAttributeNode('id').value;
      var className = xData.getAttributeNode('style').value;

   	  var headers = xData.getElementsByTagName('head');
   	  var table = document.createElement('table');
   	      table.id = list_name;
   	      table.className = className;
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
      var items = xData.getElementsByTagName('item');
      for(var i = 0; i < items.length; i++)
      {
        var item = items[i];
        var item_id = item.getAttributeNode('id').value;
        var iTr  = document.createElement('tr');
            iTr.id = item_id;

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
      parent.appendChild(table);
	},
    listItem : function (items)
    {

    }
}
//простой индикатор для отображения процесса загрузки
Net.SimpleIndicator = function (id,imsrc,parent)
{
 this.Id = id;
 this.Parent = parent;
 this.ImageSrc = imsrc;
 this.Image = null;
 this.Text =
 {
  	Family : 'Tahoma',
  	Size   : '12px',
  	Weight : ''
 }
 this.OnPanel = false;
 this.OnPanelCssClass = '';
 this.textString = null;
 this.Table = null;
}
Net.SimpleIndicator.prototype =
{
	create : function()
	{
        //создаём таблицу индикатора
        this.Table = document.createElement('table');

        this.Table.id = this.Id;
        this.Table.border = "0";
        var  cs = document.createAttribute("cellspacing");
   	         cs.value = 0;
   	    this.Table.setAttributeNode(cs);
   	    var  cp = document.createAttribute("cellpadding");
   	         cp.value = 0;
   	    this.Table.setAttributeNode(cp);
        this.Table.style.fontFamily = this.Text.Family;
        this.Table.style.fontSize = this.Text.Size;
        this.Table.style.fontWeight = this.Text.Weight;
        var  tbody = document.createElement('tbody');
        this.Table.appendChild(tbody);
        var Tr = document.createElement('tr');
        var imgTd = document.createElement('td');
        var txtTd = document.createElement('td');
        this.Image = document.createElement('img');
        this.Image.src = this.ImageSrc;
            imgTd.appendChild(this.Image);

            txtTd.appendChild(document.createTextNode(this.textString));
        this.textString = txtTd;
        Tr.appendChild(imgTd);
        Tr.appendChild(txtTd);
        tbody.appendChild(Tr);

        if(this.Parent != null)
        {
			if(this.OnPanel)
			{
				var frame = document.createElement('div');
				frame.className = this.OnPanelCssClass;
				frame.appendChild(this.Table);
				this.Parent.appendChild(frame);
			}
			else
			{
			  this.Parent.appendChild(this.Table);
			  this.Table.style.position = 'relative';
			}
		}
		else
		{
			document.body.appendChild(this.Table);
			this.Table.style.position = 'absolute';
		}
	},
	removeView : function ()
	{
       if(this.Parent != null)
	   {
		  if(this.OnPanel)
		     this.Parent.removeChild(this.Table.parentNode);
		   else
		     this.Parent.removeChild(this.Table);
	   }
	   else
	   {
		  document.body.removeChild(this.Table);
	   }
	},
	changeImage : function (imgSrc)
	{
		this.Image.src = imgSrc;
    },
	setTextString : function (text)
	{
      if(this.textString)
      	this.textString.replaceChild(document.createTextNode(text),this.textString.firstChild);
	},
	setPosition : function (x,y,dim)
	{
        this.Table.style.top = parseInt(y) + dim;
        this.Table.style.left = parseInt(x) + dim;
	}

}



