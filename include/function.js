
function CheckString(str)
{
  if(str.indexOf("'",0)!=-1)
  return 1;
  if(str.indexOf("\"",0)!=-1)
  return 1;
  if(str.indexOf("<",0)!=-1)
  return 1;
  if(str.indexOf(">",0)!=-1)
  return 1;
  if(str.indexOf("%",0)!=-1)
  return 1;
}

function isTime(str)
{
   var msg="Неверный формат времени.Формат: часы : минуты";
   var msg1="Недопустимые символы в строке времени";
   if(str.length!=5){alert(msg);return false;}
   var h=str.substr(0,2);
   var m=str.substr(3,5);
   if(isNaN(h)==true || isNaN(m)==true){alert(msg1);return false;}
   var z=h.substr(0,1);
   var z1=m.substr(0,1);
   if(z=="-" || z1=="-" || z=="+" || z1=="+"){alert(msg1);return false;}

   if(h>24 || m>=60){alert(msg);return false;}

   return true;
}

//вставляет имя файла
function GetFileName(did,str)
{ //did id объекта которому нужно присвоить имя файла
     //str - строка из которой нужно выделить имя файла
      //находим последние вхождение символа "\"

    var newstr = str.split('\\').join('/');

    var lpos=newstr.lastIndexOf("/",newstr.length);
    var dObj=document.getElementById(did);
	  

    if(lpos==-1)
    {
        lpos = 0;
    }
	   
    var len=str.length-lpos;
	
    var str1;
		
    if (lpos==0)
    {
            str1 = str.substr(lpos,len);
            }
    else{
            str1 = str.substr(lpos+1,len);		
    }
	
      if(str1.lastIndexOf(".",str1.length)==-1)
	  {
           alert("Некорректное имя файла");return false;
        }
        else{
                    dObj.value=str1;
            }
			
			
        return true;
}
//проверяет является ли строка шестнадчеричным числом
function IsHex(str)
{

   var symb="ABCDEF";
   var dig="0123456789";
   var f=0;
   str=str.toUpperCase();

   for(var i=0;i<=str.length;i++)
   {
     a=str.substr(i,1);

     if(symb.indexOf(a,0)==-1 && dig.indexOf(a,0)==-1)
     {f=1;break;}
   }
   //alert(f);
   if(f==0)return true;else return false;
}
//проверяет расширение файла
function CheckExpansion(str,ex)
{
  var lpos=str.lastIndexOf(".",str.length);
  var len=str.length-lpos;
  var ex1=str.substr(lpos+1,len);

  if(ex1.toLowerCase()===ex.toLowerCase())return true;else return false;
}

function ShowPhoto(s,i,n,path)
{

// 04.07.2011 - добавил полный путь для сохранения фотографий

// path - куда класть фотки на сервере, т.е. upload_dir 

 
   var p=document.getElementById(s);
   var im=document.getElementById(i);
   var name=document.getElementById(n);
   
   
// полное имя к файлу фото - путь_папки_загрузки(у нас это /foto/) + имя_файла
   var fullpath=path+p.value;

// перебиваем значение, ибо дальше используется    
   var path=p.value;
   
  if(path=="")
  {alert("Не выбрана фотография");}
  else
  {

  
	if(GetFileName(n,path)==false)return;	
    if(CheckExpansion(name.value,"jpg")==false){alert("11 Неверное расширение файла");return;}
	
	
	
// изменить ссылку на картинку объекта IMAGE
    im.src=fullpath;
// перерисовать окно 	
 	im.refresh();
	aepers.photo.value = p.value;
	
  }
}

function AddEdPers(f, path)
{

    var erflag=0;
    //проверка табельного номера
    if(f.tabnum.value=="")
    {f.tabnum.value=0;}
    if(CheckString(f.tabnum.value)==1)
    {erflag=1;
     alert("Недопустимый символ при вводе табельного номера");
     f.tabnum.focus();
     return;
    }
    if(isNaN(f.tabnum.value)==true)
    { erflg=1;
      alert("Табельный номер должен быть числовым");
      f.tabnum.focus();
      return;
    }
    if(f.graph_offset.value=="")
    {f.graph_offset.value=0;}
    if(isNaN(f.graph_offset.value)==true)
    { erflg=1;
      alert("Смещение графика должно быть числовым");
      f.graph_offset.focus();
      return;
    }
    if(f.graph_offset.value<0 || f.graph_offset.value>30)
    { erflg=1;
      alert("Смещение графика должно быть от 0 до 30");
      f.graph_offset.focus();
      return;
    }
    //проверка фамилии
    if(f.family.value=="")
    {erflag=1;
     alert("Не указана фамилия");
     f.family.focus();
     return;
    }

    if(CheckString(f.family.value)==1)
    {erflag=1;
     alert("Недопустимый символ при вводе фамилии");
     f.family.focus();
     return;
    }
    
    //проверка имени
    if(f.fname.value=="")
    {erflag=1;
     alert("Ошибка:Не указано имя");
     f.fname.focus();
     return;
    }
    if(CheckString(f.fname.value)==1)
    {erflag=1;
     alert("Ошибка:недопустимый символ при вводе имени");
     f.fname.focus();
     return;
    }
    //проверка отчество
    if(f.secname.value=="")
    {erflag=1;
     alert("Ошибка:Не указано отчество");
     f.secname.focus();
     return;
    }
    if(CheckString(f.secname.value)==1)
    {erflag=1;
     alert("Ошибка:недопустимый символ при вводе отчества");
     f.secname.focus();
     return;
    }
     //проверка должности
    if(f.position.value=="")
    {erflag=1;
     alert("Ошибка:Не указана должность");
     f.position.focus();
     return;
    }
    if(CheckString(f.position.value)==1)
    {erflag=1;
     alert("Ошибка:недопустимый символ при вводе должности");
     f.position.focus();
     return;
    }
    
    
  
    /////// проверить введен ли график
    if (f.graph.value==0)
    {
        erflag=1;
        alert("Не указан график работы");
        f.graph.focus();
        return;
    }   
  
    //проверяем реквизиты пропуска
    if(CheckString(f.pxcodenum.value)==1)
    {erflag=1;alert("Ошибка:недопустимый символ при вводе кода пропуска");return;}

    if(f.pxcodenum.value.length!=0)
    {
        if(f.pxcodenum.value.length!=16)
        {
            erflag=1;
            alert("Ошибка: код пропуска должен состоять из 16 символов");
            return;
        }
        if(IsHex(f.pxcodenum.value)==false)
        {
            erflag=1;
            alert("Ошибка: код пропуска должен быть в шестнадцатиричном формате");
            return;
        }

    }
    else if (!(confirm("Не указан код пропуска. Продолжить?")))
    {
        f.pxcodenum.focus();
        return;
    }
	
    if(CheckString(f.comment.value)==1)
    {erflag=1;
        alert("Недопустимый символ при вводе коментария пропуска");
        f.comment.focus();
        return;
    }    

    if(f.pincod.value=="")
    {
        f.pincod.value ="zzzz"; // теперь по умолчанию так, "0000" - было по умолчанию 
    }
    else
    {
        if(f.pincod.value.length!=4){
            alert("Pin-код должен быть числовым и состоять из 4 цифр");
            return;
        }
    }

    //заполняем hidden поля
    var h = f.zone.selectedIndex;
    f.id_zone.value=f.zone.options[h].value;

    h = f.graph.selectedIndex;
    f.graph_name.value=f.graph.options[h].value;

    h = f.algoritm.selectedIndex;
    f.id_algoritm.value = f.algoritm.options[h].value;
    
    var zzone = f.p_id_zone.checked==true ? 1 : 0;
    var ddopusk = f.p_id_dopusk.checked==true ? 1 : 0;
        
        
    if(f.pxdatein.value=="")
    {
        date = new Date();
        var today="";
        today+=date.getDate();
        today+=".";
        today+=date.getMonth();
        today+=".";
        today+=date.getYear();
        f.pxdatein.value=today;
    }

    if(erflag==0)
    {   //Создаём строку статуса
       
        var s="";
        s+= f.pxblock.checked==true ? "1" : "0";
        s+= f.pxguest.checked==true ? "1" : "0";
        s+= f.pxadmin.checked==true ? "1" : "0";
        s+= f.pxdouble.checked==true ? "1" : "0";
        s+= f.pxauto.checked==true ? "0" : "0";
        s+="000";

        var breakfast = f.breakfast.checked==true ? 1 : 0;
        var din = f.din.checked==true ? 1 : 0;
        var supper = f.supper.checked==true ? 1 : 0;
        
        
        if(f.filename.value!=""){
            var tto = f.filename.value;
            f.photoname.value=tto;
        }
        
        f.status.value=s;

         //проверка отдела
        if(f.deptName.value=='')
        {erflag=1;
            alert("Не указан отдел");
            return;
        }
        else
        {
            var data1 = {obj:"dept_check",deptname:f.deptName.value};
            $.ajax({
             url: "asinc_save_pers_data.php", 
             type: "POST",
             data: data1,
             dataType: "json",
            success: function (data) {
                var dataObj = eval(data);
                if(dataObj.id=='0'){erflag=1;alert("Указанного отдела не существует");return;};
                f.id_dept.value=dataObj.id;
                 var data3 = {obj:"code",
                    id_pers:f.id_pers.value,
                    pxcodenum:f.pxcodenum.value,
                    pxcodenum_old:f.pxcodenum_old.value,
                    pxcode_id:f.pxcode_id.value,
                    pincod:f.pincod.value,
                    pxdatein:f.pxdatein.value,
                    pxdateout:f.pxdateout.value,
                    date_in:f.date_in.value,
                    date_out:f.date_out.value,
                    status:s,
                    comment: f.comment.value};
                $.ajax({
                 url: "asinc_save_pers_data.php", 
                 type: "POST",
                 data: data3,
                 dataType: "json",
                success: function (data) {
                    var dataObj = eval(data);
                    if(dataObj.px_edit == 0 ){//Данный пропуск назначен другому активному сотруднику
                         if(dataObj.pers_edit == 0){//Нельзя редактировать сотрудника
                            alert('Указанный период приёма-увольнения пересекается с периодом работы друго сотрудника имевшего(имеющего) данный пропуск');
                            return;
                        }
                        
                        alert('Данный пропуск назначен другому активному сотруднику, изменения параметров пропуска НЕ будут сохранены');
                        //return;
                    }
                    else if(dataObj.px_edit == -1){//Добавить/обновить пропуск не удалось (хз когда такое может произойти, но всё же)
                        alert('Добавить/обновить пропуск не удалось');
                        //return;
                    }
                    else if(dataObj.px_edit == -2){// пропуск не задан
                        
                    }
                    else $('#pxcode_id').val(dataObj.px_edit);
                    
                    if(dataObj.pers_edit == 0){//Нельзя редактировать сотрудника
                        alert('Указанный период приёма-увольнения пересекается с периодом работы друго сотрудника имевшего(имеющего) данный пропуск');
                        return;
                    }
                    else
                    {
                        var ext_int = typeof f.ext_int == 'undefined' ? 0 : f.ext_int.value;
                        var ext_text = typeof f.ext_text == 'undefined' ? '' : f.ext_text.value;
                        var data2 = {obj:"save",
                                action:path,
                                id_pers:f.id_pers.value,
                                tabnum:f.tabnum.value,
                                ext_int:ext_int,
                                ext_text:ext_text,
                                family:f.family.value,
                                fname:f.fname.value,
                                secname:f.secname.value,
                                position:f.position.value,
                                id_dept:f.id_dept.value,
                                date_in:f.date_in.value,
                                date_out:f.date_out.value,
                                breakfast:breakfast,
                                din:din,
                                supper:supper,
                                pxcode_id:f.pxcode_id.value,
                                pxcodenum:f.pxcodenum.value,
                                id_zone:f.id_zone.value,
                                p_id_zone:zzone,
                                p_id_dopusk:ddopusk,
                                dopusk:f.dopusk.value,
                                graph_name:f.graph_name.value,
                                graph_offset:f.graph_offset.value,
                                id_algoritm:f.id_algoritm.value,
                                photoname:f.photoname.value
                                };
                            $.ajax({
                             url: "asinc_save_pers_data.php", 
                             type: "POST",
                             data: data2,
                             dataType: "json",
                            success: function (data) {
                                var dataObj = eval(data);
                                if(dataObj.res==1){
                                    document.location.href = 'personal.php?action=new';
                                }
                                else  alert('При обновлении / добавлении сотрудника произошла ошибка');
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert('error save pers info');
                             }
                            });
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('error with px_code');
                 }
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error Dept check');
                erflag=1;
                return;
             }
            });
        }
       



      //f.submit();
    }
}




function ShowClosePxCode(but)
{

  var w=window.document.getElementById("codewind");
  //alert(but.value);
  if(but.value=="+")
  {
    w.style.display="block";
    but.value="-";
  }
  else
  {
    w.style.display="none";
    but.value="+";
  }
}
function CheckPxCodeValidate(f)
{

  var erflg=0;

  if(CheckString(f.code.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе номера");return;}
  //alert(1);
 if(CheckString(f.datein.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе даты начала эксплуатации");return;}

  //alert(3);
  if(CheckString(f.dateout.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе даты завершения эксплуатации");return;}
 // alert(2);

  if(CheckString(f.pin.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе pin кода");return;}

  if(CheckString(f.comment.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе комментария");return;}


  if(f.code.value=="")
  {erflg=1;alert("Добавление невозможно:пропуск должен иметь код");return;}

  if(CheckString(f.code.value)==1)
  {erflag=1;alert("Ошибка:недопустимый символ при вводе кода пропуска");return;}

  if(f.code.value.length!=16 || f.code.value.length<16)
  {
    erflg=1;
    alert("Ошибка: код пропуска должен состоять из 16 символов");
    return;
  }
  if(IsHex(f.code.value)==false)
  {
    erflg=1;
    alert("Ошибка: код пропуска должен быть в шестнадцатиричном формате");
    return;
  }

  if(f.datein.value=="")
  {

     date = new Date();
     var today="";
     today+=date.getDate();
     today+=".";
     today+=date.getMonth();
     today+=".";
     today+=date.getYear();
     f.datein.value=today;
     //alert(f.datein.value);
  }

  if(erflg==0)
  {
     //Создаём строку статуса
     var s="";
     if(f.block.checked==true)s+="1";
       else
          s+="0";
     if(f.pxguest.checked==true)s+="1";
       else
          s+="0";
     if(f.pxadmin.checked==true)s+="1";
       else
          s+="0";
     if(f.pxdouble.checked==true)s+="1";
       else
          s+="0";
     if(f.pxauto.checked==true)s+="1";
       else
          s+="0";

     s+="000";
     f.status.value=s;
    // alert(f.status.value);

     f.submit();
  }

}

function Calendar(top,left)
{

   // var pos="absolute;top:"+top+"px;left:"+left+"px;";
    var cal=window.document.getElementById("calendarwind");
    //alert(cal.tagName);
    //cal.style.position="absolute";
    cal.style.position.top=top;
    cal.style.position.left=left;
    cal.style.display="block";
}
function ShowFindFrm()
{
   var el=window.document.getElementById("findwind");
   el.style.display="block";

}
function CloseFindFrm()
{
	$is_filtered = false;

   var el=window.document.getElementById("findwind");
   el.style.display="none";
}


function ShowWindow(url,title,w,h,scroll)
{
  var wp="";//свойства окна
  wp+="Width="+w+",";
  wp+="Height="+h+",";
  wp+="Scrollbars="+scroll+",";
  wp+="Resizeble=0,";
  wp+="top=200,left=300";
  //alert(wp);
  window.open(url,"",wp);
}


function SearchPers(f)
{
  //alert("sfdsfs");
  var erflg=0;
  var n=f.depart.selectedIndex;

  f.dept_id.value=f.depart.options[n].value;
  //var str=f.family.value+"-"+f.fname.value+"-"+f.secname.value+"-"+f.position.value+"-"+f.depart.options[n].value+"-"+f.tab_num.value+"-"+f.pass_code.value;
  //alert(f.depart.options[n].value);

  if(CheckString(f.fname.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе имени");return;}
 // alert(1);
 if(CheckString(f.family.value)==1)
  //alert(2);
  {erflg=1;alert("Ошибка:недопустимый символ при вводе фамилии");return;}
  //alert(3);
  if(CheckString(f.secname.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе отчества");return;}

  if(CheckString(f.position.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе должности");return;}

  if(CheckString(f.tab_num.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе табельного номера");return;}

  if(CheckString(f.pass_code.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе кода пропуска");return;}

  if(f.pass_code.value!="")
  {
     if(f.pass_code.value.length!=16)
    {  erflg=1;
       alert("Ошибка: код пропуска должен состоять из 16 символов");
       return;
    }
    if(IsHex(f.pass_code.value)==false)
    {
     erflg=1;
     alert("Ошибка: код пропуска должен быть в шестнадцатиричном формате");
     return;
    }
  }
  var ext_int = typeof f.ext_int == 'undefined' ? '' : f.ext_int.value;
  var ext_text = typeof f.ext_text == 'undefined' ? '' : f.ext_text.value;

  if(f.fname.value=="" && f.family.value=="" && f.secname.value=="" && f.position.value=="" &&
          f.tab_num.value=="" && f.pass_code.value=="" && n==0 && f.pxflag.checked==false && (ext_int =='' && ext_text==''))
  {erflg=1;alert("Не введено ни одного критерия поиска");return;}

  if(erflg==0)
  {	
//	f.search.value = '7';
		
     f.submit();

   }
}

//// поиск документов
function SearchDocs(f)
{

  var erflg=0;
  var n=f.depart.selectedIndex;

  f.dept_id.value=f.depart.options[n].value;
  //var str=f.family.value+"-"+f.fname.value+"-"+f.secname.value+"-"+f.position.value+"-"+f.depart.options[n].value+"-"+f.tab_num.value+"-"+f.pass_code.value;
  //alert(f.depart.options[n].value);

  if(CheckString(f.fname.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе имени");return;}
 // alert(1);
 if(CheckString(f.family.value)==1)
  //alert(2);
  {erflg=1;alert("Ошибка:недопустимый символ при вводе фамилии");return;}
  //alert(3);
  if(CheckString(f.secname.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе отчества");return;}

  if(CheckString(f.position.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе должности");return;}

  if(CheckString(f.tab_num.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе табельного номера");return;}

  if(CheckString(f.pass_code.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе кода пропуска");return;}

  if(f.pass_code.value!="")
  {
     if(f.pass_code.value.length!=16)
    {  erflg=1;
       alert("Ошибка: код пропуска должен состоять из 16 символов");
       return;
    }
    if(IsHex(f.pass_code.value)==false)
    {
     erflg=1;
     alert("Ошибка: код пропуска должен быть в шестнадцатиричном формате");
     return;
    }
  }
  
/*  
  if(f.fname.value=="" && f.family.value=="" && f.secname.value=="" && f.position.value=="" && f.tab_num.value=="" && f.pass_code.value=="")
  {erflg=1;alert("Не введено ни одного критерия поиска");return;}
*/

  if(erflg==0)
  {	
	
     f.submit();

   }
   
}




 function SearchPXCodes(f)
 {
 var tt=window.document.getElementById("searchForm");

//	 alert(tt.style.display);
	 
	 if (tt.style.display=="block")
	 {
	 tt.style.display="none";
	  }
	  
	  f.is_filtered = 1;
	  tt.submit();
  }

function ShowPxCodeWindow(call)
{
  var url = "pxcodes.php?action=choose";
  if(call != null)
    var url = "pxcodes.php?action=choose&call="+call;
  ShowWindow(url,"Пропуска",700,400,1);
}

function  SelectPxCode(id,num,datein,dateout,pin,bl,guest,admin,doub,auto)
{
   //alert("safsaz");
   //Снимаем флажки если они установлены
   opener.document.aepers.pxblock.checked=false;
   opener.document.aepers.pxadmin.checked=false;
   opener.document.aepers.pxguest.checked=false;
   opener.document.aepers.pxdouble.checked=false;
   opener.document.aepers.pxauto.checked=false;

   opener.document.aepers.pxcode_id.value=id;
   opener.document.aepers.pxcodenum_old.value=num;
   opener.document.aepers.pxcodenum.value=num;
   opener.document.aepers.pxdatein.value=datein;
   opener.document.aepers.pxdateout.value=dateout;
   opener.document.aepers.pincod.value=pin;

   if(bl==1)opener.document.aepers.pxblock.checked=true;
   if(admin==1)opener.document.aepers.pxadmin.checked=true;
   if(guest==1)opener.document.aepers.pxguest.checked=true;
   if(doub==1)opener.document.aepers.pxdouble.checked=true;
   if(auto==1)opener.document.aepers.pxauto.checked=true;

   self.close();
}



function ShowCalendar(CONTROL,START_YEAR,END_YEAR,FORMAT){
//CONTROL  поле в уоторое вернётся дата



ControlToSet = eval(CONTROL);
StartYear = START_YEAR;
EndYear = END_YEAR;
FormatAs = FORMAT;
/*if(theForm.DateFormats.selectedIndex > 0){
FormatAs = theForm.DateFormats.options[theForm.DateFormats.selectedIndex].value
} */
//параметры окна с календарём
var CalWidth=200;
var LEFT=300;
var TOP=300;

var strFeatures = "width=" + CalWidth + ",height=140" + ",left=" + LEFT + ",top=" + TOP;
var CalWindow = window.open("include/HTMLCalendar.htm","Calendar", strFeatures)
CalWindow.focus();
//alert("sfsd");
window.status = "Done";
} //End Function

function SetDate(DATE){
if(ControlToSet){
ControlToSet.value = DATE;
}
ControlToSet = null;
StartYear = null;
EndYear = null;
FormatAs = null;
}

function ChangeFlagPass(el,f)
{
   if(el.checked==true)
   {
       f.pass_code.disabled=true;
       f.flagval.value=1;
   }
   else
   {
     f.pass_code.disabled=false;
     f.flagval.value=0;
   }
  // alert();
}

function DeletePerson(id,family,fname,sname)
{
   //alert(id);alert(family);alert(fname);alert(sname);
   var url="personal.php?action=del&id="+id;
   var msg="Вы действительно хотите удалить "+family.replace('~',' ')+"\ "+fname.replace('~',' ')+"\ "+sname.replace('~',' ');
   //alert(msg);
   if(confirm(msg)==true)
    document.location.href=url;
   else return;
}
function DeleteItem(a,m)
{
   url=a;
   if(confirm(m)==true)
    document.location.href=url;
   else return;
}

function ValidateFilter(f)
{
   var erflg=0;

  if(CheckString(f.fname.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе имени");return;}
  //alert(1);
 if(CheckString(f.family.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе фамилии");return;}
  //alert(3);
  if(CheckString(f.secname.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе отчества");return;}
  if(CheckString(f.position.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе должности");return;}
  //alert(2);

 if(erflg==0)
 {//закоменченое для не древовидной структуры отделов в групповых операциях
   //var n = f.depart.selectedIndex;
  // f.dept_id.value=f.depart.options[n].value;//alert("Департ-"+f.dept_id.value);
       n = f.graph.selectedIndex;
   f.graph_id.value=f.graph.options[n].value;//alert("Graph-"+f.graph_id.value);
       n = f.zone.selectedIndex;
   f.zone_id.value=f.zone.options[n].value;//alert("Zone-"+f.zone_id.value);

   f.selectstring.value="";
   f.submit();
 }
 else {return;}

}
function CheckSmenaForm(f)
{
  var erflg=0;
  if(f.namesm.value=="")
   {erflg=1;alert("У смены должно быть название");f.namesm.focus();return;}
  if(CheckString(f.namesm.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе названии смены");f.namesm.focus();return;}
  if(CheckString(f.start_sm.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе начала смены");f.start_sm.focus();return;}

  if(isTime(f.start_sm.value)==false){f.start_sm.focus();return;}
  if(isTime(f.end_sm.value)==false){f.end_sm.focus();return;}
  if(isTime(f.start_din.value)==false){f.start_din.focus();return;}
  if(isTime(f.end_din.value)==false){f.end_din.focus();return;}
 // alert(f.des)
  if(CheckString(f.descrip.value)==1)
  {erflg=1;alert("Ошибка:недопустимый символ при вводе описания");f.start_sm.focus();return;}


  if(erflg==0)
  {
   f.submit();
  }
}


function ChangeColor(obj,col)
{
   obj.style.background=col;
}
function clearField(id,id1)
{

  var field = document.getElementById(id);
  field.value="";
  if(id1!=null)
  {
    field = document.getElementById(id1);
    field.value="";
  }
}
function cleanNode(dest)
{
  while (dest.firstChild)
    dest.removeChild(dest.firstChild);
}

function PrintPreview(url,w,h,top,left)
{
	params = "menubar=1,";
	params+="width="+w+",";
	params+="height="+h+",";
	params+="resizable=1";
	window.open(url,"Печать",params);
}

// 30.09.2010 - для показа\закрытия формы печати 
function ShowFrm(frm_name)
{

   var el=window.document.getElementById(frm_name);
   el.style.display="block";

}
function CloseFrm(frm_name)
{

   var el=window.document.getElementById(frm_name);
   el.style.display="none";
}


function change_server_time()
{
    if(confirm("Вы действительно хотите изменить время сервера СКУД?"))
    {
        var data2 = {obj:"time", time: $("#time").val()};
        $.ajax({
            url: "change_time.php", 
            type: "POST",
            data: data2,
            dataType: "json",
        success: function (data) {
            var dataObj = eval(data);
            if(dataObj.res==1){
                alert('Команда отправлена, проверьте правильность установленных даты и времени');
            }
            else if(dataObj.res==0){
                alert('Нет прав на выполнение операции');
            }
            else alert('Ошибка выполнения');
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error change_server_time');
         }
        });
        
    }

    
}