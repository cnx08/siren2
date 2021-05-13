     
function details(id_but,date)
{   
    //формат id: "period_turn_overper~action"
    var id = id_but.id;
    var undsp = id.indexOf('_');
    var tire = id.indexOf('-');
    var tilda = id.indexOf('x');
    var period =    id.substring(0,undsp);
    var turn =      id.substring(undsp+1,tire);
    var overper =   id.substring(tire+1,tilda);
    var action =    id.substring(tilda+1);
    if(period=='week'){
        
       for (var i = 6;i>=0;i--){       

            var data1 = {act:action, date:date, period:'day', turn:turn, offset:i, overper:overper};
            $.ajax({
             url: "../techreport/asinc.php", 
             type: "POST",
             async: false, 
             data: data1,
             dataType: "text",
            success: function (data) {
                $('#'+id).after(data);

                },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error details');
            }
            });
            
       }
    }
     if(period=='mon'){
        
       for (var i = 30;i>=0;i--){       

            var data1 = {act:action, date:date, period:'day', turn:turn, offset:i, overper:overper};
            $.ajax({
             url: "../techreport/asinc.php", 
             type: "POST",
             async: false, 
             data: data1,
             dataType: "text",
            success: function (data) {
                $('#'+id).after(data);

                },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error details');
            }
            });
            
       }
    }
    if(period=='mon3'){
        
       for (var i = 2;i>=0;i--){       

            var data1 = {act:action, date:date, period:'mon', turn:turn, offset:i, overper:overper};
            $.ajax({
             url: "../techreport/asinc.php", 
             type: "POST",
             async: false, 
             data: data1,
             dataType: "text",
            success: function (data) {
                $('#'+id).after(data);

                },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error details');
            }
            });
            
       }
    }
    if(period=='mon6'){
        
       for (var i = 5;i>=0;i--){       

            var data1 = {act:action, date:date, period:'mon', turn:turn, offset:i, overper:overper};
            $.ajax({
             url: "../techreport/asinc.php", 
             type: "POST",
             async: false, 
             data: data1,
             dataType: "text",
            success: function (data) {
                $('#'+id).after(data);

                },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error details');
            }
            });
            
       }
    }
    if(period=='year'){
        
       for (var i = 11;i>=0;i--){       

            var data1 = {act:action, date:date, period:'mon', turn:turn, offset:i, overper:overper};
            $.ajax({
             url: "../techreport/asinc.php", 
             type: "POST",
             async: false, 
             data: data1,
             dataType: "text",
            success: function (data) {
                $('#'+id).after(data);

                },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('error details');
            }
            });
            
       }
    }
    
    $('#'+id).attr("onclick",'');
}

function GoTurn()
{
    //var per = $("#sel").val();
    //$("#per").val(per);
    //$("#act").val("turn");
    
       
    var posting = $.post( "../techreport.php", { act:"turn", date:$("#date").val(), per:$("#sel").val() } );
 
    // Put the results in a div
    posting.done(function( data ) {
        var win=window.open('about:blank');
        with(win.document)
        {
          open();
          write(data);
          close();
        }
    });        
    alert('Запрос выполняется, пожалуйста подождите.');       


}
function GoUnit()
{
    //var per = $("#sel").val();
    //$("#per").val(per);
    //$("#act").val("unit");
    //f.submit();
    
    var posting = $.post( "../techreport.php", { act:"unit", date:$("#date").val(), per:$("#sel").val() } );
 
    // Put the results in a div
    posting.done(function( data ) {
        var win=window.open('about:blank');
        with(win.document)
        {
          open();
          write(data);
          close();
        }
    });
    alert('Запрос выполняется, пожалуйста подождите.');  
}
$(function () {
    $("#date").pickmeup({
        change : function (val) {
            $("#date").val(val).pickmeup("hide")
        }
    });
    $("#start_date").pickmeup({
        change : function (val) {
            $("#start_date").val(val).pickmeup("hide")
        }
    });
    $("#fin_date").pickmeup({
        change : function (val) {
            $("#fin_date").val(val).pickmeup("hide")
        }
    });
      $(".dtab").css('border-style','');

});
function SelectAll(f)
{
   if(f.checkall.checked==1)
   {
      for(var i=0;i<f.elements.length;i++)
      {
         var item=f.elements[i];
         if(item.name.indexOf("check",0)>-1 && item.disabled == false)
             item.checked=1;
      }
   }
   else
   {
      for(var i=0;i<f.elements.length;i++)
      {
         var item=f.elements[i];
         if(item.name.indexOf("check",0)>-1)
             item.checked=0;
      }
   }
}
function Go(f)
{

    if($("#rtype").val()=='4'){
        if(CheckString(f.family.value)==1)
        {alert("Недопустимый символ при вводе Фамилии");f.family.focus();return;}
        if(CheckString(f.name.value)==1)
        {alert("Недопустимый символ при вводе имени");f.name.focus();return;}
        if(CheckString(f.secname.value)==1)
        {alert("Недопустимый символ при вводе отчества");f.secname.focus();return;}
        if(CheckString(f.px_code.value)==1 )
        {alert("Недопустимый символ при вводе пропуска");f.px_code.focus();return;}
        if(CheckString(f.time_begin.value)==1 )
        {alert("Недопустимый символ при вводе времени");f.time_begin.focus();return;}
        if(CheckString(f.time_end.value)==1 )
        {alert("Недопустимый символ при вводе времени");f.time_end.focus();return;}
    }

    f.submit();

}

function checkedDisabledOff(f)
{
   for(var i=0;i<f.elements.length;i++)
      {
         var item=f.elements[i];
         if(item.name.indexOf("check",0)>-1)
             item.disabled=0;
      }
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
function toCountEvents()
{
    var data1 = {obj:"CntEvents", st_date:$("#start_date").val(), en_date:$("#fin_date").val()};
    $.ajax({
     url: "asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
    success: function (data) {
        $("#spancnt").html(data);
        },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('toCountEvents error');
    }
    });

}
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
  if(str.indexOf("/",0)!=-1)
  return 1;
}