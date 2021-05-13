//REPLACED 04.07.07
var req;

//функция для обработки ответов

function ParseXMLResponse(xdoc)
{
    var action = xdoc.getElementsByTagName('action')[0].firstChild.data;
    if(action=="save") ConfirmSaving(xdoc);
    if(action == "del") ConfirmRemove(xdoc);	
}

function processReqChange()
{
 if (req.readyState == 4)
 {
  if (req.status == 200)
   {
     try
     {
	response = req.responseXML.documentElement;	  
        ParseXMLResponse(response);
     }
     catch(e)
      {
        alert("Ошибка: Неверный ответ от сервера");
        ClearIndicators();
      }
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
function ClearIndicators()
{
 RemoveIndicator("pers_list","indicator");
 RemoveIndicator("statusbar","indicator");
 RemoveIndicator("doc_list","indicator");

}




