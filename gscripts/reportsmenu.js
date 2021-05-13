        function isTimeExtend(str)
        {
                  var msg="Неверный формат времени.Формат: часы : минуты : секунды";
                  var msg1="Недопустимые символы в строке времени";
                  if(str.length!=8){alert(msg);return false;}

                  var h=str.substring(0,2); //alert(h);
                  var m=str.substring(3,5); //alert(m)
                  var sec=str.substring(6,8);// alert(sec);
                  if(isNaN(h)==true || isNaN(m)==true || isNaN(sec)==true)
                   {alert(msg1);return false;}
                  var z=h.substring(0,1);
                  var z1=m.substring(0,1);
                  var z2=sec.substring(0,1);
                  if(z=="-" || z1=="-" || z=="+" || z1=="+" ||z2=="-" || z2=="+" )
                  {alert(msg1);return false;}
                  if(h>24 || m>=60 || sec>=60){alert(msg);return false;}

           return true;
        }

        function SelectAll(f)
        {
           if(f.checkall.checked==1)
           {
              $("input[id^=check]").prop("checked",true);
           }
           else
           {
             $("input[id^=check]").prop("checked",false);
           }
        }
        function Go(f)
        {
          for(var i=0;i<f.elements.length;i++)
           {
                 var item=f.elements[i];
                 if(item.name.indexOf("check",0)>-1)
                 {
                   // alert(item.name);
                 }
           }
          var n=f.rtype.selectedIndex;
          if( n == 5)
          {
             if(isTimeExtend(f.st_time.value) == false)return;
          }

          if(n==0)
          {
             alert("Не выбран отчёт");return;
          }
          else
          {
            if(CheckString(f.family.value)==1)
            {alert("Недопустимый символ при вводе Фамилии");f.family.focus();return;}
            if(CheckString(f.name.value)==1)
            {alert("Недопустимый символ при вводе имени");f.name.focus();return;}
            if(CheckString(f.secname.value)==1)
            {alert("Недопустимый символ при вводе отчества");f.secname.focus();return;}
            if(CheckString(f.position.value)==1)
            {alert("Недопустимый символ при вводе должности");f.position.focus;return;}
            f.submit();
          }

        }
        function Showmainfilters()
        {
            $("#checktab").show();
            $("#checkpos").show();
            $("#checkdep").show();
            $("#checkgraph").show();
            $("#checksmena").show();
        }

        function SelectReport(obj)
        {
         var f = document.getElementById("reportfrm");
         var n=obj.selectedIndex;
         //hide all
          $("div[id^=check]").hide();
          $("#t13").hide();
          $("#time_text").hide();//поле для ввода времени для отчёта о присутствии
          $("#point_to_pass").hide();//выбор точки прохода
          $("#guestfilter").hide();
          $("#g_order_by").hide();
          $("#checkzasechka").hide();
          
          switch(n)
          {
              case 0:break;
              case 1://отработ. времени
                    $("#p_order_by").show();
                    $("#t13").show();
                    Showmainfilters();
                    $("#checkzasechka").show();
                    
                    $("#t13").css('display','inline');
                    $("#t13text").css('display','inline');
                    if(f.t13.checked === 1)
                    {
                        f.checkall.checked=0;
                        SelectAll(f);
                        $("input[id^=check]").prop("disabled",'disabled');
                    }

              break;
              case 2://опозданиям
                  $("#p_order_by").show();
                    Showmainfilters();
                    f.action = "reports.php";
                    checkedDisabledOff(f);
              break;
              case 3://по проходам
                    Showmainfilters();
                    $("#p_order_by").show();
                    $("#point_to_pass").show();

                    f.action = "reports.php";
                    checkedDisabledOff(f);
              break;
              case 4://неявкам
                    Showmainfilters();
                    $("#p_order_by").show();
                    f.action = "reports.php";
                    checkedDisabledOff(f);
              break;
              case 5://о присутствии
                    Showmainfilters();
                    $("#time_text").show();
                    $("#p_order_by").show();
                    f.action = "reports.php";
                    checkedDisabledOff(f);
                break;
               case 6://ранний уход
                    Showmainfilters();
                    f.action = "reports.php";
                    $("#p_order_by").show();
                    checkedDisabledOff(f);
               break;
               case 7://по проходам сокр.
                    Showmainfilters();
                    $("#point_to_pass").show();
                    $("#p_order_by").show();
                    f.action = "reports.php";
                    checkedDisabledOff(f);
               break;
               case 8://guest
                    $("#checkgpos").show();
                    $("#checkgcomm").show();
                    $("#checkpasstime").show();
                    $("#checkdopusk").show();
                    $("#checkpass").show();
                    $("#checktowho").show();
                    $("#checkpos").show();
                    $("#checkdep").show();
                    $("#point_to_pass").show();
                    $("#guestfilter").show();
                    $("#g_order_by").show();
                    $("#p_order_by").hide();
                    f.action = "reports.php";
                    checkedDisabledOff(f)
              break;
              default: checkedDisabledOff(f);break;
          }
        }
        function SelectT13(f)
        {
           if(f.t13.checked==1)
           {
             f.checkall.checked=0;
             SelectAll(f);
             for(var i=0;i<f.elements.length;i++)
              {
                 var item=f.elements[i];
                 if(item.name.indexOf("check",0)>-1)
                     item.disabled=1;
              }
             f.action = "t13report.php";
           }
           else
           {
              for(var i=0;i<f.elements.length;i++)
              {
                 var item=f.elements[i];
                 if(item.name.indexOf("check",0)>-1)
                     item.disabled=0;
              }
             f.action = "reports.php";
           }

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

        $(function () {
            $('#start_date').pickmeup({
                change : function (val) {
                    $('#start_date').val(val).pickmeup('hide')
                }
            });
            $('#fin_date').pickmeup({
                change : function (val) {
                    $('#fin_date').val(val).pickmeup('hide')
                }
            });
        });
        
        $(document).ready(function() {
            var select = $("#select_order").get();
            SelectReport(select);
        });