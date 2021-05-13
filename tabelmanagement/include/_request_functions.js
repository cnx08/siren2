var req;


//функция для обработки ответов

function ParseXMLResponse(xdoc)
{
    var action = xdoc.getElementsByTagName('action')[0].firstChild.data;
    if(action=="filter")
       ParseFilterResult(xdoc);
    if(action=="gettime")
       ParseTimeResult(xdoc);
    if(action=="savetabel")
      ConfirmSaving(xdoc);
    if(action == "tocount")
    	onTocountResponse(xdoc);
}

function processReqChange()
{
 if (req.readyState == 4)
 {
  if (req.status == 200)
   {
     //alert(req.responseText);
     //try
   //  {
      response = req.responseXML.documentElement;
	  ParseXMLResponse(response);
     //}
    // catch(e)
      //{
     //   alert("Ошибка: Неверный ответ от сервера");
      //  ClearIndicators();
     //}
    }
    else
     {
       alert("Проблемы с загрузкой Xml данных" + req.statusText);
    }
  }
}
function loadXMLDoc(url,param)
{
    url=url+param;
    // branch for native XMLHttpRequest object
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = processReqChange;
        req.open("POST", url, true);
        req.setRequestHeader("Content-Type","applicaion/x-www-form-urlencoded");
        req.send(param);
    // branch for IE/Windows ActiveX version
    } else if (window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {
            req.onreadystatechange = processReqChange;
            req.open("POST", url, true);
            req.setRequestHeader("Content-Type","applicaion/x-www-form-urlencoded");
            req.send(param);
        }
    }
}
function loadRecalcStatus(url,param)
{
    url=url+param;
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = processReqChangeSt;
        req.open("POST", url, true);
        req.setRequestHeader("Content-Type","applicaion/x-www-form-urlencoded");
        req.send(param);
    // branch for IE/Windows ActiveX version
    } else if (window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {
            req.onreadystatechange = processReqChangeSt;
            req.open("POST", url, true);
            req.setRequestHeader("Content-Type","applicaion/x-www-form-urlencoded");
            req.send(param);
        }
    }
}
function processReqChangeSt()
{
 if (req.readyState == 4)
 {
  if (req.status == 200)
   {
      response = req.responseXML.documentElement;
	  RemoveIndicator("status_bar","indicator");
	  alert('Пересчёт выполнен');
      ParseXMLResponse(response);
    }
    else
     {
       alert("Проблемы с загрузкой Xml данных" + req.statusText);
    }
  }
}
function ClearIndicators()
{

 RemoveIndicator("list","indicator");
 RemoveIndicator("statusbar","indicator");
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
