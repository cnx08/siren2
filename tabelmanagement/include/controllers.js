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
/*
This function return string which contain
values of selected elements of list.
Oject of list transfers in first parameter
Delimiter of values in string is ","
*/

function getSelectedValues(select)
{
	var options = select.options;
	var str = "";
	for (var i = 0; i < options.length; i++)
	     if(options[i].selected) str += options[i].value + ",";

   return str.substr(0,str.length-1);
}


function RunDTS(number)
{
  var url="../dtsexec.php?action="+number;
  var param="width=300,height=5,resizable=no,scrollbars=no,menubar=no";
  window.open(url,"",param);
}