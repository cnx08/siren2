var Window = new Object();

Window.currentPopup = null;
Window.popupReady = null;
Window.currentRequest = null;

Window.poupWindow = function(id,top,left,dX,dY,width,height,className,headText)
{
   this.id = id;
   this.width = width;
   this.height = height;
   this.className = className;
   this.posX = top*1;
   this.posY = left*1;
   this.dX = dX*1;
   this.dY = dY*1;
   this.headText = headText;
   this.wnd = null;
   
//   alert('PosX='+this.posX + '; PosY = '+this.posY+'; dX = '+this.dX+'; dY = '+this.dY);

}
Window.poupWindow.prototype = {
//отображает окно. После вызова этого метода. Всем элементам окна можно
//задавать стили и классы.
Show : function ()
{
  //создаём каркас окна

  this.wnd = document.createElement('DIV');
  this.wnd.id = this.id;
  this.wnd.style.position = "absolute";
  this.wnd.style.top = this.posX + this.dY + 'px';
  this.wnd.style.left = this.posY + this.dX + 'px';
  this.wnd.style.width = this.width + 'px';
  this.wnd.style.height = this.height + 'px';
  this.wnd.className = this.className;

// alert('div.TOP = '+this.wnd.style.top+'; div.LEFT = '+this.wnd.style.left+'; div.WIDTH = '
// +this.wnd.style.width+'; div.HEIGHT = '+this.wnd.style.height);  
  
 if(Window.currentPopup == null)
 {
   document.body.appendChild(this.wnd);
   Window.currentPopup = this.id;
 }
 else
 {
          var old = document.getElementById(Window.currentPopup);
          document.body.removeChild(old);
          document.body.appendChild(this.wnd);
          Window.currentPopup = this.id;
 }

  //creating main table
  var tab = document.createElement('table');
      tab.border = 0;
      tab.width = "100%";
  var cellpadding  = document.createAttribute('cellpadding'); cellpadding.value = "0";
  var cellspacing  =  document.createAttribute('cellspacing');cellspacing.value = "0";
       tab.setAttributeNode(cellpadding);
       tab.setAttributeNode(cellspacing);

  var tbody = document.createElement('tbody');
      tab.appendChild(tbody);
 //создаём заголовок
 var tr = document.createElement('tr');
 var tdName = document.createElement('td');//tdName.width="90%";
 var tdIco = document.createElement('td'); tdIco.width="14";
     tr.appendChild(tdName);tr.appendChild(tdIco);
 //создаём название
 tdName.appendChild(document.createTextNode(this.headText));
 tdName.className = 'headWnd';
 tdIco.className = 'headWnd';
 tdIco.align = "right";
 //смоздаём иконку
 var close_ico = document.createElement('IMG');
      close_ico.src = 'buttons/crossline.gif';
      close_ico.className = 'iconSmall';
      close_ico.wnd = this;
      close_ico.onclick = function()
      {
      	this.wnd.Close();
      }
 tdIco.appendChild(close_ico);
 tbody.appendChild(tr);
 tr = document.createElement('tr');
 var td = document.createElement('td');
 var colspan = document.createAttribute('colspan');
 colspan.value = "2";
 td.setAttributeNode(colspan);
 td.className='client';
 tr.appendChild(td);

 //создаём ссылку на клиентскую часть
 this.wnd.client = td;
 tbody.appendChild(tr);
 this.wnd.appendChild(tab);

},
Close : function()
{
  document.body.removeChild(this.wnd);
  Window.currentPopup = null;
},

FillClient : function(content)
{

},

CleanClient : function()
{
 while (this.wnd.client.firstChild)
    dest.removeChild(this.wnd.client.firstChild);
}
}
