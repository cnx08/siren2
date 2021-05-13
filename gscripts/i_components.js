function Indicator(id)
{
  this.id = id;
  this.MinValue = 0;
  this.MaxValue = 0;
  this.Width = 0;
  this.Height = 0;
  this.Parent = null;
  this.HeadString = null;
  this.ProcessString = null;
  this.Border = '1px solid black';
  this.Color = 'silver';
  this.Text =
  {
  	Family : 'Tahoma',
  	Size   : '12px',
  	Weight : ''
  }
  this.Step = 10;
  this.Progress = 0;
  this.Table = null;
  this.Indicator = null;

  //events listener
  this.onCancel = null;

}
Indicator.prototype =
{
	create : function()
	{
        //устанавливаем максимальное значение
        this.MaxValue = this.Width;
		//создаём таблицу индикатора
        this.Table = document.createElement('table');
        this.Table.id = this.id;
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

        this.Table.style.width = parseInt(this.Width) + 'px';

        var  tbody = document.createElement('tbody');
        this.Table.appendChild(tbody);
        //если назначена строка заголовка то создаём её
        if(this.HeadString == true)
        {
        	var hsTr = document.createElement('tr');
        	var hsTd = document.createElement('td');
        	    hsTr.appendChild(hsTd);
        	    tbody.appendChild(hsTr);
        	this.HeadString = hsTd;
        }
         //если назначена строка процесса то создаём её
        if(this.ProcessString == true)
        {
        	var psTr = document.createElement('tr');
        	var psTd = document.createElement('td');
        	    psTr.appendChild(psTd);
        	    tbody.appendChild(psTr);
        	this.ProcessString = psTd;
        }
        //создаём строку таблицы под индикатор
        var indTr = document.createElement('tr');
        var indTd = document.createElement('td');
            indTr.appendChild(indTd);
            tbody.appendChild(indTr);
        //создаём дивки индикатора
        //1.оболочка
        var indFrame = document.createElement('div');
            indFrame.style.width = parseInt(this.Width) + 'px';
            indFrame.style.height = parseInt(this.Height) + 'px';
            indFrame.style.border = this.Border;
            indTd.appendChild(indFrame);
        //2.индикатор
        var ind = document.createElement('div');
            ind.style.width = 0 + 'px';
            ind.style.height = parseInt(this.Height) + 'px';
            ind.style.backgroundColor = this.Color;
        this.Indicator = ind;
            indFrame.appendChild(ind);
        if(this.Parent != null)
			this.Parent.appendChild(this.Table);
		else
			document.body.appendChild(this.Table);
	},
	setHeadText : function(text)
	{
 		if(this.HeadString != null)
 			this.HeadString.appendChild(document.createTextNode(text));
	},
	setProcessText : function(text)
	{
 		if(this.ProcessString != null)
 			this.ProcessString.appendChild(document.createTextNode(text));
	},
	addProgress : function (value)
	{
		if(this.Indicator!=null)
		{
		  var w = this.getIndicatorWidth();
		  if((w+value)< this.MaxValue)
		   this.Indicator.style.width = parseInt(w)+parseInt(value) + 'px';
		  else
		   this.Indicator.style.width = this.MaxValue + 'px';
		}
	},
	getIndicatorWidth : function()
	{
      var obj = this.Indicator;
      var width = obj.offsetWidth;
	  if (width > 0)
			return width;
	  if (!obj.firstChild)
		  return 0;
      return obj.lastChild.offsetLeft - obj.firstChild.offsetLeft + cmGetWidth (obj.lastChild);
	},
	clear : function ()
	{
		this.Indicator.style.width = 0 + 'px';
	}
}

function SimpleIndicator(id,imsrc,parent)
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
 this.textString = null;
 this.Table = null;
}
SimpleIndicator.prototype =
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
			this.Parent.appendChild(this.Table);
			this.Table.style.position = 'relative';
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
			this.Parent.removeChild(this.Table);
		else
			document.body.removeChild(this.Table);
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
