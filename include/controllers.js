//Модуль функций

//обработчик onclick для поднятия всех
// флажков отфильтрованного списка
//персонала
function CheckAll()
{
  var mainflag=document.getElementById("mainflag");
  var flag=0;
  var ss="";

  if(mainflag.checked==1) flag=1;else flag=0;

   for (var i = 0; i < document.forms['filterfrm'].elements.length; i++)
    {
       var item = document.forms['filterfrm'].elements[i];
             if (item.name == "check")  {
                     item.checked = flag;
                    ss=ss+item.id;ss+=",";

                 };
         }
  ss=ss.substr(0,ss.length-1);
  if(flag==1)document.filterfrm.selectstring.value=ss;else document.filterfrm.selectstring.value="";


}
//Обработчик выбора сотрудника
// из отфильтрованного списка
// добавляет id или удаляет сотрудика к строке selectstr
function  SetSelectPers(id)
{

   var flag;
   var el=document.getElementById(id);
   var ss=document.filterfrm.selectstring.value;
   var mainflag=document.getElementById("mainflag");
   mainflag.checked=false;
   if(el.checked==true)flag=1;else flag=0;
   if(flag==1)
   {
     if(ss!=""){ss+=",";ss+=el.id;}else{ss+=el.id;}
   }
   else
   {
    ss=DelSelId(ss,id);
   }

   document.filterfrm.selectstring.value=ss;

}
//вспомогательный функции для SetSelectPers
//удаление id из selectstr
function DelSelId(str,id)
{

  var strArray=new Array();
  strArray=str.split(",");
  var nstr="";
  //alert(str);alert(id);
  for(var i=0;i<strArray.length;i++)
  {
     if(strArray[i]!=id){nstr=nstr+strArray[i];nstr=nstr+",";}
  }
  nstr=nstr.substr(0,nstr.length-1);
 // alert(nstr);

  return nstr;
}
//возвращает колличество выбраных элементов
//из строки выбора
function GetSelectedCount(id)
{
   // alert(id);
    var el=document.getElementById(id)
    str=el.value;
    var qty=0;
    if(str==""){qty=0;return qty;}
    if(!el){alert("Ошибка:Не найден элемент");return;}
    else
    {
       var strArray=new Array();
       strArray=str.split(",");
       qty=strArray.length;
    }

   return qty;
}
//функция добавления выбраных сотрудников из
//исходного списка в формируемый список
function AddList()
{
  //alert();
  var c=GetSelectedCount("ss");
  if(c==0){alert("Не выбрано ни одного сотрудника!!!");return;}
  else
  {
     var el=document.getElementById("ss");
     var del=document.getElementById("dss");
     del.value=el.value;
     document.execfrm.action="groupop.php?action=addlist";
     document.execfrm.submit();
  }
}
function ShowCloseModalWindow(id,flag)
{
  var el=document.getElementById(id);
  if(!el){
		alert("Ошибка:Не найден элемент");
		return;}

  if(flag==1)
  {
    el.style.display="none";
  }
  else
   {
      //проверяем открыто ли окошко в данный момент
       if(el.style.display=="block")
       {
        //el.style.display = "none";
       }
      el.style.display="block";
   }


}
//ищет в строке значение val.
//sp - разделитель в строке
function CompareString(str,val,sp)
{
  //alert(str);
  var strArray=new Array();
  strArray=str.split(sp);
  for(var i=0;i<strArray.length;i++)
  {
     if(strArray[i]==val)
        return true;
  }
  return;
}
 // Удаляет потомка child из владельца owner
   //id-id территории для удаления
   //selstr-строка с id
   function DeleteItem(owner,child,id,selstr)
   {
       var ow=document.getElementById(owner);
       var c=document.getElementById(child);
      ow.removeChild(c);
       var sid=owner+"selstr";
       var s=document.getElementById(selstr);
       s.value=DelSelId(s.value,id);
   }



function getCoordinate(axis)
{

   var e=event || window.event;
   if(axis=="X")return e.clientX;
   if(axis=="Y")return e.clientY;
   return;
}
function CreateSelectExt(array,owner,styleclass,id,name,selected)
{
  var newel=document.createElement("select");
  if (owner==null) {
        document.body.appendChild(newel);
 }else{
  var parent=document.getElementById(owner);
  if(parent)parent.appendChild(newel);

 }

 if(styleclass!=null)newel.className=styleclass;

 for(var i=0;i<array.length;i++)
 {
   var values=array[i].split("~");
   var val=values[0];
   var text=values[1];
   var newitem=document.createElement("option");
       newitem.value=val;
      var t=document.createTextNode(text);
     newitem.appendChild(t);
   newel.appendChild(newitem);
 }
 if(name!=null)newel.name=name;
 if(selected!=null)newel.selectedIndex=selected;
}

function RunDTS(number,width,height)
{
  var url="dtsexec.php?action="+number;
  var param="width="+width+",height="+height+",resizable=no,scrollbars=no,menubar=no";
  window.open(url,"",param);
}
function ClearForm(f)
{
   var len=f.elements.length;
   for(var i=0;i<len;i++)
   {
     var item = f.elements[i];

     if(item.type!="undefined")
     {
       if(item.type=="text")item.value="";
     }
     if(item.tagName=="SELECT" || item.tagName=="select")item.selectedIndex=item.firstChild;
     if(item.tagName=="TEXTAREA" || item.tagName=="textarea")item.value="";
   }
}
//проверяет расширение загружаемого файла.
//
function SubmitUploadForm(f,expansion)
{
    if(f.file.value == "")
    {
     alert("Не выбран файл");
     f.file.focus();
     return;
    }
    var path = f.file.value;
    var rpos = path.lastIndexOf("\\",path.length);
    var file = path.substring(rpos+1,path.length);
    if(CheckExpansion(file,expansion) == false)
    {
     alert("Неверное расширение файла");
     f.file.focus();
     return;
    }
   //проверяем тип загрузки
  var type = file.substr(0,1);
  if(type!='u' && type!='p')
  {
     alert("Неверное имя файла");
     f.file.focus();
     return;
  }
  else
  {
    f.type.value=type;
  }
  f.submit();
}
function ShowIndicator(owner,text,left,top,src)
{
   var o=document.getElementById(owner);
   var cont = document.createElement("div");
       //cont.style.border="1 solid midnightblue";
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
       img.src=src;
       img.style.marginRight=10+"px";
       img.style.width=15+"px";
       img.style.height=15+"px";

   cont.appendChild(img);
   var t=document.createTextNode(text);
       cont.appendChild(t);

   return img;
}
function RemoveIndicator(owner,indicator)
{
  var o=document.getElementById(owner);
  if(!o)return;
  var i=document.getElementById(indicator);
  if(!i)return;
      o.removeChild(i);
}
function confirmRemoving(text,onTrue)
{
	if(confirm(text))
	  document.location.href=onTrue;

}
function EvLoad(f)	//загрузка событий за период
{	
	if(f.en_date.value == "" || f.st_date.value == "")
    {alert("Не указан интервал загрузки");return};
	var url="evload_http.php?action1="+f.st_date.value+"&action2="+f.en_date.value;
	var param="width=300,height=5,resizable=no,scrollbars=no,menubar=no";
	window.open(url,"",param);
}


function EvLoad2(f)	//загрузка событий за период
{	
    if(f.en_date.value == "" || f.st_date.value == ""){alert("Не указан интервал загрузки");return};
    $( "#evload" ).after( "<span>&nbsp;&nbspЗагрузка инициализирована</span><br>" );
     var data1 = {act:"init_load", date_st:f.st_date.value, date_end:f.en_date.value};
            $.ajax({
             url: "evload_http.php", 
             type: "POST", 
             data: data1,
             dataType: "text",
            success: function (data) {
                var dataObj = eval(data);
                if (dataObj == '1')
                {
                    alert('Загрузка событий за период выполнена');
                }
                else{alert('Во время загрузки были ошибки.');}
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error EvLoad2');
            }
            });

}


