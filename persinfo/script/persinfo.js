var turnid = 0;
function CheckString(str)
{
  if(str.indexOf("'",0)!==-1 || str.indexOf("\"",0)!==-1 || str.indexOf("%",0)!==-1 || str.indexOf("/",0)!==-1 || str.indexOf("<",0)!==-1 || str.indexOf(">",0)!==-1 || str.indexOf("`",0)!==-1 || str.indexOf("~",0)!==-1 || str.indexOf("!",0)!==-1 || str.indexOf("@",0)!==-1 || str.indexOf("#",0)!==-1 || str.indexOf("$",0)!==-1 || str.indexOf("^",0)!==-1 || str.indexOf("&",0)!==-1 || str.indexOf("*",0)!==-1 || str.indexOf("(",0)!==-1 || str.indexOf(")",0)!==-1 || str.indexOf("+",0)!==-1 || str.indexOf(";",0)!==-1 || str.indexOf(":",0)!==-1)
  return 1;
}
function search()
{
    var fam = $("#fam").val();
    var name = $("#name").val();
    var sec = $("#sec").val();
    var px_code = $("#px_code").val();
    $('#turn_list').empty();
    if(fam==='' && px_code==='')
    {
        alert('Укажите фамилию или код пропуска');
        return;
    }
    if(CheckString(fam)===1)
    {alert("Ошибка:недопустимый символ при вводе фамилии");return;}
    if(CheckString(name)===1)
    {alert("Ошибка:недопустимый символ при вводе имени");return;}
    if(CheckString(sec)===1)
    {alert("Ошибка:недопустимый символ при вводе отчества");return;}
    if(CheckString(px_code)===1)
    {alert("Ошибка:недопустимый символ при вводе кода пропуска");return;}
    if(px_code.length>0 && px_code.length<16)
    {alert("Код пропуска должен быть 16-ти значный");return;}
    if(fam.length<3 && px_code==='')
    {alert("Для поиска по фамилии укажите не менее трёх букв");return;}
    var data1 = {act:'search',stage:'1', fam:fam, name:name, sec:sec ,dept:$("#dept").val(),code:px_code};
    $.ajax({
        url: "./asinc.php", 
        type: "POST",
        data: data1,
        dataType: "json",
        success: function (data) {
            
            var dataObj = eval(data);
            if (dataObj.res === '0'){alert('Не найдено ни одного сотрудника по данному запросу'); return;}
            if (dataObj.res === '1'){
                $("#period").css("color","black");
                dataObj.photo==="" ? $('#ph').attr('src', '../ph2/img/none.jpg') : $('#ph').attr('src', '../foto/'+dataObj.photo);
                if (dataObj.token === 'p'){
                    $("#info_pers").show();
                    $("#info_guest").hide();
                    
                    $("#p_FIO").html(dataObj.fam + ' ' + dataObj.name + ' ' + dataObj.sec);
                    $("#p_dept_name").html(dataObj.dept);
                    $("#p_pass").html(dataObj.code);
                    $("#p_pos").html(dataObj.pos);
                    $("#gn").html(dataObj.gn);
                    $("#graph_offset").html(dataObj.graph_offset);
                    $("#sm_time").html(dataObj.stsm + '-' + dataObj.endsm);
                    $("#sm_dinner").html(dataObj.stdin + '-' + dataObj.enddin);
                    dataObj.code === '' || dataObj.gn === 'Нет доступа' ?$("#p_uvol").html("уволен"):$("#p_uvol").html("работает");
                    
                }
                if (dataObj.token === 'g'){
                    $("#info_pers").hide();
                    $("#info_guest").show();
                    
                    $("#g_FIO").html(dataObj.fam + ' ' + dataObj.name + ' ' + dataObj.sec);
                    $("#g_dept_name").html(dataObj.dept);
                    $("#g_pass").html(dataObj.code);
                    $("#g_pos").html(dataObj.pos);
                    $("#vpos").html(dataObj.vpos);
                    $("#pasp").html(dataObj.pasp);
                    $("#comm").html(dataObj.com);
                    $("#dn").html(dataObj.dop);
                    $("#period").html(dataObj.date_in + ' - ' + dataObj.date_out);
                    $("#towho").html(dataObj.towho);
                    if(dataObj.old == 1)
                    {
                        $("#period").css("color","red");
                    }
                    
                }
                if ((dataObj.token === 'g' && dataObj.old === "0") || (dataObj.token === 'p' && dataObj.code.length > 10)){
                    get_turns(dataObj.id_graph,dataObj.id_dop,dataObj.graph_offset);
                }
            }
            if (dataObj.res > 1){
               
                var res_str = '';
                for(var tid = 1;tid <= dataObj.res;tid++)
                {
                    var tid_str = ''+tid;
                    
                    res_str += dataObj[tid_str]['old'] == 1 ?'<p style = "color:red;">' : '<p>';
                    
                    res_str += '<span id='+dataObj[tid_str]['id_pers']+' class = "res_str" >';
                    res_str +=  dataObj[tid_str]['fam'] + ' '+ dataObj[tid]['name'] + ' '+ dataObj[tid]['sec'];
                    if(dataObj[tid_str]['pos'].length > 0)
                    {
                        res_str += ' | '+ dataObj[tid_str]['pos'];
                    }
                    if(dataObj[tid_str]['dept'].length > 0)
                    {
                        res_str += ' | Отдел '+ dataObj[tid_str]['dept'];
                    }
                    if(dataObj[tid_str]['pasport'].length > 1)
                    {
                        res_str += ' | ' +dataObj[tid_str]['pasport'];
                    }
                    if(dataObj[tid_str]['com'].length > 1)
                    {
                        res_str += ' | '+ dataObj[tid_str]['com'] ;
                    }
                    res_str +='</span></p>';
                    
                }
                $('#search_res').append(res_str);

                $("#search_res").show();
                $("#mask").show();
                var mask_h = document.getElementsByTagName('body')[0].scrollHeight;
                            
                $("#mask").height(mask_h);
                $("#mask").css('z-index','10');
                $("#search_res").css('z-index','15');

                MouseActions();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr);
            alert(ajaxOptions);
            alert(thrownError);
            alert('error search');
        }
    });
}
function MouseActions(){
    $("#search_res span").mouseover(function (e)
    {
        var $target = $(e.target); 
        var id = $target.attr('id');
        $("#"+id).css('background-color','#C3CFFF');
        $("#"+id).css('cursor','pointer');
    });
    $("#search_res span").mouseleave(function (e)
    {
        var $target = $(e.target); 
        var id = $target.attr('id');
        $("#"+id).css('background-color','white');
    });
    $("#search_res span").click(function (e)
    {
        var $target = $(e.target); 
        var id = $target.attr('id');
        SecondSearchStage(id);
        
    });
}
function make_clear(){
    $("#fam").val('');
    $("#name").val('');
    $("#sec").val('');
    $("#px_code").val('');
    $("#dept").val('0');
}
function SecondSearchStage(id){
    $("#mask").css('z-index','-1');
    $("#search_res").css('z-index','-1');
    $("#search_res").hide();
    $("#mask").hide();
    $("#search_res").empty();
    $('#turn_list').empty();
    $("#period").css("color","black");
    var data1 = {act:'search', stage:'2', id_pers:id};
    $.ajax({
        url: "./asinc.php", 
        type: "POST",
        data: data1,
        dataType: "json",
        success: function (data) {

            var dataObj = eval(data);
            if (dataObj.res !== '1'){alert('Error SecondSearchStage'); return;}
            if (dataObj.res === '1'){
                
                dataObj.photo==="" ? $('#ph').attr('src', '../ph2/img/none.jpg') : $('#ph').attr('src', '../foto/'+dataObj.photo);
                if (dataObj.token === 'p'){
                    $("#info_pers").show();
                    $("#info_guest").hide();

                    $("#p_FIO").html(dataObj.fam + ' ' + dataObj.name + ' ' + dataObj.sec);
                    $("#p_dept_name").html(dataObj.dept);
                    $("#p_pass").html(dataObj.code);
                    $("#p_pos").html(dataObj.pos);
                    $("#gn").html(dataObj.gn);
                    $("#graph_offset").html(dataObj.graph_offset);
                    $("#sm_time").html(dataObj.stsm + '-' + dataObj.endsm);
                    $("#sm_dinner").html(dataObj.stdin + '-' + dataObj.enddin);
                    dataObj.code === '' || dataObj.gn === 'Нет доступа' ?$("#p_uvol").html("уволен"):$("#p_uvol").html("работает");
                }
                if (dataObj.token === 'g'){
                    $("#g_FIO").html(dataObj.fam + ' ' + dataObj.name + ' ' + dataObj.sec);
                    $("#g_dept_name").html(dataObj.dept);
                    $("#g_pass").html(dataObj.code);
                    $("#g_pos").html(dataObj.pos);
                    $("#info_pers").hide();
                    $("#info_guest").show();
                    $("#vpos").html(dataObj.vpos);
                    $("#pasp").html(dataObj.pasp);
                    $("#comm").html(dataObj.com);

                    $("#dn").html(dataObj.dop);
                    $("#period").html(dataObj.date_in + ' - ' + dataObj.date_out);
                    $("#towho").html(dataObj.towho);
                    if(dataObj.old == 1)
                    {
                        $("#period").css("color","red");
                    }
                }
                if ((dataObj.token === 'g' && dataObj.old === "0") || (dataObj.token === 'p' && dataObj.code.length > 10)){
                    get_turns(dataObj.id_graph,dataObj.id_dop,dataObj.graph_offset);
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error 2nd stage');
        }
    });
}
        
function get_turns(id_gr,id_dop,graph_offset)
{   
    $('#turn_list').empty();
    var data1 = {act:'get_turn',id_gr:id_gr, id_dop:id_dop, graph_offset:graph_offset};
    $.ajax({
     url: "./asinc.php", 
     type: "POST",
     data: data1,
     dataType: "json",
    success: function (data) {
            
        var dataObj = eval(data);
        var res_str = '';
        for(turnid = 1;turnid <= dataObj.res;turnid++)
        {
            var tid_str = ''+turnid;
            res_str += '<li>'+dataObj[tid_str]+' </li>';
            
        }
        $('#turn_list').append(res_str);
        resizeturns();
        },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error get_turns');
     }
    });

   
}
function get_dept(){
    var data1 = {act:'get_dept'};    
   $.ajax({
        url: "./asinc.php", 
        type: "POST",
        data: data1,
        dataType: "json",
       success: function (data) {     
            $.each(data,function(key, value) {
              $('<option/>', {
                val:  key,
                text: value
              }).appendTo('#dept');
            });
            },
       error: function (xhr, ajaxOptions, thrownError) {
           alert('error get_dept');
        }
       });
}
 $(document).ready(function() {
    make_clear();
    get_dept();
    $("#ph").attr("src",'../ph2/img/photo.jpg');
    letsresize();
});

$(window).resize(function() 
{
    letsresize();

});

function letsresize(){
    var body_w = $("#main").width();
    
    var ph_w = $("#photo").width();
    $("#photo").height(ph_w*4/3);
    (body_w - ph_w - 350)<300 ? $('#turns').hide() : $('#turns').show();
    (ph_w + 350)> body_w-100 ? $('#photo').hide() : $('#photo').show();
    $("#turns").width(body_w - ph_w - 430);
    resizeturns();
}
function resizeturns(){
    var list_h = turnid * (20 + 0.3) + 60;
    var body_h = $("#main").height();
    if (list_h>600){
        if (body_h - 30 < list_h){
            $('#turns').height(body_h - 30);
            $('#turns').css('overflow-y','scroll');
        }
        else{
            $('#turns').height(list_h);
            $('#turns').css('overflow-y','auto');
        }
    }
    if (list_h<600){
        if (body_h - 30 < list_h){
            $('#turns').height(list_h);
            $('#turns').css('overflow-y','scroll');
        }
        else{
            $('#turns').height(600);
            $('#turns').css('overflow-y','auto');
        }
    }
}