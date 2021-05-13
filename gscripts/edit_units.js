function setMaskLevel(z){
    $("#mask").css("z-index",z);
}


function save(id){
    if(id <= 0 || $("unit_"+id).val()<0 ) {alert("Некорректный ID или номер юнита");return;}
    var data1 = {"obj":"edit_units", "unit":$("#unit_"+id).val() , "id": id, "name": $("#name_"+id).val()};
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        var kk = JSON.parse(data);
        if(kk.result < 1){
            alert("Юнит с таким номером или именем уже существует!");
        }
        else location.reload();
        },
      error: function (xhr, ajaxOptions, thrownError) {
          alert("save");
          alert(xhr);
          alert(ajaxOptions);
          alert(thrownError);
          
      }
     });

}
function add_unit(){
    $("#add_button").hide();
    var el = $("#u_list tr:nth-child(2)").clone();
    el.attr("id","new");
    el.children("td").removeAttr("rowspan");
    el.appendTo("#u_list");
    
    $("#new td:nth-child(1) input").attr("id","new_num").val('');
    $("#new td:nth-child(2) input").attr("id","new_name").val('');
    var bg_color = $("#new td:nth-child(2) input").css('background-color');
    $("#new td:nth-child(3)").html('<input type="text" id ="new_id" align="center" class="tabcontent" size = "10" maxlength = "10">');
    $("#new td:nth-child(3) input").css("background-color",bg_color).val('');
    $("#new td:nth-child(4)").html('');
    $("#new td:nth-child(5)").html('');
    $("#new td:nth-child(6)").html('<img src="buttons/save.gif"  class="icons" alt="Сохранить" title="Сохранить" onclick="save_new()">');
}
function save_new(){
    var num = $("#new_num").val();
    var name = $("#new_name").val();
    var id = $("#new_id").val();
    if(num <= 0 || num == '' ) {alert("Некорректный номер юнита");return;}
    if(name =='' ) {alert("Введите название юнита");return;}
    if( id <= 0 || id.length != 10 ) {alert("Некорректный ID юнита");return;}
    var data1 = {"obj":"add_unit", "unit":num , "id": id, "name": name};
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        var kk = JSON.parse(data);
        if(kk.result == -1){
            alert("Юнит с таким id уже существует!");
        }
        else if(kk.result == -2){
            alert("Юнит с таким номером уже существует!");
        }
        else if(kk.result == -3){
            alert("Юнит с таким именем уже существует!");
        }
        else if(kk.result == -4){
            alert("Ошибка выполнения запроса!");
        }
        else location.reload();
        },
      error: function (xhr, ajaxOptions, thrownError) {
          alert("save_new");
          alert(xhr);
          alert(ajaxOptions);
          alert(thrownError);
          
      }
     });

}

function closeUnitConf(){
    $("#unit_conf").hide();
    setMaskLevel(-1);
};
function showUnitConf(unit){
    if(unit <= 0 ) {alert("unit <= 0");return;}
    $("#cur_unit").val(unit);
    
    var data1 = {"obj":"get_unit_conf", "unit":unit };
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        var kk = JSON.parse(data);
        if(kk.result < 1){
            alert("Ошибка при получении настроек юнита");
        }
        else{
            $("#thead").html("Настройки юнита "+unit+" "+kk.u_name);
            var checked = kk.status == '1' ? true : false;
            $("#status").prop("checked",checked);
            $("#timer_corr").val(kk.timer_corr);
            $("#sunday_reboot").val(kk.sunday_reboot);
            $("#hard_err_reboot").val(kk.hard_err_reboot);
            $("#time_dbl_pass").val(kk.time_dbl_pass);
            $("#out_time_cnt").val(kk.out_time_cnt);
            $("#time_cnt").val(kk.time_cnt);
            $("#prop_cnt").val(kk.prop_cnt);
            $("#log_level_srt").val(kk.log_level_srt);
            $("#log_level_dm").val(kk.log_level_dm);
            $("#off_line_start").val(kk.off_line_start);
        }
        
        $("#unit_conf").show();
        setMaskLevel(49);
        }

        ,
      error: function (xhr, ajaxOptions, thrownError) {
          alert("showUnitConf");
          alert(xhr);
          alert(ajaxOptions);
          alert(thrownError);
          
      }
     });
};

function save_conf(){
    var data1 = {"obj":"save_unit_conf",
                "unit":$("#cur_unit").val(),
                "status":$("#status").prop("checked"),
                "timer_corr":$("#timer_corr").val(),
                "sunday_reboot":$("#sunday_reboot").val(),
                "hard_err_reboot":$("#hard_err_reboot").val(),
                "time_dbl_pass":$("#time_dbl_pass").val(),
                "out_time_cnt":$("#out_time_cnt").val(),
                "time_cnt":$("#time_cnt").val(),
                "prop_cnt":$("#prop_cnt").val(),
                "log_level_srt":$("#log_level_srt").val(),
                "log_level_dm":$("#log_level_dm").val(),
                "off_line_start":$("#off_line_start").val()
            };
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        var kk = JSON.parse(data);
        if(kk.result < 1){
            alert("Ошибка при сохранении настроек юнита");
        }
        else{
            alert("Настройки сохранены");
            $("#unit_conf").hide();
            setMaskLevel(-1);
            
        }

    },
    error: function (xhr, ajaxOptions, thrownError) {
          alert("save_conf");
          alert(xhr);
          alert(ajaxOptions);
          alert(thrownError);
          
      }
     });
    
};
function send_command(e,num){
     e.preventDefault();
    var data1 = {"obj":"comm_to_unit", "unit":$("#cur_unit").val(), "num":num};
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
function del_unit(unit_id,unit){
    var data1 = {"obj":"del_unit", "unit_id":unit_id, "unit":unit};
    $.ajax({
     url: "../asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
     success: function (data) {
        var kk = JSON.parse(data);
        if(kk.result < 1){
            alert("Ошибка при удалении юнита");
        }
        else{
            $(".tr_"+unit_id).remove();
            alert("Юнит удален");
            setMaskLevel(-1);
            
        }
    },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('del_unit');
        alert(xhr);
        alert(ajaxOptions);
        alert(thrownError);  
      }
     });
}