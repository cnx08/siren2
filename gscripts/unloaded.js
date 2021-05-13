/*
function change_unit(){
    var data1 = {"obj":"unit", "unit":$('#unit option:selected').val()};
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        var kk = JSON.parse(data);
        $('#1').val(kk.m1);
        $('#2').val(kk.m2);
        $('#3').val(kk.m3);
        $('#4').val(kk.m4);
        $('#5').val(kk.m5);
        $('#6').val(kk.m6);
        $('#7').val(kk.m7);
        $('#8').val(kk.m8);
        $('#9').val(kk.m9);
        $('#10').val(kk.m10);
        $('#110').val(kk.m110);
        kk.m11 === '1' ? $('#111').prop("checked",true) : $('#111').prop("checked",false);
        $('#new_unit').hide();
        $('#unit').show();
        },
      error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr);
          alert(ajaxOptions);
          alert(thrownError);
          
      }
     });
}
*/
function set_attempts_to_1(){
    var data1 = {"obj":"attempts"};
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        alert('Количество попыток установлено в 1');
        },
      error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr);
          alert(ajaxOptions);
          alert(thrownError);
          
      }
     });
}
/*
function send_command(e,num){
     e.preventDefault();
    var data1 = {"obj":"comm_to_unit", "unit":$('#unit option:selected').val(), "num":num};
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        alert('Команда отправлена');
        },
      error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr);
          alert(ajaxOptions);
          alert(thrownError);
          
      }
     });
}
function ooo(e){
    e.preventDefault();

    $('#unit').hide();
    $('#new_unit').show();
    $('#110').val('');
    $('#111').prop("checked",true);
    $('#reboot_OS').hide();
    $('#reboot_prog').hide();
    $('#power_off').hide();
    $('#save').hide();
    $('#sS2').show();
}

function add(e){
    e.preventDefault();
    if($('#new_unit').val()>10000 || $('#new_unit').val()<1){
        alert("Недопустимый номер юнита");
        return;
    }
    //alert($('#10').val().length);
    if($('#10').val().length == 0){
        alert("Введите название юнита");
        return;
    }
    
    function contains(a, obj) {
        var i = a.length;
        while (i--) {
           if (a[i] === obj) {
               return true;
           }
        }
        return false;
    }
    var units = [];
    $("#unit option").each(function()
    {
        units.push($(this).val());
    });
    if(!contains(units,$('#new_unit').val())){
        var data1 = {"obj":"add_unit",
                    "unit":$('#new_unit').val(),
                    "1":$('#1').val(),
                    "2":$('#2').val(),
                    "3":$('#3').val(),
                    "4":$('#4').val(),
                    "5":$('#5').val(),
                    "6":$('#6').val(),
                    "7":$('#7').val(),
                    "8":$('#8').val(),
                    "9":$('#9').val(),
                    "10":$('#10').val(),
                    "11":$('#11').val()
                    };
        $.ajax({
         url: "../asinc.php", 
         type: "POST",
         data: data1,
         dataType: "text",
         success: function (data) {
            location.reload();
            },
          error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr);
              alert(ajaxOptions);
              alert(thrownError);

          }
         });
     }
    else{
        alert("Такой номер юнита уже существует");
        return;
    }
}
function che(e){
    //e.preventDefault();
    if($('#10').val().length == 0){
        alert("Введите название юнита");
        e.preventDefault();
        return;
    }
}
*/