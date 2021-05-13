var LAST =0;
var SMENA = new Array();
var DOPUSK = new Array();
var ZONA = new Array();
var TG_REG = new Object();
var REG_TIME ="000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";//
              
    // создание режима
    // алерты на сохранение с пустыми полями или чё-нить типа того
    
function setMaskLevel(z){
    $("#mask").css("z-index",z);
}


function showAddGraphWind(){
  $("#addgraph").show();
  setMaskLevel(49);
  getSmenaList();
  getDopuskList();
  getZoneList();
};
function showAddDopuskWind(){
    $("#adddopusk").show();
    setMaskLevel(64);
    var data1 = {obj:"getTurnGroups"};
    $.ajax({
     url: "asinc.php", 
     type: "POST",
     data: data1,
     dataType: "json",
    success: function (data) {
         $.each(data,function(key, value) {
            $('<option/>', {
              val:  key,
              text: value
            }).appendTo('#tg_select');
            TurnGrMouseOver();
          });
    },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error showAddDopuskWind');
     }
    });
     var data2 = {obj:"getReg"};
    $.ajax({
     url: "asinc.php", 
     type: "POST",
     data: data2,
     dataType: "json",
    success: function (data) {
        $.each(data,function(key, value) {
            $('<option/>', {
              val:  key,
              text: value
            }).appendTo('#reg_select');
            RegMouseOver();
          });
    },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error getReg');
     }
    });
    
}
function showAddSmenaWind(){
    $("#addsm").show();
    setMaskLevel(64);
}
function showAddZoneWind(){
    setMaskLevel(64);
    var data1 = {obj:"loadterr"};
    $.ajax({
     url: "asinc.php", 
     type: "POST",
     data: data1,
     dataType: "json",
    success: function (data) {
         $.each(data,function(key, value) {
                      $('<option/>', {
                        val:  key,
                        text: value
                      }).appendTo('#terr');
          });
        },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error showAddZoneWind');
     }
    });
    
    $("#addzonediv").show();
}
function showAddRegWind(){//edit
    $("#addreg").show();
    setMaskLevel(69);
    SetRegEmptyFlags(); 
};


function closeAddSmenaWind(){
    $("#addsm").hide();
    $("#namesm").val('');
    $("#start_sm").val('00:00');
    $("#end_sm").val('00:00');
    $("#start_din").val('00:00');
    $("#end_din").val('00:00');
    $("#descrip").val('');
    setMaskLevel(49);
};
function closeAddDopuskWind(){
    $("#adddopusk").hide();
    $("#tg_select > *").remove();
    $("#reg_select > *").remove(); 
    $("#tg_reg_list > *").remove();
    TG_REG = {};
    $("#namedop").val('');
    $("#outcheck").prop("checked",false);
    $("#incheck").prop("checked",false);
    setMaskLevel(49);
    
};
function closeAddZoneWind(){
    $("#addzonediv").hide();
    $("#terr > *").remove();
    $("#tlist > *").remove();
    $("#namezone").val("");
    $("#descrzone").val("");
    setMaskLevel(49);
    
};
function closeAddGraphWind(){
    $("#addgraph").hide();
    //kill all kids inda house
    $("#zoadd > *").remove();
    $("#dopadd > *").remove(); 
    $("#smadd > *").remove(); 
    $("#main > *").remove();
    $("#gname").val('');
    $("#descript").val('');
    setMaskLevel(-1);
};
function closeAddRegWind(){
    $("#addreg").hide();
    $("#reg_time > *").remove();
    $("#namereg").val("");
    $("#stime").val('00:00');
    $("#ftime").val('00:00');
    REG_TIME ="000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
    setMaskLevel(60); 
};


function DopuskMouseOver(){
    $("#dopadd option").mouseover(function (e)
    {
        var $target = $(e.target); 
        var id = $target.val();
        if (id > 0)
        {
            var xoffset = $("#dopadd").width();
            var x = $target.offset().left;
            var y = $target.offset().top;
            var text= $target.text();
            showDopuskInfo(id,text,x,y,xoffset);
        }
    });
    $("#dopadd option").mouseleave(function (e)
    {
        $("#info_wnd").hide();
    });
}
function SmenaMouseOver(){
     $("#smadd option").mouseover(function (e)
    {
        var $target = $(e.target); 
        var id = $target.val();
        if (id > 0)
        {
            var xoffset = $("#smadd").width();
            var x = $target.offset().left;
            var y = $target.offset().top;
            var text= $target.text();
            showSmenaInfo(id,text,x,y,xoffset);
        }
    });
    $("#smadd option").mouseleave(function (e)
    {
        $("#info_wnd").hide();
    });
}
function ZoneMouseOver(){
    $("#zoadd option").mouseover(function (e)
    {
        var $target = $(e.target); 
        var id = $target.val();
        if (id > 0)
        {
            var xoffset = $("#zoadd").width();
            var x = $target.offset().left;
            var y = $target.offset().top;
            var text= $target.text();
            showZoneInfo(id,text,x,y,xoffset);
        }
    });
    $("#zoadd option").mouseleave(function (e)
    {
        $("#info_wnd").hide();
    });
}
function TurnGrMouseOver(){
    $("#tg_select option").mouseover(function (e)
    {
        var $target = $(e.target); 
        var id = $target.val();
        if (id > 0)
        {
            var xoffset = $("#tg_select").width();
            var x = $target.offset().left;
            var y = $target.offset().top;
            var text= $target.text();
            showTurnGrInfo(id,text,x,y,xoffset);
        }
    });
    $("#tg_select option").mouseleave(function (e)
    {
        $("#info_wnd").hide();
    });
}
function RegMouseOver(){
    $("#reg_select option").mouseover(function (e)
    {
        var $target = $(e.target); 
        var id = $target.val();
        if (id > 0)
        {
            var xoffset = $("#reg_select").width();
            var x = $target.offset().left;
            var y = $target.offset().top;
            var text= $target.text();
            showRegInfo(id,text,x,y,xoffset);
        }
    });
    $("#reg_select option").mouseleave(function (e)
    {
        $("#info_wnd").hide();
    });
}


function AddDept(f){

    if(f.namedept.value == "")
        {alert("У отдела должно быть название");f.namedept.focus();return;}
    if(CheckString(f.namedept.value) == 1)
        {alert("Недопустимый символ при вводе названия");f.namedept.focus();return;}
        
    var data1 = {obj:"adddept",deptname:f.namedept.value,idparent:f.in_dept.value};
    $.ajax({
     url: "asinc.php", 
     type: "POST",
     data: data1,
     dataType: "json",
    success: function (data) {
        var dataObj = eval(data);
        $('#deptName').val(dataObj.dept_name);
        $('#id_dept').val(dataObj.id_dept);
        $("#adddept").hide();
        //alert('success AddDept');
    },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error AddDept');
     }
    });
};
function AddSmena(f){
    //проверочка (как и в functions.js, ток без сабмита, но с аяксом и прикручиванием новой смены в селект)
    var erflg=0;
    if(f.namesm.value==="")
     {erflg=1;alert("У смены должно быть название");f.namesm.focus();return;}
    if(CheckString(f.namesm.value)===1)
    {erflg=1;alert("Ошибка:недопустимый символ при вводе названии смены");f.namesm.focus();return;}
    if(CheckString(f.start_sm.value)===1)
    {erflg=1;alert("Ошибка:недопустимый символ при вводе начала смены");f.start_sm.focus();return;}

    if(isTime(f.start_sm.value)===false){f.start_sm.focus();return;}
    if(isTime(f.end_sm.value)===false){f.end_sm.focus();return;}
    if(isTime(f.start_din.value)===false){f.start_din.focus();return;}
    if(isTime(f.end_din.value)===false){f.end_din.focus();return;}
    if(CheckString(f.descrip.value)===1)
    {erflg=1;alert("Ошибка:недопустимый символ при вводе описания");f.start_sm.focus();return;}

    if(erflg===0)
    {  
        var data1 = {obj:"addSmena",start_sm:f.start_sm.value,end_sm:f.end_sm.value,start_din:f.start_din.value,end_din:f.end_din.value,namesm:f.namesm.value,descrip:f.descrip.value};
        $.ajax({
            url: "asinc.php", 
            type: "POST",
            data: data1,
            dataType: "json",
        success: function (data) {
            var dataObj = eval(data);
            $('<option/>', {
              val:  dataObj.id_sm,
              text: dataObj.sm_name
            }).appendTo('#smadd');
            SMENA.push(dataObj.id_sm+"~"+dataObj.sm_name);
            $("#smadd").val(dataObj.id_sm);
            closeAddSmenaWind();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error AddSmena');
         }
        });
    }
};
function AddZone(){
        var data1 = {obj:"addZone",name:$("#namezone").val(),discr:$("#descrzone").val(),terr_arr:$("#selstr").val()};
        $.ajax({
         url: "asinc.php", 
         type: "POST",
         data: data1,
         dataType: "json",
        success: function (data) {
            var dataObj = eval(data);
            $('<option/>', {
              val:  dataObj.id_z,
              id:  "z_"+dataObj.id_z,
              text: dataObj.z_name
            }).appendTo('#zoadd');
           ZONA.push(dataObj.id_z+"~"+dataObj.z_name);
           $("#zoadd").val(dataObj.id_z);
           closeAddZoneWind();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error AddZone');
         }
        });
    
};
function AddTerr(f){
    var h = f.ter.selectedIndex;
    var n = f.ter.options[h].value;
    if(n==0){alert("Не выбрана территория");return;}
    if(CompareString(f.selstr.value,n,",")==true)
    {alert("Выбранная территоря уже есть в списке");return;}
    f.terr_id.value=n;


    if(f.selstr.value.length==0)
       f.selstr.value+=n;
    else
       f.selstr.value+=","+n;

    var terr="   "+f.ter.options[n].text;
    var l=document.getElementById("tlist");
    var newrec=document.createElement("div");

    l.appendChild(newrec);
    newrec.className="listItem";
    newrec.id=n;
    var but=document.createElement("button");
    newrec.appendChild(but);

    var text=document.createTextNode(terr);
    newrec.appendChild(text);
    but.className="delbut";
    but.innerHTML="-";
    but.onclick=function(){
        var i=document.getElementById(newrec.id);
        l.removeChild(i);
        f.selstr.value=DelSelId(f.selstr.value,n);
    }
}
function AddDopusk(){
    if($("#namedop").val()==="")
     {erflg=1;alert("У смены должно быть название");f.namesm.focus();return;}
    var dop_status='00000';
    $("#outcheck").prop("checked")===true ? dop_status="1"+dop_status:  dop_status="0"+dop_status;
    $("#incheck").prop("checked")===true ? dop_status="01"+dop_status:  dop_status="00"+dop_status;
    var data1 = {obj:"addDopusk",name:$("#namedop").val(),tgreg:TG_REG, dop_status:dop_status};
    $.ajax({
        url: "asinc.php", 
        type: "POST",
        data: data1,
        dataType: "json",
    success: function (data) {
        var dataObj = eval(data);
        $('<option/>', {
            val:  dataObj.id_d,
            text: dataObj.d_name
        }).appendTo('#dopadd');
        DopuskMouseOver();
        DOPUSK.push(dataObj.id_d+"~"+dataObj.d_name);
        $("#dopadd").val(dataObj.id_d);
        closeAddDopuskWind();

    },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error AddDopusk');
    }
    });
};

function AddReg(){
    if(REG_TIME.length!==96){
        alert ("Ошибка создания режима. Неверное число бит в строке кода режима.");
    }
    else{
        var data1 = {obj:"addReg",name:$("#namereg").val(),reg_code:REG_TIME};
        $.ajax({
         url: "asinc.php", 
         type: "POST",
         data: data1,
         dataType: "json",
        success: function (data) {
            var dataObj = eval(data);
            $('<option/>', {
              val:  dataObj.id_r,
              id:  "z_"+dataObj.id_r,
              text: dataObj.r_name
            }).appendTo('#reg_select');
            RegMouseOver()
            $("#reg_select").val(dataObj.id_r);
            closeAddRegWind();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error AddReg');
         }
        });
    }
};

function AddItem(f){
    if(f.smadd.selectedIndex==0){alert("не выбрана смена");return;}
    if(f.dopadd.selectedIndex==0){alert("не выбран допуск");return;}
    if(f.zoadd.selectedIndex==0){alert("не выбрана рабочая зона");return;}

    LAST=parseInt(f.count.value);
    LAST+=1;
    f.count.value=LAST;

    var parent=document.getElementById("main");
    var newel=document.createElement("div");
        newel.id="item"+LAST;
        newel.className="listitem";
    parent.appendChild(newel);

    var button=document.createElement("input");
        button.className="delbut";
		button.type="button";
        button.style.marginRight = 2+"px";
        button.style.height=20+"px";
        button.value="-";
        button.onclick=function(){
            RemoveItem(newel.id,f);
        };
    newel.appendChild(button);
    CreateSelectExt(SMENA,newel.id,"select",1,"smena"+LAST,f.smadd.selectedIndex);
    CreateSelectExt(DOPUSK,newel.id,"select",1,"dopusk"+LAST,f.dopadd.selectedIndex);
    CreateSelectExt(ZONA,newel.id,"select",1,"zona"+LAST,f.zoadd.selectedIndex);

}        
function RemoveItem(itemid,f){
     var el=document.getElementById(itemid);
     var parent=document.getElementById("main");
     parent.removeChild(el);

  }
function AddTgroupReg(){
    if ($("#tg_select").val()>0 && $("#reg_select").val()>0){
        TG_REG[""+$("#tg_select").val()]=""+$("#reg_select").val();//добавляем в объект, то что улетит аяксом

        $('<div/>', {
              id:  "tg_"+$("#tg_select").val(),
              class: "listItem"
            }).appendTo('#tg_reg_list');
         $('<button/>', {
              text:  "-",
              class: "delbut",
              onclick: "DelTgroupReg("+$("#tg_select").val()+")"
            }).appendTo('#'+"tg_"+$("#tg_select").val());
        $('#'+"tg_"+$("#tg_select").val()).append($("#tg_select option:selected").text()+' - '+$("#reg_select option:selected").text())
        $("#tg_select option:selected").attr('disabled','disabled');    
        $("#tg_select").val(0);
    }
}
function DelTgroupReg(id){
    delete TG_REG["\""+id+"\""];
    $('#'+"tg_"+id).remove();
    $("#tg_select option[value=" + id + "]").removeAttr('disabled');
}


function getSmenaList(){
    var data1 = {obj:"addPers",act:"getSmena"};
    $.ajax({
     url: "asinc.php", 
     type: "POST",
     data: data1,
     dataType: "json",
     success: function (data) {
            $.each(data,function(key, value) {
              SMENA.push(key.substring(2)+"~"+value);
              $('<option/>', {
                val:  key.substring(2),
                text: value
              }).appendTo('#smadd');
            });
            SmenaMouseOver();
        },
      error: function (xhr, ajaxOptions, thrownError) {
        alert('error getSmenaList');

      }
     });
};
function getDopuskList(){
            var data1 = {obj:"addPers",act:"getDopusk"};
            $.ajax({
             url: "asinc.php", 
             type: "POST",
             data: data1,
             dataType: "json",
             success: function (data) {
                    $.each(data,function(key, value) {
                      DOPUSK.push(key.substring(2)+"~"+value);
                      $('<option/>', {
                        val:  key.substring(2),
                        text: value
                      }).appendTo('#dopadd');
                    });
                    DopuskMouseOver();
                },
              error: function (xhr, ajaxOptions, thrownError) {
                alert('error getDopuskList');
                
              }
             });
        };     
function getZoneList(){
    var data1 = {obj:"addPers",act:"getZone"};
    $.ajax({
     url: "asinc.php", 
     type: "POST",
     data: data1,
     dataType: "json",
     success: function (data) {
            $.each(data,function(key, value) {
              ZONA.push(key.substring(2)+"~"+value);
              $('<option/>', {
                id: key,
                val:  key.substring(2),
                text: value
              }).appendTo('#zoadd');
            });
            ZoneMouseOver();                 
        },
      error: function (xhr, ajaxOptions, thrownError) {
        alert('error getZoneList');

      }
     });
};

 
 function getSmenaData(){
 	//alert(this.req.responseText);
 	var xdoc = this.req.responseXML.documentElement;
 	var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
        table+="<tr  class=client>";
        table+="<td align=center bgcolor=silver>Начало</td>";
        table+="<td align=center bgcolor=silver>Конец</td>";
        table+="<td align=center bgcolor=silver>Начало обеда</td>";
        table+="<td align=center bgcolor=silver>Конец обеда</td>";
        table+="</tr>";

    var res = xdoc.getElementsByTagName("item");
    if(res)
    {
        table+="<tr class=clientText>";
        table+="<td align=center>"+res[0].getAttributeNode("start_sm").value+"</td>";
        table+="<td align=center>"+res[0].getAttributeNode("end_sm").value+"</td>";
        table+="<td align=center>"+res[0].getAttributeNode("start_din").value+"</td>";
        table+="<td align=center>"+res[0].getAttributeNode("end_din").value+"</td>";
        table+="</tr>";
    }
    table+="</table>";
 	this.object.wnd.client.innerHTML+=table;
 	this.object = null;
        $("#info_wnd").css("z-index",60);
 }
 function getDopuskData(){
 	//alert(this.req.responseText);
 	var xdoc = this.req.responseXML.documentElement;
 	var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
        table+="<tr class=client>";
        table+="<td align=center bgcolor=silver>Группа турникетов</td>";
        table+="<td align=center bgcolor=silver>Режим</td>";
        table+="</tr>";

    var res = xdoc.getElementsByTagName("item");
    if(res)
      {
       var i=0;
       for(i=0;i<res.length;i++)
       {
         table+="<tr class=clientText>";
         table+="<td align=center ><span id=tg_"+res[i].getAttributeNode("tg_id").value+">"+res[i].getAttributeNode("tg_name").value+"</span></td>";
         table+="<td align=center ><span id=reg_"+res[i].getAttributeNode("reg_id").value+">"+res[i].getAttributeNode("reg_name").value+"</span></td>";

         table+="</tr>";
       }
       if(i==0) table+="<tr class=client><td>Данному допуску ничего не назначено</td></tr>";

      }
    table+="</table>";
 	this.object.wnd.client.innerHTML+=table;
 	this.object = null;
        $("#info_wnd").css("z-index",60);
 }
 function getZonaData() {
 	//alert(this.req.responseText);
 	var xdoc = this.req.responseXML.documentElement;
 	var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
        table+="<tr class=client>";
        table+="<td align=center bgcolor=silver>Территории</td>";
        table+="</tr>";

    var res = xdoc.getElementsByTagName("item");
    if(res)
      {
       var i=0;
       for(i=0;i<res.length;i++)
       {
         table+="<tr class=clientText>";
         table+="<td align=center >"+res[i].getAttributeNode("terr").value+"</td>";
         table+="</tr>";
       }
       if(i==0) table+="<tr class=client><td>Данной зоне ничего не назначено</td></tr>";

      }
    table+="</table>";
 	this.object.wnd.client.innerHTML+=table;
 	this.object = null;
        $("#info_wnd").css("z-index",60);
 }
 function getTurnGrData() {
 	//alert(this.req.responseText);
 	var xdoc = this.req.responseXML.documentElement;
 	var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
        table+="<tr class=client>";
        table+="<td align=center bgcolor=silver>Точка прохода</td>";
        table+="<td align=center bgcolor=silver>Номер</td>";
        table+="</tr>";

    var res = xdoc.getElementsByTagName("item");
    
    if(res)
    {
        var i=0;
       for(i=0;i<res.length;i++)
       {
            table+="<tr class=clientText>";
            table+="<td align=center>"+res[i].getAttributeNode("name").value+"</td>";
            table+="<td align=center>"+res[i].getAttributeNode("num").value+"</td>";
            table+="</tr>";
        }
    }
    table+="</table>";
 	this.object.wnd.client.innerHTML+=table;
 	this.object = null;
        $("#info_wnd").css("z-index",70);
 }
 function getRegData() {
    var xdoc = this.req.responseXML.documentElement;
    var table = "<table border=0 width=100% height=100% cellpadding=1 cellspacing=1>";
    table+="<tr class=client>";
    table+="<td align=center bgcolor=silver>Режим</td>";
    table+="</tr>";

    var res = xdoc.getElementsByTagName("item");
    if(res)
    {
        table+="<tr class=clientText>";
        table+="<td align=center>";
        
        var rej_code = res[0].getAttributeNode("rej_code").value;
        table+=' <div>';
        for(j=0;j<12;j++)
        {
            table+='<div class="timelineajax" style="background-color:#f5f5f5;">'+j+'</div>';
        }
        for(j=0;j<12;j++)
        {
            table+='<div class="timebasesubpanelajax">';
            for(k=0;k<4;k++)
            {
                position=(j*4)+k;
                currentbyte=rej_code.substr(position,1);
                if(currentbyte==='0')
                    bgcolor='white';
                else
                    bgcolor='green';

                table+='<div  class="timebaseitemajax" style="background-color:'+bgcolor+'" ></div>';
            }
            table+='</div>';
        }
        table+='</div>';
        table+=' <div>';
        for(j=12;j<24;j++)
        {
            table+='<div class="timelineajax" style="background-color:#f5f5f5;">'+j+'</div>';
        }
        for(j=12;j<24;j++)
        {
            table+='<div class="timebasesubpanelajax">';
            for(k=0;k<4;k++)
            {
                position=(j*4)+k;
                currentbyte=rej_code.substr(position,1);
                if(currentbyte==='0')
                    bgcolor='white';
                else
                    bgcolor='green';

                table+='<div  class="timebaseitemajax" style="background-color:'+bgcolor+'" ></div>';
            }
            table+='</div>';
        }
        table+='</div>';
        table+="</td>";
        table+="</tr>";
    }
    table+="</table>";
    this.object.wnd.client.innerHTML+=table;
    this.object = null;
    $("#info_wnd").css("z-index",70);
 }
 
 
 function showSmenaInfo(id,text,x,y,xoffset) {
    if(id > 0)
    {
        var infoWnd = new Window.poupWindow("info_wnd",y,x,xoffset,-80,400,0,"window",text.replace("-","\ "));
        
        infoWnd.Show();
        var net = new Net.ContentLoader("asinc.php",getSmenaData,Error,"POST","obj=getSmena&id="+id);
        net.object = infoWnd;
    }
 }
 function showDopuskInfo(id,text,x,y,xoffset){
    if(id > 0)
    {
        var infoWnd = new Window.poupWindow("info_wnd",y,x,xoffset,-80,400,0,"window",text.replace("-","\ "));
        
        infoWnd.Show();
        var net = new Net.ContentLoader("asinc.php",getDopuskData,Error,"POST","obj=getDopusk&id="+id);
        net.object = infoWnd;
    }
  }
 function showZoneInfo(id,text,x,y,xoffset) {
    if(id > 0)
    {
        var infoWnd = new Window.poupWindow("info_wnd",y,x,xoffset,-80,400,0,"window",text.replace("-","\ "));

        infoWnd.Show();
        var net = new Net.ContentLoader("asinc.php",getZonaData,Error,"POST","obj=getZona&id="+id);
        net.object = infoWnd;
    }
 } 
 function showTurnGrInfo(id,text,x,y,xoffset) {
    if(id > 0)
    {
        var infoWnd = new Window.poupWindow("info_wnd",y,x,xoffset,-80,400,0,"window",text.replace("-","\ "));
        
        infoWnd.Show();
        var net = new Net.ContentLoader("asinc.php",getTurnGrData,Error,"POST","obj=getTurn&id="+id);
        net.object = infoWnd;
    }
 }
 function showRegInfo(id,text,x,y,xoffset){
    if(id > 0)
    {
        var infoWnd = new Window.poupWindow("info_wnd",y,x,xoffset,-80,400,0,"window",text.replace("-","\ "));
        
        infoWnd.Show();
        var net = new Net.ContentLoader("asinc.php",getRegData,Error,"POST","obj=getRegInfo&id="+id);
        net.object = infoWnd;
    }
 }
 
 
function Save(f){ 
    if(CheckString(f.gname.value)==1){alert("Недопустимый символ при вводе имени");return;}
    if(CheckString(f.descript.value)==1){alert("Недопустимый символ при вводе описания");return;}
    if(f.gname.value==""){alert("График должен иметь название");return;}
    
    //строка с значениями смены, допуска и зоны для base_graph
    var newval="";
    for(i=0;i<f.elements.length;i++)
    {
       var item=f.elements[i];
       if(item.name.indexOf("smena")>-1)
       {
             newval+=item.options[item.selectedIndex].value+",";
       }
       if(item.name.indexOf("dopusk")>-1)
       {
             newval+=item.options[item.selectedIndex].value+",";
       }
       if(item.name.indexOf("zona")>-1)
       {
             newval+=item.options[item.selectedIndex].value+";";

       }
    }
    newval=newval.substr(0,newval.length-1);
    var data1 = {obj:"addPers",act:"save", itognewval:newval,gname:$("#gname").val(),gdate:$("#gdate").val(),discript:$("#discript").val()};
            $.ajax({
             url: "asinc.php", 
             type: "POST",
             data: data1,
             dataType: "json",
             success: function (data) {
                    $.each(data,function(key, value) {
                      $('<option/>', {
                        val:  key,
                        text: value
                      }).appendTo('#graph');
                      $('#graph').val(key);
                    });
                },
              error: function (xhr, ajaxOptions, thrownError) {
                alert('error save');
                
              }
             });
    closeAddGraphWind();
    
  }


//ф-ии добавления режима
function SetRegInterval(){//edit
    var st = $("#stime").val();
    var fin = $("#ftime").val();
    if(st==="00:00" && fin==="00:00")return;
    if(isTime(st)===false || isTime(fin)===false)return;
    if(fin==="00:00")fin==="24:00";
    var sh=parseInt(st.substr(0,2));//start hour
    var sm=parseInt(st.substr(3,2)/15);//start min
    var fh=parseInt(fin.substr(0,2));//finish hour
    var fm=parseInt(fin.substr(3,2)/15);//finish min

    var part1 = REG_TIME.substring(0,sh*4+sm);
    var part2 = REG_TIME.substring(fh*4+fm,96);
    var flags_length = (fh-sh)*4-sm+fm;
    var green_str='';
    for(var i=0;i<flags_length;i++)
    {
        green_str+="1";
    }
    REG_TIME=part1+green_str+part2;
    for(var i=sh;i<=fh;i++)
    {
        if(sh<fh)
        {
            if(i===sh)
            {
                for(var j=sm;j<=3;j++)
                {
                    $("#h_"+i+"_"+j).css("background-color","green");
                } 
            }
            if(i===fh)
            {
                for(var j=0;j<fm;j++)
                {
                    $("#h_"+i+"_"+j).css("background-color","green");
                } 
            }
            if(i!==fh && i!==sh)
            {
                for(var j=0;j<=3;j++)
                {
                    $("#h_"+i+"_"+j).css("background-color","green");
                } 
            }
        }
        else{
            if(i===fh)
            {
                for(var j=sm;j<fm;j++)
                {
                    $("#h_"+i+"_"+j).css("background-color","green");
                } 
            }
        }
    }
}
function ClearRegPanel(){
    SetRegEmptyFlags();
    REG_TIME ="000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
}
function SetRegFlag(id){
    var idstr = new Array();
    idstr=id.split("_");
    
    if(REG_TIME.substr(idstr[1]*4+idstr[2]*1,1) === '0')
    {
        $("#"+id).css("background-color","green");
        var part1 = REG_TIME.substring(0,idstr[1]*4+idstr[2]*1);
        var part2 = REG_TIME.substring((idstr[1]*4+idstr[2]*1)+1,96);
        REG_TIME=part1+'1'+part2;//раз уж в js нет норм замены символа в строке по индексу...
    }  
    else
    {
        $("#"+id).css("background-color","#f5f5f5");
        var part1 = REG_TIME.substring(0,idstr[1]*4+idstr[2]*1);
        var part2 = REG_TIME.substring((idstr[1]*4+idstr[2]*1)+1,96);
        REG_TIME=part1+'0'+part2;
    }

}
function SetRegEmptyFlags(){
  
    var reg_div_str =' <div>';
    for(j=0;j<12;j++)
    {
        reg_div_str+='<div class="timelineajax" style="background-color:#f5f5f5;">'+j+'</div>';
    }
    for(j=0;j<12;j++)
    {
        reg_div_str+='<div class="timebasesubpanelajax">';
        for(k=0;k<4;k++)
        {
            reg_div_str+='<div id = h_'+j+'_'+k+' onclick=\'SetRegFlag(this.id)\' class="timebaseitemajax" style="background-color: white"></div>';
        }
        reg_div_str+='</div>';
    }
    reg_div_str+='</div>';
    reg_div_str+=' <div>';
    for(j=12;j<24;j++)
    {
        reg_div_str+='<div class="timelineajax" style="background-color:#f5f5f5;">'+j+'</div>';
    }
    for(j=12;j<24;j++)
    {
        reg_div_str+='<div class="timebasesubpanelajax">';
        for(k=0;k<4;k++)
        {
            reg_div_str+='<div id = h_'+j+'_'+k+' onclick=\'SetRegFlag(this.id)\' class="timebaseitemajax" style="background-color: white"></div>';
        }
        reg_div_str+='</div>';
    }
    reg_div_str+='</div>';
    $("#reg_time").html(reg_div_str);
}


////////
///////
//добавленеи кода пропуска из personal.php.
 /*
function ShowDivPass(id){
    var pers_id = id.substr(5);
    $("#pers_id").val(pers_id);
    var elem = $("#"+id).offset();
    var top = elem.top;
    var left = elem.left;
    $("#pass_div").css("top",top-10);
    $("#pass_div").css("left",left-200);
    $("#pass_div").show();
}
$(document).keydown(function(e) {
    if( e.keyCode === 27 ) {
        $("#pass_div").hide();
        $("#pers_id").val("");
        $("#pass_code").val("");
        return false;
    }
});
function SetPass(){

    var pers_id =  $("#pers_id").val();
    var pxcode  =  $("#pass_code").val();
    
    if(pxcode.length===0){
        $("#pass_div").hide();
        $("#pers_id").val("");
        return;
    }
    if(pxcode.length!==16){
        alert("Код пропуска должен быть 16-ти значный!")
        return;
    }
    else{
        var data1 = {obj:"addPxcode", pers_id:pers_id, pxcode:pxcode};
            $.ajax({
                url: "asinc.php", 
                type: "POST",
                data: data1,
                dataType: "json",
                success: function (data) {
                    var dataObj = eval(data);
                    if (dataObj.res!=="1"){
                        alert(dataObj.res);
                    }
                    else{
                        $("#pass_"+pers_id+" img").attr("src","buttons/pass_changed.gif");
                        $("#pass_"+pers_id+" img").attr("title","Пропуск сохранён");

                        $("#pass_"+pers_id).parents("tr").children("td").attr("bgcolor","#CAFFAF");
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert("error SetPass");
                
                }
             });
     
        $("#pass_div").hide();
        $("#pers_id").val("");
        $("#pass_code").val("");
    }
}
*/