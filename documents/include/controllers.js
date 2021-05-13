/*REPLACED 08.02.08 */
function CheckString(str)
{
   //alert("dsf");
  if(str.indexOf("'",0)!=-1)
  return 1;
  if(str.indexOf("\"",0)!=-1)
  return 1;
  if(str.indexOf("<",0)!=-1)
  return 1;
  if(str.indexOf(">",0)!=-1)
  return 1;
}
/////////////////////////////////////////////////////////////////////////////////
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
//////////////////////////////////////////////////////////////////////////////////

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
