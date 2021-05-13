
var SELECTED_DOC_TYPE=null;
var SELECTED_PERS = new Array();
var SELECTED_DOCS = new Array();

function onSelectDocType(obj)
{
   var sel = obj.selectedIndex;
   switch(sel)
   {
      case 0:
            if(SELECTED_DOC_TYPE!=null) ShowCloseDocForm(SELECTED_DOC_TYPE,1);
      break;
      case 1:
           if(SELECTED_DOC_TYPE!=null) ShowCloseDocForm(SELECTED_DOC_TYPE,1);

             SELECTED_DOC_TYPE = "order_div";
             ShowCloseDocForm("order_div",0);

      break;
      case 2:
            if(SELECTED_DOC_TYPE!=null) ShowCloseDocForm(SELECTED_DOC_TYPE,1);

             SELECTED_DOC_TYPE = "sanction_div";
             ShowCloseDocForm("sanction_div",0);
       break;
      case 3:
            if(SELECTED_DOC_TYPE!=null) ShowCloseDocForm(SELECTED_DOC_TYPE,1);

             SELECTED_DOC_TYPE = "opr_div";
             ShowCloseDocForm("opr_div",0);
       break;
	   
	// 12.04.2013 - списки документов	   
	case 4:
            if(SELECTED_DOC_TYPE!=null) ShowCloseDocForm(SELECTED_DOC_TYPE,1);

             SELECTED_DOC_TYPE = "search_div";
             ShowCloseDocForm("search_div",0);
       break;

      default:break;
   }
}
function ShowCloseDocForm(id,flag)
{
  var wnd = document.getElementById(id);
  if(flag==0)
      wnd.style.display = "block";
  else if(flag==1)
      wnd.style.display ="none";

}
//Функции создания документов

//Приказ
function CreateOrderDoc(f)
{
    SELECTED_DOCS = [];
    //валидация
	
    // начальная дата	
   if(f.date_order.value=="")
   {alert("Не указана дата.");f.order_doc.focus();return;}

    // конечная дата	
   if(f.date_order_end.value=="")
   {alert("Не указана дата.");f.order_doc.focus();return;}
   
   
   if(f.smena.value==0)
   {alert("Не указана смена.");f.smena.focus();return;}
   if(f.dopusk.value==0)
   {alert("Не указан допуск.");f.dopusk.focus();return;}
   if(f.zone.value==0)
   {alert("Не указана зона.");f.zone.focus();return;}
   if(f.code_ord.value==0)
    {
        alert("Не указан код.");
        f.code_ord.focus();
        return;
    }
   if(CheckString(f.desc_order.value)==1)
   {
     alert("Недопустимый символ при вводе описания");f.desc_order.focus();return;
   }
   var personal=GetPersonalId();
   if(personal==false)return;
     
    var data1 = {obj:'docs',
                act:'save',
                dtype:1,
                pid:personal,
                date:f.date_order.value,
                date_end:f.date_order_end.value,
                smena:f.smena.value,
                dopusk:f.dopusk.value,
                zone:f.zone.value,
                code:f.code_ord.value,
                desc:f.desc_order.value
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
            alert('error CreateOrderDoc');
        }
    });

}
function CreateSanctionDoc(f)
{
    SELECTED_DOCS = [];
    if(f.startdate_sanction.value=="")
    {alert("Не указана дата начала.");f.startdate_sanction.focus();return;}
    if(f.enddate_sanction.value=="")
    {alert("Не указана дата окончания.");f.enddate_sanction.focus();return;}

    if(f.zone.value==0)
    {alert("Не указана зона.");f.zone.focus();return;}
    if(CheckString(f.desc_sanction.value)==1)
    {
      alert("Недопустимый символ при вводе описания");f.desc_sanction.focus();return;
    }
    if(isTime(f.starttime_sanction.value) == false ||
       isTime(f.endtime_sanction.value) == false
      )
    {
         return;
    }

   var personal=GetPersonalId();
   if(personal==false)return;

    var data1 = {obj:'docs',
                act:'save',
                dtype:2,
                pid:personal,
                start_date:f.startdate_sanction.value,
                end_date:f.enddate_sanction.value,
                zone:f.zone.value,
                desc:f.desc_sanction.value,
                start_time:f.starttime_sanction.value,
                end_time:f.endtime_sanction.value
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
            alert('error CreateSanctionDoc');
        }
    });

}
function CreateOprDoc(fltr)
{
    SELECTED_DOCS = [];
    //валидация
    if(fltr.date_opr_st.value=="")
    {
	alert("Не указана дата.");
	fltr.date_opr_st.focus();
	return;
    }
    if(fltr.date_opr_end.value=="")
    {
	alert("Не указана дата.");
	fltr.date_opr_end.focus();
	return;
    }

    if(fltr.code.value==0)
    {
        alert("Не указан код.");
        fltr.code.focus();
        return;
    }
    if(CheckString(fltr.desc_opr.value)==1)
    {
        alert("Недопустимый символ при вводе обоснования");
        fltr.desc_opr.focus();return;
    }
   
    //// получить список отмеченных сотрудников   
    var personal=GetPersonalId();	// см. описание GetPersonalId()
    if(personal==false)
    return;

    var data1 = {obj:'docs',
                act:'save',
                dtype:3,
                pid:personal,
                date_st:fltr.date_opr_st.value,
                date_en:fltr.date_opr_end.value,
                code:fltr.code.value,
                desc:fltr.desc_opr.value
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
            alert('error CreateSanctionDoc');
        }
    });

}

//функция очистки форм
function ClearForm(f)
{
   var len=f.elements.length;
   
   for(var i=0;i<len;i++)
   {
     var item = f.elements[i];

     if(item.type!="undefined")
     {
       if(item.type=="text") item.value="";
     }
	 
     if(item.tagName=="SELECT" || item.tagName=="select") item.selectedIndex=item.firstChild;
     if(item.tagName=="TEXTAREA" || item.tagName=="textarea") item.value="";
   }
}
function ConfirmSaving(xdoc)
{
  var items = xdoc.getElementsByTagName('item');
  if(items.length==0)
  {
    alert("Ошибка:Невозможно сохранить документ\nТакой документ уже существует");
    return;
  }
  else
  {
    alert("Документ сохранён успешно");
    for(var i=0;i<items.length;i++)
    {
      var val = items[i].getAttributeNode("id").value;
      var text = items[i].getAttributeNode("desc").value;
      var item=createDiv("doc_list","doc"+val,"docitem","listitem");

      var flag = document.createElement("input");
          flag.type = "checkbox";
          flag.id = val;
          item.appendChild(flag);
          flag.onclick = function()
          {
             selectDocuments(this);
          }
      var t = document.createTextNode(text);

      item.appendChild(t);
    }
  }
}
function ConfirmRemove(xdoc)
{
   var error = xdoc.getElementsByTagName('error');
   if(error.length ==0 )
   {
      var cont = document.getElementById("doc_list");
      var items =  xdoc.getElementsByTagName('item');

      for(var i=0;i<items.length;i++)
      {
          var itemid = items[i].getAttributeNode("id").value;
          var item=document.getElementById("doc"+itemid);
          if(item)cont.removeChild(item);
      }
   }
   else
   {
     alert("Ошибка при удалении документов");
   }
}
function GetPersonalId()
{
    var persones="";
    var count=0;
    for(var i=0;i<SELECTED_PERS.length;i++)
    {
        if(SELECTED_PERS[i]!=0)
        {
            persones+=SELECTED_PERS[i]+";";
            count++;
        }
    }
    if(count!=0)
    {
        persones = persones.substring(0,persones.length-1);
        return persones;
    }
    else
    {
        alert("Не выбрано ни одного сотрудника");
        return false;
    }
}
function GetDocumentId()
{
  var documents="";
  var count=0;
  for(var i=0;i<SELECTED_DOCS.length;i++)
  {
     if(SELECTED_DOCS[i]!=0)
     {
       documents+=SELECTED_DOCS[i]+";";
       count++;
     }
  }
  if(count!=0)
  {
   documents = documents.substring(0,documents.length-1);
   return documents;
  }
  else
  {
    alert("Не выбрано ни одного документа");
    return false;
  }
}
function ExecuteActionDoc(id)
{
 try
 {
    var docs="";
    var act = document.getElementById(id).value;
    //alert(act);
    if(act==0){alert("Не выбрано действие");return;}
    docs = GetDocumentId();
    if(docs==false)return;
    if(act==1)
    {
     var url="aresponse.php?obj=docs&act=show";
     var param = "&docs="+docs;
	 ShowWindow(url+param,"Документы-просмотр",700,500,"1");
    }
    if(act==2)
    {
     var url="aresponse.php?obj=docs&act=del";
     var param = "&docs="+docs;
     loadXMLDoc(url,param);
    }
 }
 catch(e)
 {alert("Ошибка: Возможно не найден объект");}
}
