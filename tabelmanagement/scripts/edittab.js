//REPLACED 04.10.2007 002
$(function () {
            $("#tc_st_date").pickmeup({
                change : function (val) {
                    $("#tc_st_date").val(val).pickmeup("hide")
                }
            });
            $("#tc_en_date").pickmeup({
                change : function (val) {
                    $("#tc_en_date").val(val).pickmeup("hide")
                }
            });
           
        });
function ShowIndicator(owner,text,left,top)
{
   var o=document.getElementById(owner);
   var cont = document.createElement("div");
       //cont.style.border="1 solid midnightblue";
       cont.id="indicator";
       cont.style.position="relative";
       cont.style.left=left+"%";
       cont.style.top=top+"%";
       cont.style.width=80+"%";
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
	// alert('sdf');
  var o=document.getElementById(owner);
  if(!o)return;
  var i=document.getElementById(indicator);
  if(!i)return;
      o.removeChild(i);
}
function RemoveListSelect(owner,id_list)
{
  var o=document.getElementById(owner);
  if(o)
  {
  	while (o.firstChild)
    o.removeChild(o.firstChild);
  }


}
function ClearFilter(f)
{
  f.tab_num.value="";
  f.family.value="";
  f.name.value="";
  f.secname.value="";
  f.position.value="";
  f.depart.selectedIndex=0;
  f.graph.selectedIndex=0;
  f.tc_st_date.value = "";
  f.tc_en_date.value = "";

}



window.onload=function()
{
   //CreatePanel("menu");
   //document.body.onscroll=movePanel;

}
///////////////////////////////////////////////////////////////////////////////
//обработчики событий при редактированиии табеля
var CURRVAL = null;//текущие редактируемое значение
var ID_PERS = new Array();
var CURR_USER_ID = null;
var EDIT_FIELDS = new Array(); //массив для хранения измененных полей

function Calculate(user_id,half_num,val)
{
   //alert(user_id);alert(half_num);alert(val);
  //alert(half_num);
 var f = document.forms["person"];
 var total=f.elements["t_t_"];
 var f_half=f.elements["t_d_f_h_"];
 var s_half=f.elements["t_d_s_h_"];
 var f_half_c=f.elements["c_d_f_h_"];
 var s_half_c=f.elements["c_d_s_h_"];
 var total_code=f.elements["c_d_t_"];
 /*//смотрим не является ли значение пустой строкой
 if(val == "" )
 {
 	val
 }*/

  //считаем для первой половины месяца
  if(half_num == 0)
  {
    if(CURRVAL > val)//произошло уменьшение значения
    {
      var r = CURRVAL-val; f_half.value=parseFloat(f_half.value)-parseFloat(r);
                           total.value=parseFloat(total.value)-parseFloat(r);
     //рассчитываем колл-во дней
     if(val==0)
     {
      f_half_c.value = parseFloat(f_half_c.value)-1;
      total_code.value = parseFloat(total_code.value)-1;
     }
    }
    else if(CURRVAL < val)//произошло увеличение значения
    {
       var r = val-CURRVAL; f_half.value=parseFloat(f_half.value)+parseFloat(r);
                            total.value=parseFloat(total.value)+parseFloat(r);
     //рассчитываем колл-во дней
     if(CURRVAL==0 && val>0)
     {
      f_half_c.value = parseFloat(f_half_c.value)+1;
      total_code.value = parseFloat(total_code.value)+1;
     }
    }
  }
  //рассчитываем для второй половины
  if(half_num == 1)
  {
    if(CURRVAL > val)
    {
      var r = CURRVAL-val; s_half.value=parseFloat(s_half.value)-parseFloat(r);
                           total.value=parseFloat(total.value)-parseFloat(r);
     //рассчитываем колл-во дней
     if(val==0)
     {
      s_half_c.value = parseFloat(s_half_c.value)-1;
      total_code.value = parseFloat(total_code.value)-1;
     }
    }
    else if(CURRVAL < val)
    {
       var r = val-CURRVAL; s_half.value=parseFloat(s_half.value)+parseFloat(r);
                            total.value=parseFloat(total.value)+parseFloat(r)
     //рассчитываем колл-во дней
     if(CURRVAL==0 && val>0)
     {
      s_half_c.value = parseFloat(s_half_c.value)+1;
      total_code.value = parseFloat(total_code.value)+1;
     }
    }

  }

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
function onEditing(obj)
{
 obj.style.border ="1px solid red";
 CURRVAL = obj.value; //запоминаем значение текущего поля
}

function onCancelEditing(obj)
{
  obj.style.border ="1px solid silver";

  if(obj.value=="")
  {
    obj.value=0;
  }
  if(isNaN(obj.value)==true)
  {
    alert("Это не число");
    obj.value=0;
    return;
  }
  if(parseFloat(obj.value)<0)
  {
    alert("Число должно быть неотрицательным");
    obj.value=0;
    return;
  }

  if(obj.value!=CURRVAL) //и такого значения нет в массиве значений
  {
     var ef = obj.name + "|" + obj.name.replace('day_','cod_');
     if(isContein(ef,EDIT_FIELDS)==false)
     {
       EDIT_FIELDS.push(ef);//alert(EDIT_FIELDS.length);
     }
    //Проверяем какая половина редактирется
     var num = obj.name.split('_')[1];
     var half = 0;//первая половина
     if(num>15)half=1; //воторая половина
     Calculate(CURR_USER_ID,half,obj.value);
  }

}

function onFilter(but,f)
{

  but.disabled = true;
  var filter="";
  if(isNaN(f.tab_num.value)==true)
  {
    alert("Табельный номер должен быть числом");f.tab_num.value="";
    f.tab_num.focus();
    but.disabled = false;
    return;
  }
  for (var i = 0; i < f.elements.length; i++ )
  {
      var item = f.elements[i];
      if(item.type == "text")
      {
         if(CheckString(item.value)==1)
         {
          alert("Недопустимый символ при вводе");item.focus();
          but.disabled = false;
          return;
         }
      }
     if(item.type!="button")
     {
     filter+="&"+item.name+"="+item.value;
     }

  }
  var url="aresponse.php";
  var param = "?obj=pers";
      param += "&act=filter";
  if(filter!="")param += filter;
  if(document.getElementById("pers_list_select"))RemoveListSelect("list","pers_list_select");

  ShowIndicator("list","Построение списка...",40,50);
  loadXMLDoc(url,param);


}
///////////////////////////////////////////////////////////////////////////////
//Обработчики ответов
function ParseFilterResult(xdoc)
{
  var items = xdoc.getElementsByTagName('item');
  if(items)
  {
     RemoveIndicator("list","indicator");
    //создаём селект
    if(items.length==0)
    {
       alert("Не найдено ни одного сотрудника");//return;
    }
    else
    {
     var select = CreateSelect("pers_list_select","selectList","input","list");
        select.multiple=true;
        select.size=30;
        select.style.width=100+"%";
        select.style.height=250+"px";
        select.ondblclick=function()
        {
          if(this.selectedIndex!=0)
          {
           getPersonTime(this.options[this.selectedIndex].value);
           this.disabled=1;
          }
        }
        AddOptions(select.id,0,"");
        select.selectedIndex[0];
    //Формируем новый список
    for(var i=0;i<items.length;i++)
    {
      var val = items[i].getAttributeNode("id").value;
      var text = items[i].getAttributeNode("family").value +" " + items[i].getAttributeNode("name").value + " " + items[i].getAttributeNode("secname").value;
      var pers=AddOptions(select.id,val,text);
    }

  }
 }
 document.filtrfrm.sbutton.disabled = 0;
}
function getPersonTime(id)
{
 if(id==0)return;
 ShowIndicator("status_bar","Получение данных...",0,0);
 var f=document.getElementById("filtrfrm");
 //записываем в поле формы табеля id текущего сотрудника
 document.person.pid.value = id;
 var url="aresponse.php?obj=pers";
 var param="&act=gettime&pid="+id+"&month="+f.sel_date_month.value+"&year="+f.sel_date_year.value;
 //alert(url+param);
 loadXMLDoc(url,param);
 setTimeout("RemoveIndicator(\"status_bar\",\"indicator\");", 4000);
}

function toCountTime(f)
{
    //try to get list of employees
    var ps = document.getElementById('pers_list_select');
    if((ps && ps.selectedIndex>0) || (ps && f.tcradio[1].checked))
    {
        var param       = "&act=tocount";
        var Date_St     = f.tc_st_date.value;
        Date_St         = Date_St.split(".");
        var newDate_St  = new Date(Date_St[1]+","+Date_St[0]+","+Date_St[2]).getTime();
        var Date_End    = f.tc_en_date.value;
        Date_End        = Date_End.split(".");
        var newDate_End = new Date(Date_End[1]+","+Date_End[0]+","+Date_End[2]).getTime();
        var Date_diff   = newDate_End - newDate_St;
        
        if(Date_diff > 16400000000){alert("Интервал расчёт должен быть менее полугода");return;}
        if(f.tc_en_date.value == "" || f.tc_st_date.value == ""){alert("Не указан интервал времени для пересчёта");return;}
        
        param+="&st_date="+f.tc_st_date.value;
        
        if(f.tc_en_date.value!="")       param+="&en_date="+f.tc_en_date.value;
        if(f.correct_flag.checked == 1 ) param+="&correct_flag=1";
        else                             param+="&correct_flag=0";
        
        //getting "ID" of selected employees
        var se = '';//selected employees
        if(f.tcradio[0].checked)
        {
            se = getSelectedValues(ps);
            param+="&empid="+se;
        }
        else
        {
            //getting filter values;
            if(isNaN(f.tab_num.value)==true)
            {
                alert("Табельный номер должен быть числом");f.tab_num.value="";
                f.tab_num.focus();
                return;
            }
            if(isNaN(f.graph_offset.value)==true)
            {
                alert("Смещение должно быть числом");f.graph_offset.value="0";
                f.graph_offset.focus();
                return;
            }
            if(CheckString(f.family.value)==1){alert("Недопустимый символ при вводе");f.family.focus();return;}
            else{param+="&family="+f.family.value;}
            if(CheckString(f.name.value)==1){alert("Недопустимый символ при вводе");f.name.focus();return;}
            else{param+="&name="+f.name.value;}
            if(CheckString(f.secname.value)==1){alert("Недопустимый символ при вводе");f.secname.focus();return;}
            else{param+="&secname="+f.secname.value;}
            if(CheckString(f.position.value)==1){alert("Недопустимый символ при вводе");f.position.focus();return;}
            else{param+="&position="+f.position.value;}
            param+="&depart="+f.depart.value;
            param+="&graph="+f.graph[f.graph.selectedIndex].value;
        }
        param+="&graph_recalc="+f.graph_recalc[f.graph_recalc.selectedIndex].value;
         param+="&graph_offset="+f.graph_offset.value;
        loadRecalcStatus('aresponse.php?obj=pers',param);
        ShowIndicator("status_bar","Перерасчёт времени... Пожалуйста дождитесь сообщения о завершении.",0,0);
    }
    else
    {
        alert("Не выбран ни один из сотрудников");
    }
}
function ParseTimeResult(xdoc)
{
    try
    {
      var  select_list = document.getElementById("pers_list_select");
            select_list.disabled=0;
     // var tocountbt = document.getElementById("tocountbt");
           //tocountbt.disabled=0;
    }
    catch(e){}
    var pid;
    var items = xdoc.getElementsByTagName('item');
    //выводим личные данные сотрудника
    desc = xdoc.getElementsByTagName('description');
    if(desc)
    {
      var f = document.getElementById("filtrfrm");
          f.tab_num.value   = desc[0].getAttributeNode("tabnum").value;
          f.family.value    = desc[0].getAttributeNode("family").value;
          f.name.value      = desc[0].getAttributeNode("name").value;
          f.secname.value   = desc[0].getAttributeNode("secname").value;
          f.position.value  = desc[0].getAttributeNode("position").value;
          f.deptName.value  = desc[0].getAttributeNode("depart").value;
          f.graph.value     = desc[0].getAttributeNode("graph").value;

          pid = desc[0].getAttributeNode("id").value;
    }
    else {return}

    if(items && items.length!=0)
    {
      var f=document.getElementById("person");
          f.pid.value=pid;
      var TIME = new Array();
      var CODE = new Array();
      var FLAGS = new Array();
      //получаем данные
      for(var i=0;i<items.length;i++)
      {
        var time=items[i].getAttributeNode("time").value;
        var code=items[i].getAttributeNode("idsign").value;
        FLAGS[i] = items[i].getAttributeNode("flag").value;
        TIME[i]=time;
        CODE[i]=code;

      }
      var j=0;
      var k=0;
      var f_h_time =0;
      var s_h_time =0;
      var f_h_code =0;
      var s_h_code =0;
      var t_time =0;

      if(TIME.length!=31 || CODE.length!=31)
      {
         var z=TIME.length;
         var n =CODE.length;
         for(z;z<31;z++)TIME[z]=0;
         for(n;n<31;n++)CODE[n]=0;
      }

      //выводим данные
      for(var i=0;i<f.elements.length;i++)
      {
        var item = f.elements[i];
        if(item.name.indexOf("day_",0)!=-1)
        {
           item.value=TIME[j];
           if(FLAGS[j]==1)
              item.style.backgroundColor = '#21f466';
           else
              item.style.backgroundColor = 'white';


           t_time +=parseFloat(TIME[j]);
           if(j<15)f_h_time+=parseFloat(TIME[j]);
           if(j>=15)s_h_time+=parseFloat(TIME[j]);
           if(TIME[j]>0 && j<15)f_h_code++;
           if(TIME[j]>0 && j>=15)s_h_code++;
           j++;
        }
        if(item.name.indexOf("cod_",0)!=-1)
        {
           item.value=CODE[k];k++;
        }
      }
	  //alert(parseFloat(TIME[18]));
	  f.t_d_f_h_.value = (f_h_time!=0) ? f_h_time.toPrecision(4) : f_h_time;	
      f.t_d_s_h_.value = (s_h_time!=0) ? s_h_time.toPrecision(4) : s_h_time;
      f.t_t_.value 	   = (t_time!=0)   ? t_time.toPrecision(4)   : t_time;
      f.c_d_f_h_.value=f_h_code;
      f.c_d_s_h_.value=s_h_code;
      f.c_d_t_.value= f_h_code+s_h_code;
    }
   RemoveIndicator("status_bar","indicator");
}

function SaveTabelChange()
{
  var f = document.getElementById("person");
  var month = document.getElementById("filtrfrm");
  var year = document.getElementById("filtrfrm");
  var date ="01."+month.sel_date_month.value+"."+year.sel_date_year.value;

  if(f.pid.value=="")
  {
    alert("Не выбран сотрудник");return;
  }
   if(EDIT_FIELDS.length==0)
   {
     alert("Изминений не было");return;
   }
   var time = "";
   var codes = "";
   ShowIndicator("status_bar","Сохранение изменений...",0,0);
   for(var i=0;i < EDIT_FIELDS.length;i++)
   {
    var item = EDIT_FIELDS[i].split('|');
    var day = item[0];
    var cod = item[1];
    //alert(day + "_" + cod);
    time  += day+"_" + f.elements[day].value + ";";
    codes += cod+"_" + f.elements[cod].options[f.elements[cod].selectedIndex].value + ";";
   }

  time=time.substr(0,time.length-1);
  codes=codes.substr(0,codes.length-1);
  var url="aresponse.php?obj=pers";
  var param="&act=savetabel&pid="+f.pid.value+"&date="+date+"&time="+time+"&codes="+codes;
  //alert(url+param);
  loadXMLDoc(url,param);
   setTimeout("RemoveIndicator(\"status_bar\",\"indicator\");", 3000);
  
}
function ConfirmSaving(xdoc)
{
alert("Данные сохранены успешно");
RemoveIndicator("status_bar","indicator");
CURRVAL = null;
//очищаем массив отредактированных значение
	for ( var i = 0; i < EDIT_FIELDS.length; i++)
	{
	  var item = EDIT_FIELDS[i];
	  var idArr = item.split('|');
	  var e = document.getElementById(idArr[0]);
	  e.style.backgroundColor = '#21f466';
	}
	clearArray(EDIT_FIELDS);
}
function onTocountResponse(xdoc)
{
	var error = xdoc.getElementsByTagName('error');
	if(error.length > 0)
	{
	 //alert("Ошибка при пересчёте" + error[0].firstChild.data);
	 RemoveIndicator("status_bar","indicator");
	}
	else
	{
		//var msg = xdoc.getElementsByTagName('message');
		RemoveIndicator("status_bar","indicator");
    }

}
function sbonmouseout(obj)
{
   obj.style.borderLeft = "1px solid silver";
   obj.style.borderBottom = "1px solid silver";
}
function sbonmouseover(obj)
{
   obj.style.borderLeft = "1px solid black";
   obj.style.borderBottom = "1px solid black";
}

function clearArray(array)
{
  for ( var i = 0; i <array.length; i++)
	 array.shift();

}
