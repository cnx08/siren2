var left_win_gritter = {show:0,top:20,right:20};
var right_win_gritter = {show:0,top:20,right:20};
var photo_in_stripe;
var duble_win;
var resized1 = 0;
var resized2 = 0;
var updatedParams = {};
//начальная инициализация, эти параметры должны будут замениться на данные из БД по готовности документа
var params = {
    "tur_first_win" :  3,
    "tur_second_win" :  3,
    "ch_show_third_fours" : "0" ,
    "tur_third_win" : 0 ,
    "tur_fours_win" : 0 ,
    "ch_dept" : "1" ,
    "ch_pos" : "0" ,
    "ch_time_sm" : "1" ,
    "ch_time_din" : "0" ,
    "ch_photo_increase" : "1" ,
    "ch_open_fullscreen" : "0" ,
    "ch_slide_strick" : "1" ,
    "ch_video_show" : "0" ,
    "time_font" : "Arial" ,
    "event_font" : "Arial" ,
    "fio_font" : "Arial" ,
    "dept_font" : "sans-serif" ,
    "pos_font" : "Verdana" ,
    "smena_font" : "Arial" ,
    "din_font" : "Verdana" ,
    "time_font_size" : "16px" ,
    "event_font_size" :"16px" ,
    "fio_font_size" : "16px" ,
    "dept_font_size" : "16px" ,
    "pos_font_size" : "16px" ,
    "smena_font_size" : "16px" ,
    "din_font_size" : "16px" ,
    "time_font_color" : "red" ,
    "event_font_color" : "red" ,
    "fio_font_color" : "red" ,
    "dept_font_color" : "red" ,
    "pos_font_color" : "red" ,
    "smena_font_color" : "red" ,
    "din_font_color" : "red"
}
function paramsToObj(){
    for (var prop in params) {
        var elem = $('#'+prop).attr('id');
        if (typeof elem != 'undefined') {
            params[prop] = elem.indexOf("ch_") === 0 ? $('#' + prop).prop("checked") : $('#' + prop).val();
        }
    }
}

//resize
function letsResize()
{
    //if(getCookie('ch_video_show') === 'false' ||params.ch_video_show') == '0')
    //{
    $('html').css("height","99.6%");
    $('body').css("height","99.6%");
    if ( params.tur_first_win!='0' && params.tur_second_win!='0')
    {
        $('.right_window').show();
        $('.right_info_up').show();
        $('.right_photo_down').show();
        $('#right_ph').show();
        $('.right_info_up').css({"float":"none","width":"100%"});

        $('.left_window').show();
        $('.left_info_up').show();
        $('.left_photo_down').show();
        $('#left_ph').show();
        $('.left_info_up').css({"float":"none","width":"100%"});

        left_win_gritter.show=0;
        right_win_gritter.show=0;
        $('#gritter-notice-wrapper').hide();

        duble_win=true;
        var body_width = $('body').width();
        var half_body_width = body_width/2;
        var body_height = $('body').height();
        $('.left_window').width(body_width*0.5);
        $('.left_window').height(body_height*1);
        $('.right_window').width(body_width*0.5);
        $('.right_window').height(body_height*1);
        //для левого
        var info_height = $('.left_info_up').height();
        $('.left_photo_down').height(body_height-info_height-15);
        var photo_height_l = $('.left_photo_down').height();
        photo_height_l*0.75 < half_body_width ? $('.left_photo_down').width(photo_height_l*0.75) : $('.left_photo_down').width(half_body_width-10);
        $('#left_ph').height(body_height-info_height-15);
        if(photo_height_l*0.75 < half_body_width)
        {
            $('#left_ph').width(photo_height_l*0.75);
        }
        else
        {
            $('#left_ph').width(half_body_width-10);
            var new_width=  $('#left_ph').width();
            $('#left_ph').height(new_width*4/3);
        }
        //для правого
        var info_height = $('.right_info_up').height();
        $('.right_photo_down').height(body_height-info_height-15);
        var photo_height_r =  $('.right_photo_down').height();
        photo_height_r*0.75 < half_body_width ? $('.right_photo_down').width(photo_height_r*0.75) : $('.right_photo_down').width(half_body_width-10);
        $('#right_ph').height(body_height-info_height-15);
        if(photo_height_r*0.75 < half_body_width)
        {
            $('#right_ph').width(photo_height_r*0.75);
        }
        else
        {
            $('#right_ph').width(half_body_width-10);
            var new_width=  $('#right_ph').width();
            $('#right_ph').height(new_width*4/3);
        }

        if (params.ch_slide_strick !== 'false'){
            //полоска фоток
            $('#left_photo_stripe').width(half_body_width-10);
            $('#right_photo_stripe').width(half_body_width-10);
            //сколько фоток показываем
            photo_in_stripe = Math.floor((half_body_width-10)/118); //~118px - ширина фотки в полоске
            if ($(".stripe_right").length >= photo_in_stripe) {
                $(".stripe_right").last().remove();
                $(".stripe_left").last().remove();
            }
        }

    }
    if ( params.tur_second_win==='0')//показывать только левое окно
    {
        $('.left_window').show();
        $('.left_info_up').show();
        $('.left_photo_down').show();
        $('#left_ph').show();
        $('.left_info_up').css({"float":"none","width":"100%"});
        duble_win=false;
        $('.left_window').css("border","0");
        var body_width = $('body').width();
        var body_height = $('body').height();

        $('.left_window').css({"width":"99%","height":"99%"});
        $('.right_window').hide();

        if (body_width/body_height<3/2)
        {
            $('.left_photo_down').css("float","none");
            $('.left_info_up').css("float","none");
            $('.left_info_up').css("border-bottom","3px solid #222222");
            $('.left_info_up').css("width","100%");

            var info_height = $('.left_info_up').height();
            $('.left_photo_down').height(body_height-info_height-20);
            var photo_height_l = $('.left_photo_down').height();
            photo_height_l*0.75 < body_width ? $('.left_photo_down').width(photo_height_l*0.75) : $('.left_photo_down').width(body_width-10);
            $('#left_ph').height(body_height-info_height-20);
            if(photo_height_l*0.75 < body_width)
            {
                $('#left_ph').width(photo_height_l*0.75);
            }
            else
            {
                $('#left_ph').width(body_width-10);
                var new_width=  $('#left_ph').width();
                $('#left_ph').height(new_width*4/3);
            }
        }
        else
        {
            $('.left_photo_down').css("float","left");
            $('.left_info_up').css("float","left");

            $('.left_photo_down').height(body_height-15);
            var photo_height_l = $('.left_photo_down').height();
            $('.left_photo_down').width(photo_height_l*0.74);

            $('#left_ph').height(body_height-15);
            $('#left_ph').width(photo_height_l*0.74);

            $('.left_info_up').width(body_width-photo_height_l*0.75-10);
        }
        $('.right_info_up').hide();
        $('.right_photo_down').hide();
        $('#right_ph').hide();


        //считаем где будет располагаться gritter (пескоразбрасыватель;) )
        var left_info_up =$('.left_info_up').height();
        var left_photo_down = $('.left_photo_down').width();
        if (body_width-left_photo_down < 310){
            left_win_gritter.show = 0;
        }
        else {
            left_win_gritter.show = 1;
            left_win_gritter.top = left_info_up + 20;
            left_win_gritter.right = (body_width-left_photo_down)/2 - 150;
        }
        if($("div").is("#gritter-notice-wrapper")){
            if(left_win_gritter.show === 1){
                $('#gritter-notice-wrapper').show();
                $('#gritter-notice-wrapper').css("right",left_win_gritter.right);
                $('#gritter-notice-wrapper').css("top",left_win_gritter.top);
            }
            else { $('#gritter-notice-wrapper').hide();}
        }
    }
    if (params.tur_first_win=='0')//показывать только правое окно
    {
        $('.right_window').show();
        $('.right_info_up').show();
        $('.right_photo_down').show();
        $('#right_ph').show();
        $('.right_info_up').css({"float":"none","width":"100%"});
        duble_win=false;
        $('.right_window').css("border","0");
        var body_width = $('body').width();
        var body_height = $('body').height();
        $('.right_window').css("width","99%").css("height","99%");
        $('.left_window').width(body_width-3);
        $('.left_window').height(body_height-3);
        $('.left_window').hide();

        if (body_width/body_height<3/2)
        {
            $('.right_photo_down').css("float","none");
            $('.right_info_up').css("float","none");
            $('.right_info_up').css("border-bottom","3px solid #222222");
            $('.right_info_up').css("width","100%");

            var info_height = $('.right_info_up').height();
            $('.right_photo_down').height(body_height-info_height-20);
            var photo_height_l = $('.right_photo_down').height();
            photo_height_l*0.75 < body_width ? $('.right_photo_down').width(photo_height_l*0.75) : $('.right_photo_down').width(body_width-10);
            $('#right_ph').height(body_height-info_height-20);
            if(photo_height_l*0.75 < body_width)
            {
                $('#right_ph').width(photo_height_l*0.75);
            }
            else
            {
                $('#right_ph').width(body_width-10);
                var new_width=  $('#right_ph').width();
                $('#right_ph').height(new_width*4/3);
            }
        }
        else
        {
            $('.right_photo_down').css("float","left");
            $('.right_info_up').css("float","left");

            $('.right_photo_down').height(body_height-15);
            var photo_height_r = $('.right_photo_down').height();
            $('.right_photo_down').width(photo_height_r*0.74);

            $('#right_ph').height(body_height-15);
            $('#right_ph').width(photo_height_r*0.74);

            $('.right_info_up').width(body_width-photo_height_r*0.75-10)
        }
        $('.left_info_up').hide();
        $('.left_photo_down').hide();
        $('#left_ph').hide();

        //считаем где будет располагаться gritter (пескоразбрасыватель;) )
        var right_info_up =$('.right_info_up').height();
        var right_photo_down = $('.right_photo_down').width();
        if (body_width-right_photo_down < 310){
            right_win_gritter.show = 0;
        }
        else {
            right_win_gritter.show = 1;
            right_win_gritter.top = right_info_up + 20;
            right_win_gritter.right = (body_width-right_photo_down)/2 - 150;
        }
        if($("div").is("#gritter-notice-wrapper")){
            if(right_win_gritter.show === 1){
                $('#gritter-notice-wrapper').show();
                $('#gritter-notice-wrapper').css("right",right_win_gritter.right);
                $('#gritter-notice-wrapper').css("top",right_win_gritter.top);
            }
            else { $('#gritter-notice-wrapper').hide();}
        }
    }
    if (params.tur_first_win=='0' && params.tur_second_win=='0')
    {
        duble_win=false;
        $("#but_show_first").show();
        //hide all
        alert('Выберите турникет');
        $('.left_window').hide();
        $('.left_info_up').hide();
        $('.left_photo_down').hide();
        $('#left_ph').hide();

        $('.right_window').hide();
        $('.right_info_up').hide();
        $('.right_photo_down').hide();
        $('#right_ph').hide();
    }

};
//resize blocks on resize window
$(window).resize(function()
{
    letsResize();

});
//акордион в настройках

function ConfigSet(params_loc) {
    //показывать дополнительные поля
    params_loc.ch_dept === '0' || params_loc.ch_dept === false ? $(".dep").hide() : $(".dep").show();
    params_loc.ch_pos === '0' || params_loc.ch_pos === false ? $(".pos").hide() : $(".pos").show();
    params_loc.ch_time_sm === '0' || params_loc.ch_time_sm === false ? $(".sm_t").hide() : $(".sm_t").show();
    params_loc.ch_time_din === '0' || params_loc.ch_time_din === false ? $(".sm_d").hide() : $(".sm_d").show();

    if (params_loc.ch_slide_strick === 'false') {
        $("left_photo_stripe").hide();
        $("right_photo_stripe").hide();
    }
    else {
        $("left_photo_stripe").show();
        $("right_photo_stripe").show();
    }

    //установить шрифт, размер и цвет поля
    $('.time_str').css({
        "font-family": str_replace('_', ' ',params_loc.time_font),
        "font-size":params_loc.time_font_size+'px',
        "color":params_loc.time_font_color
    });

    $('.code_descr').css({
        "font-family": str_replace('_', ' ',params_loc.event_font),
        "font-size":params_loc.event_font_size+'px',
        "color":params_loc.event_font_color
    });

    $('.FIO').css({
        "font-family": str_replace('_', ' ',params_loc.fio_font),
        "font-size":params_loc.fio_font_size+'px',
        "color":params_loc.fio_font_color
    });

    $('.dept').css({
        "font-family": str_replace('_', ' ',params_loc.dept_font),
        "font-size":params_loc.dept_font_size+'px',
        "color":params_loc.dept_font_color
    });

    $('.posi').css({
        "font-family": str_replace('_', ' ',params_loc.pos_font),
        "font-size":params_loc.pos_font_size+'px',
        "color":params_loc.pos_font_color
    });

    $('.sm_time').css({
        "font-family": str_replace('_', ' ',params_loc.smena_font),
        "font-size":params_loc.smena_font_size+'px',
        "color":params_loc.smena_font_color
    });

    $('.sm_dinner').css({
        "font-family": str_replace('_', ' ',params_loc.din_font),
        "font-size":params_loc.din_font_size+'px',
        "color":params_loc.din_font_color
    });

    letsResize();
}
function emitUpdates(obj) {
    socket.emit('update', obj);
}
function fillTurnData(data){
    //турникеты
    $.each(data,function(key, value) {
        $('<option/>', {
            val:  key,
            text: value
        }).appendTo(['#tur_first_win','#tur_second_win','#tur_third_win','#tur_fours_win']);
    });
}
function fillParamPanel(data){
    fonts = {"Arial":"Arial", "Georgia":"Georgia","Impact":"Impact", "Times New Roman":"Times New Roman", "Tahoma":"Tahoma", "Sylfaen":"Sylfaen", "Verdana":"Verdana", "sans-serif":"Sans-serif"};
    font_sizes = {"12":"12","14":"14","16":"16","18":"18","20":"20","22":"22","24":"24","26":"26"};
    font_colors = {"black":"black","dimgray":"dimgray","gray":"gray","brown":"brown","darkred":"darkred","red":"red","coral":"coral","saddlebrown":"saddlebrown","limegreen":"limegreen","green":"green","darkgreen":"darkgreen","seagreen":"seagreen","steelblue":"steelblue","royalblue":"royalblue","blue":"blue","darkblue":"darkblue","midnightblue":"midnightblue","indigo":"indigo","blueviolet":"blueviolet","purple":"purple"};
    $.each(fonts,function(key, value) {
        $('<option/>', {
            val:  key,
            text: value,
            style: 'font-family:'+value
        }).appendTo('#time_font,#event_font,#fio_font,#dept_font,#pos_font,#smena_font,#din_font');
    });
    $.each(font_sizes,function(key, value) {
        $('<option/>', {
            val:  key,
            text: value,
            style:  'font-size:'+value
        }).appendTo('#time_font_size, #event_font_size, #fio_font_size, #dept_font_size, #pos_font_size, #smena_font_size, #din_font_size');
    });
    $.each(font_colors,function(key, value) {
        $('<option/>', {
            val:  key,
            text: value,
            style:  'color:'+value
        }).appendTo('#event_font_color, #time_font_color, #fio_font_color, #dept_font_color, #pos_font_color, #smena_font_color, #din_font_color');
    });

    for (var prop in data) {
        var elem = $('#'+prop).attr('id');
        if (typeof elem != 'undefined') {
            elem.indexOf("ch_") === 0 ? $('#' + prop).prop("checked",parseInt(data[prop])) : $('#' + prop).val(data[prop]);
        }
    }

    //повесим onchange обработчики - все в updatedParams, которые отправим при закрытии панели
    $('#tur_first_win,#tur_second_win,#ch_show_third_fours,#tur_third_win,#tur_fours_win,#ch_dept,#ch_pos,#ch_time_sm,' +
        '#ch_time_din,#ch_open_fullscreen,#ch_slide_strick,#time_font,#event_font,#fio_font,#dept_font,#pos_font,#smena_font,' +
        '#din_font,#time_font_size,#event_font_size,#fio_font_size,#dept_font_size,#pos_font_size,#smena_font_size,' +
        '#din_font_size,#time_font_color,#event_font_color,#fio_font_color,#dept_font_color,#pos_font_color,#smena_font_color,' +
        '#din_font_color').change(function(){
            var elem = $( this ).attr('id');
            var val = elem.indexOf("ch_") === 0 ? $( this ).prop("checked") : $( this ).val() ;

            updatedParams[elem] = val;
            params[elem] = val;
        });

};
//окно настроек
$(function() {
    // окно настроек
    function runEffect() {
        $( "#effect" ).toggle( "slide", {}, 500 );
    };

    $("#but_show").click(function() {
        $("#but_show").hide();
        $("#but_hide").show();
        runEffect();
    });
    $("#but_hide").click(function() {
        //setTimeout(,500);
        ConfigSet(params);
        if (Object.keys(updatedParams).length>0) emitUpdates(updatedParams);
        if (updatedParams.ch_show_third_fours == '1') {
            var new_window_width = window.screen.width - window.innerWidth;
            window.open("http://"+window.location.host+"/ph/photo2", "photo2","left="+(window.innerWidth+10)+",top=0,width="+(new_window_width-15));
        }
        updatedParams = {};
        $("#but_hide").hide();
        $("#but_show").show();
        runEffect();
        letsResize();
        fullscr(params);
    });
    $("#area").mouseenter(function(){
        $("#but_show").show();
    });
    $("#area").mouseleave(function(){
        setTimeout(function(){$("#but_show").hide();},3000);
    });

});
$(function() {
    $( "#accordion" ).accordion({
        heightStyle: "content"
    });
});
function fullscr(params_loc) {//fullscreen
    if((params_loc.ch_open_fullscreen == 'true' || params_loc.ch_open_fullscreen == '1')  && (params_loc.ch_show_third_fours == 'false' || params_loc.ch_show_third_fours == '0'))
    {
        var elem = document.getElementById('main');
        //el.mozRequestFullScreen();
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        }
    }
}

$(document).ready(function() {
    ConfigSet(params);
    paramsToObj();
    fullscr(params);
    letsResize();

})

//универсальный реплэйсик
function str_replace ( search, replace, subject )
{ // Replace all occurrences of the search string with the replacement string
    if(!(replace instanceof Array)){
        replace=new Array(replace);
        if(search instanceof Array){//If search is an array and replace is a string, then this replacement string is used for every value of search
            while(search.length>replace.length){
                replace[replace.length]=replace[0];
            }
        }
    }
    if(!(search instanceof Array))search=new Array(search);
    while(search.length>replace.length){//If replace has fewer values than search , then an empty string is used for the rest of replacement values
        replace[replace.length]='';
    }
    if(subject instanceof Array){//If subject is an array, then the search and replace is performed with every entry of subject , and the return value is an array as well.
        for(k in subject){
            subject[k]=str_replace(search,replace,subject[k]);
        }
        return subject;
    }
    for(var k=0; k<search.length; k++){
        var i = subject.indexOf(search[k]);
        while(i>-1){
            subject = subject.replace(search[k], replace[k]);
            i = subject.indexOf(search[k],i);
        }
    }
    return subject;
}