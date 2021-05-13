var req;
//REPLACED 14.08.2007
//функции парсинга ответов
function AddTurn(f,act)
{
    var action=null;
    if(act == "add")action = "INSERT";
    if(act == "save") action = "UPDATE";
    if(f.tnum.value==""){alert("У турникета должен быть номер");f.tnum.focus();return;}
    if(isNaN(f.tnum.value)==true){alert("Номер турникета должен быть числовым");f.tnum.focus();return;}
    if(f.nameturn.value == ""){alert("У турникета должно быть название");f.nameturn.focus();return;}
    if(CheckString(f.nameturn.value) == 1){alert("Недопустимый символ при вводе названия");f.nameturn.focus();return;}
    if(CheckString(f.turndesc.value) == 1){alert("Недопустимый символ при вводе описания");f.turndesc.focus();return;}
    if(f.in_terr.selectedIndex == 0){alert("Не указана внешняя территория");return;}
    if(f.out_terr.selectedIndex == 0){alert("Не указана внутренняя территория");return;}
    var tg;
    if(f.turngroup.selectedIndex == 0)tg="NULL";else tg=f.turngroup.value;

    var status=null;
    if(f.flag_block.checked == 1)status = "1"; else status = "0";
    if(f.flag_terr_in.checked == 1)status += "1"; else status += "0";
    if(f.flag_terr_out.checked == 1)status += "1"; else status += "0";

    status += "00000";

    var sb = document.getElementById("statusbar");
    sb.style.display = "block";

    var data1 = {obj:"turn",
                act:action,
                tid:f.tid.value,
                tnum:f.tnum.value,
                tname:f.nameturn.value,
                turndesc:f.turndesc.value,
                turngroup:tg,
                status:status,
                turn_type:f.turn_type.value,
                reader_in:f.reader_in.value,
                reader_out:f.reader_out.value,
                in_terr:f.in_terr.value,
                out_terr:f.out_terr.value
            };
    $.ajax({
        url: "asinc.php", 
        type: "POST",
        data: data1,
        dataType: "json",
        success: function (data) {
            var dataObj = eval(data);
            if(dataObj.action==='INSERT'){
		if(dataObj.id=='-1'){
			alert(dataObj.desc);
			sb.style.display = "none";
			return;
		}
                var stack = document.getElementById("stack_turn");

                var el = document.createElement("div");
                    el.id="stack"+dataObj.id;
                    el.className="listitem";
                    el.name="item";
                    el.style.cursor="pointer";
                var im=document.createElement("img");
                    im.src="buttons/left.gif";
                    im.style.margin = 1+"px";
                    im.onclick=function(){
                        SetTurnInGroup(el.id);
                    };
                el.appendChild(im);

                    im=document.createElement("img");
                    im.src="buttons/edit.gif";
                    im.style.margin = 1+"px";
                    im.height = 15;
                    im.name = "editbut";
                    im.onclick=function(){
                        DefinedAction(this,el.id.substr(5,el.id.length));
                    };
                    el.appendChild(im);

                    im=document.createElement("img");
                    im.src="buttons/remove.gif";
                    im.style.margin = 1+"px";
                    im.height = 15;
                    im.onclick=function(){
                        RemoveTurn(el.id.substr(5,el.id.length));
                    };
                    el.appendChild(im);
                    var t=document.createTextNode(dataObj.name + "(#"+ dataObj.num + ")");
                    el.appendChild(t);
                    stack.insertBefore(el,stack.childNodes[0]);

                if(dataObj.tg>0)
                {
                    var turngroup=document.getElementById("checkedflag");
                    turngroup.value = "list-"+dataObj.tg;
                    var turn_id = "stack"+dataObj.id;
                    SetTurnInGroup(turn_id);
                }
		sb.style.display = "none";
                ShowCloseModalWindow("addturn",1);
            }
            else{
                var f = document.getElementById("addturnfrm");
                f.tid.value = dataObj.id;
                f.tnum.value = dataObj.num;
                f.nameturn.value = dataObj.name;
                var tg = dataObj.tg;

                f.turndesc.value = dataObj.desc;
                var st = dataObj.status;
                if(st.substring(0,1) == 1)f.flag_block.checked = 1;
                if(st.substring(1,2) == 1)f.flag_terr_in.checked = 1;
                if(st.substring(2,3) == 1)f.flag_terr_out.checked = 1;

                for(var i =0; i < f.in_terr.options.length;i++)
                {
                    var it = f.in_terr.options[i];
                    if(it.value == dataObj.interr)
                        it.selected = 1;
                }
                for(var i =0; i < f.out_terr.options.length;i++)
                {
                    var it = f.out_terr.options[i];
                    if(it.value == dataObj.outterr)
                        it.selected = 1;
                }
                for(var i =0; i < f.turn_type.options.length;i++)
                {
                    var it = f.turn_type.options[i];
                    if(it.value == dataObj.turn_type)
                        it.selected = 1;
                }
                for(var i =0; i < f.reader_in.options.length;i++)
                {
                    var it = f.reader_in.options[i];
                    if(it.value == dataObj.reader_in)
                        it.selected = 1;
                }
                for(var i =0; i < f.reader_out.options.length;i++)
                {
                    var it = f.reader_out.options[i];
                    if(it.value == dataObj.reader_out)
                        it.selected = 1;
                }
                for(var i =0; i < f.turngroup.options.length;i++)
                {
                    var it = f.turngroup.options[i];
                    if(it.value == tg) it.selected = 1;
                }

                var element=document.getElementById("stack"+dataObj.id);
                var t=document.createTextNode(dataObj.name);
                    element.replaceChild(t,element.childNodes[3]);
                sb.style.display = "none";
                ShowCloseModalWindow("addturn",1);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            alert('error AddTurn');
        }
    });
}
function EditTurn(id)
{
    ClearTurnForm();
    var sb = document.getElementById("statusbar");
    sb.style.display = "block";

    var data1 = {obj:"turn",act:"edit",tid:id};
    $.ajax({
        url: "asinc.php", 
        type: "POST",
        data: data1,
        dataType: "json",
        success: function (data) {
            var dataObj = eval(data);
            var f = document.getElementById("addturnfrm");
            f.tid.value = dataObj.id;
            f.tnum.value = dataObj.num;
            f.nameturn.value = dataObj.name;
            var tg = dataObj.tg;

            f.turndesc.value = dataObj.desc;
            var st = dataObj.status;
            if(st.substring(0,1) == 1)f.flag_block.checked = 1;
            if(st.substring(1,2) == 1)f.flag_terr_in.checked = 1;
            if(st.substring(2,3) == 1)f.flag_terr_out.checked = 1;

            for(var i =0; i < f.in_terr.options.length;i++)
            {
                var it = f.in_terr.options[i];
                if(it.value == dataObj.interr)
                    it.selected = 1;
            }
            for(var i =0; i < f.out_terr.options.length;i++)
            {
                var it = f.out_terr.options[i];
                if(it.value == dataObj.outterr)
                    it.selected = 1;
            }
            for(var i =0; i < f.turn_type.options.length;i++)
                {
                    var it = f.turn_type.options[i];
                    if(it.value == dataObj.turn_type)
                        it.selected = 1;
                }
            for(var i =0; i < f.reader_in.options.length;i++)
            {
                var it = f.reader_in.options[i];
                if(it.value == dataObj.reader_in)
                    it.selected = 1;
            }
            for(var i =0; i < f.reader_out.options.length;i++)
            {
                var it = f.reader_out.options[i];
                if(it.value == dataObj.reader_out)
                    it.selected = 1;
            }
            for(var i =0; i < f.turngroup.options.length;i++)
            {
                var it = f.turngroup.options[i];
                if(it.value == tg) it.selected = 1;
            }

            var element=document.getElementById("stack"+dataObj.id);
            var t=document.createTextNode(dataObj.name);
                element.replaceChild(t,element.childNodes[3]);
            sb.style.display = "none";
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error EditTurn');
        }
    });
}
function RemoveTurn(id)
{
    var data1 = {obj:"turn",act:"REMOVE",tid:id};
    $.ajax({
        url: "asinc.php", 
        type: "POST",
        data: data1,
        dataType: "json",
        success: function (data) {
            var dataObj = eval(data);
            var stack = document.getElementById("stack_turn");
            var remEl = document.getElementById("stack"+dataObj.id);
            stack.removeChild(remEl);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error RemoveTurn');
        }
    });
}
function DefinedAction(obj,id)
{
    var f = document.getElementById("addturnfrm");
    if(obj.name=="addbut")
    {  
        f.addturnbut.onclick = function(){AddTurn(f,"add");}
        ClearTurnForm();
    }
    else if(obj.name == "editbut")
    {
        f.addturnbut.onclick = function(){AddTurn(f,"save");}
        EditTurn(id);
    }
}


function ParseGraph(xdoc)
{
   var action = xdoc.getElementsByTagName('action')[0].firstChild.data;
   if(action == "info")
   {
      var wnd = document.getElementById("info");

      //wnd.removeChild(wnd.childNodes[0]);
      var size = wnd.childNodes.length;
      for(var i=0;i<size;i++)
      {
        wnd.removeChild(wnd.childNodes[i]);
      }

      var res = xdoc.getElementsByTagName('item');
      if(res)
      {
       var i=0;
       for(i=0;i<res.length;i++)
       {
         var text = "Смена - "+res[i].getAttributeNode("name").value+";";
             text += "&nbsp;Допуск - "+res[i].getAttributeNode("dopusk").value+";";
             text += "&nbsp;Зона - "+res[i].getAttributeNode("zone").value+'<br>';
         wnd.innerHTML+=text;
         //var t = document.createTextNode(text);
            // wnd.appendChild(t);
       }
       if(i==0) wnd.innerHTML+="Данному графику ничего не назначено";

      }

   }
}

function ShowError(xdoc)
{
  var sb = document.getElementById("statusbar");
  var error = xdoc.getElementsByTagName('text')[0].firstChild.data;
  alert(error);
  sb.style.display = "none";
}
//функция для обработки ответов
function ParseXMLResponse(xdoc)
{
    var sb = document.getElementById("statusbar");
    var obj = xdoc.getElementsByTagName('object')[0].firstChild.data;

    if(obj == "tabrep"){} //ParseReportSettings(xdoc);


    if(obj == "error")
    {
       ShowError(xdoc);
    }
    if(obj == "graph")
    {
       ParseGraph(xdoc);
    }
    sb.style.display = "none";
}

function processReqChange()
{
 if (req.readyState == 4)
 {
  if (req.status == 200)
   {
    // alert(req.responseText);
      response = req.responseXML.documentElement;
       ParseXMLResponse(response);
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
    alert("we are here");
    //alert('Из loadXMLDoc:'+url);
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
function ShowPersonalInfo()
{
  alert("ddddddddd");
}