
$(function () {
            $("#f_start_doc_date").pickmeup({
                change : function (val) {
                    $("#f_start_doc_date").val(val).pickmeup("hide")
                }
            });
            $("#f_end_doc_date").pickmeup({
                change : function (val) {
                    $("#f_end_doc_date").val(val).pickmeup("hide")
                }
            });
             $("#date_order").pickmeup({
                change : function (val) {
                    $("#date_order").val(val).pickmeup("hide")
                }
            });
            $("#date_order_end").pickmeup({
                change : function (val) {
                    $("#date_order_end").val(val).pickmeup("hide")
                }
            });
             $("#startdate_sanction").pickmeup({
                change : function (val) {
                    $("#startdate_sanction").val(val).pickmeup("hide")
                }
            });
            $("#enddate_sanction").pickmeup({
                change : function (val) {
                    $("#enddate_sanction").val(val).pickmeup("hide")
                }
            });
             $("#date_opr_st").pickmeup({
                change : function (val) {
                    $("#date_opr_st").val(val).pickmeup("hide")
                }
            });
            $("#date_opr_end").pickmeup({
                change : function (val) {
                    $("#date_opr_end").val(val).pickmeup("hide")
                }
            });
            $("#startdt_search").pickmeup({
                change : function (val) {
                    $("#startdt_search").val(val).pickmeup("hide")
                }
            });
            $("#enddt_search").pickmeup({
                change : function (val) {
                    $("#enddt_search").val(val).pickmeup("hide")
                }
            });
           
        });
function getCurDateStr()
{
///// формируем строку текущей даты в формате dd.mm.yyyy
	var CurrentDate = new Date();		
	var curday;
	if (CurrentDate.getDate()<10)
	{
            curday = '0'+CurrentDate.getDate();
	}
	else
	{
            curday = CurrentDate.getDate();
	}
	
////// нумерация месяцев начинается с 0!!!!!	
	var curmonth;

	if ((CurrentDate.getMonth()+1)<10)
	{
		curmonth = '0'+(CurrentDate.getMonth()+1);
	}
	else
	{
		curmonth = (CurrentDate.getMonth()+1);
	}
	var curyear = CurrentDate.getFullYear();		
	var cur_date = curday+'.'+curmonth+'.'+curyear;
	
return cur_date;
}

function getCurTimeStr()
{
    var CurrentDate = new Date();			
    var curtm = CurrentDate.toLocaleTimeString();
    return curtm;
}

function getCurTimeShortStr()
{
    var CurrentDate = new Date();	
    var curtm = CurrentDate.getHours()+':'+CurrentDate.getMinutes();
    return curtm;
}

function ShowIndicator(owner,text,left,top)
{
   var o=document.getElementById(owner);
   var cont = document.createElement("div");
       cont.id="indicator";
       cont.style.position="relative";
       cont.style.left=left+"%";
       cont.style.top=top+"%";
       cont.style.width=40+"%";
       cont.style.height=0+"%";
       cont.style.fontFamily="verdana";
       cont.style.fontSize=10+"px";
       cont.style.fontWeight="bold";
       cont.style.margin = 2 +"px";
   o.appendChild(cont);

   var img=document.createElement("img");
       img.src="images/indicator.gif";
       img.style.marginRight=10+"px";
       img.style.width=15+"px";
       img.style.height=15+"px";

   cont.appendChild(img);
   var t=document.createTextNode(text);
       cont.appendChild(t);

}
function RemoveIndicator(owner,indicator)
{
  var o=document.getElementById(owner);
  if(!o)return;
  var i=document.getElementById(indicator);
  if(!i)return;
      o.removeChild(i);
}

function RemoveListSelect(owner,id_list)
{
  var o=document.getElementById(owner);
  if(!o) return;
  
  var l=document.getElementById(id_list);
  
  if(!l)return;
      o.removeChild(l);
}

function ClearFilter(f)
{
  f.tab_num.value="";
  f.family.value="";
  f.fname.value="";
  f.secname.value="";
  f.position.value="";
  f.depart.selectedIndex=0;
  f.graph.selectedIndex=0;
  //f.f_doc_date.value="";
  f.f_start_doc_date.value="";
  f.f_end_doc_date.value="";
  
  f.f_doctype.selectedIndex=0;	// 1й элемент в списке
}


function ClearAll(doc)
{

	ClearFilter(doc.filtrfrm);
	
///// форма заведения приказа	
	var order_frm = document.getElementById("order_doc");
///// форма разреш
	var sanct_frm = document.getElementById("sanction_doc");
///// форма оправдат	
	var opr_frm = document.getElementById("opr_doc");
///// форма поиска 	
	var s_frm = document.getElementById("search_doc");	
	
	
	
//// текущая дата 	
	var d_now = getCurDateStr();
	var tm_now = getCurTimeStr();

//// форма создания приказа	
	ClearForm(document.order_doc);
 //// поля даты - установить значения по умолчанию 
	order_frm.date_order.value = d_now;
	order_frm.date_order_end.value = d_now; 
 
  
//// форма создания разреш документа
    ClearForm(document.sanction_doc);
    sanct_frm.startdate_sanction.value = d_now;
    sanct_frm.enddate_sanction.value = d_now;

    sanct_frm.starttime_sanction.value = getCurTimeShortStr();//tm_now;
    sanct_frm.endtime_sanction.value = "23:59";
    sanct_frm.desc_sanction.value = "";
    sanct_frm.zone.selectedIndex=0;

    //// форма создания оправдат документа
    ClearForm(document.opr_doc)	;

    //// форма поиска документов 	
    ClearForm(document.search_doc);

    s_frm.startdt_search.value=d_now;
    s_frm.enddt_search.value=d_now;
    s_frm.search_doctype.selectedIndex=0;
	
	
	
    ////// -- ВАЖНО --	
    ////// программно выполняем метод визуального компонента
    var dt_dt = document.getElementById("dtyp");	// находим на странице
    dt_dt.selectedIndex = 0;					// изменяем программно значение
    document.getElementById("dtyp").onchange();	// выполняем код метода по onChange

    //////// изменить выбранный элемент списка	
    var da = document.getElementById("docaction");	
            da.selectedIndex = 0;
    ////////// очистить список сотрудников
    clearPersList("pers_list"); 
    ////////// очистить список документов
    clearDocList("doc_list");
  
}

function onFilterTypeSelect(obj)
{
   var type = obj.value;
   var f = document.getElementById("filtrfrm");
   if(!f){alert("Ошибка:Не найден фильтр!!!!");return;}

   if(type == "document")
   {
     //f.f_doc_date.disabled=0;
     //f.date_but.disabled=0;
     //f.f_doc_date.style.backgroundColor="white";
     f.f_start_doc_date.disabled=0;
     f.f_start_doc_date.style.backgroundColor="white";
     f.f_end_doc_date.disabled=0;
     f.f_end_doc_date.style.backgroundColor="white";
     //f.s_date_but.disabled=0;
     //f.e_date_but.disabled=0;
     f.f_doctype.disabled=0;
     f.graph.disabled=1;
   }
   if(type == "personal")
   {

     //f.f_doc_date.disabled=1;
     //f.date_but.disabled=1;
     //f.s_date_but.disabled=1;
     //f.e_date_but.disabled=1;
     //f.f_doc_date.style.backgroundColor="#F5F5F5";
     f.f_start_doc_date.disabled=1;
     f.f_start_doc_date.style.backgroundColor="#F5F5F5";
     f.f_end_doc_date.disabled=1;
     f.f_end_doc_date.style.backgroundColor="#F5F5F5";
     f.f_doctype.disabled=1;
     f.graph.disabled=0;
   }

}
function cleanNode(dest)
{
  while (dest.firstChild)
    dest.removeChild(dest.firstChild);
}
function isContein(val,arr)
{
  var flag=0;
  for(var i=0;i<arr.length;i++)
  {
     if(arr[i]==val){flag=1;break;}
  }
  if(flag==1)return true;else return false;
}

function selectPersonal(obj)
{
  try
  {
   var count = document.getElementById("pers_count");
   if(obj.checked == 1)
   {
     if(isContein(obj.id,SELECTED_PERS)==false)
     {
        SELECTED_PERS.push(obj.id);
        count.value = parseInt(count.value)+1;
     }
   }
   else
   {
     if(isContein(obj.id,SELECTED_PERS)==true)//удаляем элемент из массива
     {
        for(var i=0;i<SELECTED_PERS.length;i++)
           if(SELECTED_PERS[i]==obj.id)SELECTED_PERS[i]=0;
       count.value = parseInt(count.value)-1;
     }
   }
  }
   catch(e){}

}
function selectAllPersonal(flag)
{
    var parent = document.getElementById("pers_list");
    var children_len = parent.childNodes.length;
    var flg = 0;

    if(flag.checked == true) flg = 1;else flg = 0;

    for(var i = 1; i < children_len; i++)
    {
        var items = parent.childNodes[i];
        var checkbox = items.firstChild;
        checkbox.checked = flg;
        selectPersonal(checkbox);
    }
}
function selectDocuments(obj)
{
  try
  {
   if(obj.checked == 1)
   {
     if(isContein(obj.id,SELECTED_DOCS)==false)
     {
        SELECTED_DOCS.push(obj.id);
     }
   }
   else
   {
     if(isContein(obj.id,SELECTED_DOCS)==true)//удаляем элемент из массива
     {
        for(var i=0;i<SELECTED_DOCS.length;i++)
           if(SELECTED_DOCS[i]==obj.id)SELECTED_DOCS[i]=0;
     }
   }
  }
   catch(e){}

}

function clearPersList(f)
{

    var div = document.getElementById(f);
    var elems = div.getElementsByTagName('div');

    while (elems[0])
    {
        elems[0].parentNode.removeChild(elems[0]);
    }
}


function clearDocList(f)
{
    var div = document.getElementById(f);
    var d_elems = div.getElementsByTagName('div');

    while (d_elems[0])
    {
        d_elems[0].parentNode.removeChild(d_elems[0]);	// удаляем первый из списка
    }
}

function onFilter2(f)
{
    var obj = '';
    var act = '';
    var ftype = "";
    if(f.graph.disabled==true)ftype="document";else ftype="personal";

    if(isNaN(f.tab_num.value)==true)
    {
        alert("Табельный номер должен быть числом");f.tab_num.value="";
        f.tab_num.focus();return;
    }
    for (var i = 0; i < f.elements.length; i++ )
    {
        var item = f.elements[i];
        if(item.type == "text")
        {
           if(CheckString(item.value)==1)
           {alert("Недопустимый символ при вводе");item.focus();return;}
        }
    }

    if(ftype === "personal")
    {
        //очищаем массив выбранных сотрудников
        var count = document.getElementById("pers_count");
        SELECTED_PERS.clear();
        count.value = 0;

        $("#pers_list").children().remove();
        if(document.getElementById("pers_list_select")) RemoveListSelect("pers_list","pers_list_select");
        ShowIndicator("pers_list","Построение списка...",-30,50);
        obj = "pers";
        act = "filter2";
    }
    if(ftype==="document")
    {
        SELECTED_DOCS = []; //выделенные для просмотра документы удаляем из массива
        $("#doc_list").children().remove();
        ShowIndicator("doc_list","Построение списка...",-30,50);
        obj = "docs";
        act = "docfilter2";
    }
    var data1 = {obj:obj,
                act:act,
                doc_date:'',
                f_start_doc_date:f.f_start_doc_date.value,
                f_end_doc_date:f.f_end_doc_date.value,
                dtype:f.f_doctype.value,
                tnum:f.tab_num.value,
                family:f.family.value,
                fname:f.fname.value,
                secname:f.secname.value,
                position:f.position.value,
                depart:f.depart.value,
                graph:f.graph.value
            };
    $.ajax({
        url: "aresponse.php", 
        type: "POST",
        data: data1,
        dataType: "json",
        success: function (data) {
            if(obj==="pers"){
                RemoveIndicator("pers_list","indicator");
                var i= 0;
                for ( var x in data) i++;

                if(i===0)
                {
                    alert("Не найдено ни одного сотрудника");
                }
                else
                {
                    //флажок для выделения всех сотрудников
                    var item=createDiv("pers_list","mainflag","persitem","listitem");
                    $('<input>', {
                            type: "checkbox",
                            click: function(){selectAllPersonal(this);}
                        }).appendTo(item);
                        $(item).append("Выделить всех");
                  
                    $.each(data,function(key, value) {
                        var item=createDiv("pers_list","mainflag","persitem","listitem");
                        $('<input>', {
                            id:  key.substr(2),
                            type: "checkbox",
                            click: function(){selectPersonal(this);}
                        }).appendTo(item);
                        $(item).append(value);
                    });
                }
            }
            if(obj==="docs"){
                RemoveIndicator("doc_list","indicator");
                var i= 0;
                for ( var x in data) i++;

                if(i===0)
                {
                  alert("Не найдено ни одного документа");
                }
                else
                {
                    $.each(data,function(key, value) {
                        var item=createDiv("doc_list","doc"+key,"docitem","listitem");
                        $('<input>', {
                            id:  key,
                            type: "checkbox",
                            click: function(){selectDocuments(this);}
                        }).appendTo(item);
                        $(item).append(value);
                    });
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error onFilter2');
        }
    });

 
}
function onFilter(f)//поиск доков по нескольким сотрудникам
{
    SELECTED_DOCS = []; //выделенные для просмотра документы удаляем из массива
    var personal=GetPersonalId();	// см. описание GetPersonalId()
    if(personal==false) return;
    $("#doc_list").children().remove();
    ShowIndicator("doc_list","Построение списка...",-30,50);
    var data1 = {obj:"docs",
                act:"docfilter",
                pid:personal,
                dt_start:f.startdt_search.value,
                dt_end:f.enddt_search.value,
                dtype:f.search_doctype.value
            };
    $.ajax({
        url: "aresponse.php", 
        type: "POST",
        data: data1,
        dataType: "json",
        success: function (data) {
            RemoveIndicator("doc_list","indicator");
            var i= 0;
            for ( var x in data) i++;

            if(i===0)
            {
                alert("Не найдено ни одного документа");
            }
            else
            {
                $.each(data,function(key, value) {
                    var item=createDiv("doc_list","doc"+key,"docitem","listitem");
                    $('<input>', {
                        id:  key,
                        type: "checkbox",
                        click: function(){selectDocuments(this);}
                    }).appendTo(item);
                    $(item).append(value);
                });
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error onFilter2');
        }
    });

 
}